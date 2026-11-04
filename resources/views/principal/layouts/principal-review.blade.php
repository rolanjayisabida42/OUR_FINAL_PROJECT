
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Principal | SHS Forms')</title>

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

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            color: #dbeafe;
            transition: all .2s;
        }

        .nav-item:hover {
            background: rgba(255,255,255,.10);
            color: white;
        }

        .nav-active {
            background: #0d9488;
            color: white;
        }
    </style>

</head>

<body class="min-h-screen bg-slate-50 text-slate-900">

    <!-- MOBILE OVERLAY -->
    <div id="sidebarOverlay"
         class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden">
    </div>


    <!-- ======================================
         PRINCIPAL SIDEBAR
    ======================================= -->
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

            <div class="flex h-11 w-11 items-center
                        justify-center rounded-xl bg-white/10">

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

            <button id="closeSidebar"
                    type="button"
                    class="lg:hidden"
                    aria-label="Close sidebar">

                <i data-lucide="x"
                   class="h-5 w-5"></i>

            </button>

        </div>


        <!-- NAVIGATION -->
        <nav class="min-h-0 flex-1 space-y-7
                    overflow-y-auto px-4 py-6">

            <!-- OVERVIEW -->
            <div>

                <p class="mb-3 px-4 text-[10px]
                          font-bold uppercase tracking-widest
                          text-blue-300/70">

                    Overview

                </p>

                <a href="{{ route('principal.dashboard') }}"
                   class="nav-item {{ request()->routeIs('principal.dashboard') ? 'nav-active' : '' }}">

                    <i data-lucide="layout-dashboard"
                       class="h-5 w-5"></i>

                    Dashboard

                </a>

            </div>


            <!-- SUBMISSION MANAGEMENT -->
            <div>

                <p class="mb-3 px-4 text-[10px]
                          font-bold uppercase tracking-widest
                          text-blue-300/70">

                    Submission Management

                </p>

                <div class="space-y-1">

                    <!-- SUBMISSION SETTINGS -->
                    <a href="{{ route('principal.submissions.index') }}"
                       class="nav-item {{ request()->routeIs('principal.submissions.index', 'principal.submissions.update') ? 'nav-active' : '' }}">

                        <i data-lucide="calendar-cog"
                           class="h-5 w-5"></i>

                        Submission Settings

                    </a>


                    <!-- REVIEW SUBMISSIONS -->
                    <a href="{{ route('principal.submissions.review.index') }}"
                       class="nav-item {{ request()->routeIs('principal.submissions.review.*') && !in_array(request()->query('status'), ['approved', 'returned']) ? 'nav-active' : '' }}">

                        <i data-lucide="inbox"
                           class="h-5 w-5"></i>

                        Review Submissions

                    </a>


                    <!-- APPROVED -->
                    <a href="{{ route('principal.submissions.review.index', ['status' => 'approved']) }}"
                       class="nav-item {{ request()->routeIs('principal.submissions.review.index') && request()->query('status') === 'approved' ? 'nav-active' : '' }}">

                        <i data-lucide="check-circle-2"
                           class="h-5 w-5"></i>

                        Approved Submissions

                    </a>


                    <!-- RETURNED -->
                    <a href="{{ route('principal.submissions.review.index', ['status' => 'returned']) }}"
                       class="nav-item {{ request()->routeIs('principal.submissions.review.index') && request()->query('status') === 'returned' ? 'nav-active' : '' }}">

                        <i data-lucide="rotate-ccw"
                           class="h-5 w-5"></i>

                        Returned Submissions

                    </a>

                </div>

            </div>


            <!-- GENERAL -->
            <div>

                <p class="mb-3 px-4 text-[10px]
                          font-bold uppercase tracking-widest
                          text-blue-300/70">

                    General

                </p>

                <a href="{{ route('principal.settings') }}"
                   class="nav-item {{ request()->routeIs('principal.settings*') ? 'nav-active' : '' }}">

                    <i data-lucide="settings"
                       class="h-5 w-5"></i>

                    Account Settings

                </a>

            </div>

        </nav>


        <!-- PROFILE -->
        <div class="shrink-0 border-t border-white/10 p-4">

            <div class="flex items-center gap-3
                        rounded-xl bg-white/10 p-3">

                <div class="flex h-10 w-10 items-center
                            justify-center rounded-full
                            bg-blue-300 font-bold text-blue-950">

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
                               px-4 py-3 text-sm font-bold
                               text-white hover:bg-red-700">

                    <i data-lucide="log-out"
                       class="h-5 w-5"></i>

                    Logout Account

                </button>

            </form>

        </div>

    </aside>


    <!-- ======================================
         MAIN PAGE
    ======================================= -->
    <div class="min-h-screen lg:pl-72">

        <!-- TOPBAR -->
        <header class="sticky top-0 z-30 flex h-20
                       items-center gap-4 border-b
                       border-slate-200 bg-white
                       px-5 md:px-8">

            <button id="openSidebar"
                    type="button"
                    class="rounded-xl border
                           border-slate-200 p-2 lg:hidden"
                    aria-label="Open sidebar">

                <i data-lucide="menu"
                   class="h-5 w-5"></i>

            </button>


            <div class="flex-1">

                <p class="text-xs font-semibold
                          uppercase tracking-widest
                          text-teal-700">

                    Principal Portal

                </p>

                <h2 class="mt-1 text-lg font-extrabold
                           text-[#142b61]">

                    @yield('heading', 'Submission Management')

                </h2>

            </div>


            <div class="hidden text-right sm:block">

                <p class="text-sm font-semibold text-slate-800">
                    {{ auth()->user()->name }}
                </p>

                <p class="text-xs text-slate-500">
                    Principal
                </p>

            </div>

        </header>


        <!-- MAIN CONTENT -->
        <main class="mx-auto max-w-7xl space-y-6
                     p-5 md:p-8">

            <!-- SUCCESS ALERT -->
            @if(session('success'))

                <div class="rounded-xl border
                            border-emerald-200
                            bg-emerald-50 px-5 py-4
                            text-sm font-semibold
                            text-emerald-800">

                    {{ session('success') }}

                </div>

            @endif


            <!-- VALIDATION ERRORS -->
            @if($errors->any())

                <div class="rounded-xl border
                            border-red-200 bg-red-50 p-5">

                    <p class="text-sm font-bold text-red-800">
                        Please correct the following:
                    </p>

                    <ul class="mt-2 list-disc space-y-1
                               pl-5 text-sm text-red-700">

                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <!-- REVIEW PAGE CONTENT -->
            @yield('content')

        </main>

    </div>


    <!-- ======================================
         SCRIPTS
    ======================================= -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            if (window.lucide) {
                lucide.createIcons();
            }

            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const openButton = document.getElementById('openSidebar');
            const closeButton = document.getElementById('closeSidebar');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            openButton?.addEventListener('click', openSidebar);
            closeButton?.addEventListener('click', closeSidebar);
            overlay?.addEventListener('click', closeSidebar);

        });
    </script>

    @stack('scripts')

</body>
</html>
