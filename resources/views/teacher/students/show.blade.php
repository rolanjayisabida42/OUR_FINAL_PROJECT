
@extends('layouts.teacher')

@section('title', 'Student Profile')
@section('page_heading', 'Student Profile')

@section('content')

@php
    $groups = [

        'Basic Information' => [
            ['LRN', $student->lrn],
            ['Last Name', $student->last_name],
            ['First Name', $student->first_name],
            ['Middle Name', $student->middle_name],
            ['Sex', $student->sex],

            [
                'Birth Date',
                $student->birth_date?->format('F j, Y')
            ],

            [
                'Age as of ' . $cutoff->format('F j, Y'),
                $ageOnCutoff === null
                    ? '—'
                    : $ageOnCutoff . ' years old'
            ],

            ['Birth Place (Province)', $student->birth_place_province],
            ['Mother Tongue', $student->mother_tongue],
            ['IP / Ethnic Group', $student->ip_ethnic_group],
            ['Religion', $student->religion],
        ],

        'Address Information' => [
            [
                'House # / Street / Sitio / Purok',
                $student->house_street_sitio_purok
            ],
            ['Barangay', $student->barangay],
            ['Municipality / City', $student->municipality_city],
            ['Province', $student->province],
        ],

        'Parent Information' => [
            ["Father's First Name", $student->father_first_name],
            ["Father's Family Name", $student->father_last_name],
            [
                "Mother's Maiden First Name",
                $student->mother_maiden_first_name
            ],
            [
                "Mother's Maiden Middle Name",
                $student->mother_maiden_middle_name
            ],
            [
                "Mother's Maiden Last Name",
                $student->mother_maiden_last_name
            ],
        ],

        'Guardian Information' => [
            ['Guardian Name', $student->guardian_name],
            [
                'Guardian Relationship',
                $student->guardian_relationship
            ],
            ['Contact Number', $student->contact_number],
        ],
    ];
@endphp


<div class="mx-auto max-w-5xl space-y-6">

    <!-- BACK -->
    <a href="{{ route('teacher.students.index', $section) }}"
       class="inline-flex items-center gap-2
              text-sm font-semibold text-slate-500
              hover:text-teal-700">

        <i data-lucide="arrow-left" class="h-4 w-4"></i>
        Back to Student List
    </a>


    <!-- SUCCESS -->
    @if(session('success'))

        <div class="rounded-xl border border-emerald-200
                    bg-emerald-50 p-4 text-sm
                    font-semibold text-emerald-700">

            {{ session('success') }}
        </div>

    @endif


    <!-- PROFILE HEADER -->
    <section class="rounded-2xl bg-[#142b61]
                    p-7 text-white shadow-sm">

        <div class="flex flex-wrap items-start
                    justify-between gap-4">

            <div>
                <p class="text-xs uppercase tracking-widest
                          text-blue-200">
                    Learner Profile
                </p>

                <h1 class="mt-3 text-2xl font-extrabold md:text-3xl">
                    {{ $student->last_name }},
                    {{ $student->first_name }}
                    {{ $student->middle_name }}
                </h1>

                <p class="mt-3 text-sm text-blue-100">
                    LRN: {{ $student->lrn }}
                </p>

                <p class="mt-1 text-sm text-blue-100">
                    {{ $section->name }}
                    · Grade {{ $section->grade_level }}
                    · SY {{ $section->schoolYear?->name }}
                </p>
            </div>

            <span class="rounded-full px-4 py-2 text-xs
                         font-semibold
                         {{ $isCurrent
                            ? 'bg-emerald-400/20 text-emerald-100'
                            : 'bg-white/15 text-white' }}">

                {{ $isCurrent ? 'Active Classroom' : 'Archived' }}
            </span>
        </div>


        <!-- ACTION BUTTONS -->
        @if($isCurrent)

            <div class="mt-6 flex flex-wrap gap-3">

                <a href="{{ route('teacher.students.edit', [$section, $student]) }}"
                   class="inline-flex items-center gap-2
                          rounded-xl bg-teal-600 px-5 py-3
                          text-sm font-semibold text-white
                          hover:bg-teal-700">

                    <i data-lucide="pencil" class="h-4 w-4"></i>
                    Edit Information
                </a>


                <!-- REMOVE -->
                <form method="POST"
                      action="{{ route('teacher.students.destroy', [$section, $student]) }}"
                      onsubmit="return confirm('Remove this student from the classroom? The student profile will be preserved.');">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="inline-flex cursor-pointer
                                   items-center gap-2 rounded-xl
                                   border border-white/30 px-5 py-3
                                   text-sm font-semibold text-white
                                   hover:bg-white/10">

                        <i data-lucide="user-round-x"
                           class="h-4 w-4"></i>

                        Remove from Classroom
                    </button>
                </form>
            </div>

        @endif
    </section>


    <!-- ALL STUDENT INFORMATION -->
    @foreach($groups as $title => $fields)

        <section class="overflow-hidden rounded-2xl
                        border border-slate-200
                        bg-white shadow-sm">

            <div class="border-b border-slate-100
                        bg-slate-50 px-6 py-5">

                <h2 class="text-lg font-bold text-slate-900">
                    {{ $title }}
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-6
                        p-6 sm:grid-cols-2">

                @foreach($fields as [$label, $value])

                    <div>
                        <p class="text-xs font-semibold text-slate-400">
                            {{ $label }}
                        </p>

                        <p class="mt-2 break-words text-sm
                                  font-semibold text-slate-800">

                            {{ filled($value) ? $value : '—' }}
                        </p>
                    </div>

                @endforeach

            </div>
        </section>

    @endforeach

</div>

@endsection
