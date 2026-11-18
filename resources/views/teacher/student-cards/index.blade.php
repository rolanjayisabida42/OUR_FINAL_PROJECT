
@extends('layouts.teacher')

@section('title', 'Student Cards')
@section('page_heading', 'Student Cards')

@section('content')

<div class="mx-auto max-w-7xl space-y-6 pb-10">

    <!-- =============================================== -->
    <!-- BACK BUTTON -->
    <!-- =============================================== -->

    <a
        href="{{ route('teacher.classrooms.show', $section) }}"
        class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-teal-700"
    >
        <span>←</span>
        Back to Classroom
    </a>


    <!-- =============================================== -->
    <!-- PAGE HEADER -->
    <!-- =============================================== -->

    <div class="flex flex-wrap items-center justify-between gap-4">

        <div>

            <p class="text-xs font-bold uppercase tracking-widest text-teal-700">
                Academic Management
            </p>

            <h1 class="mt-2 text-2xl font-extrabold text-[#142b61]">
                Student Card Generator
            </h1>

            <p class="mt-2 text-sm text-slate-500">

                {{ $section->name }}
                · Grade {{ $section->grade_level }}
                · {{ $section->strand?->code ?? 'N/A' }}

            </p>

        </div>


        <div class="rounded-xl border border-slate-200 bg-white px-5 py-3 shadow-sm">

            <p class="text-xs text-slate-500">
                School Year
            </p>

            <p class="mt-1 text-sm font-extrabold text-[#142b61]">
                {{ $section->schoolYear?->name ?? 'N/A' }}
            </p>

        </div>

    </div>


    <!-- =============================================== -->
    <!-- SUCCESS MESSAGE -->
    <!-- =============================================== -->

    @if(session('success'))

        <div
            role="status"
            class="flex items-start gap-3 rounded-2xl border border-teal-200 bg-teal-50 p-4 text-sm text-teal-800"
        >

            <span class="text-lg font-bold">✓</span>

            <div>
                <p class="font-bold">Success</p>

                <p class="mt-1">
                    {{ session('success') }}
                </p>
            </div>

        </div>

    @endif


    <!-- =============================================== -->
    <!-- ERROR MESSAGES -->
    <!-- =============================================== -->

    @if($errors->any() || $loadError)

        <div
            role="alert"
            class="rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700"
        >

            <p class="mb-2 font-bold">
                Please check the following:
            </p>

            @foreach($errors->all() as $error)

                <p class="mt-1">
                    • {{ $error }}
                </p>

            @endforeach


            @if($loadError)

                <p class="mt-1">
                    • {{ $loadError }}
                </p>

            @endif

        </div>

    @endif


    <!-- =============================================== -->
    <!-- SUMMARY CARDS -->
    <!-- =============================================== -->

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        <!-- FIRST AND SECOND SEMESTER -->

        @foreach([
            1 => 'First Semester',
            2 => 'Second Semester'
        ] as $semester => $label)

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                        {{ $label }}
                    </p>

                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-700">

                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>

                            <path d="M14 2v6h6M8 13h8M8 17h8"/>
                        </svg>

                    </span>

                </div>


                <p class="mt-3 text-3xl font-extrabold text-[#142b61]">
                    {{ $counts[$semester] ?? 0 }}
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Students Extracted
                </p>


                @if($uploaded[$semester] ?? false)

                    <p class="mt-3 text-xs font-bold text-teal-700">
                        ✓ Master Sheet Uploaded
                    </p>

                @else

                    <p class="mt-3 text-xs font-bold text-amber-600">
                        Not Yet Uploaded
                    </p>

                @endif

            </div>

        @endforeach


        <!-- UNIQUE STUDENTS -->

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                    Unique Students
                </p>

                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-[#142b61]">

                    <svg
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="9" cy="8" r="3"/>

                        <path d="M3 20v-2a6 6 0 0 1 12 0v2M17 5a3 3 0 0 1 0 6M18 14a5 5 0 0 1 3 5v1"/>
                    </svg>

                </span>

            </div>


            <p class="mt-3 text-3xl font-extrabold text-[#142b61]">
                {{ count($students) }}
            </p>

            <p class="mt-1 text-xs text-slate-500">
                One Student Card per student
            </p>

            <p class="mt-3 text-xs font-bold text-blue-700">
                Grade 11 / 12 Student Cards
            </p>

        </div>

    </div>


    <!-- =============================================== -->
    <!-- UPLOAD MASTER SHEETS -->
    <!-- =============================================== -->

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

        @if($isCurrent)

            <form
                id="masterUploadForm"
                action="{{ route('teacher.classrooms.student-cards.upload', $section) }}"
                method="POST"
                enctype="multipart/form-data"
                class="p-6 md:p-8"
            >

                @csrf


                <!-- HEADER -->

                <div>

                    <h2 class="text-sm font-extrabold text-[#142b61]">
                        Upload Master Sheets
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Add 1 to 2 Excel Master Sheets.
                    </p>

                </div>


                <!-- =============================================== -->
                <!-- FILE INPUTS -->
                <!-- =============================================== -->

                <!-- File chooser only -->

                <input
                    id="masterPicker"
                    type="file"
                    accept=".xlsx"
                    multiple
                    class="sr-only"
                    tabindex="-1"
                >


                <!-- Actual Laravel upload input -->

                <input
                    id="master_sheets"
                    name="master_sheets[]"
                    type="file"
                    accept=".xlsx"
                    multiple
                    class="hidden"
                    tabindex="-1"
                    aria-hidden="true"
                >


                <!-- =============================================== -->
                <!-- DRAG AND DROP AREA -->
                <!-- =============================================== -->

                <div
                    id="masterDropZone"
                    role="button"
                    tabindex="0"
                    aria-label="Drop or choose Excel Master Sheets"
                    class="mt-5 flex min-h-[345px] cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-[#08bdb2] bg-[#effbf8] px-6 py-10 text-center transition-all duration-200 hover:border-[#008a80] hover:bg-[#e6f9f4] focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
                >

                    <!-- ICON -->

                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-slate-100 bg-white text-[#00877e] shadow-sm">

                        <svg
                            width="30"
                            height="30"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <rect x="8" y="3" width="12" height="15" rx="2"/>

                            <path d="M12 3v5h5"/>

                            <path d="M4 8H3a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-1"/>

                        </svg>

                    </div>


                    <h3 class="mt-5 text-base font-extrabold text-[#142b61]">
                        Drag &amp; Drop Master Sheets Here
                    </h3>


                    <p class="mt-2 text-sm text-[#6884a4]">
                        Or choose Excel files from your computer.
                    </p>


                    <!-- CHOOSE FILES BUTTON -->

                    <button
                        type="button"
                        id="browseFilesButton"
                        class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-[#00857b] px-7 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#006f68]"
                    >

                        <svg
                            width="17"
                            height="17"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v10H3z"/>

                            <path d="M3 12h18"/>
                        </svg>

                        Choose Excel Files

                    </button>


                    <!-- FILE REQUIREMENTS -->

                    <div class="mt-5 flex flex-wrap justify-center gap-2">

                        <span class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-[#6884a4]">
                            XLSX Format
                        </span>

                        <span class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-[#6884a4]">
                            Maximum 2 Files
                        </span>

                        <span class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-[#6884a4]">
                            12 MB Each
                        </span>

                    </div>

                </div>


                <!-- =============================================== -->
                <!-- FILE SELECTION STATUS -->
                <!-- =============================================== -->

                <div class="mt-8">

                    <div class="flex items-center justify-between gap-4">

                        <div>

                            <h3 class="text-sm font-bold text-[#142b61]">
                                File Selection
                            </h3>

                            <p
                                id="selectionDescription"
                                class="mt-1 text-xs text-[#6884a4]"
                            >
                                No files selected yet
                            </p>

                        </div>


                        <span
                            id="selectedCount"
                            class="text-xs font-extrabold text-[#00877e]"
                        >
                            0 / 2
                        </span>

                    </div>


                    <!-- SELECTION PROGRESS -->

                    <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-slate-100">

                        <div
                            id="selectionProgress"
                            class="h-full rounded-full bg-[#0aa69a] transition-all duration-300"
                            style="width: 0%"
                        ></div>

                    </div>

                </div>


                <!-- =============================================== -->
                <!-- SELECTED FILE LIST -->
                <!-- =============================================== -->

                <div
                    id="selectedFilesContainer"
                    class="mt-5 hidden overflow-hidden rounded-xl border border-slate-200"
                >

                    <div class="flex items-center justify-between bg-slate-50 px-4 py-3">

                        <p class="text-xs font-bold text-slate-700">
                            Selected Excel Files
                        </p>


                        <div class="flex items-center gap-3">

                            <button
                                type="button"
                                id="addMoreFiles"
                                class="text-xs font-bold text-teal-700 hover:text-teal-900"
                            >
                                + Add File
                            </button>


                            <button
                                type="button"
                                id="clearAllFiles"
                                class="text-xs font-bold text-red-600 hover:text-red-700"
                            >
                                Clear All
                            </button>

                        </div>

                    </div>


                    <div
                        id="selectedFilesList"
                        class="divide-y divide-slate-100"
                    ></div>

                </div>


                <!-- =============================================== -->
                <!-- KEEP PREVIOUS FILES -->
                <!-- =============================================== -->

                <label class="mt-6 flex cursor-pointer items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">

                    <input
                        id="keepPrevious"
                        name="keep_previous"
                        type="checkbox"
                        value="1"
                        class="mt-1 h-4 w-4 shrink-0 accent-teal-600"
                    >

                    <span>

                        <span class="block text-xs font-bold text-slate-700">
                            Keep Previously Uploaded Semester Files
                        </span>

                        <span class="mt-1 block text-xs leading-5 text-slate-500">

                            Optional. Check this if you are adding
                            the other semester later.

                            Leave unchecked to replace previously saved files.

                        </span>

                    </span>

                </label>


                <!-- ERROR -->

                <div
                    id="uploadError"
                    role="alert"
                    aria-live="polite"
                    class="mt-5 hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                ></div>


                <!-- =============================================== -->
                <!-- FOOTER ACTION -->
                <!-- =============================================== -->

                <div class="mt-7 flex flex-wrap items-center justify-between gap-5 border-t border-slate-100 pt-6">

                    <div class="flex items-center gap-3">

                        <!-- SHIELD ICON -->

                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#e9fbf4] text-[#078e79]">

                            <svg
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.9"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>

                                <path d="m9 12 2 2 4-4"/>

                            </svg>

                        </span>


                        <div>

                            <p class="text-xs font-bold text-[#142b61]">
                                Automatic Semester Detection
                            </p>

                            <p class="mt-1 text-xs text-[#6884a4]">
                                Reads the semester from Excel cell B5.
                            </p>

                        </div>

                    </div>


                    <!-- UPLOAD BUTTON -->

                    <button
                        id="uploadButton"
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-3 rounded-xl bg-[#0c9b90] px-6 py-3.5 text-sm font-extrabold text-white shadow-sm transition hover:bg-[#07857c] disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                    >

                        <!-- SPINNER -->

                        <svg
                            id="uploadSpinner"
                            class="hidden h-5 w-5 animate-spin"
                            viewBox="0 0 24 24"
                            fill="none"
                        >

                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                                stroke="currentColor"
                                stroke-width="3"
                                opacity=".25"
                            />

                            <path
                                d="M21 12a9 9 0 0 0-9-9"
                                stroke="currentColor"
                                stroke-width="3"
                                stroke-linecap="round"
                            />

                        </svg>


                        <svg
                            id="uploadButtonIcon"
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <path d="M12 16V4m-5 5 5-5 5 5"/>

                            <path d="M4 16v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/>

                        </svg>


                        <span id="uploadButtonText">
                            Upload Selected Master Sheets
                        </span>


                        <span aria-hidden="true">→</span>

                    </button>

                </div>

            </form>


            <!-- =============================================== -->
            <!-- OPTIONAL REMOVE UPLOADED SEMESTER -->
            <!-- =============================================== -->

            @if(\Illuminate\Support\Facades\Route::has('teacher.classrooms.student-cards.remove-semester'))

                @if(($uploaded[1] ?? false) || ($uploaded[2] ?? false))

                    <div class="flex flex-wrap gap-3 border-t border-slate-100 px-6 py-5 md:px-8">

                        @foreach([
                            1 => 'First',
                            2 => 'Second'
                        ] as $semester => $label)

                            @if($uploaded[$semester] ?? false)

                                <form
                                    action="{{ route('teacher.classrooms.student-cards.remove-semester', ['section' => $section, 'semester' => $semester]) }}"
                                    method="POST"
                                    onsubmit="return confirm('Remove saved {{ $label }} Semester Master Sheet?')"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100"
                                    >

                                        Remove {{ $label }} Semester

                                    </button>

                                </form>

                            @endif

                        @endforeach

                    </div>

                @endif

            @endif

        @else

            <div class="p-6">

                <p class="rounded-xl bg-amber-50 p-5 text-sm font-semibold text-amber-800">

                    This classroom is archived.
                    Master Sheet uploads are disabled.

                </p>

            </div>

        @endif

    </section>


    <!-- =============================================== -->
    <!-- STUDENTS READY FOR STUDENT CARDS -->
    <!-- =============================================== -->

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

        <!-- HEADER -->

        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 px-6 py-5 md:px-8">

            <div>

                <h2 class="text-base font-extrabold text-[#142b61]">
                    Students Ready for Student Cards
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Review student records before generating the Word documents.
                </p>

            </div>


            <span class="rounded-lg bg-teal-50 px-3 py-2 text-xs font-bold text-teal-700">

                {{ count($students) }} Students

            </span>

        </div>


        <!-- STUDENT TABLE -->

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">

                <thead class="bg-slate-50">

                    <tr class="text-xs font-bold uppercase tracking-wide text-slate-500">

                        <th class="px-6 py-4">
                            No.
                        </th>

                        <th class="px-6 py-4">
                            Student Name
                        </th>

                        <th class="px-6 py-4">
                            Gender
                        </th>

                        <th class="px-6 py-4 text-center">
                            First Semester
                        </th>

                        <th class="px-6 py-4 text-center">
                            Second Semester
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($students as $index => $student)

                        <tr class="transition hover:bg-slate-50">

                            <td class="px-6 py-4 text-slate-500">
                                {{ $index + 1 }}
                            </td>


                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $student['name'] }}
                            </td>


                            <td class="px-6 py-4 text-slate-600">
                                {{ $student['gender'] }}
                            </td>


                            @foreach([1, 2] as $semester)

                                <td class="px-6 py-4 text-center">

                                    @if(!empty($student['semesters'][$semester]['grades']))

                                        <span class="inline-flex rounded-full bg-teal-50 px-3 py-1 text-xs font-bold text-teal-700">
                                            ✓ Ready
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">
                                            Not Available
                                        </span>

                                    @endif

                                </td>

                            @endforeach

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="px-6 py-14 text-center">

                                <p class="text-3xl">📄</p>

                                <p class="mt-3 font-bold text-slate-700">
                                    No Student Records Yet
                                </p>

                                <p class="mt-2 text-xs text-slate-500">
                                    Upload a First Semester or Second Semester
                                    Master Sheet to get started.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- =============================================== -->
        <!-- GENERATE FOOTER -->
        <!-- =============================================== -->

        <div class="flex flex-wrap items-center justify-between gap-5 border-t border-slate-100 px-6 py-6 md:px-8">

            <div class="flex items-center gap-3">

                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-50 text-teal-700">

                    <svg
                        width="21"
                        height="21"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>

                        <path d="M14 2v6h6M8 13h8M8 17h8"/>

                    </svg>

                </span>


                <div>

                    <p class="text-xs font-bold text-[#142b61]">
                        Automatic Word Generation
                    </p>

                    <p class="mt-1 text-xs text-[#6884a4]">
                        One Word Student Card per student.
                    </p>

                </div>

            </div>


            @if($isCurrent && count($students) > 0 && !$loadError)

                <form
                    id="generateForm"
                    action="{{ route('teacher.classrooms.student-cards.generate', $section) }}"
                    method="POST"
                >

                    @csrf

                    <button
                        id="generateButton"
                        type="submit"
                        class="inline-flex items-center justify-center gap-3 rounded-xl bg-[#0c9b90] px-6 py-3.5 text-sm font-extrabold text-white shadow-sm transition hover:bg-[#07857c] disabled:cursor-not-allowed disabled:opacity-60"
                    >

                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <path d="M12 3v12m-5-5 5 5 5-5"/>

                            <path d="M5 17v3h14v-3"/>

                        </svg>


                        <span id="generateButtonText">
                            Generate &amp; Download Student Cards
                        </span>


                        <span>→</span>

                    </button>

                </form>

            @else

                <button
                    type="button"
                    disabled
                    class="cursor-not-allowed rounded-xl bg-slate-200 px-6 py-3 text-xs font-bold text-slate-500"
                >

                    Generate &amp; Download Student Cards

                </button>

            @endif

        </div>


        <!-- GENERATION RESULT -->

        <div
            id="generateFeedback"
            role="status"
            aria-live="polite"
            class="mx-6 mb-5 hidden rounded-xl border px-4 py-3 text-sm"
        ></div>

    </section>

</div>


<!-- =============================================== -->
<!-- FULL SCREEN GENERATION LOADER -->
<!-- =============================================== -->

<div
    id="generateOverlay"
    role="status"
    aria-live="polite"
    aria-label="Generating Student Cards"

    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/65 p-5 backdrop-blur-sm"
>

    <div class="w-full max-w-md rounded-3xl bg-white p-8 text-center shadow-2xl">

        <!-- SPINNER -->

        <div class="relative mx-auto flex h-24 w-24 items-center justify-center">

            <div class="absolute inset-0 animate-spin rounded-full border-4 border-slate-100 border-t-[#0c9b90]"></div>


            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-teal-50 text-teal-700">

                <svg
                    width="30"
                    height="30"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >

                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>

                    <path d="M14 2v6h6M8 13h8M8 17h8"/>

                </svg>

            </div>

        </div>


        <h2 class="mt-7 text-xl font-extrabold text-[#142b61]">
            Generating Student Cards
        </h2>


        <p class="mt-3 text-sm leading-6 text-slate-500">
            Processing student records and creating
            individual Word Student Cards.
        </p>


        <!-- ANIMATED LOADING BAR -->

        <div class="mt-6 h-2 overflow-hidden rounded-full bg-slate-100">

            <div class="h-full w-full animate-pulse rounded-full bg-[#0c9b90]"></div>

        </div>


        <p class="mt-5 text-xs font-medium text-slate-500">
            Your ZIP download will begin when generation is complete.
        </p>

    </div>

</div>


<!-- =============================================== -->
<!-- JAVASCRIPT -->
<!-- =============================================== -->

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | FILE UPLOAD ELEMENTS
    |--------------------------------------------------------------------------
    */

    const picker = document.getElementById('masterPicker');

    const payload = document.getElementById('master_sheets');

    const dropZone = document.getElementById('masterDropZone');

    const browseButton = document.getElementById('browseFilesButton');

    const uploadForm = document.getElementById('masterUploadForm');

    const selectedCount = document.getElementById('selectedCount');

    const selectionDescription = document.getElementById('selectionDescription');

    const selectionProgress = document.getElementById('selectionProgress');

    const selectedFilesContainer = document.getElementById('selectedFilesContainer');

    const selectedFilesList = document.getElementById('selectedFilesList');

    const uploadError = document.getElementById('uploadError');

    const uploadButton = document.getElementById('uploadButton');

    const uploadSpinner = document.getElementById('uploadSpinner');

    const uploadButtonText = document.getElementById('uploadButtonText');

    const uploadButtonIcon = document.getElementById('uploadButtonIcon');

    const MAX_FILES = 2;

    const MAX_BYTES = 12 * 1024 * 1024;

    let selectedFiles = [];


    /*
    |--------------------------------------------------------------------------
    | SHOW UPLOAD ERROR
    |--------------------------------------------------------------------------
    */

    function showError(message) {

        if (!uploadError) return;

        uploadError.textContent = message;

        uploadError.classList.remove('hidden');
    }


    function clearError() {

        if (!uploadError) return;

        uploadError.textContent = '';

        uploadError.classList.add('hidden');
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT FILE SIZE
    |--------------------------------------------------------------------------
    */

    function formatFileSize(bytes) {

        if (bytes >= 1024 * 1024) {

            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        return (bytes / 1024).toFixed(1) + ' KB';
    }


    /*
    |--------------------------------------------------------------------------
    | SYNC SELECTED FILES WITH LARAVEL
    |--------------------------------------------------------------------------
    |
    | This ensures that the submitted input contains
    | all selected Excel files.
    |
    | Browser picker is separate from Laravel payload.
    |
    */

    function commitFiles(files) {

        const transfer = new DataTransfer();

        files.forEach(function (file) {
            transfer.items.add(file);
        });

        payload.files = transfer.files;

        selectedFiles = files;

        renderFiles();
    }


    /*
    |--------------------------------------------------------------------------
    | DISPLAY FILE SELECTION
    |--------------------------------------------------------------------------
    */

    function renderFiles() {

        const total = selectedFiles.length;

        selectedCount.textContent = total + ' / 2';

        selectionProgress.style.width =
            ((total / MAX_FILES) * 100) + '%';


        if (total === 0) {

            selectionDescription.textContent =
                'No files selected yet';

        } else if (total === 1) {

            selectionDescription.textContent =
                '1 Excel file selected';

        } else {

            selectionDescription.textContent =
                '2 Excel files selected';
        }


        selectedFilesContainer.classList.toggle(
            'hidden',
            total === 0
        );


        selectedFilesList.replaceChildren();


        selectedFiles.forEach(function (file, index) {

            /*
            |--------------------------------------------------------------------------
            | FILE ROW
            |--------------------------------------------------------------------------
            */

            const row = document.createElement('div');

            row.className =
                'flex flex-wrap items-center justify-between gap-3 px-4 py-3';


            const left = document.createElement('div');

            left.className =
                'flex min-w-0 items-center gap-3';


            const fileIcon = document.createElement('span');

            fileIcon.className =
                'flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-teal-50 text-xs font-extrabold text-teal-700';

            fileIcon.textContent = 'XLSX';


            const details = document.createElement('div');

            details.className = 'min-w-0';


            const filename = document.createElement('p');

            filename.className =
                'break-all text-sm font-semibold text-slate-800';

            filename.textContent = file.name;


            const fileSize = document.createElement('p');

            fileSize.className =
                'mt-1 text-xs text-slate-500';

            fileSize.textContent = formatFileSize(file.size);


            details.append(filename, fileSize);

            left.append(fileIcon, details);


            /*
            |--------------------------------------------------------------------------
            | REMOVE FILE BUTTON
            |--------------------------------------------------------------------------
            */

            const removeButton = document.createElement('button');

            removeButton.type = 'button';

            removeButton.textContent = 'Remove';

            removeButton.className =
                'rounded-lg px-3 py-2 text-xs font-bold text-red-600 transition hover:bg-red-50';


            removeButton.addEventListener('click', function () {

                const updated = selectedFiles.filter(
                    function (_, currentIndex) {
                        return currentIndex !== index;
                    }
                );

                commitFiles(updated);

                clearError();
            });


            row.append(left, removeButton);

            selectedFilesList.appendChild(row);

        });
    }


    /*
    |--------------------------------------------------------------------------
    | ADD FILES
    |--------------------------------------------------------------------------
    |
    | Supports:
    |
    | 1. Selecting two files together.
    | 2. Selecting one file, then adding another.
    | 3. Dragging files into the upload area.
    |
    */

    function addFiles(fileCollection) {

        clearError();

        const incoming = Array.from(
            fileCollection || []
        );

        const next = [...selectedFiles];


        for (const file of incoming) {

            /*
            |--------------------------------------------------------------------------
            | VALIDATE FILE TYPE
            |--------------------------------------------------------------------------
            */

            if (!file.name.toLowerCase().endsWith('.xlsx')) {

                showError(
                    'Only XLSX Excel Master Sheets are supported.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDATE FILE SIZE
            |--------------------------------------------------------------------------
            */

            if (file.size === 0) {

                showError(
                    'The selected Excel file is empty.'
                );

                return;
            }


            if (file.size > MAX_BYTES) {

                showError(
                    'Each Excel file must not exceed 12 MB.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | PREVENT DUPLICATES
            |--------------------------------------------------------------------------
            */

            const duplicate = next.some(function (existing) {

                return (
                    existing.name === file.name &&
                    existing.size === file.size &&
                    existing.lastModified === file.lastModified
                );

            });


            if (!duplicate) {

                next.push(file);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | MAXIMUM TWO FILES
        |--------------------------------------------------------------------------
        */

        if (next.length > MAX_FILES) {

            showError(
                'You can upload a maximum of two Master Sheets: '
                + 'one First Semester and one Second Semester.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | APPLY FILE SELECTION
        |--------------------------------------------------------------------------
        */

        try {

            commitFiles(next);

        } catch (error) {

            console.error(error);

            showError(
                'Unable to attach the selected files. '
                + 'Please use an updated browser.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | OPEN FILE PICKER
    |--------------------------------------------------------------------------
    */

    function openPicker() {

        if (picker) {

            picker.click();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | INITIALIZE UPLOAD EVENTS
    |--------------------------------------------------------------------------
    */

    if (picker && payload && dropZone && uploadForm) {

        /*
        |--------------------------------------------------------------------------
        | BROWSE BUTTON
        |--------------------------------------------------------------------------
        */

        browseButton.addEventListener('click', function (event) {

            event.stopPropagation();

            openPicker();
        });


        /*
        |--------------------------------------------------------------------------
        | CLICK ON DROP ZONE
        |--------------------------------------------------------------------------
        */

        dropZone.addEventListener('click', function (event) {

            if (event.target.closest('#browseFilesButton')) {
                return;
            }

            openPicker();
        });


        /*
        |--------------------------------------------------------------------------
        | KEYBOARD ACCESS
        |--------------------------------------------------------------------------
        */

        dropZone.addEventListener('keydown', function (event) {

            if (
                event.key === 'Enter' ||
                event.key === ' '
            ) {

                event.preventDefault();

                openPicker();
            }
        });


        /*
        |--------------------------------------------------------------------------
        | FILE PICKER CHANGE
        |--------------------------------------------------------------------------
        */

        picker.addEventListener('change', function () {

            const chosen = Array.from(
                picker.files || []
            );

            /*
            | Reset picker after reading.
            | This permits adding another Excel file later.
            */

            picker.value = '';

            addFiles(chosen);
        });


        /*
        |--------------------------------------------------------------------------
        | DRAG ENTER / OVER
        |--------------------------------------------------------------------------
        */

        ['dragenter', 'dragover'].forEach(function (name) {

            dropZone.addEventListener(name, function (event) {

                event.preventDefault();

                event.stopPropagation();

                dropZone.classList.add(
                    'border-[#00857b]',
                    'bg-[#ddf7f0]',
                    'ring-2',
                    'ring-teal-200'
                );
            });

        });


        /*
        |--------------------------------------------------------------------------
        | DRAG LEAVE
        |--------------------------------------------------------------------------
        */

        dropZone.addEventListener('dragleave', function (event) {

            event.preventDefault();

            if (
                event.relatedTarget &&
                dropZone.contains(event.relatedTarget)
            ) {
                return;
            }

            dropZone.classList.remove(
                'border-[#00857b]',
                'bg-[#ddf7f0]',
                'ring-2',
                'ring-teal-200'
            );
        });


        /*
        |--------------------------------------------------------------------------
        | DROP FILES
        |--------------------------------------------------------------------------
        */

        dropZone.addEventListener('drop', function (event) {

            event.preventDefault();

            event.stopPropagation();

            dropZone.classList.remove(
                'border-[#00857b]',
                'bg-[#ddf7f0]',
                'ring-2',
                'ring-teal-200'
            );


            const files = event.dataTransfer?.files;

            if (files && files.length > 0) {

                addFiles(files);
            }
        });


        /*
        |--------------------------------------------------------------------------
        | PREVENT BROWSER FROM OPENING DROPPED FILES
        |--------------------------------------------------------------------------
        */

        document.addEventListener('dragover', function (event) {

            if (
                event.dataTransfer &&
                Array.from(event.dataTransfer.types).includes('Files')
            ) {

                event.preventDefault();
            }
        });


        document.addEventListener('drop', function (event) {

            if (
                event.dataTransfer &&
                Array.from(event.dataTransfer.types).includes('Files')
            ) {

                event.preventDefault();
            }
        });


        /*
        |--------------------------------------------------------------------------
        | ADD MORE FILES
        |--------------------------------------------------------------------------
        */

        document.getElementById('addMoreFiles')
            ?.addEventListener('click', function () {

                openPicker();
            });


        /*
        |--------------------------------------------------------------------------
        | CLEAR ALL FILES
        |--------------------------------------------------------------------------
        */

        document.getElementById('clearAllFiles')
            ?.addEventListener('click', function () {

                commitFiles([]);

                clearError();
            });


        /*
        |--------------------------------------------------------------------------
        | FORM SUBMISSION
        |--------------------------------------------------------------------------
        */

        uploadForm.addEventListener('submit', function (event) {

            if (selectedFiles.length === 0) {

                event.preventDefault();

                showError(
                    'Please select at least one Excel Master Sheet.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CHECK LARAVEL FILE INPUT
            |--------------------------------------------------------------------------
            */

            if (
                !payload.files ||
                payload.files.length !== selectedFiles.length
            ) {

                event.preventDefault();

                showError(
                    'The files could not be attached for upload. '
                    + 'Please choose them again.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | SHOW UPLOADING STATE
            |--------------------------------------------------------------------------
            */

            uploadButton.disabled = true;

            uploadSpinner.classList.remove('hidden');

            uploadButtonIcon.classList.add('hidden');

            uploadButtonText.textContent =
                'Uploading Master Sheets...';
        });


        /*
        |--------------------------------------------------------------------------
        | RESET UPLOAD BUTTON WHEN PAGE IS RESTORED
        |--------------------------------------------------------------------------
        */

        window.addEventListener('pageshow', function () {

            uploadButton.disabled = false;

            uploadSpinner.classList.add('hidden');

            uploadButtonIcon.classList.remove('hidden');

            uploadButtonText.textContent =
                'Upload Selected Master Sheets';
        });

    }


    /*
    |--------------------------------------------------------------------------
    | STUDENT CARD GENERATION
    |--------------------------------------------------------------------------
    */

    const generateForm = document.getElementById('generateForm');

    const generateButton = document.getElementById('generateButton');

    const generateButtonText = document.getElementById('generateButtonText');

    const generateOverlay = document.getElementById('generateOverlay');

    const generateFeedback = document.getElementById('generateFeedback');


    /*
    |--------------------------------------------------------------------------
    | SHOW / HIDE GENERATION LOADER
    |--------------------------------------------------------------------------
    */

    function setLoading(active) {

        if (!generateOverlay || !generateButton) {
            return;
        }

        generateOverlay.classList.toggle(
            'hidden',
            !active
        );

        generateOverlay.classList.toggle(
            'flex',
            active
        );

        document.body.classList.toggle(
            'overflow-hidden',
            active
        );

        generateButton.disabled = active;

        if (generateButtonText) {

            generateButtonText.textContent = active
                ? 'Generating Student Cards...'
                : 'Generate & Download Student Cards';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW GENERATION RESULT
    |--------------------------------------------------------------------------
    */

    function showGenerateMessage(message, success) {

        if (!generateFeedback) {
            return;
        }

        generateFeedback.textContent = message;

        generateFeedback.className =
            'mx-6 mb-5 rounded-xl border px-4 py-3 text-sm ' +

            (
                success
                    ? 'border-teal-200 bg-teal-50 text-teal-800'
                    : 'border-red-200 bg-red-50 text-red-700'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE WITH FETCH
    |--------------------------------------------------------------------------
    |
    | The loading overlay remains visible while Laravel
    | generates the Word documents and ZIP file.
    |
    */

    if (generateForm && generateButton && generateOverlay) {

        generateForm.addEventListener('submit', async function (event) {

            event.preventDefault();

            if (generateButton.disabled) {
                return;
            }


            if (generateFeedback) {

                generateFeedback.classList.add('hidden');
            }


            setLoading(true);


            try {

                /*
                |--------------------------------------------------------------------------
                | SEND GENERATION REQUEST
                |--------------------------------------------------------------------------
                */

                const response = await fetch(
                    generateForm.action,
                    {
                        method: 'POST',

                        body: new FormData(generateForm),

                        credentials: 'same-origin',

                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    }
                );


                const contentType = (
                    response.headers.get('content-type') || ''
                ).toLowerCase();


                const disposition =
                    response.headers.get('content-disposition') || '';


                const isZip =
                    contentType.includes('zip') ||

                    (
                        disposition.toLowerCase().includes('attachment') &&
                        disposition.toLowerCase().includes('.zip')
                    );


                /*
                |--------------------------------------------------------------------------
                | HANDLE ERRORS
                |--------------------------------------------------------------------------
                */

                if (!response.ok || !isZip) {

                    let message =
                        'Student Card generation failed. No ZIP was returned.';


                    if (response.status === 419) {

                        message =
                            'Your login session has expired. Refresh the page and try again.';

                    } else if (contentType.includes('json')) {

                        const data = await response.json();

                        message =
                            Object.values(data.errors || {})
                                .flat()
                                .join(' ') ||

                            data.message ||

                            message;

                    } else if (response.redirected) {

                        message =
                            'The server redirected your request. '
                            + 'Please refresh the page and check your login.';

                    } else {

                        message =
                            'Student Card generation failed (HTTP '
                            + response.status
                            + '). Check the Laravel log.';
                    }


                    throw new Error(message);
                }


                /*
                |--------------------------------------------------------------------------
                | RECEIVE ZIP
                |--------------------------------------------------------------------------
                */

                const blob = await response.blob();


                if (!blob.size) {

                    throw new Error(
                        'The generated ZIP file is empty.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | READ DOWNLOAD FILENAME
                |--------------------------------------------------------------------------
                */

                let filename = 'StudentCards.zip';


                const utf8Match = disposition.match(
                    /filename\*=UTF-8''([^;]+)/i
                );


                const normalMatch = disposition.match(
                    /filename="?([^";]+)"?/i
                );


                if (utf8Match) {

                    try {

                        filename = decodeURIComponent(
                            utf8Match[1]
                        );

                    } catch (_) {

                        filename = 'StudentCards.zip';
                    }

                } else if (normalMatch) {

                    filename = normalMatch[1];
                }


                /*
                |--------------------------------------------------------------------------
                | DOWNLOAD ZIP
                |--------------------------------------------------------------------------
                */

                const downloadUrl =
                    URL.createObjectURL(blob);


                const link =
                    document.createElement('a');


                link.href = downloadUrl;

                link.download = filename;

                document.body.appendChild(link);

                link.click();

                link.remove();


                setTimeout(function () {

                    URL.revokeObjectURL(downloadUrl);

                }, 60000);


                /*
                |--------------------------------------------------------------------------
                | SUCCESS
                |--------------------------------------------------------------------------
                */

                showGenerateMessage(
                    'Student Cards ZIP is ready. The download has been initiated.',
                    true
                );


            } catch (error) {

                console.error(
                    'Student Card generation error:',
                    error
                );


                showGenerateMessage(
                    error.message ||
                    'Unable to generate Student Cards.',
                    false
                );


            } finally {

                setLoading(false);
            }

        });
    }

});

</script>

@endsection
