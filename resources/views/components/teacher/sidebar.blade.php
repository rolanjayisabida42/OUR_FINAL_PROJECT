
@php

    $teacher = auth()->user();

    $teacherName = trim(
        (string) ($teacher->name ?? 'Teacher')
    );

    // Generate teacher initials.
    $initials = collect(
        preg_split('/\s+/u', $teacherName)
    )
        ->filter()
        ->take(2)
        ->map(fn ($name) => mb_strtoupper(
            mb_substr($name, 0, 1)
        ))
        ->implode('');

    // Active navigation.
    $isDashboard = request()->routeIs('teacher.dashboard');

    $isClassrooms = request()->routeIs(
        'teacher.classrooms.*'
    );

    $isSettings = request()->routeIs(
        'teacher.settings*'
    );

@endphp


<!-- ==========================================
     TEACHER SIDEBAR
=========================================== -->

<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50
              flex w-70 -translate-x-full
              flex-col bg-[#142b61] text-white
              shadow-xl transition-transform
              duration-300 lg:translate-x-0">


    <!-- BRAND -->

    <div class="flex h-20 shrink-0 items-center gap-3
                border-b border-white/10 px-6">

        <div class="flex h-11 w-11 items-center
                    justify-center rounded-xl bg-white/10">

            <i data-lucide="graduation-cap"
               class="h-6 w-6 text-teal-300"></i>

        </div>

        <div class="min-w-0 flex-1">

            <h1 class="text-lg font-bold">
                SHS Forms
            </h1>

            <p class="text-xs text-blue-200">
                Teacher Portal
            </p>

        </div>

        <button id="closeSidebar"
                type="button"
                aria-label="Close sidebar"
                class="text-blue-200 lg:hidden">

            <i data-lucide="x" class="h-5 w-5"></i>

        </button>

    </div>


    <!-- ======================================
         NAVIGATION
    ======================================= -->

    <nav class="min-h-0 flex-1 space-y-7
                overflow-y-auto px-4 py-6">


        <!-- OVERVIEW -->

        <div>

            <p class="mb-3 px-4 text-[10px]
                      font-bold uppercase tracking-widest
                      text-blue-300/70">
                Overview
            </p>

            <a href="{{ route('teacher.dashboard') }}"
               @if($isDashboard)
                   aria-current="page"
               @endif
               class="group flex items-center gap-3
                      rounded-xl px-4 py-3.5
                      text-sm font-semibold transition-all

                      {{ $isDashboard
                          ? 'bg-teal-500/20 text-white ring-1 ring-teal-400/20'
                          : 'text-blue-100/80 hover:bg-white/10 hover:text-white' }}">

                <i data-lucide="layout-dashboard"
                   class="h-5 w-5 shrink-0"></i>

                <span class="flex-1">
                    Dashboard
                </span>

                @if($isDashboard)
                    <span class="h-2 w-2 rounded-full
                                 bg-teal-300"></span>
                @endif

            </a>

        </div>


        <!-- CLASSROOM MANAGEMENT -->

        <div>

            <p class="mb-3 px-4 text-[10px]
                      font-bold uppercase tracking-widest
                      text-blue-300/70">
                Classroom Management
            </p>

            <a href="{{ route('teacher.classrooms.index') }}"
               @if($isClassrooms)
                   aria-current="page"
               @endif
               class="group flex items-center gap-3
                      rounded-xl px-4 py-3.5
                      text-sm font-semibold transition-all

                      {{ $isClassrooms
                          ? 'bg-teal-500/20 text-white ring-1 ring-teal-400/20'
                          : 'text-blue-100/80 hover:bg-white/10 hover:text-white' }}">

                <i data-lucide="school"
                   class="h-5 w-5 shrink-0"></i>

                <span class="flex-1">
                    My Classrooms
                </span>

                @if($isClassrooms)
                    <span class="h-2 w-2 rounded-full
                                 bg-teal-300"></span>
                @endif

            </a>

        </div>


        <!-- GENERAL -->

        <div>

            <p class="mb-3 px-4 text-[10px]
                      font-bold uppercase tracking-widest
                      text-blue-300/70">
                General
            </p>

            <a href="{{ route('teacher.settings') }}"
               @if($isSettings)
                   aria-current="page"
               @endif
               class="group flex items-center gap-3
                      rounded-xl px-4 py-3.5
                      text-sm font-semibold transition-all

                      {{ $isSettings
                          ? 'bg-teal-500/20 text-white ring-1 ring-teal-400/20'
                          : 'text-blue-100/80 hover:bg-white/10 hover:text-white' }}">

                <i data-lucide="settings"
                   class="h-5 w-5 shrink-0"></i>

                <span class="flex-1">
                    Account Settings
                </span>

                @if($isSettings)
                    <span class="h-2 w-2 rounded-full
                                 bg-teal-300"></span>
                @endif

            </a>

        </div>

    </nav>


    <!-- ======================================
         TEACHER PROFILE & LOGOUT
    ======================================= -->

    <div class="shrink-0 border-t border-white/10
                bg-[#102653] p-4">


        <!-- PROFILE -->

        <div class="flex items-center gap-3
                    rounded-xl border border-white/10
                    bg-white/5 p-3">

            <div class="flex h-11 w-11 shrink-0
                        items-center justify-center
                        rounded-xl bg-teal-400/20
                        text-sm font-bold text-teal-100">

                {{ $initials ?: 'TC' }}

            </div>

            <div class="min-w-0 flex-1">

                <p class="truncate text-sm font-semibold">
                    {{ $teacherName }}
                </p>

                <p class="mt-1 text-xs text-blue-200">
                    SHS Teacher
                </p>

            </div>

            <i data-lucide="badge-check"
               class="h-4 w-4 text-teal-300"></i>

        </div>


        <!-- LOGOUT -->

        <form action="{{ route('logout') }}"
              method="POST"
              class="mt-3">

            @csrf

            <button type="submit"
                    class="flex w-full items-center
                           justify-center gap-3
                           rounded-xl bg-red-600
                           px-4 py-3 text-sm font-semibold
                           text-white transition-all
                           hover:bg-red-700">

                <i data-lucide="log-out"
                   class="h-5 w-5"></i>

                <span>Logout Account</span>

            </button>

        </form>

    </div>

</aside>
