<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Strand;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    //
     // DISPLAY SECTIONS AND STRANDS
    public function index(Request $request)
    {
        $schoolYears = SchoolYear::orderByDesc('start_date')->get();

        $strands = Strand::withCount('sections')
            ->orderBy('code')
            ->get();

        $sections = Section::with(['schoolYear', 'strand'])
            ->when($request->filled('school_year_id'), function ($query) use ($request) {
                $query->where('school_year_id', $request->input('school_year_id'));
            })
            ->when(
                in_array($request->input('grade_level'), ['11', '12'], true),
                fn ($query) => $query->where(
                    'grade_level',
                    $request->input('grade_level')
                )
            )
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . trim($request->input('search')) . '%');
            })
            ->orderByDesc('school_year_id')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => Section::count(),
            'grade11' => Section::where('grade_level', '11')->count(),
            'grade12' => Section::where('grade_level', '12')->count(),
            'active' => Section::where('is_active', true)->count(),
        ];

        return view('admin.sections.index', compact(
            'schoolYears',
            'strands',
            'sections',
            'stats'
        ));
    }


     // CREATE SECTION
    public function store(Request $request)
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
        ]);

        $validated = $request->validateWithBag('sectionCreate', [
            'school_year_id' => [
                'required',
                'integer',
                'exists:school_years,id',
            ],

            'strand_id' => [
                'required',
                'integer',
                Rule::exists('strands', 'id')
                    ->where('is_active', true),
            ],

            'grade_level' => [
                'required',
                Rule::in(['11', '12']),
            ],

            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                Rule::unique('sections', 'name')
                    ->where('school_year_id', $request->input('school_year_id'))
                    ->where('grade_level', $request->input('grade_level')),
            ],
        ]);

        Section::create([
            ...$validated,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.sections.index')
            ->with('success', 'Section created successfully!');
    }



      // UPDATE SECTION
    public function update(Request $request, Section $section)
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
        ]);

        $validated = $request->validateWithBag('sectionUpdate', [
            'school_year_id' => [
                'required',
                'integer',
                'exists:school_years,id',
            ],

            'strand_id' => [
                'required',
                'integer',
                Rule::exists('strands', 'id')
                    ->where('is_active', true),
            ],

            'grade_level' => [
                'required',
                Rule::in(['11', '12']),
            ],

            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                Rule::unique('sections', 'name')
                    ->where('school_year_id', $request->input('school_year_id'))
                    ->where('grade_level', $request->input('grade_level'))
                    ->ignore($section->id),
            ],
        ]);

        // FUTURE SAFEGUARD:
        // Once enrollments and teacher assignments exist,
        // prevent changing linked section identity fields.

        $section->update($validated);

        return redirect()
            ->route('admin.sections.index')
            ->with('success', 'Section updated successfully!');
    }




       // ACTIVATE / DEACTIVATE SECTION
    public function toggleStatus(Section $section)
    {
        if (!$section->is_active && !$section->strand->is_active) {
            return back()->with(
                'error',
                'Activate the strand before reactivating this section.'
            );
        }

        $section->is_active = !$section->is_active;
        $section->save();

        return back()->with(
            'success',
            $section->is_active
                ? 'Section activated successfully!'
                : 'Section deactivated successfully!'
        );
    }



}
