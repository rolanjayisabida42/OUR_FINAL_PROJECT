<?php
namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\SectionSubmission;
use App\Models\SubmissionFile;
use App\Models\SubmissionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SubmissionReviewController extends Controller
{
    // ==========================================
    // ALL CLASSROOM SUBMISSIONS
    // ==========================================

    public function index(Request $request)
    {
        $years = SchoolYear::orderByDesc('id')->get();

        $status = (string) $request->query('status', '');
        $year = $request->integer('year');
        $semester = $request->integer('semester');
        $grade = (string) $request->query('grade', '');
        $search = trim((string) $request->query('search', ''));
        $search = substr($search, 0, 100);

        $allowedStatuses = [
            'draft',
            'submitted',
            'approved',
            'returned',
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = '';
        }

        $query = SectionSubmission::query()
            ->with([
                'section.schoolYear',
                'section.strand',
                'teacher',
                'semester',
            ])
            ->withCount('files');

        // FILTER BY SCHOOL YEAR
        if ($year > 0) {
            $query->whereHas('section', function ($q) use ($year) {
                $q->where('school_year_id', $year);
            });
        }

        // FILTER BY SEMESTER
        if (in_array($semester, [1, 2], true)) {
            $query->whereHas('semester', function ($q) use ($semester) {
                $q->where('semester_number', $semester);
            });
        }

        // FILTER BY GRADE
        if (in_array($grade, ['11', '12'], true)) {
            $query->whereHas('section', function ($q) use ($grade) {
                $q->where('grade_level', $grade);
            });
        }

        // FILTER BY SUBMISSION STATUS
        if ($status !== '') {
            $query->where('status', $status);
        }

        // SEARCH CLASSROOM OR TEACHER
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('section', function ($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });

                $q->orWhereHas('teacher', function ($tq) use ($search) {
                    $tq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $submissions = $query
            ->orderByRaw(
                "CASE WHEN status = 'submitted' THEN 0 ELSE 1 END"
            )
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        // Overall counts across all school years.
        $counts = SectionSubmission::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('principal.submissions.review.index', compact(
            'years',
            'submissions',
            'counts',
            'status',
            'year',
            'semester',
            'grade',
            'search'
        ));
    }


    // ==========================================
    // SHOW ONE SUBMISSION
    // ==========================================

    public function show(SectionSubmission $submission)
    {
        $submission->load([
            'section.schoolYear',
            'section.strand',
            'teacher',
            'semester',
            'files.requirement',
            'reviews.reviewer',
        ]);

        $setting = SubmissionSetting::with('requirements')
            ->where('semester_id', $submission->semester_id)
            ->first();

        return view('principal.submissions.review.show', compact(
            'submission',
            'setting'
        ));
    }


    // ==========================================
    // DOWNLOAD PRIVATE SUBMISSION FILE
    // ==========================================

  
/**
 * Download the original uploaded document.
 */
public function download(
    SectionSubmission $submission,
    SubmissionFile $file
) {
    // Make sure the file belongs to this submission.
    abort_unless(
        (int) $file->section_submission_id === (int) $submission->id,
        404,
        'This file does not belong to the submission.'
    );

    // Only access the private local disk.
    abort_unless(
        $file->disk === 'local',
        404,
        'Invalid storage disk.'
    );

    $disk = Storage::disk('local');

    // Make sure the uploaded file still exists.
    abort_unless(
        $disk->exists($file->path),
        404,
        'Uploaded file not found.'
    );

    // Get the real file path inside Laravel storage.
    $absolutePath = $disk->path($file->path);

    abort_unless(
        is_file($absolutePath) && is_readable($absolutePath),
        404,
        'Uploaded file cannot be read.'
    );

    // Use the original filename provided at upload time.
    $filename = basename(
        str_replace('\\', '/', (string) $file->original_name)
    );

    if ($filename === '') {
        $filename = basename($file->path);
    }

    // Determine the appropriate response content type.
    $extension = strtolower(
        pathinfo($filename, PATHINFO_EXTENSION)
    );

    $contentType = match ($extension) {
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xls'  => 'application/vnd.ms-excel',
        'csv'  => 'text/csv',
        'pdf'  => 'application/pdf',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'doc'  => 'application/msword',
        'png'  => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        default => 'application/octet-stream',
    };

    // Serve the existing file directly from private storage.
    // Do not load, convert, or rewrite the workbook.
    return response()->download(
        $absolutePath,
        $filename,
        [
            'Content-Type' => $contentType,
            'X-Content-Type-Options' => 'nosniff',
        ]
    );
}


    // ==========================================
    // APPROVE COMPLETE SUBMISSION
    // ==========================================

    public function approve(
        Request $request,
        SectionSubmission $submission
    ) 
    {
        $data = $request->validate([
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use (
            $request,
            $submission,
            $data
        ) {
            $locked = SectionSubmission::query()
                ->whereKey($submission->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'submitted') {
                throw ValidationException::withMessages([
                    'review' =>
                        'Only submitted requirements can be approved.',
                ]);
            }

            // Approve all documents.
            $locked->files()->update([
                'review_status' => 'approved',
                'review_remarks' => null,
            ]);

            $remarks = trim((string) ($data['remarks'] ?? ''));

            $locked->update([
                'status' => 'approved',
                'reviewed_at' => now('UTC'),
                'principal_remarks' =>
                    $remarks !== '' ? $remarks : null,
            ]);

            // Save permanent review history.
            $locked->reviews()->create([
                'reviewed_by' => $request->user()->id,
                'decision' => 'approved',
                'remarks' => $remarks !== '' ? $remarks : null,
                'file_feedback' => null,
            ]);
        });

        return redirect()
            ->route('principal.submissions.review.show', $submission)
            ->with(
                'success',
                'Submission approved successfully.'
            );
    }


    // ==========================================
    // RETURN INCORRECT DOCUMENTS
    // ==========================================

    public function returnSubmission(
        Request $request,
        SectionSubmission $submission
    )
    {
        $data = $request->validate([
            'remarks' => [
                'required',
                'string',
                'min:5',
                'max:2000',
            ],

            'returned_files' => [
                'required',
                'array',
                'min:1',
            ],

            'returned_files.*' => [
                'required',
                'integer',
                'distinct',
                'exists:submission_files,id',
            ],

            'file_remarks' => [
                'nullable',
                'array',
            ],

            'file_remarks.*' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        DB::transaction(function () use (
            $request,
            $submission,
            $data
        ) {
            $locked = SectionSubmission::query()
                ->whereKey($submission->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'submitted') {
                throw ValidationException::withMessages([
                    'review' =>
                        'Only submitted requirements can be returned.',
                ]);
            }

            $files = $locked->files()
                ->with('requirement')
                ->get();

            $returnedIds = collect($data['returned_files'])
                ->map(fn ($id) => (int) $id)
                ->all();

            // Verify that selected files belong to THIS
            // submission and are not already approved.
            $eligibleIds = $files
                ->filter(
                    fn ($file) =>
                        $file->review_status !== 'approved'
                )
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            if (array_diff($returnedIds, $eligibleIds)) {
                throw ValidationException::withMessages([
                    'returned_files' =>
                        'Invalid file selection. Choose only eligible files from this submission.',
                ]);
            }

            $overallRemarks = trim($data['remarks']);
            $fileRemarks = $data['file_remarks'] ?? [];

            $feedbackSnapshot = [];

            foreach ($files as $file) {

                if (in_array((int) $file->id, $returnedIds, true)) {

                    $specific = trim(
                        (string) ($fileRemarks[$file->id] ?? '')
                    );

                    $reason = $specific !== ''
                        ? $specific
                        : $overallRemarks;

                    $file->update([
                        'review_status' => 'returned',
                        'review_remarks' => $reason,
                    ]);

                    // Keep a copy of the review feedback
                    // even if the teacher later deletes
                    // and replaces this file.
                  $feedbackSnapshot[] = [
                            'file_id' => $file->id,

                            'requirement_id' =>
                                $file->submission_requirement_id,

                            'filename' =>
                                $file->original_name,

                            'requirement' =>
                                $file->requirement?->name
                                ?? 'Unknown Requirement',

                            'remarks' => $reason,
                        ];
                } elseif ($file->review_status !== 'approved') {

                    // Documents not marked for correction
                    // are accepted and locked.
                    $file->update([
                        'review_status' => 'approved',
                        'review_remarks' => null,
                    ]);
                }
            }

            $locked->update([
                'status' => 'returned',
                'reviewed_at' => now('UTC'),
                'principal_remarks' => $overallRemarks,
            ]);

            $locked->reviews()->create([
                'reviewed_by' => $request->user()->id,
                'decision' => 'returned',
                'remarks' => $overallRemarks,
                'file_feedback' => $feedbackSnapshot,
            ]);
        });

        return redirect()
            ->route('principal.submissions.review.show', $submission)
            ->with(
                'success',
                'Submission returned to Teacher with remarks.'
            );
    }

    
    // =====================================================
    // PREVIEW EXCEL FILE IN BROWSER
    // =====================================================

    public function preview(
        SectionSubmission $submission,
        SubmissionFile $file
    ) {
        // ==========================================
        // VERIFY FILE BELONGS TO THIS SUBMISSION
        // ==========================================

        abort_unless(
            (int) $file->section_submission_id ===
            (int) $submission->id,
            404,
            'This file does not belong to the submission.'
        );


        // ==========================================
        // VERIFY PRIVATE STORAGE DISK
        // ==========================================

        abort_unless(
            $file->disk === 'local',
            404,
            'Invalid storage disk.'
        );


        // ==========================================
        // VERIFY FILE EXISTS
        // ==========================================

        abort_unless(
            Storage::disk('local')->exists($file->path),
            404,
            'Uploaded Excel document not found.'
        );


        // ==========================================
        // VALIDATE EXCEL FILE TYPE
        // ==========================================

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
            'Only Excel files can be previewed.'
        );


        // ==========================================
        // DISPLAY EXCEL PREVIEW PAGE
        // ==========================================

        return view(
            'principal.submissions.review.preview',
            compact(
                'submission',
                'file'
            )
        );
    }




}