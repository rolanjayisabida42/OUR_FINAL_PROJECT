
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Section Advisers | SHS Forms</title>

    <!-- TAILWIND CSS V4 -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- INTER FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect"
          href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- LUCIDE -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            color: #bfdbfe;
            font-size: 14px;
            transition: .2s;
        }

        .nav-item:hover {
            background: rgba(255,255,255,.10);
            color: white;
        }

        .nav-active {
            background: rgba(255,255,255,.16);
            color: white;
            font-weight: 600;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        .form-input {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 11px 14px;
            background: white;
            color: #334155;
            font-size: 14px;
            outline: none;
        }

        .form-input:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13,148,136,.12);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 18px;
            background: #0d9488;
            color: white;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-primary:hover { background: #0f766e; }

        .btn-primary:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .btn-secondary {
            border: 1px solid #e2e8f0;
            padding: 11px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-[#f5f7fb] text-slate-800">

    <!-- MOBILE OVERLAY -->
    <div id="sidebarOverlay"
         class="fixed inset-0 z-40 hidden bg-slate-950/50 lg:hidden">
    </div>

    <!-- SIDEBAR -->
    <x-admin.sidebar />

    <!-- MAIN CONTENT -->
    <div class="min-h-screen lg:ml-70">

        <!-- TOP HEADER -->
        <header class="sticky top-0 z-30 flex h-18 items-center
                       justify-between border-b border-slate-200
                       bg-white/95 px-5 backdrop-blur lg:px-8">

            <div class="flex items-center gap-4">
                <button id="openSidebar" type="button"
                        aria-label="Open sidebar"
                        class="rounded-lg p-2 hover:bg-slate-100 lg:hidden">
                    <i data-lucide="menu" class="h-6 w-6"></i>
                </button>

                <div>
                    <p class="text-xs text-slate-400">
                        Admin / Academic Management
                    </p>
                    <h2 class="text-lg font-bold">Section Advisers</h2>
                </div>
            </div>

            <div class="flex h-10 w-10 items-center justify-center
                        rounded-full bg-[#142b61] font-bold text-white">
                AD
            </div>
        </header>

        <!-- CONTENT -->
        <main class="mx-auto max-w-7xl space-y-7 px-5 py-8 lg:px-8">

            <!-- PAGE HEADING -->
            <div class="flex flex-col gap-4
                        sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight
                               text-slate-900 md:text-3xl">
                        Assign Section Advisers
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Assign teachers to their respective Grade 11
                        and Grade 12 sections.
                    </p>
                </div>

                <button id="openAssignModal" type="button"
                        class="btn-primary"
                        @disabled(
                            $availableSections->isEmpty() ||
                            $teachers->isEmpty()
                        )>

                    <i data-lucide="user-plus" class="h-5 w-5"></i>
                    Assign Adviser
                </button>
            </div>

            <!-- ACTIVE SCHOOL YEAR -->
            <div class="flex flex-wrap items-center justify-between
                        gap-3 rounded-xl border border-blue-100
                        bg-blue-50 p-5">

                <div class="flex items-center gap-3">
                    <i data-lucide="calendar-check"
                       class="h-6 w-6 text-blue-600"></i>

                    <div>
                        <p class="text-sm font-bold text-blue-900">
                            Active School Year
                        </p>

                        <p class="mt-1 text-xs text-blue-700">
                            {{ $activeYear?->name ?? 'No active school year' }}
                        </p>
                    </div>
                </div>

                <span class="rounded-full bg-white px-3 py-1.5
                             text-xs font-semibold text-blue-700">
                    Current Assignments
                </span>
            </div>

            <!-- MESSAGES -->
            @if(session('success'))
                <div class="rounded-xl border border-green-200
                            bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl border border-red-200
                            bg-red-50 p-4 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if(
                $errors->assignmentCreate->any() ||
                $errors->assignmentUpdate->any()
            )
                <div class="rounded-xl border border-red-200
                            bg-red-50 p-4 text-sm text-red-700">

                    <p class="mb-2 font-bold">
                        Please correct the following:
                    </p>

                    @foreach($errors->assignmentCreate->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach

                    @foreach($errors->assignmentUpdate->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if(!$activeYear)
                <div class="rounded-xl border border-amber-200
                            bg-amber-50 p-4 text-sm text-amber-800">
                    Activate a school year before assigning advisers.
                </div>
            @elseif($teachers->isEmpty())
                <div class="rounded-xl border border-amber-200
                            bg-amber-50 p-4 text-sm text-amber-800">
                    No eligible active teachers found. Create or activate
                    a Teacher account and configure its grade access.
                </div>
            @elseif($availableSections->isEmpty())
                <div class="rounded-xl border border-blue-100
                            bg-blue-50 p-4 text-sm text-blue-800">
                    No unassigned active sections are currently available
                    in the active school year.
                </div>
            @endif

            <!-- SUMMARY -->
            <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">

                @foreach([
                    ['label' => 'Total Sections',
                     'value' => $stats['total'],
                     'icon' => 'school'],

                    ['label' => 'Assigned Sections',
                     'value' => $stats['assigned'],
                     'icon' => 'check-circle'],

                    ['label' => 'Unassigned Sections',
                     'value' => $stats['unassigned'],
                     'icon' => 'user-x'],

                    ['label' => 'Eligible Teachers',
                     'value' => $stats['teachers'],
                     'icon' => 'users'],
                ] as $stat)

                    <div class="rounded-2xl border border-slate-200
                                bg-white p-5 shadow-sm">

                        <div class="mb-4 flex h-11 w-11 items-center
                                    justify-center rounded-xl bg-teal-50
                                    text-teal-600">
                            <i data-lucide="{{ $stat['icon'] }}"
                               class="h-5 w-5"></i>
                        </div>

                        <p class="text-3xl font-extrabold">
                            {{ $stat['value'] }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $stat['label'] }}
                        </p>
                    </div>
                @endforeach
            </div>

            <!-- ASSIGNMENTS TABLE -->
            <section class="overflow-hidden rounded-2xl
                            border border-slate-200 bg-white shadow-sm">

                <div class="flex flex-col gap-2 border-b
                            border-slate-100 px-6 py-5
                            sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Section Assignments
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            View assigned and unassigned sections.
                        </p>
                    </div>

                    <span class="text-xs text-slate-500">
                        {{ $sections->total() }} Results
                    </span>
                </div>

                <!-- FILTERS -->
                <form method="GET"
                      action="{{ route('admin.advisers.index') }}"
                      class="grid grid-cols-1 gap-3 border-b
                             border-slate-100 p-5 md:grid-cols-5">

                    <input type="search" name="search"
                           value="{{ request('search') }}"
                           placeholder="Search section or adviser..."
                           class="form-input md:col-span-2">

                    <select name="school_year_id" class="form-input">
                        <option value="">All School Years</option>

                        @foreach($schoolYears as $year)
                            <option value="{{ $year->id }}"
                                    @selected(request('school_year_id') == $year->id)>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="grade_level" class="form-input">
                        <option value="">All Grades</option>
                        <option value="11"
                                @selected(request('grade_level') == '11')>
                            Grade 11
                        </option>
                        <option value="12"
                                @selected(request('grade_level') == '12')>
                            Grade 12
                        </option>
                    </select>

                    <select name="status" class="form-input">
                        <option value="">All Assignments</option>
                        <option value="assigned"
                                @selected(request('status') == 'assigned')>
                            Assigned
                        </option>
                        <option value="unassigned"
                                @selected(request('status') == 'unassigned')>
                            Unassigned
                        </option>
                    </select>

                    <div class="flex gap-3 md:col-span-5">
                        <button type="submit" class="btn-primary">
                            <i data-lucide="search" class="h-4 w-4"></i>
                            Apply Filters
                        </button>

                        <a href="{{ route('admin.advisers.index') }}"
                           class="btn-secondary">
                            Reset
                        </a>
                    </div>
                </form>

                <!-- TABLE -->
                <div class="overflow-x-auto">
                    <table class="w-full min-w-210 text-left">

                        <thead class="bg-slate-50 text-xs uppercase
                                      tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Section</th>
                                <th class="px-6 py-4">Grade / Strand</th>
                                <th class="px-6 py-4">School Year</th>
                                <th class="px-6 py-4">Adviser</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            @forelse($sections as $section)

                                @php
                                    $manageable =
                                        $section->is_active &&
                                        $section->schoolYear?->is_active &&
                                        $section->strand?->is_active;
                                @endphp

                                <tr class="hover:bg-slate-50/70">

                                    <!-- SECTION -->
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">

                                            <div class="flex h-11 w-11
                                                        shrink-0 items-center
                                                        justify-center
                                                        rounded-xl bg-blue-50
                                                        text-blue-600">
                                                <i data-lucide="school"
                                                   class="h-5 w-5"></i>
                                            </div>

                                            <div>
                                                <p class="text-sm font-bold">
                                                    {{ $section->name }}
                                                </p>
                                                <p class="mt-1 text-xs text-slate-400">
                                                    Section #{{ $section->id }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- GRADE -->
                                    <td class="px-6 py-5">
                                        <p class="text-sm font-semibold">
                                            Grade {{ $section->grade_level }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $section->strand?->code ?? '—' }}
                                        </p>
                                    </td>

                                    <!-- SCHOOL YEAR -->
                                    <td class="px-6 py-5 text-sm text-slate-600">
                                        {{ $section->schoolYear?->name ?? '—' }}
                                    </td>

                                    <!-- TEACHER -->
                                    <td class="px-6 py-5">

                                        @if($section->adviser?->teacher)

                                            <p class="text-sm font-semibold
                                                      text-slate-800">
                                                {{ $section->adviser->teacher->name }}
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $section->adviser->teacher->email }}
                                            </p>

                                        @else

                                            <span class="text-sm text-slate-400">
                                                No adviser assigned
                                            </span>

                                        @endif
                                    </td>

                                    <!-- ASSIGNMENT STATUS -->
                                    <td class="px-6 py-5">
                                        @if($section->adviser)
                                            <span class="rounded-full
                                                         bg-emerald-50
                                                         px-3 py-1 text-xs
                                                         font-semibold
                                                         text-emerald-700">
                                                Assigned
                                            </span>
                                        @else
                                            <span class="rounded-full
                                                         bg-amber-50
                                                         px-3 py-1 text-xs
                                                         font-semibold
                                                         text-amber-700">
                                                Unassigned
                                            </span>
                                        @endif
                                    </td>

                                    <!-- ACTIONS -->
                                    <td class="px-6 py-5">

                                        <div class="flex items-center
                                                    justify-end gap-2">

                                            @if($section->adviser && $manageable)

                                                <button type="button"
                                                        data-reassign
                                                        data-id="{{ $section->adviser->id }}"
                                                        data-action="{{ route('admin.advisers.update', $section->adviser) }}"
                                                        data-section="{{ $section->name }}"
                                                        data-grade="{{ $section->grade_level }}"
                                                        data-teacher="{{ $section->adviser->teacher_id }}"
                                                        class="cursor-pointer rounded-lg
                                                               bg-blue-50 px-3 py-2
                                                               text-xs font-semibold
                                                               text-blue-700 hover:bg-blue-100">
                                                    Reassign
                                                </button>

                                                <form method="POST"
                                                      action="{{ route('admin.advisers.destroy', $section->adviser) }}"
                                                      onsubmit="return confirm('Remove this adviser from the section?')">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="cursor-pointer rounded-lg
                                                                   bg-red-50 px-3 py-2
                                                                   text-xs font-semibold
                                                                   text-red-700 hover:bg-red-100">
                                                        Unassign
                                                    </button>
                                                </form>

                                            @elseif(!$section->adviser && $manageable)

                                                <button type="button"
                                                        data-assign-section
                                                        data-section="{{ $section->id }}"
                                                        class="cursor-pointer rounded-lg
                                                               bg-teal-50 px-3 py-2
                                                               text-xs font-semibold
                                                               text-teal-700 hover:bg-teal-100">
                                                    Assign
                                                </button>

                                            @else

                                                <span class="text-xs text-slate-400">
                                                    Read only
                                                </span>

                                            @endif
                                        </div>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6"
                                        class="px-6 py-14 text-center">
                                        <i data-lucide="user-round-x"
                                           class="mx-auto h-10 w-10 text-slate-300">
                                        </i>

                                        <p class="mt-4 font-semibold text-slate-700">
                                            No sections found
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Create sections first or change your filters.
                                        </p>
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                @if($sections->hasPages())
                    <div class="border-t border-slate-100 p-5">
                        {{ $sections->links() }}
                    </div>
                @endif
            </section>

            <footer class="py-5 text-center text-xs text-slate-400">
                SHS Forms Management System &copy; {{ date('Y') }}
            </footer>
        </main>
    </div>


    <!-- ASSIGN ADVISER MODAL -->
    <div id="assignModal" role="dialog" aria-modal="true"
         aria-labelledby="assignModalTitle"
         class="fixed inset-0 z-60 hidden items-center
                justify-center bg-slate-950/60 p-4">

        <div class="max-h-[90vh] w-full max-w-lg
                    overflow-y-auto rounded-2xl bg-white shadow-2xl">

            <div class="flex items-center justify-between
                        border-b border-slate-100 p-6">
                <div>
                    <h3 id="assignModalTitle" class="text-lg font-bold">
                        Assign Section Adviser
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Select a section and an eligible Teacher.
                    </p>
                </div>

                <button type="button" data-close-modal
                        aria-label="Close modal"
                        class="cursor-pointer text-slate-400">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <form action="{{ route('admin.advisers.store') }}"
                  method="POST">

                @csrf

                <div class="space-y-5 p-6">

                    <div>
                        <label class="form-label" for="assignSection">
                            Select Section
                        </label>

                        <select name="section_id" id="assignSection"
                                class="form-input" required>

                            <option value="">Choose a Section</option>

                            @foreach($availableSections as $section)
                                <option value="{{ $section->id }}"
                                        data-grade="{{ $section->grade_level }}"
                                        @selected(old('section_id') == $section->id)>

                                    Grade {{ $section->grade_level }}
                                    - {{ $section->name }}
                                    ({{ $section->strand?->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label" for="assignTeacher">
                            Select Teacher
                        </label>

                        <select name="teacher_id" id="assignTeacher"
                                class="form-input" required>

                            <option value="">Choose a Teacher</option>

                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}"
                                        data-grade="{{ $teacher->grade_access }}"
                                        @selected(old('teacher_id') == $teacher->id)>

                                    {{ $teacher->name }}
                                    (Grade {{ $teacher->grade_access === 'both'
                                        ? '11 & 12'
                                        : $teacher->grade_access }})
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-slate-500">
                            Only teachers with matching grade access
                            can be selected.
                        </p>
                    </div>

                    <div class="rounded-xl bg-blue-50 p-4
                                text-xs leading-6 text-blue-700">
                        Each section can have only one adviser.
                        A teacher may advise multiple sections.
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t
                            border-slate-100 p-6">

                    <button type="button" data-close-modal
                            class="btn-secondary">
                        Cancel
                    </button>

                    <button type="submit" class="btn-primary">
                        <i data-lucide="user-check" class="h-4 w-4"></i>
                        Save Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- REASSIGN ADVISER MODAL -->
    <div id="reassignModal" role="dialog" aria-modal="true"
         aria-labelledby="reassignModalTitle"
         class="fixed inset-0 z-60 hidden items-center
                justify-center bg-slate-950/60 p-4">

        <div class="max-h-[90vh] w-full max-w-lg
                    overflow-y-auto rounded-2xl bg-white shadow-2xl">

            <div class="flex items-center justify-between
                        border-b border-slate-100 p-6">

                <div>
                    <h3 id="reassignModalTitle" class="text-lg font-bold">
                        Reassign Section Adviser
                    </h3>

                    <p class="mt-1 text-xs text-slate-500"
                       id="reassignSectionName">
                    </p>
                </div>

                <button type="button" data-close-modal
                        aria-label="Close modal"
                        class="cursor-pointer text-slate-400">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <form id="reassignForm" method="POST">

                @csrf
                @method('PUT')

                <input type="hidden" name="_assignment_id"
                       id="reassignAssignmentId">

                <div class="space-y-5 p-6">

                    <div>
                        <label class="form-label" for="reassignTeacher">
                            New Adviser
                        </label>

                        <select name="teacher_id" id="reassignTeacher"
                                class="form-input" required>

                            <option value="">Choose a Teacher</option>

                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}"
                                        data-grade="{{ $teacher->grade_access }}">

                                    {{ $teacher->name }}
                                    (Grade {{ $teacher->grade_access === 'both'
                                        ? '11 & 12'
                                        : $teacher->grade_access }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="rounded-xl bg-amber-50 p-4
                                text-xs leading-6 text-amber-800">
                        Reassigning will replace the current adviser
                        for this section. Previous adviser changes
                        are not yet stored as an audit history.
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t
                            border-slate-100 p-6">

                    <button type="button" data-close-modal
                            class="btn-secondary">
                        Cancel
                    </button>

                    <button type="submit" class="btn-primary">
                        Save Reassignment
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- JAVASCRIPT -->
    <script>
        lucide.createIcons();

        // SIDEBAR
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        document.getElementById('openSidebar')
            .addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
            });

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }

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

        document.querySelectorAll('[data-close-modal]').forEach(button => {
            button.addEventListener('click', () => {
                hideModal(button.closest('[role="dialog"]').id);
            });
        });

        ['assignModal', 'reassignModal'].forEach(id => {
            document.getElementById(id).addEventListener('click', event => {
                if (event.target.id === id) hideModal(id);
            });
        });


        // FILTER TEACHERS BY GRADE ACCESS
        function filterTeachers(select, grade) {
            Array.from(select.options).forEach(option => {
                if (!option.value) return;

                const teacherGrade = option.dataset.grade;

                option.disabled = (
                    !grade ||
                    (teacherGrade !== 'both' && teacherGrade !== grade)
                );

                option.hidden = option.disabled;
            });

            const selected = select.selectedOptions[0];

            if (selected && selected.disabled) {
                select.value = '';
            }
        }


        // ASSIGN MODAL
        const assignSection = document.getElementById('assignSection');
        const assignTeacher = document.getElementById('assignTeacher');

        function refreshAssignTeachers() {
            const grade = assignSection.selectedOptions[0]?.dataset.grade;
            filterTeachers(assignTeacher, grade);
        }

        assignSection.addEventListener('change', refreshAssignTeachers);

        document.getElementById('openAssignModal')
            .addEventListener('click', () => {
                refreshAssignTeachers();
                showModal('assignModal');
            });

        document.querySelectorAll('[data-assign-section]')
            .forEach(button => {
                button.addEventListener('click', () => {
                    assignSection.value = button.dataset.section;
                    refreshAssignTeachers();
                    showModal('assignModal');
                });
            });


        // REASSIGN MODAL
        const reassignTeacher = document.getElementById('reassignTeacher');

        function openReassign(button) {
            document.getElementById('reassignForm').action =
                button.dataset.action;

            document.getElementById('reassignAssignmentId').value =
                button.dataset.id;

            document.getElementById('reassignSectionName').textContent =
                'Section: ' + button.dataset.section;

            filterTeachers(reassignTeacher, button.dataset.grade);

            const currentTeacher = button.dataset.teacher;
            const option = Array.from(reassignTeacher.options)
                .find(item => item.value === currentTeacher);

            reassignTeacher.value = option && !option.disabled
                ? currentTeacher
                : '';

            showModal('reassignModal');
        }

        document.querySelectorAll('[data-reassign]')
            .forEach(button => {
                button.addEventListener('click', () => {
                    openReassign(button);
                });
            });


        // REOPEN MODALS AFTER VALIDATION FAILURE
        @if($errors->assignmentCreate->any())
            refreshAssignTeachers();
            showModal('assignModal');
        @endif

        @if($errors->assignmentUpdate->any())
            const failedAssignmentId = @json(old('_assignment_id'));

            const failedButton = Array.from(
                document.querySelectorAll('[data-reassign]')
            ).find(button => button.dataset.id === String(failedAssignmentId));

            if (failedButton) {
                openReassign(failedButton);
                reassignTeacher.value = @json(old('teacher_id'));
            }
        @endif


        // ESCAPE KEY
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                hideModal('assignModal');
                hideModal('reassignModal');
            }
        });
    </script>

</body>
</html>
