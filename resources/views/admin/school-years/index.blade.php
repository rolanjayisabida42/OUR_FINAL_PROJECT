
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>School Years & Periods | SHS Forms</title>

    <!-- TAILWIND CSS V4 -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- INTER FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
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
            color: #bfdbfe;
            font-size: 14px;
            transition: 0.2s ease;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.10);
            color: white;
        }

        .nav-active {
            background: rgba(255,255,255,0.16);
            color: white;
            font-weight: 600;
        }

        .form-input {
            display: block;
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            outline: none;
            font-size: 14px;
            transition: 0.2s;
        }

        .form-input:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13,148,136,0.12);
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 12px;
            padding: 11px 18px;
            background: #0d9488;
            color: white;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-primary:hover {
            background: #0f766e;
        }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-outline:hover {
            background: #f8fafc;
        }
    </style>
</head>

<body class="bg-[#f5f7fb] text-slate-800">

    <!-- MOBILE SIDEBAR OVERLAY -->
    <div id="sidebarOverlay"
         class="fixed inset-0 z-40 hidden
                bg-slate-950/50 lg:hidden">
    </div>


    <!-- ADMIN SIDEBAR -->
    <x-admin.sidebar />


    <!-- MAIN CONTENT -->
    <div class="min-h-screen lg:ml-70">

        <!-- HEADER -->
        <header class="sticky top-0 z-30 flex h-18
                       items-center justify-between
                       border-b border-slate-200
                       bg-white/95 px-5 backdrop-blur
                       lg:px-8">

            <div class="flex items-center gap-4">

                <button id="openSidebar"
                        type="button"
                        aria-label="Open sidebar"
                        class="rounded-lg p-2
                               hover:bg-slate-100 lg:hidden">

                    <i data-lucide="menu"
                       class="h-6 w-6"></i>
                </button>

                <div>
                    <p class="text-xs text-slate-400">
                        Admin / Academic Management
                    </p>

                    <h2 class="text-lg font-bold">
                        School Years & Periods
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-3">

                <span class="hidden rounded-full
                             bg-teal-50 px-3 py-1.5
                             text-xs font-semibold
                             text-teal-700 sm:inline-flex">
                    Administrator
                </span>

                <div class="flex h-10 w-10 items-center
                            justify-center rounded-full
                            bg-[#142b61] font-bold text-white">
                    AD
                </div>
            </div>
        </header>


        <!-- PAGE CONTENT -->
        <main class="mx-auto max-w-7xl space-y-7
                     px-5 py-8 lg:px-8">

            <!-- PAGE TITLE -->
            <div class="flex flex-col gap-4
                        sm:flex-row sm:items-center
                        sm:justify-between">

                <div>
                    <h1 class="text-2xl font-extrabold
                               tracking-tight text-slate-900
                               md:text-3xl">

                        School Year Management
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Configure academic years and semester
                        periods for school forms and reporting.
                    </p>
                </div>

                <button type="button"
                        id="openCreateModal"
                        class="btn-primary">

                    <i data-lucide="plus"
                       class="h-5 w-5"></i>

                    Add School Year
                </button>
            </div>

            @if(session('error'))
                <div class="flex items-center gap-3
                            rounded-xl border border-red-200
                            bg-red-50 px-5 py-4
                            text-sm font-medium text-red-700">

                    <i data-lucide="alert-circle"
                    class="h-5 w-5 shrink-0"></i>

                    {{ session('error') }}
                </div>
            @endif


            <!-- SUCCESS MESSAGE -->
            @if(session('success'))
                <div class="flex items-center gap-3
                            rounded-xl border border-green-200
                            bg-green-50 px-5 py-4
                            text-sm font-medium text-green-700">

                    <i data-lucide="check-circle"
                       class="h-5 w-5"></i>

                    {{ session('success') }}
                </div>
            @endif


            <!-- ERROR MESSAGES -->
            @if($errors->yearEdit->any() || $errors->period->any())
                <div class="rounded-xl border border-red-200
                            bg-red-50 p-4 text-sm text-red-700">

                    @foreach($errors->yearEdit->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach

                    @foreach($errors->period->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif


            <!-- SUMMARY CARDS -->
            <div class="grid grid-cols-1 gap-5
                        sm:grid-cols-3">

                <!-- SCHOOL YEARS -->
                <div class="rounded-2xl border border-slate-200
                            bg-white p-5 shadow-sm">

                    <div class="flex items-center justify-between">

                        <div class="flex h-12 w-12 items-center
                                    justify-center rounded-xl
                                    bg-blue-50 text-blue-600">

                            <i data-lucide="calendar-days"
                               class="h-6 w-6"></i>
                        </div>

                        <span class="text-xs text-slate-400">
                            Academic Records
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-extrabold">
                        {{ $stats['school_years'] }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Total School Years
                    </p>
                </div>


                <!-- ACTIVE YEAR -->
                <div class="rounded-2xl border border-slate-200
                            bg-white p-5 shadow-sm">

                    <div class="flex items-center justify-between">

                        <div class="flex h-12 w-12 items-center
                                    justify-center rounded-xl
                                    bg-emerald-50 text-emerald-600">

                            <i data-lucide="check-circle"
                               class="h-6 w-6"></i>
                        </div>

                        <span class="text-xs text-slate-400">
                            Current Selection
                        </span>
                    </div>

                    <p class="mt-5 text-2xl font-extrabold">
                        {{ $stats['active_year'] ?? 'Not Set' }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Active School Year
                    </p>
                </div>


                <!-- PERIOD COUNT -->
                <div class="rounded-2xl border border-slate-200
                            bg-white p-5 shadow-sm">

                    <div class="flex items-center justify-between">

                        <div class="flex h-12 w-12 items-center
                                    justify-center rounded-xl
                                    bg-violet-50 text-violet-600">

                            <i data-lucide="calendar-clock"
                               class="h-6 w-6"></i>
                        </div>

                        <span class="text-xs text-slate-400">
                            All School Years
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-extrabold">
                        {{ $stats['periods'] }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Configured Semesters
                    </p>
                </div>
            </div>


            <!-- SCHOOL YEARS TABLE -->
            <section class="overflow-hidden rounded-2xl
                            border border-slate-200
                            bg-white shadow-sm">

                <div class="flex flex-col gap-3
                            border-b border-slate-100
                            px-6 py-5 sm:flex-row
                            sm:items-center sm:justify-between">

                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            School Year Records
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Manage historical and active academic years.
                        </p>
                    </div>

                    <span class="rounded-full bg-slate-100
                                 px-3 py-1.5 text-xs
                                 font-semibold text-slate-600">

                        {{ $schoolYears->total() }} Records
                    </span>
                </div>


                <div class="overflow-x-auto">

                    <table class="w-full min-w-190 text-left">

                        <thead class="bg-slate-50 text-xs
                                      font-bold uppercase
                                      tracking-wider text-slate-500">

                            <tr>
                                <th class="px-6 py-4">School Year</th>
                                <th class="px-6 py-4">Date Range</th>
                                <th class="px-6 py-4">Semesters</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            @forelse($schoolYears as $year)

                                <tr class="hover:bg-slate-50/70">

                                    <!-- SCHOOL YEAR -->
                                    <td class="px-6 py-5">

                                        <div class="flex items-center gap-3">

                                            <div class="flex h-11 w-11
                                                        shrink-0 items-center
                                                        justify-center rounded-xl
                                                        bg-blue-50 text-blue-600">

                                                <i data-lucide="calendar"
                                                   class="h-5 w-5"></i>
                                            </div>

                                            <div>
                                                <p class="text-sm
                                                          font-bold text-slate-800">

                                                    {{ $year->name }}
                                                </p>

                                                <p class="text-xs text-slate-400">
                                                    ID #{{ $year->id }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>


                                    <!-- DATE RANGE -->
                                    <td class="px-6 py-5
                                               text-sm text-slate-600">

                                        {{ $year->start_date->format('M d, Y') }}

                                        <span class="mx-1 text-slate-400">
                                            —
                                        </span>

                                        {{ $year->end_date->format('M d, Y') }}
                                    </td>


                                    <!-- SEMESTER COUNT -->
                                    <td class="px-6 py-5">

                                        <span class="rounded-full
                                                     bg-slate-100 px-3 py-1
                                                     text-xs font-semibold
                                                     text-slate-600">

                                            {{ $year->semesters->count() }}
                                            / 2 Configured
                                        </span>
                                    </td>


                                    <!-- STATUS -->
                                    <td class="px-6 py-5">

                                        @if($year->is_active)

                                            <span class="inline-flex items-center
                                                         gap-2 rounded-full
                                                         bg-emerald-50 px-3 py-1
                                                         text-xs font-semibold
                                                         text-emerald-700">

                                                <span class="h-2 w-2
                                                             rounded-full
                                                             bg-emerald-500">
                                                </span>

                                                Active
                                            </span>

                                        @else

                                            <span class="rounded-full
                                                         bg-slate-100 px-3 py-1
                                                         text-xs font-semibold
                                                         text-slate-500">

                                                Inactive
                                            </span>

                                        @endif
                                    </td>


                                    <!-- ACTIONS -->
                                    <td class="px-6 py-5">

                                        <div class="flex items-center
                                                    justify-end gap-2">

                                            <a href="{{ route('admin.school-years.index', ['year_id' => $year->id]) }}#academic-periods"
                                               class="rounded-lg bg-teal-50
                                                      px-3 py-2 text-xs
                                                      font-semibold text-teal-700
                                                      hover:bg-teal-100">

                                                Periods
                                            </a>

                                            <button type="button"
                                                    data-edit-year
                                                    data-id="{{ $year->id }}"
                                                    data-name="{{ $year->name }}"
                                                    data-start="{{ $year->start_date->format('Y-m-d') }}"
                                                    data-end="{{ $year->end_date->format('Y-m-d') }}"
                                                    data-url="{{ route('admin.school-years.update', $year) }}"
                                                    class="cursor-pointer
                                                           rounded-lg bg-blue-50
                                                           px-3 py-2 text-xs
                                                           font-semibold text-blue-700
                                                           hover:bg-blue-100">

                                                Edit
                                            </button>

                                            @unless($year->is_active)

                                                <form method="POST"
                                                      action="{{ route('admin.school-years.activate', $year) }}"
                                                      onsubmit="return confirm('Set this as the active school year?')">

                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit"
                                                            class="cursor-pointer
                                                                   rounded-lg border
                                                                   border-slate-200
                                                                   px-3 py-2 text-xs
                                                                   font-semibold text-slate-600
                                                                   hover:bg-slate-50">

                                                        Set Active
                                                    </button>
                                                </form>

                                            @endunless
                                        </div>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5"
                                        class="px-6 py-16 text-center">

                                        <i data-lucide="calendar-x"
                                           class="mx-auto h-10 w-10
                                                  text-slate-300"></i>

                                        <h4 class="mt-4 font-bold
                                                   text-slate-700">

                                            No school years yet
                                        </h4>

                                        <p class="mt-2 text-sm text-slate-500">
                                            Create your first school year
                                            to configure its academic periods.
                                        </p>
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>
                    </table>
                </div>


                <!-- PAGINATION -->
                @if($schoolYears->hasPages())
                    <div class="border-t border-slate-100
                                px-6 py-5">

                        {{ $schoolYears->links() }}
                    </div>
                @endif

            </section>


            <!-- ACADEMIC PERIOD MANAGEMENT -->
            <section id="academic-periods"
                     class="scroll-mt-26 space-y-5">

                <div class="flex flex-col gap-3
                            sm:flex-row sm:items-center
                            sm:justify-between">

                    <div>
                        <h3 class="text-xl font-bold text-slate-900">
                            Academic Period Management
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Configure semester dates for the
                            selected school year.
                        </p>
                    </div>

                    @if($selectedYear)
                        <span class="inline-flex items-center
                                     gap-2 rounded-xl bg-blue-50
                                     px-4 py-2 text-sm font-semibold
                                     text-blue-700">

                            <i data-lucide="calendar-days"
                               class="h-4 w-4"></i>

                            SY {{ $selectedYear->name }}
                        </span>
                    @endif
                </div>


                @if($selectedYear)

                    <!-- SCHOOL YEAR INFO -->
                    <div class="flex flex-col gap-3
                                rounded-xl border border-blue-100
                                bg-blue-50 p-5
                                sm:flex-row sm:items-center
                                sm:justify-between">

                        <div>
                            <p class="text-sm font-semibold
                                      text-blue-900">

                                {{ $selectedYear->name }}
                            </p>

                            <p class="mt-1 text-xs text-blue-700">
                                School year:
                                {{ $selectedYear->start_date->format('M d, Y') }}
                                —
                                {{ $selectedYear->end_date->format('M d, Y') }}
                            </p>
                        </div>

                        <span class="text-xs font-semibold
                                     text-blue-700">

                            {{ $selectedYear->is_active
                                ? 'Active School Year'
                                : 'Historical / Inactive School Year' }}
                        </span>
                    </div>


                    <!-- PERIOD CARDS -->
                    <div class="grid grid-cols-1 gap-5
                                xl:grid-cols-2">

                        @foreach([1 => 'First Semester', 2 => 'Second Semester'] as $number => $label)

                            @php
                                $semester = $selectedYear->semesters
                                    ->firstWhere('semester_number', $number);

                                $hasOld = old('semester_number') == $number;
                            @endphp

                            <div class="overflow-hidden rounded-2xl
                                        border border-slate-200
                                        bg-white shadow-sm">

                                <!-- CARD HEADER -->
                                <div class="flex items-center
                                            justify-between gap-3
                                            border-b border-slate-100
                                            px-6 py-5">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-11 w-11
                                                    items-center justify-center
                                                    rounded-xl bg-teal-50
                                                    text-teal-600">

                                            <i data-lucide="book-open"
                                               class="h-5 w-5"></i>
                                        </div>

                                        <div>
                                            <h4 class="font-bold text-slate-800">
                                                {{ $label }}
                                            </h4>

                                            <p class="mt-1 text-xs text-slate-500">
                                                Academic Period {{ $number }}
                                            </p>
                                        </div>
                                    </div>

                                    <span class="rounded-full px-3 py-1
                                                 text-xs font-semibold
                                                 {{ $semester
                                                    ? 'bg-emerald-50 text-emerald-700'
                                                    : 'bg-amber-50 text-amber-700' }}">

                                        {{ $semester ? 'Configured' : 'Not Set' }}
                                    </span>
                                </div>


                                <!-- SEMESTER FORM -->
                                <form action="{{ route('admin.school-years.periods.save', $selectedYear) }}"
                                      method="POST">

                                    @csrf

                                    <input type="hidden"
                                           name="semester_number"
                                           value="{{ $number }}">

                                    <div class="space-y-5 p-6">

                                        <!-- START DATE -->
                                        <div>
                                            <label class="form-label"
                                                   for="periodStart{{ $number }}">

                                                Semester Start Date
                                            </label>

                                            <input id="periodStart{{ $number }}"
                                                   type="date"
                                                   name="start_date"
                                                   required
                                                   min="{{ $selectedYear->start_date->format('Y-m-d') }}"
                                                   max="{{ $selectedYear->end_date->format('Y-m-d') }}"
                                                   value="{{ $hasOld ? old('start_date') : $semester?->start_date?->format('Y-m-d') }}"
                                                   class="form-input">
                                        </div>


                                        <!-- END DATE -->
                                        <div>
                                            <label class="form-label"
                                                   for="periodEnd{{ $number }}">

                                                Semester End Date
                                            </label>

                                            <input id="periodEnd{{ $number }}"
                                                   type="date"
                                                   name="end_date"
                                                   required
                                                   min="{{ $selectedYear->start_date->format('Y-m-d') }}"
                                                   max="{{ $selectedYear->end_date->format('Y-m-d') }}"
                                                   value="{{ $hasOld ? old('end_date') : $semester?->end_date?->format('Y-m-d') }}"
                                                   class="form-input">
                                        </div>


                                        <!-- DESCRIPTION -->
                                        <div class="rounded-xl
                                                    bg-slate-50 p-4">

                                            <div class="flex items-start gap-3">

                                                <i data-lucide="info"
                                                   class="mt-0.5 h-5 w-5
                                                          shrink-0 text-slate-400"></i>

                                                <p class="text-xs leading-6
                                                          text-slate-500">

                                                    Semester dates must
                                                    fall within the school
                                                    year and cannot overlap
                                                    with another semester.
                                                </p>
                                            </div>
                                        </div>


                                        <!-- SAVE -->
                                        <button type="submit"
                                                class="btn-primary w-full">

                                            <i data-lucide="save"
                                               class="h-4 w-4"></i>

                                            {{ $semester
                                                ? 'Update Semester'
                                                : 'Save Semester' }}
                                        </button>

                                    </div>
                                </form>
                            </div>

                        @endforeach
                    </div>

                @else

                    <!-- EMPTY STATE -->
                    <div class="rounded-2xl border
                                border-dashed border-slate-300
                                bg-white px-6 py-14 text-center">

                        <i data-lucide="calendar-plus"
                           class="mx-auto h-12 w-12
                                  text-slate-300"></i>

                        <h4 class="mt-4 font-bold text-slate-700">
                            No school year selected
                        </h4>

                        <p class="mt-2 text-sm text-slate-500">
                            Create a school year first,
                            then configure its semesters.
                        </p>
                    </div>

                @endif
            </section>


            <!-- FOOTER -->
            <footer class="border-t border-slate-200
                           py-6 text-center
                           text-xs text-slate-400">

                SHS Forms Management System
                &copy; {{ date('Y') }}

            </footer>

        </main>
    </div>


    <!-- CREATE SCHOOL YEAR MODAL -->
    <div id="createModal"
         role="dialog"
         aria-modal="true"
         aria-labelledby="createModalTitle"
         class="fixed inset-0 z-60 hidden
                items-center justify-center
                bg-slate-950/60 p-4">

        <div class="max-h-[90vh] w-full max-w-lg
                    overflow-y-auto rounded-2xl
                    bg-white shadow-2xl">

            <div class="flex items-center justify-between
                        border-b border-slate-100 px-6 py-5">

                <div>
                    <h3 id="createModalTitle"
                        class="text-lg font-bold">
                        Add School Year
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Create a new academic year.
                    </p>
                </div>

                <button type="button"
                        data-close-modal="createModal"
                        aria-label="Close"
                        class="cursor-pointer text-slate-400">

                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>


            <form method="POST"
                  action="{{ route('admin.school-years.store') }}">

                @csrf

                <div class="space-y-5 p-6">

                    @if($errors->yearCreate->any())
                        <div class="rounded-xl bg-red-50
                                    p-4 text-sm text-red-700">

                            @foreach($errors->yearCreate->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div>
                        <label for="createName"
                               class="form-label">
                            School Year
                        </label>

                        <input id="createName"
                               name="name"
                               type="text"
                               required
                               pattern="[0-9]{4}-[0-9]{4}"
                               value="{{ old('name') }}"
                               class="form-input"
                               placeholder="2026-2027">
                    </div>

                    <div>
                        <label for="createStart"
                               class="form-label">
                            Start Date
                        </label>

                        <input id="createStart"
                               name="start_date"
                               type="date"
                               required
                               value="{{ old('start_date') }}"
                               class="form-input">
                    </div>

                    <div>
                        <label for="createEnd"
                               class="form-label">
                            End Date
                        </label>

                        <input id="createEnd"
                               name="end_date"
                               type="date"
                               required
                               value="{{ old('end_date') }}"
                               class="form-input">
                    </div>

                    <div class="rounded-xl bg-blue-50 p-4
                                text-xs leading-6 text-blue-700">

                        The first school year will automatically
                        become active. You can change the active
                        year later.
                    </div>
                </div>

                <div class="flex justify-end gap-3
                            border-t border-slate-100 px-6 py-5">

                    <button type="button"
                            data-close-modal="createModal"
                            class="btn-outline">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn-primary">

                        <i data-lucide="plus"
                           class="h-4 w-4"></i>

                        Create School Year
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- EDIT SCHOOL YEAR MODAL -->
    <div id="editModal"
         role="dialog"
         aria-modal="true"
         aria-labelledby="editModalTitle"
         class="fixed inset-0 z-60 hidden
                items-center justify-center
                bg-slate-950/60 p-4">

        <div class="max-h-[90vh] w-full max-w-lg
                    overflow-y-auto rounded-2xl
                    bg-white shadow-2xl">

            <div class="flex items-center justify-between
                        border-b border-slate-100 px-6 py-5">

                <div>
                    <h3 id="editModalTitle"
                        class="text-lg font-bold">
                        Edit School Year
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Update academic year information.
                    </p>
                </div>

                <button type="button"
                        data-close-modal="editModal"
                        aria-label="Close"
                        class="cursor-pointer text-slate-400">

                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>


            <form id="editForm"
                  method="POST">

                @csrf
                @method('PUT')

                <input type="hidden"
                       id="editYearId"
                       name="_year_id">

                <div class="space-y-5 p-6">

                    <div>
                        <label for="editName"
                               class="form-label">
                            School Year
                        </label>

                        <input id="editName"
                               name="name"
                               type="text"
                               required
                               pattern="[0-9]{4}-[0-9]{4}"
                               class="form-input">
                    </div>

                    <div>
                        <label for="editStart"
                               class="form-label">
                            Start Date
                        </label>

                        <input id="editStart"
                               name="start_date"
                               type="date"
                               required
                               class="form-input">
                    </div>

                    <div>
                        <label for="editEnd"
                               class="form-label">
                            End Date
                        </label>

                        <input id="editEnd"
                               name="end_date"
                               type="date"
                               required
                               class="form-input">
                    </div>
                </div>

                <div class="flex justify-end gap-3
                            border-t border-slate-100 px-6 py-5">

                    <button type="button"
                            data-close-modal="editModal"
                            class="btn-outline">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn-primary">

                        <i data-lucide="save"
                           class="h-4 w-4"></i>

                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- JAVASCRIPT -->
    <script>
        lucide.createIcons();

        // MOBILE SIDEBAR
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay =
            document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }

        document.getElementById('openSidebar')
            .addEventListener('click', openSidebar);

        document.getElementById('closeSidebar')
            .addEventListener('click', closeSidebar);

        sidebarOverlay.addEventListener('click', closeSidebar);


        // MODAL HELPERS
        function showModal(id) {
            const modal = document.getElementById(id);

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            document.body.classList.add('overflow-hidden');
        }

        function hideModal(id) {
            const modal = document.getElementById(id);

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            document.body.classList.remove('overflow-hidden');
        }

        document.querySelectorAll('[data-close-modal]')
            .forEach(button => {

                button.addEventListener('click', () => {
                    hideModal(button.dataset.closeModal);
                });
            });


        // CREATE MODAL
        document.getElementById('openCreateModal')
            .addEventListener('click', () => {
                showModal('createModal');
            });


        // EDIT MODAL
        const editForm = document.getElementById('editForm');
        const editName = document.getElementById('editName');
        const editStart = document.getElementById('editStart');
        const editEnd = document.getElementById('editEnd');

        function openEditModal(button) {
            editForm.action = button.dataset.url;

            document.getElementById('editYearId').value =
                button.dataset.id;

            editName.value = button.dataset.name;
            editStart.value = button.dataset.start;
            editEnd.value = button.dataset.end;

            showModal('editModal');
        }

        document.querySelectorAll('[data-edit-year]')
            .forEach(button => {

                button.addEventListener('click', () => {
                    openEditModal(button);
                });
            });


        // REOPEN CREATE MODAL ON VALIDATION ERRORS
        @if($errors->yearCreate->any())
            showModal('createModal');
        @endif


        // REOPEN EDIT MODAL ON VALIDATION ERRORS
        @if($errors->yearEdit->any())

            const editingYearId = @json(old('_year_id'));

            const matchingYearButton = Array.from(
                document.querySelectorAll('[data-edit-year]')
            ).find(button => button.dataset.id === String(editingYearId));

            if (matchingYearButton) {
                openEditModal(matchingYearButton);

                editName.value = @json(old('name'));
                editStart.value = @json(old('start_date'));
                editEnd.value = @json(old('end_date'));
            }

        @endif


        // CLOSE MODAL WHEN CLICKING THE BACKDROP
        ['createModal', 'editModal'].forEach(id => {

            document.getElementById(id)
                .addEventListener('click', function(event) {

                    if (event.target === this) {
                        hideModal(id);
                    }
                });
        });


        // ESCAPE KEY
        document.addEventListener('keydown', event => {

            if (event.key === 'Escape') {
                hideModal('createModal');
                hideModal('editModal');
            }
        });

    </script>

</body>
</html>
