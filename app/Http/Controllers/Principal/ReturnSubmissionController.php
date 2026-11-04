<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SectionSubmission;
use App\Services\SchoolFormNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ReturnSubmissionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST ALL CLASSROOMS
    |--------------------------------------------------------------------------
    |
    | Shows classrooms even when the Teacher
    | has not submitted requirements yet.
    |
    */

    public function queue(Request $request): View
    {
        abort_unless(
            $request->user()?->role === 'principal',
            403
        );

        $sections = Section::query()
            ->with([
                'strand',
                'schoolYear',
                'adviser.teacher'
            ])
            ->orderBy('grade_level')
            ->orderBy('name')
            ->paginate(12);

        /*
        | Load submissions belonging to classrooms
        | displayed on the current page.
        */

        $submissionGroups = SectionSubmission::query()
            ->with('semester')
            ->withCount([
                'files as pending_files_count' => fn ($query) =>
                    $query->where('review_status', 'pending'),
            ])
            ->whereIn(
                'section_id',
                $sections->getCollection()->pluck('id')
            )
            ->get()
            ->groupBy('section_id');

        return view(
            'principal.submissions.review.return-queue',
            compact(
                'sections',
                'submissionGroups'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW RETURN SUBMISSION FORM
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        SectionSubmission $submission
    ): View {

        abort_unless(
            $request->user()?->role === 'principal',
            403
        );

        /*
        | Fetch classroom using section_id.
        | If missing, return 404 instead of N/A.
        */

        $section = Section::query()
            ->with([
                'strand',
                'schoolYear'
            ])
            ->findOrFail($submission->section_id);

        $submission->load([
            'teacher',
            'semester',
            'files.requirement'
        ]);

        $submission->setRelation(
            'section',
            $section
        );

        $files = $submission->files
            ->sortBy('submission_requirement_id')
            ->values();

        $canReturn =
            $submission->status === 'submitted'
            &&
            $files->contains(
                'review_status',
                'pending'
            );

        return view(
            'principal.submissions.review.return',
            compact(
                'submission',
                'section',
                'files',
                'canReturn'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RETURN SELECTED DOCUMENTS
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        SectionSubmission $submission
    ): RedirectResponse {

        abort_unless(
            $request->user()?->role === 'principal',
            403
        );

        /*
        | Validate Principal remarks and selected files.
        */

        $validated = $request->validate([

            'returned_files' => [
                'required',
                'array',
                'min:1'
            ],

            'returned_files.*' => [
                'required',
                'integer',
                'distinct'
            ],

            'remarks' => [
                'required',
                'string',
                'min:10',
                'max:2000'
            ],

            'file_remarks' => [
                'nullable',
                'array'
            ],

            'file_remarks.*' => [
                'nullable',
                'string',
                'max:1000'
            ],
        ]);

        $selectedIds = collect(
            $validated['returned_files']
        )
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();


        /*
        |--------------------------------------------------------------------------
        | PROCESS RETURN TRANSACTION
        |--------------------------------------------------------------------------
        */

        $updatedSubmission = DB::transaction(
            function () use (
                $request,
                $submission,
                $validated,
                $selectedIds
            ) {

                /*
                | Lock submission before review.
                */

                $locked = SectionSubmission::query()
                    ->whereKey($submission->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($locked->status !== 'submitted') {

                    throw ValidationException::withMessages([

                        'returned_files' =>
                            'This submission has already been reviewed or changed. Refresh the page.',
                    ]);
                }

                /*
                | Load only files belonging
                | to this submission.
                */

                $files = $locked->files()
                    ->with('requirement')
                    ->lockForUpdate()
                    ->get();

                /*
                | Check selected IDs are valid
                | and pending Principal review.
                */

                $selected = $files->filter(
                    fn ($file) =>
                        in_array(
                            (int) $file->id,
                            $selectedIds,
                            true
                        )
                        &&
                        $file->review_status === 'pending'
                );

                if (
                    $selected->count() !==
                    count($selectedIds)
                ) {

                    throw ValidationException::withMessages([
                        'returned_files' =>
                            'Some selected files are not pending or do not belong to this classroom submission.',
                    ]);
                }

                $feedback = [];


                /*
                |--------------------------------------------------------------------------
                | REVIEW EACH DOCUMENT
                |--------------------------------------------------------------------------
                */

                foreach ($files as $file) {

                    /*
                    | Keep previously approved files.
                    */

                    if ($file->review_status === 'approved') {
                        continue;
                    }

                    if ($file->review_status !== 'pending') {

                        throw ValidationException::withMessages([
                            'returned_files' =>
                                'An unexpected file review status was found. Refresh the page.',
                        ]);
                    }

                    /*
                    | Mark selected files as returned.
                    */

                    if (
                        in_array(
                            (int) $file->id,
                            $selectedIds,
                            true
                        )
                    ) {

                        $note = trim(
                            (string) (
                                $validated['file_remarks'][$file->id]
                                ?? ''
                            )
                        );

                        /*
                        | Use overall remarks as fallback.
                        */

                        $note = $note !== ''
                            ? $note
                            : $validated['remarks'];

                        $file->update([

                            'review_status' => 'returned',

                            'review_remarks' => $note,

                        ]);

                        /*
                        | Save feedback for Teacher
                        | correction workflow.
                        */

                        $feedback[] = [

                            'file_id' => $file->id,

                            'requirement_id' =>
                                $file->submission_requirement_id,

                            'requirement' =>
                                $file->requirement?->name
                                ?? 'Requirement',

                            'filename' =>
                                $file->original_name,

                            'remarks' => $note,

                        ];

                    } else {

                        /*
                        | Unselected pending files
                        | are approved.
                        */

                        $file->update([

                            'review_status' => 'approved',

                            'review_remarks' => null,

                        ]);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | UPDATE SUBMISSION STATUS
                |--------------------------------------------------------------------------
                */

                $locked->update([

                    'status' => 'returned',

                    'principal_remarks' =>
                        $validated['remarks'],

                    'reviewed_at' => now('UTC'),

                ]);


                /*
                |--------------------------------------------------------------------------
                | SAVE REVIEW HISTORY
                |--------------------------------------------------------------------------
                */

                $locked->reviews()->create([

                    'reviewer_id' =>
                        $request->user()->id,

                    'decision' => 'returned',

                    'remarks' =>
                        $validated['remarks'],

                    'file_feedback' => $feedback,

                ]);

                return $locked->fresh();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | NOTIFY THE TEACHER
        |--------------------------------------------------------------------------
        |
        | Send notification after database commit.
        */

        try {

            app(SchoolFormNotifier::class)
                ->reviewed(
                    $updatedSubmission,
                    'returned',
                    $validated['remarks']
                );

        } catch (Throwable $exception) {

            report($exception);
        }


        /*
        |--------------------------------------------------------------------------
        | SUCCESS REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'principal.submissions.review.show',
                $submission
            )
            ->with(
                'success',
                'The selected documents were returned to the Teacher for correction.'
            );
    }
}
