
@php
    $editing = $mode === 'edit';

    $inputClass = 'mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/10';

    $labelClass = 'block text-xs font-semibold text-slate-600';

    $groups = [
        [
            'title' => 'Additional Learner Information',
            'icon' => 'languages',
            'fields' => [
                ['birth_place_province', 'Birth Place (Province)', 150],
                ['mother_tongue', 'Mother Tongue', 120],
                ['ip_ethnic_group', 'IP / Ethnic Group', 150],
                ['religion', 'Religion', 150],
            ],
        ],
        [
            'title' => 'Address Information',
            'icon' => 'map-pin',
            'fields' => [
                ['house_street_sitio_purok', 'House # / Street / Sitio / Purok', 255],
                ['barangay', 'Barangay', 150],
                ['municipality_city', 'Municipality / City', 150],
                ['province', 'Province', 150],
            ],
        ],
        [
            'title' => 'Parent Information',
            'icon' => 'users-round',
            'fields' => [
                ['father_first_name', "Father's First Name", 120],
                ['father_last_name', "Father's Family Name (if different)", 120],
                ['mother_maiden_first_name', "Mother's Maiden First Name", 120],
                ['mother_maiden_middle_name', "Mother's Maiden Middle Name", 120],
                ['mother_maiden_last_name', "Mother's Maiden Last Name", 120],
            ],
        ],
        [
            'title' => 'Guardian Information',
            'icon' => 'contact-round',
            'fields' => [
                ['guardian_name', 'Guardian Name', 200],
                ['guardian_relationship', 'Guardian Relationship', 120],
                ['contact_number', 'Contact Number (Parent / Guardian)', 30],
            ],
        ],
    ];
@endphp


<!-- VALIDATION ERRORS -->
@if($errors->any())

    <div class="rounded-xl border border-red-200
                bg-red-50 p-5 text-sm text-red-700">

        <p class="font-bold">
            Please correct the following errors:
        </p>

        <ul class="mt-3 list-inside list-disc space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>

@endif


<form method="POST"
      action="{{ $editing
            ? route('teacher.students.update', [$section, $student])
            : route('teacher.students.store', $section) }}"
      class="space-y-6">

    @csrf

    @if($editing)
        @method('PUT')
    @endif


    <!-- BASIC INFORMATION -->
    <section class="rounded-2xl border border-slate-200
                    bg-white p-6 shadow-sm">

        <h2 class="mb-6 flex items-center gap-3
                   text-lg font-bold text-slate-900">

            <i data-lucide="user-round"
               class="h-5 w-5 text-teal-600"></i>

            Basic Information
        </h2>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

            <!-- LRN -->
            <div class="md:col-span-3">

                <label for="lrn" class="{{ $labelClass }}">
                    LRN <span class="text-red-600">*</span>
                </label>

                <input type="text"
                       id="lrn"
                       name="lrn"
                       inputmode="numeric"
                       pattern="[0-9]{12}"
                       maxlength="12"
                       required
                       value="{{ old('lrn', $student?->lrn) }}"
                       placeholder="12-digit Learner Reference Number"
                       class="{{ $inputClass }}">

                @error('lrn')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            <!-- STUDENT NAME -->
            @foreach([
                ['last_name', 'Last Name'],
                ['first_name', 'First Name'],
                ['middle_name', 'Middle Name']
            ] as [$field, $label])

                <div>
                    <label for="{{ $field }}"
                           class="{{ $labelClass }}">
                        {{ $label }}

                        @if($field !== 'middle_name')
                            <span class="text-red-600">*</span>
                        @endif
                    </label>

                    <input type="text"
                           id="{{ $field }}"
                           name="{{ $field }}"
                           maxlength="120"
                           @if($field !== 'middle_name') required @endif
                           value="{{ old($field, $student?->{$field}) }}"
                           class="{{ $inputClass }}">

                    @error($field)
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            @endforeach


            <!-- SEX -->
            <div>
                <label for="sex" class="{{ $labelClass }}">
                    Sex <span class="text-red-600">*</span>
                </label>

                <select id="sex"
                        name="sex"
                        required
                        class="{{ $inputClass }}">

                    <option value="">Select Sex</option>

                    <option value="M"
                        @selected(old('sex', $student?->sex) === 'M')>
                        M
                    </option>

                    <option value="F"
                        @selected(old('sex', $student?->sex) === 'F')>
                        F
                    </option>
                </select>

                @error('sex')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            <!-- BIRTH DATE -->
            <div>
                <label for="birth_date" class="{{ $labelClass }}">
                    Birth Date <span class="text-red-600">*</span>
                </label>

                <input type="date"
                       id="birth_date"
                       name="birth_date"
                       required
                       max="{{ now()->toDateString() }}"
                       value="{{ old('birth_date', $student?->birth_date?->format('Y-m-d')) }}"
                       class="{{ $inputClass }}">

                @error('birth_date')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            <!-- CALCULATED AGE -->
            <div>
                <label for="calculated_age"
                       class="{{ $labelClass }}">
                    Age as of First Friday of June
                </label>

                <input type="text"
                       id="calculated_age"
                       readonly
                       data-cutoff="{{ $cutoff->toDateString() }}"
                       placeholder="Automatically calculated"
                       class="{{ $inputClass }} bg-slate-50">

                <p class="mt-1 text-xs text-slate-400">
                    Reference:
                    {{ $cutoff->format('F j, Y') }}
                </p>
            </div>

        </div>
    </section>


    <!-- OTHER INFORMATION GROUPS -->
    @foreach($groups as $group)

        <section class="rounded-2xl border border-slate-200
                        bg-white p-6 shadow-sm">

            <h2 class="mb-6 flex items-center gap-3
                       text-lg font-bold text-slate-900">

                <i data-lucide="{{ $group['icon'] }}"
                   class="h-5 w-5 text-teal-600"></i>

                {{ $group['title'] }}
            </h2>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                @foreach($group['fields'] as [$field, $label, $max])

                    <div>
                        <label for="{{ $field }}"
                               class="{{ $labelClass }}">
                            {{ $label }}
                        </label>

                        <input type="text"
                               id="{{ $field }}"
                               name="{{ $field }}"
                               maxlength="{{ $max }}"
                               value="{{ old($field, $student?->{$field}) }}"
                               class="{{ $inputClass }}">

                        @error($field)
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                @endforeach

            </div>
        </section>

    @endforeach


    <!-- BUTTONS -->
    <div class="flex flex-wrap items-center gap-3">

        <button type="submit"
                class="inline-flex cursor-pointer items-center
                       gap-2 rounded-xl bg-teal-600
                       px-6 py-3 text-sm font-bold
                       text-white hover:bg-teal-700">

            <i data-lucide="save" class="h-4 w-4"></i>

            {{ $editing ? 'Save Changes' : 'Save Student' }}
        </button>

        <a href="{{ $editing
                ? route('teacher.students.show', [$section, $student])
                : route('teacher.students.index', $section) }}"
           class="rounded-xl border border-slate-200
                  bg-white px-6 py-3 text-sm font-semibold
                  text-slate-600 hover:bg-slate-50">

            Cancel
        </a>

    </div>

</form>


<!-- AUTOMATIC AGE CALCULATION -->
@push('scripts')

<script>
(() => {

    const birthDate = document.getElementById('birth_date');
    const ageOutput = document.getElementById('calculated_age');

    if (!birthDate || !ageOutput) return;

    function calculateAge() {

        if (!birthDate.value) {
            ageOutput.value = '';
            return;
        }

        const birth = birthDate.value.split('-').map(Number);
        const cutoff = ageOutput.dataset.cutoff.split('-').map(Number);

        let age = cutoff[0] - birth[0];

        if (
            cutoff[1] < birth[1] ||
            (cutoff[1] === birth[1] && cutoff[2] < birth[2])
        ) {
            age--;
        }

        ageOutput.value = age >= 0
            ? `${age} years old`
            : 'Not yet born on cutoff';
    }

    birthDate.addEventListener('input', calculateAge);
    birthDate.addEventListener('change', calculateAge);

    calculateAge();

})();
</script>

@endpush
