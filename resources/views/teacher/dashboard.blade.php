
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Teacher Dashboard | SHS Forms</title>

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
        }

        .nav-item:hover {
            background: rgba(255,255,255,.1);
            color: white;
        }

        .nav-active {
            background: rgba(255,255,255,.15);
            color: white;
            font-weight: 600;
        }
    </style>

</head>

<body class="bg-[#f5f7fb] text-slate-800 antialiased">

    <!-- MOBILE OVERLAY -->
    <div id="sidebarOverlay"
         class="fixed inset-0 z-40 hidden
                bg-slate-950/50 lg:hidden">
    </div>

    <!-- SIDEBAR -->
    <x-teacher.sidebar />

    <!-- MAIN CONTENT -->
    <div class="min-h-screen lg:ml-70">

       <x-teacher.header />

        <!-- DASHBOARD BODY -->
        <main class="mx-auto max-w-7xl
                     space-y-8 p-5 lg:p-8">

            <!-- WELCOME BANNER -->
            <section class="relative overflow-hidden
                            rounded-2xl bg-gradient-to-r
                            from-[#142b61] to-[#2857b5]
                            p-7 text-white md:p-9">

                <div class="absolute -right-20 -top-20
                            h-72 w-72 rounded-full
                            bg-white/5"></div>

                <div class="relative z-10">

                    <span class="inline-flex items-center gap-2
                                 rounded-full bg-white/10
                                 px-3 py-1.5 text-xs
                                 text-blue-100">

                        <i data-lucide="calendar"
                           class="h-4 w-4"></i>

                        <span id="currentDate">Current Date</span>
                    </span>

                    <h2 class="mt-5 text-2xl font-bold md:text-3xl">
                        Welcome back, Teacher!
                    </h2>

                    <p class="mt-3 max-w-xl text-sm
                              leading-7 text-blue-100">

                        Manage your classrooms, upload class
                        records, generate school forms, and
                        monitor your semester requirements.

                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">

                        <span class="inline-flex items-center
                                     gap-2 rounded-lg bg-white
                                     px-4 py-2.5 text-sm
                                     font-semibold text-blue-900">

                            <i data-lucide="calendar-days"
                               class="h-4 w-4"></i>

                            SY 2026–2027
                        </span>

                        <span class="inline-flex items-center
                                     gap-2 rounded-lg
                                     border border-white/25
                                     bg-white/10 px-4 py-2.5
                                     text-sm font-semibold">

                            <i data-lucide="book-open"
                               class="h-4 w-4"></i>

                            First Semester
                        </span>
                    </div>
                </div>
            </section>

            <!-- STATISTICS -->
            <section>

                <div class="mb-5">
                    <h3 class="text-lg font-bold text-slate-900">
                        Teaching Overview
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Overview of your assigned classrooms
                        and academic records.
                    </p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

                    <!-- CLASSROOMS -->
                    <div class="rounded-2xl border border-slate-200
                                bg-white p-6 shadow-sm">

                        <div class="flex items-center justify-between">

                            <div class="rounded-xl bg-blue-50 p-3
                                        text-blue-600">

                                <i data-lucide="school"
                                   class="h-6 w-6"></i>
                            </div>

                            <span class="rounded-full bg-blue-50
                                         px-3 py-1 text-xs
                                         font-semibold text-blue-700">
                                Assigned
                            </span>
                        </div>

                        <h4 class="mt-5 text-3xl font-bold">
                            3
                        </h4>

                        <p class="mt-1 text-sm text-slate-500">
                            My Classrooms
                        </p>
                    </div>

                    <!-- STUDENTS -->
                    <div class="rounded-2xl border border-slate-200
                                bg-white p-6 shadow-sm">

                        <div class="flex items-center justify-between">

                            <div class="rounded-xl bg-violet-50 p-3
                                        text-violet-600">

                                <i data-lucide="users"
                                   class="h-6 w-6"></i>
                            </div>

                            <span class="rounded-full bg-violet-50
                                         px-3 py-1 text-xs
                                         font-semibold text-violet-700">
                                Enrolled
                            </span>
                        </div>

                        <h4 class="mt-5 text-3xl font-bold">
                            120
                        </h4>

                        <p class="mt-1 text-sm text-slate-500">
                            Total Students
                        </p>
                    </div>

                    <!-- CLASS RECORDS -->
                    <div class="rounded-2xl border border-slate-200
                                bg-white p-6 shadow-sm">

                        <div class="flex items-center justify-between">

                            <div class="rounded-xl bg-emerald-50 p-3
                                        text-emerald-600">

                                <i data-lucide="file-spreadsheet"
                                   class="h-6 w-6"></i>
                            </div>

                            <span class="rounded-full bg-emerald-50
                                         px-3 py-1 text-xs
                                         font-semibold text-emerald-700">
                                Uploaded
                            </span>
                        </div>

                        <h4 class="mt-5 text-3xl font-bold">
                            16
                        </h4>

                        <p class="mt-1 text-sm text-slate-500">
                            Subject Class Records
                        </p>
                    </div>

                    <!-- REQUIREMENTS -->
                    <div class="rounded-2xl border border-slate-200
                                bg-white p-6 shadow-sm">

                        <div class="flex items-center justify-between">

                            <div class="rounded-xl bg-amber-50 p-3
                                        text-amber-600">

                                <i data-lucide="clipboard-check"
                                   class="h-6 w-6"></i>
                            </div>

                            <span class="rounded-full bg-amber-50
                                         px-3 py-1 text-xs
                                         font-semibold text-amber-700">
                                Semester
                            </span>
                        </div>

                        <h4 class="mt-5 text-3xl font-bold">
                            1<span class="text-xl text-slate-400">/4</span>
                        </h4>

                        <p class="mt-1 text-sm text-slate-500">
                            Approved Requirements
                        </p>
                    </div>

                </div>
            </section>

            <!-- QUICK ACTIONS -->
            <section>

                <div class="mb-5">
                    <h3 class="text-lg font-bold">
                        Quick Access
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Access your commonly used teaching tools.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

                    <!-- CLASS RECORDS -->
                    <a href="#class-records"
                       class="flex items-center gap-4 rounded-2xl
                              border border-slate-200 bg-white
                              p-5 shadow-sm transition
                              hover:border-blue-300 hover:shadow-md">

                        <div class="rounded-xl bg-blue-50 p-3
                                    text-blue-600">

                            <i data-lucide="upload"
                               class="h-6 w-6"></i>
                        </div>

                        <div class="flex-1">

                            <h4 class="text-sm font-bold">
                                Class Records
                            </h4>

                            <p class="mt-1 text-xs text-slate-500">
                                Upload records
                            </p>
                        </div>

                        <i data-lucide="chevron-right"
                           class="h-4 w-4 text-slate-400"></i>
                    </a>

                    <!-- MASTER SHEET -->
                    <a href="#master-sheet"
                       class="flex items-center gap-4 rounded-2xl
                              border border-slate-200 bg-white
                              p-5 shadow-sm transition
                              hover:border-blue-300 hover:shadow-md">

                        <div class="rounded-xl bg-violet-50 p-3
                                    text-violet-600">

                            <i data-lucide="table-2"
                               class="h-6 w-6"></i>
                        </div>

                        <div class="flex-1">
                            <h4 class="text-sm font-bold">
                                Master Sheets
                            </h4>

                            <p class="mt-1 text-xs text-slate-500">
                                Generate or import
                            </p>
                        </div>

                        <i data-lucide="chevron-right"
                           class="h-4 w-4 text-slate-400"></i>
                    </a>

                    <!-- SCHOOL FORMS -->
                    <a href="#sf-forms"
                       class="flex items-center gap-4 rounded-2xl
                              border border-slate-200 bg-white
                              p-5 shadow-sm transition
                              hover:border-blue-300 hover:shadow-md">

                        <div class="rounded-xl bg-emerald-50 p-3
                                    text-emerald-600">

                            <i data-lucide="files"
                               class="h-6 w-6"></i>
                        </div>

                        <div class="flex-1">
                            <h4 class="text-sm font-bold">
                                School Forms
                            </h4>

                            <p class="mt-1 text-xs text-slate-500">
                                SF1, SF2, SF5
                            </p>
                        </div>

                        <i data-lucide="chevron-right"
                           class="h-4 w-4 text-slate-400"></i>
                    </a>

                    <!-- REQUIREMENTS -->
                    <a href="#requirements"
                       class="flex items-center gap-4 rounded-2xl
                              border border-slate-200 bg-white
                              p-5 shadow-sm transition
                              hover:border-blue-300 hover:shadow-md">

                        <div class="rounded-xl bg-amber-50 p-3
                                    text-amber-600">

                            <i data-lucide="send"
                               class="h-6 w-6"></i>
                        </div>

                        <div class="flex-1">
                            <h4 class="text-sm font-bold">
                                Submissions
                            </h4>

                            <p class="mt-1 text-xs text-slate-500">
                                Track requirements
                            </p>
                        </div>

                        <i data-lucide="chevron-right"
                           class="h-4 w-4 text-slate-400"></i>
                    </a>

                </div>
            </section>

            <!-- CLASSROOMS AND SUBMISSIONS -->
            <div class="grid gap-6 xl:grid-cols-3">

                <!-- MY CLASSROOMS -->
                <section id="classrooms"
                         class="scroll-mt-28 rounded-2xl
                                border border-slate-200
                                bg-white shadow-sm xl:col-span-2">

                    <div class="flex items-center justify-between
                                border-b border-slate-100
                                px-6 py-5">

                        <div>
                            <h3 class="font-bold">
                                My Classrooms
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Assigned Grade 11 and 12 classrooms
                            </p>
                        </div>

                        <span class="rounded-lg bg-blue-50
                                     px-3 py-2 text-xs
                                     font-semibold text-blue-700">
                            3 Sections
                        </span>
                    </div>

                    <!-- SEARCH -->
                    <div class="border-b border-slate-100 px-6 py-4">

                        <div class="relative max-w-sm">

                            <i data-lucide="search"
                               class="absolute left-3 top-1/2
                                      h-4 w-4 -translate-y-1/2
                                      text-slate-400"></i>

                            <input id="classroomSearch"
                                   type="search"
                                   placeholder="Search classrooms..."
                                   aria-label="Search classrooms"
                                   class="w-full rounded-lg
                                          border border-slate-200
                                          bg-slate-50 py-2.5 pl-10
                                          pr-4 text-sm outline-none
                                          focus:border-blue-500">
                        </div>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full text-left text-sm">

                            <thead class="bg-slate-50 text-xs
                                          uppercase tracking-wide
                                          text-slate-500">

                                <tr>
                                    <th class="px-6 py-4">Classroom</th>
                                    <th class="px-6 py-4">Students</th>
                                    <th class="px-6 py-4">Records</th>
                                    <th class="px-6 py-4">Status</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">

                                <!-- CLASSROOM 1 -->
                                <tr class="classroom-row hover:bg-slate-50">

                                    <td class="whitespace-nowrap px-6 py-5">

                                        <div class="font-semibold">
                                            STEM 11-A
                                        </div>

                                        <p class="mt-1 text-xs
                                                  text-slate-500">
                                            Grade 11
                                        </p>
                                    </td>

                                    <td class="px-6 py-5">42</td>

                                    <td class="px-6 py-5">

                                        <p class="font-semibold">
                                            8 / 8
                                        </p>

                                        <div class="mt-2 h-1.5 w-24
                                                    rounded-full bg-slate-100">

                                            <div class="h-1.5 w-full
                                                        rounded-full
                                                        bg-emerald-500">
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5">

                                        <span class="rounded-full
                                                     bg-emerald-50
                                                     px-3 py-1
                                                     text-xs font-semibold
                                                     text-emerald-700">
                                            Complete
                                        </span>
                                    </td>
                                </tr>

                                <!-- CLASSROOM 2 -->
                                <tr class="classroom-row hover:bg-slate-50">

                                    <td class="whitespace-nowrap px-6 py-5">

                                        <div class="font-semibold">
                                            HUMSS 11-B
                                        </div>

                                        <p class="mt-1 text-xs
                                                  text-slate-500">
                                            Grade 11
                                        </p>
                                    </td>

                                    <td class="px-6 py-5">38</td>

                                    <td class="px-6 py-5">

                                        <p class="font-semibold">
                                            5 / 7
                                        </p>

                                        <div class="mt-2 h-1.5 w-24
                                                    rounded-full bg-slate-100">

                                            <div class="h-1.5 w-[71%]
                                                        rounded-full
                                                        bg-blue-500">
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5">

                                        <span class="rounded-full
                                                     bg-blue-50 px-3 py-1
                                                     text-xs font-semibold
                                                     text-blue-700">
                                            In Progress
                                        </span>
                                    </td>
                                </tr>

                                <!-- CLASSROOM 3 -->
                                <tr class="classroom-row hover:bg-slate-50">

                                    <td class="whitespace-nowrap px-6 py-5">

                                        <div class="font-semibold">
                                            ABM 12-A
                                        </div>

                                        <p class="mt-1 text-xs
                                                  text-slate-500">
                                            Grade 12
                                        </p>
                                    </td>

                                    <td class="px-6 py-5">40</td>

                                    <td class="px-6 py-5">

                                        <p class="font-semibold">
                                            3 / 6
                                        </p>

                                        <div class="mt-2 h-1.5 w-24
                                                    rounded-full bg-slate-100">

                                            <div class="h-1.5 w-1/2
                                                        rounded-full
                                                        bg-amber-500">
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5">

                                        <span class="rounded-full
                                                     bg-amber-50 px-3 py-1
                                                     text-xs font-semibold
                                                     text-amber-700">
                                            In Progress
                                        </span>
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                        <p id="noClassroomResults"
                           class="hidden px-6 py-8 text-center
                                  text-sm text-slate-500">

                            No matching classrooms found.
                        </p>
                    </div>
                </section>

                <!-- SUBMISSION OVERVIEW -->
                <section id="requirements"
                         class="scroll-mt-28 rounded-2xl
                                border border-slate-200
                                bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-6 py-5">

                        <h3 class="font-bold">
                            Semester Requirements
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            First Semester submission tracking
                        </p>
                    </div>

                    <div class="p-5">

                        <div class="flex items-center justify-between">

                            <span class="text-sm font-semibold">
                                Approval Progress
                            </span>

                            <span class="text-sm font-bold
                                         text-blue-700">
                                25%
                            </span>
                        </div>

                        <div class="mt-3 h-2 rounded-full
                                    bg-slate-100">

                            <div class="h-2 w-1/4 rounded-full
                                        bg-blue-600"></div>
                        </div>

                        <p class="mt-2 text-xs text-slate-500">
                            1 of 4 requirements approved
                        </p>

                        <!-- STATUS ITEMS -->
                        <div class="mt-6 space-y-4">

                            <div class="flex items-center
                                        justify-between gap-3">

                                <div class="flex items-center gap-3">
                                    <div class="rounded-lg
                                                bg-emerald-50 p-2
                                                text-emerald-600">

                                        <i data-lucide="check-circle"
                                           class="h-4 w-4"></i>
                                    </div>

                                    <p class="text-sm font-medium">
                                        Requirement 01
                                    </p>
                                </div>

                                <span class="text-xs font-semibold
                                             text-emerald-600">
                                    Approved
                                </span>
                            </div>

                            <div class="flex items-center
                                        justify-between gap-3">

                                <div class="flex items-center gap-3">
                                    <div class="rounded-lg
                                                bg-blue-50 p-2
                                                text-blue-600">

                                        <i data-lucide="clock"
                                           class="h-4 w-4"></i>
                                    </div>

                                    <p class="text-sm font-medium">
                                        Requirement 02
                                    </p>
                                </div>

                                <span class="text-xs font-semibold
                                             text-blue-600">
                                    Under Review
                                </span>
                            </div>

                            <div class="flex items-center
                                        justify-between gap-3">

                                <div class="flex items-center gap-3">
                                    <div class="rounded-lg
                                                bg-red-50 p-2
                                                text-red-600">

                                        <i data-lucide="rotate-ccw"
                                           class="h-4 w-4"></i>
                                    </div>

                                    <p class="text-sm font-medium">
                                        Requirement 03
                                    </p>
                                </div>

                                <span class="text-xs font-semibold
                                             text-red-600">
                                    Returned
                                </span>
                            </div>

                            <div class="flex items-center
                                        justify-between gap-3">

                                <div class="flex items-center gap-3">
                                    <div class="rounded-lg
                                                bg-slate-100 p-2
                                                text-slate-500">

                                        <i data-lucide="circle-dashed"
                                           class="h-4 w-4"></i>
                                    </div>

                                    <p class="text-sm font-medium">
                                        Requirement 04
                                    </p>
                                </div>

                                <span class="text-xs font-semibold
                                             text-slate-500">
                                    Missing
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 rounded-xl
                                    bg-blue-50 p-4">

                            <p class="text-xs leading-6
                                      text-blue-700">
                                Requirement names and deadlines
                                will be provided by the administrator.
                            </p>
                        </div>

                    </div>
                </section>

            </div>

            <!-- CLASS RECORDS -->
            <section id="class-records"
                     class="scroll-mt-28 rounded-2xl
                            border border-slate-200
                            bg-white shadow-sm">

                <div class="flex flex-wrap items-center
                            justify-between gap-4
                            border-b border-slate-100 px-6 py-5">

                    <div>
                        <h3 class="font-bold">
                            Subject Class Records
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Monitor uploaded subject class records
                        </p>
                    </div>

                    <span class="rounded-lg bg-slate-100
                                 px-3 py-2 text-xs
                                 font-semibold text-slate-600">
                        Demo Records
                    </span>
                </div>

                <div class="overflow-x-auto">

                    <table class="w-full text-left text-sm">

                        <thead class="bg-slate-50 text-xs
                                      uppercase tracking-wide
                                      text-slate-500">

                            <tr>
                                <th class="px-6 py-4">Subject</th>
                                <th class="px-6 py-4">Section</th>
                                <th class="px-6 py-4">File</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            <tr>
                                <td class="px-6 py-5 font-semibold">
                                    General Mathematics
                                </td>

                                <td class="px-6 py-5">
                                    STEM 11-A
                                </td>

                                <td class="px-6 py-5 text-slate-500">
                                    mathematics.xlsx
                                </td>

                                <td class="px-6 py-5">
                                    <span class="rounded-full
                                                 bg-emerald-50
                                                 px-3 py-1 text-xs
                                                 font-semibold
                                                 text-emerald-700">
                                        Uploaded
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td class="px-6 py-5 font-semibold">
                                    Oral Communication
                                </td>

                                <td class="px-6 py-5">
                                    HUMSS 11-B
                                </td>

                                <td class="px-6 py-5 text-slate-500">
                                    oral-communication.xlsx
                                </td>

                                <td class="px-6 py-5">
                                    <span class="rounded-full
                                                 bg-emerald-50
                                                 px-3 py-1 text-xs
                                                 font-semibold
                                                 text-emerald-700">
                                        Uploaded
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td class="px-6 py-5 font-semibold">
                                    Business Mathematics
                                </td>

                                <td class="px-6 py-5">
                                    ABM 12-A
                                </td>

                                <td class="px-6 py-5 text-slate-400">
                                    No file
                                </td>

                                <td class="px-6 py-5">
                                    <span class="rounded-full
                                                 bg-amber-50 px-3 py-1
                                                 text-xs font-semibold
                                                 text-amber-700">
                                        Not Uploaded
                                    </span>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-100 px-6 py-4">

                    <p class="text-xs text-slate-500">
                        Excel uploads and record validation will
                        be implemented in the Class Records module.
                    </p>
                </div>
            </section>

            <!-- SCHOOL FORM GENERATION -->
            <section id="document-tools">

                <div class="mb-5">
                    <h3 class="text-lg font-bold">
                        School Form Generation
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Generate academic records using the official
                        school-approved Excel templates.
                    </p>
                </div>

                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">

                    <!-- MASTER SHEET -->
                    <div id="master-sheet"
                         class="scroll-mt-28 rounded-2xl
                                border border-slate-200
                                bg-white p-6 shadow-sm">

                        <div class="flex items-center justify-between">

                            <div class="rounded-xl bg-blue-50 p-3
                                        text-blue-600">
                                <i data-lucide="table-2"
                                   class="h-6 w-6"></i>
                            </div>

                            <span class="text-xs text-slate-400">
                                Excel
                            </span>
                        </div>

                        <h4 class="mt-5 font-bold">
                            Master Sheet
                        </h4>

                        <p class="mt-2 text-sm leading-6
                                  text-slate-500">

                            Generate a consolidated master sheet
                            from multiple subject class records,
                            or import an existing master sheet.

                        </p>

                        <div class="mt-5 rounded-lg bg-slate-50
                                    p-3 text-xs text-slate-500">

                            Generation and import coming soon
                        </div>
                    </div>

                    <!-- STUDENT CARD -->
                    <div id="student-card"
                         class="scroll-mt-28 rounded-2xl
                                border border-slate-200
                                bg-white p-6 shadow-sm">

                        <div class="flex items-center justify-between">

                            <div class="rounded-xl bg-violet-50 p-3
                                        text-violet-600">
                                <i data-lucide="contact"
                                   class="h-6 w-6"></i>
                            </div>

                            <span class="text-xs text-slate-400">
                                Excel
                            </span>
                        </div>

                        <h4 class="mt-5 font-bold">
                            Student Card
                        </h4>

                        <p class="mt-2 text-sm leading-6
                                  text-slate-500">

                            Generate individual student
                            report cards from finalized
                            academic records and grades.

                        </p>

                        <div class="mt-5 rounded-lg bg-slate-50
                                    p-3 text-xs text-slate-500">

                            Generation coming soon
                        </div>
                    </div>

                    <!-- SF FORMS -->
                    <div id="sf-forms"
                         class="scroll-mt-28 rounded-2xl
                                border border-slate-200
                                bg-white p-6 shadow-sm">

                        <div class="flex items-center justify-between">

                            <div class="rounded-xl bg-emerald-50 p-3
                                        text-emerald-600">
                                <i data-lucide="file-spreadsheet"
                                   class="h-6 w-6"></i>
                            </div>

                            <span class="text-xs text-slate-400">
                                Excel
                            </span>
                        </div>

                        <h4 class="mt-5 font-bold">
                            SF1, SF2 & SF5
                        </h4>

                        <p class="mt-2 text-sm leading-6
                                  text-slate-500">

                            Generate official school forms
                            using student information,
                            attendance, and promotion records.

                        </p>

                        <div class="mt-5 flex flex-wrap gap-2">

                            <span class="rounded-lg bg-emerald-50
                                         px-3 py-1.5 text-xs
                                         font-semibold text-emerald-700">
                                SF1
                            </span>

                            <span class="rounded-lg bg-emerald-50
                                         px-3 py-1.5 text-xs
                                         font-semibold text-emerald-700">
                                SF2
                            </span>

                            <span class="rounded-lg bg-emerald-50
                                         px-3 py-1.5 text-xs
                                         font-semibold text-emerald-700">
                                SF5
                            </span>
                        </div>

                        <p class="mt-4 text-xs text-slate-400">
                            Generation coming soon
                        </p>
                    </div>

                </div>
            </section>

            <!-- STUDENT DATA & ATTENDANCE -->
            <div class="grid gap-6 xl:grid-cols-2">

                <!-- STUDENT INFORMATION -->
                <section id="students"
                         class="scroll-mt-28 rounded-2xl
                                border border-slate-200
                                bg-white p-6 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="rounded-xl bg-blue-50 p-3
                                    text-blue-600">

                            <i data-lucide="users"
                               class="h-6 w-6"></i>
                        </div>

                        <div>
                            <h3 class="font-bold">
                                Student Information
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Student profile and enrollment records
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4">

                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs text-slate-500">
                                Grade 11
                            </p>

                            <p class="mt-2 text-2xl font-bold">
                                80
                            </p>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs text-slate-500">
                                Grade 12
                            </p>

                            <p class="mt-2 text-2xl font-bold">
                                40
                            </p>
                        </div>
                    </div>

                    <p class="mt-5 text-xs leading-6
                              text-slate-500">

                        Student profile registration,
                        updating, and classroom enrollment
                        will be available in this module.

                    </p>
                </section>

                <!-- ATTENDANCE -->
                <section id="attendance"
                         class="scroll-mt-28 rounded-2xl
                                border border-slate-200
                                bg-white p-6 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="rounded-xl bg-violet-50 p-3
                                    text-violet-600">

                            <i data-lucide="calendar-check"
                               class="h-6 w-6"></i>
                        </div>

                        <div>
                            <h3 class="font-bold">
                                Attendance & Promotion
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Student attendance and academic status
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">

                        <div class="flex items-center gap-3
                                    rounded-xl border border-slate-100 p-4">

                            <i data-lucide="calendar-days"
                               class="h-5 w-5 text-blue-600"></i>

                            <div class="flex-1">
                                <p class="text-sm font-semibold">
                                    Attendance Records
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Monthly attendance for SF2
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3
                                    rounded-xl border border-slate-100 p-4">

                            <i data-lucide="graduation-cap"
                               class="h-5 w-5 text-violet-600"></i>

                            <div class="flex-1">
                                <p class="text-sm font-semibold">
                                    Promotion Records
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Promotion data for SF5
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- RETURNED DOCUMENTS -->
            <section id="returned"
                     class="scroll-mt-28 rounded-2xl
                            border border-slate-200
                            bg-white shadow-sm">

                <div class="flex items-center gap-3
                            border-b border-slate-100 px-6 py-5">

                    <div class="rounded-xl bg-red-50 p-3
                                text-red-600">

                        <i data-lucide="rotate-ccw"
                           class="h-5 w-5"></i>
                    </div>

                    <div>
                        <h3 class="font-bold">
                            Returned Documents
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Documents requiring correction and resubmission
                        </p>
                    </div>
                </div>

                <div class="p-6">

                    <div class="flex flex-wrap items-center
                                justify-between gap-4
                                rounded-xl border border-red-100
                                bg-red-50/50 p-5">

                        <div>
                            <p class="text-sm font-semibold">
                                Requirement 03
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                First Semester • Sample submission
                            </p>

                            <p class="mt-3 text-sm text-red-700">
                                Principal's remarks: Please verify
                                the student information and correct
                                the incomplete entries.
                            </p>
                        </div>

                        <span class="rounded-full bg-red-100
                                     px-3 py-1.5 text-xs
                                     font-semibold text-red-700">
                            Needs Revision
                        </span>
                    </div>

                    <p class="mt-4 text-xs text-slate-500">
                        The document correction and resubmission
                        workflow will be implemented with the
                        submission module.
                    </p>
                </div>
            </section>

            <!-- ACCOUNT SETTINGS -->
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
                            Account Settings
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Profile settings and password changes
                            will be available after authentication
                            integration.
                        </p>
                    </div>
                </div>
            </section>

            <!-- FOOTER -->
            <footer class="flex flex-wrap items-center
                           justify-between gap-3
                           border-t border-slate-200
                           py-6 text-xs text-slate-400">

                <p>
                    © <span id="currentYear">2026</span>
                    SHS Forms Management System
                </p>

                <p>Teacher Portal</p>
            </footer>

        </main>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        lucide.createIcons();

        // SIDEBAR
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const openSidebarBtn = document.getElementById('openSidebar');
        const closeSidebarBtn = document.getElementById('closeSidebar');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
            openSidebarBtn.setAttribute('aria-expanded', 'true');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
            openSidebarBtn.setAttribute('aria-expanded', 'false');
        }

        openSidebarBtn.addEventListener('click', openSidebar);
        closeSidebarBtn.addEventListener('click', closeSidebar);
        sidebarOverlay.addEventListener('click', closeSidebar);

        // CLOSE SIDEBAR AFTER MOBILE NAVIGATION
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

        notificationBtn.addEventListener('click', function(event) {
            event.stopPropagation();

            notificationPanel.classList.toggle('hidden');

            notificationBtn.setAttribute(
                'aria-expanded',
                String(!notificationPanel.classList.contains('hidden'))
            );
        });

        document.addEventListener('click', function(event) {
            if (!notificationBtn.contains(event.target) &&
                !notificationPanel.contains(event.target)) {

                notificationPanel.classList.add('hidden');
                notificationBtn.setAttribute('aria-expanded', 'false');
            }
        });

        // SEARCH CLASSROOMS
        const classroomSearch = document.getElementById('classroomSearch');
        const classroomRows = document.querySelectorAll('.classroom-row');
        const noClassroomResults = document.getElementById('noClassroomResults');

        classroomSearch.addEventListener('input', function() {

            const searchValue = this.value.toLowerCase().trim();
            let visibleCount = 0;

            classroomRows.forEach(row => {

                const text = row.textContent.toLowerCase();

                if (text.includes(searchValue)) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            noClassroomResults.classList.toggle('hidden', visibleCount > 0);
        });

        // CURRENT DATE
        const today = new Date();

        document.getElementById('currentDate').textContent =
            today.toLocaleDateString('en-PH', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });

        document.getElementById('currentYear').textContent =
            today.getFullYear();

        // ESCAPE KEY
        document.addEventListener('keydown', function(event) {

            if (event.key === 'Escape') {
                closeSidebar();

                notificationPanel.classList.add('hidden');
                notificationBtn.setAttribute('aria-expanded', 'false');
            }
        });
    </script>

</body>
</html>
