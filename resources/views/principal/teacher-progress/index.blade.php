
@extends('layouts.principal-review')

@section('title', 'Teacher Progress | SHS Forms')
@section('heading', 'Teacher Progress')

@section('content')

<div class="mx-auto max-w-7xl space-y-7 pb-12">

    <!-- ======================================
         PAGE HEADING
    ======================================= -->

    <div class="flex flex-wrap items-center
                justify-between gap-4">

        <div>

            <p class="text-xs font-bold uppercase
                      tracking-widest text-teal-700">

                Principal / Monitoring

            </p>

            <h1 class="mt-2 text-2xl font-extrabold
                       text-slate-900 md:text-3xl">

                Teacher Submission Progress

            </h1>

            <p class="mt-2 max-w-2xl text-sm
                      leading-6 text-slate-500">

                Monitor classroom submissions, approved
                requirements, missing documents, and
                correction progress for every Teacher.

            </p>

        </div>

        <a href="{{ route('principal.dashboard') }}"
           class="inline-flex items-center gap-2
                  rounded-xl border border-slate-200
                  bg-white px-5 py-3
                  text-sm font-bold text-slate-700
                  hover:bg-slate-50">

            <i data-lucide="arrow-left"
               class="h-4 w-4"></i>

            Dashboard

        </a>

    </div>


    <!-- ======================================
         SCHOOL YEAR BANNER
    ======================================= -->

    <section class="relative overflow-hidden
                    rounded-3xl bg-[#142b61]
                    p-7 text-white shadow-lg">

        <div class="pointer-events-none absolute
                    -right-16 -top-20 h-60 w-60
                    rounded-full border
                    border-white/10"></div>

        <div class="relative flex flex-wrap
                    items-center justify-between gap-5">

            <div class="flex items-center gap-4">

                <div class="grid h-14 w-14
                            place-items-center rounded-2xl
                            bg-white/10">

                    <i data-lucide="chart-no-axes-column"
                       class="h-7 w-7 text-teal-300"></i>

                </div>

                <div>

                    <p class="text-xs font-bold uppercase
                              tracking-widest text-blue-200">

                        Academic Monitoring

                    </p>

                    <h2 class="mt-2 text-xl
                               font-extrabold">

                        SY {{ $schoolYear?->name ?? 'Not Configured' }}

                    </h2>

                    <p class="mt-1 text-xs text-blue-100">

                        @if($semesterFilter === 'all')

                            Both Semesters

                        @elseif($semesterFilter === '1')

                            First Semester

                        @else

                            Second Semester

                        @endif

                    </p>

                </div>

            </div>

            <span class="rounded-xl border
                         border-white/20 bg-white/10
                         px-4 py-2 text-xs font-bold">

                <i data-lucide="database"
                   class="mr-1 inline-block h-4 w-4"></i>

                Database Connected

            </span>

        </div>

    </section>


    <!-- ======================================
         STATISTICS
    ======================================= -->

    @php

        $cards = [

            [
                'label' => 'Assigned Teachers',
                'value' => $stats['teachers'],
                'icon' => 'users',
                'color' => 'bg-blue-50 text-blue-700',
            ],

            [
                'label' => 'Classrooms',
                'value' => $stats['classrooms'],
                'icon' => 'school',
                'color' => 'bg-violet-50 text-violet-700',
            ],

            [
                'label' => 'Awaiting Review',
                'value' => $stats['submitted'],
                'icon' => 'clock-3',
                'color' => 'bg-amber-50 text-amber-700',
            ],

            [
                'label' => 'Approved Requirements',
                'value' => $stats['approved_requirements'],
                'icon' => 'check-circle-2',
                'color' => 'bg-emerald-50 text-emerald-700',
            ],

            [
                'label' => 'Missing Requirements',
                'value' => $stats['missing_requirements'],
                'icon' => 'file-warning',
                'color' => 'bg-red-50 text-red-700',
            ],

        ];

    @endphp

    <section class="grid gap-4 sm:grid-cols-2
                    lg:grid-cols-3 xl:grid-cols-5">

        @foreach($cards as $card)

            <div class="rounded-2xl border
                        border-slate-200 bg-white
                        p-5 shadow-sm">

                <div class="flex items-center
                            justify-between gap-3">

                    <div class="rounded-xl p-3
                                {{ $card['color'] }}">

                        <i data-lucide="{{ $card['icon'] }}"
                           class="h-5 w-5"></i>

                    </div>

                </div>

                <p class="mt-5 text-3xl
                          font-extrabold text-slate-900">

                    {{ number_format($card['value']) }}

                </p>

                <p class="mt-1 text-xs
                          font-medium text-slate-500">

                    {{ $card['label'] }}

                </p>

            </div>

        @endforeach

    </section>


    <!-- ======================================
         FILTER FORM
    ======================================= -->

    <section class="rounded-2xl border
                    border-slate-200 bg-white
                    p-6 shadow-sm">

        <div class="flex items-center gap-3">

            <div class="rounded-xl bg-teal-50
                        p-2.5 text-teal-700">

                <i data-lucide="sliders-horizontal"
                   class="h-5 w-5"></i>

            </div>

            <div>

                <h2 class="text-lg font-extrabold
                           text-slate-900">

                    Filter Teacher Progress

                </h2>

                <p class="mt-1 text-xs text-slate-500">

                    Select the academic period and
                    submission status you want to monitor.

                </p>

            </div>

        </div>


        <form method="GET"
              action="{{ route('principal.teacher-progress.index') }}"
              class="mt-6 grid gap-4
                     sm:grid-cols-2 xl:grid-cols-4">


            <!-- SCHOOL YEAR -->

            <div>

                <label for="year"
                       class="mb-2 block text-xs
                              font-bold text-slate-600">

                    School Year

                </label>

                <select id="year"
                        name="year"
                        class="w-full rounded-xl
                               border border-slate-200
                               bg-white px-4 py-3 text-sm">

                    @foreach($years as $year)

                        <option value="{{ $year->id }}"
                            @selected(
                                $schoolYear?->id === $year->id
                            )>

                            {{ $year->name }}

                        </option>

                    @endforeach

                </select>

            </div>


            <!-- SEMESTER -->

            <div>

                <label for="semester"
                       class="mb-2 block text-xs
                              font-bold text-slate-600">

                    Semester

                </label>

                <select id="semester"
                        name="semester"
                        class="w-full rounded-xl
                               border border-slate-200
                               bg-white px-4 py-3 text-sm">

                    <option value="all"
                        @selected($semesterFilter === 'all')>

                        Both Semesters

                    </option>

                    <option value="1"
                        @selected($semesterFilter === '1')>

                        1st Semester

                    </option>

                    <option value="2"
                        @selected($semesterFilter === '2')>

                        2nd Semester

                    </option>

                </select>

            </div>


            <!-- GRADE -->

            <div>

                <label for="grade"
                       class="mb-2 block text-xs
                              font-bold text-slate-600">

                    Grade Level

                </label>

                <select id="grade"
                        name="grade"
                        class="w-full rounded-xl
                               border border-slate-200
                               bg-white px-4 py-3 text-sm">

                    <option value=""
                        @selected($grade === '')>

                        All Grades

                    </option>

                    <option value="11"
                        @selected($grade === '11')>

                        Grade 11

                    </option>

                    <option value="12"
                        @selected($grade === '12')>

                        Grade 12

                    </option>

                </select>

            </div>


            <!-- STATUS -->

            <div>

                <label for="status"
                       class="mb-2 block text-xs
                              font-bold text-slate-600">

                    Submission Status

                </label>

                <select id="status"
                        name="status"
                        class="w-full rounded-xl
                               border border-slate-200
                               bg-white px-4 py-3 text-sm">

                    @foreach([
                        'all' => 'All Statuses',
                        'not_started' => 'Not Submitted',
                        'draft' => 'Draft',
                        'submitted' => 'Awaiting Review',
                        'returned' => 'Returned',
                        'approved' => 'Approved',
                    ] as $value => $label)

                        <option value="{{ $value }}"
                            @selected($statusFilter === $value)>

                            {{ $label }}

                        </option>

                    @endforeach

                </select>

            </div>


            <!-- SEARCH TEACHER -->

            <div class="sm:col-span-2 xl:col-span-4">

                <label for="search"
                       class="mb-2 block text-xs
                              font-bold text-slate-600">

                    Search Teacher or Classroom

                </label>

                <div class="relative">

                    <i data-lucide="search"
                       class="absolute left-3 top-1/2
                              h-4 w-4 -translate-y-1/2
                              text-slate-400"></i>

                    <input id="search"
                           type="search"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Enter Teacher or classroom name..."
                           class="w-full rounded-xl
                                  border border-slate-200
                                  bg-white py-3 pl-10 pr-4
                                  text-sm outline-none
                                  focus:border-teal-500">

                </div>

            </div>


            <!-- FILTER ACTIONS -->

            <div class="flex flex-wrap gap-3
                        sm:col-span-2 xl:col-span-4">

                <button type="submit"
                        class="inline-flex items-center
                               gap-2 rounded-xl bg-teal-600
                               px-6 py-3 text-sm font-bold
                               text-white hover:bg-teal-700">

                    <i data-lucide="filter"
                       class="h-4 w-4"></i>

                    Apply Filters

                </button>


                <a href="{{ route('principal.teacher-progress.index') }}"
                   class="rounded-xl border
                          border-slate-200 bg-white
                          px-6 py-3 text-sm
                          font-bold text-slate-600
                          hover:bg-slate-50">

                    Reset Filters

                </a>

            </div>

        </form>

    </section>


    <!-- ======================================
         PROGRESS TABLE
    ======================================= -->

    <section class="overflow-hidden rounded-2xl
                    border border-slate-200
                    bg-white shadow-sm">

        <div class="flex flex-wrap items-center
                    justify-between gap-4
                    border-b border-slate-100 p-6">

            <div>

                <h2 class="text-lg font-extrabold
                           text-slate-900">

                    Classroom Submission Monitoring

                </h2>

                <p class="mt-1 text-xs text-slate-500">

                    {{ $rows->total() }}
                    classroom-semester record(s) found.

                </p>

            </div>

            <span class="rounded-full bg-teal-50
                         px-4 py-2 text-xs
                         font-bold text-teal-700">

                {{ $stats['approved'] }}
                Approved Submissions

            </span>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full min-w-[1080px]
                          text-left text-sm">

                <thead class="bg-slate-50 text-xs
                              uppercase tracking-wide
                              text-slate-500">

                    <tr>

                        <th class="px-5 py-4">
                            Teacher / Classroom
                        </th>

                        <th class="px-5 py-4">
                            Semester
                        </th>

                        <th class="px-5 py-4">
                            Uploaded
                        </th>

                        <th class="px-5 py-4">
                            Approval Progress
                        </th>

                        <th class="px-5 py-4">
                            Issues
                        </th>

                        <th class="px-5 py-4">
                            Submission Status
                        </th>

                        <th class="px-5 py-4 text-right">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($rows as $item)

                        @php

                            $statusClass = match(
                                $item['status']
                            ) {
                                'approved' =>
                                    'bg-emerald-50 text-emerald-700',

                                'submitted' =>
                                    'bg-blue-50 text-blue-700',

                                'returned' =>
                                    'bg-amber-50 text-amber-800',

                                'draft' =>
                                    'bg-slate-100 text-slate-700',

                                default =>
                                    'bg-slate-100 text-slate-600',
                            };

                            $statusLabel = match(
                                $item['status']
                            ) {
                                'approved' => 'Approved',
                                'submitted' => 'Awaiting Review',
                                'returned' => 'Returned',
                                'draft' => 'Draft',
                                default => 'Not Submitted',
                            };

                        @endphp


                        <tr class="transition
                                   hover:bg-slate-50/80">


                            <!-- TEACHER / CLASSROOM -->

                            <td class="px-5 py-5">

                                <p class="text-sm font-bold
                                          text-slate-900">

                                    {{ $item['teacher'] }}

                                </p>

                                <p class="mt-2 text-xs
                                          font-semibold
                                          text-teal-700">

                                    {{ $item['classroom'] }}

                                </p>

                                <p class="mt-1 text-xs
                                          text-slate-500">

                                    Grade {{ $item['grade'] }}

                                    · {{ $item['strand'] }}

                                </p>

                            </td>


                            <!-- SEMESTER -->

                            <td class="px-5 py-5">

                                <p class="font-semibold
                                          text-slate-800">

                                    {{ $item['semester'] === 1
                                        ? '1st Semester'
                                        : '2nd Semester' }}

                                </p>

                                <p class="mt-2 text-xs
                                          text-slate-500">

                                    {{ $item['period_label'] }}

                                </p>

                            </td>


                            <!-- UPLOADED -->

                            <td class="px-5 py-5">

                                <p class="font-bold
                                          text-slate-800">

                                    {{ $item['uploaded'] }}
                                    /
                                    {{ $item['total'] }}

                                </p>

                                <p class="mt-1 text-xs
                                          text-slate-500">

                                    {{ $item['pending'] }}
                                    awaiting approval

                                </p>

                            </td>


                            <!-- PROGRESS -->

                            <td class="px-5 py-5">

                                @if($item['total'] > 0)

                                    <div class="flex items-center
                                                justify-between
                                                gap-3">

                                        <p class="text-xs
                                                  font-bold
                                                  text-slate-800">

                                            {{ $item['approved'] }}
                                            /{{ $item['total'] }}
                                            approved

                                        </p>

                                        <span class="text-xs
                                                     font-extrabold
                                                     text-teal-700">

                                            {{ $item['percentage'] }}%

                                        </span>

                                    </div>


                                    <div class="mt-3 h-2.5
                                                w-44 overflow-hidden
                                                rounded-full bg-slate-100">

                                        <div class="h-full
                                                    rounded-full
                                                    bg-teal-600"
                                             style="width: {{ $item['percentage'] }}%">
                                        </div>

                                    </div>

                                @else

                                    <p class="text-xs
                                              text-slate-400">

                                        No active requirements

                                    </p>

                                @endif

                            </td>


                            <!-- ISSUES -->

                            <td class="px-5 py-5">

                                <div class="space-y-1">

                                    <p class="text-xs
                                              font-bold text-amber-700">

                                        {{ $item['returned'] }}
                                        Returned

                                    </p>

                                    <p class="text-xs
                                              font-bold text-red-700">

                                        {{ $item['missing'] }}
                                        Missing

                                    </p>

                                </div>

                            </td>


                            <!-- STATUS -->

                            <td class="px-5 py-5">

                                <span class="inline-flex
                                             rounded-full px-3 py-1.5
                                             text-xs font-bold
                                             {{ $statusClass }}">

                                    {{ $statusLabel }}

                                </span>

                            </td>


                            <!-- ACTION -->

                            <td class="px-5 py-5 text-right">

                                @if($item['submission'])

                                    <a href="{{ route(
                                            'principal.submissions.review.show',
                                            $item['submission']
                                        ) }}"
                                       class="inline-flex
                                              items-center gap-2
                                              rounded-xl bg-[#142b61]
                                              px-4 py-2.5
                                              text-xs font-bold text-white
                                              hover:bg-blue-900">

                                        <i data-lucide="eye"
                                           class="h-4 w-4"></i>

                                        {{ $item['status'] === 'submitted'
                                            ? 'Review'
                                            : 'View Details' }}

                                    </a>

                                @else

                                    <span class="text-xs
                                                 font-semibold
                                                 text-slate-400">

                                        No Submission

                                    </span>

                                @endif

                            </td>

                        </tr>


                        <!-- ATTENTION ROW -->

                        @if(count($item['attention']) > 0)

                            <tr class="bg-amber-50/40">

                                <td colspan="7"
                                    class="px-5 py-3">

                                    <div class="flex items-start
                                                gap-2 text-xs
                                                text-amber-900">

                                        <i data-lucide="alert-circle"
                                           class="mt-0.5 h-4 w-4 shrink-0"></i>

                                        <p class="leading-5">

                                            <strong>
                                                Needs Attention:
                                            </strong>

                                            {{ implode(
                                                ', ',
                                                $item['attention']
                                            ) }}

                                        </p>

                                    </div>

                                </td>

                            </tr>

                        @endif


                    @empty

                        <tr>

                            <td colspan="7"
                                class="px-6 py-16 text-center">

                                <div class="mx-auto grid h-14 w-14
                                            place-items-center
                                            rounded-xl bg-slate-100">

                                    <i data-lucide="school"
                                       class="h-7 w-7
                                              text-slate-400"></i>

                                </div>

                                <p class="mt-4 font-bold
                                          text-slate-800">

                                    No Classroom Records Found

                                </p>

                                <p class="mt-2 text-sm
                                          text-slate-500">

                                    Try selecting another school year,
                                    semester, grade, or status.

                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- PAGINATION -->

        @if($rows->hasPages())

            <div class="border-t border-slate-100
                        p-5">

                {{ $rows->links() }}

            </div>

        @endif

    </section>


    <!-- ======================================
         MONITORING EXPLANATION
    ======================================= -->

  

</div>

@endsection
