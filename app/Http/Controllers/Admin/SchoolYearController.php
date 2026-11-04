<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolYear;
use App\Models\Semester;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolYearController extends Controller
{
    
    // ADJUST BASED ON YOUR SCHOOL'S POLICY
    private const MIN_SEMESTER_DAYS = 60;
    private const MAX_BREAK_DAYS = 60;

      // ==========================================
    // DISPLAY SCHOOL YEARS AND PERIODS
    // ==========================================
    public function index(Request $request)
    {
        $schoolYears = SchoolYear::with('semesters')
            ->orderByDesc('is_active')
            ->orderByDesc('start_date')
            ->paginate(8);

        $selectedYear = SchoolYear::with('semesters')
            ->find($request->integer('year_id'));

        if (!$selectedYear) {
            $selectedYear = SchoolYear::with('semesters')
                ->where('is_active', true)
                ->first();
        }

        if (!$selectedYear) {
            $selectedYear = SchoolYear::with('semesters')
                ->orderByDesc('start_date')
                ->first();
        }

        $stats = [
            'school_years' => SchoolYear::count(),
            'periods' => Semester::count(),
            'active_year' => SchoolYear::where('is_active', true)
                ->value('name'),
        ];

        return view('admin.school-years.index', compact(
            'schoolYears',
            'selectedYear',
            'stats'
        ));
    }


      // ==========================================
    // CREATE SCHOOL YEAR
    // ==========================================
    public function store(Request $request)
    {
        $validated = $request->validateWithBag('yearCreate', [
            'name' => [
                'required',
                'regex:/^\d{4}-\d{4}$/',
                'unique:school_years,name',
            ],
            'start_date' => ['required', 'date'],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
        ]);

        // VALIDATE CONSECUTIVE YEARS AND DATES
        $error = $this->schoolYearDateError($validated);

        if ($error) {
            return back()
                ->withErrors(['name' => $error], 'yearCreate')
                ->withInput();
        }

        // NEW SCHOOL YEARS MUST BE CONFIGURED FIRST
        $validated['is_active'] = false;

        $schoolYear = SchoolYear::create($validated);

        return redirect()
            ->route('admin.school-years.index', [
                'year_id' => $schoolYear->id,
            ])
            ->with(
                'success',
                'School year created! Configure both semesters before activation.'
            );
    }



     // ==========================================
    // UPDATE SCHOOL YEAR
    // ==========================================
    public function update(Request $request, SchoolYear $schoolYear)
    {
        $validated = $request->validateWithBag('yearEdit', [
            'name' => [
                'required',
                'regex:/^\d{4}-\d{4}$/',
                Rule::unique('school_years', 'name')
                    ->ignore($schoolYear->id),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
        ]);

        // VALIDATE YEAR NAME AND DATE CONSISTENCY
        $error = $this->schoolYearDateError($validated);

        if ($error) {
            return back()
                ->withErrors(['name' => $error], 'yearEdit')
                ->withInput();
        }

        // DO NOT EXCLUDE EXISTING SEMESTER DATES
        $invalidPeriods = $schoolYear->semesters()
            ->where(function ($query) use ($validated) {
                $query->whereDate(
                    'start_date',
                    '<',
                    $validated['start_date']
                )->orWhereDate(
                    'end_date',
                    '>',
                    $validated['end_date']
                );
            })
            ->exists();

        if ($invalidPeriods) {
            return back()
                ->withErrors([
                    'date_range' =>
                        'New school year dates must include all existing semesters.',
                ], 'yearEdit')
                ->withInput();
        }

        // CHECK WHETHER YEAR DETAILS ARE CHANGING
        $hasChanges =
            $schoolYear->name !== $validated['name'] ||
            Carbon::parse($schoolYear->start_date)->toDateString()
                !== $validated['start_date'] ||
            Carbon::parse($schoolYear->end_date)->toDateString()
                !== $validated['end_date'];

        // PROTECT RECORDS LINKED TO THE SCHOOL YEAR
        if (
            $hasChanges &&
            $this->schoolYearHasDependencies($schoolYear)
        ) {
            return back()
                ->withErrors([
                    'date_range' =>
                        'This school year already has linked academic records. '
                        . 'Its name and dates cannot be changed.',
                ], 'yearEdit')
                ->withInput();
        }

        $schoolYear->update($validated);

        return redirect()
            ->route('admin.school-years.index', [
                'year_id' => $schoolYear->id,
            ])
            ->with(
                'success',
                'School year updated successfully!'
            );
    }


     // ==========================================
    // SET ACTIVE SCHOOL YEAR
    // ==========================================
    public function activate(SchoolYear $schoolYear)
    {
        return DB::transaction(function () use ($schoolYear) {

            // LOCK SCHOOL YEAR ROWS DURING ACTIVATION
            $years = SchoolYear::query()
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $selectedYear = $years->firstWhere(
                'id',
                $schoolYear->id
            );

            abort_if(!$selectedYear, 404);

            $selectedYear->load('semesters');

            $first = $selectedYear->semesters
                ->firstWhere('semester_number', 1);

            $second = $selectedYear->semesters
                ->firstWhere('semester_number', 2);

            // REQUIRE BOTH SEMESTERS
            if (!$first || !$second) {
                return back()->with(
                    'error',
                    'Configure both First and Second Semester before activating this school year.'
                );
            }

            // RECHECK SCHOOL YEAR CONSISTENCY
            $yearError = $this->schoolYearDateError([
                'name' => $selectedYear->name,
                'start_date' => $selectedYear->start_date,
                'end_date' => $selectedYear->end_date,
            ]);

            if ($yearError) {
                return back()->with('error', $yearError);
            }

            // VALIDATE FIRST SEMESTER
            $firstError = $this->semesterDateError(
                $selectedYear,
                1,
                $first->start_date,
                $first->end_date,
                $second
            );

            if ($firstError) {
                return back()->with('error', $firstError);
            }

            // VALIDATE SECOND SEMESTER
            $secondError = $this->semesterDateError(
                $selectedYear,
                2,
                $second->start_date,
                $second->end_date,
                $first
            );

            if ($secondError) {
                return back()->with('error', $secondError);
            }

            // DEACTIVATE OTHER SCHOOL YEARS
            SchoolYear::where('is_active', true)
                ->update(['is_active' => false]);

            // ACTIVATE SELECTED YEAR
            $selectedYear->update([
                'is_active' => true,
            ]);

            return redirect()
                ->route('admin.school-years.index', [
                    'year_id' => $selectedYear->id,
                ])
                ->with(
                    'success',
                    'Active school year updated successfully!'
                );
        });
    }


      // ==========================================
    // CREATE OR UPDATE SEMESTER
    // ==========================================
    public function savePeriod(
        Request $request,
        SchoolYear $schoolYear) 
    {
        $validated = $request->validateWithBag('period', [
            'semester_number' => [
                'required',
                'integer',
                Rule::in([1, 2]),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
        ]);

        $number = (int) $validated['semester_number'];

        return DB::transaction(function () use (
            $schoolYear,
            $validated,
            $number
        ) {

            // LOCK PARENT YEAR TO COORDINATE DATE CHANGES
            $year = SchoolYear::whereKey($schoolYear->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existingSemester = $year->semesters()
                ->where('semester_number', $number)
                ->first();

            $otherSemester = $year->semesters()
                ->where(
                    'semester_number',
                    $number === 1 ? 2 : 1
                )
                ->first();

            // CHECK ALL SEMESTER DATE RULES
            $error = $this->semesterDateError(
                $year,
                $number,
                $validated['start_date'],
                $validated['end_date'],
                $otherSemester
            );

            if ($error) {
                return back()
                    ->withErrors([
                        'dates' => $error,
                    ], 'period')
                    ->withInput();
            }

            // DETECT CHANGES TO AN EXISTING SEMESTER
            if ($existingSemester) {

                $datesChanged =
                    Carbon::parse($existingSemester->start_date)
                        ->toDateString()
                        !== $validated['start_date'] ||
                    Carbon::parse($existingSemester->end_date)
                        ->toDateString()
                        !== $validated['end_date'];

                // PROTECT EXISTING LINKED RECORDS
                if (
                    $datesChanged &&
                    $this->semesterHasDependencies($existingSemester)
                ) {
                    return back()
                        ->withErrors([
                            'dates' =>
                                'This semester already has linked academic records. '
                                . 'Its dates cannot be changed.',
                        ], 'period')
                        ->withInput();
                }
            }

            // CREATE OR UPDATE
            $year->semesters()->updateOrCreate(
                [
                    'semester_number' => $number,
                ],
                [
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                ]
            );

            return redirect()
                ->route('admin.school-years.index', [
                    'year_id' => $year->id,
                ])
                ->with(
                    'success',
                    'Academic period saved successfully!'
                );
        });
    }


     // ==========================================
    // VALIDATE SCHOOL YEAR NAME AND DATES
    // ==========================================
    private function schoolYearDateError(array $data): ?string
    {
        [$firstYear, $secondYear] = array_map(
            'intval',
            explode('-', $data['name'])
        );

        // EXAMPLE: 2026-2027 IS VALID
        // EXAMPLE: 2026-2029 IS INVALID
        if ($secondYear !== $firstYear + 1) {
            return 'School years must be consecutive, such as 2026-2027.';
        }

        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);

        // CHECK START AND END DATE YEARS
        if (
            $start->year !== $firstYear ||
            $end->year !== $secondYear
        ) {
            return 'The start and end dates must match the school year. '
                . 'For example, 2026-2027 must start in 2026 and end in 2027.';
        }

        if ($end->lte($start)) {
            return 'The school year end date must be later than the start date.';
        }

        return null;
    }


    // ==========================================
    // VALIDATE SEMESTER SCHEDULE
    // ==========================================
    private function semesterDateError(SchoolYear $schoolYear, int $number, $startDate, $endDate, ?Semester $otherSemester = null): ?string 
    {

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $yearStart = Carbon::parse($schoolYear->start_date);
        $yearEnd = Carbon::parse($schoolYear->end_date);

        // END MUST FOLLOW START
        if ($end->lte($start)) {
            return 'Semester end date must be after the start date.';
        }

        // SEMESTER MUST BE WITHIN THE SCHOOL YEAR
        if (
            $start->lt($yearStart) ||
            $end->gt($yearEnd)
        ) {
            return 'Semester dates must fall within the school year.';
        }

        // MINIMUM SEMESTER LENGTH
        $duration = $start->diffInDays($end) + 1;

        if ($duration < self::MIN_SEMESTER_DAYS) {
            return 'A semester must last at least '
                . self::MIN_SEMESTER_DAYS
                . ' calendar days.';
        }

        // CHECK ORDER AND GAP BETWEEN SEMESTERS
        if ($otherSemester) {

            $otherStart = Carbon::parse(
                $otherSemester->start_date
            );

            $otherEnd = Carbon::parse(
                $otherSemester->end_date
            );

            if ($number === 1) {

                // FIRST SEMESTER MUST END BEFORE SECOND
                if ($end->gte($otherStart)) {
                    return 'First Semester must end before Second Semester begins. '
                        . 'Overlapping periods are not allowed.';
                }

                $breakDays = $end->diffInDays($otherStart) - 1;

            } else {

                // SECOND SEMESTER MUST START AFTER FIRST
                if ($start->lte($otherEnd)) {
                    return 'Second Semester must begin after First Semester ends. '
                        . 'Overlapping periods are not allowed.';
                }

                $breakDays = $otherEnd->diffInDays($start) - 1;
            }

            // MAXIMUM ALLOWED BREAK BETWEEN SEMESTERS
            if ($breakDays > self::MAX_BREAK_DAYS) {
                return 'The break between semesters cannot exceed '
                    . self::MAX_BREAK_DAYS
                    . ' calendar days.';
            }
        }

        return null;
    }


    // ==========================================
    // CHECK LINKED SCHOOL YEAR RECORDS
    // ==========================================
    private function schoolYearHasDependencies(SchoolYear $schoolYear): bool 
    {

        // EXPECTED FUTURE MODULE TABLES
        $tables = [
            'sections',
            'classrooms',
            'teacher_assignments',
            'students',
            'enrollments',
            'student_enrollments',
            'requirements',
            'submissions',
            'generated_forms',
        ];

        $semesterIds = $schoolYear->semesters()
            ->pluck('id');

        foreach ($tables as $table) {

            // SKIP TABLES NOT CREATED YET
            if (!Schema::hasTable($table)) {
                continue;
            }

            // CHECK DIRECT SCHOOL YEAR REFERENCES
            if (Schema::hasColumn($table, 'school_year_id')) {

                if (
                    DB::table($table)
                        ->where('school_year_id', $schoolYear->id)
                        ->exists()
                ) {
                    return true;
                }
            }

            // CHECK REFERENCES THROUGH SEMESTERS
            if (
                $semesterIds->isNotEmpty() &&
                Schema::hasColumn($table, 'semester_id')
            ) {
                if (
                    DB::table($table)
                        ->whereIn('semester_id', $semesterIds)
                        ->exists()
                ) {
                    return true;
                }
            }
        }

        return false;
    }


      // ==========================================
    // CHECK LINKED SEMESTER RECORDS
    // ==========================================
    private function semesterHasDependencies(Semester $semester): bool 
    {

        $tables = [
            'teacher_assignments',
            'enrollments',
            'student_enrollments',
            'requirements',
            'submissions',
            'generated_forms',
        ];

        foreach ($tables as $table) {

            if (!Schema::hasTable($table)) {
                continue;
            }

            if (!Schema::hasColumn($table, 'semester_id')) {
                continue;
            }

            if (
                DB::table($table)
                    ->where('semester_id', $semester->id)
                    ->exists()
            ) {
                return true;
            }
        }

        return false;
    }


}
