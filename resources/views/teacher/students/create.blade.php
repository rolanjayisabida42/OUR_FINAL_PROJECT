
@extends('layouts.teacher')

@section('title', 'Add Student')
@section('page_heading', 'Add Student')

@section('content')

<div class="mx-auto max-w-5xl space-y-7">

    <!-- BACK NAVIGATION -->
    <a href="{{ route('teacher.students.index', $section) }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-teal-700">

        <svg width="18" height="18"
             viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5"/>
            <path d="m12 19-7-7 7-7"/>
        </svg>

        Back to Student List
    </a>


    <!-- PAGE HEADER -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 md:text-3xl">
                Add Student
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                {{ $section->name }}
                · Grade {{ $section->grade_level }}
                · {{ $section->strand?->code }}
                · SY {{ $section->schoolYear?->name }}
            </p>
        </div>

        <span class="self-start rounded-full bg-teal-50 px-4 py-2 text-xs font-semibold text-teal-700">
            Student Registration
        </span>
    </div>


    <!-- VALIDATION ERROR -->
    @if($errors->any())

        <div class="rounded-xl border border-red-200 bg-red-50 p-4">

            <p class="text-sm font-semibold text-red-700">
                Please check the information and try again.
            </p>

            <ul class="mt-2 list-inside list-disc text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif


    <!-- =====================================
         STEP 1: LOOK UP STUDENT
    ====================================== -->
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <!-- CARD HEADING -->
        <div class="border-b border-slate-100 bg-slate-50 px-6 py-5">

            <div class="flex items-center gap-4">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#142b61] text-white">

                    <svg width="24" height="24"
                         viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="10.5" cy="10.5" r="6.5"/>
                        <path d="m16 16 5 5"/>
                    </svg>
                </div>

                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Step 1 — Find Student
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Enter the learner's LRN to check if they are already registered.
                    </p>
                </div>

            </div>
        </div>


        <!-- LRN LOOKUP -->
        <div class="p-6">

            <form id="lrnLookupForm"
                  method="POST"
                  action="{{ route('teacher.students.lookup', $section) }}"
                  class="space-y-4">

                @csrf

                <div>
                    <label for="lookupLrn"
                           class="mb-2 block text-sm font-semibold text-slate-700">

                        Learner Reference Number (LRN)
                    </label>

                    <div class="flex flex-col gap-3 sm:flex-row">

                        <input type="text"
                               id="lookupLrn"
                               name="lrn"
                               value="{{ old('lrn') }}"
                               maxlength="12"
                               inputmode="numeric"
                               pattern="[0-9]{12}"
                               autocomplete="off"
                               required
                               placeholder="Enter 12-digit LRN"
                               class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3.5 font-mono text-sm outline-none transition focus:border-teal-500 focus:ring-2 focus:ring-teal-500/10">

                        <button type="submit"
                                id="lookupButton"
                                class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-[#0d9488] px-6 py-3.5 text-sm font-bold text-white transition hover:bg-[#0f766e] disabled:cursor-wait disabled:opacity-60">

                            <svg width="18" height="18"
                                 viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>

                            <span id="lookupButtonText">
                                Find Student
                            </span>
                        </button>

                    </div>

                    <p class="mt-2 text-xs text-slate-400">
                        Enter exactly 12 digits, without spaces or hyphens.
                    </p>
                </div>
            </form>


            <!-- LOOKUP FEEDBACK -->
            <div id="lookupFeedback"
                 role="status"
                 aria-live="polite"
                 class="mt-5 hidden rounded-xl border px-4 py-3 text-sm">
            </div>


            <!-- =====================================
                 FOUND EXISTING STUDENT
            ====================================== -->
            <div id="foundStudentPanel"
                 class="mt-6 hidden overflow-hidden rounded-2xl border border-emerald-200 bg-emerald-50">

                <div class="border-b border-emerald-200 px-5 py-4">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">

                            <svg width="21" height="21"
                                 viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="m8 12 3 3 5-6"/>
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-bold text-emerald-900">
                                Existing Student Found
                            </h3>

                            <p class="text-xs text-emerald-700">
                                Verify the student's identity before proceeding.
                            </p>
                        </div>
                    </div>
                </div>


                <!-- STUDENT PREVIEW -->
                <div class="p-5">

                    <div class="grid gap-5 sm:grid-cols-2">

                        <div>
                            <p class="text-xs font-semibold text-emerald-700">
                                Student Name
                            </p>

                            <p id="foundStudentName"
                               class="mt-2 text-lg font-bold text-slate-900">
                                —
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-emerald-700">
                                Sex
                            </p>

                            <p id="foundStudentSex"
                               class="mt-2 font-bold text-slate-900">
                                —
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl border border-emerald-200 bg-white/70 p-4">

                        <p class="text-xs leading-6 text-slate-600">
                            Confirm that this is the correct learner.
                            The existing student profile will be linked
                            to this classroom without creating a duplicate.
                        </p>
                    </div>


                    <!-- ADD EXISTING STUDENT -->
                    <form id="attachExistingForm"
                          action="{{ route('teacher.students.attach-existing', $section) }}"
                          method="POST"
                          class="mt-5">

                        @csrf

                        <input type="hidden"
                               name="lrn"
                               id="existingStudentLrn">

                        <button type="submit"
                                class="inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-[#142b61] px-5 py-3.5 text-sm font-bold text-white transition hover:bg-[#1e4085]">

                            <svg width="19" height="19"
                                 viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M2 21v-2a7 7 0 0 1 14 0v2"/>
                                <path d="M19 8v6M16 11h6"/>
                            </svg>

                            Add Existing Student to Classroom
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </section>


    <!-- =====================================
         STEP 2: REGISTER NEW STUDENT
    ====================================== -->
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    Step 2 — New Student Registration
                </h2>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    If the LRN is not found, enter the learner's
                    complete information to create a profile.
                </p>
            </div>

            <button type="button"
                    id="manualRegisterButton"
                    class="inline-flex cursor-pointer items-center justify-center gap-2 self-start rounded-xl border border-teal-200 bg-teal-50 px-5 py-3 text-sm font-semibold text-teal-700 transition hover:bg-teal-100">

                <svg width="18" height="18"
                     viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M2 21v-2a7 7 0 0 1 14 0v2"/>
                    <path d="M19 8v6M16 11h6"/>
                </svg>

                Register New Student
            </button>
        </div>
    </section>


    <!-- FULL STUDENT REGISTRATION FORM -->
    <div id="newStudentPanel"
         class="{{ $errors->any() ? '' : 'hidden' }}">

        <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 p-5">

            <h3 class="text-sm font-bold text-blue-900">
                New Learner Information
            </h3>

            <p class="mt-2 text-sm leading-6 text-blue-700">
                Complete the information below. Age will be calculated
                automatically based on the first Friday of June
                for school year {{ $section->schoolYear?->name }}.
            </p>
        </div>

        @include('teacher.students._form', [
            'mode' => 'create'
        ])

    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', () => {

    const lookupForm = document.getElementById('lrnLookupForm');
    const lookupInput = document.getElementById('lookupLrn');
    const lookupButton = document.getElementById('lookupButton');
    const lookupButtonText = document.getElementById('lookupButtonText');

    const feedback = document.getElementById('lookupFeedback');

    const foundPanel = document.getElementById('foundStudentPanel');
    const foundName = document.getElementById('foundStudentName');
    const foundSex = document.getElementById('foundStudentSex');
    const existingLrn = document.getElementById('existingStudentLrn');

    const newStudentPanel = document.getElementById('newStudentPanel');
    const manualButton = document.getElementById('manualRegisterButton');

    const newStudentLrn = document.getElementById('lrn');


    // ==========================================
    // DISPLAY STATUS MESSAGE
    // ==========================================
    function showFeedback(message, type = 'info') {

        feedback.classList.remove('hidden');

        const base = 'mt-5 rounded-xl border px-4 py-3 text-sm ';

        if (type === 'success') {
            feedback.className =
                base + 'border-emerald-200 bg-emerald-50 text-emerald-800';
        } else if (type === 'error') {
            feedback.className =
                base + 'border-red-200 bg-red-50 text-red-700';
        } else {
            feedback.className =
                base + 'border-blue-200 bg-blue-50 text-blue-700';
        }

        feedback.textContent = message;
    }


    // ==========================================
    // ENABLE NEW STUDENT FORM
    // ==========================================
    function showRegistrationForm(lrn = '') {

        foundPanel.classList.add('hidden');

        newStudentPanel.classList.remove('hidden');

        if (newStudentLrn && lrn) {
            newStudentLrn.value = lrn;
        }

        manualButton.textContent = 'Registration Form Open';

        newStudentPanel.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }


    // ==========================================
    // KEEP LRN NUMERIC
    // ==========================================
    lookupInput.addEventListener('input', () => {

        lookupInput.value = lookupInput.value
            .replace(/\D/g, '')
            .slice(0, 12);

        foundPanel.classList.add('hidden');

        feedback.classList.add('hidden');

        existingLrn.value = '';
    });


    // ==========================================
    // LOOKUP EXISTING STUDENT
    // ==========================================
    lookupForm.addEventListener('submit', async (event) => {

        event.preventDefault();

        const lrn = lookupInput.value.trim();

        if (!/^\d{12}$/.test(lrn)) {
            showFeedback('Please enter a valid 12-digit LRN.', 'error');
            return;
        }

        foundPanel.classList.add('hidden');
        feedback.classList.add('hidden');
        existingLrn.value = '';

        lookupButton.disabled = true;
        lookupButtonText.textContent = 'Searching...';

        try {

            const response = await fetch(lookupForm.action, {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN':
                        lookupForm.querySelector('[name="_token"]').value
                },

                body: JSON.stringify({ lrn })
            });

            const data = await response.json();

            if (!response.ok) {
                const validationMessage =
                    data.errors?.lrn?.[0] ??
                    data.message ??
                    'Unable to search for this student.';

                showFeedback(validationMessage, 'error');
                return;
            }

            // EXISTING STUDENT FOUND
            if (data.status === 'found') {

                foundName.textContent = data.student.name;
                foundSex.textContent =
                    data.student.sex === 'M' ? 'Male' : 'Female';

                existingLrn.value = lrn;

                foundPanel.classList.remove('hidden');
                newStudentPanel.classList.add('hidden');

                showFeedback(data.message, 'success');

                foundPanel.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });

            // NEW STUDENT
            } else if (data.status === 'not_found') {

                showFeedback(data.message, 'info');

                showRegistrationForm(lrn);

            // EXISTING CONFLICT
            } else {

                showFeedback(data.message, 'error');

                newStudentPanel.classList.add('hidden');
            }

        } catch (error) {

            showFeedback(
                'Search failed. Check your connection and try again.',
                'error'
            );

        } finally {

            lookupButton.disabled = false;
            lookupButtonText.textContent = 'Find Student';
        }
    });


    // ==========================================
    // MANUAL REGISTRATION BUTTON
    // ==========================================
    manualButton.addEventListener('click', () => {

        showRegistrationForm(lookupInput.value.trim());

    });

});
</script>

@endpush
