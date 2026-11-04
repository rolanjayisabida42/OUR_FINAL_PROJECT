
@extends('layouts.principal-review')

@section('title', 'Review Submissions | SHS Forms')
@section('heading', 'Review Submissions')

@section('content')

<div class="space-y-7">

    <!-- PAGE HEADER -->
    <div class="flex flex-wrap items-center justify-between gap-4">

        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-teal-700">
                Submission Management
            </p>

            <h1 class="mt-2 text-2xl font-extrabold text-slate-900">
                Review Submissions
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Review uploaded requirements from teachers,
                approve completed submissions, or return
                documents for correction.
            </p>
        </div>

        <a href="{{ route('principal.submissions.index') }}"
           class="rounded-xl border border-slate-200
                  bg-white px-5 py-3 text-sm font-bold
                  text-slate-700 hover:bg-slate-50">
            Submission Settings
        </a>

    </div>


    <!-- STATUS SUMMARY -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">

        @foreach([
            ['Submitted', 'submitted'],
            ['Approved', 'approved'],
            ['Returned', 'returned'],
            ['Draft', 'draft']
        ] as $item)

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <p class="text-xs font-semibold text-slate-500">
                    {{ $item[0] }}
                </p>

                <p class="mt-3 text-3xl font-extrabold text-[#142b61]">
                    {{ $counts->get($item[1], 0) }}
                </p>

            </div>

        @endforeach

    </div>


    <!-- FILTER FORM -->
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-bold text-slate-900">
            Filter Submissions
        </h2>

        <form method="GET"
              action="{{ route('principal.submissions.review.index') }}"
              class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">

            <!-- SCHOOL YEAR -->
            <div>

                <label class="mb-2 block text-xs font-bold text-slate-600">
                    School Year
                </label>

                <select name="year"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">

                    <option value="">All School Years</option>

                    @foreach($years as $schoolYear)

                        <option value="{{ $schoolYear->id }}"
                            @selected((string) $year === (string) $schoolYear->id)>

                            {{ $schoolYear->name }}

                        </option>

                    @endforeach

                </select>

            </div>


            <!-- SEMESTER -->
            <div>

                <label class="mb-2 block text-xs font-bold text-slate-600">
                    Semester
                </label>

                <select name="semester"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">

                    <option value="">All Semesters</option>

                    <option value="1" @selected($semester === 1)>
                        1st Semester
                    </option>

                    <option value="2" @selected($semester === 2)>
                        2nd Semester
                    </option>

                </select>

            </div>


            <!-- STATUS -->
            <div>

                <label class="mb-2 block text-xs font-bold text-slate-600">
                    Status
                </label>

                <select name="status"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">

                    <option value="">All Statuses</option>

                    @foreach([
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'returned' => 'Returned',
                        'draft' => 'Draft'
                    ] as $value => $label)

                        <option value="{{ $value }}"
                            @selected($status === $value)>

                            {{ $label }}

                        </option>

                    @endforeach

                </select>

            </div>


            <!-- GRADE LEVEL -->
            <div>

                <label class="mb-2 block text-xs font-bold text-slate-600">
                    Grade Level
                </label>

                <select name="grade"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">

                    <option value="">All Grades</option>

                    <option value="11" @selected($grade === '11')>
                        Grade 11
                    </option>

                    <option value="12" @selected($grade === '12')>
                        Grade 12
                    </option>

                </select>

            </div>


            <!-- SEARCH -->
            <div class="md:col-span-2 xl:col-span-4">

                <label class="mb-2 block text-xs font-bold text-slate-600">
                    Search Classroom or Teacher
                </label>

                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Enter classroom or teacher name..."
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">

            </div>


            <div class="flex flex-wrap gap-3 md:col-span-2 xl:col-span-4">

                <button type="submit"
                        class="rounded-xl bg-[#0d9488] px-6 py-3 text-sm font-bold text-white hover:bg-teal-700">
                    Apply Filters
                </button>

                <a href="{{ route('principal.submissions.review.index') }}"
                   class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-600">
                    Reset
                </a>

            </div>

        </form>

    </section>


    <!-- SUBMISSION LIST -->
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 p-6">

            <div>

                <h2 class="text-lg font-extrabold text-slate-900">
                    Teacher Submissions
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    {{ $submissions->total() }} submission record(s)
                </p>

            </div>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full min-w-[850px] text-left">

                <thead class="bg-slate-50 text-xs uppercase text-slate-500">

                    <tr>
                        <th class="px-5 py-4">Classroom</th>
                        <th class="px-5 py-4">Teacher</th>
                        <th class="px-5 py-4">Semester</th>
                        <th class="px-5 py-4">Files</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Submitted</th>
                        <th class="px-5 py-4 text-right">Action</th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($submissions as $submission)

                        @php
                            $statusClass = match($submission->status) {
                                'submitted' => 'bg-blue-50 text-blue-700',
                                'approved' => 'bg-emerald-50 text-emerald-700',
                                'returned' => 'bg-amber-50 text-amber-700',
                                default => 'bg-slate-100 text-slate-600',
                            };
                        @endphp

                        <tr class="hover:bg-slate-50">

                            <!-- CLASSROOM -->
                            <td class="px-5 py-5">

                                <p class="text-sm font-bold text-slate-800">
                                    {{ $submission->section?->name ?? 'N/A' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Grade {{ $submission->section?->grade_level ?? 'N/A' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $submission->section?->schoolYear?->name ?? 'N/A' }}
                                </p>

                            </td>


                            <!-- TEACHER -->
                            <td class="px-5 py-5 text-sm text-slate-700">
                                {{ $submission->teacher?->name ?? 'N/A' }}
                            </td>


                            <!-- SEMESTER -->
                            <td class="px-5 py-5 text-sm text-slate-700">

                                {{ $submission->semester?->semester_number == 1
                                    ? '1st Semester'
                                    : '2nd Semester' }}

                            </td>


                            <!-- FILES -->
                            <td class="px-5 py-5 text-sm font-bold text-slate-700">
                                {{ $submission->files_count }}
                            </td>


                            <!-- STATUS -->
                            <td class="px-5 py-5">

                                <span class="rounded-full px-3 py-1.5 text-xs font-bold {{ $statusClass }}">

                                    {{ ucfirst($submission->status) }}

                                </span>

                            </td>


                            <!-- SUBMISSION DATE -->
                            <td class="px-5 py-5 text-xs text-slate-500">

                                {{ $submission->submitted_at
                                    ? $submission->submitted_at->copy()->timezone('Asia/Manila')->format('M d, Y h:i A')
                                    : 'Not Submitted' }}

                            </td>


                            <!-- REVIEW BUTTON -->
                            <td class="px-5 py-5 text-right">

                                <a href="{{ route('principal.submissions.review.show', $submission) }}"
                                   class="inline-flex rounded-xl bg-[#142b61] px-4 py-2.5 text-xs font-bold text-white hover:bg-blue-900">

                                    {{ $submission->status === 'submitted'
                                        ? 'Review'
                                        : 'View' }}

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="px-6 py-14 text-center">

                                <p class="text-base font-bold text-slate-700">
                                    No Submissions Found
                                </p>

                                <p class="mt-2 text-sm text-slate-500">
                                    Teacher submissions will appear here after they upload documents.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($submissions->hasPages())

            <div class="border-t border-slate-100 p-5">
                {{ $submissions->links() }}
            </div>

        @endif

    </section>

</div>

@endsection
