<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Strand;
use Illuminate\Validation\Rule;

class StrandController extends Controller
{
    //
    // CREATE STRAND
    public function store(Request $request)
    {
        $request->merge([
            'code' => strtoupper(trim((string) $request->input('code'))),
        ]);

        $validated = $request->validateWithBag('strandCreate', [
            'code' => [
                'required',
                'regex:/^[A-Z0-9-]{2,20}$/',
                'unique:strands,code',
            ],
            'name' => ['required', 'string', 'max:255'],
        ]);

        Strand::create([
            ...$validated,
            'is_active' => true,
        ]);

        return back()->with('success', 'Strand created successfully!');
    }


     // UPDATE STRAND
    public function update(Request $request, Strand $strand)
    {
        $request->merge([
            'code' => strtoupper(trim((string) $request->input('code'))),
        ]);

        $validated = $request->validateWithBag('strandUpdate', [
            'code' => [
                'required',
                'regex:/^[A-Z0-9-]{2,20}$/',
                Rule::unique('strands', 'code')->ignore($strand->id),
            ],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $strand->update($validated);

        return back()->with('success', 'Strand updated successfully!');
    }


    // ACTIVATE / DEACTIVATE STRAND
    public function toggleStatus(Strand $strand)
    {
        if (
            $strand->is_active &&
            $strand->sections()->where('is_active', true)->exists()
        ) {
            return back()->with(
                'error',
                'Deactivate the sections under this strand first.'
            );
        }

        $strand->is_active = !$strand->is_active;
        $strand->save();

        return back()->with(
            'success',
            $strand->is_active
                ? 'Strand activated successfully!'
                : 'Strand deactivated successfully!'
        );
    }


}
