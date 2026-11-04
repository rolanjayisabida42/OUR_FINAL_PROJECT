
@extends('layouts.teacher')

@section('title', 'Student Information')
@section('page_heading', 'Student Information')

@section('content')

<div class="space-y-6">

    <!-- BACK -->
    <a href="{{ route('teacher.classrooms.show', $section) }}"
       class="inline-flex items-center gap-2 text-sm
              font-semibold text-slate-500 hover:text-teal-700">

        <i data-lucide="arrow-left" class="h-4 w-4"></i>
        Back to Classroom
    </a>

    <!-- TITLE -->
    <div class="flex flex-col justify-between gap-4
                sm:flex-row sm:items-center">

        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">
                Student Information
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                {{ $section->name }}
                · Grade {{ $section->grade_level }}
                · {{ $section->strand?->code }}
                · SY {{ $section->schoolYear?->name }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Age cutoff: {{ $cutoff->format('F j, Y') }}
            </p>
        </div>

        @if($isCurrent)

            <a href="{{ route('teacher.students.create', $section) }}"
               class="inline-flex items-center justify-center
                      gap-2 rounded-xl bg-teal-600
                      px-5 py-3 text-sm font-semibold
                      text-white hover:bg-teal-700">

                <i data-lucide="user-round-plus"
                   class="h-5 w-5"></i>

                Add Student
            </a>

        @else

            <span class="rounded-xl bg-amber-50
                         px-4 py-3 text-sm text-amber-700">
                Archived — Read Only
            </span>

        @endif
    </div>


    <!-- SUCCESS MESSAGE -->
    @if(session('success'))

        <div class="rounded-xl border border-emerald-200
                    bg-emerald-50 p-4 text-sm
                    font-semibold text-emerald-800">

            {{ session('success') }}
        </div>

    @endif


    <!-- STATISTICS -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">

        @foreach([
            [
                'label' => 'Total Students',
                'value' => $stats['total'],
                'icon' => 'users',
                'color' => 'bg-blue-50 text-blue-600'
            ],
            [
                'label' => 'Male',
                'value' => $stats['male'],
                'icon' => 'user-round',
                'color' => 'bg-indigo-50 text-indigo-600'
            ],
            [
                'label' => 'Female',
                'value' => $stats['female'],
                'icon' => 'user-round',
                'color' => 'bg-rose-50 text-rose-600'
            ]
        ] as $stat)

            <div class="rounded-2xl border border-slate-200
                        bg-white p-6 shadow-sm">

                <div class="flex items-center justify-between">

                    <div class="rounded-xl p-3 {{ $stat['color'] }}">
                        <i data-lucide="{{ $stat['icon'] }}"
                           class="h-6 w-6"></i>
                    </div>

                    <h2 class="text-3xl font-extrabold">
                        {{ $stat['value'] }}
                    </h2>
                </div>

                <p class="mt-4 text-sm text-slate-500">
                    {{ $stat['label'] }}
                </p>
            </div>

        @endforeach
    </div>


    <!-- STUDENT TABLE -->
    <div class="overflow-hidden rounded-2xl
                border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col gap-4 border-b
                    border-slate-100 p-6
                    sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h3 class="text-lg font-bold">
                    Student List
                </h3>

                <p class="mt-1 text-xs text-slate-400">
                    {{ $students->total() }} results
                </p>
            </div>

            <!-- SEARCH -->
            <form method="GET"
                  action="{{ route('teacher.students.index', $section) }}"
                  class="flex flex-wrap gap-2">

                <input type="search"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search LRN or name..."
                       class="min-w-48 rounded-xl
                              border border-slate-200
                              px-4 py-3 text-sm outline-none
                              focus:border-teal-500">

                <button type="submit"
                        class="inline-flex items-center gap-2
                               rounded-xl bg-[#142b61]
                               px-5 py-3 text-sm font-semibold
                               text-white hover:bg-blue-900">

                    <i data-lucide="search" class="h-4 w-4"></i>
                    Search
                </button>

                <a href="{{ route('teacher.students.index', $section) }}"
                   class="rounded-xl border border-slate-200
                          px-4 py-3 text-sm font-semibold
                          text-slate-600 hover:bg-slate-50">

                    Reset
                </a>
            </form>
        </div>


        <div class="overflow-x-auto">

            <table class="w-full min-w-180 text-left text-sm">

                <thead class="bg-slate-50 text-xs
                              uppercase text-slate-500">

                    <tr>
                        <th class="px-6 py-4">LRN</th>
                        <th class="px-6 py-4">Student Name</th>
                        <th class="px-6 py-4">Sex</th>
                        <th class="px-6 py-4">Age</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($students as $student)

                        <tr class="hover:bg-slate-50">

                            <td class="px-6 py-4 font-mono
                                       font-semibold">
                                {{ $student->lrn }}
                            </td>

                            <td class="px-6 py-4 font-semibold">
                                {{ $student->last_name }},
                                {{ $student->first_name }}
                                {{ $student->middle_name }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $student->sex }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $student->age_on_cutoff ?? '—' }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">

                                <a href="{{ route('teacher.students.show', [$section, $student]) }}"
                                   class="font-semibold text-teal-700
                                          hover:underline">
                                    View
                                </a>

                                @if($isCurrent)

                                    <a href="{{ route('teacher.students.edit', [$section, $student]) }}"
                                       class="ml-4 font-semibold
                                              text-blue-700 hover:underline">
                                        Edit
                                    </a>

                                @endif
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5"
                                class="px-6 py-16 text-center">

                                <i data-lucide="users"
                                   class="mx-auto h-10 w-10
                                          text-slate-300"></i>

                                <p class="mt-4 font-semibold
                                          text-slate-600">
                                    No Students Found
                                </p>

                                <p class="mt-2 text-sm text-slate-400">
                                    Add a student or change your search.
                                </p>
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-5">

            <p class="mb-3 text-xs text-slate-400">
                Age calculated as of
                {{ $cutoff->format('F j, Y') }}.
            </p>

            {{ $students->links() }}
        </div>
    </div>

</div>

@endsection
