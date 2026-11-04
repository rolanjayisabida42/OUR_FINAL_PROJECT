
@extends('layouts.principal-review')

@section('title', 'Submission Settings | SHS Forms')
@section('heading', 'Submission Settings')

@section('content')

@php
    $defaultRows = [
        [
            'id' => '',
            'name' => 'SF1',
            'description' => 'School Register',
        ],
        [
            'id' => '',
            'name' => 'SF2',
            'description' => 'Attendance Report',
        ],
        [
            'id' => '',
            'name' => 'SF5',
            'description' => 'Learner Status Report',
        ],
        [
            'id' => '',
            'name' => 'Master Sheet',
            'description' => 'Consolidated Academic Grades',
        ],
    ];

    $savedRows = $setting
        ? $setting->requirements->map(fn ($item) => [
            'id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
        ])->all()
        : $defaultRows;

    $rows = old('requirements', $savedRows);

    $localOpening = $setting?->opens_at
        ? $setting->opens_at->copy()
            ->timezone('Asia/Manila')
            ->format('Y-m-d\TH:i')
        : '';

    $localDeadline = $setting?->deadline_at
        ? $setting->deadline_at->copy()
            ->timezone('Asia/Manila')
            ->format('Y-m-d\TH:i')
        : '';

    $currentStatus = $setting?->status ?? 'draft';

    $now = now('UTC');

    $periodAvailability = 'Not yet configured';

    if ($setting) {
        if ($setting->status === 'draft') {
            $periodAvailability = 'Not activated';
        } elseif ($setting->status === 'closed') {
            $periodAvailability = 'Closed';
        } elseif (!$setting->opens_at || !$setting->deadline_at) {
            $periodAvailability = 'Dates incomplete';
        } elseif ($now->lt($setting->opens_at)) {
            $periodAvailability = 'Scheduled';
        } elseif ($now->gte($setting->deadline_at)) {
            $periodAvailability = 'Deadline passed';
        } elseif ($setting->status === 'active') {
            $periodAvailability = 'Open for submissions';
        } else {
            $periodAvailability = 'Unavailable';
        }
    }

    $statusClass = match($currentStatus) {
        'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'closed' => 'bg-slate-100 text-slate-600 border-slate-200',
        default => 'bg-amber-50 text-amber-700 border-amber-200',
    };
@endphp

<div class="mx-auto max-w-7xl space-y-7">

    <!-- ======================================
         PAGE HEADER
    ======================================= -->

    <div class="flex flex-wrap items-start justify-between gap-4">

        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-teal-700">
                Academic Requirements
            </p>

            <h1 class="mt-2 text-2xl font-extrabold text-slate-900 md:text-3xl">
                Submission Management
            </h1>

            <p class="mt-2 text-sm leading-6 text-slate-500">
                Configure semester submission periods,
                requirements, and deadlines for teachers.
            </p>
        </div>

        <a href="{{ route('principal.submissions.review.index') }}"
           class="inline-flex items-center gap-2 rounded-xl
                  border border-slate-200 bg-white px-4 py-3
                  text-sm font-bold text-slate-700
                  hover:border-teal-300 hover:text-teal-700">

            <i data-lucide="inbox"
               class="h-4 w-4"></i>

            Review Submissions
        </a>

    </div>


    <!-- ======================================
         EMPTY SCHOOL YEAR STATE
    ======================================= -->

    @if(!$selectedYear)

        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-8">

            <div class="flex items-start gap-4">

                <div class="rounded-xl bg-amber-100 p-3 text-amber-700">
                    <i data-lucide="calendar-x"
                       class="h-6 w-6"></i>
                </div>

                <div>

                    <h2 class="text-lg font-bold text-amber-900">
                        No School Year Available
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-amber-800">
                        An Administrator must create a school
                        year before the Principal can configure
                        submission requirements.
                    </p>

                </div>

            </div>

        </section>

    @else


        <!-- ======================================
             SCHOOL YEAR SELECTOR
        ======================================= -->

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex flex-wrap items-end justify-between gap-5">

                <div>

                    <h2 class="text-lg font-extrabold text-slate-900">
                        School Year
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Select the academic year you want to configure.
                    </p>

                </div>


                <div class="w-full sm:max-w-xs">

                    <label for="yearSelect"
                           class="mb-2 block text-xs font-bold text-slate-600">

                        Academic Year

                    </label>

                    <select id="yearSelect"
                            class="w-full rounded-xl border
                                   border-slate-300 bg-white
                                   px-4 py-3 text-sm outline-none
                                   focus:border-teal-500">

                        @foreach($years as $year)

                            <option
                                value="{{ route('principal.submissions.index', [
                                    'year' => $year->id,
                                    'semester' => 1
                                ]) }}"
                                @selected((int) $selectedYear->id === (int) $year->id)>

                                {{ $year->name }}
                                {{ $year->is_active ? '(Active)' : '' }}

                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

        </section>


        <!-- ======================================
             SEMESTER SELECTOR
        ======================================= -->

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

            @foreach([1, 2] as $number)

                @php
                    $period = $selectedYear->semesters
                        ->firstWhere('semester_number', $number);

                    $isSelected = (int) $semesterNumber === $number;
                @endphp

                <a href="{{ route('principal.submissions.index', [
                        'year' => $selectedYear->id,
                        'semester' => $number
                    ]) }}"
                   class="rounded-2xl border-2 p-6 shadow-sm transition
                          {{ $isSelected
                              ? 'border-teal-500 bg-[#142b61] text-white'
                              : 'border-slate-200 bg-white text-slate-900 hover:border-teal-300' }}">

                    <p class="text-xs font-bold uppercase tracking-widest
                              {{ $isSelected ? 'text-blue-200' : 'text-slate-400' }}">

                        Semester {{ str_pad($number, 2, '0', STR_PAD_LEFT) }}

                    </p>

                    <div class="mt-3 flex items-center justify-between gap-3">

                        <h3 class="text-xl font-extrabold">
                            {{ $number === 1 ? '1st Semester' : '2nd Semester' }}
                        </h3>

                        <i data-lucide="{{ $isSelected ? 'check-circle-2' : 'calendar-days' }}"
                           class="h-5 w-5"></i>

                    </div>

                    <p class="mt-3 text-xs
                              {{ $isSelected ? 'text-blue-100' : 'text-slate-500' }}">

                        {{ $period
                            ? 'Academic semester configured'
                            : 'Academic semester not yet configured' }}

                    </p>

                </a>

            @endforeach

        </div>


        <!-- ======================================
             MISSING ACADEMIC SEMESTER
        ======================================= -->

        @if(!$selectedSemester)

            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-6">

                <div class="flex items-start gap-4">

                    <i data-lucide="calendar-x"
                       class="h-6 w-6 shrink-0 text-amber-700"></i>

                    <div>

                        <h2 class="text-base font-bold text-amber-900">
                            Semester Not Yet Created
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-amber-800">

                            The Administrator must configure
                            this academic semester under
                            School Year Management.

                            After that, the Principal can
                            define its submission requirements.

                        </p>

                    </div>

                </div>

            </section>

        @else


            <!-- ======================================
                 CONFIGURATION SUMMARY
            ======================================= -->

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="flex flex-wrap items-start justify-between gap-4">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-widest text-teal-700">
                            Current Configuration
                        </p>

                        <h2 class="mt-2 text-xl font-extrabold text-slate-900">
                            {{ $semesterNumber === 1 ? '1st Semester' : '2nd Semester' }}
                            Requirements
                        </h2>

                        <p class="mt-2 text-sm text-slate-500">

                            {{ $setting
                                ? $setting->requirements->count() . ' saved requirements'
                                : 'No saved configuration yet' }}

                        </p>

                    </div>


                    <span class="inline-flex items-center rounded-full
                                 border px-4 py-2 text-xs font-bold
                                 {{ $statusClass }}">

                        {{ ucfirst($currentStatus) }}

                    </span>

                </div>


                <!-- SUMMARY CARDS -->
                <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">


                    <!-- CONFIGURATION STATUS -->
                    <div class="rounded-xl bg-slate-50 p-4">

                        <p class="text-xs font-semibold text-slate-500">
                            Configuration Status
                        </p>

                        <p class="mt-2 text-base font-extrabold text-[#142b61]">
                            {{ ucfirst($currentStatus) }}
                        </p>

                    </div>


                    <!-- TEACHER AVAILABILITY -->
                    <div class="rounded-xl bg-slate-50 p-4">

                        <p class="text-xs font-semibold text-slate-500">
                            Submission Availability
                        </p>

                        <p class="mt-2 text-base font-extrabold
                                  {{ $periodAvailability === 'Open for submissions'
                                      ? 'text-emerald-700'
                                      : 'text-amber-700' }}">

                            {{ $periodAvailability }}

                        </p>

                    </div>


                    <!-- GRADE COVERAGE -->
                    <div class="rounded-xl bg-slate-50 p-4">

                        <p class="text-xs font-semibold text-slate-500">
                            Grade Coverage
                        </p>

                        <p class="mt-2 text-base font-extrabold text-[#142b61]">

                            @if($setting?->grade_scope === 'both')
                                Grades 11 & 12
                            @elseif($setting?->grade_scope === '11')
                                Grade 11
                            @elseif($setting?->grade_scope === '12')
                                Grade 12
                            @else
                                Not configured
                            @endif

                        </p>

                    </div>

                </div>


                <p class="mt-5 text-xs leading-5 text-slate-500">

                    Dates and times are displayed in Philippine
                    Standard Time (Asia/Manila).

                    An Active configuration allows teacher uploads
                    only between the opening date and deadline.

                </p>

            </section>


            <!-- ======================================
                 CONFIGURATION FORM
            ======================================= -->

            <form id="submissionSettingsForm"
                  method="POST"
                  action="{{ route('principal.submissions.update', $selectedSemester) }}"
                  class="overflow-hidden rounded-2xl border
                         border-slate-200 bg-white shadow-sm">

                @csrf
                @method('PUT')


                <!-- FORM HEADER -->
                <div class="bg-[#142b61] px-6 py-7 text-white">

                    <p class="text-xs font-bold uppercase tracking-widest text-blue-200">
                        Semester Settings
                    </p>

                    <h2 class="mt-2 text-xl font-extrabold">
                        Configure Submission Requirements
                    </h2>

                    <p class="mt-2 text-xs leading-5 text-blue-100">
                        Specify grade coverage, required documents,
                        instructions, and submission deadlines.
                    </p>

                </div>


                <div class="space-y-9 p-6 md:p-8">


                    <!-- ==================================
                         GRADE COVERAGE
                    =================================== -->

                    <section>

                        <h3 class="text-base font-extrabold text-slate-900">
                            Applicable Grade Levels
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Select which grade levels must submit these requirements.
                        </p>

                        <select name="grade_scope"
                                required
                                class="mt-4 w-full rounded-xl border
                                       border-slate-300 bg-white px-4 py-3
                                       text-sm outline-none
                                       focus:border-teal-500">

                            <option value="both"
                                @selected(old('grade_scope', $setting?->grade_scope ?? 'both') === 'both')>

                                Grade 11 and Grade 12

                            </option>

                            <option value="11"
                                @selected(old('grade_scope', $setting?->grade_scope ?? 'both') === '11')>

                                Grade 11 Only

                            </option>

                            <option value="12"
                                @selected(old('grade_scope', $setting?->grade_scope ?? 'both') === '12')>

                                Grade 12 Only

                            </option>

                        </select>

                    </section>


                    <!-- ==================================
                         OPENING AND DEADLINE
                    =================================== -->

                    <section>

                        <h3 class="text-base font-extrabold text-slate-900">
                            Submission Schedule
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Set the period during which teachers can upload and submit.
                        </p>


                        <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">


                            <!-- OPENING DATE -->
                            <div>

                                <label for="opening_local"
                                       class="mb-2 block text-xs font-bold text-slate-700">

                                    Opening Date and Time

                                </label>

                                <input id="opening_local"
                                       type="datetime-local"
                                       name="opening_local"
                                       value="{{ old('opening_local', $localOpening) }}"
                                       class="w-full rounded-xl border border-slate-300
                                              bg-white px-4 py-3 text-sm outline-none
                                              focus:border-teal-500">

                                <p class="mt-2 text-xs text-slate-400">
                                    Teachers cannot submit before this time.
                                </p>

                            </div>


                            <!-- DEADLINE -->
                            <div>

                                <label for="deadline_local"
                                       class="mb-2 block text-xs font-bold text-slate-700">

                                    Submission Deadline

                                </label>

                                <input id="deadline_local"
                                       type="datetime-local"
                                       name="deadline_local"
                                       value="{{ old('deadline_local', $localDeadline) }}"
                                       class="w-full rounded-xl border border-slate-300
                                              bg-white px-4 py-3 text-sm outline-none
                                              focus:border-teal-500">

                                <p class="mt-2 text-xs text-slate-400">
                                    Uploading stops when the deadline is reached.
                                </p>

                            </div>

                        </div>


                        <!-- DATE REMINDER -->
                        <div class="mt-5 flex items-start gap-3 rounded-xl
                                    border border-blue-200 bg-blue-50 p-4">

                            <i data-lucide="info"
                               class="h-5 w-5 shrink-0 text-blue-700"></i>

                            <p class="text-xs leading-5 text-blue-800">

                                Activating a submission period does not
                                automatically open it.

                                For teachers to upload immediately,
                                the opening date must be in the past
                                and the deadline must be in the future.

                            </p>

                        </div>

                    </section>


                    <!-- ==================================
                         REQUIRED DOCUMENTS
                    =================================== -->

                    <section>

                        <div class="flex flex-wrap items-center justify-between gap-4">

                            <div>

                                <h3 class="text-lg font-extrabold text-slate-900">
                                    Required Documents
                                </h3>

                                <p class="mt-2 text-xs leading-5 text-slate-500">
                                    Add, edit, or remove required documents.
                                    Maximum of 20 per semester.
                                </p>

                            </div>


                            <button id="addRequirement"
                                    type="button"
                                    class="inline-flex cursor-pointer
                                           items-center gap-2 rounded-xl
                                           bg-[#0d9488] px-5 py-3
                                           text-xs font-bold text-white
                                           transition hover:bg-[#0f766e]">

                                <i data-lucide="plus"
                                   class="h-4 w-4"></i>

                                Add Requirement

                            </button>

                        </div>


                        <!-- REQUIREMENT ROWS -->
                        <div id="requirementsList"
                             class="mt-6 space-y-3">

                            @foreach($rows as $index => $row)

                                <div data-requirement-row
                                     class="rounded-xl border border-slate-200
                                            bg-slate-50 p-5">

                                    <input type="hidden"
                                           data-field="id"
                                           name="requirements[{{ $index }}][id]"
                                           value="{{ $row['id'] ?? '' }}">


                                    <div class="flex items-start justify-between gap-3">

                                        <p data-requirement-number
                                           class="text-xs font-bold uppercase
                                                  tracking-widest text-teal-700">

                                            Requirement {{ $index + 1 }}

                                        </p>


                                        <button type="button"
                                                data-remove-requirement
                                                class="cursor-pointer text-xs
                                                       font-bold text-red-600
                                                       hover:text-red-800">

                                            Remove

                                        </button>

                                    </div>


                                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">


                                        <!-- NAME -->
                                        <div>

                                            <label class="mb-2 block text-xs font-bold text-slate-600">
                                                Document Name
                                            </label>

                                            <input type="text"
                                                   data-field="name"
                                                   name="requirements[{{ $index }}][name]"
                                                   value="{{ $row['name'] ?? '' }}"
                                                   maxlength="120"
                                                   required
                                                   placeholder="e.g. SF1, SF2, Master Sheet"
                                                   class="w-full rounded-xl
                                                          border border-slate-300
                                                          bg-white px-4 py-3 text-sm
                                                          outline-none focus:border-teal-500">

                                        </div>


                                        <!-- DESCRIPTION -->
                                        <div>

                                            <label class="mb-2 block text-xs font-bold text-slate-600">
                                                Description (Optional)
                                            </label>

                                            <input type="text"
                                                   data-field="description"
                                                   name="requirements[{{ $index }}][description]"
                                                   value="{{ $row['description'] ?? '' }}"
                                                   maxlength="500"
                                                   placeholder="Brief description"
                                                   class="w-full rounded-xl
                                                          border border-slate-300
                                                          bg-white px-4 py-3 text-sm
                                                          outline-none focus:border-teal-500">

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>


                        <!-- REQUIREMENT COUNT -->
                        <div class="mt-5 flex flex-wrap items-center justify-between gap-3">

                            <p class="text-xs text-slate-500">
                                Maximum 20 requirements per semester.
                            </p>

                            <span id="requirementCount"
                                  class="rounded-full bg-teal-50 px-4 py-2
                                         text-xs font-bold text-teal-700">

                                {{ count($rows) }} Selected

                            </span>

                        </div>


                        <!-- PROTECTION REMINDER -->
                        <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4">

                            <p class="text-xs leading-5 text-amber-800">

                                <strong>Important:</strong>
                                Once teachers have uploaded documents,
                                changing or deleting existing requirements
                                may be restricted to protect their submissions.

                            </p>

                        </div>

                    </section>


                    <!-- ==================================
                         ADDITIONAL INSTRUCTIONS
                    =================================== -->

                    <section>

                        <label for="instructions"
                               class="block text-base font-extrabold text-slate-900">

                            Additional Instructions

                        </label>

                        <p class="mt-1 text-xs text-slate-500">
                            Explain any special instructions teachers must follow.
                        </p>


                        <textarea id="instructions"
                                  name="instructions"
                                  rows="5"
                                  maxlength="2000"
                                  placeholder="Example: Ensure all uploaded school forms are complete, signed, and properly checked."
                                  class="mt-4 w-full rounded-xl border
                                         border-slate-300 bg-white px-4 py-3
                                         text-sm leading-6 outline-none
                                         focus:border-teal-500">{{ old('instructions', $setting?->instructions) }}</textarea>

                    </section>


                    <!-- ==================================
                         SAVE ACTIONS
                    =================================== -->

                    <section class="border-t border-slate-100 pt-7">

                        <div>

                            <h3 class="text-base font-extrabold text-slate-900">
                                Save Submission Settings
                            </h3>

                            <p class="mt-2 text-xs text-slate-500">
                                Choose how you want to save this semester.
                            </p>

                        </div>


                        <div class="mt-5 flex flex-wrap gap-3">


                            <!-- SAVE DRAFT -->
                            <button type="submit"
                                    name="status"
                                    value="draft"
                                    class="inline-flex cursor-pointer
                                           items-center gap-2 rounded-xl
                                           border border-slate-300 bg-white
                                           px-5 py-3 text-sm font-bold
                                           text-slate-700 hover:bg-slate-50">

                                <i data-lucide="save"
                                   class="h-4 w-4"></i>

                                Save Draft

                            </button>


                            <!-- ACTIVATE -->
                            <button type="submit"
                                    name="status"
                                    value="active"
                                    class="inline-flex cursor-pointer
                                           items-center gap-2 rounded-xl
                                           bg-[#0d9488] px-5 py-3
                                           text-sm font-bold text-white
                                           hover:bg-[#0f766e]">

                                <i data-lucide="check-circle-2"
                                   class="h-4 w-4"></i>

                                Activate Submission

                            </button>


                            <!-- CLOSE -->
                            <button type="submit"
                                    name="status"
                                    value="closed"
                                    onclick="return confirm('Close this semester submission period?')"
                                    class="inline-flex cursor-pointer
                                           items-center gap-2 rounded-xl
                                           border border-red-200 bg-red-50
                                           px-5 py-3 text-sm font-bold
                                           text-red-700 hover:bg-red-100">

                                <i data-lucide="lock"
                                   class="h-4 w-4"></i>

                                Close Submission

                            </button>

                        </div>


                        <p class="mt-5 text-xs leading-6 text-slate-500">

                            Settings are saved to MySQL.
                            The Teacher Portal reads the saved
                            requirements, opening date, deadline,
                            grade coverage, and submission status.

                        </p>

                    </section>

                </div>

            </form>

        @endif

    @endif

</div>


<!-- ==========================================
     TEMPLATE FOR NEW REQUIREMENTS
=========================================== -->

<template id="requirementRowTemplate">

    <div data-requirement-row
         class="rounded-xl border border-slate-200 bg-slate-50 p-5">

        <input type="hidden"
               data-field="id"
               value="">

        <div class="flex items-start justify-between gap-3">

            <p data-requirement-number
               class="text-xs font-bold uppercase tracking-widest text-teal-700">
                New Requirement
            </p>

            <button type="button"
                    data-remove-requirement
                    class="cursor-pointer text-xs font-bold text-red-600 hover:text-red-800">
                Remove
            </button>

        </div>


        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">

            <div>

                <label class="mb-2 block text-xs font-bold text-slate-600">
                    Document Name
                </label>

                <input type="text"
                       data-field="name"
                       maxlength="120"
                       required
                       placeholder="Enter document name"
                       class="w-full rounded-xl border border-slate-300
                              bg-white px-4 py-3 text-sm outline-none
                              focus:border-teal-500">

            </div>


            <div>

                <label class="mb-2 block text-xs font-bold text-slate-600">
                    Description (Optional)
                </label>

                <input type="text"
                       data-field="description"
                       maxlength="500"
                       placeholder="Brief description"
                       class="w-full rounded-xl border border-slate-300
                              bg-white px-4 py-3 text-sm outline-none
                              focus:border-teal-500">

            </div>

        </div>

    </div>

</template>

@endsection


<!-- ==========================================
     PAGE JAVASCRIPT
=========================================== -->

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ======================================
    // SCHOOL YEAR NAVIGATION
    // ======================================

    const yearSelect = document.getElementById('yearSelect');

    if (yearSelect) {
        yearSelect.addEventListener('change', function () {
            window.location.href = this.value;
        });
    }


    // ======================================
    // REQUIREMENT ELEMENTS
    // ======================================

    const list = document.getElementById('requirementsList');
    const addButton = document.getElementById('addRequirement');
    const count = document.getElementById('requirementCount');
    const template = document.getElementById('requirementRowTemplate');

    if (!list || !addButton || !count || !template) {
        return;
    }


    // ======================================
    // REINDEX REQUIREMENTS
    // ======================================

    function renumber() {

        const rows = list.querySelectorAll(
            '[data-requirement-row]'
        );

        rows.forEach(function (row, index) {

            const number = row.querySelector(
                '[data-requirement-number]'
            );

            if (number) {
                number.textContent =
                    'Requirement ' + (index + 1);
            }

            row.querySelectorAll('[data-field]')
                .forEach(function (input) {

                    const field = input.dataset.field;

                    input.name =
                        'requirements[' + index + '][' + field + ']';
                });
        });


        count.textContent = rows.length + ' Selected';

        const isMaximum = rows.length >= 20;

        addButton.disabled = isMaximum;

        addButton.classList.toggle(
            'opacity-50',
            isMaximum
        );

        addButton.classList.toggle(
            'cursor-not-allowed',
            isMaximum
        );
    }


    // ======================================
    // ADD NEW REQUIREMENT
    // ======================================

    addButton.addEventListener('click', function () {

        const currentRows = list.querySelectorAll(
            '[data-requirement-row]'
        );

        if (currentRows.length >= 20) {
            return;
        }

        const newRow = template.content.cloneNode(true);

        list.appendChild(newRow);

        renumber();

        const rows = list.querySelectorAll(
            '[data-requirement-row]'
        );

        const lastRow = rows[rows.length - 1];

        if (lastRow) {
            const input = lastRow.querySelector(
                '[data-field="name"]'
            );

            input?.focus();
        }
    });


    // ======================================
    // REMOVE REQUIREMENT
    // ======================================

    list.addEventListener('click', function (event) {

        const button = event.target.closest(
            '[data-remove-requirement]'
        );

        if (!button) {
            return;
        }

        const row = button.closest(
            '[data-requirement-row]'
        );

        if (!row) {
            return;
        }

        const confirmed = confirm(
            'Remove this requirement from the current form? Changes will be applied when you save.'
        );

        if (!confirmed) {
            return;
        }

        row.remove();

        renumber();
    });


    // ======================================
    // INITIALIZE
    // ======================================

    renumber();

});
</script>
@endpush
