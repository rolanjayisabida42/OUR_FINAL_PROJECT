
@extends('layouts.teacher')

@section('title', 'Edit Student')
@section('page_heading', 'Edit Student')

@section('content')

<div class="mx-auto max-w-5xl space-y-6">

    <a href="{{ route('teacher.students.show', [$section, $student]) }}"
       class="inline-flex items-center gap-2
              text-sm font-semibold text-slate-500
              hover:text-teal-700">

        <i data-lucide="arrow-left" class="h-4 w-4"></i>
        Back to Student Profile
    </a>

    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">
            Edit Student Information
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            {{ $student->last_name }},
            {{ $student->first_name }}
            · {{ $section->name }}
        </p>
    </div>

    <div class="rounded-xl border border-amber-200
                bg-amber-50 p-4 text-sm text-amber-800">

        Changes to the student's profile also affect
        the same profile in other school years.
        Historical snapshots are not yet implemented.
    </div>

    @include('teacher.students._form', [
        'mode' => 'edit'
    ])

</div>

@endsection
