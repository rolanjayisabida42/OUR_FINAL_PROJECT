
@extends('layouts.principal-review')

@section('title', 'Submission Details | SHS Forms')
@section('heading', 'Submission Review')


@section('content')

@php
    // ==========================================
    // SUBMISSION STATUS
    // ==========================================

    $isReviewable = $submission->status === 'submitted';

    $statusClass = match ($submission->status) {
        'submitted' => 'bg-blue-50 text-blue-700 border-blue-200',
        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'returned' => 'bg-amber-50 text-amber-700 border-amber-200',
        default => 'bg-slate-100 text-slate-600 border-slate-200',
    };

    $allFiles = $submission->files
        ->sortBy('submission_requirement_id')
        ->values();

    $approvedCount = $allFiles
        ->where('review_status', 'approved')
        ->count();

    $returnedCount = $allFiles
        ->where('review_status', 'returned')
        ->count();

    $pendingCount = $allFiles
        ->where('review_status', 'pending')
        ->count();
@endphp


<div class="space-y-7">


        @if($submission->status === 'submitted')

            <a href="{{ route('principal.submissions.review.return-page', $submission) }}"
            class="inline-flex items-center
                    justify-center gap-2
                    rounded-xl bg-amber-600
                    px-5 py-3 text-sm
                    font-bold text-white
                    transition hover:bg-amber-700">

                <i data-lucide="rotate-ccw"
                class="h-4 w-4"></i>

                Return for Correction

            </a>

        @endif


    <!-- ======================================
         BACK NAVIGATION
    ======================================= -->

    <a href="{{ route('principal.submissions.review.index') }}"
       class="inline-flex items-center gap-2
              text-sm font-semibold text-slate-500
              hover:text-teal-700">

        <i data-lucide="arrow-left"
           class="h-4 w-4"></i>

        Back to Review Submissions

    </a>



    <!-- ======================================
         PAGE HEADING
    ======================================= -->

    <div class="flex flex-wrap items-start justify-between gap-4">

        <div>

            <p class="text-xs font-bold uppercase
                      tracking-widest text-teal-700">

                Classroom Submission #{{ $submission->id }}

            </p>

            <h1 class="mt-2 text-2xl font-extrabold
                       text-slate-900 md:text-3xl">

                {{ $submission->section?->name ?? 'Classroom' }}

            </h1>

            <p class="mt-2 text-sm leading-6 text-slate-500">

                Review uploaded school forms directly,
                approve completed documents, or return
                incorrect files with remarks.

            </p>

        </div>


        <span class="inline-flex items-center gap-2
                     rounded-full border px-4 py-2
                     text-xs font-bold {{ $statusClass }}">

            {{ ucfirst($submission->status) }}

        </span>

    </div>



    <!-- ======================================
         CLASSROOM INFORMATION
    ======================================= -->

    <section class="rounded-2xl bg-[#142b61]
                    p-6 text-white shadow-sm md:p-8">

        <div class="grid grid-cols-1 gap-6
                    sm:grid-cols-2 xl:grid-cols-4">


            <!-- TEACHER -->
            <div>

                <div class="flex items-center gap-2
                            text-xs text-blue-200">

                    <i data-lucide="user-round"
                       class="h-4 w-4"></i>

                    Teacher / Adviser

                </div>

                <p class="mt-3 text-sm font-bold">

                    {{ $submission->teacher?->name ?? 'Unknown Teacher' }}

                </p>

            </div>



            <!-- GRADE AND STRAND -->
            <div>

                <div class="flex items-center gap-2
                            text-xs text-blue-200">

                    <i data-lucide="graduation-cap"
                       class="h-4 w-4"></i>

                    Grade / Strand

                </div>

                <p class="mt-3 text-sm font-bold">

                    Grade {{ $submission->section?->grade_level ?? 'N/A' }}

                    /

                    {{ $submission->section?->strand?->code
                        ?? $submission->section?->strand?->name
                        ?? 'N/A' }}

                </p>

            </div>



            <!-- SCHOOL YEAR -->
            <div>

                <div class="flex items-center gap-2
                            text-xs text-blue-200">

                    <i data-lucide="calendar-days"
                       class="h-4 w-4"></i>

                    School Year

                </div>

                <p class="mt-3 text-sm font-bold">

                    {{ $submission->section?->schoolYear?->name ?? 'N/A' }}

                </p>

            </div>



            <!-- SEMESTER -->
            <div>

                <div class="flex items-center gap-2
                            text-xs text-blue-200">

                    <i data-lucide="book-open"
                       class="h-4 w-4"></i>

                    Semester

                </div>

                <p class="mt-3 text-sm font-bold">

                    @if($submission->semester?->semester_number == 1)

                        1st Semester

                    @elseif($submission->semester?->semester_number == 2)

                        2nd Semester

                    @else

                        N/A

                    @endif

                </p>

            </div>

        </div>

    </section>



    <!-- ======================================
         SUBMISSION SUMMARY
    ======================================= -->

    <section class="grid grid-cols-1 gap-4
                    sm:grid-cols-2 xl:grid-cols-4">


        <!-- SUBMISSION DATE -->
        <div class="rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm">

            <p class="text-xs font-semibold text-slate-500">
                Submitted On
            </p>

            <p class="mt-3 text-sm font-extrabold
                      text-[#142b61]">

                {{ $submission->submitted_at
                    ? $submission->submitted_at->copy()
                        ->timezone('Asia/Manila')
                        ->format('M d, Y h:i A')
                    : 'Not yet submitted' }}

            </p>

        </div>



        <!-- REQUIREMENT COUNT -->
        <div class="rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm">

            <p class="text-xs font-semibold text-slate-500">
                Required Documents
            </p>

            <p class="mt-3 text-3xl font-extrabold
                      text-[#142b61]">

                {{ $setting?->requirements?->count() ?? 0 }}

            </p>

        </div>



        <!-- UPLOADED FILES -->
        <div class="rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm">

            <p class="text-xs font-semibold text-slate-500">
                Uploaded Files
            </p>

            <p class="mt-3 text-3xl font-extrabold
                      text-[#142b61]">

                {{ $allFiles->count() }}

            </p>

        </div>



        <!-- PENDING DOCUMENTS -->
        <div class="rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm">

            <p class="text-xs font-semibold text-slate-500">
                Pending File Reviews
            </p>

            <p class="mt-3 text-3xl font-extrabold
                      text-blue-700">

                {{ $pendingCount }}

            </p>

        </div>

    </section>



    <!-- ======================================
         FILE REVIEW SUMMARY
    ======================================= -->

    <section class="rounded-2xl border border-slate-200
                    bg-white p-6 shadow-sm">

        <div class="flex flex-wrap items-center
                    justify-between gap-4">

            <div>

                <h2 class="text-base font-extrabold text-slate-900">
                    Document Review Summary
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Current review status of uploaded files.
                </p>

            </div>

        </div>


        <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">


            <!-- APPROVED -->
            <div class="flex items-center gap-3
                        rounded-xl bg-emerald-50 p-4">

                <i data-lucide="check-circle-2"
                   class="h-5 w-5 text-emerald-700"></i>

                <div>

                    <p class="text-xs font-semibold text-emerald-700">
                        Approved
                    </p>

                    <p class="text-xl font-extrabold text-emerald-900">
                        {{ $approvedCount }}
                    </p>

                </div>

            </div>



            <!-- RETURNED -->
            <div class="flex items-center gap-3
                        rounded-xl bg-amber-50 p-4">

                <i data-lucide="rotate-ccw"
                   class="h-5 w-5 text-amber-700"></i>

                <div>

                    <p class="text-xs font-semibold text-amber-700">
                        Returned
                    </p>

                    <p class="text-xl font-extrabold text-amber-900">
                        {{ $returnedCount }}
                    </p>

                </div>

            </div>



            <!-- PENDING -->
            <div class="flex items-center gap-3
                        rounded-xl bg-blue-50 p-4">

                <i data-lucide="clock-3"
                   class="h-5 w-5 text-blue-700"></i>

                <div>

                    <p class="text-xs font-semibold text-blue-700">
                        Pending
                    </p>

                    <p class="text-xl font-extrabold text-blue-900">
                        {{ $pendingCount }}
                    </p>

                </div>

            </div>

        </div>

    </section>



    <!-- ======================================
         PRINCIPAL REMARKS
    ======================================= -->

    @if($submission->principal_remarks)

        <section class="rounded-2xl border border-amber-200
                        bg-amber-50 p-6">

            <div class="flex items-center gap-2">

                <i data-lucide="message-square"
                   class="h-5 w-5 text-amber-700"></i>

                <h2 class="text-sm font-extrabold text-amber-900">
                    Latest Principal Remarks
                </h2>

            </div>

            <p class="mt-3 whitespace-pre-line
                      text-sm leading-6 text-amber-800">{{ $submission->principal_remarks }}</p>

        </section>

    @endif



    <!-- ======================================
         UPLOADED DOCUMENTS
    ======================================= -->

    <section class="overflow-hidden rounded-2xl
                    border border-slate-200
                    bg-white shadow-sm">


        <!-- SECTION HEADER -->
        <div class="border-b border-slate-100 p-6">

            <div class="flex flex-wrap items-start
                        justify-between gap-4">

                <div>

                    <h2 class="text-lg font-extrabold text-slate-900">
                        Uploaded Documents
                    </h2>

                    <p class="mt-2 text-xs leading-5 text-slate-500">

                        Preview Excel documents in your browser
                        or download the original files.

                    </p>

                </div>


                <span class="rounded-full bg-slate-100
                             px-4 py-2 text-xs font-bold
                             text-slate-600">

                    {{ $allFiles->count() }} Files

                </span>

            </div>


            @if($isReviewable)

                <div class="mt-5 rounded-xl border border-blue-200
                            bg-blue-50 p-4">

                    <div class="flex items-start gap-3">

                        <i data-lucide="info"
                           class="h-5 w-5 shrink-0 text-blue-700"></i>

                        <p class="text-xs leading-6 text-blue-800">

                            <strong>How to review:</strong>

                            Open each uploaded document and check
                            its contents.

                            If a document is incorrect, select
                            <strong>Return this file for correction</strong>
                            and enter specific remarks.

                            Documents not selected for return
                            will be marked as approved when you
                            return the submission.

                        </p>

                    </div>

                </div>

            @endif

        </div>



        <!-- ==================================
             DOCUMENT LIST
        =================================== -->

        <div class="divide-y divide-slate-100">

            @forelse($allFiles as $index => $file)

                @php
                    // Check whether this file is Excel.
                    $fileExtension = strtolower(
                        pathinfo(
                            (string) $file->original_name,
                            PATHINFO_EXTENSION
                        )
                    );

                    $isExcel = in_array(
                        $fileExtension,
                        ['xlsx', 'xls'],
                        true
                    );

                    $fileBadge = match($file->review_status) {
                        'approved' => 'bg-emerald-50 text-emerald-700',
                        'returned' => 'bg-amber-50 text-amber-800',
                        default => 'bg-blue-50 text-blue-700',
                    };

                    $previouslySelected = collect(
                        old('returned_files', [])
                    )->map(
                        fn ($id) => (string) $id
                    )->contains(
                        (string) $file->id
                    );
                @endphp


                <article class="p-6">


                    <div class="flex flex-col gap-4
                                sm:flex-row sm:items-start">


                        <!-- FILE NUMBER -->
                        <div class="flex h-11 w-11 shrink-0
                                    items-center justify-center
                                    rounded-xl
                                    {{ $isExcel
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'bg-slate-100 text-slate-600' }}">

                            <i data-lucide="{{ $isExcel
                                ? 'file-spreadsheet'
                                : 'file-text' }}"
                               class="h-6 w-6"></i>

                        </div>



                        <!-- DOCUMENT DETAILS -->
                        <div class="min-w-0 flex-1">


                            <div class="flex flex-wrap
                                        items-start justify-between
                                        gap-4">


                                <div class="min-w-0">

                                    <!-- REQUIREMENT NAME -->
                                    <p class="text-xs font-bold
                                              uppercase tracking-wide
                                              text-teal-700">

                                        Requirement {{ $index + 1 }}
                                        ·
                                        {{ $file->requirement?->name ?? 'Requirement' }}

                                    </p>



                                    <!-- CLICKABLE FILE NAME -->
                                    @if($isExcel)

                                        <a href="{{ route(
                                                'principal.submissions.review.files.preview',
                                                [
                                                    'submission' => $submission,
                                                    'file' => $file
                                                ]
                                            ) }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           title="Preview Excel in new tab"
                                           class="mt-2 inline-block
                                                  break-all text-sm
                                                  font-extrabold
                                                  text-teal-700
                                                  hover:underline">

                                            {{ $file->original_name }}

                                            <i data-lucide="external-link"
                                               class="ml-1 inline-block
                                                      h-3 w-3"></i>

                                        </a>

                                    @else

                                        <h3 class="mt-2 break-all
                                                   text-sm font-extrabold
                                                   text-slate-900">

                                            {{ $file->original_name }}

                                        </h3>

                                    @endif



                                    <!-- FILE INFORMATION -->
                                    <p class="mt-2 text-xs text-slate-500">

                                        {{ number_format(
                                            ($file->size_bytes ?? 0) / 1024,
                                            1
                                        ) }}
                                        KB

                                        <span class="mx-1">•</span>

                                        {{ $file->created_at
                                            ? $file->created_at->copy()
                                                ->timezone('Asia/Manila')
                                                ->format('M d, Y h:i A')
                                            : 'Date unavailable' }}

                                    </p>

                                </div>



                                <!-- FILE REVIEW STATUS -->
                                <span class="rounded-full px-3 py-1.5
                                             text-xs font-bold
                                             {{ $fileBadge }}">

                                    {{ ucfirst(
                                        $file->review_status ?? 'pending'
                                    ) }}

                                </span>

                            </div>



                            <!-- ==================================
                                 FILE ACTION BUTTONS
                            =================================== -->

                            <div class="mt-5 flex flex-wrap gap-3">


                                <!-- PREVIEW EXCEL -->
                                @if($isExcel)

                                    <a href="{{ route(
                                            'principal.submissions.review.files.preview',
                                            [
                                                'submission' => $submission,
                                                'file' => $file
                                            ]
                                        ) }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center
                                              gap-2 rounded-xl
                                              bg-[#0d9488]
                                              px-5 py-3
                                              text-xs font-bold
                                              text-white transition
                                              hover:bg-[#0f766e]">

                                        <i data-lucide="eye"
                                           class="h-4 w-4"></i>

                                        Preview Excel

                                        <i data-lucide="external-link"
                                           class="h-3.5 w-3.5"></i>

                                    </a>

                                @endif



                                <!-- DOWNLOAD ORIGINAL FILE -->
                                <a href="{{ route(
                                        'principal.submissions.review.files.download',
                                        [
                                            'submission' => $submission,
                                            'file' => $file
                                        ]
                                    ) }}"
                                   class="inline-flex items-center
                                          gap-2 rounded-xl
                                          border border-slate-200
                                          bg-white px-5 py-3
                                          text-xs font-bold
                                          text-slate-700 transition
                                          hover:bg-slate-50">

                                    <i data-lucide="download"
                                       class="h-4 w-4"></i>

                                    Download Original

                                </a>

                            </div>



                            <!-- ==================================
                                 PREVIOUS FILE REMARKS
                            =================================== -->

                            @if($file->review_remarks)

                                <div class="mt-4 rounded-xl
                                            border border-amber-200
                                            bg-amber-50 p-4">

                                    <div class="flex items-center gap-2">

                                        <i data-lucide="message-square-warning"
                                           class="h-4 w-4 text-amber-700"></i>

                                        <p class="text-xs font-bold
                                                  text-amber-900">

                                            File Review Remarks

                                        </p>

                                    </div>

                                    <p class="mt-2 whitespace-pre-line
                                              text-xs leading-5
                                              text-amber-800">{{ $file->review_remarks }}</p>

                                </div>

                            @endif



                            <!-- ==================================
                                 RETURN FILE FOR CORRECTION
                            =================================== -->

                            @if(
                                $isReviewable
                                &&
                                $file->review_status !== 'approved'
                            )

                                <div class="mt-5 rounded-xl
                                            border border-amber-200
                                            bg-amber-50/60 p-5">


                                    <!-- CHECKBOX -->
                                    <label class="flex cursor-pointer
                                                  items-start gap-3
                                                  text-sm font-bold
                                                  text-amber-900">

                                        <input type="checkbox"
                                               form="returnSubmissionForm"
                                               name="returned_files[]"
                                               value="{{ $file->id }}"
                                               @checked($previouslySelected)
                                               class="mt-1 h-4 w-4
                                                      cursor-pointer
                                                      accent-amber-600
                                                      return-file-checkbox">

                                        <span>

                                            Return this file for correction

                                            <span class="mt-1 block
                                                         text-xs font-normal
                                                         leading-5
                                                         text-amber-700">

                                                Select only if this file
                                                contains incorrect or
                                                incomplete information.

                                            </span>

                                        </span>

                                    </label>



                                    <!-- SPECIFIC REMARKS -->
                                    <div class="mt-4">

                                        <label for="file-remarks-{{ $file->id }}"
                                               class="block text-xs
                                                      font-bold text-amber-800">

                                            Specific Remarks (Optional)

                                        </label>


                                        <textarea
                                            id="file-remarks-{{ $file->id }}"
                                            form="returnSubmissionForm"
                                            name="file_remarks[{{ $file->id }}]"
                                            rows="3"
                                            maxlength="1000"
                                            placeholder="Example: Please correct the student grades and upload the updated class record."
                                            class="mt-2 w-full rounded-xl
                                                   border border-amber-200
                                                   bg-white px-4 py-3
                                                   text-sm leading-6
                                                   outline-none
                                                   focus:border-amber-500">{{ old('file_remarks.' . $file->id) }}</textarea>


                                        <p class="mt-2 text-xs text-amber-700">

                                            These remarks will be shown
                                            to the Teacher.

                                        </p>

                                    </div>

                                </div>



                            @elseif(
                                $isReviewable
                                &&
                                $file->review_status === 'approved'
                            )

                                <div class="mt-4 flex items-center gap-2
                                            text-xs font-semibold
                                            text-emerald-700">

                                    <i data-lucide="check-circle-2"
                                       class="h-4 w-4"></i>

                                    Previously approved.
                                    No correction needed.

                                </div>

                            @endif


                        </div>

                    </div>

                </article>


            @empty


                <!-- NO DOCUMENTS -->
                <div class="px-6 py-16 text-center">

                    <div class="mx-auto flex h-14 w-14
                                items-center justify-center
                                rounded-2xl bg-slate-100">

                        <i data-lucide="file-x-2"
                           class="h-7 w-7 text-slate-400"></i>

                    </div>

                    <h3 class="mt-4 text-base font-bold
                               text-slate-700">

                        No Documents Uploaded

                    </h3>

                    <p class="mt-2 text-sm text-slate-500">

                        This classroom currently has no
                        uploaded requirement files.

                    </p>

                </div>


            @endforelse

        </div>

    </section>



    <!-- ======================================
         PRINCIPAL REVIEW ACTIONS
    ======================================= -->

    @if($isReviewable)


        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">


            <!-- ==================================
                 APPROVE SUBMISSION
            =================================== -->

            <section class="rounded-2xl border
                            border-emerald-200
                            bg-white p-6 shadow-sm">


                <!-- HEADER -->
                <div class="flex items-center gap-3">

                    <div class="rounded-xl
                                bg-emerald-50 p-3
                                text-emerald-700">

                        <i data-lucide="check-circle-2"
                           class="h-6 w-6"></i>

                    </div>

                    <div>

                        <h2 class="text-lg font-extrabold text-slate-900">
                            Approve Submission
                        </h2>

                        <p class="mt-1 text-xs leading-5
                                  text-slate-500">

                            Accept all documents and mark this
                            classroom submission as complete.

                        </p>

                    </div>

                </div>



                <!-- APPROVE FORM -->
                <form method="POST"
                      action="{{ route(
                          'principal.submissions.review.approve',
                          $submission
                      ) }}"
                      class="mt-6 space-y-5"
                      onsubmit="return confirm('Approve this complete submission?')">

                    @csrf


                    <div>

                        <label for="approvalRemarks"
                               class="block text-xs font-bold
                                      text-slate-700">

                            Approval Remarks (Optional)

                        </label>


                        <textarea id="approvalRemarks"
                                  name="remarks"
                                  rows="5"
                                  maxlength="2000"
                                  placeholder="Example: All documents have been reviewed and approved."
                                  class="mt-2 w-full rounded-xl
                                         border border-slate-200
                                         bg-white p-4
                                         text-sm leading-6
                                         outline-none
                                         focus:border-teal-500">{{ old('approval_remarks') }}</textarea>

                    </div>



                    <button type="submit"
                            @disabled($allFiles->isEmpty())
                            class="flex w-full items-center
                                   justify-center gap-2
                                   rounded-xl bg-emerald-600
                                   px-6 py-3.5
                                   text-sm font-bold
                                   text-white
                                   hover:bg-emerald-700
                                   disabled:cursor-not-allowed
                                   disabled:opacity-50">

                        <i data-lucide="check-circle-2"
                           class="h-5 w-5"></i>

                        Approve Submission

                    </button>


                    <p class="text-xs leading-5 text-slate-500">

                        Approved submissions become read-only
                        for the Teacher.

                    </p>

                </form>

            </section>



            <!-- ==================================
                 RETURN FOR CORRECTION
            =================================== -->

            <section class="rounded-2xl border
                            border-amber-200
                            bg-white p-6 shadow-sm">


                <!-- HEADER -->
                <div class="flex items-center gap-3">

                    <div class="rounded-xl
                                bg-amber-50 p-3
                                text-amber-700">

                        <i data-lucide="rotate-ccw"
                           class="h-6 w-6"></i>

                    </div>

                    <div>

                        <h2 class="text-lg font-extrabold text-slate-900">
                            Return for Correction
                        </h2>

                        <p class="mt-1 text-xs leading-5
                                  text-slate-500">

                            Return only the documents that
                            require corrections.

                        </p>

                    </div>

                </div>



                <!-- RETURN FORM -->
                <form id="returnSubmissionForm"
                      method="POST"
                      action="{{ route(
                          'principal.submissions.review.return',
                          $submission
                      ) }}"
                      class="mt-6 space-y-5">

                    @csrf


                    <div>

                        <label for="returnRemarks"
                               class="block text-xs font-bold
                                      text-slate-700">

                            Overall Remarks

                            <span class="text-red-600">*</span>

                        </label>


                        <textarea id="returnRemarks"
                                  name="remarks"
                                  rows="5"
                                  required
                                  minlength="5"
                                  maxlength="2000"
                                  placeholder="Explain which requirements need corrections and why."
                                  class="mt-2 w-full rounded-xl
                                         border border-slate-200
                                         bg-white p-4
                                         text-sm leading-6
                                         outline-none
                                         focus:border-amber-500">{{ old('remarks') }}</textarea>

                    </div>



                    <!-- SELECTED FILE COUNT -->
                    <div class="rounded-xl border border-amber-200
                                bg-amber-50 p-4">

                        <div class="flex flex-wrap items-center
                                    justify-between gap-3">

                            <div>

                                <p class="text-xs font-bold text-amber-900">

                                    Files Selected for Return

                                </p>

                                <p class="mt-1 text-xs
                                          leading-5 text-amber-800">

                                    Use the checkboxes in the
                                    Uploaded Documents section above.

                                </p>

                            </div>


                            <span id="selectedFileCount"
                                  class="rounded-full bg-amber-200
                                         px-4 py-2 text-xs
                                         font-bold text-amber-900">

                                0 Selected

                            </span>

                        </div>

                    </div>



                    <!-- RETURN BUTTON -->
                    <button type="submit"
                            @disabled(
                                $allFiles->every(
                                    fn ($file) =>
                                        $file->review_status === 'approved'
                                )
                            )
                            class="flex w-full items-center
                                   justify-center gap-2
                                   rounded-xl bg-amber-600
                                   px-6 py-3.5 text-sm
                                   font-bold text-white
                                   hover:bg-amber-700
                                   disabled:cursor-not-allowed
                                   disabled:opacity-50">

                        <i data-lucide="rotate-ccw"
                           class="h-5 w-5"></i>

                        Return Selected Documents

                    </button>



                    <p class="text-xs leading-5 text-slate-500">

                        The Teacher will receive the
                        returned status and remarks
                        in their Submission of Requirements page.

                    </p>

                </form>

            </section>

        </div>



    @else


        <!-- ==================================
             REVIEW ACTIONS LOCKED
        =================================== -->

        <section class="rounded-2xl border
                        border-slate-200 bg-slate-50 p-6">


            <div class="flex items-start gap-3">

                <i data-lucide="lock"
                   class="h-5 w-5 text-slate-500"></i>


                <div>

                    <h2 class="text-sm font-bold text-slate-700">

                        Review Actions Unavailable

                    </h2>


                    <p class="mt-2 text-sm leading-6
                              text-slate-500">

                        @if($submission->status === 'approved')

                            This submission has already been approved.

                            You can still preview and download
                            its uploaded documents.

                        @elseif($submission->status === 'returned')

                            This submission was returned to
                            the Teacher.

                            The Teacher must upload the
                            corrected documents and resubmit
                            before another review.

                        @elseif($submission->status === 'draft')

                            The Teacher has not completed
                            the final submission.

                        @else

                            This submission is not ready
                            for Principal review.

                        @endif

                    </p>

                </div>

            </div>

        </section>

    @endif



    <!-- ======================================
         PRINCIPAL REVIEW HISTORY
    ======================================= -->

    <section class="overflow-hidden rounded-2xl
                    border border-slate-200
                    bg-white shadow-sm">


        <!-- HEADER -->
        <div class="border-b border-slate-100 p-6">

            <h2 class="text-lg font-extrabold text-slate-900">
                Review History
            </h2>

            <p class="mt-2 text-xs text-slate-500">

                Previous approvals, returned documents,
                Principal remarks, and review dates.

            </p>

        </div>



        <!-- REVIEW RECORDS -->
        <div class="divide-y divide-slate-100">

            @forelse($submission->reviews as $review)

                <div class="p-6">


                    <div class="flex flex-wrap
                                items-start justify-between
                                gap-4">

                        <div>

                            <div class="flex flex-wrap items-center gap-3">

                                <span class="rounded-full px-3 py-1.5
                                             text-xs font-bold
                                             {{ $review->decision === 'approved'
                                                 ? 'bg-emerald-50 text-emerald-700'
                                                 : 'bg-amber-50 text-amber-800' }}">

                                    {{ ucfirst($review->decision) }}

                                </span>

                            </div>


                            <p class="mt-3 text-xs text-slate-500">

                                Reviewed by

                                <span class="font-bold text-slate-700">
                                    {{ $review->reviewer?->name ?? 'Former Principal' }}
                                </span>

                            </p>

                        </div>



                        <p class="text-xs text-slate-500">

                            {{ $review->created_at
                                ? $review->created_at->copy()
                                    ->timezone('Asia/Manila')
                                    ->format('M d, Y h:i A')
                                : 'Date unavailable' }}

                        </p>

                    </div>



                    <!-- OVERALL REVIEW REMARKS -->
                    @if($review->remarks)

                        <div class="mt-4 rounded-xl
                                    border border-slate-100
                                    bg-slate-50 p-4">

                            <p class="text-xs font-bold text-slate-700">
                                Overall Remarks
                            </p>

                            <p class="mt-2 whitespace-pre-line
                                      text-sm leading-6
                                      text-slate-700">{{ $review->remarks }}</p>

                        </div>

                    @endif



                    <!-- RETURNED FILE FEEDBACK -->
                    @if(!empty($review->file_feedback))

                        <div class="mt-5 space-y-3">

                            <p class="text-xs font-bold
                                      uppercase tracking-widest
                                      text-amber-800">

                                Returned File Feedback

                            </p>


                            @foreach($review->file_feedback as $feedback)

                                <div class="rounded-xl
                                            border border-amber-200
                                            bg-amber-50 p-4">


                                    <p class="break-all text-xs
                                              font-extrabold
                                              text-amber-900">

                                        {{ $feedback['filename'] ?? 'Document' }}

                                    </p>


                                    <p class="mt-2 text-xs
                                              font-semibold
                                              text-amber-700">

                                        Requirement:

                                        {{ $feedback['requirement'] ?? 'Unknown' }}

                                    </p>


                                    <p class="mt-3 whitespace-pre-line
                                              text-xs leading-6
                                              text-amber-800">{{ $feedback['remarks'] ?? 'Correction requested.' }}</p>

                                </div>

                            @endforeach

                        </div>

                    @endif

                </div>


            @empty


                <div class="p-12 text-center">

                    <div class="mx-auto flex h-12 w-12
                                items-center justify-center
                                rounded-xl bg-slate-100">

                        <i data-lucide="history"
                           class="h-6 w-6 text-slate-400"></i>

                    </div>


                    <p class="mt-4 text-sm font-bold text-slate-700">

                        No Review History Yet

                    </p>


                    <p class="mt-2 text-xs text-slate-500">

                        Principal decisions will appear here
                        once this submission has been reviewed.

                    </p>

                </div>


            @endforelse

        </div>

    </section>

</div>

@endsection



<!-- ==========================================
     PAGE JAVASCRIPT
=========================================== -->

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ======================================
    // RETURN CHECKBOXES
    // ======================================

    const returnForm = document.getElementById(
        'returnSubmissionForm'
    );

    const checkboxes = document.querySelectorAll(
        '.return-file-checkbox'
    );

    const selectedFileCount = document.getElementById(
        'selectedFileCount'
    );


    // ======================================
    // UPDATE SELECTED COUNT
    // ======================================

    function updateSelectedCount() {

        const selected = Array.from(checkboxes)
            .filter(function (checkbox) {
                return checkbox.checked;
            });

        if (selectedFileCount) {
            selectedFileCount.textContent =
                selected.length + ' Selected';
        }
    }


    checkboxes.forEach(function (checkbox) {

        checkbox.addEventListener(
            'change',
            updateSelectedCount
        );

    });

    updateSelectedCount();


    // ======================================
    // VALIDATE RETURN SUBMISSION
    // ======================================

    if (returnForm) {

        returnForm.addEventListener('submit', function (event) {

            const selected = Array.from(checkboxes)
                .filter(function (checkbox) {
                    return checkbox.checked;
                });

            // At least one file must be selected.
            if (selected.length === 0) {

                event.preventDefault();

                alert(
                    'Please select at least one incorrect document before returning the submission.'
                );

                return;
            }


            // Confirm decision before sending.
            const confirmed = confirm(
                'Return ' +
                selected.length +
                ' selected document(s) to the Teacher for correction?'
            );

            if (!confirmed) {
                event.preventDefault();
            }

        });

    }

});
</script>

@endpush
