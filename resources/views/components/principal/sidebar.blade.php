
@php

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED PRINCIPAL
    |--------------------------------------------------------------------------
    */

    $principal = auth()->user();

    $principalName = trim(
        (string) ($principal?->name ?? 'Principal')
    );


    /*
    |--------------------------------------------------------------------------
    | PROFILE INITIALS
    |--------------------------------------------------------------------------
    */

    $principalInitials = collect(
        preg_split('/\s+/u', $principalName)
    )
        ->filter()
        ->take(2)
        ->map(function ($name) {
            return mb_strtoupper(
                mb_substr($name, 0, 1)
            );
        })
        ->implode('');


    /*
    |--------------------------------------------------------------------------
    | ACTIVE NAVIGATION STATES
    |--------------------------------------------------------------------------
    */

    // Dashboard
    $isDashboard = request()->routeIs(
        'principal.dashboard'
    );


    // Submission Settings
    $isSubmissionSettings = request()->routeIs(
        'principal.submissions.index',
        'principal.submissions.update'
    );


    // Review Submissions Index
    $isReviewIndex = request()->routeIs(
        'principal.submissions.review.index'
    );


    // Review Submission Details
    $isReviewDetails = request()->routeIs(
        'principal.submissions.review.show'
    );


    // Return Queue
    $isReturnQueue = request()->routeIs(
        'principal.submissions.review.return-queue'
    );


    // Return Correction Page
    $isReturnPage = request()->routeIs(
        'principal.submissions.review.return-page'
    );


    /*
    |--------------------------------------------------------------------------
    | SUBMISSION STATUS FILTER
    |--------------------------------------------------------------------------
    */

    $reviewStatus = strtolower(
        (string) request()->query('status', '')
    );


    // Approved Submissions
    $isApprovedPage =
        $isReviewIndex &&
        $reviewStatus === 'approved';


    // Returned Submissions
    $isReturnedPage =
        $isReviewIndex &&
        $reviewStatus === 'returned';


    // General Review Submissions
    $isReviewSubmissions =
        (
            $isReviewIndex &&
            !$isApprovedPage &&
            !$isReturnedPage
        )
        ||
        $isReviewDetails;


    // Return Submissions
    $isReturnSubmissions =
        $isReturnQueue ||
        $isReturnPage;


    /*
    |--------------------------------------------------------------------------
    | TEACHER PROGRESS MONITORING
    |--------------------------------------------------------------------------
    |
    | Teacher Progress now handles:
    |
    | - Teacher submission tracking
    | - Approved requirements
    | - Returned requirements
    | - Missing requirements
    | - Classroom progress
    |
    */

    $isTeacherProgress = request()->routeIs(
        'principal.teacher-progress.index'
    );


    /*
    |--------------------------------------------------------------------------
    | ACCOUNT SETTINGS
    |--------------------------------------------------------------------------
    */

    $isAccountSettings = request()->routeIs(
        'principal.settings',
        'principal.settings.*'
    );


    /*
    |--------------------------------------------------------------------------
    | NAVIGATION DESIGN
    |--------------------------------------------------------------------------
    */

    $navBase = '
        group relative flex w-full
        items-center gap-3
        rounded-xl px-4 py-3
        text-[13px] font-semibold
        transition-all duration-200
    ';

    $navActive = '
        bg-teal-500/20
        text-white
        ring-1 ring-teal-400/20
        shadow-sm
    ';

    $navInactive = '
        text-blue-100/80
        hover:bg-white/10
        hover:text-white
    ';


    /*
    |--------------------------------------------------------------------------
    | NAVIGATION GROUPS
    |--------------------------------------------------------------------------
    */

    $navigation = [

        /*
        |--------------------------------------------------------------------------
        | OVERVIEW
        |--------------------------------------------------------------------------
        */

        [
            'heading' => 'Overview',

            'items' => [

                [
                    'label' => 'Dashboard',
                    'icon' => 'layout-dashboard',
                    'url' => route(
                        'principal.dashboard'
                    ),
                    'active' => $isDashboard,
                ],

            ],
        ],


        /*
        |--------------------------------------------------------------------------
        | SUBMISSION MANAGEMENT
        |--------------------------------------------------------------------------
        */

        [
            'heading' => 'Submission Management',

            'items' => [

                // Submission Settings
                [
                    'label' => 'Submission Settings',
                    'icon' => 'calendar-cog',
                    'url' => route(
                        'principal.submissions.index'
                    ),
                    'active' => $isSubmissionSettings,
                ],


                // Review Submissions
                [
                    'label' => 'Review Submissions',
                    'icon' => 'inbox',
                    'url' => route(
                        'principal.submissions.review.index'
                    ),
                    'active' => $isReviewSubmissions,
                ],


                // Return Submissions
                [
                    'label' => 'Return Submissions',
                    'icon' => 'rotate-ccw',
                    'url' => route(
                        'principal.submissions.review.return-queue'
                    ),
                    'active' => $isReturnSubmissions,
                ],


                // Approved Submissions
                [
                    'label' => 'Approved Submissions',
                    'icon' => 'check-circle-2',
                    'url' => route(
                        'principal.submissions.review.index',
                        ['status' => 'approved']
                    ),
                    'active' => $isApprovedPage,
                ],


                // Returned Submissions
                [
                    'label' => 'Returned Submissions',
                    'icon' => 'archive-restore',
                    'url' => route(
                        'principal.submissions.review.index',
                        ['status' => 'returned']
                    ),
                    'active' => $isReturnedPage,
                ],

            ],
        ],


        /*
        |--------------------------------------------------------------------------
        | MONITORING
        |--------------------------------------------------------------------------
        |
        | Only Teacher Progress is needed.
        |
        | The Teacher Progress page already displays
        | missing, returned, approved, and pending
        | requirements.
        |
        */

        [
            'heading' => 'Monitoring',

            'items' => [

                [
                    'label' => 'Teacher Progress',
                    'icon' => 'chart-no-axes-column',
                    'url' => route(
                        'principal.teacher-progress.index'
                    ),
                    'active' => $isTeacherProgress,
                ],

            ],
        ],


        /*
        |--------------------------------------------------------------------------
        | GENERAL
        |--------------------------------------------------------------------------
        */

        [
            'heading' => 'General',

            'items' => [

                [
                    'label' => 'Account Settings',
                    'icon' => 'settings',
                    'url' => route(
                        'principal.settings'
                    ),
                    'active' => $isAccountSettings,
                ],

            ],
        ],

    ];

@endphp


<!-- ==========================================
     PRINCIPAL SIDEBAR
=========================================== -->

<aside id="sidebar"
       aria-label="Principal navigation"
       class="fixed inset-y-0 left-0 z-50
              flex w-72 -translate-x-full
              flex-col overflow-hidden
              bg-[#142b61] text-white
              shadow-2xl
              transition-transform duration-300
              ease-in-out
              lg:translate-x-0">


    <!-- ======================================
         SIDEBAR BRAND
    ======================================= -->

    <div class="flex h-20 shrink-0
                items-center gap-3
                border-b border-white/10
                px-6">


        <!-- BRAND ICON -->

        <div class="flex h-11 w-11 shrink-0
                    items-center justify-center
                    rounded-xl bg-white/10
                    ring-1 ring-white/10">

            <i data-lucide="graduation-cap"
               class="h-6 w-6 text-teal-300">
            </i>

        </div>


        <!-- BRAND INFORMATION -->

        <div class="min-w-0 flex-1">

            <h1 class="truncate text-lg
                       font-extrabold tracking-tight
                       text-white">

                SHS Forms

            </h1>

            <p class="mt-0.5 text-xs text-blue-200">

                Principal Portal

            </p>

        </div>


        <!-- MOBILE CLOSE BUTTON -->

        <button id="closeSidebar"
                type="button"
                aria-label="Close sidebar"
                class="flex h-9 w-9 shrink-0
                       items-center justify-center
                       rounded-lg text-blue-200
                       transition
                       hover:bg-white/10
                       hover:text-white
                       lg:hidden">

            <i data-lucide="x"
               class="h-5 w-5">
            </i>

        </button>

    </div>


    <!-- ======================================
         MAIN NAVIGATION
    ======================================= -->

    <nav class="min-h-0 flex-1
                space-y-7 overflow-y-auto
                px-4 py-6
                [scrollbar-width:thin]
                [scrollbar-color:#48618c_transparent]">


        @foreach($navigation as $group)

            <div>


                <!-- GROUP HEADING -->

                <p class="mb-3 px-4
                          text-[10px] font-bold
                          uppercase tracking-widest
                          text-blue-300/70">

                    {{ $group['heading'] }}

                </p>


                <!-- NAVIGATION ITEMS -->

                <div class="space-y-1">

                    @foreach($group['items'] as $item)

                        @php

                            $isActive = $item['active'];

                        @endphp


                        <a href="{{ $item['url'] }}"

                           @if($isActive)
                               aria-current="page"
                           @endif

                           class="{{ $navBase }}
                                  {{ $isActive
                                      ? $navActive
                                      : $navInactive
                                  }}">


                            <!-- ACTIVE LEFT INDICATOR -->

                            @if($isActive)

                                <span class="absolute
                                             bottom-2 left-0 top-2
                                             w-1 rounded-r-full
                                             bg-teal-400">
                                </span>

                            @endif


                            <!-- NAVIGATION ICON -->

                            <i data-lucide="{{ $item['icon'] }}"
                               class="h-5 w-5 shrink-0
                                      transition-colors

                                      {{ $isActive
                                          ? 'text-teal-300'
                                          : 'text-blue-200/75 group-hover:text-teal-300'
                                      }}">
                            </i>


                            <!-- NAVIGATION LABEL -->

                            <span class="min-w-0 flex-1">

                                {{ $item['label'] }}

                            </span>


                            <!-- RIGHT INDICATOR -->

                            @if($isActive)

                                <span class="h-2 w-2
                                             shrink-0
                                             rounded-full
                                             bg-teal-300">
                                </span>

                            @else

                                <i data-lucide="chevron-right"
                                   class="h-4 w-4 shrink-0
                                          text-blue-300/40
                                          transition-transform
                                          duration-200
                                          group-hover:translate-x-1
                                          group-hover:text-blue-200">
                                </i>

                            @endif

                        </a>

                    @endforeach

                </div>

            </div>

        @endforeach

    </nav>


    <!-- ======================================
         PRINCIPAL PROFILE & LOGOUT
    ======================================= -->

    <div class="shrink-0
                border-t border-white/10
                bg-[#102653] p-4">


        <!-- PRINCIPAL PROFILE -->

        <div class="flex items-center gap-3
                    rounded-2xl
                    border border-white/10
                    bg-white/5 p-3">


            <!-- PROFILE AVATAR -->

            <div class="flex h-11 w-11 shrink-0
                        items-center justify-center
                        rounded-xl
                        bg-teal-400/20
                        text-sm font-extrabold
                        text-teal-100
                        ring-1 ring-teal-300/15">

                {{ $principalInitials ?: 'PR' }}

            </div>


            <!-- PROFILE INFORMATION -->

            <div class="min-w-0 flex-1">

                <p class="truncate text-sm
                          font-bold text-white">

                    {{ $principalName }}

                </p>


                <div class="mt-1 flex
                            items-center gap-1.5">

                    <!-- ACCOUNT STATUS -->

                    <span class="h-1.5 w-1.5
                                 rounded-full
                                 bg-emerald-400">
                    </span>


                    <p class="text-[11px]
                              text-blue-200">

                        Principal Account

                    </p>

                </div>

            </div>


            <!-- PROFILE ICON -->

            <i data-lucide="user-round-check"
               class="h-4 w-4 shrink-0
                      text-teal-300">
            </i>

        </div>


        <!-- ==================================
             LOGOUT FORM
        =================================== -->

        <form action="{{ route('logout') }}"
              method="POST"
              class="mt-3">

            @csrf


            <button type="submit"
                    class="group flex w-full
                           cursor-pointer
                           items-center justify-center
                           gap-3 rounded-xl
                           border border-red-400/20
                           bg-red-500/15
                           px-4 py-3
                           text-sm font-bold
                           text-red-100
                           transition-all duration-200
                           hover:border-red-400/40
                           hover:bg-red-600
                           hover:text-white">


                <!-- LOGOUT ICON -->

                <i data-lucide="log-out"
                   class="h-[18px] w-[18px]">
                </i>


                <!-- LOGOUT LABEL -->

                <span>

                    Logout Account

                </span>


                <!-- ARROW -->

                <i data-lucide="arrow-right"
                   class="h-4 w-4 opacity-50
                          transition-transform
                          group-hover:translate-x-1">
                </i>

            </button>

        </form>

    </div>

</aside>
