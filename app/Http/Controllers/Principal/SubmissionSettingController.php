<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\SubmissionSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SubmissionSettingController extends Controller
{
    //
    public function index(Request $request)
    {
        $years = SchoolYear::with([
            'semesters' => fn ($query) =>
                $query->orderBy('semester_number'),
        ])
        ->orderByDesc('id')
        ->get();

        $selectedYear =
            $years->firstWhere('id', $request->integer('year'))
            ?? $years->firstWhere('is_active', true)
            ?? $years->first();

        $semesterNumber = $request->integer('semester', 1);

        if (!in_array($semesterNumber, [1, 2], true)) {
            $semesterNumber = 1;
        }

        $selectedSemester = $selectedYear?->semesters
            ->firstWhere('semester_number', $semesterNumber);

        $setting = $selectedSemester
            ? SubmissionSetting::with('requirements')
                ->where('semester_id', $selectedSemester->id)
                ->first()
            : null;

        return view('principal.submissions.index', compact(
            'years',
            'selectedYear',
            'selectedSemester',
            'semesterNumber',
            'setting'
        ));
    }

     public function update(Request $request, Semester $semester)
    {

    
        // ==========================================
        // PROTECT REQUIREMENTS WITH EXISTING FILES
        // ==========================================

        $existingSetting = SubmissionSetting::with('requirements')
            ->where('semester_id', $semester->id)
            ->first();

        if (
            $existingSetting &&
            $existingSetting->requirements()
                ->whereHas('submissionFiles')
                ->exists()
        ) {

            $original = $existingSetting->requirements
                ->map(fn ($item) => [
                    'id' => (int) $item->id,
                    'name' => trim($item->name),
                    'description' => trim((string) $item->description),
                ])
                ->sortBy('id')
                ->values()
                ->all();

            $updated = collect($data['requirements'] ?? [])
                ->map(fn ($item) => [
                    'id' => (int) ($item['id'] ?? 0),
                    'name' => trim($item['name']),
                    'description' => trim(
                        (string) ($item['description'] ?? '')
                    ),
                ])
                ->sortBy('id')
                ->values()
                ->all();

            if (
                $original !== $updated ||
                $existingSetting->grade_scope !== $data['grade_scope']
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'requirements' =>
                        'Teachers have already uploaded documents for this semester. ' .
                        'The requirement list and grade coverage are locked to protect existing files.',
                ]);
            }
        }

        // Normalize document names before validation.
        $requirements = collect(
            $request->input('requirements', [])
        )->map(function ($row) {
            return [
                'id' => $row['id'] ?? null,
                'name' => is_string($row['name'] ?? null)
                    ? trim($row['name'])
                    : ($row['name'] ?? null),
                'description' => is_string($row['description'] ?? null)
                    ? trim($row['description'])
                    : ($row['description'] ?? null),
            ];
        })->values()->all();

        $request->merge(['requirements' => $requirements]);

        $data = $request->validate([
            'grade_scope' => [
                'required',
                Rule::in(['11', '12', 'both']),
            ],

            'opening_local' => [
                'nullable',
                'date_format:Y-m-d\TH:i',
            ],

            'deadline_local' => [
                'nullable',
                'date_format:Y-m-d\TH:i',
            ],

            'status' => [
                'required',
                Rule::in(['draft', 'active', 'closed']),
            ],

            'instructions' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'requirements' => [
                'array',
                'max:20',
            ],

            'requirements.*' => [
                'required',
                'array:id,name,description',
            ],

            'requirements.*.id' => [
                'nullable',
                'integer',
                'distinct',
            ],

            'requirements.*.name' => [
                'required',
                'string',
                'min:2',
                'max:120',
                'distinct:ignore_case',
            ],

            'requirements.*.description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        // Interpret browser datetime-local inputs
        // in Philippine time, then store UTC.
        $opening = !empty($data['opening_local'])
            ? Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $data['opening_local'],
                'Asia/Manila'
            )->startOfMinute()->utc()
            : null;

        $deadline = !empty($data['deadline_local'])
            ? Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $data['deadline_local'],
                'Asia/Manila'
            )->startOfMinute()->utc()
            : null;

        if ($opening && $deadline && $deadline->lte($opening)) {
            throw ValidationException::withMessages([
                'deadline_local' =>
                    'The deadline must be later than the opening date.',
            ]);
        }

        if ($data['status'] === 'active') {
            if (!$opening || !$deadline) {
                throw ValidationException::withMessages([
                    'opening_local' =>
                        'Opening date and deadline are required to activate submissions.',
                ]);
            }

            if ($deadline->lte(now('UTC'))) {
                throw ValidationException::withMessages([
                    'deadline_local' =>
                        'The deadline must be in the future.',
                ]);
            }

            if (count($data['requirements'] ?? []) === 0) {
                throw ValidationException::withMessages([
                    'requirements' =>
                        'Add at least one requirement before activation.',
                ]);
            }
        }

        DB::transaction(function () use (
            $semester,
            $data,
            $opening,
            $deadline
        ) {

            $setting = SubmissionSetting::updateOrCreate(
                [
                    'semester_id' => $semester->id,
                ],
                [
                    'grade_scope' => $data['grade_scope'],
                    'opens_at' => $opening,
                    'deadline_at' => $deadline,
                    'status' => $data['status'],
                    'instructions' => $data['instructions'] ?? null,
                    'configured_by' => auth()->id(),
                ]
            );

            $keepIds = [];

            foreach (($data['requirements'] ?? []) as $position => $item) {

                $attributes = [
                    'name' => $item['name'],
                    'description' => $item['description'] ?: null,
                    'position' => $position,
                ];

                if (!empty($item['id'])) {

                    // Existing requirement must belong to
                    // this exact semester configuration.
                    $requirement = $setting->requirements()
                        ->whereKey($item['id'])
                        ->first();

                    if (!$requirement) {
                        throw ValidationException::withMessages([
                            'requirements' =>
                                'An invalid requirement was submitted. Refresh and try again.',
                        ]);
                    }

                    $requirement->update($attributes);

                } else {

                    $requirement = $setting->requirements()
                        ->create($attributes);
                }

                $keepIds[] = $requirement->id;
            }

            // Delete requirements removed by Principal.
            if (empty($keepIds)) {
                $setting->requirements()->delete();
            } else {
                $setting->requirements()
                    ->whereNotIn('id', $keepIds)
                    ->delete();
            }
        });

        $message = match ($data['status']) {
            'active' => 'Semester submission configuration activated successfully.',
            'closed' => 'Semester submission period closed successfully.',
            default => 'Semester submission draft saved successfully.',
        };

        return redirect()
            ->route('principal.submissions.index', [
                'year' => $semester->school_year_id,
                'semester' => $semester->semester_number,
            ])
            ->with('success', $message);
    }

}
