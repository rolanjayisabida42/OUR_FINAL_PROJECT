
@extends('layouts.teacher')

@section('title', 'Master Sheets')

@section('breadcrumb', 'My Classrooms / ' . $section->name)

@section('page_heading', 'Master Sheets')

@section('content')

<div class="mx-auto max-w-6xl space-y-6 pb-12">

    <!-- BACK BUTTON -->

    <a href="{{ route('teacher.classrooms.show', $section) }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-teal-700">

        <i data-lucide="arrow-left" class="h-4 w-4"></i>
        Back to Classroom

    </a>


    <!-- CLASSROOM HEADER -->

    <section class="overflow-hidden rounded-3xl bg-[#142b61] text-white shadow-lg">

        <div class="p-7 md:p-9">

            <div class="flex flex-wrap items-center justify-between gap-5">

                <div class="flex items-center gap-4">

                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10">

                        <i data-lucide="file-spreadsheet"
                           class="h-7 w-7"></i>

                    </div>

                    <div>

                        <span class="text-xs font-bold uppercase tracking-wider text-teal-200">
                            Academic Records
                        </span>

                        <h1 class="mt-2 text-2xl font-extrabold md:text-3xl">
                            Master Sheets
                        </h1>

                        <p class="mt-2 max-w-xl text-sm text-blue-100">
                            Upload Class Records and generate a Master Sheet
                            with student names, subjects, and grades.
                        </p>

                    </div>

                </div>

                <div class="rounded-xl border border-white/15 bg-white/10 px-5 py-4">

                    <p class="text-xs text-blue-200">
                        Selected Classroom
                    </p>

                    <p class="mt-1 font-bold">
                        {{ $section->name }}
                    </p>

                </div>

            </div>

        </div>


        <!-- CLASSROOM INFORMATION -->

        <div class="grid grid-cols-2 gap-5 border-t border-white/10 bg-black/10 p-6 md:grid-cols-4 md:px-9">

            <div>
                <p class="text-xs text-blue-200">Grade Level</p>
                <p class="mt-1 text-sm font-bold">
                    Grade {{ $section->grade_level }}
                </p>
            </div>

            <div>
                <p class="text-xs text-blue-200">Strand</p>
                <p class="mt-1 text-sm font-bold">
                    {{ $section->strand?->code ?? 'N/A' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-blue-200">School Year</p>
                <p class="mt-1 text-sm font-bold">
                    {{ $section->schoolYear?->name ?? 'N/A' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-blue-200">Class Adviser</p>
                <p class="mt-1 text-sm font-bold">
                    {{ $section->adviser?->teacher?->name ?? auth()->user()->name }}
                </p>
            </div>

        </div>

    </section>


    <!-- LARAVEL ERRORS -->

    @if($errors->any())

        <div class="rounded-2xl border border-red-200 bg-red-50 p-5">

            <div class="flex items-start gap-3">

                <i data-lucide="alert-circle"
                   class="h-5 w-5 shrink-0 text-red-600"></i>

                <div>

                    <h3 class="text-sm font-extrabold text-red-800">
                        Unable to Generate Master Sheet
                    </h3>

                    <ul class="mt-2 list-disc space-y-1 pl-4 text-sm text-red-700">

                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    <!-- ===================================================
         MASTER SHEET UPLOAD FORM
         ALL FIELDS ARE INSIDE THIS FORM
    ==================================================== -->

    <form
        id="uploadForm"
        action="{{ route('teacher.classrooms.master-sheets.test-extract', $section) }}"
        method="POST"
        enctype="multipart/form-data"
        class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

        @csrf


        <!-- FORM HEADER -->

        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 px-6 py-6 md:px-8">

            <div class="flex items-center gap-4">

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-50 text-teal-700">

                    <i data-lucide="cloud-upload" class="h-6 w-6"></i>

                </div>

                <div>

                    <h2 class="text-lg font-extrabold text-slate-900">
                        Generate Master Sheet
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Select the grade and semester, then upload your Class Records.
                    </p>

                </div>

            </div>

            <div class="rounded-xl bg-slate-50 px-4 py-3">

                <span id="fileCount"
                      class="text-sm font-extrabold text-teal-700">
                    0 / 11
                </span>

                <span class="ml-1 text-xs text-slate-500">
                    Files
                </span>

            </div>

        </div>


        <div class="space-y-7 p-6 md:p-8">


            <!-- ===========================================
                 ACADEMIC INFORMATION
                 IMPORTANT: INSIDE FORM
            ============================================ -->

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 md:p-6">

                <div class="mb-5">

                    <h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900">

                        <i data-lucide="graduation-cap"
                           class="h-5 w-5 text-teal-700"></i>

                        Academic Information

                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        These details will be used when generating the Excel Master Sheet.
                    </p>

                </div>


                <div class="grid gap-5 md:grid-cols-2">


                    <!-- GRADE LEVEL -->

                    <div>

                        <label
                            for="grade_level"
                            class="mb-2 block text-xs font-bold text-slate-700">

                            Grade Level

                        </label>

                        <select
                            id="grade_level"
                            name="grade_level"
                            required
                            class="w-full cursor-pointer rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-100">

                            <option value="11"
                                @selected((string) old('grade_level', $section->grade_level) === '11')>
                                Grade 11
                            </option>

                            <option value="12"
                                @selected((string) old('grade_level', $section->grade_level) === '12')>
                                Grade 12
                            </option>

                        </select>

                        <p class="mt-2 text-xs text-slate-500">
                            Must match the selected classroom.
                        </p>

                    </div>


                    <!-- SEMESTER -->

                    <div>

                        <label
                            for="semester"
                            class="mb-2 block text-xs font-bold text-slate-700">

                            Semester

                        </label>

                        <select
                            id="semester"
                            name="semester"
                            required
                            class="w-full cursor-pointer rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-100">

                            <option value="" disabled
                                @selected(old('semester', '') === '')>
                                Select Semester
                            </option>

                            <option value="1"
                                @selected((string) old('semester') === '1')>
                                First Semester
                            </option>

                            <option value="2"
                                @selected((string) old('semester') === '2')>
                                Second Semester
                            </option>

                        </select>

                        <p class="mt-2 text-xs text-slate-500">
                            Uploaded Class Records must belong to this semester.
                        </p>

                    </div>

                </div>


                <!-- SCHOOL YEAR DISPLAY -->

                <div class="mt-5 flex items-center gap-3 rounded-xl border border-teal-100 bg-white p-4">

                    <i data-lucide="calendar-days"
                       class="h-5 w-5 text-teal-700"></i>

                    <div>

                        <p class="text-xs font-bold text-slate-700">
                            School Year
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-600">
                            {{ $section->schoolYear?->name ?? 'Not Assigned' }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- ===========================================
                 CLASS RECORD UPLOAD
            ============================================ -->

            <div>

                <div class="mb-4">

                    <h3 class="text-sm font-extrabold text-slate-900">
                        Upload Class Records
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Add 1 to 11 Excel Class Records.
                    </p>

                </div>


                <!-- FILE INPUT -->

                <input
                    id="classRecords"
                    name="class_records[]"
                    type="file"
                    accept=".xlsx"
                    multiple
                    class="sr-only">


                <!-- DROPZONE -->

                <div
                    id="dropzone"
                    role="button"
                    tabindex="0"
                    class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-5 py-12 text-center transition hover:border-teal-500 hover:bg-teal-50">

                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-teal-700 shadow-sm">

                        <i data-lucide="files" class="h-8 w-8"></i>

                    </div>

                    <h3 class="mt-5 text-base font-extrabold text-slate-800">
                        Drag & Drop Class Records Here
                    </h3>

                    <p class="mt-2 text-sm text-slate-500">
                        Or choose Excel files from your computer.
                    </p>

                    <button
                        id="chooseFilesButton"
                        type="button"
                        class="mt-5 inline-flex cursor-pointer items-center gap-2 rounded-xl bg-teal-700 px-6 py-3 text-sm font-bold text-white transition hover:bg-teal-800">

                        <i data-lucide="folder-open" class="h-4 w-4"></i>

                        Choose Excel Files

                    </button>


                    <div class="mt-5 flex flex-wrap justify-center gap-2">

                        <span class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-slate-500">
                            XLSX Format
                        </span>

                        <span class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-slate-500">
                            Maximum 11 Files
                        </span>

                        <span class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-slate-500">
                            15 MB Each
                        </span>

                    </div>

                </div>

            </div>


            <!-- SELECTION STATUS -->

            <div>

                <div class="mb-3 flex items-center justify-between gap-3">

                    <div>

                        <p class="text-sm font-bold text-slate-800">
                            File Selection
                        </p>

                        <p id="selectionStatus"
                           class="mt-1 text-xs text-slate-500">
                            No files selected yet
                        </p>

                    </div>

                    <span id="selectionPercentage"
                          class="text-xs font-bold text-teal-700">
                        0 / 11
                    </span>

                </div>

                <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">

                    <div
                        id="selectionProgress"
                        class="h-full w-0 rounded-full bg-teal-500 transition-all duration-300">
                    </div>

                </div>

            </div>


            <!-- SELECTED FILES -->

            <div id="selectedSection" class="hidden">

                <div class="mb-4 flex items-center justify-between gap-3">

                    <h3 class="text-sm font-extrabold text-slate-800">
                        Selected Class Records
                    </h3>

                    <button
                        id="clearFiles"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-50">

                        <i data-lucide="trash-2" class="h-4 w-4"></i>

                        Clear All

                    </button>

                </div>

                <div id="fileList" class="grid gap-3 sm:grid-cols-2"></div>

            </div>


            <!-- JAVASCRIPT ERROR MESSAGE -->

            <div
                id="fileError"
                role="alert"
                aria-live="assertive"
                class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            </div>


            <!-- FOOTER -->

            <div class="flex flex-wrap items-center justify-between gap-5 border-t border-slate-100 pt-6">

                <div class="flex items-start gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">

                        <i data-lucide="shield-check" class="h-5 w-5"></i>

                    </div>

                    <div>

                        <p class="text-xs font-bold text-slate-700">
                            Automatic Excel Generation
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Uses the Final Semestral Grade worksheet.
                        </p>

                    </div>

                </div>


                <!-- GENERATE BUTTON -->

                <button
                    id="extractButton"
                    type="submit"
                    class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-[#0d9488] px-7 py-4 text-sm font-bold text-white shadow-sm transition hover:bg-[#0f766e] disabled:cursor-not-allowed disabled:opacity-50">

                    <i data-lucide="file-spreadsheet" class="h-5 w-5"></i>

                    <span id="buttonText">
                        Generate & Download Master Sheet
                    </span>

                    <i data-lucide="arrow-right" class="h-4 w-4"></i>

                </button>

            </div>

        </div>

    </form>

</div>


<!-- ===================================================
     GENERATION PROGRESS OVERLAY
==================================================== -->

<div
    id="processingOverlay"
    role="dialog"
    aria-modal="true"
    aria-labelledby="processingTitle"
    tabindex="-1"
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/65 px-4">

    <div class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl">

        <div class="bg-[#142b61] px-7 py-8 text-center text-white">

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-white/10">

                <i data-lucide="file-spreadsheet" class="h-8 w-8"></i>

            </div>

            <h2
                id="processingTitle"
                class="mt-5 text-xl font-extrabold">

                Generating Master Sheet

            </h2>

            <p class="mt-2 text-xs text-blue-200">
                Processing academic records
            </p>

        </div>


        <div class="space-y-5 p-7">

            <div class="flex items-center justify-between">

                <p
                    id="processingStage"
                    class="text-sm font-bold text-slate-800">

                    Uploading Class Records...

                </p>

                <span
                    id="processingPercent"
                    class="text-sm font-extrabold text-teal-700">

                    0%

                </span>

            </div>


            <!-- PROGRESS BAR -->

            <div class="relative h-3 overflow-hidden rounded-full bg-slate-100">

                <div
                    id="uploadProgressBar"
                    class="h-full w-0 rounded-full bg-teal-500 transition-all duration-200">
                </div>

                <div
                    id="serverProgressBar"
                    class="master-processing-bar absolute inset-y-0 left-0 hidden w-1/3 rounded-full bg-teal-500">
                </div>

            </div>


            <p
                id="processingDescription"
                class="text-center text-xs leading-6 text-slate-500">

                Preparing your Excel files...

            </p>


            <p
                id="processingFileCount"
                class="rounded-xl bg-slate-50 p-3 text-center text-xs font-semibold text-slate-600">

                Preparing files...

            </p>


            <p class="border-t border-slate-100 pt-4 text-center text-xs text-slate-400">

                Your original Excel templates will not be modified.

            </p>

        </div>

    </div>

</div>


<!-- ===================================================
     PROGRESS ANIMATION
==================================================== -->

<style>
@keyframes masterSheetProcessing {
    0% {
        transform: translateX(-110%);
    }

    100% {
        transform: translateX(310%);
    }
}

.master-processing-bar {
    animation: masterSheetProcessing 1.3s ease-in-out infinite;
}
</style>


<!-- ===================================================
     JAVASCRIPT
==================================================== -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('uploadForm');

    const input = document.getElementById('classRecords');

    const dropzone = document.getElementById('dropzone');

    const chooseButton = document.getElementById('chooseFilesButton');

    const gradeSelect = document.getElementById('grade_level');

    const semesterSelect = document.getElementById('semester');

    const generateButton = document.getElementById('extractButton');

    const buttonText = document.getElementById('buttonText');

    const fileCount = document.getElementById('fileCount');

    const fileList = document.getElementById('fileList');

    const selectedSection = document.getElementById('selectedSection');

    const clearButton = document.getElementById('clearFiles');

    const selectionProgress = document.getElementById('selectionProgress');

    const selectionPercentage = document.getElementById('selectionPercentage');

    const selectionStatus = document.getElementById('selectionStatus');

    const fileError = document.getElementById('fileError');

    const processingOverlay = document.getElementById('processingOverlay');

    const processingStage = document.getElementById('processingStage');

    const processingPercent = document.getElementById('processingPercent');

    const processingDescription = document.getElementById('processingDescription');

    const processingFileCount = document.getElementById('processingFileCount');

    const uploadProgressBar = document.getElementById('uploadProgressBar');

    const serverProgressBar = document.getElementById('serverProgressBar');


    const MAX_FILES = 11;

    const MAX_SIZE = 15 * 1024 * 1024;

    let records = [];

    let nextId = 1;

    let isGenerating = false;


    /*
    |--------------------------------------------------------------------------
    | REFRESH ICONS
    |--------------------------------------------------------------------------
    */

    function refreshIcons() {

        if (window.lucide) {
            window.lucide.createIcons();
        }

    }


    /*
    |--------------------------------------------------------------------------
    | ERROR HANDLING
    |--------------------------------------------------------------------------
    */

    function showError(message) {

        fileError.textContent = message;

        fileError.classList.remove('hidden');

    }


    function hideError() {

        fileError.textContent = '';

        fileError.classList.add('hidden');

    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT FILE SIZE
    |--------------------------------------------------------------------------
    */

    function formatSize(bytes) {

        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';

    }


    /*
    |--------------------------------------------------------------------------
    | CHOOSE EXCEL FILES
    |--------------------------------------------------------------------------
    */

    chooseButton.addEventListener('click', function (event) {

        event.stopPropagation();

        if (!isGenerating) {
            input.click();
        }

    });


    dropzone.addEventListener('click', function () {

        if (!isGenerating) {
            input.click();
        }

    });


    dropzone.addEventListener('keydown', function (event) {

        if (event.target !== dropzone) {
            return;
        }

        if (event.key === 'Enter' || event.key === ' ') {

            event.preventDefault();

            if (!isGenerating) {
                input.click();
            }

        }

    });


    /*
    |--------------------------------------------------------------------------
    | ADD SELECTED FILES
    |--------------------------------------------------------------------------
    */

    function addFiles(files) {

        if (isGenerating) {
            return;
        }

        hideError();

        const errors = [];

        for (const file of Array.from(files)) {

            // XLSX only
            if (!/\.xlsx$/i.test(file.name)) {

                errors.push(
                    file.name + ' must be an XLSX file.'
                );

                continue;
            }

            // File size
            if (file.size === 0 || file.size > MAX_SIZE) {

                errors.push(
                    file.name + ' is empty or exceeds 15 MB.'
                );

                continue;
            }

            // Maximum files
            if (records.length >= MAX_FILES) {

                errors.push(
                    'You can upload a maximum of 11 Class Records.'
                );

                break;
            }

            // Duplicate file selection
            const exists = records.some(function (record) {

                return (
                    record.file.name === file.name &&
                    record.file.size === file.size &&
                    record.file.lastModified === file.lastModified
                );

            });

            if (exists) {

                errors.push(
                    file.name + ' is already selected.'
                );

                continue;
            }

            records.push({
                id: nextId++,
                file: file
            });

        }

        renderFiles();

        if (errors.length > 0) {
            showError(errors[0]);
        }

    }


    /*
    |--------------------------------------------------------------------------
    | INPUT CHANGE
    |--------------------------------------------------------------------------
    */

    input.addEventListener('change', function () {

        addFiles(this.files);

        // Allows choosing additional files afterward.
        // Selected files remain in the records array.
        this.value = '';

    });


    /*
    |--------------------------------------------------------------------------
    | DRAG AND DROP
    |--------------------------------------------------------------------------
    */

    dropzone.addEventListener('dragover', function (event) {

        event.preventDefault();

        dropzone.classList.add(
            'border-teal-500',
            'bg-teal-50'
        );

    });


    dropzone.addEventListener('dragleave', function () {

        dropzone.classList.remove(
            'border-teal-500',
            'bg-teal-50'
        );

    });


    dropzone.addEventListener('drop', function (event) {

        event.preventDefault();

        dropzone.classList.remove(
            'border-teal-500',
            'bg-teal-50'
        );

        if (event.dataTransfer && event.dataTransfer.files) {

            addFiles(event.dataTransfer.files);

        }

    });


    /*
    |--------------------------------------------------------------------------
    | RENDER SELECTED FILES
    |--------------------------------------------------------------------------
    */

    function renderFiles() {

        const total = records.length;

        fileCount.textContent = total + ' / ' + MAX_FILES;

        selectionPercentage.textContent =
            total + ' / ' + MAX_FILES;

        selectionProgress.style.width =
            ((total / MAX_FILES) * 100) + '%';

        selectionStatus.textContent =
            total === 0
                ? 'No files selected yet'
                : total + ' Class Record(s) selected';

        selectedSection.classList.toggle(
            'hidden',
            total === 0
        );

        fileList.replaceChildren();


        records.forEach(function (record, index) {

            const row = document.createElement('div');

            row.className =
                'flex items-center gap-3 rounded-xl ' +
                'border border-slate-200 bg-slate-50 p-4';


            const iconBox = document.createElement('div');

            iconBox.className =
                'flex h-10 w-10 shrink-0 items-center ' +
                'justify-center rounded-xl bg-teal-100 ' +
                'text-teal-700';

            const fileIcon = document.createElement('span');

            fileIcon.textContent = 'XLSX';

            fileIcon.className = 'text-xs font-extrabold';

            iconBox.appendChild(fileIcon);


            const details = document.createElement('div');

            details.className = 'min-w-0 flex-1';


            const name = document.createElement('p');

            name.className =
                'break-all text-sm font-bold text-slate-800';

            name.textContent = record.file.name;


            const size = document.createElement('p');

            size.className = 'mt-1 text-xs text-slate-500';

            size.textContent =
                'File ' + (index + 1) +
                ' - ' + formatSize(record.file.size);


            details.appendChild(name);

            details.appendChild(size);


            const remove = document.createElement('button');

            remove.type = 'button';

            remove.dataset.removeId = record.id;

            remove.className =
                'rounded-lg px-3 py-2 text-xs font-bold ' +
                'text-red-600 hover:bg-red-50';

            remove.textContent = 'Remove';


            row.appendChild(iconBox);

            row.appendChild(details);

            row.appendChild(remove);

            fileList.appendChild(row);

        });

    }


    /*
    |--------------------------------------------------------------------------
    | REMOVE FILE
    |--------------------------------------------------------------------------
    */

    fileList.addEventListener('click', function (event) {

        if (isGenerating) {
            return;
        }

        const button = event.target.closest(
            '[data-remove-id]'
        );

        if (!button) {
            return;
        }

        const id = Number(button.dataset.removeId);

        records = records.filter(function (record) {
            return record.id !== id;
        });

        hideError();

        renderFiles();

    });


    /*
    |--------------------------------------------------------------------------
    | CLEAR ALL FILES
    |--------------------------------------------------------------------------
    */

    clearButton.addEventListener('click', function () {

        if (isGenerating) {
            return;
        }

        records = [];

        input.value = '';

        hideError();

        renderFiles();

    });


    /*
    |--------------------------------------------------------------------------
    | SHOW PROCESSING OVERLAY
    |--------------------------------------------------------------------------
    */

    function showProcessing() {

        processingOverlay.classList.remove('hidden');

        processingOverlay.classList.add('flex');

        processingStage.textContent =
            'Uploading Class Records...';

        processingPercent.textContent = '0%';

        processingDescription.textContent =
            'Sending your Excel files to Laravel.';

        processingFileCount.textContent =
            records.length + ' Class Record(s) selected';

        uploadProgressBar.style.width = '0%';

        uploadProgressBar.classList.remove('hidden');

        serverProgressBar.classList.add('hidden');

    }


    /*
    |--------------------------------------------------------------------------
    | SERVER GENERATION STAGE
    |--------------------------------------------------------------------------
    */

    function showGenerating() {

        processingStage.textContent =
            'Generating Master Sheet...';

        processingPercent.textContent =
            'Processing';

        processingDescription.textContent =
            'Laravel is extracting student names, ' +
            'matching subjects, inserting grades, ' +
            'and generating the Excel file.';

        uploadProgressBar.classList.add('hidden');

        serverProgressBar.classList.remove('hidden');

    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE OVERLAY
    |--------------------------------------------------------------------------
    */

    function closeProcessing() {

        processingOverlay.classList.add('hidden');

        processingOverlay.classList.remove('flex');

        isGenerating = false;

        generateButton.disabled = false;

        buttonText.textContent =
            'Generate & Download Master Sheet';

    }


    /*
    |--------------------------------------------------------------------------
    | READ LARAVEL ERROR RESPONSE
    |--------------------------------------------------------------------------
    */

    async function getServerError(xhr) {

        if (xhr.status === 419) {

            return 'Your session expired. Refresh and try again.';

        }

        if (xhr.status === 413) {

            return 'Uploaded files exceed the server size limit.';

        }

        if (xhr.status === 403) {

            return 'You do not have permission to generate this Master Sheet.';

        }

        try {

            const text = await xhr.response.text();

            const data = JSON.parse(text);

            if (data.errors) {

                const messages =
                    Object.values(data.errors).flat();

                if (messages.length > 0) {

                    return messages.join(' | ');

                }

            }

            if (data.message) {
                return data.message;
            }

        } catch (error) {
            // Non-JSON response.
        }

        return 'Master Sheet generation failed. Please check your files.';

    }


    /*
    |--------------------------------------------------------------------------
    | READ DOWNLOAD FILENAME
    |--------------------------------------------------------------------------
    */

    function getDownloadFilename(xhr) {

        const disposition =
            xhr.getResponseHeader('Content-Disposition') || '';

        const encoded =
            disposition.match(/filename\*=UTF-8''([^;]+)/i);

        if (encoded) {

            try {
                return decodeURIComponent(encoded[1]);
            } catch (error) {
                // Use ordinary filename.
            }

        }

        const normal =
            disposition.match(/filename="([^"]+)"|filename=([^;]+)/i);

        if (normal) {

            return (normal[1] || normal[2]).trim();

        }

        return 'MasterSheet.xlsx';

    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT AND GENERATE MASTER SHEET
    |--------------------------------------------------------------------------
    */

    form.addEventListener('submit', function (event) {

        event.preventDefault();

        if (isGenerating) {
            return;
        }

        hideError();


        // Validate selected grade.
        if (!gradeSelect.value) {

            showError('Please select a grade level.');

            gradeSelect.focus();

            return;
        }


        // Validate selected semester.
        if (!semesterSelect.value) {

            showError('Please select a semester.');

            semesterSelect.focus();

            return;
        }


        // Validate file count.
        if (records.length < 1 || records.length > MAX_FILES) {

            showError(
                'Please select between 1 and 11 Class Records.'
            );

            return;
        }


        // Lock submit button.
        isGenerating = true;

        generateButton.disabled = true;

        buttonText.textContent =
            'Generating Master Sheet...';

        showProcessing();


        /*
        |--------------------------------------------------------------------------
        | IMPORTANT FIX
        |--------------------------------------------------------------------------
        |
        | FormData now contains:
        | _token
        | grade_level
        | semester
        |
        | The selected Excel files are added afterward.
        |
        */

        const formData = new FormData(form);

        // Remove the file input's empty/default entry.
        formData.delete('class_records[]');

        // Add ALL selected Excel files.
        records.forEach(function (record) {

            formData.append(
                'class_records[]',
                record.file,
                record.file.name
            );

        });


        /*
        |--------------------------------------------------------------------------
        | SEND REQUEST TO LARAVEL
        |--------------------------------------------------------------------------
        */

        const xhr = new XMLHttpRequest();

        xhr.open('POST', form.action, true);

        xhr.responseType = 'blob';

        xhr.setRequestHeader(
            'X-Requested-With',
            'XMLHttpRequest'
        );

        xhr.setRequestHeader(
            'Accept',
            'application/json'
        );


        /*
        |--------------------------------------------------------------------------
        | ACTUAL FILE UPLOAD PROGRESS
        |--------------------------------------------------------------------------
        */

        xhr.upload.addEventListener('progress', function (event) {

            if (!event.lengthComputable) {
                return;
            }

            const percent = Math.min(
                100,
                Math.round((event.loaded / event.total) * 100)
            );

            uploadProgressBar.style.width = percent + '%';

            processingPercent.textContent = percent + '%';

        });


        xhr.upload.addEventListener('load', function () {

            showGenerating();

        });


        /*
        |--------------------------------------------------------------------------
        | HANDLE LARAVEL RESPONSE
        |--------------------------------------------------------------------------
        */

        xhr.onload = async function () {

            if (xhr.status >= 200 && xhr.status < 300) {

                // Success: Laravel returned an XLSX file.
                const filename = getDownloadFilename(xhr);

                const blob = xhr.response;

                const url = URL.createObjectURL(blob);

                const link = document.createElement('a');

                link.href = url;

                link.download = filename;

                document.body.appendChild(link);

                link.click();

                link.remove();

                setTimeout(function () {
                    URL.revokeObjectURL(url);
                }, 5000);

                closeProcessing();

                return;
            }


            // Error returned by Laravel.
            const message = await getServerError(xhr);

            closeProcessing();

            showError(message);

            fileError.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

        };


        xhr.onerror = function () {

            closeProcessing();

            showError(
                'Network error. Please check your connection and try again.'
            );

        };


        xhr.onabort = function () {

            closeProcessing();

            showError('Upload was cancelled.');

        };


        // Send the entire request.
        xhr.send(formData);

    });


    /*
    |--------------------------------------------------------------------------
    | INITIALIZE
    |--------------------------------------------------------------------------
    */

    renderFiles();

    refreshIcons();

});
</script>

@endsection
