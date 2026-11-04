<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\SectionSubmission;
use App\Models\Semester;
use App\Models\SubmissionSetting;
use Carbon\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PRINCIPAL DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index(): View
    {
        return view(
            'principal.dashboard',
            $this->dashboardData()
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORT REAL MONITORING REPORT
    |--------------------------------------------------------------------------
    */

    public function export(): StreamedResponse
    {
        $data = $this->dashboardData();

        $year = $data['schoolYear']?->name ?? 'none';

        $semester = $data['semester']?->semester_number ?? 'none';

        $safeYear = preg_replace(
            '/[^A-Za-z0-9-]/',
            '-',
            $year
        );

        $filename = 'shs-monitoring-'
            . $safeYear
            . '-sem-'
            . $semester
            . '.csv';

        return response()->streamDownload(
            function () use ($data) {

                $output = fopen('php://output', 'w');

                if ($output === false) {
                    return;
                }

                // UTF-8 BOM for Microsoft Excel.
                fwrite($output, "\xEF\xBB\xBF");

                fputcsv($output, [
                    'School Year',
                    'Semester',
                    'Classroom',
                    'Grade',
                    'Strand',
                    'Teacher',
                    'Requirement',
                    'Document Status',
                    'Submission Status',
                    'Submitted At (PHT)',
                    'Principal Remarks',
                ]);

                foreach ($data['reportRows'] as $row) {

                    // Prevent spreadsheet formula injection.
                    $safeRow = array_map(
                        function ($cell) {

                            $value = (string) ($cell ?? '');

                            if (preg_match('/^\s*[=+\-@]/u', $value)) {
                                return "'" . $value;
                            }

                            return $value;
                        },
                        $row
                    );

                    fputcsv($output, $safeRow);
                }

                fclose($output);
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD DASHBOARD DATA FROM DATABASE
    |--------------------------------------------------------------------------
    */

    private function dashboardData(): array
    {
        /*
        |--------------------------------------------------------------------------
        | 1. GET ACTIVE SCHOOL YEAR
        |--------------------------------------------------------------------------
        */

        $schoolYear = SchoolYear::query()
            ->where('is_active', true)
            ->orderByDesc('id')
            ->first();

        // Fallback when no school year is active.
        if (!$schoolYear) {

            $schoolYear = SchoolYear::query()
                ->orderByDesc('id')
                ->first();
        }


        /*
        |--------------------------------------------------------------------------
        | 2. GET ALL SEMESTERS OF THE SCHOOL YEAR
        |--------------------------------------------------------------------------
        */

        $semesters = $schoolYear
            ? Semester::query()
                ->where('school_year_id', $schoolYear->id)
                ->orderBy('semester_number')
                ->get()
            : collect();

        $semesterIds = $semesters->pluck('id');


        /*
        |--------------------------------------------------------------------------
        | 3. DETERMINE DISPLAYED SEMESTER
        |--------------------------------------------------------------------------
        |
        | We still use the current semester for:
        | - Teacher progress
        | - Missing requirements
        | - Outstanding documents
        | - CSV document report
        |
        | But the status cards will count BOTH semesters.
        |
        */

        $today = now('Asia/Manila');

        // Find semester whose dates include today.
        $semester = $semesters->first(
            function ($item) use ($today) {

                if (
                    !$item->start_date ||
                    !$item->end_date
                ) {
                    return false;
                }

                $start = Carbon::parse(
                    $item->start_date,
                    'Asia/Manila'
                )->startOfDay();

                $end = Carbon::parse(
                    $item->end_date,
                    'Asia/Manila'
                )->endOfDay();

                return $today->between(
                    $start,
                    $end,
                    true
                );
            }
        );


        // If between semesters, find the next one.
        if (!$semester) {

            $semester = $semesters->first(
                function ($item) use ($today) {

                    if (!$item->start_date) {
                        return false;
                    }

                    $start = Carbon::parse(
                        $item->start_date,
                        'Asia/Manila'
                    )->startOfDay();

                    return $start->greaterThan($today);
                }
            );
        }


        // If no active or future semester, use the last.
        $semester ??= $semesters->last();


        /*
        |--------------------------------------------------------------------------
        | 4. COUNT SUBMISSIONS ACROSS THE ENTIRE SCHOOL YEAR
        |--------------------------------------------------------------------------
        |
        | IMPORTANT FIX:
        |
        | Previously, the dashboard counted submissions
        | only in the displayed semester.
        |
        | Now we count statuses across ALL semesters
        | belonging to the active school year.
        |
        | Example:
        |
        | First Semester:
        |   1 Approved
        |
        | Second Semester:
        |   0 Approved
        |
        | Dashboard Approved Total:
        |   1 Approved
        |
        */

        $yearCounts = SectionSubmission::query()
            ->whereIn('semester_id', $semesterIds)
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');


        /*
        |--------------------------------------------------------------------------
        | 5. LOAD ACTIVE CLASSROOMS
        |--------------------------------------------------------------------------
        */

        $sections = $schoolYear
            ? Section::query()
                ->with([
                    'strand',
                    'schoolYear',
                    'adviser.teacher',
                ])
                ->where(
                    'school_year_id',
                    $schoolYear->id
                )
                ->where('is_active', true)
                ->orderBy('grade_level')
                ->orderBy('name')
                ->get()
            : collect();

        $sectionIds = $sections->pluck('id');


        /*
        |--------------------------------------------------------------------------
        | 6. LOAD SUBMISSIONS FOR THE DISPLAYED SEMESTER
        |--------------------------------------------------------------------------
        |
        | These are used for per-classroom progress
        | and missing requirement calculations.
        |
        */

        $semesterSubmissions = $semester
            ? SectionSubmission::query()
                ->with([
                    'section.schoolYear',
                    'section.strand',
                    'teacher',
                    'semester',
                    'files',
                ])
                ->withCount('files')
                ->where('semester_id', $semester->id)
                ->whereIn('section_id', $sectionIds)
                ->orderByDesc('submitted_at')
                ->orderByDesc('id')
                ->get()
            : collect();


        $submissionsBySection = $semesterSubmissions
            ->keyBy('section_id');


        /*
        |--------------------------------------------------------------------------
        | 7. LOAD RECENT SUBMISSIONS FROM BOTH SEMESTERS
        |--------------------------------------------------------------------------
        |
        | This makes the recent submission table
        | consistent with the school-year status cards.
        |
        */

        $recentSubmissions = SectionSubmission::query()
            ->with([
                'section.schoolYear',
                'section.strand',
                'teacher',
                'semester',
            ])
            ->withCount('files')
            ->whereIn('semester_id', $semesterIds)
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->limit(12)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 8. LOAD APPROVED AND RETURNED SUBMISSIONS
        |--------------------------------------------------------------------------
        |
        | These are current submission statuses.
        | Previous decisions remain in review history.
        |
        */

        $history = SectionSubmission::query()
            ->with([
                'section.schoolYear',
                'section.strand',
                'teacher',
                'semester',
            ])
            ->whereIn('semester_id', $semesterIds)
            ->whereIn('status', [
                'approved',
                'returned',
            ])
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 9. LOAD SUBMISSION SETTINGS AND REQUIREMENTS
        |--------------------------------------------------------------------------
        */

        $setting = $semester
            ? SubmissionSetting::query()
                ->with([
                    'requirements' => function ($query) {
                        $query->orderBy('position');
                    },
                ])
                ->where('semester_id', $semester->id)
                ->first()
            : null;


        /*
        |--------------------------------------------------------------------------
        | 10. PREPARE MONITORING COLLECTIONS
        |--------------------------------------------------------------------------
        */

        $progress = collect();

        $outstanding = collect();

        $reportRows = collect();

        $missingCount = 0;


        /*
        |--------------------------------------------------------------------------
        | 11. PROCESS EACH ACTIVE CLASSROOM
        |--------------------------------------------------------------------------
        */

        foreach ($sections as $section) {

            $submission = $submissionsBySection->get(
                $section->id
            );

            $teacherName =
                $section->adviser?->teacher?->name
                ?? 'Unassigned';

            $grade = (string) $section->grade_level;


            /*
            |--------------------------------------------------------------------------
            | CHECK IF REQUIREMENTS APPLY TO THIS CLASSROOM
            |--------------------------------------------------------------------------
            */

            $applies = $setting
                &&
                in_array(
                    (string) $setting->grade_scope,
                    ['both', $grade],
                    true
                );


            /*
            |--------------------------------------------------------------------------
            | CHECK IF THE SUBMISSION PERIOD STARTED
            |--------------------------------------------------------------------------
            */

            $periodStarted = $setting?->opens_at
                &&
                Carbon::parse(
                    $setting->opens_at
                )->lte(now('UTC'));


            /*
            |--------------------------------------------------------------------------
            | CHECK WHETHER REQUIREMENTS SHOULD BE MONITORED
            |--------------------------------------------------------------------------
            */

            $configured = $applies
                &&
                in_array(
                    $setting->status,
                    ['active', 'closed'],
                    true
                )
                &&
                $periodStarted;


            $requirements = $configured
                ? $setting->requirements
                : collect();


            /*
            |--------------------------------------------------------------------------
            | GROUP UPLOADED DOCUMENTS BY REQUIREMENT
            |--------------------------------------------------------------------------
            */

            $filesByRequirement = $submission
                ? $submission->files->groupBy(
                    'submission_requirement_id'
                )
                : collect();


            $approved = 0;

            $uploaded = 0;


            /*
            |--------------------------------------------------------------------------
            | 12. CHECK EACH REQUIRED DOCUMENT
            |--------------------------------------------------------------------------
            */

            foreach ($requirements as $requirement) {

                $files = $filesByRequirement->get(
                    $requirement->id,
                    collect()
                );


                /*
                |--------------------------------------------------------------------------
                | CHECK RETURNED FILES
                |--------------------------------------------------------------------------
                */

                $hasReturned = $files->contains(
                    'review_status',
                    'returned'
                );


                /*
                |--------------------------------------------------------------------------
                | CHECK UPLOADED FILES
                |--------------------------------------------------------------------------
                |
                | Pending and approved files count
                | as uploaded.
                |
                */

                $hasAvailable = $files->contains(
                    function ($file) {

                        return in_array(
                            $file->review_status,
                            ['pending', 'approved'],
                            true
                        );
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | CHECK APPROVED REQUIREMENT
                |--------------------------------------------------------------------------
                |
                | A requirement is fully approved when
                | it has files and every file is approved.
                |
                | This prevents empty requirements
                | from being counted as approved.
                |
                */

                $isApproved = $files->isNotEmpty()
                    &&
                    $files->every(
                        fn ($file) =>
                            $file->review_status === 'approved'
                    );


                /*
                |--------------------------------------------------------------------------
                | COUNT UPLOADED REQUIREMENTS
                |--------------------------------------------------------------------------
                */

                if ($hasAvailable && !$hasReturned) {
                    $uploaded++;
                }


                /*
                |--------------------------------------------------------------------------
                | COUNT APPROVED REQUIREMENTS
                |--------------------------------------------------------------------------
                */

                if ($isApproved) {
                    $approved++;
                }


                /*
                |--------------------------------------------------------------------------
                | DETERMINE DOCUMENT STATUS
                |--------------------------------------------------------------------------
                */

                if ($hasReturned) {

                    $documentStatus = 'Returned';

                } elseif ($isApproved) {

                    $documentStatus = 'Approved';

                } elseif ($hasAvailable) {

                    $documentStatus = 'Awaiting Review';

                } else {

                    $documentStatus = 'Missing';
                }


                /*
                |--------------------------------------------------------------------------
                | COUNT OUTSTANDING REQUIREMENTS
                |--------------------------------------------------------------------------
                */

                if (
                    $documentStatus === 'Returned'
                    ||
                    $documentStatus === 'Missing'
                ) {

                    $missingCount++;

                    $outstanding->push([

                        'teacher' => $teacherName,

                        'classroom' => $section->name,

                        'requirement' => $requirement->name,

                        'status' => strtolower(
                            $documentStatus
                        ),

                        'url' => $submission
                            ? route(
                                'principal.submissions.review.show',
                                $submission
                            )
                            : route(
                                'principal.submissions.index'
                            ),
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | ADD ROW TO CSV REPORT
                |--------------------------------------------------------------------------
                */

                $reportRows->push([

                    // School Year
                    $schoolYear?->name ?? '',

                    // Semester
                    $semester?->semester_number ?? '',

                    // Classroom
                    $section->name,

                    // Grade
                    $grade,

                    // Strand
                    $section->strand?->code ?? '',

                    // Teacher
                    $teacherName,

                    // Requirement
                    $requirement->name,

                    // Document Status
                    $documentStatus,

                    // Submission Status
                    ucfirst(
                        $submission?->status
                        ?? 'Not Started'
                    ),

                    // Submitted Date
                    $submission?->submitted_at
                        ? Carbon::parse(
                            $submission->submitted_at
                        )
                            ->timezone('Asia/Manila')
                            ->format('Y-m-d h:i A')
                        : '',

                    // Principal Remarks
                    $submission?->principal_remarks ?? '',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | 13. CALCULATE CLASSROOM APPROVAL PROGRESS
            |--------------------------------------------------------------------------
            */

            $total = $requirements->count();

            $percent = $total > 0
                ? (int) round(
                    ($approved / $total) * 100
                )
                : 0;


            $progress->push([

                'teacher' => $teacherName,

                'classroom' => $section->name,

                'grade' => $grade,

                'approved' => $approved,

                'uploaded' => $uploaded,

                'total' => $total,

                'percent' => $percent,

                'configured' => (bool) $configured,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 14. PREPARE FINAL DASHBOARD DATA
        |--------------------------------------------------------------------------
        */

        return [

            // ==================================
            // ACADEMIC INFORMATION
            // ==================================

            'schoolYear' => $schoolYear,

            'semester' => $semester,

            'setting' => $setting,


            // ==================================
            // DASHBOARD STATISTICS
            // ==================================
            //
            // Submitted, Approved, Returned:
            // ALL semesters of the displayed SY.
            //
            // Missing and Classrooms:
            // Displayed semester / active SY.
            //
            // ==================================

            'stats' => [

                'submitted' => (int) $yearCounts->get(
                    'submitted',
                    0
                ),

                'approved' => (int) $yearCounts->get(
                    'approved',
                    0
                ),

                'returned' => (int) $yearCounts->get(
                    'returned',
                    0
                ),

                'missing' => $missingCount,

                'classrooms' => $sections->count(),
            ],


            // ==================================
            // RECENT TEACHER SUBMISSIONS
            // ==================================

            'recentSubmissions' => $recentSubmissions,


            // ==================================
            // APPROVED AND RETURNED HISTORY
            // ==================================

            'history' => $history,


            // ==================================
            // TEACHER CLASSROOM PROGRESS
            // ==================================

            'progress' => $progress,


            // ==================================
            // OUTSTANDING REQUIREMENTS
            // ==================================

            'outstanding' => $outstanding
                ->sortBy(
                    fn ($row) =>
                        $row['status'] === 'returned'
                            ? 0
                            : 1
                )
                ->take(10)
                ->values(),


            // ==================================
            // CSV MONITORING REPORT
            // ==================================

            'reportRows' => $reportRows,
        ];
    }
}
