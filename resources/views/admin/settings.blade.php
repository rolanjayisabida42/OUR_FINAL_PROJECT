
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Account Settings | SHS Forms</title>

    <!-- TAILWIND CSS V4 -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- INTER FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- LUCIDE ICONS -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            color: #bfdbfe;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.10);
            color: #ffffff;
        }

        .nav-active {
            background: rgba(255, 255, 255, 0.16);
            color: white;
            font-weight: 600;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            outline: none;
            font-size: 14px;
            transition: 0.2s;
        }

        .form-input:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13,148,136,0.12);
        }

        .form-input:disabled {
            background: #f8fafc;
            color: #64748b;
            cursor: not-allowed;
        }
    </style>
</head>

<body class="bg-[#f5f7fb] text-slate-800">

    <!-- MOBILE OVERLAY -->
    <div id="sidebarOverlay"
         class="fixed inset-0 z-40 hidden bg-slate-950/50 lg:hidden">
    </div>


    <!-- SIDEBAR -->
    <x-admin.sidebar />

    <!-- MAIN CONTENT -->
    <div class="min-h-screen lg:ml-70">

        <!-- TOP HEADER -->
        <header class="sticky top-0 z-30
                       flex h-18 items-center justify-between
                       border-b border-slate-200
                       bg-white/95 px-5 backdrop-blur
                       lg:px-8">

            <div class="flex items-center gap-4">

                <button id="openSidebar"
                        type="button"
                        aria-label="Open sidebar"
                        class="rounded-lg p-2 hover:bg-slate-100
                               lg:hidden">

                    <i data-lucide="menu"
                       class="h-6 w-6"></i>
                </button>

                <div>
                    <p class="text-xs text-slate-400">
                        Admin / Account Settings
                    </p>

                    <h2 class="text-lg font-bold text-slate-800">
                        Settings
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-3">

                <span class="hidden rounded-full
                             bg-emerald-50 px-3 py-1.5
                             text-xs font-semibold
                             text-emerald-700 sm:inline-flex">

                    <span class="mr-2 h-2 w-2 rounded-full
                                 bg-emerald-500"></span>

                    Account Active
                </span>

                <div class="flex h-10 w-10 items-center
                            justify-center rounded-full
                            bg-[#142b61] font-semibold
                            text-white">
                    AD
                </div>
            </div>
        </header>


        <!-- PAGE CONTENT -->
        <main class="mx-auto max-w-7xl space-y-7
                     px-5 py-8 lg:px-8">

            <!-- PAGE HEADING -->
            <div>
                <h1 class="text-2xl font-extrabold
                           tracking-tight text-slate-900
                           md:text-3xl">
                    Account Settings
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Manage your personal information,
                    account security, and preferences.
                </p>
            </div>


            <!-- PROFILE BANNER -->
            <section class="relative overflow-hidden
                            rounded-2xl bg-[#142b61]
                            p-6 text-white shadow-sm
                            md:p-8">

                <div class="absolute -right-16 -top-20
                            h-64 w-64 rounded-full
                            bg-white/5"></div>

                <div class="absolute -bottom-24 right-24
                            h-48 w-48 rounded-full
                            bg-teal-400/10"></div>

                <div class="relative flex flex-col
                            items-start gap-5 sm:flex-row
                            sm:items-center">

                    <div class="flex h-20 w-20 shrink-0
                                items-center justify-center
                                rounded-2xl border-4
                                border-white/15 bg-teal-600
                                text-2xl font-extrabold">
                        AD
                    </div>

                    <div class="min-w-0 flex-1">

                        <div class="flex flex-wrap
                                    items-center gap-3">

                            <h2 class="break-words text-2xl
                                       font-bold">
                                {{ auth()->user()->name }}
                            </h2>

                            <span class="rounded-full
                                         bg-teal-500/20
                                         px-3 py-1 text-xs
                                         font-semibold text-teal-200">
                                Administrator
                            </span>
                        </div>

                        <p class="mt-2 break-all
                                  text-sm text-blue-200">
                            {{ auth()->user()->email }}
                        </p>

                        <p class="mt-3 text-xs
                                  text-blue-200/80">
                            SHS Forms Management System
                        </p>
                    </div>

                    <div class="rounded-xl border
                                border-white/15
                                bg-white/10 px-4 py-3">

                        <div class="flex items-center gap-2">
                            <i data-lucide="shield-check"
                               class="h-5 w-5 text-teal-300"></i>

                            <span class="text-sm font-semibold">
                                Verified Role
                            </span>
                        </div>

                        <p class="mt-1 text-xs text-blue-200">
                            System Administrator
                        </p>
                    </div>
                </div>
            </section>


            <!-- SETTINGS LAYOUT -->
            <div class="grid grid-cols-1 gap-7
                        lg:grid-cols-[240px_minmax(0,1fr)]">

                <!-- SECTION NAVIGATION -->
                <div class="lg:sticky lg:top-26
                            lg:self-start">

                    <div class="rounded-2xl border
                                border-slate-200 bg-white
                                p-3 shadow-sm">

                        <p class="px-3 py-3 text-xs
                                  font-bold uppercase
                                  tracking-wider text-slate-400">
                            Settings Menu
                        </p>

                        <a href="#profile"
                           class="flex items-center gap-3
                                  rounded-xl bg-teal-50
                                  px-3 py-3 text-sm
                                  font-semibold text-teal-700">

                            <i data-lucide="user-round"
                               class="h-5 w-5"></i>

                            Profile Information
                        </a>

                        <a href="#security"
                           class="flex items-center gap-3
                                  rounded-xl px-3 py-3
                                  text-sm text-slate-600
                                  hover:bg-slate-50">

                            <i data-lucide="lock-keyhole"
                               class="h-5 w-5"></i>

                            Password & Security
                        </a>

                        <a href="#account-details"
                           class="flex items-center gap-3
                                  rounded-xl px-3 py-3
                                  text-sm text-slate-600
                                  hover:bg-slate-50">

                            <i data-lucide="info"
                               class="h-5 w-5"></i>

                            Account Details
                        </a>
                    </div>
                </div>


                <!-- RIGHT CONTENT -->
                <div class="min-w-0 space-y-7">

                    <!-- PROFILE INFORMATION -->
                    
<!-- PROFILE INFORMATION -->
<section id="profile"
         class="scroll-mt-26 overflow-hidden
                rounded-2xl border
                border-slate-200 bg-white
                shadow-sm">

    <!-- HEADER -->
    <div class="flex items-center gap-4
                border-b border-slate-100
                px-6 py-5">

        <div class="flex h-11 w-11
                    items-center justify-center
                    rounded-xl bg-blue-50
                    text-blue-600">

            <i data-lucide="user-round"
               class="h-5 w-5"></i>
        </div>

        <div>
            <h3 class="font-bold text-slate-800">
                Profile Information
            </h3>
        </div>
    </div>

    
                    <!-- PROFILE INFORMATION -->
                    <section id="profile"
                            class="scroll-mt-26 overflow-hidden
                                    rounded-2xl border
                                    border-slate-200 bg-white
                                    shadow-sm">

                        <!-- PROFILE UPDATE FORM -->
                        <form action="{{ route('admin.settings.update') }}"
                            method="POST">

                            @csrf

                            <div class="space-y-5 p-6">

                                <!-- SUCCESS MESSAGE -->
                                @if (session('success'))
                                    <div class="rounded-xl border
                                                border-green-200 bg-green-50
                                                px-4 py-3 text-sm text-green-700">

                                        {{ session('success') }}

                                    </div>
                                @endif


                                <!-- FULL NAME -->
                                <div>
                                    <label for="name"
                                        class="mb-2 block text-sm
                                                font-semibold text-slate-700">
                                        Full Name
                                    </label>

                                    <input id="name"
                                        name="name"
                                        type="text"
                                        required
                                        class="form-input"
                                        value="{{ old('name', auth()->user()->name) }}"
                                        autocomplete="name">

                                    @error('name')
                                        <p class="mt-2 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>


                                <!-- EMAIL ADDRESS -->
                                <div>
                                    <label for="email"
                                        class="mb-2 block text-sm
                                                font-semibold text-slate-700">
                                        Email Address
                                    </label>

                                    <div class="relative">

                                        <i data-lucide="mail"
                                        class="absolute left-4 top-1/2
                                                h-4 w-4 -translate-y-1/2
                                                text-slate-400"></i>

                                        <input id="email"
                                            name="email"
                                            type="email"
                                            required
                                            class="form-input pl-11"
                                            value="{{ old('email', auth()->user()->email) }}"
                                            autocomplete="email">
                                    </div>

                                    @error('email')
                                        <p class="mt-2 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>


                                <!-- ACCOUNT ROLE -->
                                <div>
                                    <label for="role"
                                        class="mb-2 block text-sm
                                                font-semibold text-slate-700">
                                        Account Role
                                    </label>

                                    <input id="role"
                                        type="text"
                                        value="System Administrator"
                                        class="form-input"
                                        disabled>

                                    <p class="mt-2 text-xs text-slate-400">
                                        Your role cannot be changed
                                        from this page.
                                    </p>
                                </div>


                                <!-- SAVE CHANGES -->
                                <div class="flex justify-end
                                            border-t border-slate-100
                                            pt-5">

                                    <button type="submit"
                                            class="flex cursor-pointer
                                                items-center gap-2
                                                rounded-xl bg-teal-600
                                                px-5 py-3 text-sm
                                                font-semibold text-white
                                                transition-colors
                                                hover:bg-teal-700">

                                        <i data-lucide="save"
                                        class="h-4 w-4"></i>

                                        Save Changes
                                    </button>
                                </div>

                            </div>

                        </form>

                    </section>



                    <!-- PASSWORD & SECURITY -->
                   
                    <!-- PASSWORD & SECURITY -->
                    <section id="security"
                            class="scroll-mt-26 overflow-hidden
                                    rounded-2xl border
                                    border-slate-200 bg-white
                                    shadow-sm">

                        <!-- HEADER -->
                        <div class="flex items-center gap-4
                                    border-b border-slate-100 px-6 py-5">

                            <div class="flex h-11 w-11 items-center
                                        justify-center rounded-xl
                                        bg-amber-50 text-amber-600">

                                <i data-lucide="lock-keyhole"
                                class="h-5 w-5"></i>
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-800">
                                    Password & Security
                                </h3>

                                <p class="mt-1 text-xs text-slate-500">
                                    Manage your account password.
                                </p>
                            </div>
                        </div>


                        <!-- UPDATE PASSWORD FORM -->
                        <form action="{{ route('admin.settings.password.update') }}"
                            method="POST">

                            @csrf

                            <div class="space-y-5 p-6">

                                <!-- SUCCESS MESSAGE -->
                                @if (session('password_success'))
                                    <div class="flex items-center gap-3
                                                rounded-xl border border-green-200
                                                bg-green-50 px-4 py-3
                                                text-sm text-green-700">

                                        <i data-lucide="check-circle"
                                        class="h-5 w-5"></i>

                                        {{ session('password_success') }}
                                    </div>
                                @endif


                                <!-- CURRENT PASSWORD -->
                                <div>
                                    <label for="currentPassword"
                                        class="mb-2 block text-sm
                                                font-semibold text-slate-700">
                                        Current Password
                                    </label>

                                    <div class="relative">

                                        <input id="currentPassword"
                                            name="current_password"
                                            type="password"
                                            required
                                            autocomplete="current-password"
                                            class="form-input pr-12"
                                            placeholder="Enter current password">

                                        <button type="button"
                                                data-password-toggle="currentPassword"
                                                aria-label="Show current password"
                                                class="absolute right-4 top-1/2
                                                    -translate-y-1/2
                                                    cursor-pointer text-slate-400
                                                    hover:text-slate-600">

                                            <i data-lucide="eye"
                                            class="h-5 w-5"></i>
                                        </button>
                                    </div>

                                    @error('current_password')
                                        <p class="mt-2 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>


                                <!-- NEW PASSWORD -->
                                <div>
                                    <label for="newPassword"
                                        class="mb-2 block text-sm
                                                font-semibold text-slate-700">
                                        New Password
                                    </label>

                                    <div class="relative">

                                        <input id="newPassword"
                                            name="password"
                                            type="password"
                                            required
                                            minlength="12"
                                            autocomplete="new-password"
                                            class="form-input pr-12"
                                            placeholder="Enter new password">

                                        <button type="button"
                                                data-password-toggle="newPassword"
                                                aria-label="Show new password"
                                                class="absolute right-4 top-1/2
                                                    -translate-y-1/2
                                                    cursor-pointer text-slate-400
                                                    hover:text-slate-600">

                                            <i data-lucide="eye"
                                            class="h-5 w-5"></i>
                                        </button>
                                    </div>

                                    @error('password')
                                        <p class="mt-2 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>


                                <!-- CONFIRM PASSWORD -->
                                <div>
                                    <label for="confirmPassword"
                                        class="mb-2 block text-sm
                                                font-semibold text-slate-700">
                                        Confirm New Password
                                    </label>

                                    <div class="relative">

                                        <input id="confirmPassword"
                                            name="password_confirmation"
                                            type="password"
                                            required
                                            minlength="12"
                                            autocomplete="new-password"
                                            class="form-input pr-12"
                                            placeholder="Confirm new password">

                                        <button type="button"
                                                data-password-toggle="confirmPassword"
                                                aria-label="Show confirmation password"
                                                class="absolute right-4 top-1/2
                                                    -translate-y-1/2
                                                    cursor-pointer text-slate-400
                                                    hover:text-slate-600">

                                            <i data-lucide="eye"
                                            class="h-5 w-5"></i>
                                        </button>
                                    </div>

                                    @error('password_confirmation')
                                        <p class="mt-2 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>


                                <!-- PASSWORD NOTE -->
                                <div class="flex gap-3 rounded-xl
                                            border border-blue-100
                                            bg-blue-50 p-4">

                                    <i data-lucide="info"
                                    class="mt-0.5 h-5 w-5
                                            shrink-0 text-blue-600"></i>

                                    <p class="text-xs leading-6 text-blue-700">
                                        Use at least 12 characters with uppercase
                                        and lowercase letters, numbers, and symbols.
                                        Your new password must be different
                                        from your current password.
                                    </p>
                                </div>


                                <!-- UPDATE PASSWORD BUTTON -->
                                <div class="flex justify-end
                                            border-t border-slate-100 pt-5">

                                    <button type="submit"
                                            class="flex cursor-pointer items-center
                                                gap-2 rounded-xl bg-[#142b61]
                                                px-5 py-3 text-sm font-semibold
                                                text-white transition-colors
                                                hover:bg-[#1d3c7a]">

                                        <i data-lucide="shield-check"
                                        class="h-4 w-4"></i>

                                        Update Password
                                    </button>
                                </div>

                            </div>
                        </form>

                    </section>


                    <!-- ACCOUNT DETAILS -->
                    <section id="account-details"
                             class="scroll-mt-26 overflow-hidden
                                    rounded-2xl border
                                    border-slate-200 bg-white
                                    shadow-sm">

                        <div class="flex items-center gap-4
                                    border-b border-slate-100
                                    px-6 py-5">

                            <div class="flex h-11 w-11
                                        items-center justify-center
                                        rounded-xl bg-emerald-50
                                        text-emerald-600">

                                <i data-lucide="badge-check"
                                   class="h-5 w-5"></i>
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-800">
                                    Account Details
                                </h3>

                                <p class="mt-1 text-xs text-slate-500">
                                    Your account information and status.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5
                                    p-6 sm:grid-cols-2">

                            <div class="rounded-xl bg-slate-50 p-4">

                                <p class="text-xs text-slate-500">
                                    Account ID
                                </p>

                                <p class="mt-2 text-sm font-bold">
                                    #{{ auth()->user()->id }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">

                                <p class="text-xs text-slate-500">
                                    Role
                                </p>

                                <p class="mt-2 text-sm font-bold
                                          capitalize">
                                    {{ auth()->user()->role }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">

                                <p class="text-xs text-slate-500">
                                    Account Status
                                </p>

                                <span class="mt-2 inline-flex items-center
                                             gap-2 rounded-full
                                             px-3 py-1 text-xs
                                             font-semibold
                                             {{ auth()->user()->is_active
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-red-100 text-red-700' }}">

                                    {{ auth()->user()->is_active
                                        ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">

                                <p class="text-xs text-slate-500">
                                    Date Created
                                </p>

                                <p class="mt-2 text-sm font-bold">
                                    {{ auth()->user()->created_at?->format('M d, Y') ?? 'N/A' }}
                                </p>
                            </div>

                        </div>
                    </section>

                </div>
            </div>


            <!-- FOOTER -->
            <footer class="border-t border-slate-200
                           py-6 text-center text-xs
                           text-slate-400">

                SHS Forms Management System
                &copy; {{ date('Y') }}

            </footer>

        </main>
    </div>


    <!-- PREVIEW NOTICE -->
    <div id="previewNotice"
         role="status"
         aria-live="polite"
         class="pointer-events-none fixed bottom-5 right-5
                z-60 hidden max-w-sm rounded-xl
                bg-slate-900 px-5 py-4 text-sm
                text-white shadow-xl">

        This is a frontend preview. Changes have not been saved.
    </div>


    <!-- JAVASCRIPT -->
    <script>
        lucide.createIcons();

        // SIDEBAR
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay =
            document.getElementById('sidebarOverlay');

        const openSidebar =
            document.getElementById('openSidebar');

        const closeSidebar =
            document.getElementById('closeSidebar');

        function showSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
        }

        function hideSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }

        openSidebar.addEventListener('click', showSidebar);
        closeSidebar.addEventListener('click', hideSidebar);
        sidebarOverlay.addEventListener('click', hideSidebar);


        // PASSWORD VISIBILITY
        document.querySelectorAll('[data-password-toggle]')
            .forEach(button => {

                button.addEventListener('click', function () {

                    const inputId =
                        this.dataset.passwordToggle;

                    const input =
                        document.getElementById(inputId);

                    const isHidden =
                        input.type === 'password';

                    input.type = isHidden
                        ? 'text'
                        : 'password';

                    this.setAttribute(
                        'aria-label',
                        isHidden
                            ? 'Hide password'
                            : 'Show password'
                    );

                    const icon = this.querySelector('i');

                    if (icon) {
                        icon.setAttribute(
                            'data-lucide',
                            isHidden ? 'eye-off' : 'eye'
                        );

                        lucide.createIcons();
                    }
                });
            });


        // PREVIEW NOTICE
        let noticeTimeout;

        function showPreviewNotice() {
            const notice =
                document.getElementById('previewNotice');

            notice.classList.remove('hidden');

            clearTimeout(noticeTimeout);

            noticeTimeout = setTimeout(() => {
                notice.classList.add('hidden');
            }, 3500);
        }


        // SAVE BUTTONS (FRONTEND ONLY)
        document.querySelectorAll('[data-preview-save]')
            .forEach(button => {

                button.addEventListener(
                    'click',
                    showPreviewNotice
                );
            });


        // PREFERENCE TOGGLES (FRONTEND ONLY)
        document.querySelectorAll('[data-preference-toggle]')
            .forEach(button => {

                button.addEventListener('click', function () {

                    const enabled =
                        this.getAttribute('aria-checked') === 'true';

                    const nextState = !enabled;

                    this.setAttribute(
                        'aria-checked',
                        String(nextState)
                    );

                    this.classList.toggle(
                        'bg-teal-600',
                        nextState
                    );

                    this.classList.toggle(
                        'bg-slate-300',
                        !nextState
                    );

                    const circle = this.querySelector('span');

                    circle.classList.toggle(
                        'translate-x-5',
                        nextState
                    );

                    showPreviewNotice();
                });
            });

    </script>

</body>
</html>
