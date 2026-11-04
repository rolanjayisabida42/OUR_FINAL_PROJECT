<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Semester;
use App\Models\SectionSubmission;
use App\Models\SubmissionFile;
use App\Models\SubmissionRequirement;
use App\Models\SubmissionSetting;
use App\Services\SchoolFormNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class SubmissionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | AUTHORIZE THE CLASSROOM ADVISER
    |--------------------------------------------------------------------------
    |
    | Only the assigned Teacher with the correct grade access
    | may access this classroom.
    |
    | Returns true for active classrooms, false for archived.
    |
    */

    private function authorizeClassroom(
        Request $request,
        Section $section
    ): bool {

        $teacher = $request->user();

        $section->loadMissing([
            'schoolYear',
            'strand'
        ]);

        $gradeAccess = $teacher->grade_access;

        $allowedGrades = is_array($gradeAccess)
            ? array_map('strval', $gradeAccess)
            : match ((string) $gradeAccess) {
                '11' => ['11'],
                '12' => ['12'],
                'both' => ['11', '12'],
                default => [],
            };

        abort_unless(
            in_array(
                (string) $section->grade_level,
                $allowedGrades,
                true
            )
            &&
            $section->adviser()
                ->where('teacher_id', $teacher->id)
                ->exists(),

            403,

            'You are not assigned to this classroom.'
        );

        return (bool) $section->is_active
            && (bool) $section->schoolYear?->is_active
            && (bool) $section->strand?->is_active;
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK SUBMISSION PERIOD STATUS
    |--------------------------------------------------------------------------
    */

    private function periodStatus(
        ?Semester $semester,
        ?SubmissionSetting $setting,
        bool $isCurrent,
        string $gradeLevel
    ): string {

        if (!$isCurrent) {
            return 'archived';
        }

        if (!$semester || !$setting) {
            return 'not_configured';
        }

        if (!in_array(
            (string) $setting->grade_scope,
            ['both', $gradeLevel],
            true
        )) {
            return 'not_applicable';
        }

        if ($setting->status === 'draft') {
            return 'draft';
        }

        if ($setting->status === 'closed') {
            return 'closed';
        }

        if (
            $setting->status !== 'active' ||
            !$setting->opens_at ||
            !$setting->deadline_at
        ) {
            return 'unavailable';
        }

        if (now('UTC')->lt($setting->opens_at)) {
            return 'scheduled';
        }

        if (now('UTC')->gte($setting->deadline_at)) {
            return 'expired';
        }

        return 'open';
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFY SUBMISSION PERIOD IS OPEN
    |--------------------------------------------------------------------------
    */

    private function readySetting(
        Request $request,
        Section $section,
        Semester $semester
    ): SubmissionSetting {

        $isCurrent = $this->authorizeClassroom(
            $request,
            $section
        );

        abort_unless(
            (int) $semester->school_year_id ===
            (int) $section->school_year_id,

            404,

            'Semester does not belong to this classroom.'
        );

        $setting = SubmissionSetting::query()
            ->where('semester_id', $semester->id)
            ->first();

        $status = $this->periodStatus(
            $semester,
            $setting,
            $isCurrent,
            (string) $section->grade_level
        );

        if ($status !== 'open') {

            throw ValidationException::withMessages([

                'submission' => match ($status) {

                    'scheduled' =>
                        'This submission period has not opened yet.',

                    'expired' =>
                        'The deadline has passed. Ask the Principal to extend it.',

                    'closed' =>
                        'The Principal has closed submissions.',

                    'draft' =>
                        'The Principal has not activated submissions.',

                    'archived' =>
                        'This classroom is inactive.',

                    'not_applicable' =>
                        'This submission period does not cover your grade level.',

                    default =>
                        'Submission is unavailable for this semester.',
                },

            ]);
        }

        return $setting;
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK IF SUBMISSION CAN BE EDITED
    |--------------------------------------------------------------------------
    |
    | Draft and returned requirements can be modified.
    | Submitted and approved requirements are locked.
    |
    */

    private function ensureEditable(
        SectionSubmission $submission
    ): void {

        if (!in_array(
            $submission->status,
            ['draft', 'returned'],
            true
        )) {

            throw ValidationException::withMessages([

                'submission' =>
                    'This submission is locked while awaiting Principal review or after approval.',

            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | GET REQUIREMENTS THAT NEED CORRECTIONS
    |--------------------------------------------------------------------------
    */

    private function correctionRequirementIds(
        ?SectionSubmission $submission
    ): array {

        if (
            !$submission ||
            $submission->status !== 'returned'
        ) {
            return [];
        }

        $latestReturn = $submission->reviews()
            ->where('decision', 'returned')
            ->latest('id')
            ->first();

        $idsFromReview = collect(
            $latestReturn?->file_feedback ?? []
        )
            ->pluck('requirement_id')
            ->filter()
            ->map(fn ($id) => (int) $id);

        /*
        | Support previous returned documents
        | without requirement IDs in review history.
        */

        $idsFromFiles = $submission->files()
            ->where('review_status', 'returned')
            ->pluck('submission_requirement_id')
            ->map(fn ($id) => (int) $id);

        return $idsFromReview
            ->merge($idsFromFiles)
            ->unique()
            ->values()
            ->all();
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFY SUBMISSION IS COMPLETE
    |--------------------------------------------------------------------------
    */

    private function submissionBlocker(
        SectionSubmission $submission,
        $requirements
    ): ?string {

        if ($requirements->isEmpty()) {
            return 'The Principal has not configured any requirements.';
        }

        $files = $submission->files()->get();

        /*
        | Returned files must be removed first.
        */

        if ($files->contains(
            'review_status',
            'returned'
        )) {

            return 'Remove the returned documents and upload corrected replacements first.';
        }

        /*
        | Every requirement must contain
        | at least one valid document.
        */

        foreach ($requirements as $requirement) {

            $validFile = $files->contains(
                fn ($file) =>
                    (int) $file->submission_requirement_id
                        === (int) $requirement->id
                    &&
                    $file->review_status !== 'returned'
            );

            if (!$validFile) {

                return 'Please upload a document for every required item.';
            }
        }

        /*
        | Returned requirements must have
        | a replacement pending file.
        */

        if ($submission->status === 'returned') {

            foreach (
                $this->correctionRequirementIds($submission)
                as $requirementId
            ) {

                $hasReplacement = $files->contains(
                    fn ($file) =>
                        (int) $file->submission_requirement_id
                            === (int) $requirementId
                        &&
                        $file->review_status === 'pending'
                );

                if (!$hasReplacement) {

                    return 'Please upload a corrected file for each returned requirement.';
                }
            }
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | DISPLAY TEACHER SUBMISSIONS
    |--------------------------------------------------------------------------
    |
    | Shows:
    | - First Semester
    | - Second Semester
    | - Requirements
    | - Uploaded files
    | - Submission status
    | - Principal review history
    |
    */

    public function index(
        Request $request,
        Section $section
    ) {

        $isCurrent = $this->authorizeClassroom(
            $request,
            $section
        );

        /*
        | Load the classroom's semesters.
        */

        $semesterRows = Semester::query()
            ->where(
                'school_year_id',
                $section->school_year_id
            )
            ->whereIn('semester_number', [1, 2])
            ->get()
            ->keyBy('semester_number');

        /*
        | Load Principal requirements.
        */

        $settings = SubmissionSetting::query()
            ->with([
                'requirements' => fn ($query) =>
                    $query->orderBy('position')
                        ->orderBy('id')
            ])
            ->whereIn(
                'semester_id',
                $semesterRows->pluck('id')
            )
            ->get()
            ->keyBy('semester_id');

        /*
        | Load classroom submissions and reviews.
        */

        $submissions = SectionSubmission::query()
            ->with([
                'files.requirement',
                'reviews.reviewer'
            ])
            ->where('section_id', $section->id)
            ->get()
            ->keyBy('semester_id');

        $semesters = collect();

        foreach ([1, 2] as $number) {

            $semester = $semesterRows->get($number);

            $setting = $semester
                ? $settings->get($semester->id)
                : null;

            $submission = $semester
                ? $submissions->get($semester->id)
                : null;

            $requirements = $setting?->requirements
                ?? collect();

            $fileGroups = $submission?->files
                ?->groupBy('submission_requirement_id')
                ?? collect();

            $status = $this->periodStatus(
                $semester,
                $setting,
                $isCurrent,
                (string) $section->grade_level
            );

            /*
            | Count prepared requirements.
            */

            $preparedCount = $requirements
                ->filter(function ($requirement) use ($fileGroups) {

                    return $fileGroups
                        ->get($requirement->id, collect())
                        ->contains(
                            fn ($file) =>
                                $file->review_status !== 'returned'
                        );

                })
                ->count();

            /*
            | Can manage while the submission
            | period is open and requirements
            | are still draft or returned.
            */

            $canManage = $status === 'open'
                && (
                    !$submission ||
                    in_array(
                        $submission->status,
                        ['draft', 'returned'],
                        true
                    )
                );

            $canSubmit = $canManage
                && $submission
                && $this->submissionBlocker(
                    $submission,
                    $requirements
                ) === null;

            $semesters->put($number, [

                'number' => $number,

                'label' => $number === 1
                    ? '1st Semester'
                    : '2nd Semester',

                'semester' => $semester,

                'setting' => $setting,

                'status' => $status,

                'submission' => $submission,

                'requirements' => $requirements,

                'fileGroups' => $fileGroups,

                'preparedCount' => $preparedCount,

                'canManage' => $canManage,

                'canSubmit' => (bool) $canSubmit,

                'correctionRequirementIds' =>
                    $this->correctionRequirementIds(
                        $submission
                    ),
            ]);
        }

        /*
        | Selected semester.
        */

        $selectedNumber = $request->integer(
            'semester',
            1
        );

        if (!in_array(
            $selectedNumber,
            [1, 2],
            true
        )) {
            $selectedNumber = 1;
        }

        $selected = $semesters->get(
            $selectedNumber
        );

        return view(
            'teacher.classrooms.submissions',
            compact(
                'section',
                'isCurrent',
                'semesters',
                'selected',
                'selectedNumber'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPLOAD ORIGINAL OR CORRECTED DOCUMENTS
    |--------------------------------------------------------------------------
    */

    public function upload(
        Request $request,
        Section $section,
        Semester $semester,
        SubmissionRequirement $requirement
    ) {

        /*
        | Validate document uploads.
        */

        $data = $request->validate([

            'files' => [
                'required',
                'array',
                'min:1',
                'max:10'
            ],

            'files.*' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,csv,png,jpg,jpeg',
                'max:10240'
            ],
        ]);

        $setting = $this->readySetting(
            $request,
            $section,
            $semester
        );

        /*
        | Requirement must belong to this semester.
        */

        abort_unless(
            $setting->requirements()
                ->whereKey($requirement->id)
                ->exists(),
            404,
            'This requirement does not belong to the selected semester.'
        );

        $uploadedFiles = $data['files'];

        $storedPaths = [];

        try {

            DB::transaction(function () use (
                $request,
                $section,
                $semester,
                $requirement,
                $uploadedFiles,
                &$storedPaths
            ) {

                /*
                | Create or locate submission.
                */

                $submission = SectionSubmission::query()
                    ->firstOrCreate(
                        [
                            'section_id' => $section->id,
                            'semester_id' => $semester->id
                        ],
                        [
                            'teacher_id' => $request->user()->id,
                            'status' => 'draft'
                        ]
                    );

                /*
                | Lock submission during changes.
                */

                $submission = SectionSubmission::query()
                    ->whereKey($submission->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->ensureEditable($submission);

                /*
                | Only returned requirements
                | can receive corrections.
                */

                if ($submission->status === 'returned') {

                    $correctionIds =
                        $this->correctionRequirementIds(
                            $submission
                        );

                    if (!in_array(
                        (int) $requirement->id,
                        $correctionIds,
                        true
                    )) {

                        throw ValidationException::withMessages([
                            'files' =>
                                'Only requirements returned by the Principal can be modified.'
                        ]);
                    }
                }

                /*
                | Maximum 10 files per requirement.
                */

                $existingCount = $submission->files()
                    ->where(
                        'submission_requirement_id',
                        $requirement->id
                    )
                    ->count();

                if (
                    $existingCount + count($uploadedFiles) > 10
                ) {

                    throw ValidationException::withMessages([
                        'files' =>
                            'Maximum of 10 files per requirement.'
                    ]);
                }

                /*
                | Maximum 50 MB total submission size.
                */

                $existingBytes = (int)
                    $submission->files()
                        ->sum('size_bytes');

                $newBytes = array_sum(
                    array_map(
                        fn ($file) => $file->getSize(),
                        $uploadedFiles
                    )
                );

                if (
                    $existingBytes + $newBytes >
                    50 * 1024 * 1024
                ) {

                    throw ValidationException::withMessages([
                        'files' =>
                            'Maximum total submission storage is 50 MB.'
                    ]);
                }

                /*
                | Save each document privately.
                */

                foreach ($uploadedFiles as $uploadedFile) {

                    $path = $uploadedFile->store(
                        "teacher-submissions/{$section->id}/{$semester->id}",
                        'local'
                    );

                    if ($path === false) {

                        throw ValidationException::withMessages([
                            'files' =>
                                'Unable to save the uploaded file.'
                        ]);
                    }

                    $storedPaths[] = $path;

                    $submission->files()->create([

                        'submission_requirement_id' =>
                            $requirement->id,

                        'uploaded_by' =>
                            $request->user()->id,

                        'disk' => 'local',

                        'path' => $path,

                        'original_name' => basename(
                            str_replace(
                                '\\',
                                '/',
                                $uploadedFile->getClientOriginalName()
                            )
                        ),

                        'mime_type' =>
                            $uploadedFile->getMimeType()
                                ?: 'application/octet-stream',

                        'size_bytes' =>
                            $uploadedFile->getSize(),

                        'review_status' => 'pending',

                        'review_remarks' => null,
                    ]);
                }
            });

        } catch (Throwable $exception) {

            /*
            | Delete files when transaction fails.
            */

            foreach ($storedPaths as $path) {

                Storage::disk('local')->delete($path);
            }

            throw $exception;
        }

        /*
        | Redirect to correct semester.
        */

        return redirect()
            ->route(
                'teacher.classrooms.submissions',
                [
                    'section' => $section->id,
                    'semester' =>
                        $semester->semester_number
                ]
            )
            ->with(
                'success',
                'Document uploaded successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | REMOVE PENDING OR RETURNED DOCUMENTS
    |--------------------------------------------------------------------------
    */

    public function destroyFile(
        Request $request,
        Section $section,
        SubmissionFile $submissionFile
    ) {

        $this->authorizeClassroom(
            $request,
            $section
        );

        $submission = SectionSubmission::findOrFail(
            $submissionFile->section_submission_id
        );

        abort_unless(
            (int) $submission->section_id ===
            (int) $section->id,
            404
        );

        $semester = Semester::findOrFail(
            $submission->semester_id
        );

        $this->readySetting(
            $request,
            $section,
            $semester
        );

        $path = DB::transaction(function () use (
            $submission,
            $submissionFile
        ) {

            $locked = SectionSubmission::query()
                ->whereKey($submission->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureEditable($locked);

            $file = $locked->files()
                ->whereKey($submissionFile->id)
                ->firstOrFail();

            /*
            | Approved documents cannot be removed.
            */

            if ($file->review_status === 'approved') {

                throw ValidationException::withMessages([
                    'file' =>
                        'Approved documents cannot be deleted.'
                ]);
            }

            abort_unless(
                $file->disk === 'local',
                404
            );

            $storedPath = $file->path;

            $file->delete();

            return $storedPath;
        });

        Storage::disk('local')->delete($path);

        return back()->with(
            'success',
            'Document removed. Upload a corrected file if it was returned.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SECURE DOWNLOAD OF TEACHER FILES
    |--------------------------------------------------------------------------
    */

    public function download(
        Request $request,
        Section $section,
        SubmissionFile $submissionFile
    ) {

        $this->authorizeClassroom(
            $request,
            $section
        );

        /*
        | File must belong to this classroom.
        */

        $belongsToSection = SectionSubmission::query()
            ->whereKey(
                $submissionFile->section_submission_id
            )
            ->where(
                'section_id',
                $section->id
            )
            ->exists();

        abort_unless(
            $belongsToSection &&
            $submissionFile->disk === 'local',
            404
        );

        abort_unless(
            Storage::disk('local')
                ->exists($submissionFile->path),
            404
        );

        $safeName = basename(
            str_replace(
                '\\',
                '/',
                $submissionFile->original_name
            )
        );

        return Storage::disk('local')->download(
            $submissionFile->path,
            $safeName
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT OR RESUBMIT REQUIREMENTS
    |--------------------------------------------------------------------------
    |
    | This is where notifications are generated.
    |
    | When Teacher submits:
    | 1. Validate requirements.
    | 2. Save status as submitted.
    | 3. Commit database transaction.
    | 4. Notify Teacher and Principal.
    |
    | When Teacher resubmits returned documents:
    | 1. Save corrected submission.
    | 2. Notify Teacher and Principal.
    |
    */

    public function submit(
        Request $request,
        Section $section,
        Semester $semester
    ) {

        /*
        | Validate submission period.
        */

        $setting = $this->readySetting(
            $request,
            $section,
            $semester
        );

        $requirements = $setting->requirements()
            ->orderBy('position')
            ->get();

        $wasReturned = false;

        /*
        |--------------------------------------------------------------------------
        | SAVE SUBMISSION
        |--------------------------------------------------------------------------
        */

        $submission = DB::transaction(function () use (
            $request,
            $section,
            $semester,
            $requirements,
            &$wasReturned
        ) {

            /*
            | Lock submission.
            */

            $submission = SectionSubmission::query()
                ->where('section_id', $section->id)
                ->where('semester_id', $semester->id)
                ->lockForUpdate()
                ->first();

            if (!$submission) {

                throw ValidationException::withMessages([
                    'submission' =>
                        'Upload the required documents before submitting.'
                ]);
            }

            /*
            | Only draft/returned submissions
            | may be submitted.
            */

            $this->ensureEditable($submission);

            /*
            | Check whether all required documents
            | have been uploaded.
            */

            $blocker = $this->submissionBlocker(
                $submission,
                $requirements
            );

            if ($blocker !== null) {

                throw ValidationException::withMessages([
                    'submission' => $blocker
                ]);
            }

            /*
            | Detect whether this is a resubmission.
            */

            $wasReturned =
                $submission->status === 'returned';

            /*
            | Save submission status and date.
            */

            $submission->update([

                'teacher_id' => $request->user()->id,

                'status' => 'submitted',

                'submitted_at' => now('UTC'),

                'reviewed_at' => null,

                'principal_remarks' => null,
            ]);

            return $submission->fresh();
        });


        /*
        |--------------------------------------------------------------------------
        | SEND NOTIFICATIONS AFTER SUCCESSFUL SUBMISSION
        |--------------------------------------------------------------------------
        |
        | SchoolFormNotifier::submitted() should:
        |
        | - Notify the Teacher
        | - Notify active Principal accounts
        | - Distinguish submission from resubmission
        | - Include classroom and semester details
        |
        | A notification failure is logged, but
        | must not roll back an already saved submission.
        |
        */

        try {

            app(SchoolFormNotifier::class)
                ->submitted(
                    $submission,
                    $wasReturned
                );

        } catch (Throwable $exception) {

            report($exception);
        }


        /*
        |--------------------------------------------------------------------------
        | SUCCESS RESPONSE
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'teacher.classrooms.submissions',
                [
                    'section' => $section->id,

                    'semester' =>
                        $semester->semester_number
                ]
            )
            ->with(
                'success',

                $wasReturned

                    ? 'Corrections resubmitted successfully. Awaiting Principal review.'

                    : 'Requirements submitted successfully. Awaiting Principal review.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | PREVIEW EXCEL FILE
    |--------------------------------------------------------------------------
    |
    | Only the assigned Teacher may preview
    | files belonging to their classroom.
    |
    */

    public function preview(
        Request $request,
        SectionSubmission $submission,
        SubmissionFile $file
    ) {

        /*
        | Verify classroom ownership.
        */

        $section = Section::findOrFail(
            $submission->section_id
        );

        $this->authorizeClassroom(
            $request,
            $section
        );

        /*
        | Check file belongs to submission.
        */

        abort_unless(
            (int) $file->section_submission_id ===
            (int) $submission->id,
            404
        );

        /*
        | Only private local disk.
        */

        abort_unless(
            $file->disk === 'local',
            404
        );

        abort_unless(
            Storage::disk('local')->exists(
                $file->path
            ),
            404,
            'Uploaded document not found.'
        );

        /*
        | Excel files only.
        */

        $extension = strtolower(
            pathinfo(
                $file->original_name,
                PATHINFO_EXTENSION
            )
        );

        abort_unless(
            in_array(
                $extension,
                ['xlsx', 'xls'],
                true
            ),
            415,
            'Only Excel files can be previewed here.'
        );

        /*
        | Existing Excel preview view.
        */

        return view(
            'principal.submissions.review.preview',
            compact(
                'submission',
                'file'
            )
        );
    }
}
