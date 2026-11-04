<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolYear;
use App\Models\Section;


class ClassroomController extends Controller
{
    //
     // ==========================================
    // DISPLAY MY ASSIGNED CLASSROOMS
    // ==========================================
    public function index(Request $request)
    {
        $teacher = $request->user();

        // ONLY THE LOGGED-IN TEACHER'S GRADE ACCESS
        $allowedGrades = $this->allowedGrades($teacher);

        $baseQuery = Section::query()
            ->whereHas('adviser', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->whereIn('grade_level', $allowedGrades);

        // CURRENT CLASSROOMS
        $currentQuery = (clone $baseQuery)
            ->where('is_active', true)
            ->whereHas('schoolYear', function ($query) {
                $query->where('is_active', true);
            })
            ->whereHas('strand', function ($query) {
                $query->where('is_active', true);
            });

        $total = (clone $baseQuery)->count();
        $current = (clone $currentQuery)->count();

        $stats = [
            'total' => $total,
            'current' => $current,
            'archived' => $total - $current,
        ];

        // DEFAULT: ALL ASSIGNED CLASSROOMS
        $view = $request->input('view', 'all');

        if (!in_array($view, ['all', 'current', 'archived'], true)) {
            $view = 'all';
        }

        $sections = (clone $baseQuery)
            ->with(['schoolYear', 'strand'])

            // SEARCH CLASSROOM OR STRAND
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereHas('strand', function ($strand) use ($search) {
                          $strand->where('code', 'like', "%{$search}%")
                                 ->orWhere('name', 'like', "%{$search}%");
                      });
                });
            })

            // GRADE FILTER
            ->when(
                in_array($request->input('grade_level'), ['11', '12'], true),
                fn ($query) => $query->where(
                    'grade_level',
                    $request->input('grade_level')
                )
            )

            // CURRENT CLASSROOMS
            ->when($view === 'current', function ($query) {
                $query->where('is_active', true)
                    ->whereHas('schoolYear', function ($q) {
                        $q->where('is_active', true);
                    })
                    ->whereHas('strand', function ($q) {
                        $q->where('is_active', true);
                    });
            })

            // ARCHIVED / INACTIVE CLASSROOMS
            ->when($view === 'archived', function ($query) {
                $query->where(function ($q) {
                    $q->where('is_active', false)
                      ->orWhereHas('schoolYear', function ($year) {
                          $year->where('is_active', false);
                      })
                      ->orWhereHas('strand', function ($strand) {
                          $strand->where('is_active', false);
                      });
                });
            })

            ->orderByDesc('school_year_id')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        $activeYear = SchoolYear::where('is_active', true)->first();

        return view('teacher.classrooms.index', compact(
            'sections',
            'stats',
            'activeYear',
            'view'
        ));
    }


     // ==========================================
    // OPEN A SPECIFIC CLASSROOM
    // ==========================================
    
    public function show(Request $request, Section $section)
    {
        $teacher = $request->user();

        // Load classroom relationships
        $section->load([
            'schoolYear',
            'strand',
            'adviser.teacher',
        ]);

        // Verify teacher's grade access
        $allowedGrades = match ((string) $teacher->grade_access) {
            '11' => ['11'],
            '12' => ['12'],
            'both' => ['11', '12'],
            default => [],
        };

        abort_unless(
            in_array((string) $section->grade_level, $allowedGrades, true),
            403
        );

        // Verify classroom belongs to the teacher
        abort_unless(
            $section->adviser()
                ->where('teacher_id', $teacher->id)
                ->exists(),
            403
        );

        // Check if classroom is active
        $isCurrent =
            (bool) $section->is_active &&
            (bool) $section->schoolYear?->is_active &&
            (bool) $section->strand?->is_active;

        // IMPORTANT: Display 6 classroom modules
        return view('teacher.classrooms.show', compact(
            'section',
            'isCurrent'
        ));
    }



    // ==========================================
    // CHECK TEACHER GRADE ACCESS
    // ==========================================
    private function allowedGrades($teacher): array
    {
        return match ((string) $teacher->grade_access) {
            '11' => ['11'],
            '12' => ['12'],
            'both' => ['11', '12'],
            default => [],
        };
    }



}
