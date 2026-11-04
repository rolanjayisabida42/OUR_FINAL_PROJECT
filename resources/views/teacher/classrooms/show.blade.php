
@extends('layouts.teacher')

@section('title', 'Classroom Overview')
@section('breadcrumb', 'My Classrooms / ' . $section->name)
@section('page_heading', 'Classroom Overview')

@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | CLASSROOM MODULES
    |--------------------------------------------------------------------------
    */

    $modules = [

        [
            'key' => 'students',
            'number' => '01',
            'title' => 'Student Information',
            'description' =>
                'Manage learner profiles, LRNs, personal information, addresses, and parent or guardian details.',
            'icon' => 'users-round',
            'available' => true,
            'route' => 'teacher.students.index',
            'action' => 'Manage Students',
            'archived_action' => 'View Students',
        ],

        [
            'key' => 'records',
            'number' => '02',
            'title' => 'Class Records',
            'description' =>
                'Select and prepare multiple Excel Class Records for the selected classroom.',
            'icon' => 'files',
            'available' => true,
            'route' => 'teacher.classrooms.class-records',
            'action' => 'Open Class Records',
            'archived_action' => 'View Class Records',
        ],

        [
            'key' => 'sheets',
            'number' => '03',
            'title' => 'Master Sheets',
            'description' =>
                'Organize multiple subject Class Records and prepare a consolidated Master Sheet containing students grades across different subjects.',
            'icon' => 'table-2',
            'available' => true,
            'route' => 'teacher.classrooms.master-sheets',
            'action' => 'Generate Master Sheet',
            'archived_action' => 'View Master Sheets',
        ],

        [
            'key' => 'cards',
            'number' => '04',
            'title' => 'Student Cards',
            'description' =>
                'Generate individual student report cards using finalized Master Sheets.',
            'icon' => 'contact-round',
            'available' => false,
            'route' => null,
            'action' => null,
            'archived_action' => null,
        ],

        [
            'key' => 'forms',
            'number' => '05',
            'title' => 'School Forms',
            'description' =>
                'Generate and manage SF1, SF2, SF5, SF6, and SF7.',
            'icon' => 'file-text',
            'available' => false,
            'route' => null,
            'action' => null,
            'archived_action' => null,
        ],

        [
            'key' => 'submissions',
            'number' => '06',
            'title' => 'Submission of Requirements',
            'description' =>
                'Prepare semester requirements, upload documents, and monitor Principal review and approval status.',
            'icon' => 'clipboard-check',
            'available' => true,
            'route' => 'teacher.classrooms.submissions',
            'action' => 'Manage Submissions',
            'archived_action' => 'View Submissions',
        ],

    ];

    $availableCount = collect($modules)
        ->where('available', true)
        ->count();

@endphp


<div class="mx-auto max-w-7xl space-y-8 pb-12">

    <!-- ========================================
         BACK NAVIGATION
    ========================================= -->

    <div class="flex flex-wrap items-center
                justify-between gap-4">

        <a href="{{ route('teacher.classrooms.index') }}"
           class="inline-flex items-center gap-2
                  text-sm font-semibold text-slate-500
                  transition hover:text-teal-600">

            <i data-lucide="arrow-left"
               class="h-4 w-4"></i>

            Back to My Classrooms
        </a>


        <span class="inline-flex items-center gap-2
                     rounded-full border px-4 py-2
                     text-xs font-bold

                     {{ $isCurrent
                        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                        : 'border-amber-200 bg-amber-50 text-amber-700' }}">

            <span class="h-2 w-2 rounded-full

                         {{ $isCurrent
                            ? 'bg-emerald-500'
                            : 'bg-amber-500' }}">
            </span>

            {{ $isCurrent
                ? 'Active Classroom'
                : 'Archived Classroom' }}

        </span>

    </div>


    <!-- ========================================
         CLASSROOM HEADER
    ========================================= -->

    <section class="overflow-hidden rounded-3xl
                    border border-slate-200
                    bg-white shadow-sm">

        <div class="relative overflow-hidden
                    bg-[#142b61] px-6 py-9
                    text-white md:px-9 md:py-10">

            <!-- BACKGROUND DECORATION -->
            <div class="pointer-events-none
                        absolute -right-14 -top-20
                        h-64 w-64 rounded-full
                        border border-white/10">
            </div>

            <div class="pointer-events-none
                        absolute -right-6 top-4
                        h-44 w-44 rounded-full
                        bg-white/5">
            </div>


            <div class="relative flex flex-col
                        gap-6 sm:flex-row
                        sm:items-center sm:justify-between">

                <div class="flex items-start gap-5">

                    <div class="flex h-16 w-16 shrink-0
                                items-center justify-center
                                rounded-2xl border
                                border-white/15 bg-white/10">

                        <i data-lucide="school"
                           class="h-8 w-8"></i>

                    </div>


                    <div>

                        <span class="mb-3 inline-flex
                                     rounded-full
                                     border border-white/15
                                     bg-white/10 px-3 py-1
                                     text-[11px] font-semibold
                                     uppercase tracking-widest
                                     text-blue-100">

                            Classroom Overview

                        </span>


                        <h1 class="text-2xl font-extrabold
                                   tracking-tight md:text-3xl">

                            {{ $section->name }}

                        </h1>


                        <p class="mt-3 text-sm text-blue-100">

                            Grade {{ $section->grade_level }}

                            <span class="mx-2 text-blue-300">
                                •
                            </span>

                            {{ $section->strand?->code ?? 'N/A' }}

                        </p>

                    </div>

                </div>


                <!-- SCHOOL YEAR -->
                <div class="self-start rounded-2xl
                            border border-white/15
                            bg-white/10 px-5 py-4
                            sm:text-right">

                    <p class="text-xs font-medium
                              text-blue-200">
                        School Year
                    </p>

                    <p class="mt-1 text-xl font-bold">
                        {{ $section->schoolYear?->name ?? 'N/A' }}
                    </p>

                </div>

            </div>

        </div>


        <!-- ====================================
             CLASSROOM DETAILS
        ===================================== -->

        <div class="grid grid-cols-1 gap-6
                    p-6 sm:grid-cols-2
                    lg:grid-cols-4 md:p-8">


            <!-- GRADE LEVEL -->
            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 shrink-0
                            items-center justify-center
                            rounded-xl bg-blue-50
                            text-blue-600">

                    <i data-lucide="graduation-cap"
                       class="h-5 w-5"></i>

                </div>


                <div>

                    <p class="text-xs text-slate-400">
                        Grade Level
                    </p>

                    <p class="mt-1 text-sm font-bold
                              text-slate-800">

                        Grade {{ $section->grade_level }}

                    </p>

                </div>

            </div>


            <!-- STRAND -->
            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 shrink-0
                            items-center justify-center
                            rounded-xl bg-purple-50
                            text-purple-600">

                    <i data-lucide="book-open"
                       class="h-5 w-5"></i>

                </div>


                <div class="min-w-0">

                    <p class="text-xs text-slate-400">
                        Strand
                    </p>

                    <p class="mt-1 break-words
                              text-sm font-bold
                              text-slate-800">

                        {{ $section->strand?->code ?? 'N/A' }}

                    </p>

                </div>

            </div>


            <!-- SCHOOL YEAR -->
            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 shrink-0
                            items-center justify-center
                            rounded-xl bg-amber-50
                            text-amber-600">

                    <i data-lucide="calendar-days"
                       class="h-5 w-5"></i>

                </div>


                <div>

                    <p class="text-xs text-slate-400">
                        School Year
                    </p>

                    <p class="mt-1 text-sm font-bold
                              text-slate-800">

                        {{ $section->schoolYear?->name ?? 'N/A' }}

                    </p>

                </div>

            </div>


            <!-- CLASS ADVISER -->
            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 shrink-0
                            items-center justify-center
                            rounded-xl bg-teal-50
                            text-teal-600">

                    <i data-lucide="user-round-check"
                       class="h-5 w-5"></i>

                </div>


                <div class="min-w-0">

                    <p class="text-xs text-slate-400">
                        Class Adviser
                    </p>

                    <p class="mt-1 break-words
                              text-sm font-bold
                              text-slate-800">

                        {{ $section->adviser?->teacher?->name
                            ?? auth()->user()->name }}

                    </p>

                </div>

            </div>

        </div>

    </section>


    <!-- ========================================
         ARCHIVED CLASSROOM NOTICE
    ========================================= -->

    @unless($isCurrent)

        <div class="flex items-start gap-4
                    rounded-2xl border border-amber-200
                    bg-amber-50 p-5">

            <i data-lucide="archive"
               class="h-6 w-6 shrink-0
                      text-amber-700"></i>

            <div>

                <h3 class="text-sm font-bold
                           text-amber-900">

                    Archived Classroom

                </h3>

                <p class="mt-1 text-sm leading-6
                          text-amber-800">

                    This classroom is no longer active.
                    Existing records remain available
                    for viewing. New uploads and record
                    modifications should be restricted.

                </p>

            </div>

        </div>

    @endunless


    <!-- ========================================
         CLASSROOM MANAGEMENT
    ========================================= -->

    <section>

        <!-- SECTION HEADING -->
        <div class="mb-6 flex flex-col gap-4
                    sm:flex-row sm:items-center
                    sm:justify-between">

            <div>

                <div class="mb-2 flex items-center gap-2">

                    <span class="h-6 w-1
                                 rounded-full bg-teal-500">
                    </span>

                    <p class="text-xs font-bold
                              uppercase tracking-widest
                              text-teal-700">

                        Academic Tools

                    </p>

                </div>


                <h2 class="text-2xl font-extrabold
                           tracking-tight text-slate-900">

                    Classroom Management

                </h2>


                <p class="mt-2 text-sm text-slate-500">

                    Select a module to manage learners,
                    Class Records, Master Sheets,
                    and school requirements.

                </p>

            </div>


            <!-- MODULE COUNTER -->
            <div class="inline-flex items-center gap-2
                        self-start rounded-xl border
                        border-slate-200 bg-white
                        px-4 py-3 shadow-sm">

                <span class="h-2.5 w-2.5
                             rounded-full bg-teal-500">
                </span>

                <span class="text-xs font-semibold
                             text-slate-700">

                    {{ $availableCount }}
                    of {{ count($modules) }}
                    Modules Accessible

                </span>

            </div>

        </div>


        <!-- ====================================
             SIX MODULE CARDS
        ===================================== -->

        <div class="grid grid-cols-1 gap-5
                    md:grid-cols-2 xl:grid-cols-3">


            @foreach($modules as $module)

                <article class="group relative flex
                                min-h-80 flex-col
                                overflow-hidden rounded-2xl
                                border p-6 transition-all
                                duration-300
                                hover:-translate-y-1
                                hover:shadow-lg

                                {{ $module['available']
                                    ? 'border-[#142b61] bg-[#142b61] text-white shadow-md'
                                    : 'border-slate-200 bg-white text-slate-800 shadow-sm hover:border-slate-300' }}">

                    <div class="relative z-10
                                flex h-full flex-col">

                        <!-- TOP ROW -->
                        <div class="flex items-start
                                    justify-between gap-3">

                            <!-- ICON -->
                            <div class="flex h-14 w-14 shrink-0
                                        items-center justify-center
                                        rounded-2xl

                                        {{ $module['available']
                                            ? 'border border-white/15 bg-white/10 text-white'
                                            : 'border border-slate-100 bg-slate-50 text-slate-500' }}">

                                <i data-lucide="{{ $module['icon'] }}"
                                   class="h-7 w-7"></i>

                            </div>


                            <!-- BADGE -->
                            @if($module['available'])

                                <span class="inline-flex
                                             items-center gap-2
                                             rounded-full
                                             border border-emerald-300/20
                                             bg-emerald-400/15
                                             px-3 py-1.5
                                             text-[11px] font-bold
                                             text-emerald-200">

                                    <span class="h-2 w-2
                                                 rounded-full
                                                 bg-emerald-400">
                                    </span>

                                    Accessible

                                </span>

                            @else

                                <span class="inline-flex
                                             items-center
                                             rounded-full
                                             bg-slate-100
                                             px-3 py-1.5
                                             text-[11px]
                                             font-semibold
                                             text-slate-500">

                                    Coming Soon

                                </span>

                            @endif

                        </div>


                        <!-- MODULE DETAILS -->
                        <div class="mt-6 flex-1">

                            <p class="text-[11px] font-bold
                                      uppercase tracking-widest

                                      {{ $module['available']
                                          ? 'text-blue-200'
                                          : 'text-slate-400' }}">

                                Module {{ $module['number'] }}

                            </p>


                            <h3 class="mt-2 text-xl font-bold
                                       tracking-tight

                                       {{ $module['available']
                                           ? 'text-white'
                                           : 'text-slate-900' }}">

                                {{ $module['title'] }}

                            </h3>


                            <p class="mt-3 text-sm leading-6

                                      {{ $module['available']
                                          ? 'text-blue-100'
                                          : 'text-slate-500' }}">

                                {{ $module['description'] }}

                            </p>

                        </div>


                        <!-- ACTION AREA -->
                        <div class="mt-6 border-t pt-5

                                    {{ $module['available']
                                        ? 'border-white/15'
                                        : 'border-slate-100' }}">


                            @if($module['available'] && $module['route'])

                                <a href="{{ route($module['route'], $section) }}"
                                   class="flex w-full items-center
                                          justify-between gap-3
                                          rounded-xl bg-[#0d9488]
                                          px-4 py-3.5 text-sm
                                          font-bold text-white
                                          shadow-sm transition
                                          hover:bg-[#0f766e]">

                                    <span class="flex items-center gap-2">

                                        @if($module['key'] === 'sheets')

                                            <i data-lucide="table-2"
                                               class="h-5 w-5"></i>

                                        @elseif($module['key'] === 'records')

                                            <i data-lucide="files"
                                               class="h-5 w-5"></i>

                                        @elseif($module['key'] === 'students')

                                            <i data-lucide="users-round"
                                               class="h-5 w-5"></i>

                                        @elseif($module['key'] === 'submissions')

                                            <i data-lucide="clipboard-check"
                                               class="h-5 w-5"></i>

                                        @endif


                                        {{ $isCurrent
                                            ? $module['action']
                                            : $module['archived_action'] }}

                                    </span>


                                    <i data-lucide="arrow-right"
                                       class="h-5 w-5 shrink-0"></i>

                                </a>

                            @else

                                <div class="flex w-full
                                            cursor-not-allowed
                                            items-center justify-center
                                            gap-2 rounded-xl
                                            border border-slate-200
                                            bg-slate-50 px-4 py-3.5
                                            text-sm font-semibold
                                            text-slate-400">

                                    <i data-lucide="lock-keyhole"
                                       class="h-4 w-4"></i>

                                    Coming Soon

                                </div>

                            @endif

                        </div>

                    </div>

                </article>

            @endforeach

        </div>

    </section>


    <!-- ========================================
         INFORMATION NOTICE
    ========================================= -->


</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    if (window.lucide) {
        lucide.createIcons();
    }

});
</script>

@endsection
