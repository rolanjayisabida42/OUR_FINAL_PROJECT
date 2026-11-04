
<!-- SIDEBAR -->
<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50 flex w-70
              -translate-x-full flex-col bg-[#142b61]
              text-white shadow-xl transition-transform
              duration-300 lg:translate-x-0">

    <!-- BRAND -->
    <div class="flex h-20 shrink-0 items-center gap-3
                border-b border-white/10 px-6">

        <div class="flex h-11 w-11 items-center
                    justify-center rounded-xl bg-white/10">

            <i data-lucide="graduation-cap"
               class="h-6 w-6"></i>
        </div>

        <div class="flex-1">
            <h1 class="text-lg font-bold">SHS Forms</h1>
            <p class="text-xs text-blue-200">
                Administration Portal
            </p>
        </div>

        <button id="closeSidebar"
                type="button"
                aria-label="Close sidebar"
                class="text-blue-200 lg:hidden">

            <i data-lucide="x" class="h-5 w-5"></i>
        </button>
    </div>


    <!-- NAVIGATION -->
    <nav class="min-h-0 flex-1 space-y-6
                overflow-y-auto px-4 py-6 text-sm">

        <!-- OVERVIEW -->
        <div>
            <p class="mb-3 px-4 text-[10px]
                      font-bold uppercase tracking-widest
                      text-blue-300/70">
                Overview
            </p>

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 rounded-xl
                      bg-white/15 px-4 py-3 font-semibold
                      text-white">

                <i data-lucide="layout-dashboard"
                   class="h-5 w-5"></i>

                Dashboard
            </a>
        </div>


        <!-- USER MANAGEMENT -->
        <div>
            <p class="mb-3 px-4 text-[10px]
                      font-bold uppercase tracking-widest
                      text-blue-300/70">
                User Management
            </p>

            <div class="space-y-1">

                <a href="{{ route('admin.users.index') }}" class="nav-item">
                    <i data-lucide="users"
                       class="h-5 w-5"></i>
                    Users Accounts
                </a>

            </div>
        </div>


        <!-- ACADEMIC MANAGEMENT -->
        <div>
            <p class="mb-3 px-4 text-[10px]
                      font-bold uppercase tracking-widest
                      text-blue-300/70">
                Academic Management
            </p>

            <div class="space-y-1">

                <a href="{{ route('admin.advisers.index') }}" class="nav-item">
                    <i data-lucide="book-open-check"
                       class="h-5 w-5"></i>
                    Teacher Assignments
                </a>

                <a href="{{ route('admin.school-years.index') }}" class="nav-item">
                    <i data-lucide="calendar-days"
                       class="h-5 w-5"></i>
                    School Years & Periods
                </a>

                
                <a href="{{ route('admin.sections.index') }}" class="nav-item">
                    <i data-lucide="calendar-days"
                       class="h-5 w-5"></i>
                    Strands & Sections
                </a>
                

            </div>
        </div>


        <!-- DOCUMENT MANAGEMENT -->
        <div>
            <p class="mb-3 px-4 text-[10px]
                      font-bold uppercase tracking-widest
                      text-blue-300/70">
                Document Management
            </p>

            <div class="space-y-1">

                <a href="#requirements" class="nav-item">
                    <i data-lucide="clipboard-list"
                       class="h-5 w-5"></i>
                    Requirements
                </a>

                <a href="#templates" class="nav-item">
                    <i data-lucide="file-spreadsheet"
                       class="h-5 w-5"></i>
                    Excel Templates
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

            <a href="{{ route('admin.settings') }}" class="nav-item">
                <i data-lucide="settings"
                   class="h-5 w-5"></i>
                Account Settings
            </a>
        </div>

    </nav>


    <!-- ADMIN PROFILE AND LOGOUT -->
    <div class="shrink-0 border-t border-white/10 p-4">

        <!-- ADMIN PROFILE -->
        <div class="flex items-center gap-3
                    rounded-xl bg-white/10 p-3">

            <div class="flex h-10 w-10 shrink-0
                        items-center justify-center
                        rounded-full bg-blue-300
                        font-bold text-blue-950">
                AD
            </div>

            <div class="min-w-0 flex-1">

                <p class="truncate text-sm font-semibold">
                    {{ auth()->user()->name }}
                </p>

                <p class="text-xs text-blue-200">
                    System Admin
                </p>
            </div>

            <i data-lucide="shield-check"
               class="h-4 w-4 text-blue-200"></i>
        </div>


        <!-- LOGOUT BUTTON -->
        <form action="{{ route('logout') }}"
              method="POST"
              class="mt-3">

            @csrf

            <button type="submit"
                    class="flex w-full cursor-pointer
                           items-center justify-center gap-3
                           rounded-xl bg-red-600 px-4 py-3
                           text-sm font-semibold text-white
                           transition-all duration-200
                           hover:bg-red-700">

                <i data-lucide="log-out"
                   class="h-5 w-5"></i>

                <span>Logout Account</span>
            </button>

        </form>

    </div>

</aside>
