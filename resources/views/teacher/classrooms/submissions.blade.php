
@extends('layouts.teacher')

@section('title', 'Submission of Requirements | SHS Forms')

@section('content')

@php
    $submission = $selected['submission'];
    $setting = $selected['setting'];
    $requirements = $selected['requirements'];
    $fileGroups = $selected['fileGroups'];
    $periodStatus = $selected['status'];
    $canManage = $selected['canManage'];
    $canSubmit = $selected['canSubmit'];
    $correctionIds = $selected['correctionRequirementIds'];

    $latestReturn = $submission?->reviews
        ?->firstWhere('decision', 'returned');

    $periodMessages = [
        'archived' => 'This classroom or school year is inactive.',
        'not_configured' => 'The Principal has not configured this semester.',
        'not_applicable' => 'This submission period does not apply to your grade level.',
        'draft' => 'The Principal has not activated this submission period.',
        'closed' => 'The Principal has closed this submission period.',
        'scheduled' => 'The submission period has not opened yet.',
        'expired' => 'The deadline has passed. Ask the Principal to extend it.',
        'unavailable' => 'The submission period is currently unavailable.',
        'open' => 'The submission period is open.',
    ];

    $statusStyles = [
        'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
        'submitted' => 'bg-blue-50 text-blue-700 border-blue-200',
        'returned' => 'bg-amber-50 text-amber-800 border-amber-200',
        'approved' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
    ];
@endphp


<div class="mx-auto max-w-7xl space-y-7 p-5 md:p-8">

    <!-- ======================================
         PAGE HEADER
    ======================================= -->

    <div class="flex flex-wrap items-start justify-between gap-4">

        <div>

            <p class="text-xs font-bold uppercase tracking-widest text-teal-700">
                Teacher Portal / Classroom
            </p>

            <h1 class="mt-2 text-2xl font-extrabold text-slate-900 md:text-3xl">
                Submission of Requirements
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                {{ $section->name }}
                · Grade {{ $section->grade_level }}
                · {{ $section->schoolYear?->name ?? 'School Year' }}
            </p>

        </div>


        <a href="{{ route('teacher.classrooms.show', $section) }}"
           class="inline-flex items-center gap-2 rounded-xl
                  border border-slate-200 bg-white px-4 py-3
                  text-sm font-bold text-slate-700
                  hover:border-teal-300">

            <i data-lucide="arrow-left"
               class="h-4 w-4"></i>

            Back to Classroom

        </a>

    </div>



    <!-- ======================================
         SUCCESS MESSAGE
    ======================================= -->

    @if(session('success'))

        <div role="status"
             class="rounded-xl border border-emerald-200
                    bg-emerald-50 px-5 py-4 text-sm
                    font-semibold text-emerald-800">

            {{ session('success') }}

        </div>

    @endif



    <!-- ======================================
         VALIDATION ERRORS
    ======================================= -->

    @if($errors->any())

        <div role="alert"
             class="rounded-xl border border-red-200 bg-red-50 p-5">

            <h3 class="font-bold text-red-800">
                Please correct the following:
            </h3>

            <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-red-700">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif



    <!-- ======================================
         SEMESTER SELECTION
    ======================================= -->

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

        @foreach($semesters as $number => $item)

            @php
                $active = $selectedNumber === (int) $number;
                $itemSubmission = $item['submission'];
            @endphp

            <a href="{{ route('teacher.classrooms.submissions', [
                    'section' => $section,
                    'semester' => $number
                ]) }}"
               class="rounded-2xl border-2 p-6 shadow-sm transition
                      {{ $active
                          ? 'border-teal-500 bg-[#142b61] text-white'
                          : 'border-slate-200 bg-white text-slate-900 hover:border-teal-300' }}">

                <div class="flex flex-wrap items-center justify-between gap-3">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-wide
                                  {{ $active ? 'text-blue-200' : 'text-slate-400' }}">

                            Semester {{ str_pad($number, 2, '0', STR_PAD_LEFT) }}

                        </p>

                        <h2 class="mt-2 text-xl font-extrabold">
                            {{ $item['label'] }}
                        </h2>

                    </div>


                    <span class="rounded-full px-3 py-1.5 text-xs font-bold
                                 {{ $active
                                     ? 'bg-white/15 text-white'
                                     : ($statusStyles[$itemSubmission?->status ?? 'draft']
                                         ?? $statusStyles['draft']) }}">

                        {{ ucfirst($itemSubmission?->status ?? $item['status']) }}

                    </span>

                </div>

            </a>

        @endforeach

    </div>



    <!-- ======================================
         SUBMISSION SUMMARY
    ======================================= -->

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">


        <!-- SUBMISSION STATUS -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <p class="text-xs font-semibold text-slate-500">
                Submission Status
            </p>

            <div class="mt-3">

                <span class="inline-flex rounded-full border
                             px-4 py-2 text-xs font-bold
                             {{ $statusStyles[$submission?->status ?? 'draft']
                                 ?? $statusStyles['draft'] }}">

                    {{ ucfirst($submission?->status ?? 'Not submitted') }}

                </span>

            </div>

        </div>


        <!-- COMPLETION -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <p class="text-xs font-semibold text-slate-500">
                Requirements Prepared
            </p>

            <p class="mt-2 text-3xl font-extrabold text-[#142b61]">

                {{ $selected['preparedCount'] }}
                /
                {{ $requirements->count() }}

            </p>

        </div>


        <!-- AVAILABILITY -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <p class="text-xs font-semibold text-slate-500">
                Submission Availability
            </p>

            <p class="mt-2 text-lg font-extrabold
                      {{ $periodStatus === 'open'
                          ? 'text-emerald-700'
                          : 'text-amber-700' }}">

                {{ ucfirst(str_replace('_', ' ', $periodStatus)) }}

            </p>

        </div>

    </div>



    <!-- ======================================
         RETURNED SUBMISSION NOTIFICATION
    ======================================= -->

    @if($submission?->status === 'returned')

        <section class="rounded-2xl border-2 border-amber-300
                        bg-amber-50 p-6 shadow-sm">

            <div class="flex items-start gap-4">

                <div class="rounded-xl bg-amber-100 p-3 text-amber-700">

                    <i data-lucide="message-square-warning"
                       class="h-6 w-6"></i>

                </div>


                <div class="min-w-0 flex-1">

                    <div class="flex flex-wrap items-center gap-3">

                        <h2 class="text-lg font-extrabold text-amber-950">
                            Returned by the Principal
                        </h2>

                        <span class="rounded-full bg-amber-200 px-3 py-1
                                     text-xs font-bold text-amber-900">

                            Needs Correction

                        </span>

                    </div>


                    <p class="mt-3 text-sm leading-6 text-amber-900">

                        Your submitted requirements have been reviewed.
                        The Principal requested corrections to some documents.

                        Read the remarks below, remove the returned files,
                        upload corrected versions, and resubmit.

                    </p>



                    <!-- PRINCIPAL REMARKS -->
                    <div class="mt-5 rounded-xl border border-amber-200 bg-white p-5">

                        <div class="flex items-center gap-2">

                            <i data-lucide="message-square"
                               class="h-4 w-4 text-amber-700"></i>

                            <h3 class="text-xs font-bold uppercase tracking-widest text-amber-800">
                                Principal's Remarks
                            </h3>

                        </div>


                        <p class="mt-3 whitespace-pre-line text-sm
                                  leading-6 text-slate-800">{{ $submission->principal_remarks ?: 'Please correct the returned documents.' }}</p>

                    </div>



                    <!-- FILES THAT REQUIRE CORRECTION -->
                    @if($latestReturn?->file_feedback)

                        <div class="mt-5">

                            <h3 class="text-sm font-extrabold text-amber-900">
                                Documents Requiring Correction
                            </h3>


                            <div class="mt-3 space-y-3">

                                @foreach($latestReturn->file_feedback as $feedback)

                                    <div class="rounded-xl border border-amber-200 bg-white p-4">

                                        <p class="text-sm font-bold text-slate-900">
                                            {{ $feedback['requirement'] ?? 'Requirement' }}
                                        </p>

                                        <p class="mt-1 break-all text-xs text-slate-500">
                                            {{ $feedback['filename'] ?? 'Document' }}
                                        </p>

                                        <p class="mt-3 whitespace-pre-line text-xs leading-5 text-amber-900">{{ $feedback['remarks'] ?? 'Correction requested.' }}</p>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @endif



                    <!-- CLOSED CORRECTION PERIOD -->
                    @if(!$canManage)

                        <div class="mt-5 rounded-xl border border-amber-200 bg-white p-4">

                            <p class="text-xs font-bold text-amber-900">

                                Corrections are currently locked.

                            </p>

                            <p class="mt-2 text-xs leading-5 text-amber-800">

                                {{ $periodMessages[$periodStatus] ?? 'Submission period unavailable.' }}

                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </section>



    <!-- ======================================
         AWAITING REVIEW
    ======================================= -->

    @elseif($submission?->status === 'submitted')

        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-6">

            <div class="flex items-start gap-3">

                <i data-lucide="clock-3"
                   class="h-6 w-6 text-blue-700"></i>

                <div>

                    <h3 class="text-base font-bold text-blue-900">
                        Submitted — Awaiting Principal Review
                    </h3>

                    <p class="mt-2 text-sm text-blue-800">

                        Your submission has been received.
                        Documents are locked until the Principal
                        approves or returns them.

                    </p>

                </div>

            </div>

        </div>



    <!-- ======================================
         APPROVED SUBMISSION
    ======================================= -->

    @elseif($submission?->status === 'approved')

        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">

            <div class="flex items-start gap-3">

                <i data-lucide="check-circle-2"
                   class="h-6 w-6 text-emerald-700"></i>

                <div>

                    <h3 class="text-base font-bold text-emerald-900">
                        Submission Approved
                    </h3>

                    <p class="mt-2 text-sm text-emerald-800">

                        All submitted requirements have been
                        accepted by the Principal.

                    </p>

                    @if($submission->principal_remarks)

                        <p class="mt-3 text-sm text-emerald-800">
                            Remarks: {{ $submission->principal_remarks }}
                        </p>

                    @endif

                </div>

            </div>

        </div>

    @endif



    <!-- ======================================
         SUBMISSION PERIOD INFORMATION
    ======================================= -->

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-extrabold text-slate-900">
            {{ $selected['label'] }} Submission Period
        </h2>

        <p class="mt-2 text-sm text-slate-500">
            {{ $periodMessages[$periodStatus] ?? 'Unavailable' }}
        </p>


        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">

            <div class="rounded-xl bg-slate-50 p-4">

                <p class="text-xs font-semibold text-slate-500">
                    Opening Date
                </p>

                <p class="mt-2 text-sm font-bold text-slate-900">

                    {{ $setting?->opens_at
                        ? $setting->opens_at->copy()->timezone('Asia/Manila')->format('M d, Y h:i A')
                        : 'Not Set' }}

                </p>

            </div>


            <div class="rounded-xl bg-slate-50 p-4">

                <p class="text-xs font-semibold text-slate-500">
                    Submission Deadline
                </p>

                <p class="mt-2 text-sm font-bold text-slate-900">

                    {{ $setting?->deadline_at
                        ? $setting->deadline_at->copy()->timezone('Asia/Manila')->format('M d, Y h:i A')
                        : 'Not Set' }}

                </p>

            </div>

        </div>


        @if($setting?->instructions)

            <div class="mt-5 rounded-xl border border-slate-100 bg-slate-50 p-4">

                <p class="text-xs font-bold text-slate-700">
                    Principal Instructions
                </p>

                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $setting->instructions }}</p>

            </div>

        @endif

    </section>



    <!-- ======================================
         REQUIRED DOCUMENTS
    ======================================= -->

    <section class="space-y-4">

        <div>

            <h2 class="text-xl font-extrabold text-slate-900">
                Required Documents
            </h2>

            <p class="mt-2 text-sm text-slate-500">

                Approved documents remain locked.
                Returned documents can be replaced during
                the active submission period.

            </p>

        </div>


        @forelse($requirements as $requirement)

            @php
                $files = $fileGroups->get(
                    $requirement->id,
                    collect()
                );

                $needsCorrection =
                    in_array(
                        (int) $requirement->id,
                        $correctionIds,
                        true
                    )
                    ||
                    $files->contains(
                        'review_status',
                        'returned'
                    );

                $hasApproved = $files->contains(
                    'review_status',
                    'approved'
                );

                $showUploader = $canManage
                    &&
                    (
                        $submission?->status !== 'returned'
                        ||
                        $needsCorrection
                    )
                    &&
                    (
                        !$hasApproved
                        ||
                        $needsCorrection
                    );
            @endphp


            <article class="rounded-2xl border
                            {{ $needsCorrection
                                ? 'border-amber-300'
                                : 'border-slate-200' }}
                            bg-white p-6 shadow-sm">


                <!-- REQUIREMENT HEADER -->
                <div class="flex flex-wrap items-start justify-between gap-3">

                    <div>

                        <h3 class="text-lg font-extrabold text-slate-900">

                            {{ $loop->iteration }}.
                            {{ $requirement->name }}

                        </h3>

                        @if($requirement->description)

                            <p class="mt-2 text-sm text-slate-500">
                                {{ $requirement->description }}
                            </p>

                        @endif

                    </div>


                    <!-- REQUIREMENT STATUS -->
                    @if($needsCorrection)

                        <span class="rounded-full bg-amber-50 px-3 py-1.5
                                     text-xs font-bold text-amber-800">

                            Needs Correction

                        </span>

                    @elseif($hasApproved)

                        <span class="rounded-full bg-emerald-50 px-3 py-1.5
                                     text-xs font-bold text-emerald-700">

                            Approved

                        </span>

                    @elseif($files->isNotEmpty())

                        <span class="rounded-full bg-blue-50 px-3 py-1.5
                                     text-xs font-bold text-blue-700">

                            Uploaded

                        </span>

                    @else

                        <span class="rounded-full bg-slate-100 px-3 py-1.5
                                     text-xs font-bold text-slate-600">

                            Missing

                        </span>

                    @endif

                </div>



                <!-- ==================================
                     UPLOADED FILES
                =================================== -->

                <div class="mt-5 space-y-3">

                    @forelse($files as $file)

                        <div class="rounded-xl border p-4
                                    {{ $file->review_status === 'returned'
                                        ? 'border-amber-200 bg-amber-50'
                                        : 'border-slate-200 bg-slate-50' }}">


                            <div class="flex flex-wrap items-start justify-between gap-4">

                                <!-- FILE INFORMATION -->
                                <div class="min-w-0 flex-1">

                                    <p class="break-all text-sm font-bold text-slate-800">
                                        {{ $file->original_name }}
                                    </p>

                                    <p class="mt-2 text-xs text-slate-500">

                                        {{ number_format(($file->size_bytes ?? 0) / 1024, 1) }}
                                        KB

                                        ·

                                        {{ $file->created_at
                                            ? $file->created_at->copy()->timezone('Asia/Manila')->format('M d, Y h:i A')
                                            : '' }}

                                    </p>


                                    @if($file->review_status === 'approved')

                                        <span class="mt-3 inline-flex rounded-full
                                                     bg-emerald-100 px-3 py-1.5
                                                     text-xs font-bold text-emerald-800">

                                            Approved

                                        </span>

                                    @elseif($file->review_status === 'returned')

                                        <span class="mt-3 inline-flex rounded-full
                                                     bg-amber-100 px-3 py-1.5
                                                     text-xs font-bold text-amber-900">

                                            Returned

                                        </span>

                                    @else

                                        <span class="mt-3 inline-flex rounded-full
                                                     bg-blue-100 px-3 py-1.5
                                                     text-xs font-bold text-blue-800">

                                            Pending

                                        </span>

                                    @endif

                                </div>



                                <!-- FILE ACTIONS -->
                                <div class="flex flex-wrap items-center gap-2">


                                    <!-- DOWNLOAD -->
                                    <a href="{{ route('teacher.classrooms.submissions.files.download', [
                                            'section' => $section,
                                            'submissionFile' => $file
                                        ]) }}"
                                       class="inline-flex items-center gap-2
                                              rounded-lg border border-slate-200
                                              bg-white px-3 py-2 text-xs
                                              font-bold text-slate-700
                                              hover:bg-slate-100">

                                        <i data-lucide="download"
                                           class="h-4 w-4"></i>

                                        Download

                                    </a>



                                    <!-- REMOVE FILE -->
                                    @if($canManage && $file->review_status !== 'approved'
                                        && ($submission?->status !== 'returned' || $needsCorrection))

                                        <form method="POST"
                                              action="{{ route('teacher.classrooms.submissions.files.destroy', [
                                                  'section' => $section,
                                                  'submissionFile' => $file
                                              ]) }}"
                                              onsubmit="return confirm('Remove this document?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="inline-flex items-center gap-2
                                                           rounded-lg border border-red-200
                                                           bg-red-50 px-3 py-2
                                                           text-xs font-bold text-red-700
                                                           hover:bg-red-100">

                                                <i data-lucide="trash-2"
                                                   class="h-4 w-4"></i>

                                                Remove

                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </div>



                            <!-- FILE RETURN REMARKS -->
                            @if($file->review_status === 'returned')

                                <div class="mt-4 rounded-xl border border-amber-200 bg-white p-4">

                                    <p class="text-xs font-bold text-amber-900">
                                        Principal's File Remarks
                                    </p>

                                    <p class="mt-2 whitespace-pre-line
                                              text-sm leading-6 text-amber-900">{{ $file->review_remarks ?: 'Please correct and replace this file.' }}</p>

                                </div>

                            @endif



                            <!-- APPROVED FILE NOTE -->
                            @if($file->review_status === 'approved')

                                <p class="mt-3 text-xs font-semibold text-emerald-700">

                                    This document has been accepted by the Principal.
                                    No correction is needed.

                                </p>

                            @endif

                        </div>


                    @empty

                        <div class="rounded-xl border border-dashed
                                    border-slate-200 bg-slate-50 p-5">

                            <p class="text-sm text-slate-500">
                                No document uploaded for this requirement yet.
                            </p>

                        </div>

                    @endforelse

                </div>



                <!-- ==================================
                     UPLOAD NEW / CORRECTED FILE
                =================================== -->

                @if($showUploader && $selected['semester'])

                    <form method="POST"
                          enctype="multipart/form-data"
                          action="{{ route('teacher.classrooms.submissions.files.store', [
                              'section' => $section,
                              'semester' => $selected['semester'],
                              'requirement' => $requirement
                          ]) }}"
                          class="mt-5 rounded-xl border border-teal-200
                                 bg-teal-50 p-5">

                        @csrf


                        <label for="files-{{ $requirement->id }}"
                               class="block text-sm font-bold text-slate-800">

                            {{ $needsCorrection
                                ? 'Upload Corrected Document'
                                : 'Upload Documents' }}

                        </label>


                        <p class="mt-2 text-xs text-slate-500">

                            Accepted: PDF, Word, Excel, CSV and images.

                            Maximum 10 MB per file, 10 files per requirement.

                        </p>


                        <input id="files-{{ $requirement->id }}"
                               type="file"
                               name="files[]"
                               multiple
                               required
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.png,.jpg,.jpeg"
                               class="mt-4 block w-full rounded-xl
                                      border border-slate-200 bg-white
                                      p-3 text-sm text-slate-700">


                        <button type="submit"
                                class="mt-4 inline-flex cursor-pointer
                                       items-center gap-2 rounded-xl
                                       bg-[#0d9488] px-5 py-3
                                       text-sm font-bold text-white
                                       hover:bg-[#0f766e]">

                            <i data-lucide="upload"
                               class="h-4 w-4"></i>

                            {{ $needsCorrection
                                ? 'Upload Corrected File'
                                : 'Upload Selected Files' }}

                        </button>

                    </form>


                @elseif($hasApproved && $canManage && !$needsCorrection)

                    <p class="mt-4 text-xs font-semibold text-emerald-700">

                        This requirement is already approved.
                        No new upload is needed.

                    </p>

                @endif

            </article>


        @empty

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6">

                <h3 class="font-bold text-amber-900">
                    No Requirements Configured
                </h3>

                <p class="mt-2 text-sm text-amber-800">

                    The Principal has not configured any requirements
                    for this semester.

                </p>

            </div>

        @endforelse

    </section>



    <!-- ======================================
         FINAL SUBMISSION / RESUBMISSION
    ======================================= -->

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="flex flex-wrap items-start justify-between gap-5">

            <div>

                <h2 class="text-lg font-extrabold text-slate-900">

                    {{ $submission?->status === 'returned'
                        ? 'Resubmit Corrected Requirements'
                        : 'Final Submission' }}

                </h2>


                <p class="mt-2 max-w-xl text-sm leading-6 text-slate-500">

                    All required documents must be available.
                    Returned files must be removed and replaced
                    before the Principal can review your corrections.

                </p>

            </div>



            <!-- SUBMISSION BUTTON -->
            @if($canManage && $selected['semester'])

                <form method="POST"
                      action="{{ route('teacher.classrooms.submissions.submit', [
                          'section' => $section,
                          'semester' => $selected['semester']
                      ]) }}"
                      onsubmit="return confirm('Submit these requirements for Principal review?')">

                    @csrf


                    <button type="submit"
                            @disabled(!$canSubmit)
                            class="inline-flex items-center gap-2
                                   rounded-xl px-6 py-3.5
                                   text-sm font-bold text-white
                                   {{ $canSubmit
                                       ? 'cursor-pointer bg-[#142b61] hover:bg-blue-900'
                                       : 'cursor-not-allowed bg-slate-400' }}">

                        <i data-lucide="send"
                           class="h-4 w-4"></i>

                        {{ $submission?->status === 'returned'
                            ? 'Resubmit Corrections'
                            : 'Submit Requirements' }}

                    </button>

                </form>

            @else

                <span class="rounded-xl border border-slate-200
                             bg-slate-50 px-5 py-3
                             text-xs font-bold text-slate-500">

                    @if($submission?->status === 'submitted')

                        Awaiting Principal Review

                    @elseif($submission?->status === 'approved')

                        Already Approved

                    @else

                        Submission Unavailable

                    @endif

                </span>

            @endif

        </div>



        <!-- DISABLED SUBMISSION EXPLANATION -->
        @if($canManage && !$canSubmit)

            <div class="mt-5 rounded-xl border border-amber-200
                        bg-amber-50 p-4">

                <p class="text-xs font-semibold leading-5 text-amber-900">

                    Submission is disabled until every requirement
                    contains a valid document.

                    @if($submission?->status === 'returned')

                        Remove the returned documents and upload
                        corrected replacements.

                    @endif

                </p>

            </div>

        @endif

    </section>



    <!-- ======================================
         PRINCIPAL REVIEW HISTORY
    ======================================= -->

    @if($submission && $submission->reviews->isNotEmpty())

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <h2 class="text-lg font-extrabold text-slate-900">
                Principal Review History
            </h2>


            <div class="mt-5 divide-y divide-slate-100">

                @foreach($submission->reviews as $review)

                    <div class="py-5">

                        <div class="flex flex-wrap items-center
                                    justify-between gap-3">

                            <span class="text-sm font-bold
                                         {{ $review->decision === 'returned'
                                             ? 'text-amber-800'
                                             : 'text-emerald-800' }}">

                                {{ ucfirst($review->decision) }}

                            </span>


                            <span class="text-xs text-slate-500">

                                {{ $review->created_at
                                    ? $review->created_at->copy()->timezone('Asia/Manila')->format('M d, Y h:i A')
                                    : '' }}

                            </span>

                        </div>


                        <p class="mt-2 text-xs text-slate-500">

                            Reviewed by
                            {{ $review->reviewer?->name ?? 'Principal' }}

                        </p>


                        @if($review->remarks)

                            <p class="mt-3 whitespace-pre-line
                                      text-sm leading-6 text-slate-700">{{ $review->remarks }}</p>

                        @endif

                    </div>

                @endforeach

            </div>

        </section>

    @endif

</div>

@endsection
