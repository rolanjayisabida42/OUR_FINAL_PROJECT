
@extends('layouts.principal-review')

@section('title', 'Classroom Return Review | SHS Forms')
@section('heading', 'Return Submissions')

@section('content')

<div class="mx-auto max-w-7xl space-y-7 pb-12">

    <!-- PAGE HEADER -->

    <div class="flex flex-wrap items-center
                justify-between gap-4">

        <div>

            <p class="text-xs font-bold uppercase
                      tracking-widest text-teal-700">
                Principal / Submission Management
            </p>

            <h1 class="mt-2 text-2xl font-extrabold
                       text-slate-900 md:text-3xl">
                Select a Classroom
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                View all classrooms and select submitted
                requirements that need Principal review.
            </p>

        </div>

        <a href="{{ route('principal.submissions.review.index') }}"
           class="inline-flex items-center gap-2 rounded-xl
                  border border-slate-200 bg-white
                  px-4 py-3 text-sm font-bold text-slate-700
                  hover:bg-slate-50">

            <i data-lucide="arrow-left"
               class="h-4 w-4"></i>

            Review Submissions

        </a>

    </div>


    <!-- CLASSROOM COUNTER -->

    <section class="rounded-3xl bg-[#142b61]
                    px-7 py-8 text-white shadow-sm">

        <div class="flex items-center gap-4">

            <div class="grid h-14 w-14
                        shrink-0 place-items-center
                        rounded-2xl bg-white/10">

                <i data-lucide="school"
                   class="h-7 w-7 text-teal-300"></i>

            </div>

            <div>

                <p class="text-sm text-blue-200">
                    Registered Classrooms
                </p>

                <p class="mt-1 text-2xl font-extrabold">
                    {{ $sections->total() }} Classrooms
                </p>

            </div>

        </div>

    </section>


    <!-- EMPTY CLASSROOM STATE -->

    @if($sections->count() === 0)

        <div class="rounded-2xl border
                    border-dashed border-slate-300
                    bg-white px-6 py-16 text-center">

            <div class="mx-auto grid h-16 w-16
                        place-items-center rounded-2xl
                        bg-slate-100">

                <i data-lucide="school"
                   class="h-8 w-8 text-slate-400"></i>

            </div>

            <h2 class="mt-5 text-lg font-bold text-slate-900">
                No Classrooms Found
            </h2>

            <p class="mx-auto mt-2 max-w-lg
                      text-sm leading-6 text-slate-500">

                There are no classroom records in your system.
                Create and assign classrooms before Teachers
                can submit their semester requirements.

            </p>

        </div>

    @else

        <!-- CLASSROOM CARDS -->

        <div class="grid gap-5
                    md:grid-cols-2 xl:grid-cols-3">

            @foreach($sections as $section)

                @php

                    $records = $submissionGroups->get(
                        $section->id,
                        collect()
                    );

                @endphp

                <article class="flex flex-col
                                rounded-2xl border
                                border-slate-200 bg-white
                                p-6 shadow-sm transition
                                hover:border-teal-300
                                hover:shadow-md">


                    <!-- CARD HEADER -->

                    <div class="flex items-start
                                justify-between gap-3">

                        <div class="grid h-12 w-12
                                    place-items-center
                                    rounded-xl bg-teal-50
                                    text-teal-700">

                            <i data-lucide="school"
                               class="h-6 w-6"></i>

                        </div>

                        <span class="rounded-full px-3 py-1.5
                                     text-xs font-bold

                                     {{ $section->is_active
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'bg-amber-50 text-amber-800' }}">

                            {{ $section->is_active
                                ? 'Active'
                                : 'Archived' }}

                        </span>

                    </div>


                    <!-- CLASSROOM INFORMATION -->

                    <p class="mt-5 text-xs font-bold
                              uppercase tracking-widest
                              text-teal-700">

                        Grade {{ $section->grade_level }}

                        · {{ $section->strand?->code ?? 'No Strand' }}

                    </p>

                    <h2 class="mt-2 text-xl font-extrabold
                               text-slate-900">

                        {{ $section->name }}

                    </h2>

                    <p class="mt-2 text-xs leading-5
                              text-slate-500">

                        School Year:
                        {{ $section->schoolYear?->name ?? 'Not Set' }}

                    </p>

                    <p class="mt-1 text-xs leading-5
                              text-slate-500">

                        Teacher:
                        {{ $section->adviser?->teacher?->name ?? 'Not Assigned' }}

                    </p>


                    <!-- SEMESTER SUBMISSIONS -->

                    <div class="mt-5 flex-1 space-y-3
                                rounded-xl bg-slate-50 p-4">

                        <p class="text-xs font-bold uppercase
                                  tracking-wider text-slate-500">

                            Semester Submissions

                        </p>


                        @forelse($records as $submission)

                            @php

                                $reviewable =
                                    $submission->status === 'submitted'
                                    && $submission->pending_files_count > 0;

                                $semesterNumber =
                                    $submission->semester?->semester_number;

                                $semesterName = match (
                                    (int) $semesterNumber
                                ) {
                                    1 => '1st Semester',
                                    2 => '2nd Semester',
                                    default => 'Semester',
                                };

                                $statusClass = match (
                                    $submission->status
                                ) {
                                    'submitted' =>
                                        'text-blue-700',

                                    'approved' =>
                                        'text-emerald-700',

                                    'returned' =>
                                        'text-amber-700',

                                    default =>
                                        'text-slate-500',
                                };

                            @endphp


                            <div class="flex flex-wrap
                                        items-center justify-between
                                        gap-3 border-t
                                        border-slate-200 pt-3
                                        first:border-0 first:pt-0">

                                <div>

                                    <p class="text-xs font-bold
                                              text-slate-800">

                                        {{ $semesterName }}

                                    </p>

                                    <p class="mt-1 text-xs
                                              font-semibold
                                              {{ $statusClass }}">

                                        {{ ucfirst($submission->status) }}

                                    </p>

                                </div>


                                @if($reviewable)

                                    <a href="{{ route(
                                            'principal.submissions.review.return-page',
                                            $submission
                                        ) }}"
                                       class="inline-flex
                                              items-center gap-2
                                              rounded-lg bg-teal-600
                                              px-3 py-2
                                              text-xs font-bold
                                              text-white
                                              hover:bg-teal-700">

                                        <i data-lucide="arrow-right"
                                           class="h-3.5 w-3.5"></i>

                                        Review & Return

                                    </a>

                                @else

                                    <span class="rounded-lg
                                                 bg-slate-200
                                                 px-3 py-2
                                                 text-[11px]
                                                 font-semibold
                                                 text-slate-500">

                                        Not Reviewable

                                    </span>

                                @endif

                            </div>


                        @empty

                            <div class="rounded-lg border
                                        border-dashed
                                        border-slate-200
                                        bg-white p-4">

                                <div class="flex items-center gap-2">

                                    <i data-lucide="clock"
                                       class="h-4 w-4 text-slate-400"></i>

                                    <p class="text-xs text-slate-500">

                                        No requirements submitted yet.

                                    </p>

                                </div>

                            </div>

                        @endforelse

                    </div>

                </article>

            @endforeach

        </div>


        <!-- PAGINATION -->

        <div class="mt-6">

            {{ $sections->links() }}

        </div>

    @endif

</div>

@endsection
