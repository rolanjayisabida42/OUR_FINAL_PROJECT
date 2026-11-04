
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>Principal Dashboard | SHS Forms</title>

    <!-- TAILWIND CSS V4 -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- INTER FONT -->
    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- LUCIDE ICONS -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            color: #dbeafe;
            transition: .2s;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, .1);
            color: white;
        }

        .nav-active {
            background: rgba(255, 255, 255, .15);
            color: white;
            font-weight: 600;
        }
    </style>

</head>


<body class="bg-[#f5f7fb] text-slate-800 antialiased">

    <!-- ======================================
         MOBILE SIDEBAR OVERLAY
    ======================================= -->

    <div id="sidebarOverlay"
         class="fixed inset-0 z-40 hidden
                bg-slate-950/50 lg:hidden">
    </div>


    <!-- SIDEBAR -->
    <x-principal.sidebar />


    <!-- ======================================
         MAIN CONTENT WRAPPER
    ======================================= -->

    <div class="min-h-screen lg:ml-72">

        <!-- HEADER -->
        <x-principal.header />


        <main id="dashboard"
              class="mx-auto max-w-7xl space-y-8
                     p-5 lg:p-8">


            <!-- ==================================
                 WELCOME BANNER
            =================================== -->

            <section class="relative overflow-hidden
                            rounded-2xl
                            bg-gradient-to-r
                            from-[#142b61] to-[#2857b5]
                            p-7 text-white md:p-9">

                <div class="pointer-events-none absolute
                            -right-20 -top-20
                            h-72 w-72 rounded-full
                            bg-white/5">
                </div>

                <div class="relative z-10">

                    <!-- CURRENT DATE -->

                    <span class="inline-flex items-center gap-2
                                 rounded-full bg-white/10
                                 px-3 py-1.5 text-xs text-blue-100">

                        <i data-lucide="calendar"
                           class="h-4 w-4"></i>

                        {{ now('Asia/Manila')->format('F d, Y') }}

                    </span>


                    <!-- WELCOME MESSAGE -->

                    <h1 class="mt-5 text-2xl font-bold
                               md:text-3xl">

                        Welcome back, {{ auth()->user()->name }}!

                    </h1>

                    <p class="mt-3 max-w-xl text-sm
                              leading-7 text-blue-100">

                        Review school forms, monitor classroom
                        requirements, and follow up on
                        incomplete submissions.

                    </p>


                    <!-- QUICK ACTIONS -->

                    <div class="mt-6 flex flex-wrap gap-3">

                        <a href="{{ route(
                                'principal.submissions.review.index',
                                ['status' => 'submitted']
                            ) }}"
                           class="inline-flex items-center gap-2
                                  rounded-lg bg-white
                                  px-4 py-2.5 text-sm
                                  font-semibold text-blue-900
                                  hover:bg-blue-50">

                            <i data-lucide="clipboard-check"
                               class="h-4 w-4"></i>

                            Review Submissions

                        </a>


                        <!-- SCHOOL YEAR -->

                        <span class="rounded-lg
                                     border border-white/25
                                     bg-white/10 px-4 py-2.5
                                     text-sm font-semibold">

                            SY {{ $schoolYear?->name ?? 'Not configured' }}

                        </span>


                        <!-- SEMESTER -->

                        <span class="rounded-lg
                                     border border-white/25
                                     bg-white/10 px-4 py-2.5
                                     text-sm font-semibold">

                            @if($semester?->semester_number == 1)

                                First Semester

                            @elseif($semester?->semester_number == 2)

                                Second Semester

                            @else

                                No Semester

                            @endif

                        </span>


                        <!-- ACTIVE CLASSROOMS -->

                        <span class="rounded-lg
                                     border border-white/25
                                     bg-white/10 px-4 py-2.5
                                     text-sm font-semibold">

                            {{ $stats['classrooms'] }}
                            Active Classrooms

                        </span>

                    </div>

                </div>

            </section>


            <!-- ==================================
                 ACADEMIC CONFIGURATION WARNING
            =================================== -->

            @if(!$schoolYear || !$semester)

                <div class="rounded-2xl
                            border border-amber-200
                            bg-amber-50 px-6 py-5
                            text-sm text-amber-900">

                    @if(!$schoolYear)

                        No school year exists yet.

                    @else

                        This school year has no configured semesters.

                    @endif

                    Configure the academic calendar
                    under Admin School Years.

                </div>

            @endif


            <!-- ==================================
                 STATISTICS
            =================================== -->

            <section>

                <div class="mb-5">

                    <h2 class="text-lg font-bold
                               text-slate-900">

                        Submission Overview

                    </h2>

                    <p class="mt-1 text-sm text-slate-500">

                        Live submission totals for the displayed
                        school year and semester.

                    </p>

                </div>


                @php

                    $statCards = [

                        [
                            'label' => 'Pending Reviews',
                            'value' => $stats['submitted'],
                            'icon' => 'clock-3',
                            'tag' => 'To Review',
                            'color' => 'blue',
                            'url' => route(
                                'principal.submissions.review.index',
                                ['status' => 'submitted']
                            ),
                        ],

                        [
                            'label' => 'Approved Submissions',
                            'value' => $stats['approved'],
                            'icon' => 'check-circle-2',
                            'tag' => 'Completed',
                            'color' => 'emerald',
                            'url' => route(
                                'principal.submissions.review.index',
                                ['status' => 'approved']
                            ),
                        ],

                        [
                            'label' => 'Returned Submissions',
                            'value' => $stats['returned'],
                            'icon' => 'rotate-ccw',
                            'tag' => 'Corrections',
                            'color' => 'red',
                            'url' => route(
                                'principal.submissions.review.index',
                                ['status' => 'returned']
                            ),
                        ],

                        [
                            'label' => 'Outstanding Requirements',
                            'value' => $stats['missing'],
                            'icon' => 'file-warning',
                            'tag' => 'Needs Action',
                            'color' => 'amber',
                            'url' => '#missing',
                        ],

                    ];

                    $cardColors = [

                        'blue' => [
                            'bg-blue-50',
                            'text-blue-700'
                        ],

                        'emerald' => [
                            'bg-emerald-50',
                            'text-emerald-700'
                        ],

                        'red' => [
                            'bg-red-50',
                            'text-red-700'
                        ],

                        'amber' => [
                            'bg-amber-50',
                            'text-amber-700'
                        ],

                    ];

                @endphp


                <div class="grid gap-5 sm:grid-cols-2
                            xl:grid-cols-4">

                    @foreach($statCards as $card)

                        @php
                            $tone = $cardColors[$card['color']];
                        @endphp


                        <a href="{{ $card['url'] }}"
                           class="block rounded-2xl border
                                  border-slate-200 bg-white
                                  p-6 shadow-sm transition
                                  hover:-translate-y-0.5
                                  hover:shadow-md">

                            <div class="flex items-center
                                        justify-between gap-2">

                                <div class="rounded-xl p-3
                                            {{ $tone[0] }}
                                            {{ $tone[1] }}">

                                    <i data-lucide="{{ $card['icon'] }}"
                                       class="h-6 w-6"></i>

                                </div>

                                <span class="rounded-full
                                             px-3 py-1 text-xs
                                             font-semibold
                                             {{ $tone[0] }}
                                             {{ $tone[1] }}">

                                    {{ $card['tag'] }}

                                </span>

                            </div>


                            <p class="mt-5 text-3xl
                                      font-bold text-slate-900">

                                {{ number_format($card['value']) }}

                            </p>

                            <p class="mt-1 text-sm text-slate-500">

                                {{ $card['label'] }}

                            </p>

                        </a>

                    @endforeach

                </div>

                <p class="mt-3 text-xs text-slate-500">

                    Outstanding Requirements counts individual
                    documents that are missing or need correction.
                    Other counters count classroom submissions.

                </p>

            </section>


            <!-- ==================================
                 RECENT TEACHER SUBMISSIONS
            =================================== -->

            <section id="submissions"
                     class="scroll-mt-28 overflow-hidden
                            rounded-2xl border
                            border-slate-200 bg-white
                            shadow-sm">


                <!-- SECTION HEADER -->

                <div class="flex flex-wrap items-center
                            justify-between gap-4
                            border-b border-slate-100
                            px-6 py-5">

                    <div>

                        <h2 class="font-bold text-slate-900">

                            Recent Teacher Submissions

                        </h2>

                        <p class="mt-1 text-xs text-slate-500">

                            Latest {{ $recentSubmissions->count() }}
                            records for the displayed semester.

                        </p>

                    </div>

                    <a href="{{ route('principal.submissions.review.index') }}"
                       class="text-sm font-bold
                              text-teal-700 hover:underline">

                        View All Submissions

                    </a>

                </div>


                <!-- SEARCH AND STATUS FILTER -->

                <div class="flex flex-wrap gap-3
                            border-b border-slate-100 p-5">

                    <div class="relative min-w-0 flex-1
                                sm:min-w-64">

                        <i data-lucide="search"
                           class="absolute left-3 top-1/2
                                  h-4 w-4 -translate-y-1/2
                                  text-slate-400"></i>

                        <input id="submissionSearch"
                               type="search"
                               placeholder="Search Teacher or Classroom..."
                               class="w-full rounded-xl
                                      border border-slate-200
                                      bg-slate-50 py-3 pl-10 pr-4
                                      text-sm outline-none
                                      focus:border-blue-500">

                    </div>


                    <select id="statusFilter"
                            aria-label="Filter submissions by status"
                            class="rounded-xl border
                                   border-slate-200 bg-white
                                   px-4 py-3 text-sm
                                   outline-none
                                   focus:border-blue-500">

                        <option value="all">
                            All Statuses
                        </option>

                        <option value="submitted">
                            Awaiting Review
                        </option>

                        <option value="approved">
                            Approved
                        </option>

                        <option value="returned">
                            Returned
                        </option>

                        <option value="draft">
                            Draft
                        </option>

                    </select>

                </div>


                <!-- SUBMISSION TABLE -->

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[850px]
                                  text-left text-sm">

                        <thead class="bg-slate-50 text-xs
                                      uppercase tracking-wide
                                      text-slate-500">

                            <tr>

                                <th class="px-6 py-4">
                                    Teacher
                                </th>

                                <th class="px-6 py-4">
                                    Classroom
                                </th>

                                <th class="px-6 py-4">
                                    Semester
                                </th>

                                <th class="px-6 py-4">
                                    Files
                                </th>

                                <th class="px-6 py-4">
                                    Submitted
                                </th>

                                <th class="px-6 py-4">
                                    Status
                                </th>

                                <th class="px-6 py-4 text-right">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody id="submissionTable"
                               class="divide-y divide-slate-100">

                            @forelse($recentSubmissions as $submission)

                                @php

                                    $statusStyle = match(
                                        $submission->status
                                    ) {

                                        'submitted' =>
                                            'bg-blue-50 text-blue-700',

                                        'approved' =>
                                            'bg-emerald-50 text-emerald-700',

                                        'returned' =>
                                            'bg-amber-50 text-amber-800',

                                        default =>
                                            'bg-slate-100 text-slate-700',
                                    };

                                    $searchText = mb_strtolower(
                                        ($submission->teacher?->name ?? '')
                                        . ' '
                                        . ($submission->section?->name ?? '')
                                        . ' '
                                        . ($submission->semester?->semester_number ?? '')
                                    );

                                @endphp


                                <tr data-submission-row
                                    data-status="{{ $submission->status }}"
                                    data-search="{{ $searchText }}"
                                    class="hover:bg-slate-50">


                                    <!-- TEACHER -->

                                    <td class="whitespace-nowrap
                                               px-6 py-5
                                               font-semibold">

                                        {{ $submission->teacher?->name ?? 'Unassigned' }}

                                    </td>


                                    <!-- CLASSROOM -->

                                    <td class="px-6 py-5">

                                        <p class="font-semibold">

                                            {{ $submission->section?->name ?? 'Unknown' }}

                                        </p>

                                        <p class="mt-1 text-xs
                                                  text-slate-500">

                                            Grade
                                            {{ $submission->section?->grade_level ?? 'N/A' }}

                                        </p>

                                    </td>


                                    <!-- SEMESTER -->

                                    <td class="whitespace-nowrap
                                               px-6 py-5">

                                        {{ $submission->semester?->semester_number == 1
                                            ? '1st Semester'
                                            : '2nd Semester' }}

                                    </td>


                                    <!-- UPLOADED FILE COUNT -->

                                    <td class="px-6 py-5">

                                        {{ $submission->files_count }}

                                    </td>


                                    <!-- SUBMISSION DATE -->

                                    <td class="whitespace-nowrap
                                               px-6 py-5
                                               text-xs text-slate-500">

                                        {{ $submission->submitted_at
                                            ? \Carbon\Carbon::parse($submission->submitted_at)
                                                ->timezone('Asia/Manila')
                                                ->format('M d, Y h:i A')
                                            : 'Not Submitted' }}

                                    </td>


                                    <!-- STATUS -->

                                    <td class="px-6 py-5">

                                        <span class="rounded-full
                                                     px-3 py-1.5
                                                     text-xs font-bold
                                                     {{ $statusStyle }}">

                                            {{ ucfirst($submission->status) }}

                                        </span>

                                    </td>


                                    <!-- ACTION -->

                                    <td class="px-6 py-5 text-right">

                                        <a href="{{ route('principal.submissions.review.show', $submission) }}"
                                           class="inline-flex rounded-lg
                                                  bg-[#142b61]
                                                  px-4 py-2.5
                                                  text-xs font-bold
                                                  text-white
                                                  hover:bg-blue-900">

                                            {{ $submission->status === 'submitted'
                                                ? 'Review'
                                                : 'View' }}

                                        </a>

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td colspan="7"
                                        class="px-6 py-12 text-center
                                               text-sm text-slate-500">

                                        No Teacher submissions yet
                                        for this semester.

                                    </td>

                                </tr>

                            @endforelse


                            <!-- EMPTY FILTER STATE -->

                            <tr id="filteredEmpty"
                                class="hidden">

                                <td colspan="7"
                                    class="px-6 py-12 text-center
                                           text-sm text-slate-500">

                                    No submissions match your search.

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


                <!-- RESULT COUNT -->

                <p id="submissionResultCount"
                   class="border-t border-slate-100
                          px-6 py-4 text-xs text-slate-500">

                    Showing {{ $recentSubmissions->count() }}
                    recent records.
                    Use View All for the complete list.

                </p>

            </section>


            <!-- ==================================
                 TEACHER PROGRESS AND MISSING
            =================================== -->

            <div class="grid gap-6 xl:grid-cols-2">


                <!-- ==============================
                     TEACHER PROGRESS
                =============================== -->

                <section id="teacher-progress"
                         class="scroll-mt-28 overflow-hidden
                                rounded-2xl border
                                border-slate-200 bg-white
                                shadow-sm">

                    <div class="border-b border-slate-100
                                px-6 py-5">

                        <h2 class="font-bold">

                            Teacher Submission Progress

                        </h2>

                        <p class="mt-1 text-xs text-slate-500">

                            Approved requirements per active
                            classroom in this semester.

                        </p>

                    </div>


                    <div class="max-h-[540px] space-y-6
                                overflow-y-auto p-6">

                        @forelse($progress as $item)

                            <div>

                                <!-- TEACHER AND CLASSROOM -->

                                <div class="flex items-start
                                            justify-between gap-3">

                                    <div>

                                        <p class="text-sm font-semibold
                                                  text-slate-900">

                                            {{ $item['teacher'] }}

                                        </p>

                                        <p class="mt-1 text-xs
                                                  text-slate-500">

                                            {{ $item['classroom'] }}

                                            · Grade {{ $item['grade'] }}

                                        </p>

                                    </div>


                                    <!-- APPROVED/TOTAL -->

                                    <span class="whitespace-nowrap
                                                 text-sm font-bold
                                                 text-blue-700">

                                        @if($item['configured'])

                                            {{ $item['approved'] }}/{{ $item['total'] }}

                                        @else

                                            —

                                        @endif

                                    </span>

                                </div>


                                @if($item['configured'] && $item['total'] > 0)

                                    <!-- PROGRESS BAR -->

                                    <div class="mt-3 h-2
                                                overflow-hidden
                                                rounded-full
                                                bg-slate-100">

                                        <div class="h-full
                                                    rounded-full
                                                    bg-blue-600"
                                             style="width: {{ $item['percent'] }}%">
                                        </div>

                                    </div>


                                    <p class="mt-2 text-xs text-slate-500">

                                        {{ $item['percent'] }}%
                                        approved

                                        · {{ $item['uploaded'] }}
                                        complete uploads

                                    </p>

                                @else

                                    <p class="mt-2 text-xs
                                              text-amber-700">

                                        No active requirements configured
                                        for this classroom and semester.

                                    </p>

                                @endif

                            </div>

                        @empty

                            <p class="py-8 text-center
                                      text-sm text-slate-500">

                                No active classrooms found.

                            </p>

                        @endforelse

                    </div>

                </section>



                <!-- ==============================
                     OUTSTANDING REQUIREMENTS
                =============================== -->

                <section id="missing"
                         class="scroll-mt-28 overflow-hidden
                                rounded-2xl border
                                border-slate-200 bg-white
                                shadow-sm">


                    <!-- HEADER -->

                    <div class="flex items-center
                                justify-between gap-2
                                border-b border-slate-100
                                px-6 py-5">

                        <div>

                            <h2 class="font-bold">

                                Outstanding Requirements

                            </h2>

                            <p class="mt-1 text-xs text-slate-500">

                                Missing documents and returned
                                files awaiting correction.

                            </p>

                        </div>


                        <span class="rounded-full bg-amber-50
                                     px-3 py-1 text-xs
                                     font-bold text-amber-800">

                            {{ $stats['missing'] }} Total

                        </span>

                    </div>


                    <!-- OUTSTANDING LIST -->

                    <div class="max-h-[540px] space-y-3
                                overflow-y-auto p-5">

                        @forelse($outstanding as $item)

                            <a href="{{ $item['url'] }}"
                               class="flex items-start gap-3
                                      rounded-xl border
                                      border-slate-100 p-4
                                      transition hover:bg-slate-50">


                                <!-- STATUS ICON -->

                                <div class="rounded-lg p-2.5
                                            {{ $item['status'] === 'returned'
                                                ? 'bg-red-50 text-red-600'
                                                : 'bg-amber-50 text-amber-600' }}">

                                    <i data-lucide="{{ $item['status'] === 'returned'
                                            ? 'rotate-ccw'
                                            : 'file-warning' }}"
                                       class="h-5 w-5"></i>

                                </div>


                                <!-- DETAILS -->

                                <div class="min-w-0 flex-1">

                                    <p class="text-sm font-semibold
                                              text-slate-900">

                                        {{ $item['teacher'] }}

                                    </p>

                                    <p class="mt-1 text-xs
                                              text-slate-500">

                                        {{ $item['classroom'] }}

                                        · {{ $item['requirement'] }}

                                    </p>

                                </div>


                                <!-- STATUS -->

                                <span class="rounded-full
                                             px-3 py-1
                                             text-[11px] font-bold
                                             {{ $item['status'] === 'returned'
                                                ? 'bg-red-50 text-red-700'
                                                : 'bg-amber-50 text-amber-800' }}">

                                    {{ ucfirst($item['status']) }}

                                </span>

                            </a>

                        @empty

                            <p class="py-8 text-center
                                      text-sm text-slate-500">

                                No outstanding requirements
                                for the configured period.

                            </p>

                        @endforelse


                        @if($stats['missing'] > $outstanding->count())

                            <p class="text-center text-xs
                                      text-slate-500">

                                Showing the first
                                {{ $outstanding->count() }}
                                of {{ $stats['missing'] }}
                                outstanding requirements.

                            </p>

                        @endif

                    </div>

                </section>

            </div>


            <!-- ==================================
                 APPROVED AND RETURNED HISTORY
            =================================== -->

            <section id="history"
                     class="scroll-mt-28 overflow-hidden
                            rounded-2xl border
                            border-slate-200 bg-white
                            shadow-sm">


                <!-- HEADER -->

                <div class="flex flex-wrap items-center
                            justify-between gap-3
                            border-b border-slate-100
                            px-6 py-5">

                    <div>

                        <h2 class="font-bold">

                            Approved & Returned Submissions

                        </h2>

                        <p class="mt-1 text-xs text-slate-500">

                            Recent outcomes from Principal reviews.

                        </p>

                    </div>

                    <a href="{{ route('principal.submissions.review.index') }}"
                       class="text-xs font-bold text-teal-700
                              hover:underline">

                        View All

                    </a>

                </div>


                <!-- TABLE -->

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[700px]
                                  text-left text-sm">

                        <thead class="bg-slate-50
                                      text-xs uppercase
                                      text-slate-500">

                            <tr>

                                <th class="px-6 py-4">
                                    Teacher
                                </th>

                                <th class="px-6 py-4">
                                    Classroom
                                </th>

                                <th class="px-6 py-4">
                                    Semester
                                </th>

                                <th class="px-6 py-4">
                                    Status
                                </th>

                                <th class="px-6 py-4 text-right">
                                    Details
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            @forelse($history as $submission)

                                <tr class="hover:bg-slate-50">

                                    <td class="px-6 py-4 font-semibold">

                                        {{ $submission->teacher?->name ?? 'Unassigned' }}

                                    </td>


                                    <td class="px-6 py-4">

                                        {{ $submission->section?->name ?? 'Unknown' }}

                                    </td>


                                    <td class="px-6 py-4">

                                        {{ $submission->semester?->semester_number == 1
                                            ? '1st Semester'
                                            : '2nd Semester' }}

                                    </td>


                                    <td class="px-6 py-4">

                                        <span class="rounded-full
                                                     px-3 py-1
                                                     text-xs font-bold
                                                     {{ $submission->status === 'approved'
                                                        ? 'bg-emerald-50 text-emerald-700'
                                                        : 'bg-amber-50 text-amber-800' }}">

                                            {{ ucfirst($submission->status) }}

                                        </span>

                                    </td>


                                    <td class="px-6 py-4 text-right">

                                        <a href="{{ route('principal.submissions.review.show', $submission) }}"
                                           class="text-xs font-bold
                                                  text-blue-700
                                                  hover:underline">

                                            View Details

                                        </a>

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td colspan="5"
                                        class="px-6 py-12 text-center
                                               text-sm text-slate-500">

                                        No completed reviews
                                        yet for this semester.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </section>


         


            <!-- ==================================
                 ACCOUNT SETTINGS
            =================================== -->

            <section id="settings"
                     class="scroll-mt-28 rounded-2xl
                            border border-slate-200
                            bg-white p-6 shadow-sm">

                <div class="flex flex-wrap items-center
                            justify-between gap-4">


                    <div class="flex items-center gap-4">

                        <div class="rounded-xl bg-slate-100
                                    p-3 text-slate-600">

                            <i data-lucide="settings"
                               class="h-6 w-6"></i>

                        </div>


                        <div>

                            <h2 class="font-bold">

                                Principal Account Settings

                            </h2>

                            <p class="mt-1 text-xs text-slate-500">

                                Manage your profile and
                                account password securely.

                            </p>

                        </div>

                    </div>


                    <a href="{{ route('principal.settings') }}"
                       class="rounded-xl bg-[#142b61]
                              px-5 py-3 text-sm
                              font-bold text-white
                              hover:bg-blue-900">

                        Open Settings

                    </a>

                </div>

            </section>


          

        </main>

    </div>


    <!-- ==========================================
         JAVASCRIPT
    =========================================== -->

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // =====================================
        // SEARCH AND STATUS FILTER
        // =====================================

        const search = document.getElementById(
            'submissionSearch'
        );

        const status = document.getElementById(
            'statusFilter'
        );

        const rows = Array.from(
            document.querySelectorAll(
                '[data-submission-row]'
            )
        );

        const empty = document.getElementById(
            'filteredEmpty'
        );

        const result = document.getElementById(
            'submissionResultCount'
        );


        function filterRows() {

            const term = (
                search?.value || ''
            ).trim().toLowerCase();

            const selected = status?.value || 'all';

            let visible = 0;


            rows.forEach(function (row) {

                const matchesSearch =
                    !term ||
                    row.dataset.search.includes(term);

                const matchesStatus =
                    selected === 'all' ||
                    row.dataset.status === selected;

                const matches =
                    matchesSearch && matchesStatus;

                row.classList.toggle(
                    'hidden',
                    !matches
                );

                if (matches) {
                    visible++;
                }

            });


            if (empty) {

                empty.classList.toggle(
                    'hidden',
                    visible !== 0 || rows.length === 0
                );
            }

            if (result) {

                result.textContent =
                    'Showing ' + visible +
                    ' of ' + rows.length +
                    ' recent submission(s). ' +
                    'Use View All for the full list.';
            }
        }


        search?.addEventListener(
            'input',
            filterRows
        );

        status?.addEventListener(
            'change',
            filterRows
        );


        // =====================================
        // RESPONSIVE SIDEBAR
        // =====================================

        const sidebar = document.getElementById(
            'sidebar'
        );

        const overlay = document.getElementById(
            'sidebarOverlay'
        );

        const openButton = document.getElementById(
            'openSidebar'
        );

        const closeButton = document.getElementById(
            'closeSidebar'
        );


        function setSidebar(open) {

            sidebar?.classList.toggle(
                '-translate-x-full',
                !open
            );

            overlay?.classList.toggle(
                'hidden',
                !open
            );

            openButton?.setAttribute(
                'aria-expanded',
                String(open)
            );
        }


        openButton?.addEventListener(
            'click',
            () => setSidebar(true)
        );

        closeButton?.addEventListener(
            'click',
            () => setSidebar(false)
        );

        overlay?.addEventListener(
            'click',
            () => setSidebar(false)
        );


        document.querySelectorAll(
            '#sidebar a'
        ).forEach(function (link) {

            link.addEventListener(
                'click',
                function () {

                    if (window.innerWidth < 1024) {
                        setSidebar(false);
                    }
                }
            );

        });


        // =====================================
        // PRINCIPAL NOTIFICATION SUMMARY
        // =====================================

        const notificationBtn = document.getElementById(
            'notificationBtn'
        );

        const notificationPanel = document.getElementById(
            'notificationPanel'
        );

        const notificationDot = document.getElementById(
            'notificationDot'
        );

        const notificationText = document.getElementById(
            'notificationText'
        );

        const pending = @json($stats['submitted']);


        if (notificationText) {

            notificationText.textContent = pending

                ? pending +
                    ' classroom submission(s) awaiting your review.'

                : 'No submissions awaiting review.';
        }


        notificationDot?.classList.toggle(
            'hidden',
            pending === 0
        );


        notificationBtn?.addEventListener(
            'click',
            function (event) {

                event.stopPropagation();

                notificationPanel?.classList.toggle(
                    'hidden'
                );

                notificationBtn.setAttribute(
                    'aria-expanded',
                    String(
                        !notificationPanel?.classList.contains(
                            'hidden'
                        )
                    )
                );
            }
        );


        document.addEventListener(
            'click',
            function (event) {

                if (
                    notificationPanel &&
                    !notificationPanel.contains(event.target) &&
                    !notificationBtn?.contains(event.target)
                ) {

                    notificationPanel.classList.add(
                        'hidden'
                    );

                    notificationBtn?.setAttribute(
                        'aria-expanded',
                        'false'
                    );
                }
            }
        );


        // =====================================
        // ESCAPE KEY
        // =====================================

        document.addEventListener(
            'keydown',
            function (event) {

                if (event.key === 'Escape') {

                    setSidebar(false);

                    notificationPanel?.classList.add(
                        'hidden'
                    );

                    notificationBtn?.setAttribute(
                        'aria-expanded',
                        'false'
                    );
                }
            }
        );


        // =====================================
        // INITIALIZE LUCIDE ICONS
        // =====================================

        if (window.lucide) {
            window.lucide.createIcons();
        }

    });
    </script>

</body>
</html>
