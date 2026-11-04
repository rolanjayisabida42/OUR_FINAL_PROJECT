<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Student;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    //
     // ==========================================
    // DISPLAY STUDENTS IN CLASSROOM
    // ==========================================
    public function index(Request $request, Section $section)
    {
        $section = $this->authorizedSection($request, $section);

        $cutoff = $this->firstFridayOfJune($section);
        $isCurrent = $this->isCurrent($section);

        $students = $section->students()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('students.lrn', 'like', "%{$search}%")
                        ->orWhere('students.first_name', 'like', "%{$search}%")
                        ->orWhere('students.middle_name', 'like', "%{$search}%")
                        ->orWhere('students.last_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('students.last_name')
            ->orderBy('students.first_name')
            ->paginate(15)
            ->withQueryString();

        foreach ($students as $student) {
            $student->age_on_cutoff = $this->ageAtCutoff(
                $student,
                $cutoff
            );
        }

        $stats = [
            'total' => $section->students()->count(),

            'male' => $section->students()
                ->where('students.sex', 'M')
                ->count(),

            'female' => $section->students()
                ->where('students.sex', 'F')
                ->count(),
        ];

        return view('teacher.students.index', compact(
            'section',
            'students',
            'stats',
            'cutoff',
            'isCurrent'
        ));
    }


      // ==========================================
    // SHOW ADD STUDENT FORM
    // ==========================================
    public function create(Request $request, Section $section)
    {
        $section = $this->authorizedSection($request, $section, true);

        $cutoff = $this->firstFridayOfJune($section);
        $student = null;

        return view('teacher.students.create', compact(
            'section',
            'cutoff',
            'student'
        ));
    }


     // ==========================================
    // SAVE STUDENT
    // ==========================================
    public function store(Request $request, Section $section)
    {
        $section = $this->authorizedSection($request, $section, true);

        $data = $this->validatedStudent($request);

        DB::transaction(function () use ($section, $data) {

            // REUSE AN EXISTING LRN FROM ANOTHER SCHOOL YEAR
            $student = Student::where('lrn', $data['lrn'])
                ->lockForUpdate()
                ->first();

            if ($student) {

                $sameIdentity =
                    Str::lower(trim($student->first_name)) ===
                        Str::lower(trim($data['first_name'])) &&

                    Str::lower(trim($student->last_name)) ===
                        Str::lower(trim($data['last_name'])) &&

                    $student->birth_date->format('Y-m-d') ===
                        $data['birth_date'] &&

                    $student->sex === $data['sex'];

                if (!$sameIdentity) {
                    throw ValidationException::withMessages([
                        'lrn' => 'This LRN already belongs to a student with different identity details.',
                    ]);
                }

            } else {

                $student = Student::create($data);
            }

            // CHECK IF ALREADY IN THIS SECTION
            if (
                $student->sections()
                    ->where('sections.id', $section->id)
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'lrn' => 'This student is already in this classroom.',
                ]);
            }

            // PREVENT TWO SECTIONS WITHIN THE SAME SCHOOL YEAR
            if (
                $student->sections()
                    ->where(
                        'sections.school_year_id',
                        $section->school_year_id
                    )
                    ->where('sections.id', '!=', $section->id)
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'lrn' => 'This student is already assigned to another section in this school year. Contact the administrator for a transfer.',
                ]);
            }

            $section->students()->attach($student->id);
        });

        return redirect()
            ->route('teacher.students.index', $section)
            ->with('success', 'Student added successfully.');
    }



      // ==========================================
    // VIEW STUDENT PROFILE
    // ==========================================
    public function show(
        Request $request,
        Section $section,
        Student $student
    ) 
    {
        $section = $this->authorizedSection($request, $section);

        $this->assertStudentInSection($section, $student);

        $cutoff = $this->firstFridayOfJune($section);

        $ageOnCutoff = $this->ageAtCutoff(
            $student,
            $cutoff
        );

        $isCurrent = $this->isCurrent($section);

        return view('teacher.students.show', compact(
            'section',
            'student',
            'cutoff',
            'ageOnCutoff',
            'isCurrent'
        ));
    }



     // ==========================================
    // SHOW EDIT FORM
    // ==========================================
    public function edit(
        Request $request,
        Section $section,
        Student $student
    ) 
    {
        $section = $this->authorizedSection($request, $section, true);

        $this->assertStudentInSection($section, $student);

        $cutoff = $this->firstFridayOfJune($section);

        return view('teacher.students.edit', compact(
            'section',
            'student',
            'cutoff'
        ));
    }



// ==========================================
    // UPDATE STUDENT
    // ==========================================
    public function update(
        Request $request,
        Section $section,
        Student $student
    ) 
    {
        $section = $this->authorizedSection($request, $section, true);

        $this->assertStudentInSection($section, $student);

        $data = $this->validatedStudent($request);

        if (
            Student::where('lrn', $data['lrn'])
                ->where('id', '!=', $student->id)
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'lrn' => 'This LRN is already used by another student.',
            ]);
        }

        $student->update($data);

        return redirect()
            ->route('teacher.students.show', [$section, $student])
            ->with('success', 'Student information updated successfully.');
    }


       // ==========================================
    // REMOVE STUDENT FROM CLASSROOM
    // ==========================================
    public function destroy(
        Request $request,
        Section $section,
        Student $student
    ) 
    {
        $section = $this->authorizedSection($request, $section, true);

        $this->assertStudentInSection($section, $student);

        // PRESERVE THE STUDENT PROFILE
        // ONLY REMOVE THE CLASSROOM ASSOCIATION
        $section->students()->detach($student->id);

        return redirect()
            ->route('teacher.students.index', $section)
            ->with('success', 'Student removed from this classroom.');
    }


    // ==========================================
    // TEACHER CLASSROOM AUTHORIZATION
    // ==========================================
    private function authorizedSection(
        Request $request,
        Section $section,
        bool $mustBeCurrent = false
    ): Section 
    {

        $teacher = $request->user();

        $grades = match ((string) $teacher->grade_access) {
            '11' => ['11'],
            '12' => ['12'],
            'both' => ['11', '12'],
            default => [],
        };

        $section = Section::query()
            ->with(['schoolYear', 'strand'])
            ->whereKey($section->id)
            ->whereIn('grade_level', $grades)
            ->whereHas('adviser', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->firstOrFail();

        abort_if(
            $mustBeCurrent && !$this->isCurrent($section),
            403,
            'This classroom is inactive. Student changes are not allowed.'
        );

        return $section;
    }


    // ==========================================
    // CHECK ACTIVE SCHOOL YEAR / SECTION
    // ==========================================
    private function isCurrent(Section $section): bool
    {
        return (bool) $section->is_active &&
            (bool) $section->schoolYear?->is_active &&
            (bool) $section->strand?->is_active;
    }


    // ==========================================
    // CHECK STUDENT BELONGS TO SECTION
    // ==========================================
    private function assertStudentInSection(
        Section $section,
        Student $student
    ): void 
    {

        abort_unless(
            $section->students()
                ->where('students.id', $student->id)
                ->exists(),
            404
        );
    }


    // ==========================================
    // FIRST FRIDAY OF JUNE
    // ==========================================
    private function firstFridayOfJune(
        Section $section
    ): CarbonImmutable 
    {

        // EXAMPLE: 2026-2027 => JUNE 2026
        $schoolYear = (string) $section->schoolYear?->name;

        $year = preg_match(
            '/^(\d{4})-\d{4}$/',
            $schoolYear,
            $matches
        )
            ? (int) $matches[1]
            : (int) CarbonImmutable::parse(
                $section->schoolYear->start_date
            )->year;

        $juneFirst = CarbonImmutable::parse(
            sprintf('%04d-06-01', $year)
        );

        return $juneFirst->addDays(
            (5 - $juneFirst->dayOfWeek + 7) % 7
        );
    }


    // ==========================================
    // CALCULATE AGE ON CUTOFF DATE
    // ==========================================
    private function ageAtCutoff(
        Student $student,
        CarbonImmutable $cutoff
    ): ?int
    {

        $birthDate = CarbonImmutable::parse(
            $student->birth_date
        );

        if ($birthDate->greaterThan($cutoff)) {
            return null;
        }

        return (int) floor(
            $birthDate->diffInYears($cutoff)
        );
    }


    // ==========================================
    // STUDENT FORM VALIDATION
    // ==========================================
    private function validatedStudent(Request $request): array
    {
        return $request->validate([

            // BASIC INFORMATION
            'lrn' => [
                'required',
                'string',
                'regex:/^\d{12}$/',
            ],

            'last_name' => ['required', 'string', 'max:120'],
            'first_name' => ['required', 'string', 'max:120'],
            'middle_name' => ['nullable', 'string', 'max:120'],
            'sex' => ['required', 'in:M,F'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],

            // ADDITIONAL INFORMATION
            'birth_place_province' => ['nullable', 'string', 'max:150'],
            'mother_tongue' => ['nullable', 'string', 'max:120'],
            'ip_ethnic_group' => ['nullable', 'string', 'max:150'],
            'religion' => ['nullable', 'string', 'max:150'],

            // ADDRESS
            'house_street_sitio_purok' => [
                'nullable',
                'string',
                'max:255',
            ],
            'barangay' => ['nullable', 'string', 'max:150'],
            'municipality_city' => ['nullable', 'string', 'max:150'],
            'province' => ['nullable', 'string', 'max:150'],

            // PARENTS
            'father_first_name' => ['nullable', 'string', 'max:120'],
            'father_last_name' => ['nullable', 'string', 'max:120'],
            'mother_maiden_first_name' => ['nullable', 'string', 'max:120'],
            'mother_maiden_middle_name' => ['nullable', 'string', 'max:120'],
            'mother_maiden_last_name' => ['nullable', 'string', 'max:120'],

            // GUARDIAN
            'guardian_name' => ['nullable', 'string', 'max:200'],
            'guardian_relationship' => ['nullable', 'string', 'max:120'],
            'contact_number' => ['nullable', 'string', 'max:30'],

        ], [
            'lrn.regex' => 'LRN must contain exactly 12 digits.',
        ]);
    }


    
// ==========================================
// FIND EXISTING STUDENT BY LRN
// ==========================================
public function lookup(Request $request, Section $section)
{
    // TEACHER MUST OWN AN ACTIVE CLASSROOM
    $section = $this->authorizedSection(
        $request,
        $section,
        true
    );

    $data = $request->validate([
        'lrn' => [
            'required',
            'string',
            'regex:/^\d{12}$/',
        ],
    ]);

    $student = Student::where('lrn', $data['lrn'])->first();

    // NO EXISTING RECORD
    if (!$student) {
        return response()->json([
            'status' => 'not_found',
            'message' => 'No existing student found. Please complete the registration form.',
        ]);
    }

    // ALREADY ASSIGNED TO THIS CLASSROOM
    if (
        $student->sections()
            ->where('sections.id', $section->id)
            ->exists()
    ) {
        return response()->json([
            'status' => 'already_assigned',
            'message' => 'This student is already registered in this classroom.',
        ]);
    }

    // ALREADY ASSIGNED TO ANOTHER SECTION
    // IN THE SAME SCHOOL YEAR
    if (
        $student->sections()
            ->where(
                'sections.school_year_id',
                $section->school_year_id
            )
            ->where('sections.id', '!=', $section->id)
            ->exists()
    ) {
        return response()->json([
            'status' => 'other_section',
            'message' => 'This student is assigned to another section in the same school year. Please contact the administrator for a transfer.',
        ]);
    }

    // STUDENT FOUND AND ELIGIBLE
    return response()->json([
        'status' => 'found',

        'student' => [
            'name' => trim(
                $student->last_name . ', ' .
                $student->first_name . ' ' .
                ($student->middle_name ?? '')
            ),

            'sex' => $student->sex,
        ],

        'message' => 'Existing student found. Verify the identity before adding to this classroom.',
    ]);
}


// ==========================================
// ATTACH EXISTING STUDENT TO CLASSROOM
// ==========================================
public function attachExisting(
    Request $request,
    Section $section
)
 {
    $section = $this->authorizedSection(
        $request,
        $section,
        true
    );

    $data = $request->validate([
        'lrn' => [
            'required',
            'string',
            'regex:/^\d{12}$/',
        ],
    ]);

    DB::transaction(function () use ($section, $data) {

        // LOCK STUDENT DURING THE ASSIGNMENT
        $student = Student::where('lrn', $data['lrn'])
            ->lockForUpdate()
            ->first();

        if (!$student) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'lrn' => 'Student not found. Register the student first.',
            ]);
        }

        // PREVENT DUPLICATE CLASSROOM ASSIGNMENT
        if (
            $student->sections()
                ->where('sections.id', $section->id)
                ->exists()
        ) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'lrn' => 'This student is already in this classroom.',
            ]);
        }

        // PREVENT MULTIPLE SECTIONS IN SAME SCHOOL YEAR
        if (
            $student->sections()
                ->where(
                    'sections.school_year_id',
                    $section->school_year_id
                )
                ->where('sections.id', '!=', $section->id)
                ->exists()
        ) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'lrn' => 'Student is already assigned to another section in this school year.',
            ]);
        }

        // REUSE EXISTING STUDENT PROFILE
        // NO NEW STUDENT RECORD IS CREATED
        $section->students()->attach($student->id);

    });

    return redirect()
        ->route('teacher.students.index', $section)
        ->with(
            'success',
            'Existing student added to this classroom successfully.'
        );
}




}
