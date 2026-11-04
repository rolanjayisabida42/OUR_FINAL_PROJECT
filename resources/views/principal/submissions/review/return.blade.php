
@extends('layouts.principal-review')

@section('title', 'Return Submission | SHS Forms')
@section('heading', 'Return Submission')

@section('content')

@php

    $pendingFiles = $files->where(
        'review_status',
        'pending'
    );

    $approvedFiles = $files->where(
        'review_status',
        'approved'
    );

    $previousSelections = array_map(
        'strval',
        old('returned_files', [])
    );

    $semesterLabel =
        $submission->semester?->semester_number == 1
            ? '1st Semester'
            : '2nd Semester';

@endphp


<div class="mx-auto max-w-7xl space-y-6 pb-12">

    <!-- ========================================
         BACK NAVIGATION
    ========================================= -->

    <div class="flex flex-wrap items-center
                justify-between gap-4">

        <a href="{{ route('principal.submissions.review.return-queue') }}"
           class="inline-flex items-center gap-2
                  text-sm font-semibold text-slate-500
                  hover:text-teal-600">

            <i data-lucide="arrow-left"
               class="h-4 w-4"></i>

            Back to Classrooms

        </a>

        <a href="{{ route('principal.submissions.review.show', $submission) }}"
           class="text-sm font-bold text-teal-700
                  hover:underline">

            View Submission Details

        </a>

    </div>


    <!-- ========================================
         CLASSROOM HEADER
    ========================================= -->

    <section class="overflow-hidden rounded-3xl
                    bg-[#142b61] text-white shadow-lg">

        <div class="px-6 py-8 md:px-9">

            <div class="flex flex-wrap items-start
                        justify-between gap-5">

                <div class="flex items-start gap-4">

                    <div class="grid h-14 w-14 shrink-0
                                place-items-center rounded-2xl
                                bg-white/10">

                        <i data-lucide="file-warning"
                           class="h-7 w-7 text-amber-300"></i>

                    </div>

                    <div>

                        <p class="text-xs font-bold uppercase
                                  tracking-widest text-amber-200">

                            Principal / Return for Correction

                        </p>

                        <h1 class="mt-2 text-2xl font-extrabold
                                   md:text-3xl">

                            {{ $section->name }}

                        </h1>

                        <p class="mt-2 text-sm text-blue-100">

                            Grade {{ $section->grade_level }}

                            · {{ $section->strand?->code ?? 'No Strand' }}

                            · {{ $semesterLabel }}

                        </p>

                    </div>

                </div>

                <span class="rounded-xl bg-white/10
                             px-4 py-2 text-xs font-bold">

                    Submission #{{ $submission->id }}

                </span>

            </div>

        </div>

    </section>


    <!-- ========================================
         CLASSROOM DETAILS
    ========================================= -->

    <section class="grid gap-4 rounded-2xl
                    border border-slate-200
                    bg-white p-6 shadow-sm
                    sm:grid-cols-2 lg:grid-cols-4">

        <div>

            <p class="text-xs text-slate-500">
                Classroom
            </p>

            <p class="mt-1 font-bold text-slate-900">
                {{ $section->name }}
            </p>

        </div>

        <div>

            <p class="text-xs text-slate-500">
                Teacher / Adviser
            </p>

            <p class="mt-1 font-bold text-slate-900">
                {{ $submission->teacher?->name ?? 'Not Assigned' }}
            </p>

        </div>

        <div>

            <p class="text-xs text-slate-500">
                School Year / Semester
            </p>

            <p class="mt-1 font-bold text-slate-900">

                {{ $section->schoolYear?->name ?? 'N/A' }}

                · {{ $semesterLabel }}

            </p>

        </div>

        <div>

            <p class="text-xs text-slate-500">
                Submitted On
            </p>

            <p class="mt-1 font-bold text-slate-900">

                {{ $submission->submitted_at
                    ?->copy()
                    ->timezone('Asia/Manila')
                    ->format('M d, Y h:i A')
                    ?? 'Not Recorded' }}

            </p>

        </div>

    </section>


    <!-- ========================================
         VALIDATION ERRORS
    ========================================= -->

    @if($errors->any())

        <div role="alert"
             class="rounded-2xl border border-red-200
                    bg-red-50 p-5 text-sm text-red-800">

            <p class="font-bold">
                Unable to Return Submission
            </p>

            <ul class="mt-2 list-disc space-y-1 pl-5">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <!-- ========================================
         CHECK RETURN AVAILABILITY
    ========================================= -->

    @unless($canReturn)

        <div class="rounded-2xl border border-amber-200
                    bg-amber-50 p-5 text-sm text-amber-900">

            This submission is currently

            <strong>
                {{ ucfirst($submission->status) }}
            </strong>

            and has no pending documents eligible
            for correction.

            Select another classroom submission
            from the classroom list.

        </div>

    @else


        <!-- ====================================
             RETURN SUBMISSION FORM
        ===================================== -->

        <form id="returnSubmissionForm"
              action="{{ route('principal.submissions.review.return', $submission) }}"
              method="POST">

            @csrf


            <div class="grid items-start gap-6
                        xl:grid-cols-3">


                <!-- ==========================
                     DOCUMENT LIST
                =========================== -->

                <div class="space-y-5 xl:col-span-2">


                    <!-- INSTRUCTIONS -->

                    <section class="rounded-2xl
                                    border border-blue-100
                                    bg-blue-50 p-5
                                    text-sm leading-6
                                    text-blue-900">

                        <div class="flex items-start gap-3">

                            <i data-lucide="info"
                               class="mt-0.5 h-5 w-5 shrink-0"></i>

                            <p>

                                Select only the documents
                                that need corrections.

                                Unselected pending documents
                                will be approved.

                                Previously approved documents
                                will remain approved.

                            </p>

                        </div>

                    </section>


                    <!-- UPLOADED DOCUMENTS -->

                    <section class="overflow-hidden
                                    rounded-2xl
                                    border border-slate-200
                                    bg-white shadow-sm">


                        <!-- DOCUMENT HEADER -->

                        <div class="flex flex-wrap items-center
                                    justify-between gap-3
                                    border-b border-slate-100 p-6">

                            <div>

                                <h2 class="text-lg font-bold
                                           text-slate-900">

                                    Uploaded Documents

                                </h2>

                                <p class="mt-1 text-xs text-slate-500">

                                    Review files for
                                    {{ $section->name }}.

                                </p>

                            </div>

                            <span id="selectedCounter"
                                  class="rounded-full bg-amber-50
                                         px-3 py-2 text-xs
                                         font-bold text-amber-800">

                                0 selected

                            </span>

                        </div>


                        <!-- SELECT ALL -->

                        <div class="flex flex-wrap items-center
                                    justify-between gap-3
                                    border-b border-slate-100
                                    bg-slate-50 px-6 py-4">

                            <label class="flex cursor-pointer
                                          items-center gap-2
                                          text-xs font-bold
                                          text-slate-700">

                                <input id="selectAllFiles"
                                       type="checkbox"
                                       class="h-4 w-4
                                              accent-amber-600">

                                Select All Pending Files

                            </label>

                            <span class="text-xs text-slate-500">

                                {{ $pendingFiles->count() }}
                                awaiting review

                            </span>

                        </div>


                        <!-- FILE ITEMS -->

                        <div class="divide-y divide-slate-100">

                            @foreach($files as $file)

                                @php

                                    $pending =
                                        $file->review_status === 'pending';

                                @endphp


                                <article class="p-5 md:p-6">

                                    <div class="flex items-start gap-4">


                                        <!-- CHECKBOX / ICON -->

                                        <div class="grid h-11 w-11
                                                    shrink-0 place-items-center
                                                    rounded-xl

                                                    {{ $pending
                                                        ? 'bg-amber-50'
                                                        : 'bg-emerald-50' }}">

                                            @if($pending)

                                                <input
                                                    type="checkbox"
                                                    name="returned_files[]"
                                                    value="{{ $file->id }}"
                                                    class="return-file-checkbox
                                                           h-5 w-5
                                                           accent-amber-600"
                                                    aria-label="Return {{ $file->original_name }}"
                                                    @checked(
                                                        in_array(
                                                            (string) $file->id,
                                                            $previousSelections,
                                                            true
                                                        )
                                                    )
                                                >

                                            @else

                                                <i data-lucide="check-circle-2"
                                                   class="h-5 w-5
                                                          text-emerald-600"></i>

                                            @endif

                                        </div>


                                        <!-- FILE INFORMATION -->

                                        <div class="min-w-0 flex-1">

                                            <div class="flex flex-wrap
                                                        justify-between gap-3">

                                                <div class="min-w-0">

                                                    <p class="text-xs font-bold
                                                              uppercase tracking-wide
                                                              text-teal-700">

                                                        {{ $file->requirement?->name ?? 'Requirement' }}

                                                    </p>

                                                    <p class="mt-2 break-all
                                                              text-sm font-bold
                                                              text-slate-900">

                                                        {{ $file->original_name }}

                                                    </p>

                                                    <p class="mt-1 text-xs
                                                              text-slate-500">

                                                        {{ number_format(
                                                            $file->size_bytes / 1024,
                                                            1
                                                        ) }} KB

                                                    </p>

                                                </div>


                                                <!-- STATUS -->

                                                <span class="self-start
                                                             rounded-full
                                                             px-3 py-1.5
                                                             text-xs font-bold

                                                             {{ $pending
                                                                ? 'bg-blue-50 text-blue-700'
                                                                : 'bg-emerald-50 text-emerald-700' }}">

                                                    {{ ucfirst($file->review_status) }}

                                                </span>

                                            </div>


                                            <!-- DOWNLOAD FILE -->

                                            <a href="{{ route(
                                                    'principal.submissions.review.files.download',
                                                    [
                                                        'submission' => $submission,
                                                        'file' => $file
                                                    ]
                                                ) }}"
                                               class="mt-4 inline-flex
                                                      items-center gap-2
                                                      rounded-xl
                                                      border border-slate-200
                                                      px-4 py-2.5
                                                      text-xs font-bold
                                                      text-slate-700
                                                      hover:border-teal-300
                                                      hover:text-teal-700">

                                                <i data-lucide="download"
                                                   class="h-4 w-4"></i>

                                                Download Document

                                            </a>


                                            <!-- SPECIFIC REMARKS -->

                                            @if($pending)

                                                <div class="mt-5 rounded-xl
                                                            border border-amber-100
                                                            bg-amber-50/60 p-4">

                                                    <label
                                                        for="fileRemarks{{ $file->id }}"
                                                        class="block text-xs
                                                               font-bold
                                                               text-amber-900">

                                                        Specific Correction Remarks
                                                        (Optional)

                                                    </label>

                                                    <textarea
                                                        id="fileRemarks{{ $file->id }}"
                                                        name="file_remarks[{{ $file->id }}]"
                                                        rows="3"
                                                        maxlength="1000"
                                                        placeholder="Explain what should be corrected..."
                                                        class="mt-2 w-full
                                                               rounded-xl
                                                               border border-amber-200
                                                               bg-white p-3
                                                               text-sm outline-none
                                                               focus:border-amber-500">{{ old('file_remarks.' . $file->id) }}</textarea>

                                                </div>

                                            @else

                                                <p class="mt-4 text-xs
                                                          font-semibold
                                                          text-emerald-700">

                                                    Already approved —
                                                    cannot be returned.

                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                </article>

                            @endforeach

                        </div>

                    </section>

                </div>


                <!-- ==========================
                     RETURN SUMMARY
                =========================== -->

                <div class="space-y-5
                            xl:sticky xl:top-24">


                    <!-- SUMMARY -->

                    <section class="rounded-2xl
                                    border border-slate-200
                                    bg-white p-6 shadow-sm">

                        <h2 class="text-lg font-extrabold
                                   text-slate-900">

                            Return Summary

                        </h2>

                        <div class="mt-5 space-y-3 text-sm">

                            <div class="flex justify-between">

                                <span class="text-slate-500">
                                    Total Files
                                </span>

                                <strong>
                                    {{ $files->count() }}
                                </strong>

                            </div>

                            <div class="flex justify-between">

                                <span class="text-slate-500">
                                    Previously Approved
                                </span>

                                <strong class="text-emerald-700">
                                    {{ $approvedFiles->count() }}
                                </strong>

                            </div>

                            <div class="flex justify-between">

                                <span class="text-slate-500">
                                    Return for Correction
                                </span>

                                <strong id="returnCount"
                                        class="text-amber-700">
                                    0
                                </strong>

                            </div>

                            <div class="flex justify-between">

                                <span class="text-slate-500">
                                    Newly Approved
                                </span>

                                <strong id="approveCount"
                                        class="text-emerald-700">

                                    {{ $pendingFiles->count() }}

                                </strong>

                            </div>

                        </div>

                    </section>


                    <!-- PRINCIPAL REMARKS -->

                    <section class="rounded-2xl
                                    border border-amber-200
                                    bg-white p-6 shadow-sm">

                        <label for="returnRemarks"
                               class="block text-sm
                                      font-extrabold text-slate-900">

                            Overall Principal Remarks

                            <span class="text-red-600">*</span>

                        </label>

                        <textarea
                            id="returnRemarks"
                            name="remarks"
                            rows="6"
                            required
                            minlength="10"
                            maxlength="2000"
                            placeholder="Explain clearly what the Teacher must correct..."
                            class="mt-3 w-full
                                   rounded-xl border border-slate-200
                                   p-3 text-sm leading-6
                                   outline-none
                                   focus:border-amber-500">{{ old('remarks') }}</textarea>

                        <p class="mt-2 text-xs text-slate-500">

                            At least 10 characters.
                            These remarks will be visible
                            to the Teacher.

                        </p>

                    </section>


                    <!-- SUBMIT ACTIONS -->

                    <section class="rounded-2xl
                                    border border-slate-200
                                    bg-white p-6 shadow-sm">

                        <button id="returnSubmitBtn"
                                type="submit"
                                class="flex w-full items-center
                                       justify-center gap-2
                                       rounded-xl bg-amber-600
                                       px-4 py-3.5 text-sm
                                       font-bold text-white
                                       hover:bg-amber-700
                                       disabled:cursor-not-allowed
                                       disabled:opacity-40">

                            <i data-lucide="send"
                               class="h-4 w-4"></i>

                            <span id="returnButtonText">
                                Return Selected Documents
                            </span>

                        </button>


                        <a href="{{ route('principal.submissions.review.return-queue') }}"
                           class="mt-3 flex justify-center
                                  rounded-xl border
                                  border-slate-200
                                  px-4 py-3 text-sm
                                  font-bold text-slate-700
                                  hover:bg-slate-50">

                            Cancel Review

                        </a>


                        <p class="mt-3 text-center
                                  text-xs text-slate-500">

                            The Teacher will be notified
                            after this review is saved.

                        </p>

                    </section>

                </div>

            </div>

        </form>

    @endunless

</div>


<!-- ========================================
     JAVASCRIPT
========================================= -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form =
        document.getElementById('returnSubmissionForm');

    if (!form) {
        return;
    }

    const checks = [
        ...form.querySelectorAll('.return-file-checkbox')
    ];

    const selectAll =
        document.getElementById('selectAllFiles');

    const selectedCounter =
        document.getElementById('selectedCounter');

    const returnCount =
        document.getElementById('returnCount');

    const approveCount =
        document.getElementById('approveCount');

    const button =
        document.getElementById('returnSubmitBtn');

    const buttonText =
        document.getElementById('returnButtonText');

    const remarks =
        document.getElementById('returnRemarks');

    let submitting = false;


    // ======================================
    // UPDATE SELECTION COUNTS
    // ======================================

    function updateCounts() {

        const selected = checks.filter(
            check => check.checked
        ).length;

        selectedCounter.textContent =
            selected + ' selected';

        returnCount.textContent = selected;

        approveCount.textContent =
            checks.length - selected;

        button.disabled =
            submitting || selected === 0;

        if (selectAll) {

            selectAll.checked =
                checks.length > 0 &&
                selected === checks.length;

            selectAll.indeterminate =
                selected > 0 &&
                selected < checks.length;
        }
    }


    // ======================================
    // SELECT ALL PENDING FILES
    // ======================================

    if (selectAll) {

        selectAll.addEventListener(
            'change',
            function () {

                checks.forEach(function (check) {

                    check.checked = selectAll.checked;

                });

                updateCounts();
            }
        );
    }


    // ======================================
    // INDIVIDUAL FILE SELECTION
    // ======================================

    checks.forEach(function (check) {

        check.addEventListener(
            'change',
            updateCounts
        );
    });


    // ======================================
    // FORM CONFIRMATION
    // ======================================

    form.addEventListener('submit', function (event) {

        const count = checks.filter(
            check => check.checked
        ).length;

        if (
            submitting ||
            count === 0 ||
            remarks.value.trim().length < 10
        ) {

            event.preventDefault();

            if (count === 0) {

                alert(
                    'Select at least one document to return.'
                );

            } else if (
                remarks.value.trim().length < 10
            ) {

                alert(
                    'Enter at least 10 characters of correction remarks.'
                );

                remarks.focus();
            }

            return;
        }

        const confirmed = confirm(
            'Return ' + count +
            ' selected document(s)? ' +
            'Unselected pending documents will be approved.'
        );

        if (!confirmed) {

            event.preventDefault();
            return;
        }

        submitting = true;

        button.disabled = true;

        buttonText.textContent =
            'Returning Documents...';
    });


    // ======================================
    // INITIALIZE
    // ======================================

    updateCounts();

    if (window.lucide) {
        window.lucide.createIcons();
    }

});
</script>

@endsection
