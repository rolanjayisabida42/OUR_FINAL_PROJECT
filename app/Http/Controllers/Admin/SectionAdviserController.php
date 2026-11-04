<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\SectionAdviser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SectionAdviserController extends Controller
{
 

     // ==========================================
    // 1. DISPLAY SECTION ADVISERS
    // ==========================================
    public function index(Request $request)
    {
        $schoolYears = SchoolYear::orderByDesc('start_date')
            ->get();

        $activeYear = SchoolYear::where('is_active', true)
            ->first();

        $teachers = User::where('role', 'teacher')
            ->where('is_active', true)
            ->whereIn('grade_access', ['11', '12', 'both'])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'grade_access']);

        $availableSections = Section::with([
                'schoolYear',
                'strand'
            ])
            ->where('is_active', true)
            ->whereHas('schoolYear', function ($query) {
                $query->where('is_active', true);
            })
            ->whereHas('strand', function ($query) {
                $query->where('is_active', true);
            })
            ->whereDoesntHave('adviser')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $sections = Section::with([
                'schoolYear',
                'strand',
                'adviser.teacher'
            ])

            // SEARCH SECTION, STRAND OR TEACHER
            ->when($request->filled('search'), function ($query) use ($request) {

                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {

                    $q->where('name', 'like', "%{$search}%")

                      ->orWhereHas('strand', function ($strand) use ($search) {
                          $strand->where('code', 'like', "%{$search}%");
                      })

                      ->orWhereHas('adviser.teacher', function ($teacher) use ($search) {
                          $teacher->where('name', 'like', "%{$search}%");
                      });

                });
            })

            // SCHOOL YEAR FILTER
            ->when($request->filled('school_year_id'), function ($query) use ($request) {
                $query->where(
                    'school_year_id',
                    $request->input('school_year_id')
                );
            })

            // GRADE LEVEL FILTER
            ->when(
                in_array($request->input('grade_level'), ['11', '12'], true),
                fn ($query) => $query->where(
                    'grade_level',
                    $request->input('grade_level')
                )
            )

            // ASSIGNED FILTER
            ->when(
                $request->input('status') === 'assigned',
                fn ($query) => $query->whereHas('adviser')
            )

            // UNASSIGNED FILTER
            ->when(
                $request->input('status') === 'unassigned',
                fn ($query) => $query->whereDoesntHave('adviser')
            )

            ->orderByDesc('school_year_id')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => Section::count(),

            'assigned' => SectionAdviser::count(),

            'unassigned' => Section::whereDoesntHave('adviser')
                ->count(),

            'teachers' => $teachers->count(),
        ];

        return view('admin.advisers.index', compact(
            'schoolYears',
            'activeYear',
            'teachers',
            'availableSections',
            'sections',
            'stats'
        ));
    }




    // ==========================================
    // 2. ASSIGN SECTION ADVISER
    // ==========================================
    public function store(Request $request)
    {
        $validated = $request->validateWithBag(
            'assignmentCreate',
            [
                'teacher_id' => [
                    'required',
                    'integer',
                    Rule::exists('users', 'id')
                        ->where('role', 'teacher')
                        ->where('is_active', 1),
                ],

                'section_id' => [
                    'required',
                    'integer',
                    'exists:sections,id',
                ],
            ]
        );

        return DB::transaction(function () use ($validated) {

            // LOCK THE SECTION DURING ASSIGNMENT
            $section = Section::whereKey($validated['section_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $section->load(['schoolYear', 'strand']);

            // VALIDATE SECTION STATUS
            if (!$this->canManageSection($section)) {

                return back()->withErrors([
                    'section_id' =>
                        'Section must be active and belong to the active school year and an active strand.',
                ], 'assignmentCreate')->withInput();
            }

            // PREVENT DUPLICATE SECTION ADVISER
            if (
                SectionAdviser::where('section_id', $section->id)
                    ->exists()
            ) {

                return back()->withErrors([
                    'section_id' =>
                        'This section already has an assigned adviser.',
                ], 'assignmentCreate')->withInput();
            }

            // GET THE SELECTED TEACHER
            $teacher = User::whereKey($validated['teacher_id'])
                ->where('role', 'teacher')
                ->where('is_active', true)
                ->first();

            // VALIDATE TEACHER GRADE ACCESS
            if (!$teacher || !$this->hasGradeAccess($teacher, $section)) {

                return back()->withErrors([
                    'teacher_id' =>
                        'The selected teacher is inactive or does not have access to Grade '
                        . $section->grade_level . '.',
                ], 'assignmentCreate')->withInput();
            }

            // SAVE THE ASSIGNMENT
            SectionAdviser::create([
                'teacher_id' => $teacher->id,
                'section_id' => $section->id,
            ]);

            return redirect()
                ->route('admin.advisers.index')
                ->with(
                    'success',
                    'Section adviser assigned successfully!'
                );
        });
    }




    // ==========================================
    // 3. REASSIGN SECTION ADVISER
    // ==========================================
    public function update(
        Request $request,
        SectionAdviser $sectionAdviser
    ) 
    {
        $validated = $request->validateWithBag(
            'assignmentUpdate',
            [
                'teacher_id' => [
                    'required',
                    'integer',
                    Rule::exists('users', 'id')
                        ->where('role', 'teacher')
                        ->where('is_active', 1),
                ],
            ]
        );

        return DB::transaction(function () use (
            $sectionAdviser,
            $validated
        ) {

            $section = Section::whereKey($sectionAdviser->section_id)
                ->lockForUpdate()
                ->firstOrFail();

            $section->load(['schoolYear', 'strand']);

            if (!$this->canManageSection($section)) {

                return back()->withErrors([
                    'teacher_id' =>
                        'Only active sections in the active school year can be reassigned.',
                ], 'assignmentUpdate')->withInput();
            }

            $assignment = SectionAdviser::findOrFail(
                $sectionAdviser->id
            );

            $teacher = User::whereKey($validated['teacher_id'])
                ->where('role', 'teacher')
                ->where('is_active', true)
                ->first();

            if (!$teacher || !$this->hasGradeAccess($teacher, $section)) {

                return back()->withErrors([
                    'teacher_id' =>
                        'Selected teacher is inactive or does not have access to Grade '
                        . $section->grade_level . '.',
                ], 'assignmentUpdate')->withInput();
            }

            if ($assignment->teacher_id == $teacher->id) {

                return back()->with(
                    'error',
                    'This teacher is already assigned to this section.'
                );
            }

            // UPDATE THE ADVISER
            $assignment->update([
                'teacher_id' => $teacher->id,
            ]);

            return redirect()
                ->route('admin.advisers.index')
                ->with(
                    'success',
                    'Section adviser reassigned successfully!'
                );
        });
    }



  // ==========================================
    // 4. UNASSIGN SECTION ADVISER
    // ==========================================
    public function destroy(SectionAdviser $sectionAdviser)
    {
        DB::transaction(function () use ($sectionAdviser) {

            $section = Section::whereKey($sectionAdviser->section_id)
                ->lockForUpdate()
                ->firstOrFail();

            $section->load(['schoolYear', 'strand']);

            abort_unless($this->canManageSection($section), 403);

            // REMOVE CURRENT ASSIGNMENT
            SectionAdviser::whereKey($sectionAdviser->id)
                ->delete();
        });

        return redirect()
            ->route('admin.advisers.index')
            ->with(
                'success',
                'Section adviser unassigned successfully!'
            );
    }



   // ==========================================
    // 5. VALIDATE SECTION
    // ==========================================
    private function canManageSection(Section $section): bool
    {
        return (bool) $section->is_active
            && (bool) $section->schoolYear?->is_active
            && (bool) $section->strand?->is_active;
    }


  // ==========================================
    // 6. VALIDATE TEACHER GRADE ACCESS
    // ==========================================
    private function hasGradeAccess(
        User $teacher,
        Section $section
    ): bool {

        if (
            $teacher->role !== 'teacher' ||
            !$teacher->is_active
        ) {
            return false;
        }

        return in_array(
            $teacher->grade_access,
            ['both', (string) $section->grade_level],
            true
        );
    }
  




}
