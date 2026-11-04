
@extends('layouts.teacher')

@section('title', 'Master Sheets')
@section('breadcrumb', 'My Classrooms / ' . $section->name)
@section('page_heading', 'Master Sheets')

@section('content')

<div class="mx-auto max-w-6xl space-y-6 pb-12">

    <!-- BACK NAVIGATION -->

    <div class="flex items-center justify-between gap-3">

        <a href="{{ route('teacher.classrooms.index') }}"
           class="inline-flex items-center gap-2 text-sm
                  font-semibold text-slate-500 transition
                  hover:text-teal-600">

            <i data-lucide="arrow-left" class="h-4 w-4"></i>

            Back to My Classrooms

        </a>

        <span class="inline-flex items-center gap-2
                     rounded-full border border-teal-200
                     bg-teal-50 px-3 py-1.5
                     text-xs font-bold text-teal-700">

            <span class="h-2 w-2 rounded-full bg-teal-500"></span>

            Teacher Workspace

        </span>

    </div>


    <!-- ==========================================
         CLASSROOM HEADER
    =========================================== -->

    <section class="relative overflow-hidden rounded-3xl
                    bg-[#142b61] text-white shadow-lg">

        <!-- DECORATIVE BACKGROUND -->

        <div class="pointer-events-none absolute -right-20 -top-28
                    h-80 w-80 rounded-full border border-white/10">
        </div>

        <div class="pointer-events-none absolute -right-8 top-10
                    h-52 w-52 rounded-full bg-white/5">
        </div>

        <div class="pointer-events-none absolute bottom-0 right-24
                    h-32 w-32 rounded-full bg-teal-400/10 blur-2xl">
        </div>


        <!-- HEADER CONTENT -->

        <div class="relative px-6 py-8 md:px-9 md:py-10">

            <div class="flex flex-col gap-6
                        sm:flex-row sm:items-start
                        sm:justify-between">

                <div class="flex items-start gap-4">

                    <div class="flex h-14 w-14 shrink-0
                                items-center justify-center
                                rounded-2xl border border-white/15
                                bg-white/10">

                        <i data-lucide="file-spreadsheet"
                           class="h-7 w-7"></i>

                    </div>

                    <div>

                        <span class="inline-flex items-center
                                     rounded-full border border-white/15
                                     bg-white/10 px-3 py-1
                                     text-[10px] font-bold
                                     uppercase tracking-widest
                                     text-blue-100">

                            Academic Records

                        </span>

                        <h1 class="mt-3 text-2xl font-extrabold
                                   tracking-tight md:text-3xl">

                            Master Sheets

                        </h1>

                        <p class="mt-2 max-w-xl text-sm
                                  leading-6 text-blue-100">

                            Upload Class Records to automatically
                            generate a consolidated Master Sheet
                            containing student names, subjects,
                            and quarterly grades.

                        </p>

                    </div>

                </div>


                <!-- CLASSROOM BADGE -->

                <div class="self-start rounded-2xl
                            border border-white/15
                            bg-white/10 px-5 py-4">

                    <p class="text-[11px] font-medium text-blue-200">
                        Selected Classroom
                    </p>

                    <p class="mt-1 text-lg font-bold">
                        {{ $section->name }}
                    </p>

                </div>

            </div>

        </div>


        <!-- CLASSROOM INFORMATION -->

        <div class="relative grid grid-cols-2 gap-5
                    border-t border-white/10
                    bg-black/10 px-6 py-6
                    md:grid-cols-4 md:px-9">

            <div>

                <p class="text-[11px] font-medium text-blue-200">
                    Grade Level
                </p>

                <p class="mt-1 text-sm font-bold">
                    Grade {{ $section->grade_level }}
                </p>

            </div>

            <div>

                <p class="text-[11px] font-medium text-blue-200">
                    Strand
                </p>

                <p class="mt-1 text-sm font-bold">
                    {{ $section->strand?->code ?? 'N/A' }}
                </p>

            </div>

            <div>

                <p class="text-[11px] font-medium text-blue-200">
                    School Year
                </p>

                <p class="mt-1 text-sm font-bold">
                    {{ $section->schoolYear?->name ?? 'N/A' }}
                </p>

            </div>

            <div>

                <p class="text-[11px] font-medium text-blue-200">
                    Class Adviser
                </p>

                <p class="mt-1 break-words text-sm font-bold">
                    {{ $section->adviser?->teacher?->name ?? auth()->user()->name }}
                </p>

            </div>

        </div>

    </section>


    <!-- ==========================================
         LARAVEL VALIDATION ERRORS
    =========================================== -->

    @if($errors->any())

        <div class="rounded-2xl border border-red-200
                    bg-red-50 p-5">

            <div class="flex items-start gap-3">

                <i data-lucide="alert-circle"
                   class="mt-0.5 h-5 w-5 shrink-0 text-red-600"></i>

                <div>

                    <h3 class="text-sm font-bold text-red-800">
                        Upload Failed
                    </h3>

                    <ul class="mt-2 list-disc space-y-1
                               pl-4 text-xs text-red-700">

                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    <!-- ==========================================
         UPLOAD FORM
    =========================================== -->

    <form id="uploadForm"
          action="{{ route('teacher.classrooms.master-sheets.test-extract', $section) }}"
          method="POST"
          enctype="multipart/form-data"
          class="overflow-hidden rounded-3xl
                 border border-slate-200 bg-white shadow-sm">

        @csrf


        <!-- FORM HEADER -->

        <div class="flex flex-wrap items-center
                    justify-between gap-4
                    border-b border-slate-100
                    px-6 py-6 md:px-8">

            <div class="flex items-center gap-4">

                <div class="flex h-12 w-12 shrink-0
                            items-center justify-center
                            rounded-2xl bg-teal-50 text-teal-600">

                    <i data-lucide="cloud-upload"
                       class="h-6 w-6"></i>

                </div>

                <div>

                    <h2 class="text-lg font-extrabold text-slate-900">
                        Upload Class Records
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Select the Excel files for your subjects.
                    </p>

                </div>

            </div>

            <!-- FILE COUNTER -->

            <div class="rounded-xl border border-slate-200
                        bg-slate-50 px-4 py-2.5">

                <div class="flex items-center gap-2">

                    <i data-lucide="files"
                       class="h-4 w-4 text-teal-600"></i>

                    <span id="fileCount"
                          class="text-sm font-extrabold text-slate-800">
                        0 / 11
                    </span>

                    <span class="text-xs text-slate-500">
                        Files
                    </span>

                </div>

            </div>

        </div>


        <!-- UPLOAD CONTENT -->

        <div class="space-y-7 p-6 md:p-8">


            <!-- HIDDEN FILE INPUT -->

            <input type="file"
                   id="classRecords"
                   name="class_records[]"
                   accept=".xlsx"
                   multiple
                   class="sr-only">


            <!-- DRAG AND DROP AREA -->

            <label for="classRecords"
                   id="dropzone"
                   role="button"
                   tabindex="0"
                   class="group relative flex cursor-pointer
                          flex-col items-center justify-center
                          rounded-2xl border-2 border-dashed
                          border-slate-300 bg-slate-50/70
                          px-5 py-12 text-center
                          transition-all duration-200
                          hover:border-teal-400 hover:bg-teal-50/50
                          focus:outline-none focus:ring-2
                          focus:ring-teal-500 focus:ring-offset-2">

                <div class="mb-5 flex h-20 w-20
                            items-center justify-center
                            rounded-2xl bg-white
                            text-teal-600 shadow-sm
                            ring-1 ring-slate-100
                            transition-transform duration-200
                            group-hover:-translate-y-1">

                    <i data-lucide="file-up"
                       class="h-10 w-10"></i>

                </div>

                <h3 class="text-base font-extrabold
                           text-slate-800 md:text-lg">

                    Drag & Drop Class Records Here

                </h3>

                <p class="mt-2 text-sm text-slate-500">
                    Or browse files from your computer
                </p>

                <span class="mt-5 inline-flex items-center gap-2
                             rounded-xl bg-teal-600 px-6 py-3
                             text-sm font-bold text-white
                             shadow-sm transition
                             group-hover:bg-teal-700">

                    <i data-lucide="folder-open"
                       class="h-4 w-4"></i>

                    Choose Excel Files

                </span>

                <div class="mt-6 flex flex-wrap
                            items-center justify-center gap-3">

                    <span class="rounded-lg bg-white px-3 py-1.5
                                 text-[11px] font-semibold text-slate-500
                                 ring-1 ring-slate-200">
                        XLSX Format
                    </span>

                    <span class="rounded-lg bg-white px-3 py-1.5
                                 text-[11px] font-semibold text-slate-500
                                 ring-1 ring-slate-200">
                        1–11 Files
                    </span>

                    <span class="rounded-lg bg-white px-3 py-1.5
                                 text-[11px] font-semibold text-slate-500
                                 ring-1 ring-slate-200">
                        15 MB Each
                    </span>

                </div>

            </label>


            <!-- FILE SELECTION CAPACITY -->

            <div>

                <div class="mb-3 flex items-center
                            justify-between gap-3">

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

                <div class="h-2.5 overflow-hidden
                            rounded-full bg-slate-100">

                    <div id="selectionProgress"
                         class="h-full w-0 rounded-full
                                bg-teal-500 transition-all duration-300">
                    </div>

                </div>

                <p class="mt-2 text-[11px] text-slate-400">
                    File selection capacity, not upload progress.
                </p>

            </div>


            <!-- SELECTED FILES -->

            <div id="selectedSection" class="hidden">

                <div class="mb-4 flex flex-wrap
                            items-center justify-between gap-3">

                    <div>

                        <h3 class="text-sm font-extrabold text-slate-800">
                            Selected Class Records
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Review the Excel files before generation.
                        </p>

                    </div>

                    <button id="clearFiles"
                            type="button"
                            class="inline-flex items-center gap-2
                                   rounded-lg px-3 py-2 text-xs
                                   font-bold text-red-600
                                   transition hover:bg-red-50">

                        <i data-lucide="trash-2"
                           class="h-4 w-4"></i>

                        Clear All

                    </button>

                </div>

                <div id="fileList"
                     class="grid gap-3 sm:grid-cols-2">
                </div>

            </div>


            <!-- UPLOAD ERROR -->

            <div id="fileError"
                 role="alert"
                 aria-live="assertive"
                 class="hidden rounded-xl border border-red-200
                        bg-red-50 px-4 py-3
                        text-sm font-medium text-red-700">
            </div>


            <!-- FORM FOOTER -->

            <div class="flex flex-col gap-5
                        border-t border-slate-100 pt-6
                        sm:flex-row sm:items-center
                        sm:justify-between">

                <div class="flex items-start gap-3">

                    <div class="flex h-9 w-9 shrink-0
                                items-center justify-center
                                rounded-xl bg-emerald-50
                                text-emerald-600">

                        <i data-lucide="shield-check"
                           class="h-5 w-5"></i>

                    </div>

                    <div>

                        <p class="text-xs font-bold text-slate-700">
                            Automatic Excel Generation
                        </p>

                        <p class="mt-1 text-[11px]
                                  leading-5 text-slate-500">

                            Uses the Final Semestral Grade
                            worksheet from each Class Record.

                        </p>

                    </div>

                </div>


                <!-- GENERATE BUTTON -->

                <button id="extractButton"
                        type="submit"
                        disabled
                        class="inline-flex min-h-12 items-center
                               justify-center gap-2 rounded-xl
                               bg-[#0d9488] px-7 py-3
                               text-sm font-bold text-white
                               shadow-sm transition-all
                               hover:bg-[#0f766e]
                               hover:shadow-md
                               disabled:cursor-not-allowed
                               disabled:opacity-40">

                    <i data-lucide="file-spreadsheet"
                       class="h-5 w-5"></i>

                    <span id="buttonText">
                        Generate & Download Master Sheet
                    </span>

                    <i data-lucide="arrow-right"
                       class="h-4 w-4"></i>

                </button>

            </div>

        </div>

    </form>

</div>


<!-- ==========================================
     GENERATION LOADING OVERLAY
=========================================== -->

<div id="processingOverlay"
     role="dialog"
     aria-modal="true"
     aria-labelledby="processingTitle"
     aria-describedby="processingDescription"
     tabindex="-1"
     class="fixed inset-0 z-[9999] hidden
            items-center justify-center
            bg-slate-950/65 px-4 backdrop-blur-sm">

    <div class="w-full max-w-md overflow-hidden
                rounded-3xl bg-white shadow-2xl">

        <!-- LOADING HEADER -->

        <div class="relative overflow-hidden
                    bg-[#142b61] px-7 py-8 text-center">

            <div class="pointer-events-none absolute
                        -right-10 -top-10 h-40 w-40
                        rounded-full bg-white/5">
            </div>

            <div class="relative mx-auto flex h-16 w-16
                        items-center justify-center
                        rounded-2xl border border-white/10
                        bg-white/10">

                <i data-lucide="file-spreadsheet"
                   class="h-9 w-9 text-teal-300"></i>

            </div>

            <h2 id="processingTitle"
                class="relative mt-5 text-xl
                       font-extrabold text-white">

                Generating Master Sheet

            </h2>

            <p class="relative mt-2 text-xs text-blue-200">
                Processing your academic records
            </p>

        </div>


        <!-- LOADING BODY -->

        <div class="space-y-6 px-7 py-7">

            <!-- STATUS AND PERCENTAGE -->

            <div class="flex items-center
                        justify-between gap-3">

                <div class="flex items-center gap-2">

                    <span id="processingDot"
                          class="h-2.5 w-2.5
                                 animate-pulse rounded-full
                                 bg-teal-500">
                    </span>

                    <p id="processingStage"
                       class="text-sm font-bold text-slate-800">
                        Uploading Class Records...
                    </p>

                </div>

                <span id="processingPercent"
                      class="text-sm font-extrabold
                             text-teal-600 tabular-nums">
                    0%
                </span>

            </div>


            <!-- PROGRESS BAR -->

            <div id="processingTrack"
                 role="progressbar"
                 aria-label="Master Sheet upload progress"
                 aria-valuemin="0"
                 aria-valuemax="100"
                 aria-valuenow="0"
                 class="relative h-3 overflow-hidden
                        rounded-full bg-slate-100">

                <!-- ACTUAL UPLOAD PERCENTAGE -->

                <div id="uploadProgressBar"
                     class="h-full rounded-full
                            bg-gradient-to-r
                            from-teal-500 to-emerald-400
                            transition-all duration-200"
                     style="width:0%">
                </div>

                <!-- INDETERMINATE EXCEL PROCESSING -->

                <div id="serverProgressBar"
                     class="master-processing-bar
                            absolute inset-y-0 left-0
                            hidden w-1/3 rounded-full
                            bg-gradient-to-r
                            from-teal-500 to-emerald-400">
                </div>

            </div>


            <!-- DESCRIPTION -->

            <p id="processingDescription"
               class="min-h-12 text-center text-xs
                      leading-6 text-slate-500">

                Preparing your Excel files...

            </p>


            <!-- FILE STATUS -->

            <div class="flex items-center
                        justify-center gap-2
                        rounded-xl bg-slate-50 px-4 py-3">

                <i data-lucide="files"
                   class="h-4 w-4 text-teal-600"></i>

                <span id="processingFileCount"
                      class="text-xs font-semibold
                             text-slate-600">
                    Preparing files...
                </span>

            </div>


            <!-- FOOTER -->

            <div class="flex items-center
                        justify-center gap-2
                        border-t border-slate-100 pt-5">

                <i data-lucide="shield-check"
                   class="h-4 w-4 text-emerald-600"></i>

                <span class="text-[11px]
                             text-slate-500">

                    Your original Excel template
                    will not be modified.

                </span>

            </div>

        </div>

    </div>

</div>


<!-- ==========================================
     LOADING ANIMATION
=========================================== -->

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
    animation: masterSheetProcessing
        1.3s ease-in-out infinite;
}

@media (prefers-reduced-motion: reduce) {

    .master-processing-bar {
        animation-duration: 3s;
    }

}

</style>


<!-- ==========================================
     JAVASCRIPT
=========================================== -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const input = document.getElementById('classRecords');
    const dropzone = document.getElementById('dropzone');

    const form = document.getElementById('uploadForm');
    const button = document.getElementById('extractButton');
    const buttonText = document.getElementById('buttonText');

    const fileList = document.getElementById('fileList');
    const fileCount = document.getElementById('fileCount');
    const selectedSection = document.getElementById('selectedSection');

    const clearButton = document.getElementById('clearFiles');
    const errorBox = document.getElementById('fileError');

    const selectionProgress =
        document.getElementById('selectionProgress');

    const selectionPercentage =
        document.getElementById('selectionPercentage');

    const selectionStatus =
        document.getElementById('selectionStatus');


    /*
    |--------------------------------------------------------------------------
    | LOADING OVERLAY ELEMENTS
    |--------------------------------------------------------------------------
    */

    const processingOverlay =
        document.getElementById('processingOverlay');

    const processingStage =
        document.getElementById('processingStage');

    const processingPercent =
        document.getElementById('processingPercent');

    const processingDescription =
        document.getElementById('processingDescription');

    const processingFileCount =
        document.getElementById('processingFileCount');

    const processingDot =
        document.getElementById('processingDot');

    const processingTrack =
        document.getElementById('processingTrack');

    const uploadProgressBar =
        document.getElementById('uploadProgressBar');

    const serverProgressBar =
        document.getElementById('serverProgressBar');


    /*
    |--------------------------------------------------------------------------
    | CONFIGURATION
    |--------------------------------------------------------------------------
    */

    const MAX_FILES = 11;

    const MAX_SIZE = 15 * 1024 * 1024;

    let records = [];

    let nextId = 1;

    let isGenerating = false;

    let previousBodyOverflow = '';


    /*
    |--------------------------------------------------------------------------
    | SHOW ERROR
    |--------------------------------------------------------------------------
    */

    function showError(message) {

        errorBox.textContent = message;

        errorBox.classList.remove('hidden');
    }


    /*
    |--------------------------------------------------------------------------
    | HIDE ERROR
    |--------------------------------------------------------------------------
    */

    function hideError() {

        errorBox.textContent = '';

        errorBox.classList.add('hidden');
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT FILE SIZE
    |--------------------------------------------------------------------------
    */

    function formatSize(bytes) {

        return (
            bytes / (1024 * 1024)
        ).toFixed(2) + ' MB';
    }


    /*
    |--------------------------------------------------------------------------
    | REFRESH LUCIDE ICONS
    |--------------------------------------------------------------------------
    */

    function refreshIcons() {

        if (window.lucide) {
            window.lucide.createIcons();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SYNCHRONIZE FILE INPUT
    |--------------------------------------------------------------------------
    |
    | This ensures all selected files are
    | included in the FormData sent to Laravel.
    |
    */

    function syncInput() {

        try {

            const transfer = new DataTransfer();

            records.forEach(function (record) {

                transfer.items.add(record.file);

            });

            input.files = transfer.files;

            return input.files.length === records.length;

        } catch (error) {

            return false;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ADD EXCEL FILES
    |--------------------------------------------------------------------------
    */

    function addFiles(files) {

        if (isGenerating) {
            return;
        }

        const selected = Array.from(files);

        const errors = [];

        hideError();

        for (const file of selected) {

            /*
            | XLSX FORMAT ONLY
            */

            if (!/\.xlsx$/i.test(file.name)) {

                errors.push(
                    file.name + ' must be an XLSX file.'
                );

                continue;
            }


            /*
            | FILE SIZE VALIDATION
            */

            if (
                file.size === 0 ||
                file.size > MAX_SIZE
            ) {

                errors.push(
                    file.name +
                    ' is empty or exceeds 15 MB.'
                );

                continue;
            }


            /*
            | MAXIMUM 11 FILES
            */

            if (records.length >= MAX_FILES) {

                errors.push(
                    'You can upload a maximum of 11 Class Records.'
                );

                break;
            }


            /*
            | PREVENT DUPLICATE FILE SELECTION
            */

            const exists = records.some(function (record) {

                return (

                    record.file.name.toLowerCase() ===
                        file.name.toLowerCase() &&

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


            /*
            | ADD FILE TO LIST
            */

            records.push({

                id: nextId++,

                file: file

            });
        }

        const synced = syncInput();

        renderFiles();

        if (!synced) {

            button.disabled = true;

            errors.unshift(
                'Unable to update the file selection. ' +
                'Please use a modern browser.'
            );
        }

        if (errors.length > 0) {

            showError(errors[0]);

        }
    }


    /*
    |--------------------------------------------------------------------------
    | FILE INPUT CHANGE
    |--------------------------------------------------------------------------
    */

    input.addEventListener('change', function () {

        addFiles(input.files);

    });


    /*
    |--------------------------------------------------------------------------
    | KEYBOARD ACCESSIBILITY
    |--------------------------------------------------------------------------
    */

    dropzone.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Enter' ||
                event.key === ' '
            ) {

                event.preventDefault();

                input.click();
            }
        }
    );


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


    dropzone.addEventListener('dragleave', function (event) {

        if (dropzone.contains(event.relatedTarget)) {
            return;
        }

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

        addFiles(event.dataTransfer.files);

    });


    /*
    |--------------------------------------------------------------------------
    | RENDER SELECTED FILES
    |--------------------------------------------------------------------------
    */

    function renderFiles() {

        fileList.replaceChildren();

        const total = records.length;

        fileCount.textContent =
            total + ' / ' + MAX_FILES;

        selectionPercentage.textContent =
            total + ' / ' + MAX_FILES;

        selectionProgress.style.width =
            ((total / MAX_FILES) * 100) + '%';

        selectedSection.classList.toggle(
            'hidden',
            total === 0
        );

        selectionStatus.textContent =
            total === 0

                ? 'No files selected yet'

                : total +
                  ' Class Record' +
                  (total === 1 ? '' : 's') +
                  ' ready for generation';


        /*
        | ENABLE GENERATE BUTTON
        */

        button.disabled =
            isGenerating ||
            total < 1 ||
            total > MAX_FILES;


        /*
        | CREATE FILE CARDS
        */

        records.forEach(function (record, index) {

            const row = document.createElement('div');

            row.className =
                'flex items-center justify-between gap-3 ' +
                'rounded-xl border border-slate-200 ' +
                'bg-white p-4 transition ' +
                'hover:border-teal-300 hover:bg-slate-50';


            /*
            | FILE ICON
            */

            const fileIcon = document.createElement('div');

            fileIcon.className =
                'flex h-11 w-11 shrink-0 ' +
                'items-center justify-center rounded-xl ' +
                'bg-emerald-50 text-emerald-600';

            fileIcon.innerHTML =
                '<i data-lucide="file-spreadsheet" ' +
                'class="h-5 w-5"></i>';


            /*
            | FILE DETAILS
            */

            const details = document.createElement('div');

            details.className = 'min-w-0 flex-1';

            const filename = document.createElement('p');

            filename.className =
                'break-all text-sm font-bold text-slate-800';

            filename.textContent = record.file.name;


            const metadata = document.createElement('p');

            metadata.className =
                'mt-1 text-[11px] text-slate-500';

            metadata.textContent =
                'File ' + (index + 1) +
                ' · ' + formatSize(record.file.size);


            details.appendChild(filename);

            details.appendChild(metadata);


            /*
            | REMOVE BUTTON
            */

            const removeButton = document.createElement('button');

            removeButton.type = 'button';

            removeButton.dataset.removeId = record.id;

            removeButton.setAttribute(
                'aria-label',
                'Remove ' + record.file.name
            );

            removeButton.className =
                'flex h-9 w-9 shrink-0 items-center ' +
                'justify-center rounded-lg text-slate-400 ' +
                'transition hover:bg-red-50 hover:text-red-600';

            removeButton.innerHTML =
                '<i data-lucide="x" class="h-4 w-4"></i>';


            row.appendChild(fileIcon);

            row.appendChild(details);

            row.appendChild(removeButton);

            fileList.appendChild(row);
        });

        refreshIcons();
    }


    /*
    |--------------------------------------------------------------------------
    | REMOVE INDIVIDUAL FILE
    |--------------------------------------------------------------------------
    */

    fileList.addEventListener('click', function (event) {

        if (isGenerating) {
            return;
        }

        const removeButton = event.target.closest(
            '[data-remove-id]'
        );

        if (!removeButton) {
            return;
        }

        const id = Number(
            removeButton.dataset.removeId
        );

        records = records.filter(function (record) {

            return record.id !== id;

        });

        hideError();

        if (!syncInput()) {

            showError(
                'Unable to update selected files.'
            );
        }

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

        previousBodyOverflow =
            document.body.style.overflow;

        document.body.style.overflow = 'hidden';

        processingOverlay.classList.remove('hidden');
        processingOverlay.classList.add('flex');

        processingOverlay.focus();

        processingStage.textContent =
            'Uploading Class Records...';

        processingPercent.textContent = '0%';

        processingDescription.textContent =
            'Sending your Excel files to Laravel.';

        processingFileCount.textContent =
            records.length +
            ' Class Record' +
            (records.length === 1 ? '' : 's') +
            ' selected';

        processingDot.classList.remove(
            'bg-emerald-500'
        );

        processingDot.classList.add(
            'bg-teal-500',
            'animate-pulse'
        );

        uploadProgressBar.style.width = '0%';

        uploadProgressBar.classList.remove('hidden');

        serverProgressBar.classList.add('hidden');

        processingTrack.setAttribute(
            'aria-valuenow',
            '0'
        );

        processingTrack.setAttribute(
            'aria-label',
            'Uploading Class Records'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE ACTUAL UPLOAD PERCENTAGE
    |--------------------------------------------------------------------------
    */

    function updateUploadProgress(percent) {

        const value = Math.min(
            100,
            Math.max(0, Math.round(percent))
        );

        uploadProgressBar.style.width =
            value + '%';

        processingPercent.textContent =
            value + '%';

        processingStage.textContent =
            'Uploading Class Records...';

        processingDescription.textContent =
            'Uploading ' +
            records.length +
            ' Excel Class Record' +
            (records.length === 1 ? '' : 's') +
            '. Please keep this page open.';

        processingTrack.setAttribute(
            'aria-valuenow',
            String(value)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SERVER IS GENERATING EXCEL
    |--------------------------------------------------------------------------
    |
    | Upload percentage is finished.
    |
    | Server progress cannot be measured
    | accurately without a backend progress API.
    |
    | Show an animated processing bar instead.
    |
    */

    function showServerProcessing() {

        processingStage.textContent =
            'Generating Master Sheet...';

        processingPercent.textContent =
            'Processing';

        processingDescription.textContent =
            'Upload complete. Laravel is validating ' +
            'student names, inserting grades, and ' +
            'building the Excel Master Sheet.';

        uploadProgressBar.classList.add('hidden');

        serverProgressBar.classList.remove('hidden');

        processingTrack.removeAttribute('aria-valuenow');

        processingTrack.setAttribute(
            'aria-label',
            'Generating Master Sheet'
        );

        processingTrack.setAttribute(
            'aria-valuetext',
            'Processing Excel workbook'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATION COMPLETE
    |--------------------------------------------------------------------------
    */

    function showCompleted() {

        processingStage.textContent =
            'Master Sheet Ready!';

        processingPercent.textContent =
            '100%';

        processingDescription.textContent =
            'Your Master Sheet has been generated. ' +
            'The Excel download is starting.';

        processingDot.classList.remove(
            'animate-pulse',
            'bg-teal-500'
        );

        processingDot.classList.add(
            'bg-emerald-500'
        );

        serverProgressBar.classList.add('hidden');

        uploadProgressBar.classList.remove('hidden');

        uploadProgressBar.style.width = '100%';

        processingTrack.removeAttribute('aria-valuetext');

        processingTrack.setAttribute(
            'aria-valuenow',
            '100'
        );

        processingTrack.setAttribute(
            'aria-label',
            'Master Sheet ready'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE PROCESSING OVERLAY
    |--------------------------------------------------------------------------
    */

    function closeProcessing() {

        processingOverlay.classList.add('hidden');

        processingOverlay.classList.remove('flex');

        document.body.style.overflow =
            previousBodyOverflow;

        isGenerating = false;

        buttonText.textContent =
            'Generate & Download Master Sheet';

        renderFiles();
    }


    /*
    |--------------------------------------------------------------------------
    | HANDLE GENERATION FAILURE
    |--------------------------------------------------------------------------
    */

    function generationFailed(message) {

        closeProcessing();

        showError(message);

        errorBox.scrollIntoView({

            behavior: 'smooth',

            block: 'center'

        });
    }


    /*
    |--------------------------------------------------------------------------
    | GET DOWNLOAD FILENAME
    |--------------------------------------------------------------------------
    */

    function getDownloadFilename(xhr) {

        const disposition =
            xhr.getResponseHeader('Content-Disposition') || '';

        /*
        | RFC 5987 encoded filename.
        */

        const encoded = disposition.match(
            /filename\*=UTF-8''([^;]+)/i
        );

        if (encoded) {

            try {

                return decodeURIComponent(
                    encoded[1].replace(/^"|"$/g, '')
                );

            } catch (error) {
                // Use fallback filename.
            }
        }

        /*
        | Normal filename.
        */

        const normal = disposition.match(
            /filename="([^"]+)"|filename=([^;]+)/i
        );

        if (normal) {

            return (
                normal[1] || normal[2]
            ).trim().replace(/^"|"$/g, '');
        }

        return 'MasterSheet.xlsx';
    }


    /*
    |--------------------------------------------------------------------------
    | READ LARAVEL ERROR RESPONSE
    |--------------------------------------------------------------------------
    */

    async function getServerError(xhr) {

        if (xhr.status === 419) {

            return 'Your session expired. ' +
                   'Refresh the page and try again.';
        }

        if (xhr.status === 413) {

            return 'The uploaded files exceed the server ' +
                   'upload limit. Please reduce their size.';
        }

        if (xhr.status === 403) {

            return 'You do not have permission to ' +
                   'generate this Master Sheet.';
        }

        try {

            const raw = xhr.response
                ? await xhr.response.text()
                : '';

            const data = JSON.parse(raw);

            /*
            | Laravel validation errors.
            */

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

            // Response may be an HTML error page.
        }

        return 'Master Sheet generation failed. ' +
               'Please check your Excel files and try again.';
    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT FORM WITH AJAX UPLOAD PROGRESS
    |--------------------------------------------------------------------------
    */

    form.addEventListener('submit', function (event) {

        event.preventDefault();

        if (isGenerating) {
            return;
        }

        hideError();


        /*
        | VALIDATE NUMBER OF CLASS RECORDS
        */

        if (
            records.length < 1 ||
            records.length > MAX_FILES
        ) {

            showError(
                'Please select between 1 and 11 Class Records.'
            );

            return;
        }


        /*
        | VALIDATE SELECTED FILES
        */

        for (const record of records) {

            const file = record.file;

            if (!/\.xlsx$/i.test(file.name)) {

                showError(
                    file.name + ' must be an XLSX file.'
                );

                return;
            }

            if (
                file.size === 0 ||
                file.size > MAX_SIZE
            ) {

                showError(
                    file.name +
                    ' is empty or exceeds 15 MB.'
                );

                return;
            }
        }


        /*
        | SYNCHRONIZE ACTUAL FILE INPUT
        */

        if (
            !syncInput() ||
            input.files.length !== records.length
        ) {

            showError(
                'Unable to attach the selected Excel files.'
            );

            return;
        }


        /*
        | LOCK FORM DURING GENERATION
        */

        isGenerating = true;

        button.disabled = true;

        buttonText.textContent =
            'Generating Master Sheet...';

        showProcessing();


        /*
        | PREPARE REQUEST
        */

        const xhr = new XMLHttpRequest();

        const formData = new FormData(form);

        xhr.open(
            'POST',
            form.action,
            true
        );

        /*
        | Request JSON validation errors.
        */

        xhr.setRequestHeader(
            'X-Requested-With',
            'XMLHttpRequest'
        );

        xhr.setRequestHeader(
            'Accept',
            'application/json'
        );

        /*
        | Receive Excel as binary data.
        */

        xhr.responseType = 'blob';


        /*
        | TRACK REAL UPLOAD PERCENTAGE
        */

        xhr.upload.addEventListener(
            'progress',
            function (event) {

                if (event.lengthComputable) {

                    const percent =
                        (event.loaded / event.total) * 100;

                    updateUploadProgress(percent);
                }
            }
        );


        /*
        | UPLOAD FINISHED; SERVER PROCESSING
        */

        xhr.upload.addEventListener(
            'load',
            function () {

                if (isGenerating) {
                    showServerProcessing();
                }
            }
        );


        /*
        | HANDLE SERVER RESPONSE
        */

        xhr.onload = async function () {

            const contentType = (
                xhr.getResponseHeader('Content-Type') || ''
            ).toLowerCase();

            const disposition = (
                xhr.getResponseHeader('Content-Disposition') || ''
            ).toLowerCase();

            const isExcel =
                contentType.includes(
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ) ||
                (
                    contentType.includes('application/octet-stream') &&
                    disposition.includes('attachment')
                );

            /*
            | SUCCESS: EXCEL RECEIVED
            */

            if (
                xhr.status >= 200 &&
                xhr.status < 300 &&
                isExcel &&
                xhr.response &&
                xhr.response.size > 0
            ) {

                const blob = xhr.response;

                const filename =
                    getDownloadFilename(xhr);

                const downloadUrl =
                    URL.createObjectURL(blob);

                const link =
                    document.createElement('a');

                link.href = downloadUrl;

                link.download = filename;

                document.body.appendChild(link);

                /*
                | START DOWNLOAD
                */

                link.click();

                link.remove();

                /*
                | RELEASE BLOB URL
                */

                setTimeout(function () {

                    URL.revokeObjectURL(
                        downloadUrl
                    );

                }, 60000);


                /*
                | SUCCESS SCREEN
                */

                showCompleted();

                setTimeout(function () {

                    closeProcessing();

                }, 1200);

                return;
            }


            /*
            | FAILURE: READ LARAVEL MESSAGE
            */

            const message =
                await getServerError(xhr);

            generationFailed(message);
        };


        /*
        | NETWORK ERROR
        */

        xhr.onerror = function () {

            generationFailed(
                'Network error while generating ' +
                'the Master Sheet. Please try again.'
            );
        };


        /*
        | REQUEST ABORTED
        */

        xhr.onabort = function () {

            generationFailed(
                'Master Sheet generation was interrupted.'
            );
        };


        /*
        | SEND REQUEST
        */

        try {

            xhr.send(formData);

        } catch (error) {

            generationFailed(
                'Unable to start the upload. Please try again.'
            );
        }

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
