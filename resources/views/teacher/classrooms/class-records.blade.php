
@extends('layouts.teacher')

@section('title', 'Class Records')
@section('page_heading', 'Class Records')

@section('content')

<div class="mx-auto max-w-7xl space-y-6">

    <!-- BACK BUTTON -->
    <a href="{{ route('teacher.classrooms.show', $section) }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-teal-700">

        <svg width="18" height="18" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5"/>
            <path d="m12 19-7-7 7-7"/>
        </svg>

        Back to Classroom
    </a>


    <!-- HEADER -->
    <div class="flex flex-wrap items-center justify-between gap-4">

        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-teal-700">
                Academic Management
            </p>

            <h1 class="mt-2 text-3xl font-extrabold text-slate-900">
                Class Records
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Select and organize your Excel Class Records before uploading.
            </p>
        </div>

        <span class="rounded-full border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-bold text-blue-700">
            Frontend Preview
        </span>
    </div>


    <!-- CLASSROOM BANNER -->
    <section class="rounded-2xl bg-[#142b61] p-6 text-white shadow-sm">

        <div class="flex flex-wrap items-center justify-between gap-5">

            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-200">
                    Selected Classroom
                </p>

                <h2 class="mt-2 text-2xl font-extrabold">
                    {{ $section->name }}
                </h2>

                <p class="mt-2 text-sm text-blue-100">
                    Grade {{ $section->grade_level }}
                    · {{ $section->strand?->code ?? 'N/A' }}
                </p>
            </div>

            <div class="rounded-xl border border-white/15 bg-white/10 px-5 py-3">

                <p class="text-xs text-blue-200">
                    School Year
                </p>

                <p class="mt-1 text-lg font-bold">
                    {{ $section->schoolYear?->name ?? 'N/A' }}
                </p>
            </div>
        </div>
    </section>


    <!-- SUMMARY -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                Selected Files
            </p>

            <p id="fileCount"
               class="mt-3 text-3xl font-extrabold text-[#142b61]">
                0
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Excel Class Records
            </p>
        </div>


        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                Total File Size
            </p>

            <p id="totalSize"
               class="mt-3 text-3xl font-extrabold text-[#142b61]">
                0 MB
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Combined size
            </p>
        </div>


        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                Upload Status
            </p>

            <p class="mt-3 text-xl font-extrabold text-amber-600">
                Not Submitted
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Backend integration pending
            </p>
        </div>

    </div>


    <!-- MAIN CONTENT -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        <div class="space-y-6 xl:col-span-2">

            <!-- UPLOAD BOX -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="text-lg font-bold text-slate-900">
                        Add Excel Class Records
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        You can select multiple files at once.
                    </p>
                </div>

                <div class="p-6">

                    @if($isCurrent)

                        <div id="dropZone"
                             class="rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-5 py-12 text-center transition-colors">

                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-teal-100 text-teal-700">

                                <svg width="32" height="32"
                                     viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8"
                                     stroke-linecap="round" stroke-linejoin="round">

                                    <path d="M12 16V4"/>
                                    <path d="m7 9 5-5 5 5"/>
                                    <path d="M4 16v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/>
                                </svg>
                            </div>

                            <h4 class="mt-5 text-lg font-bold text-slate-800">
                                Drag and Drop Excel Files
                            </h4>

                            <p class="mt-2 text-sm text-slate-500">
                                Or click below to select files.
                            </p>

                            <input id="excelInput"
                                   type="file"
                                   accept=".xlsx,.xls"
                                   multiple
                                   class="sr-only">

                            <label for="excelInput"
                                   class="mt-6 inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-[#0d9488] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#0f766e]">

                                <svg width="18" height="18"
                                     viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">

                                    <path d="M12 5v14"/>
                                    <path d="M5 12h14"/>
                                </svg>

                                Browse Excel Files
                            </label>

                            <p class="mt-5 text-xs text-slate-400">
                                .xlsx and .xls · Maximum 10 files
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Up to 10 MB each · 50 MB total
                            </p>

                        </div>

                    @else

                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 text-center">

                            <p class="font-bold text-amber-900">
                                Archived Classroom
                            </p>

                            <p class="mt-2 text-sm text-amber-700">
                                You cannot add new Class Records to an archived classroom.
                            </p>
                        </div>

                    @endif

                    <!-- ERROR / INFO MESSAGES -->
                    <div id="fileMessage"
                         role="status"
                         aria-live="polite"
                         class="mt-4 hidden rounded-xl border px-4 py-3 text-sm">
                    </div>
                </div>
            </section>


            <!-- FILE LIST -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <!-- FILE LIST HEADING -->
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 px-6 py-5">

                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Selected Excel Files
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Each Excel file appears separately below.
                        </p>
                    </div>

                    <button id="clearFiles"
                            type="button"
                            disabled
                            class="cursor-pointer rounded-xl border border-red-100 bg-red-50 px-4 py-2 text-xs font-bold text-red-600 transition hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-40">

                        Clear All
                    </button>
                </div>


                <!-- DYNAMIC FILE ROWS -->
                <div id="selectedFileList"
                     class="divide-y divide-slate-100">
                </div>


                <!-- EMPTY STATE -->
                <div id="emptyState"
                     class="px-6 py-14 text-center">

                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                        <svg width="30" height="30"
                             viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">

                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>
                            <path d="M14 2v6h6"/>
                            <path d="M8 13h8M8 17h8"/>
                        </svg>
                    </div>

                    <h4 class="mt-4 font-bold text-slate-700">
                        No Excel Files Selected
                    </h4>

                    <p class="mt-2 text-sm text-slate-400">
                        Select Mathematics, English, Science or other subject files.
                    </p>
                </div>


                <!-- SUBMISSION FOOTER -->
                <div class="border-t border-slate-100 bg-slate-50 p-6">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <div>
                            <p id="selectionSummary"
                               class="text-sm font-bold text-slate-800">
                                No files selected
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Review or remove files before submission.
                            </p>
                        </div>

                        <button type="button"
                                id="submitFiles"
                                disabled
                                class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-[#0d9488] px-6 py-3.5 text-sm font-bold text-white transition hover:bg-[#0f766e] disabled:cursor-not-allowed disabled:bg-slate-300">

                            <svg width="18" height="18"
                                 viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 16V4"/>
                                <path d="m7 9 5-5 5 5"/>
                                <path d="M4 16v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/>
                            </svg>

                            <span id="submitText">
                                Submit Files (Preview)
                            </span>
                        </button>
                    </div>


                    <!-- SUBMISSION PREVIEW RESULT -->
                    <div id="submissionResult"
                         role="status"
                         aria-live="polite"
                         class="mt-5 hidden rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                    </div>
                </div>

            </section>

        </div>


        <!-- SIDEBAR -->
        <aside class="space-y-5">

            <!-- INSTRUCTIONS -->
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <h3 class="text-lg font-bold text-slate-900">
                    How to Upload
                </h3>

                <div class="mt-6 space-y-6">

                    <div class="flex gap-3">

                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#142b61] text-xs font-bold text-white">
                            01
                        </span>

                        <div>
                            <h4 class="text-sm font-bold text-slate-800">
                                Select Excel Files
                            </h4>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Choose all subject Class Records.
                            </p>
                        </div>
                    </div>


                    <div class="flex gap-3">

                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#142b61] text-xs font-bold text-white">
                            02
                        </span>

                        <div>
                            <h4 class="text-sm font-bold text-slate-800">
                                Review Your Files
                            </h4>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                All selected files appear in individual rows.
                            </p>
                        </div>
                    </div>


                    <div class="flex gap-3">

                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#142b61] text-xs font-bold text-white">
                            03
                        </span>

                        <div>
                            <h4 class="text-sm font-bold text-slate-800">
                                Remove If Needed
                            </h4>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Remove incorrect files or clear everything.
                            </p>
                        </div>
                    </div>


                    <div class="flex gap-3">

                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#142b61] text-xs font-bold text-white">
                            04
                        </span>

                        <div>
                            <h4 class="text-sm font-bold text-slate-800">
                                Submit Class Records
                            </h4>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Backend uploading will be implemented later.
                            </p>
                        </div>
                    </div>

                </div>
            </section>


            <!-- NOTE -->
            <section class="rounded-2xl border border-teal-100 bg-teal-50 p-5">

                <h3 class="text-sm font-bold text-teal-900">
                    Multiple Subjects Supported
                </h3>

                <p class="mt-2 text-xs leading-6 text-teal-800">
                    You can select Mathematics, English, Science,
                    Filipino, Research and other subjects for the
                    same classroom.
                </p>

                <p class="mt-3 text-xs leading-6 text-teal-800">
                    No files are saved to the server yet.
                    File selections will reset when you refresh this page.
                </p>
            </section>

        </aside>
    </div>

</div>


<!-- ==========================================
     JAVASCRIPT - FILE MANAGEMENT
========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('excelInput');
    const dropZone = document.getElementById('dropZone');

    const list = document.getElementById('selectedFileList');
    const emptyState = document.getElementById('emptyState');

    const fileCount = document.getElementById('fileCount');
    const totalSize = document.getElementById('totalSize');

    const clearButton = document.getElementById('clearFiles');
    const submitButton = document.getElementById('submitFiles');
    const submitText = document.getElementById('submitText');

    const selectionSummary = document.getElementById('selectionSummary');

    const message = document.getElementById('fileMessage');
    const submissionResult = document.getElementById('submissionResult');

    if (!input || !dropZone) {
        return;
    }

    const MAX_FILES = 10;
    const MAX_FILE_SIZE = 10 * 1024 * 1024;
    const MAX_TOTAL_SIZE = 50 * 1024 * 1024;

    let selectedFiles = [];


    // ------------------------------------------
    // FORMAT FILE SIZE
    // ------------------------------------------
    function formatBytes(bytes) {

        if (bytes < 1024) {
            return `${bytes} B`;
        }

        if (bytes < 1048576) {
            return `${(bytes / 1024).toFixed(1)} KB`;
        }

        return `${(bytes / 1048576).toFixed(2)} MB`;
    }


    // ------------------------------------------
    // DISPLAY MESSAGE
    // ------------------------------------------
    function showMessage(text, isError = false) {

        message.classList.remove('hidden');

        message.className = isError
            ? 'mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700'
            : 'mt-4 rounded-xl border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-800';

        message.textContent = text;
    }


    // ------------------------------------------
    // REMOVE INDIVIDUAL FILE
    // ------------------------------------------
    function removeFile(index) {

        selectedFiles.splice(index, 1);

        submissionResult.classList.add('hidden');
        message.classList.add('hidden');

        renderFiles();
    }


    // ------------------------------------------
    // DISPLAY ALL SELECTED FILE ROWS
    // ------------------------------------------
    function renderFiles() {

        list.replaceChildren();

        const count = selectedFiles.length;

        const bytes = selectedFiles.reduce(
            (sum, item) => sum + item.file.size,
            0
        );

        fileCount.textContent = count;
        totalSize.textContent = formatBytes(bytes);

        emptyState.classList.toggle('hidden', count > 0);

        clearButton.disabled = count === 0;
        submitButton.disabled = count === 0;

        selectionSummary.textContent = count === 0
            ? 'No files selected'
            : `${count} Excel file${count === 1 ? '' : 's'} ready for submission`;

        submitText.textContent = count === 0
            ? 'Submit Files (Preview)'
            : `Submit ${count} File${count === 1 ? '' : 's'} (Preview)`;


        selectedFiles.forEach((item, index) => {

            // MAIN FILE ROW
            const row = document.createElement('div');

            row.className =
                'flex flex-col gap-4 px-6 py-5 transition-colors hover:bg-slate-50 sm:flex-row sm:items-center';


            // FILE NUMBER
            const number = document.createElement('div');

            number.className =
                'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-teal-50 text-sm font-extrabold text-teal-700';

            number.textContent = String(index + 1).padStart(2, '0');


            // INFORMATION AREA
            const details = document.createElement('div');

            details.className = 'min-w-0 flex-1';


            // FILE NAME
            const name = document.createElement('p');

            name.className =
                'break-all text-sm font-bold text-slate-900';

            name.textContent = item.file.name;


            // FILE SIZE AND READY STATUS
            const meta = document.createElement('div');

            meta.className =
                'mt-2 flex flex-wrap items-center gap-3';


            const size = document.createElement('span');

            size.className = 'text-xs text-slate-500';
            size.textContent = formatBytes(item.file.size);


            const status = document.createElement('span');

            status.className =
                'rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700';

            status.textContent = '✓ Ready to Submit';


            meta.append(size, status);


            // SUBJECT NAME INPUT
            const subject = document.createElement('input');

            subject.type = 'text';
            subject.maxLength = 120;
            subject.placeholder = 'Subject (e.g. Mathematics)';
            subject.value = item.subject;

            subject.setAttribute(
                'aria-label',
                `Subject name for ${item.file.name}`
            );

            subject.className =
                'mt-3 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs outline-none focus:border-teal-500 sm:max-w-xs';

            subject.addEventListener('input', function () {
                item.subject = subject.value;
                submissionResult.classList.add('hidden');
            });


            details.append(name, meta, subject);


            // REMOVE BUTTON
            const removeButton = document.createElement('button');

            removeButton.type = 'button';

            removeButton.className =
                'inline-flex cursor-pointer items-center justify-center gap-2 self-start rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-xs font-bold text-red-600 transition hover:bg-red-100 sm:self-center';

            removeButton.setAttribute(
                'aria-label',
                `Remove ${item.file.name}`
            );

            removeButton.textContent = '✕ Remove';


            removeButton.addEventListener('click', function () {
                removeFile(index);
            });


            // COMBINE ROW
            row.append(number, details, removeButton);

            list.appendChild(row);
        });
    }


    // ------------------------------------------
    // ADD NEW EXCEL FILES
    // ------------------------------------------
    function addFiles(files) {

        const errors = [];
        let added = 0;

        for (const file of files) {

            // VALID EXCEL EXTENSION
            if (!/\.(xlsx|xls)$/i.test(file.name)) {
                errors.push(`${file.name} is not an Excel file.`);
                continue;
            }

            // INDIVIDUAL SIZE
            if (file.size > MAX_FILE_SIZE) {
                errors.push(`${file.name} exceeds 10 MB.`);
                continue;
            }

            // MAX FILE COUNT
            if (selectedFiles.length >= MAX_FILES) {
                errors.push('Maximum of 10 Excel files allowed.');
                break;
            }

            // DUPLICATE CHECK
            const exists = selectedFiles.some(item =>
                item.file.name === file.name &&
                item.file.size === file.size &&
                item.file.lastModified === file.lastModified
            );

            if (exists) {
                errors.push(`${file.name} is already selected.`);
                continue;
            }

            // TOTAL SIZE
            const currentSize = selectedFiles.reduce(
                (sum, item) => sum + item.file.size,
                0
            );

            if (currentSize + file.size > MAX_TOTAL_SIZE) {
                errors.push('Total file size cannot exceed 50 MB.');
                continue;
            }

            // ADD FILE
            selectedFiles.push({
                file: file,
                subject: ''
            });

            added++;
        }

        submissionResult.classList.add('hidden');

        renderFiles();

        if (errors.length > 0) {
            showMessage(errors.join(' '), true);
        } else if (added > 0) {
            showMessage(
                `${added} file(s) added successfully to the selection.`
            );
        }
    }


    // ------------------------------------------
    // BROWSE FILES
    // ------------------------------------------
    input.addEventListener('change', function (event) {

        addFiles(Array.from(event.target.files));

        // ALLOW RESELECTING SAME FILE AFTER REMOVAL
        input.value = '';
    });


    // ------------------------------------------
    // DRAG AND DROP
    // ------------------------------------------
    dropZone.addEventListener('dragover', function (event) {

        event.preventDefault();

        dropZone.classList.add(
            'border-teal-500',
            'bg-teal-50'
        );
    });


    dropZone.addEventListener('dragleave', function () {

        dropZone.classList.remove(
            'border-teal-500',
            'bg-teal-50'
        );
    });


    dropZone.addEventListener('drop', function (event) {

        event.preventDefault();

        dropZone.classList.remove(
            'border-teal-500',
            'bg-teal-50'
        );

        addFiles(Array.from(event.dataTransfer.files));
    });


    // ------------------------------------------
    // CLEAR ALL FILES
    // ------------------------------------------
    clearButton.addEventListener('click', function () {

        selectedFiles = [];

        input.value = '';

        message.classList.add('hidden');
        submissionResult.classList.add('hidden');

        renderFiles();
    });


    // ------------------------------------------
    // SUBMISSION PREVIEW
    // FRONTEND ONLY - NO ACTUAL UPLOAD
    // ------------------------------------------
    submitButton.addEventListener('click', function () {

        if (selectedFiles.length === 0) {
            return;
        }

        const count = selectedFiles.length;

        submissionResult.classList.remove('hidden');

        submissionResult.textContent =
            `${count} Excel file(s) selected and ready. ` +
            'This is a frontend preview only. ' +
            'No files have been uploaded or saved yet. ' +
            'Actual submission will be enabled when the Laravel backend is implemented.';

        submissionResult.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    });


    // INITIAL RENDER
    renderFiles();

});
</script>

@endsection
