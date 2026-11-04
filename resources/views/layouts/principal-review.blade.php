
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Principal Portal | SHS Forms')
    </title>

    <!-- TAILWIND CSS V4 -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- GOOGLE FONT -->
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

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            color: #dbeafe;
            transition: all 0.2s ease;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.10);
            color: #ffffff;
        }

        .nav-active {
            background: #0d9488 !important;
            color: #ffffff !important;
        }
    </style>

    @stack('styles')

</head>


<body class="min-h-screen bg-[#f5f7fb] text-slate-900">

@php
    $isDashboard = request()->routeIs(
        'principal.dashboard'
    );

    $isSubmissionSettings = request()->routeIs(
        'principal.submissions.index',
        'principal.submissions.update'
    );

    $isReviewIndex = request()->routeIs(
        'principal.submissions.review.index'
    );

    $isApproved = $isReviewIndex
        && request()->query('status') === 'approved';

    $isReturned = $isReviewIndex
        && request()->query('status') === 'returned';

    $isReview = request()->routeIs(
        'principal.submissions.review.*'
    ) && !$isApproved && !$isReturned;

    $isAccountSettings = request()->routeIs(
        'principal.settings',
        'principal.settings.*'
    );
@endphp


<!-- ==========================================
     MOBILE SIDEBAR OVERLAY
=========================================== -->

<div id="sidebarOverlay"
     class="fixed inset-0 z-40 hidden bg-slate-950/60 lg:hidden">
</div>



<!-- ==========================================
     PRINCIPAL SIDEBAR
=========================================== -->

<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50
              flex w-72 -translate-x-full
              flex-col bg-[#142b61]
              text-white shadow-xl
              transition-transform duration-300
              lg:translate-x-0">


    <!-- BRAND -->
    <div class="flex h-20 shrink-0 items-center gap-3
                border-b border-white/10 px-6">

        <div class="flex h-11 w-11 items-center justify-center
                    rounded-xl bg-white/10">

            <i data-lucide="graduation-cap"
               class="h-6 w-6"></i>

        </div>

        <div class="flex-1">

            <h1 class="text-lg font-bold">
                SHS Forms
            </h1>

            <p class="text-xs text-blue-200">
                Principal Portal
            </p>

        </div>

        <button type="button"
                id="closeSidebar"
                aria-label="Close sidebar"
                class="cursor-pointer lg:hidden">

            <i data-lucide="x"
               class="h-5 w-5"></i>

        </button>

    </div>



    <!-- ======================================
         SIDEBAR NAVIGATION
    ======================================= -->

    <nav class="min-h-0 flex-1 space-y-7
                overflow-y-auto px-4 py-6"
         aria-label="Principal navigation">


        <!-- OVERVIEW -->
        <div>

            <p class="mb-3 px-4 text-[10px] font-bold
                      uppercase tracking-widest text-blue-300/70">
                Overview
            </p>

            <a href="{{ route('principal.dashboard') }}"
               class="nav-item {{ $isDashboard ? 'nav-active' : '' }}"
               @if($isDashboard) aria-current="page" @endif>

                <i data-lucide="layout-dashboard"
                   class="h-5 w-5"></i>

                Dashboard

            </a>

        </div>



        <!-- SUBMISSION MANAGEMENT -->
        <div>

            <p class="mb-3 px-4 text-[10px] font-bold
                      uppercase tracking-widest text-blue-300/70">
                Submission Management
            </p>


            <div class="space-y-1">

                <!-- SUBMISSION SETTINGS -->
                <a href="{{ route('principal.submissions.index') }}"
                   class="nav-item {{ $isSubmissionSettings ? 'nav-active' : '' }}"
                   @if($isSubmissionSettings) aria-current="page" @endif>

                    <i data-lucide="calendar-cog"
                       class="h-5 w-5"></i>

                    Submission Settings

                </a>


                <!-- REVIEW SUBMISSIONS -->
                <a href="{{ route('principal.submissions.review.index') }}"
                   class="nav-item {{ $isReview ? 'nav-active' : '' }}"
                   @if($isReview) aria-current="page" @endif>

                    <i data-lucide="inbox"
                       class="h-5 w-5"></i>

                    Review Submissions

                </a>


                <!-- APPROVED SUBMISSIONS -->
                <a href="{{ route('principal.submissions.review.index', [
                        'status' => 'approved'
                    ]) }}"
                   class="nav-item {{ $isApproved ? 'nav-active' : '' }}"
                   @if($isApproved) aria-current="page" @endif>

                    <i data-lucide="check-circle-2"
                       class="h-5 w-5"></i>

                    Approved Submissions

                </a>


                <!-- RETURNED SUBMISSIONS -->
                <a href="{{ route('principal.submissions.review.index', [
                        'status' => 'returned'
                    ]) }}"
                   class="nav-item {{ $isReturned ? 'nav-active' : '' }}"
                   @if($isReturned) aria-current="page" @endif>

                    <i data-lucide="rotate-ccw"
                       class="h-5 w-5"></i>

                    Returned Submissions

                </a>

            </div>

        </div>



        <!-- MONITORING -->
        <div>

            <p class="mb-3 px-4 text-[10px] font-bold
                      uppercase tracking-widest text-blue-300/70">
                Monitoring
            </p>

            <div class="space-y-1">

                <a href="{{ route('principal.dashboard') }}#teacher-progress"
                   class="nav-item">

                    <i data-lucide="chart-no-axes-column"
                       class="h-5 w-5"></i>

                    Teacher Progress

                </a>

                <a href="{{ route('principal.dashboard') }}#missing"
                   class="nav-item">

                    <i data-lucide="clipboard-list"
                       class="h-5 w-5"></i>

                    Missing Requirements

                </a>

                <a href="{{ route('principal.dashboard') }}#reports"
                   class="nav-item">

                    <i data-lucide="file-bar-chart"
                       class="h-5 w-5"></i>

                    Monitoring Reports

                </a>

            </div>

        </div>



        <!-- GENERAL -->
        <div>

            <p class="mb-3 px-4 text-[10px] font-bold
                      uppercase tracking-widest text-blue-300/70">
                General
            </p>

            <a href="{{ route('principal.settings') }}"
               class="nav-item {{ $isAccountSettings ? 'nav-active' : '' }}"
               @if($isAccountSettings) aria-current="page" @endif>

                <i data-lucide="settings"
                   class="h-5 w-5"></i>

                Account Settings

            </a>

        </div>

    </nav>



    <!-- ======================================
         PRINCIPAL PROFILE
    ======================================= -->

    <div class="shrink-0 border-t border-white/10 p-4">

        <div class="flex items-center gap-3
                    rounded-xl bg-white/10 p-3">

            <div class="flex h-10 w-10 shrink-0
                        items-center justify-center
                        rounded-full bg-blue-300
                        font-bold text-blue-950">

                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}

            </div>


            <div class="min-w-0 flex-1">

                <p class="truncate text-sm font-semibold">
                    {{ auth()->user()->name }}
                </p>

                <p class="text-xs text-blue-200">
                    Principal Account
                </p>

            </div>

            <i data-lucide="badge-check"
               class="h-4 w-4 text-blue-200"></i>

        </div>


        <!-- LOGOUT -->
        <form action="{{ route('logout') }}"
              method="POST"
              class="mt-3">

            @csrf

            <button type="submit"
                    class="flex w-full cursor-pointer
                           items-center justify-center gap-3
                           rounded-xl bg-red-600 px-4 py-3
                           text-sm font-bold text-white
                           hover:bg-red-700">

                <i data-lucide="log-out"
                   class="h-5 w-5"></i>

                Logout Account

            </button>

        </form>

    </div>

</aside>



<!-- ==========================================
     MAIN CONTENT AREA
=========================================== -->

<div class="min-h-screen lg:pl-72">


    <!-- ======================================
         TOP HEADER
    ======================================= -->

    <header class="sticky top-0 z-30 flex h-20
                   items-center gap-4 border-b
                   border-slate-200 bg-white
                   px-5 shadow-sm md:px-8">


        <!-- MOBILE MENU -->
        <button type="button"
                id="openSidebar"
                aria-label="Open sidebar"
                class="cursor-pointer rounded-xl
                       border border-slate-200 p-2 lg:hidden">

            <i data-lucide="menu"
               class="h-5 w-5"></i>

        </button>


        <!-- PAGE TITLE -->
        <div class="min-w-0 flex-1">

            <p class="text-xs font-bold uppercase
                      tracking-widest text-teal-700">

                Principal Portal

            </p>

            <h2 class="mt-1 truncate text-lg font-extrabold
                       text-[#142b61]">

                @yield('heading', 'Principal Dashboard')

            </h2>

        </div>


        <!-- PRINCIPAL INFO -->
        <div class="hidden text-right sm:block">

            <p class="text-sm font-semibold text-slate-800">
                {{ auth()->user()->name }}
            </p>

            <p class="text-xs text-slate-500">
                Principal
            </p>

        </div>

    </header>



    <!-- ======================================
         PAGE CONTENT
    ======================================= -->

    <main class="mx-auto max-w-7xl space-y-6 p-5 md:p-8">


        <!-- SUCCESS MESSAGE -->
        @if(session('success'))

            <div role="status"
                 class="rounded-xl border border-emerald-200
                        bg-emerald-50 px-5 py-4
                        text-sm font-semibold text-emerald-800">

                {{ session('success') }}

            </div>

        @endif


        <!-- ERROR MESSAGE -->
        @if(session('error'))

            <div role="alert"
                 class="rounded-xl border border-red-200
                        bg-red-50 px-5 py-4
                        text-sm font-semibold text-red-800">

                {{ session('error') }}

            </div>

        @endif


        <!-- VALIDATION ERRORS -->
        @if($errors->any())

            <div role="alert"
                 class="rounded-xl border border-red-200
                        bg-red-50 p-5">

                <h3 class="text-sm font-bold text-red-800">
                    Please correct the following:
                </h3>

                <ul class="mt-3 list-disc space-y-2 pl-5
                           text-sm text-red-700">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif



        <!-- ==================================
             CHILD BLADE CONTENT
        =================================== -->

        @yield('content')


    </main>

</div>



<!-- ==========================================
     JAVASCRIPT
=========================================== -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ======================================
    // LUCIDE ICONS
    // ======================================

    if (window.lucide) {
        lucide.createIcons();
    }


    // ======================================
    // MOBILE SIDEBAR
    // ======================================

    const sidebar = document.getElementById('sidebar');

    const overlay = document.getElementById('sidebarOverlay');

    const openButton = document.getElementById('openSidebar');

    const closeButton = document.getElementById('closeSidebar');


    function openSidebar() {

        sidebar.classList.remove('-translate-x-full');

        overlay.classList.remove('hidden');

        document.body.classList.add('overflow-hidden');

    }


    function closeSidebar() {

        sidebar.classList.add('-translate-x-full');

        overlay.classList.add('hidden');

        document.body.classList.remove('overflow-hidden');

    }


    openButton?.addEventListener(
        'click',
        openSidebar
    );

    closeButton?.addEventListener(
        'click',
        closeSidebar
    );

    overlay?.addEventListener(
        'click',
        closeSidebar
    );


    // ======================================
    // CLOSE WITH ESCAPE KEY
    // ======================================

    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {
            closeSidebar();
        }

    });

});
</script>


<!-- PAGE-SPECIFIC JAVASCRIPT -->
@stack('scripts')

</body>
</html>
