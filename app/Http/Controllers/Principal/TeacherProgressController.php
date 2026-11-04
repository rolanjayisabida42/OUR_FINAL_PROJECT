<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\SectionSubmission;
use App\Models\Semester;
use App\Models\SubmissionSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class TeacherProgressController extends Controller
{
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | 1. GET SCHOOL YEAR
        |--------------------------------------------------------------------------
        */

        $years = SchoolYear::query()
            ->orderByDesc('id')
            ->get();

        $schoolYear = $years->firstWhere(
            'id',
            $request->integer('year')
        )
            ?? $years->firstWhere('is_active', true)
            ?? $years->first();


        /*
        |--------------------------------------------------------------------------
        | 2. GET SEMESTERS
        |--------------------------------------------------------------------------
        */

        $semesters = $schoolYear
            ? Semester::query()
                ->where('school_year_id', $schoolYear->id)
                ->orderBy('semester_number')
                ->get()
            : collect();

        $today = now('Asia/Manila');

        $currentSemester = $semesters->first(
            function ($item) use ($today) {

                if (!$item->start_date || !$item->end_date) {
                    return false;
                }

                return $today->between(
                    Carbon::parse(
                        $item->start_date,
                        'Asia/Manila'
                    )->startOfDay(),

                    Carbon::parse(
                        $item->end_date,
                        'Asia/Manila'
                    )->endOfDay()
                );
            }
        );

        if (!$currentSemester) {

            $currentSemester = $semesters->first(
                function ($item) use ($today) {

                    return $item->start_date
                        &&
                        Carbon::parse(
                            $item->start_date,
                            'Asia/Manila'
                        )
                            ->startOfDay()
                            ->greaterThan($today);
                }
            );
        }

        $currentSemester ??= $semesters->last();


        /*
        |--------------------------------------------------------------------------
        | 3. FILTERS
        |--------------------------------------------------------------------------
        */

        $semesterFilter = (string) $request->query(
            'semester',
            ''
        );

        if (!in_array(
            $semesterFilter,
            ['all', '1', '2'],
            true
        )) {

            $semesterFilter = $currentSemester
                ? (string) $currentSemester->semester_number
                : 'all';
        }

        $displaySemesters = $semesterFilter === 'all'
            ? $semesters
            : $semesters->where(
                'semester_number',
                (int) $semesterFilter
            )->values();


        $grade = (string) $request->query('grade', '');

        if (!in_array($grade, ['', '11', '12'], true)) {
            $grade = '';
        }


        $statusFilter = (string) $request->query(
            'status',
            'all'
        );

        if (!in_array(
            $statusFilter,
            [
                'all',
                'submitted',
                'approved',
                'returned',
                'draft',
                'not_started',
            ],
            true
        )) {

            $statusFilter = 'all';
        }


        $search = trim(
            (string) $request->query('search', '')
        );


        /*
        |--------------------------------------------------------------------------
        | 4. LOAD CLASSROOMS
        |--------------------------------------------------------------------------
        |
        | Include classrooms even when there
        | are no Teacher submissions.
        |
        */

        $sections = $schoolYear
            ? Section::query()
                ->with([
                    'strand',
                    'adviser.teacher',
                ])
                ->where(
                    'school_year_id',
                    $schoolYear->id
                )
                ->when(
                    $grade !== '',
                    fn ($query) => $query->where(
                        'grade_level',
                        $grade
                    )
                )
                ->when(
                    $search !== '',
                    function ($query) use ($search) {

                        $query->where(
                            function ($nested) use ($search) {

                                $nested->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'adviser.teacher',
                                    function ($teacherQuery) use ($search) {

                                        $teacherQuery->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                );
                            }
                        );
                    }
                )
                ->orderBy('grade_level')
                ->orderBy('name')
                ->get()
            : collect();


        /*
        |--------------------------------------------------------------------------
        | 5. LOAD SETTINGS AND SUBMISSIONS
        |--------------------------------------------------------------------------
        */

        $semesterIds = $displaySemesters->pluck('id');

        $sectionIds = $sections->pluck('id');


        $settings = SubmissionSetting::query()
            ->with([
                'requirements' => fn ($query) =>
                    $query->orderBy('position')
                        ->orderBy('id'),
            ])
            ->whereIn('semester_id', $semesterIds)
            ->get()
            ->keyBy('semester_id');


        $submissions = SectionSubmission::query()
            ->with([
                'teacher',
                'files',
            ])
            ->whereIn('section_id', $sectionIds)
            ->whereIn('semester_id', $semesterIds)
            ->get()
            ->keyBy(
                fn ($item) =>
                    $item->section_id . ':' . $item->semester_id
            );


        /*
        |--------------------------------------------------------------------------
        | 6. CALCULATE TEACHER PROGRESS
        |--------------------------------------------------------------------------
        */

        $allRows = collect();

        $nowUtc = now('UTC');


        foreach ($sections as $section) {

            foreach ($displaySemesters as $semester) {

                $key = $section->id . ':' . $semester->id;

                $submission = $submissions->get($key);

                $setting = $settings->get($semester->id);

                $teacher = $section->adviser?->teacher
                    ?? $submission?->teacher;


                /*
                |--------------------------------------------------------------------------
                | CHECK REQUIREMENT APPLICABILITY
                |--------------------------------------------------------------------------
                */

                $applicable = (bool) $setting
                    &&
                    (bool) $section->is_active
                    &&
                    in_array(
                        (string) $setting->grade_scope,
                        [
                            'both',
                            (string) $section->grade_level
                        ],
                        true
                    );


                $configured = $applicable
                    &&
                    in_array(
                        $setting->status,
                        ['active', 'closed'],
                        true
                    );


                $periodStarted = $configured
                    &&
                    $setting->opens_at
                    &&
                    Carbon::parse(
                        $setting->opens_at
                    )->lte($nowUtc);


                $requirements = $configured
                    ? $setting->requirements
                    : collect();


                /*
                |--------------------------------------------------------------------------
                | GROUP FILES BY REQUIREMENT
                |--------------------------------------------------------------------------
                */

                $filesByRequirement = $submission
                    ? $submission->files->groupBy(
                        'submission_requirement_id'
                    )
                    : collect();


                $approved = 0;
                $uploaded = 0;
                $pending = 0;
                $returned = 0;
                $missing = 0;
                $attention = [];


                /*
                |--------------------------------------------------------------------------
                | CHECK EACH REQUIRED DOCUMENT
                |--------------------------------------------------------------------------
                */

                foreach ($requirements as $requirement) {

                    $files = $filesByRequirement->get(
                        $requirement->id,
                        collect()
                    );


                    $hasReturned = $files->contains(
                        'review_status',
                        'returned'
                    );


                    $allApproved = $files->isNotEmpty()
                        &&
                        $files->every(
                            fn ($file) =>
                                $file->review_status === 'approved'
                        );


                    $hasAvailable = $files->contains(
                        fn ($file) => in_array(
                            $file->review_status,
                            ['pending', 'approved'],
                            true
                        )
                    );


                    /*
                    | Fully approved requirement.
                    */

                    if ($allApproved) {

                        $approved++;
                        $uploaded++;

                    /*
                    | Returned for correction.
                    */

                    } elseif ($hasReturned) {

                        $returned++;

                        if ($periodStarted) {

                            $attention[] =
                                $requirement->name . ' (returned)';
                        }

                    /*
                    | Uploaded, waiting for approval.
                    */

                    } elseif ($hasAvailable) {

                        $uploaded++;
                        $pending++;

                    /*
                    | Missing, provided the submission
                    | period has already started.
                    */

                    } elseif ($periodStarted) {

                        $missing++;

                        $attention[] =
                            $requirement->name . ' (missing)';
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | APPROVAL PERCENTAGE
                |--------------------------------------------------------------------------
                */

                $total = $requirements->count();

                $percentage = $total > 0
                    ? (int) round(
                        ($approved / $total) * 100
                    )
                    : 0;


                /*
                |--------------------------------------------------------------------------
                | SUBMISSION PERIOD STATUS
                |--------------------------------------------------------------------------
                */

                $periodLabel = match (true) {

                    !$section->is_active =>
                        'Archived classroom',

                    !$setting =>
                        'Not configured',

                    !$applicable =>
                        'Not applicable',

                    $setting->status === 'draft' =>
                        'Settings in draft',

                    $setting->status === 'closed' =>
                        'Closed',

                    !$setting->opens_at =>
                        'Not scheduled',

                    !$periodStarted =>
                        'Scheduled',

                    $setting->deadline_at
                        &&
                        Carbon::parse(
                            $setting->deadline_at
                        )->lte($nowUtc) =>
                        'Deadline passed',

                    default =>
                        'Open',
                };


                /*
                |--------------------------------------------------------------------------
                | ADD CLASSROOM PROGRESS RECORD
                |--------------------------------------------------------------------------
                */

                $allRows->push([

                    'section_id' => $section->id,

                    'teacher_id' => $teacher?->id,

                    'teacher' =>
                        $teacher?->name ?? 'Unassigned',

                    'classroom' => $section->name,

                    'grade' => $section->grade_level,

                    'strand' =>
                        $section->strand?->code ?? 'N/A',

                    'semester' =>
                        (int) $semester->semester_number,

                    'submission' => $submission,

                    'status' =>
                        $submission?->status ?? 'not_started',

                    'period_label' => $periodLabel,

                    'total' => $total,

                    'approved' => $approved,

                    'uploaded' => $uploaded,

                    'pending' => $pending,

                    'returned' => $returned,

                    'missing' => $missing,

                    'percentage' => $percentage,

                    'attention' => $attention,
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | 7. SUMMARY STATISTICS
        |--------------------------------------------------------------------------
        |
        | These use the selected year, semester,
        | grade and search criteria.
        |
        | The status dropdown does not change
        | the summary totals.
        |
        */

        $stats = [

            'teachers' => $allRows
                ->pluck('teacher_id')
                ->filter()
                ->unique()
                ->count(),

            'classrooms' => $sections->count(),

            'submitted' => $allRows
                ->where('status', 'submitted')
                ->count(),

            'approved' => $allRows
                ->where('status', 'approved')
                ->count(),

            'returned' => $allRows
                ->where('status', 'returned')
                ->count(),

            'approved_requirements' =>
                $allRows->sum('approved'),

            'required_requirements' =>
                $allRows->sum('total'),

            'missing_requirements' =>
                $allRows->sum('missing'),
        ];


        /*
        |--------------------------------------------------------------------------
        | 8. APPLY SUBMISSION STATUS FILTER
        |--------------------------------------------------------------------------
        */

        $filteredRows = $allRows
            ->filter(
                fn ($row) =>
                    $statusFilter === 'all'
                    ||
                    $row['status'] === $statusFilter
            )
            ->values();


        /*
        |--------------------------------------------------------------------------
        | 9. PAGINATE CLASSROOM ROWS
        |--------------------------------------------------------------------------
        */

        $page = max(
            1,
            LengthAwarePaginator::resolveCurrentPage()
        );

        $rows = new LengthAwarePaginator(

            $filteredRows
                ->forPage($page, 12)
                ->values(),

            $filteredRows->count(),

            12,

            $page,

            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 10. DISPLAY PAGE
        |--------------------------------------------------------------------------
        */

        return view(
            'principal.teacher-progress.index',
            compact(
                'years',
                'schoolYear',
                'semesters',
                'semesterFilter',
                'grade',
                'statusFilter',
                'search',
                'stats',
                'rows'
            )
        );
    }
}
