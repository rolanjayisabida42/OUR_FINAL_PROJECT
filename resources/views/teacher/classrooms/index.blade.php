
@extends('layouts.teacher')

@section('title', 'My Classrooms')
@section('breadcrumb', 'My Classrooms')
@section('page_heading', 'My Classrooms')

@section('content')



<style>
    /* ==========================================
       GLOBAL
    ========================================== */
    body {
        font-family: 'Inter', sans-serif;
    }

    html {
        scroll-behavior: smooth;
    }

    /* ==========================================
       SIDEBAR NAVIGATION
    ========================================== */
    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 12px;
        color: #dbeafe;
        font-size: 14px;
        font-weight: 500;
        transition: all .2s;
        text-decoration: none;
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

    /* ==========================================
       FIX COLLAPSING ICONS
    ========================================== */
    svg.lucide {
        flex-shrink: 0;
    }

    .nav-item svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    /* ==========================================
       PRIMARY BUTTON - TEAL
    ========================================== */
    .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;

        background-color: #0d9488;
        color: #ffffff;

        padding: 12px 18px;
        border-radius: 12px;
        border: none;

        font-size: 14px;
        font-weight: 600;
        text-decoration: none;

        cursor: pointer;
        transition: all .2s ease;
    }

    .btn-primary:hover {
        background-color: #0f766e;
        color: #ffffff;
    }

    .btn-primary:focus-visible {
        outline: 3px solid rgba(13,148,136,.25);
        outline-offset: 2px;
    }

    .btn-primary svg {
        flex-shrink: 0;
    }

    /* ==========================================
       FORM INPUTS
    ========================================== */
    .form-input {
        display: block;
        width: 100%;

        padding: 11px 14px;

        background: #ffffff;
        color: #1e293b;

        border: 1px solid #e2e8f0;
        border-radius: 12px;

        font-size: 14px;
        outline: none;

        transition: all .2s;
    }

    .form-input:focus {
        border-color: #0d9488;
        box-shadow: 0 0 0 3px rgba(13,148,136,.12);
    }

    /* ==========================================
       PREVENT LONG TEXT FROM SQUASHING ICONS
    ========================================== */
    .nav-item span {
        min-width: 0;
    }
</style>


    <!-- PAGE HEADING -->
    <div class="mb-7 flex flex-col gap-4
                sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-extrabold tracking-tight
                       text-slate-900 md:text-3xl">

                My Classrooms
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                View and manage the sections assigned to you
                by the administrator.
            </p>
        </div>

        <div class="inline-flex items-center gap-2 self-start
                    rounded-xl border border-slate-200
                    bg-white px-4 py-3">

            <i data-lucide="calendar-days"
               class="h-5 w-5 text-teal-600"></i>

            <div>
                <p class="text-[11px] text-slate-400">
                    Active School Year
                </p>

                <p class="text-sm font-bold text-slate-800">
                    {{ $activeYear?->name ?? 'Not configured' }}
                </p>
            </div>
        </div>
    </div>


    <!-- STATISTICS -->
    <div class="mb-7 grid grid-cols-1 gap-5 sm:grid-cols-3">

        <!-- TOTAL -->
        <div class="rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div class="flex h-12 w-12 items-center
                            justify-center rounded-xl
                            bg-blue-50 text-blue-600">

                    <i data-lucide="school" class="h-6 w-6"></i>
                </div>

                <span class="text-xs text-slate-400">
                    All Assignments
                </span>
            </div>

            <p class="mt-5 text-3xl font-extrabold">
                {{ $stats['total'] }}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Total Classrooms
            </p>
        </div>


        <!-- CURRENT -->
        <div class="rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div class="flex h-12 w-12 items-center
                            justify-center rounded-xl
                            bg-emerald-50 text-emerald-600">

                    <i data-lucide="check-circle" class="h-6 w-6"></i>
                </div>

                <span class="text-xs text-slate-400">
                    Current School Year
                </span>
            </div>

            <p class="mt-5 text-3xl font-extrabold">
                {{ $stats['current'] }}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Current Classrooms
            </p>
        </div>


        <!-- ARCHIVED -->
        <div class="rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div class="flex h-12 w-12 items-center
                            justify-center rounded-xl
                            bg-amber-50 text-amber-600">

                    <i data-lucide="archive" class="h-6 w-6"></i>
                </div>

                <span class="text-xs text-slate-400">
                    Previous / Inactive
                </span>
            </div>

            <p class="mt-5 text-3xl font-extrabold">
                {{ $stats['archived'] }}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Archived Classrooms
            </p>
        </div>
    </div>


    <!-- FILTERS -->
    <section class="mb-7 rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm">

        <form action="{{ route('teacher.classrooms.index') }}"
              method="GET"
              class="grid grid-cols-1 gap-3 md:grid-cols-4">

            <div class="md:col-span-2">
                <label for="search" class="mb-2 block
                           text-xs font-semibold text-slate-600">
                    Search Classroom
                </label>

                <input id="search"
                       type="search"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search section or strand..."
                       class="form-input">
            </div>

            <div>
                <label for="grade" class="mb-2 block
                           text-xs font-semibold text-slate-600">
                    Grade Level
                </label>

                <select id="grade" name="grade_level"
                        class="form-input">

                    <option value="">All Grades</option>

                    <option value="11"
                        @selected(request('grade_level') === '11')>
                        Grade 11
                    </option>

                    <option value="12"
                        @selected(request('grade_level') === '12')>
                        Grade 12
                    </option>
                </select>
            </div>

            <div>
                <label for="view" class="mb-2 block
                           text-xs font-semibold text-slate-600">
                    Classroom Status
                </label>

                <select id="view" name="view"
                        class="form-input">

                    <option value="all"
                        @selected($view === 'all')>
                        All Classrooms
                    </option>

                    <option value="current"
                        @selected($view === 'current')>
                        Current
                    </option>

                    <option value="archived"
                        @selected($view === 'archived')>
                        Archived
                    </option>
                </select>
            </div>

            <div class="flex items-center gap-3 md:col-span-4">

                <button type="submit" class="btn-primary
                               cursor-pointer">

                    <i data-lucide="search"
                       class="h-4 w-4"></i>
                    Apply Filters
                </button>

                <a href="{{ route('teacher.classrooms.index') }}"
                   class="rounded-xl border border-slate-200
                          px-4 py-3 text-sm font-semibold
                          text-slate-600 hover:bg-slate-50">

                    Clear Filters
                </a>
            </div>
        </form>
    </section>


    <!-- SECTION LIST HEADER -->
    <div class="mb-5 flex items-center justify-between gap-3">

        <div>
            <h3 class="text-lg font-bold text-slate-900">
                Assigned Sections
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Only sections assigned to your account are displayed.
            </p>
        </div>

        <span class="rounded-full bg-slate-100 px-3 py-1.5
                     text-xs font-semibold text-slate-600">

            {{ $sections->total() }} Results
        </span>
    </div>


    <!-- CLASSROOM CARDS -->
    <div class="grid grid-cols-1 gap-5
                md:grid-cols-2 xl:grid-cols-3">

        @forelse($sections as $section)

            @php
                $isCurrent =
                    $section->is_active &&
                    $section->schoolYear?->is_active &&
                    $section->strand?->is_active;
            @endphp

            <article class="overflow-hidden rounded-2xl
                            border border-slate-200
                            bg-white shadow-sm transition
                            hover:-translate-y-1 hover:shadow-md">

                <!-- ACCENT -->
                <div class="h-1.5 {{ $isCurrent
                    ? 'bg-teal-500'
                    : 'bg-slate-300' }}">
                </div>

                <div class="p-6">

                    <!-- STATUS -->
                    <div class="flex items-start justify-between">

                        <div class="flex h-12 w-12 items-center
                                    justify-center rounded-xl
                                    bg-blue-50 text-blue-600">

                            <i data-lucide="school"
                               class="h-6 w-6"></i>
                        </div>

                        <span class="rounded-full px-3 py-1
                                     text-xs font-semibold
                                     {{ $isCurrent
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'bg-slate-100 text-slate-500' }}">

                            {{ $isCurrent ? 'Current' : 'Archived' }}
                        </span>
                    </div>

                    <!-- SECTION NAME -->
                    <h4 class="mt-5 text-xl font-bold
                               text-slate-900">

                        {{ $section->name }}
                    </h4>

                    <p class="mt-1 text-sm text-slate-500">
                        Grade {{ $section->grade_level }}
                        · {{ $section->strand?->code ?? 'N/A' }}
                    </p>

                    <!-- DETAILS -->
                    <div class="mt-5 space-y-3
                                border-t border-slate-100 pt-5">

                        <div class="flex items-center gap-3
                                    text-sm text-slate-600">

                            <i data-lucide="calendar-days"
                               class="h-4 w-4 text-slate-400"></i>

                            SY {{ $section->schoolYear?->name ?? 'N/A' }}
                        </div>

                        <div class="flex items-center gap-3
                                    text-sm text-slate-600">

                            <i data-lucide="graduation-cap"
                               class="h-4 w-4 text-slate-400"></i>

                            {{ $section->strand?->name ?? 'N/A' }}
                        </div>

                        <div class="flex items-center gap-3
                                    text-sm text-slate-600">

                            <i data-lucide="user-round-check"
                               class="h-4 w-4 text-slate-400"></i>

                            Assigned Adviser
                        </div>
                    </div>

                    <!-- ACTION -->
                    <a href="{{ route('teacher.classrooms.show', $section) }}"
                       class="btn-primary mt-6 w-full">

                        <i data-lucide="folder-open"
                           class="h-4 w-4"></i>

                        {{ $isCurrent
                            ? 'Manage Classroom'
                            : 'View Classroom' }}
                    </a>
                </div>
            </article>

        @empty

            <div class="rounded-2xl border border-dashed
                        border-slate-300 bg-white px-6 py-16
                        text-center md:col-span-2 xl:col-span-3">

                <div class="mx-auto flex h-16 w-16
                            items-center justify-center
                            rounded-2xl bg-slate-100
                            text-slate-400">

                    <i data-lucide="school"
                       class="h-8 w-8"></i>
                </div>

                <h3 class="mt-5 text-lg font-bold
                           text-slate-800">

                    No Classrooms Found
                </h3>

                <p class="mx-auto mt-2 max-w-md
                          text-sm leading-6 text-slate-500">

                    @if($stats['total'] === 0)
                        You don't have any assigned sections yet.
                        Please contact the administrator to assign
                        you to a section.
                    @else
                        No assigned classrooms match your filters.
                        Try clearing your search or filters.
                    @endif
                </p>
            </div>

        @endforelse

    </div>


    <!-- PAGINATION -->
    @if($sections->hasPages())
        <div class="mt-7">
            {{ $sections->links() }}
        </div>
    @endif

@endsection
