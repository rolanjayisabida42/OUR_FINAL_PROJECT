
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strands & Sections | SHS Forms</title>

    <!-- TAILWIND CSS V4 -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- INTER FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- LUCIDE ICONS -->
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
            color: #fff;
        }

        .nav-active {
            background: rgba(255,255,255,.16);
            color: white;
            font-weight: 600;
        }

        .form-input {
            width: 100%;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 11px 14px;
            font-size: 14px;
            outline: none;
        }

        .form-input:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13,148,136,.12);
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 17px;
            border-radius: 12px;
            background: #0d9488;
            color: white;
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
            padding: 10px 15px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-secondary:hover { background: #f8fafc; }
    </style>
</head>

<body class="bg-[#f5f7fb] text-slate-800">

    <!-- MOBILE OVERLAY -->
    <div id="sidebarOverlay"
         class="fixed inset-0 z-40 hidden bg-slate-950/50 lg:hidden">
    </div>

    <!-- SIDEBAR -->
    <x-admin.sidebar />

    <!-- MAIN -->
    <div class="min-h-screen lg:ml-70">

        <!-- HEADER -->
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
                    <h2 class="text-lg font-bold">Strands & Sections</h2>
                </div>
            </div>

            <div class="flex h-10 w-10 items-center justify-center
                        rounded-full bg-[#142b61] font-bold text-white">
                AD
            </div>
        </header>

        <main class="mx-auto max-w-7xl space-y-7 px-5 py-8 lg:px-8">

            <!-- PAGE HEADING -->
            <div class="flex flex-col gap-4 sm:flex-row
                        sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight
                               text-slate-900 md:text-3xl">
                        Strand & Section Management
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Organize SHS strands and Grade 11/12 sections
                        by school year.
                    </p>
                </div>

                <button id="openCreateSection"
                        type="button"
                        class="btn-primary"
                        @disabled($schoolYears->isEmpty() ||
                                  $strands->where('is_active', true)->isEmpty())>
                    <i data-lucide="plus" class="h-5 w-5"></i>
                    Add Section
                </button>
            </div>

            <!-- ALERTS -->
            @if(session('success'))
                <div class="rounded-xl border border-green-200
                            bg-green-50 px-5 py-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl border border-red-200
                            bg-red-50 px-5 py-4 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-xl border border-red-200
                            bg-red-50 px-5 py-4 text-sm text-red-700">
                    <p class="mb-2 font-bold">Please correct these errors:</p>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- STATISTICS -->
            <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">

                @foreach([
                    ['label' => 'Total Sections', 'value' => $stats['total']],
                    ['label' => 'Grade 11 Sections', 'value' => $stats['grade11']],
                    ['label' => 'Grade 12 Sections', 'value' => $stats['grade12']],
                    ['label' => 'Active Sections', 'value' => $stats['active']],
                ] as $stat)

                    <div class="rounded-2xl border border-slate-200
                                bg-white p-5 shadow-sm">
                        <div class="mb-4 flex h-10 w-10 items-center
                                    justify-center rounded-xl bg-teal-50
                                    text-teal-600">
                            <i data-lucide="school" class="h-5 w-5"></i>
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

            <!-- STRAND MANAGEMENT -->
            <section id="strands"
                     class="overflow-hidden rounded-2xl border
                            border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="text-lg font-bold">Manage Strands</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Create the academic strands available to your school.
                    </p>
                </div>

                <!-- CREATE STRAND -->
                <form action="{{ route('admin.strands.store') }}"
                      method="POST"
                      class="grid grid-cols-1 items-end gap-3 p-6 md:grid-cols-5">

                    @csrf

                    <div>
                        <label class="form-label" for="strandCode">Strand Code</label>
                        <input id="strandCode" name="code" type="text"
                               maxlength="20" required
                               value="{{ old('code') }}"
                               placeholder="STEM"
                               class="form-input uppercase">
                    </div>

                    <div class="md:col-span-3">
                        <label class="form-label" for="strandName">Strand Name</label>
                        <input id="strandName" name="name" type="text"
                               maxlength="255" required
                               value="{{ old('name') }}"
                               placeholder="Science, Technology, Engineering and Mathematics"
                               class="form-input">
                    </div>

                    <button type="submit" class="btn-primary h-11">
                        <i data-lucide="plus" class="h-4 w-4"></i>
                        Add Strand
                    </button>
                </form>

                <!-- STRAND LIST -->
                <div class="overflow-x-auto">
                    <table class="w-full min-w-140 text-left">
                        <thead class="bg-slate-50 text-xs uppercase
                                      tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Strand</th>
                                <th class="px-6 py-4">Sections</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse($strands as $strand)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-slate-800">
                                            {{ $strand->code }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $strand->name }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-4 text-sm">
                                        {{ $strand->sections_count }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-3 py-1
                                                     text-xs font-semibold
                                                     {{ $strand->is_active
                                                        ? 'bg-green-50 text-green-700'
                                                        : 'bg-red-50 text-red-700' }}">
                                            {{ $strand->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <button type="button"
                                                    data-edit-strand
                                                    data-action="{{ route('admin.strands.update', $strand) }}"
                                                    data-id="{{ $strand->id }}"
                                                    data-code="{{ $strand->code }}"
                                                    data-name="{{ $strand->name }}"
                                                    class="cursor-pointer rounded-lg
                                                           bg-blue-50 px-3 py-2
                                                           text-xs font-semibold text-blue-700">
                                                Edit
                                            </button>

                                            <form method="POST"
                                                  action="{{ route('admin.strands.status', $strand) }}"
                                                  onsubmit="return confirm('Change this strand status?')">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="cursor-pointer rounded-lg
                                                               bg-slate-100 px-3 py-2
                                                               text-xs font-semibold text-slate-700">
                                                    {{ $strand->is_active
                                                        ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="px-6 py-10 text-center
                                               text-sm text-slate-500">
                                        No strands yet. Add your first strand above.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- SECTION MANAGEMENT -->
            <section id="sections"
                     class="overflow-hidden rounded-2xl border
                            border-slate-200 bg-white shadow-sm">

                <div class="flex flex-col gap-3 border-b
                            border-slate-100 px-6 py-5
                            sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <h3 class="text-lg font-bold">Registered Sections</h3>
                        <p class="mt-1 text-xs text-slate-500">
                            Grade 11 and Grade 12 sections per school year.
                        </p>
                    </div>

                    <span class="text-xs text-slate-500">
                        {{ $sections->total() }} Records
                    </span>
                </div>

                <!-- FILTERS -->
                <form action="{{ route('admin.sections.index') }}"
                      method="GET"
                      class="grid grid-cols-1 gap-3 border-b
                             border-slate-100 p-5 sm:grid-cols-4">

                    <input type="search" name="search"
                           value="{{ request('search') }}"
                           placeholder="Search section..."
                           class="form-input">

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

                    <button type="submit" class="btn-primary">
                        <i data-lucide="search" class="h-4 w-4"></i>
                        Filter
                    </button>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-190 text-left">
                        <thead class="bg-slate-50 text-xs uppercase
                                      tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Section</th>
                                <th class="px-6 py-4">Grade</th>
                                <th class="px-6 py-4">Strand</th>
                                <th class="px-6 py-4">School Year</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse($sections as $section)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold">
                                            {{ $section->name }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            ID #{{ $section->id }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-4 text-sm">
                                        Grade {{ $section->grade_level }}
                                    </td>

                                    <td class="px-6 py-4 text-sm">
                                        {{ $section->strand->code }}
                                    </td>

                                    <td class="px-6 py-4 text-sm">
                                        {{ $section->schoolYear->name }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-3 py-1
                                                     text-xs font-semibold
                                                     {{ $section->is_active
                                                        ? 'bg-green-50 text-green-700'
                                                        : 'bg-red-50 text-red-700' }}">
                                            {{ $section->is_active
                                                ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <button type="button"
                                                    data-edit-section
                                                    data-action="{{ route('admin.sections.update', $section) }}"
                                                    data-id="{{ $section->id }}"
                                                    data-name="{{ $section->name }}"
                                                    data-grade="{{ $section->grade_level }}"
                                                    data-year="{{ $section->school_year_id }}"
                                                    data-strand="{{ $section->strand_id }}"
                                                    class="cursor-pointer rounded-lg bg-blue-50
                                                           px-3 py-2 text-xs
                                                           font-semibold text-blue-700">
                                                Edit
                                            </button>

                                            <form method="POST"
                                                  action="{{ route('admin.sections.status', $section) }}"
                                                  onsubmit="return confirm('Change this section status?')">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="cursor-pointer rounded-lg
                                                               bg-slate-100 px-3 py-2
                                                               text-xs font-semibold text-slate-700">
                                                    {{ $section->is_active
                                                        ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="px-6 py-14 text-center">
                                        <i data-lucide="school"
                                           class="mx-auto h-10 w-10 text-slate-300"></i>
                                        <p class="mt-3 font-semibold text-slate-700">
                                            No sections found
                                        </p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            Create a section or change your filters.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

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


    <!-- CREATE SECTION MODAL -->
    <div id="createSectionModal"
         role="dialog" aria-modal="true"
         aria-labelledby="createSectionTitle"
         class="fixed inset-0 z-60 hidden items-center
                justify-center bg-slate-950/60 p-4">

        <div class="max-h-[90vh] w-full max-w-lg
                    overflow-y-auto rounded-2xl bg-white shadow-2xl">

            <div class="flex items-center justify-between
                        border-b border-slate-100 p-6">
                <h3 id="createSectionTitle" class="text-lg font-bold">
                    Add New Section
                </h3>

                <button type="button" data-close-modal
                        aria-label="Close modal" class="cursor-pointer">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <form action="{{ route('admin.sections.store') }}" method="POST">
                @csrf

                <div class="space-y-5 p-6">
                    <div>
                        <label class="form-label">School Year</label>
                        <select name="school_year_id" required class="form-input">
                            <option value="">Select School Year</option>
                            @foreach($schoolYears as $year)
                                <option value="{{ $year->id }}"
                                        @selected(old('school_year_id') == $year->id)>
                                    {{ $year->name }}
                                    {{ $year->is_active ? '(Active)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Strand</label>
                        <select name="strand_id" required class="form-input">
                            <option value="">Select Strand</option>
                            @foreach($strands->where('is_active', true) as $strand)
                                <option value="{{ $strand->id }}"
                                        @selected(old('strand_id') == $strand->id)>
                                    {{ $strand->code }} - {{ $strand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Grade Level</label>
                        <select name="grade_level" required class="form-input">
                            <option value="">Select Grade</option>
                            <option value="11" @selected(old('grade_level') == '11')>
                                Grade 11
                            </option>
                            <option value="12" @selected(old('grade_level') == '12')>
                                Grade 12
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Section Name</label>
                        <input name="name" type="text" minlength="2"
                               maxlength="100" required
                               value="{{ old('name') }}"
                               placeholder="Example: STEM-A"
                               class="form-input">
                    </div>
                </div>

                <div class="flex justify-end gap-3
                            border-t border-slate-100 p-6">
                    <button type="button" data-close-modal
                            class="btn-secondary">Cancel</button>

                    <button type="submit" class="btn-primary">
                        <i data-lucide="plus" class="h-4 w-4"></i>
                        Create Section
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- EDIT SECTION MODAL -->
    <div id="editSectionModal"
         role="dialog" aria-modal="true"
         aria-labelledby="editSectionTitle"
         class="fixed inset-0 z-60 hidden items-center
                justify-center bg-slate-950/60 p-4">

        <div class="max-h-[90vh] w-full max-w-lg
                    overflow-y-auto rounded-2xl bg-white shadow-2xl">

            <div class="flex items-center justify-between
                        border-b border-slate-100 p-6">
                <h3 id="editSectionTitle" class="text-lg font-bold">
                    Edit Section
                </h3>

                <button type="button" data-close-modal
                        aria-label="Close modal" class="cursor-pointer">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <form id="editSectionForm" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="_edit_section_id"
                       id="editSectionId">

                <div class="space-y-5 p-6">
                    <div>
                        <label class="form-label">School Year</label>
                        <select id="editSectionYear" name="school_year_id"
                                required class="form-input">
                            @foreach($schoolYears as $year)
                                <option value="{{ $year->id }}">
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Strand</label>
                        <select id="editSectionStrand" name="strand_id"
                                required class="form-input">
                            @foreach($strands->where('is_active', true) as $strand)
                                <option value="{{ $strand->id }}">
                                    {{ $strand->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Grade Level</label>
                        <select id="editSectionGrade" name="grade_level"
                                required class="form-input">
                            <option value="11">Grade 11</option>
                            <option value="12">Grade 12</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Section Name</label>
                        <input id="editSectionName" name="name"
                               type="text" minlength="2" maxlength="100"
                               required class="form-input">
                    </div>
                </div>

                <div class="flex justify-end gap-3
                            border-t border-slate-100 p-6">
                    <button type="button" data-close-modal
                            class="btn-secondary">Cancel</button>

                    <button type="submit" class="btn-primary">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- EDIT STRAND MODAL -->
    <div id="editStrandModal"
         role="dialog" aria-modal="true"
         aria-labelledby="editStrandTitle"
         class="fixed inset-0 z-60 hidden items-center
                justify-center bg-slate-950/60 p-4">

        <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl">

            <div class="flex items-center justify-between
                        border-b border-slate-100 p-6">
                <h3 id="editStrandTitle" class="text-lg font-bold">
                    Edit Strand
                </h3>

                <button type="button" data-close-modal
                        aria-label="Close modal" class="cursor-pointer">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <form id="editStrandForm" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" id="editStrandId"
                       name="_edit_strand_id">

                <div class="space-y-5 p-6">
                    <div>
                        <label class="form-label">Strand Code</label>
                        <input id="editStrandCode" name="code"
                               type="text" maxlength="20" required
                               class="form-input uppercase">
                    </div>

                    <div>
                        <label class="form-label">Strand Name</label>
                        <input id="editStrandName" name="name"
                               type="text" maxlength="255" required
                               class="form-input">
                    </div>
                </div>

                <div class="flex justify-end gap-3
                            border-t border-slate-100 p-6">
                    <button type="button" data-close-modal
                            class="btn-secondary">Cancel</button>

                    <button type="submit" class="btn-primary">
                        Save Changes
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
        const overlay = document.getElementById('sidebarOverlay');

        document.getElementById('openSidebar').addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        });

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }

        document.getElementById('closeSidebar')
            .addEventListener('click', closeSidebar);

        overlay.addEventListener('click', closeSidebar);


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


        // CREATE SECTION
        document.getElementById('openCreateSection')
            .addEventListener('click', () => showModal('createSectionModal'));


        // EDIT SECTION
        function editSection(button) {
            document.getElementById('editSectionForm').action =
                button.dataset.action;

            document.getElementById('editSectionId').value =
                button.dataset.id;

            document.getElementById('editSectionName').value =
                button.dataset.name;

            document.getElementById('editSectionGrade').value =
                button.dataset.grade;

            document.getElementById('editSectionYear').value =
                button.dataset.year;

            document.getElementById('editSectionStrand').value =
                button.dataset.strand;

            showModal('editSectionModal');
        }

        document.querySelectorAll('[data-edit-section]').forEach(button => {
            button.addEventListener('click', () => editSection(button));
        });


        // EDIT STRAND
        function editStrand(button) {
            document.getElementById('editStrandForm').action =
                button.dataset.action;

            document.getElementById('editStrandId').value =
                button.dataset.id;

            document.getElementById('editStrandCode').value =
                button.dataset.code;

            document.getElementById('editStrandName').value =
                button.dataset.name;

            showModal('editStrandModal');
        }

        document.querySelectorAll('[data-edit-strand]').forEach(button => {
            button.addEventListener('click', () => editStrand(button));
        });


        // REOPEN MODALS ON VALIDATION ERROR
        @if($errors->sectionCreate->any())
            showModal('createSectionModal');
        @endif

        @if($errors->sectionUpdate->any())
            const failedSectionId = @json(old('_edit_section_id'));

            const failedSectionButton =
                Array.from(document.querySelectorAll('[data-edit-section]'))
                    .find(button => button.dataset.id === String(failedSectionId));

            if (failedSectionButton) {
                editSection(failedSectionButton);
                document.getElementById('editSectionName').value =
                    @json(old('name'));
                document.getElementById('editSectionGrade').value =
                    @json(old('grade_level'));
                document.getElementById('editSectionYear').value =
                    @json(old('school_year_id'));
                document.getElementById('editSectionStrand').value =
                    @json(old('strand_id'));
            }
        @endif

        @if($errors->strandUpdate->any())
            const failedStrandId = @json(old('_edit_strand_id'));

            const failedStrandButton =
                Array.from(document.querySelectorAll('[data-edit-strand]'))
                    .find(button => button.dataset.id === String(failedStrandId));

            if (failedStrandButton) {
                editStrand(failedStrandButton);
                document.getElementById('editStrandCode').value =
                    @json(old('code'));
                document.getElementById('editStrandName').value =
                    @json(old('name'));
            }
        @endif

        // ESC TO CLOSE
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                ['createSectionModal', 'editSectionModal', 'editStrandModal']
                    .forEach(hideModal);
            }
        });
    </script>
</body>
</html>
