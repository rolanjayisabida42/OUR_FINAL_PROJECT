
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | SHS Forms Management</title>

    <!-- TAILWIND CSS V4 -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- INTER FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect"
          href="https://fonts.gstatic.com" crossorigin>
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
    </style>
</head>

<body class="bg-[#f5f7fb] text-slate-800 antialiased">

    <!-- MOBILE SIDEBAR OVERLAY -->
    <div id="sidebarOverlay"
         class="fixed inset-0 z-40 hidden bg-slate-950/50 lg:hidden">
    </div>

    <!-- SIDEBAR -->
    <x-admin.sidebar />

    <!-- MAIN WRAPPER -->
    <div class="min-h-screen lg:ml-70">

        <!-- HEADER -->
        <header class="sticky top-0 z-30 flex h-20
                       items-center justify-between border-b
                       border-slate-200 bg-white px-5 lg:px-8">

            <div class="flex items-center gap-4">

                <button id="openSidebar"
                        type="button"
                        aria-label="Open sidebar"
                        aria-expanded="false"
                        class="rounded-lg p-2 text-slate-600
                               hover:bg-slate-100 lg:hidden">

                    <i data-lucide="menu"
                       class="h-6 w-6"></i>
                </button>

                <div>
                    <h2 class="text-lg font-bold
                               text-slate-900 sm:text-xl">
                        Admin Dashboard
                    </h2>

                    <p class="hidden text-xs text-slate-500 sm:block">
                        SHS Forms Management System
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4">

                <!-- NOTIFICATIONS -->
                <div class="relative">
                    <button id="notificationBtn"
                            type="button"
                            aria-label="Notifications"
                            aria-expanded="false"
                            class="relative rounded-xl p-2.5
                                   text-slate-500 transition
                                   hover:bg-slate-100">

                        <i data-lucide="bell"
                           class="h-5 w-5"></i>

                        <span class="absolute right-2 top-2
                                     h-2 w-2 rounded-full
                                     bg-red-500"></span>
                    </button>

                    <div id="notificationPanel"
                         class="absolute right-0 top-13 z-50
                                hidden w-72 rounded-xl
                                border border-slate-200
                                bg-white p-4 shadow-xl sm:w-80">

                        <h4 class="font-bold text-slate-900">
                            Notifications
                        </h4>

                        <p class="mt-1 text-xs text-slate-400">
                            Sample notifications
                        </p>

                        <div class="mt-4 border-t
                                    border-slate-100 pt-4">

                            <div class="flex items-start gap-3">

                                <div class="rounded-lg
                                            bg-amber-50 p-2
                                            text-amber-600">

                                    <i data-lucide="clock"
                                       class="h-4 w-4"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-semibold">
                                        Requirements
                                    </p>
                                    <p class="mt-1 text-xs
                                              leading-5 text-slate-500">
                                        Configure the four required
                                        semester documents.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PROFILE -->
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-semibold text-slate-800">
                        Administrator
                    </p>

                    <p class="text-xs text-slate-500">
                        Admin Account
                    </p>
                </div>

                <div class="flex h-10 w-10 items-center
                            justify-center rounded-full
                            bg-blue-100 font-bold text-blue-700">
                    AD
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="mx-auto max-w-7xl space-y-8
                     p-5 lg:p-8">

            <!-- WELCOME BANNER -->
            <section class="relative overflow-hidden
                            rounded-2xl bg-gradient-to-r
                            from-[#142b61] to-[#2857b5]
                            p-7 text-white md:p-9">

                <div class="absolute -right-16 -top-20
                            h-72 w-72 rounded-full bg-white/5">
                </div>

                <div class="absolute -bottom-20 right-40
                            h-48 w-48 rounded-full bg-white/5">
                </div>

                <div class="relative z-10">

                    <span class="inline-flex items-center gap-2
                                 rounded-full bg-white/10
                                 px-3 py-1.5 text-xs
                                 text-blue-100">

                        <i data-lucide="calendar"
                           class="h-3.5 w-3.5"></i>

                        <span id="currentDate">
                            Current Date
                        </span>
                    </span>

                    <h2 class="mt-5 text-2xl font-bold md:text-3xl">
                        Welcome back, Administrator!
                    </h2>

                    <p class="mt-3 max-w-xl text-sm
                              leading-7 text-blue-100">

                        Manage teacher accounts, class assignments,
                        school years, submission requirements,
                        and official Excel templates in one place.

                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">

                        <a href="#assignments"
                           class="inline-flex items-center gap-2
                                  rounded-lg bg-white px-4 py-2.5
                                  text-sm font-semibold text-blue-900
                                  transition hover:bg-blue-50">

                            <i data-lucide="users"
                               class="h-4 w-4"></i>

                            View Assignments
                        </a>

                        <a href="#requirements"
                           class="inline-flex items-center gap-2
                                  rounded-lg border border-white/30
                                  bg-white/10 px-4 py-2.5
                                  text-sm font-semibold
                                  transition hover:bg-white/20">

                            <i data-lucide="clipboard-check"
                               class="h-4 w-4"></i>

                            View Requirements
                        </a>
                    </div>
                </div>
            </section>

            <!-- STATISTICS -->
            <section>

                <div class="mb-5">
                    <h3 class="text-lg font-bold text-slate-900">
                        School Overview
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Overview of your school management records.
                        Sample data only.
                    </p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

                    <!-- TEACHERS -->
                    <div class="rounded-2xl border border-slate-200
                                bg-white p-6 shadow-sm">

                        <div class="flex items-center justify-between">

                            <div class="rounded-xl bg-blue-50 p-3
                                        text-blue-600">
                                <i data-lucide="users"
                                   class="h-6 w-6"></i>
                            </div>

                            <span class="rounded-full bg-blue-50
                                         px-3 py-1 text-xs
                                         font-semibold text-blue-700">
                                Teachers
                            </span>
                        </div>

                        <h4 class="mt-5 text-3xl font-bold
                                   text-slate-900">
                            8
                        </h4>

                        <p class="mt-1 text-sm text-slate-500">
                            Total Teacher Accounts
                        </p>
                    </div>

                    <!-- SECTIONS -->
                    <div class="rounded-2xl border border-slate-200
                                bg-white p-6 shadow-sm">

                        <div class="flex items-center justify-between">

                            <div class="rounded-xl bg-violet-50 p-3
                                        text-violet-600">
                                <i data-lucide="school"
                                   class="h-6 w-6"></i>
                            </div>

                            <span class="rounded-full bg-violet-50
                                         px-3 py-1 text-xs
                                         font-semibold text-violet-700">
                                Grade 11 & 12
                            </span>
                        </div>

                        <h4 class="mt-5 text-3xl font-bold
                                   text-slate-900">
                            12
                        </h4>

                        <p class="mt-1 text-sm text-slate-500">
                            Total Sections
                        </p>
                    </div>

                    <!-- ASSIGNMENTS -->
                    <div class="rounded-2xl border border-slate-200
                                bg-white p-6 shadow-sm">

                        <div class="flex items-center justify-between">

                            <div class="rounded-xl bg-emerald-50 p-3
                                        text-emerald-600">
                                <i data-lucide="book-open-check"
                                   class="h-6 w-6"></i>
                            </div>

                            <span class="rounded-full bg-emerald-50
                                         px-3 py-1 text-xs
                                         font-semibold text-emerald-700">
                                Assigned
                            </span>
                        </div>

                        <h4 class="mt-5 text-3xl font-bold
                                   text-slate-900">
                            10
                        </h4>

                        <p class="mt-1 text-sm text-slate-500">
                            Teacher Assignments
                        </p>
                    </div>

                    <!-- TEMPLATES -->
                    <div class="rounded-2xl border border-slate-200
                                bg-white p-6 shadow-sm">

                        <div class="flex items-center justify-between">

                            <div class="rounded-xl bg-amber-50 p-3
                                        text-amber-600">
                                <i data-lucide="files"
                                   class="h-6 w-6"></i>
                            </div>

                            <span class="rounded-full bg-amber-50
                                         px-3 py-1 text-xs
                                         font-semibold text-amber-700">
                                Templates
                            </span>
                        </div>

                        <h4 class="mt-5 text-3xl font-bold
                                   text-slate-900">
                            0
                        </h4>

                        <p class="mt-1 text-sm text-slate-500">
                            Uploaded Excel Templates
                        </p>
                    </div>

                </div>
            </section>

            <!-- ASSIGNMENTS & REQUIREMENTS -->
            <div class="grid gap-6 xl:grid-cols-3">

                <!-- TEACHER ASSIGNMENTS -->
                <section id="assignments"
                         class="scroll-mt-28 rounded-2xl
                                border border-slate-200 bg-white
                                shadow-sm xl:col-span-2">

                    <div class="flex flex-wrap items-center
                                justify-between gap-4
                                border-b border-slate-100 px-6 py-5">

                        <div>
                            <h3 class="font-bold text-slate-900">
                                Teacher Assignments
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Recent Grade 11 and 12 assignments
                            </p>
                        </div>

                        <span class="rounded-lg bg-slate-100
                                     px-3 py-2 text-xs
                                     font-medium text-slate-600">
                            Demo Records
                        </span>
                    </div>

                    <!-- SEARCH -->
                    <div class="border-b border-slate-100 px-6 py-4">

                        <div class="relative max-w-sm">

                            <i data-lucide="search"
                               class="absolute left-3 top-1/2
                                      h-4 w-4 -translate-y-1/2
                                      text-slate-400"></i>

                            <input id="teacherSearch"
                                   type="search"
                                   placeholder="Search teacher or section..."
                                   aria-label="Search teacher assignments"
                                   class="w-full rounded-lg border
                                          border-slate-200 bg-slate-50
                                          py-2.5 pl-10 pr-4 text-sm
                                          outline-none transition
                                          focus:border-blue-500
                                          focus:ring-3
                                          focus:ring-blue-500/10">
                        </div>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full text-left text-sm">

                            <thead class="bg-slate-50 text-xs
                                          uppercase tracking-wide
                                          text-slate-500">

                                <tr>
                                    <th class="px-6 py-4 font-semibold">
                                        Teacher
                                    </th>

                                    <th class="px-6 py-4 font-semibold">
                                        Section
                                    </th>

                                    <th class="px-6 py-4 font-semibold">
                                        Grade
                                    </th>

                                    <th class="px-6 py-4 font-semibold">
                                        Status
                                    </th>
                                </tr>
                            </thead>

                            <tbody id="teacherTable"
                                   class="divide-y divide-slate-100">

                                <tr class="teacher-row hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">

                                            <div class="flex h-9 w-9
                                                        items-center justify-center
                                                        rounded-full bg-blue-100
                                                        text-xs font-bold text-blue-700">
                                                MS
                                            </div>

                                            <span class="font-semibold">
                                                Maria Santos
                                            </span>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        STEM 11-A
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        Grade 11
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="rounded-full
                                                     bg-emerald-50 px-3 py-1
                                                     text-xs font-semibold
                                                     text-emerald-700">
                                            Assigned
                                        </span>
                                    </td>
                                </tr>

                                <tr class="teacher-row hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">

                                            <div class="flex h-9 w-9
                                                        items-center justify-center
                                                        rounded-full bg-violet-100
                                                        text-xs font-bold text-violet-700">
                                                JR
                                            </div>

                                            <span class="font-semibold">
                                                John Reyes
                                            </span>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        HUMSS 11-A
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        Grade 11
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="rounded-full
                                                     bg-emerald-50 px-3 py-1
                                                     text-xs font-semibold
                                                     text-emerald-700">
                                            Assigned
                                        </span>
                                    </td>
                                </tr>

                                <tr class="teacher-row hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">

                                            <div class="flex h-9 w-9
                                                        items-center justify-center
                                                        rounded-full bg-pink-100
                                                        text-xs font-bold text-pink-700">
                                                AC
                                            </div>

                                            <span class="font-semibold">
                                                Ana Cruz
                                            </span>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        ABM 12-A
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        Grade 12
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="rounded-full
                                                     bg-emerald-50 px-3 py-1
                                                     text-xs font-semibold
                                                     text-emerald-700">
                                            Assigned
                                        </span>
                                    </td>
                                </tr>

                                <tr class="teacher-row hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">

                                            <div class="flex h-9 w-9
                                                        items-center justify-center
                                                        rounded-full bg-amber-100
                                                        text-xs font-bold text-amber-700">
                                                LG
                                            </div>

                                            <span class="font-semibold">
                                                Luis Garcia
                                            </span>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        TVL 12-A
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        Grade 12
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="rounded-full
                                                     bg-amber-50 px-3 py-1
                                                     text-xs font-semibold
                                                     text-amber-700">
                                            Pending
                                        </span>
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                        <p id="noResults"
                           class="hidden px-6 py-8 text-center
                                  text-sm text-slate-500">
                            No matching teacher assignments found.
                        </p>
                    </div>

                    <div class="border-t border-slate-100 px-6 py-4">
                        <p class="text-xs text-slate-400">
                            Showing sample teacher assignments
                        </p>
                    </div>
                </section>

                <!-- REQUIREMENTS -->
                <section id="requirements"
                         class="scroll-mt-28 rounded-2xl
                                border border-slate-200
                                bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-6 py-5">

                        <h3 class="font-bold text-slate-900">
                            Mandatory Requirements
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Four required semester submissions
                        </p>
                    </div>

                    <div class="space-y-3 p-5">

                        <!-- REQUIREMENT 1 -->
                        <div class="flex items-center gap-3
                                    rounded-xl bg-slate-50 p-4">

                            <div class="rounded-lg bg-white p-2.5
                                        text-blue-600">
                                <i data-lucide="file-text"
                                   class="h-5 w-5"></i>
                            </div>

                            <div class="flex-1">
                                <p class="text-sm font-semibold">
                                    Requirement 01
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Deadline not configured
                                </p>
                            </div>

                            <i data-lucide="clock-3"
                               class="h-4 w-4 text-slate-400"></i>
                        </div>

                        <!-- REQUIREMENT 2 -->
                        <div class="flex items-center gap-3
                                    rounded-xl bg-slate-50 p-4">

                            <div class="rounded-lg bg-white p-2.5
                                        text-blue-600">
                                <i data-lucide="file-text"
                                   class="h-5 w-5"></i>
                            </div>

                            <div class="flex-1">
                                <p class="text-sm font-semibold">
                                    Requirement 02
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Deadline not configured
                                </p>
                            </div>

                            <i data-lucide="clock-3"
                               class="h-4 w-4 text-slate-400"></i>
                        </div>

                        <!-- REQUIREMENT 3 -->
                        <div class="flex items-center gap-3
                                    rounded-xl bg-slate-50 p-4">

                            <div class="rounded-lg bg-white p-2.5
                                        text-blue-600">
                                <i data-lucide="file-text"
                                   class="h-5 w-5"></i>
                            </div>

                            <div class="flex-1">
                                <p class="text-sm font-semibold">
                                    Requirement 03
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Deadline not configured
                                </p>
                            </div>

                            <i data-lucide="clock-3"
                               class="h-4 w-4 text-slate-400"></i>
                        </div>

                        <!-- REQUIREMENT 4 -->
                        <div class="flex items-center gap-3
                                    rounded-xl bg-slate-50 p-4">

                            <div class="rounded-lg bg-white p-2.5
                                        text-blue-600">
                                <i data-lucide="file-text"
                                   class="h-5 w-5"></i>
                            </div>

                            <div class="flex-1">
                                <p class="text-sm font-semibold">
                                    Requirement 04
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Deadline not configured
                                </p>
                            </div>

                            <i data-lucide="clock-3"
                               class="h-4 w-4 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="px-5 pb-5">
                        <div class="rounded-lg bg-blue-50 p-3
                                    text-xs leading-5 text-blue-700">
                            Requirement names and deadlines
                            will be configured by the administrator.
                        </div>
                    </div>
                </section>
            </div>

            <!-- QUICK ACCESS -->
            <section>

                <div class="mb-5">
                    <h3 class="text-lg font-bold text-slate-900">
                        Quick Access
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Navigate to administration modules.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

                    <!-- USERS -->
                    <a href="#accounts"
                       class="group flex items-center gap-4
                              rounded-2xl border border-slate-200
                              bg-white p-5 shadow-sm transition
                              hover:border-blue-300 hover:shadow-md">

                        <div class="rounded-xl bg-blue-50 p-3
                                    text-blue-600">

                            <i data-lucide="user-plus"
                               class="h-6 w-6"></i>
                        </div>

                        <div class="flex-1">
                            <p class="text-sm font-bold">
                                User Accounts
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Manage users
                            </p>
                        </div>

                        <i data-lucide="chevron-right"
                           class="h-4 w-4 text-slate-400"></i>
                    </a>

                    <!-- ASSIGNMENTS -->
                    <a href="#assignments"
                       class="group flex items-center gap-4
                              rounded-2xl border border-slate-200
                              bg-white p-5 shadow-sm transition
                              hover:border-blue-300 hover:shadow-md">

                        <div class="rounded-xl bg-violet-50 p-3
                                    text-violet-600">

                            <i data-lucide="school"
                               class="h-6 w-6"></i>
                        </div>

                        <div class="flex-1">
                            <p class="text-sm font-bold">
                                Assign Teachers
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Manage sections
                            </p>
                        </div>

                        <i data-lucide="chevron-right"
                           class="h-4 w-4 text-slate-400"></i>
                    </a>

                    <!-- REQUIREMENTS -->
                    <a href="#requirements"
                       class="group flex items-center gap-4
                              rounded-2xl border border-slate-200
                              bg-white p-5 shadow-sm transition
                              hover:border-blue-300 hover:shadow-md">

                        <div class="rounded-xl bg-amber-50 p-3
                                    text-amber-600">

                            <i data-lucide="calendar-clock"
                               class="h-6 w-6"></i>
                        </div>

                        <div class="flex-1">
                            <p class="text-sm font-bold">
                                Requirements
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Manage deadlines
                            </p>
                        </div>

                        <i data-lucide="chevron-right"
                           class="h-4 w-4 text-slate-400"></i>
                    </a>

                    <!-- TEMPLATES -->
                    <a href="#templates"
                       class="group flex items-center gap-4
                              rounded-2xl border border-slate-200
                              bg-white p-5 shadow-sm transition
                              hover:border-blue-300 hover:shadow-md">

                        <div class="rounded-xl bg-emerald-50 p-3
                                    text-emerald-600">

                            <i data-lucide="file-spreadsheet"
                               class="h-6 w-6"></i>
                        </div>

                        <div class="flex-1">
                            <p class="text-sm font-bold">
                                Excel Templates
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Manage forms
                            </p>
                        </div>

                        <i data-lucide="chevron-right"
                           class="h-4 w-4 text-slate-400"></i>
                    </a>

                </div>
            </section>

            <!-- ACCOUNT & ACADEMIC SETUP -->
            <div class="grid gap-6 xl:grid-cols-2">

                <!-- ACCOUNT MANAGEMENT -->
                <section id="accounts"
                         class="scroll-mt-28 rounded-2xl
                                border border-slate-200
                                bg-white p-6 shadow-sm">

                    <h3 class="font-bold text-slate-900">
                        Account Management
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Manage authorized school personnel
                    </p>

                    <div class="mt-6 space-y-4">

                        <div class="flex items-center gap-4
                                    rounded-xl border border-slate-100 p-4">

                            <div class="rounded-xl bg-blue-50 p-3
                                        text-blue-600">
                                <i data-lucide="users"
                                   class="h-5 w-5"></i>
                            </div>

                            <div class="flex-1">
                                <p class="text-sm font-semibold">
                                    Teacher Accounts
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Teacher account management
                                </p>
                            </div>

                            <span class="font-bold">8</span>
                        </div>

                        <div class="flex items-center gap-4
                                    rounded-xl border border-slate-100 p-4">

                            <div class="rounded-xl bg-violet-50 p-3
                                        text-violet-600">
                                <i data-lucide="user-round-check"
                                   class="h-5 w-5"></i>
                            </div>

                            <div class="flex-1">
                                <p class="text-sm font-semibold">
                                    Principal Account
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Single principal account
                                </p>
                            </div>

                            <span class="rounded-full bg-slate-100
                                         px-3 py-1 text-xs text-slate-600">
                                Setup
                            </span>
                        </div>

                    </div>
                </section>

                <!-- SCHOOL YEARS -->
                <section id="school-years"
                         class="scroll-mt-28 rounded-2xl
                                border border-slate-200
                                bg-white p-6 shadow-sm">

                    <h3 class="font-bold text-slate-900">
                        Academic Configuration
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Sample school year and reporting period
                    </p>

                    <div class="mt-6 space-y-5">

                        <div class="flex items-center justify-between
                                    border-b border-slate-100 pb-4">

                            <div>
                                <p class="text-xs text-slate-500">
                                    School Year
                                </p>

                                <p class="mt-1 text-sm font-semibold">
                                    2026–2027
                                </p>
                            </div>

                            <i data-lucide="calendar-days"
                               class="h-5 w-5 text-blue-600"></i>
                        </div>

                        <div class="flex items-center justify-between
                                    border-b border-slate-100 pb-4">

                            <div>
                                <p class="text-xs text-slate-500">
                                    Semester
                                </p>

                                <p class="mt-1 text-sm font-semibold">
                                    First Semester
                                </p>
                            </div>

                            <i data-lucide="book-open"
                               class="h-5 w-5 text-blue-600"></i>
                        </div>

                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-xs text-slate-500">
                                    Reporting Period
                                </p>

                                <p class="mt-1 text-sm font-semibold">
                                    First Quarter
                                </p>
                            </div>

                            <i data-lucide="calendar-range"
                               class="h-5 w-5 text-blue-600"></i>
                        </div>
                    </div>
                </section>
            </div>

            <!-- EXCEL TEMPLATES -->
            <section id="templates"
                     class="scroll-mt-28 rounded-2xl
                            border border-slate-200
                            bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="font-bold text-slate-900">
                        School Form Templates
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Official Excel templates managed by the administrator
                    </p>
                </div>

                <div class="grid gap-4 p-6 sm:grid-cols-2 xl:grid-cols-3">

                    <!-- MASTER SHEET -->
                    <div class="flex items-center gap-4
                                rounded-xl border border-slate-200 p-4">

                        <div class="rounded-xl bg-emerald-50 p-3
                                    text-emerald-600">
                            <i data-lucide="file-spreadsheet"
                               class="h-6 w-6"></i>
                        </div>

                        <div class="flex-1">
                            <p class="text-sm font-semibold">
                                Master Sheet
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Not uploaded
                            </p>
                        </div>
                    </div>

                    <!-- STUDENT CARD -->
                    <div class="flex items-center gap-4
                                rounded-xl border border-slate-200 p-4">

                        <div class="rounded-xl bg-emerald-50 p-3
                                    text-emerald-600">
                            <i data-lucide="file-spreadsheet"
                               class="h-6 w-6"></i>
                        </div>

                        <div class="flex-1">
                            <p class="text-sm font-semibold">
                                Student Card
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Not uploaded
                            </p>
                        </div>
                    </div>

                    <!-- SF1 -->
                    <div class="flex items-center gap-4
                                rounded-xl border border-slate-200 p-4">

                        <div class="rounded-xl bg-emerald-50 p-3
                                    text-emerald-600">
                            <i data-lucide="file-spreadsheet"
                               class="h-6 w-6"></i>
                        </div>

                        <div class="flex-1">
                            <p class="text-sm font-semibold">
                                SF1
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Not uploaded
                            </p>
                        </div>
                    </div>

                    <!-- SF2 -->
                    <div class="flex items-center gap-4
                                rounded-xl border border-slate-200 p-4">

                        <div class="rounded-xl bg-emerald-50 p-3
                                    text-emerald-600">
                            <i data-lucide="file-spreadsheet"
                               class="h-6 w-6"></i>
                        </div>

                        <div class="flex-1">
                            <p class="text-sm font-semibold">
                                SF2
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Not uploaded
                            </p>
                        </div>
                    </div>

                    <!-- SF5 -->
                    <div class="flex items-center gap-4
                                rounded-xl border border-slate-200 p-4">

                        <div class="rounded-xl bg-emerald-50 p-3
                                    text-emerald-600">
                            <i data-lucide="file-spreadsheet"
                               class="h-6 w-6"></i>
                        </div>

                        <div class="flex-1">
                            <p class="text-sm font-semibold">
                                SF5
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Not uploaded
                            </p>
                        </div>
                    </div>

                </div>
            </section>

            <!-- SETTINGS -->
            <section id="settings"
                     class="scroll-mt-28 rounded-2xl
                            border border-slate-200
                            bg-white p-6 shadow-sm">

                <div class="flex items-center gap-4">

                    <div class="rounded-xl bg-slate-100 p-3
                                text-slate-600">
                        <i data-lucide="settings"
                           class="h-6 w-6"></i>
                    </div>

                    <div>
                        <h3 class="font-bold">
                            Administrator Settings
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Profile and account security
                            settings will be available
                            after authentication integration.
                        </p>
                    </div>
                </div>
            </section>

            <!-- FOOTER -->
            <footer class="flex flex-wrap items-center
                           justify-between gap-3 border-t
                           border-slate-200 py-6 text-xs
                           text-slate-400">

                <p>
                    © <span id="currentYear">2026</span>
                    SHS Forms Management System
                </p>

                <p>Administrator Portal</p>
            </footer>

        </main>
    </div>

    <!-- SIDEBAR LINK STYLES -->
    <style>
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 12px;
            padding: 12px 16px;
            font-weight: 500;
            color: #dbeafe;
            transition: background .2s, color .2s;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
    </style>

    <!-- JAVASCRIPT -->
    <script>
        lucide.createIcons();

        // SIDEBAR
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const openSidebarBtn = document.getElementById('openSidebar');
        const closeSidebarBtn = document.getElementById('closeSidebar');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            openSidebarBtn.setAttribute('aria-expanded', 'true');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            openSidebarBtn.setAttribute('aria-expanded', 'false');
        }

        openSidebarBtn.addEventListener('click', openSidebar);
        closeSidebarBtn.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);

        // CLOSE SIDEBAR AFTER CLICKING A LINK
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', function () {
                if (window.innerWidth < 1024) {
                    closeSidebar();
                }
            });
        });

        // NOTIFICATIONS
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationPanel = document.getElementById('notificationPanel');

        notificationBtn.addEventListener('click', function (event) {
            event.stopPropagation();

            notificationPanel.classList.toggle('hidden');

            notificationBtn.setAttribute(
                'aria-expanded',
                String(!notificationPanel.classList.contains('hidden'))
            );
        });

        document.addEventListener('click', function (event) {
            if (!notificationPanel.contains(event.target) &&
                !notificationBtn.contains(event.target)) {

                notificationPanel.classList.add('hidden');
                notificationBtn.setAttribute('aria-expanded', 'false');
            }
        });

        // SEARCH TEACHER ASSIGNMENTS
        const searchInput = document.getElementById('teacherSearch');
        const teacherRows = document.querySelectorAll('.teacher-row');
        const noResults = document.getElementById('noResults');

        searchInput.addEventListener('input', function () {
            const searchValue = this.value.toLowerCase().trim();
            let visibleCount = 0;

            teacherRows.forEach(row => {
                const rowText = row.textContent.toLowerCase();

                if (rowText.includes(searchValue)) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            noResults.classList.toggle('hidden', visibleCount > 0);
        });

        // CURRENT DATE AND YEAR
        const today = new Date();

        document.getElementById('currentDate').textContent =
            today.toLocaleDateString('en-PH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

        document.getElementById('currentYear').textContent =
            today.getFullYear();

        // ESC KEY
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeSidebar();
                notificationPanel.classList.add('hidden');
                notificationBtn.setAttribute('aria-expanded', 'false');
            }
        });
    </script>

</body>
</html>
