
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login | SHS Forms Management</title>

    <!-- Tailwind CSS v4 -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect"
          href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50">

<div class="min-h-screen lg:grid lg:grid-cols-2">

    <!-- LEFT PANEL -->
    <section class="relative hidden lg:flex flex-col
                    justify-between overflow-hidden
                    bg-[#142b61] p-12 xl:p-16 text-white">

        <!-- Decorative backgrounds -->
        <div class="absolute -top-24 -right-24
                    h-96 w-96 rounded-full
                    bg-blue-400/10 blur-3xl"></div>

        <div class="absolute -bottom-32 -left-24
                    h-96 w-96 rounded-full
                    bg-indigo-400/10 blur-3xl"></div>

        <!-- Branding -->
        <div class="relative z-10 flex items-center gap-3">

            <div class="flex h-12 w-12 items-center
                        justify-center rounded-xl
                        border border-white/20 bg-white/10">

                <i data-lucide="graduation-cap"
                   class="h-7 w-7"></i>

            </div>

            <div>
                <h1 class="text-xl font-bold tracking-tight">
                    SHS Forms
                </h1>
                <p class="text-xs text-blue-200">
                    Management System
                </p>
            </div>

        </div>

        <!-- Main content -->
        <div class="relative z-10 max-w-lg">

            <div class="mb-8 inline-flex items-center
                        gap-2 rounded-full border
                        border-white/15 bg-white/10
                        px-4 py-2 text-xs text-blue-100">

                <i data-lucide="sparkles"
                   class="h-4 w-4"></i>

                Smarter School Records Management

            </div>

            <h2 class="text-4xl xl:text-5xl
                       font-extrabold leading-tight
                       tracking-tight">

                School records,
                <span class="text-blue-300">
                    simplified.
                </span>

            </h2>

            <p class="mt-6 max-w-md text-base
                      leading-8 text-blue-100/80">

                Manage academic records, generate official
                school forms, and streamline document
                submissions in one secure platform.

            </p>

            <!-- Feature highlights -->
            <div class="mt-12 space-y-6">

                <div class="flex items-start gap-4">

                    <div class="flex h-11 w-11 shrink-0
                                items-center justify-center
                                rounded-xl bg-white/10">

                        <i data-lucide="file-spreadsheet"
                           class="h-5 w-5 text-blue-200"></i>

                    </div>

                    <div>
                        <h3 class="text-sm font-semibold">
                            Automated School Forms
                        </h3>
                        <p class="mt-1 text-sm text-blue-100/70">
                            Generate master sheets, student cards,
                            and SF Excel documents.
                        </p>
                    </div>

                </div>

                <div class="flex items-start gap-4">

                    <div class="flex h-11 w-11 shrink-0
                                items-center justify-center
                                rounded-xl bg-white/10">

                        <i data-lucide="clipboard-check"
                           class="h-5 w-5 text-blue-200"></i>

                    </div>

                    <div>
                        <h3 class="text-sm font-semibold">
                            Submission Monitoring
                        </h3>
                        <p class="mt-1 text-sm text-blue-100/70">
                            Track requirements, deadlines,
                            and document approvals.
                        </p>
                    </div>

                </div>

                <div class="flex items-start gap-4">

                    <div class="flex h-11 w-11 shrink-0
                                items-center justify-center
                                rounded-xl bg-white/10">

                        <i data-lucide="shield-check"
                           class="h-5 w-5 text-blue-200"></i>

                    </div>

                    <div>
                        <h3 class="text-sm font-semibold">
                            Role-Based Access
                        </h3>
                        <p class="mt-1 text-sm text-blue-100/70">
                            Dedicated workspaces for administrators,
                            teachers, and the principal.
                        </p>
                    </div>

                </div>

            </div>

        </div>

        <!-- Footer -->
        <div class="relative z-10 flex items-center
                    justify-between text-xs text-blue-200/70">

            <span>© {{ date('Y') }} SHS Forms Management</span>

            <span>Secure Academic Portal</span>

        </div>

    </section>


    <!-- RIGHT LOGIN PANEL -->
    <main class="flex min-h-screen items-center
                 justify-center px-6 py-12
                 sm:px-10 lg:px-16">

        <div class="w-full max-w-md">

            <!-- Mobile branding -->
            <div class="mb-12 flex items-center
                        gap-3 lg:hidden">

                <div class="flex h-11 w-11 items-center
                            justify-center rounded-xl
                            bg-blue-900 text-white">

                    <i data-lucide="graduation-cap"
                       class="h-6 w-6"></i>

                </div>

                <div>
                    <h1 class="font-bold text-slate-900">
                        SHS Forms
                    </h1>

                    <p class="text-xs text-slate-500">
                        Management System
                    </p>
                </div>

            </div>

            <!-- Heading -->
            <div class="mb-9">

                <div class="mb-5 flex h-12 w-12
                            items-center justify-center
                            rounded-2xl bg-blue-50
                            text-blue-700">

                    <i data-lucide="lock-keyhole"
                       class="h-6 w-6"></i>

                </div>

                <h2 class="text-3xl font-bold
                           tracking-tight text-slate-900">
                    Welcome back
                </h2>

                <p class="mt-3 text-sm leading-6
                          text-slate-500">
                    Enter your account credentials to
                    access your school management portal.
                </p>

            </div>


            
<!-- Login Form -->
<form action="{{ route('login.submit') }}"
      method="POST"
      class="space-y-5">

    @csrf

    <!-- Login Error -->
    @if ($errors->any())
        <div role="alert"
             class="flex items-start gap-3
                    rounded-xl border border-red-200
                    bg-red-50 p-4 text-sm text-red-700">

            <i data-lucide="circle-alert"
               class="mt-0.5 h-5 w-5 shrink-0"></i>

            <span>{{ $errors->first() }}</span>
        </div>
    @endif


    <!-- Email -->
    <div>
        <label for="email"
               class="mb-2 block text-sm
                      font-semibold text-slate-700">
            Email address
        </label>

        <div class="relative">

            <i data-lucide="mail"
               class="pointer-events-none
                      absolute left-4 top-1/2
                      h-5 w-5 -translate-y-1/2
                      text-slate-400"></i>

            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Enter your email"
                autocomplete="username"
                required
                class="h-13 w-full rounded-xl
                       border border-slate-200
                       bg-white py-3.5 pl-12 pr-4
                       text-sm text-slate-900
                       outline-none transition
                       placeholder:text-slate-400
                       focus:border-blue-500
                       focus:ring-4
                       focus:ring-blue-500/10"
            >
        </div>
    </div>


    <!-- Password -->
    <div>
        <label for="password"
               class="mb-2 block text-sm
                      font-semibold text-slate-700">
            Password
        </label>

        <div class="relative">

            <i data-lucide="lock"
               class="pointer-events-none
                      absolute left-4 top-1/2
                      h-5 w-5 -translate-y-1/2
                      text-slate-400"></i>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                autocomplete="current-password"
                required
                class="h-13 w-full rounded-xl
                       border border-slate-200
                       bg-white py-3.5 pl-12 pr-12
                       text-sm text-slate-900
                       outline-none transition
                       placeholder:text-slate-400
                       focus:border-blue-500
                       focus:ring-4
                       focus:ring-blue-500/10"
            >

            <button
                type="button"
                id="togglePassword"
                aria-label="Show password"
                aria-pressed="false"
                class="absolute right-4 top-1/2
                       -translate-y-1/2
                       text-slate-400 transition
                       hover:text-slate-700">

                <i data-lucide="eye"
                   id="eyeIcon"
                   class="h-5 w-5"></i>
            </button>
        </div>
    </div>


    <!-- Remember Me -->
    <div class="flex items-center justify-between">

        <label class="flex cursor-pointer
                      items-center gap-2.5">

            <input
                type="checkbox"
                name="remember"
                value="1"
                class="h-4 w-4 rounded
                       border-slate-300
                       accent-blue-700"
            >

            <span class="text-sm text-slate-600">
                Remember me
            </span>
        </label>
    </div>


    <!-- Submit Button -->
    <button
        type="submit"
        class="flex h-13 w-full cursor-pointer
               items-center justify-center gap-2
               rounded-xl bg-[#234eb3]
               px-5 py-3.5 text-sm
               font-semibold text-white
               shadow-lg shadow-blue-900/15
               transition hover:bg-[#193e95]
               focus:outline-none focus:ring-4
               focus:ring-blue-500/20">

        Sign in to your account

        <i data-lucide="arrow-right"
           class="h-4 w-4"></i>
    </button>

</form>



            <!-- Account information -->
            <div class="mt-9 rounded-xl border
                        border-slate-200 bg-white p-4">

                <div class="flex items-start gap-3">

                    <i data-lucide="info"
                       class="mt-0.5 h-5 w-5
                              shrink-0 text-blue-600"></i>

                    <p class="text-xs leading-6 text-slate-500">

                        Accounts are managed by the school
                        administrator. Contact your administrator
                        if you need access or assistance.

                    </p>

                </div>

            </div>


    

        </div>

    </main>

</div>


<!-- JavaScript -->
<script>
    lucide.createIcons();

    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function () {

        const isHidden = passwordInput.type === 'password';

        passwordInput.type = isHidden ? 'text' : 'password';

        togglePassword.setAttribute(
            'aria-label',
            isHidden ? 'Hide password' : 'Show password'
        );

        togglePassword.setAttribute(
            'aria-pressed',
            String(isHidden)
        );

        const icon = document.getElementById('eyeIcon');

        icon.setAttribute(
            'data-lucide',
            isHidden ? 'eye-off' : 'eye'
        );

        lucide.createIcons();
    });
</script>

</body>
</html>
