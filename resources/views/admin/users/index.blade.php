
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Accounts | SHS Forms</title>

    <!-- TAILWIND CSS V4 -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- INTER FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- LUCIDE -->
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
            transition: 0.2s;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-active {
            background: rgba(255, 255, 255, 0.16);
            color: white;
            font-weight: 600;
        }

        .form-input {
            width: 100%;
            padding: 11px 14px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            outline: none;
            font-size: 14px;
            transition: 0.2s;
        }

        .form-input:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13,148,136,0.12);
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #0d9488;
            color: white;
            padding: 11px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-primary:hover {
            background: #0f766e;
        }
    </style>
</head>

<body class="bg-[#f5f7fb] text-slate-800">

    <!-- MOBILE OVERLAY -->
    <div id="sidebarOverlay"
         class="fixed inset-0 z-40 hidden bg-slate-950/50 lg:hidden">
    </div>


    <!-- ADMIN SIDEBAR -->
    <x-admin.sidebar />

    <!-- MAIN CONTENT -->
    <div class="min-h-screen lg:ml-70">

        <!-- TOP BAR -->
        <header class="sticky top-0 z-30
                       flex h-18 items-center justify-between
                       border-b border-slate-200
                       bg-white/95 px-5 backdrop-blur
                       lg:px-8">

            <div class="flex items-center gap-4">

                <button id="openSidebar"
                        type="button"
                        aria-label="Open sidebar"
                        class="rounded-lg p-2
                               hover:bg-slate-100 lg:hidden">

                    <i data-lucide="menu"
                       class="h-6 w-6"></i>
                </button>

                <div>
                    <p class="text-xs text-slate-400">
                        Admin / User Management
                    </p>

                    <h2 class="text-lg font-bold">
                        Manage Accounts
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-3">

                <span class="hidden rounded-full
                             bg-teal-50 px-3 py-1.5
                             text-xs font-semibold
                             text-teal-700 sm:inline-flex">

                    Administrator
                </span>

                <div class="flex h-10 w-10 items-center
                            justify-center rounded-full
                            bg-[#142b61] font-bold text-white">

                    AD
                </div>
            </div>
        </header>


        <!-- PAGE CONTENT -->
        <main class="mx-auto max-w-7xl space-y-7
                     px-5 py-8 lg:px-8">

            <!-- PAGE TITLE -->
            <div class="flex flex-col gap-4
                        sm:flex-row sm:items-center
                        sm:justify-between">

                <div>
                    <h1 class="text-2xl font-extrabold
                               tracking-tight text-slate-900
                               md:text-3xl">

                        User Account Management
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Create, update, and manage Teacher
                        and Principal accounts.
                    </p>
                </div>

                <button type="button"
                        id="openCreateModal"
                        class="btn-primary">

                    <i data-lucide="user-plus"
                       class="h-5 w-5"></i>

                    Add New Account
                </button>
            </div>


            <!-- SUCCESS MESSAGE -->
            @if (session('success'))
                <div class="flex items-center gap-3
                            rounded-xl border border-green-200
                            bg-green-50 px-5 py-4
                            text-sm font-medium text-green-700">

                    <i data-lucide="check-circle"
                       class="h-5 w-5 shrink-0"></i>

                    {{ session('success') }}
                </div>
            @endif


            <!-- UPDATE VALIDATION ERRORS -->
            @if ($errors->updateUser->any())
                <div class="rounded-xl border border-red-200
                            bg-red-50 p-4 text-sm text-red-700">

                    <p class="mb-2 font-bold">
                        Please correct the account details:
                    </p>

                    @foreach ($errors->updateUser->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif


            <!-- STATISTICS -->
            <div class="grid grid-cols-1 gap-5
                        sm:grid-cols-2 xl:grid-cols-4">

                <!-- TEACHERS -->
                <div class="rounded-2xl border border-slate-200
                            bg-white p-5 shadow-sm">

                    <div class="flex items-center justify-between">

                        <div class="flex h-12 w-12 items-center
                                    justify-center rounded-xl
                                    bg-blue-50 text-blue-600">

                            <i data-lucide="users"
                               class="h-6 w-6"></i>
                        </div>

                        <span class="text-xs text-slate-400">
                            Teaching Staff
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-extrabold">
                        {{ $stats['teachers'] }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Teacher Accounts
                    </p>
                </div>


                <!-- PRINCIPAL -->
                <div class="rounded-2xl border border-slate-200
                            bg-white p-5 shadow-sm">

                    <div class="flex items-center justify-between">

                        <div class="flex h-12 w-12 items-center
                                    justify-center rounded-xl
                                    bg-violet-50 text-violet-600">

                            <i data-lucide="graduation-cap"
                               class="h-6 w-6"></i>
                        </div>

                        <span class="text-xs text-slate-400">
                            School Leadership
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-extrabold">
                        {{ $stats['principals'] }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Principal Accounts
                    </p>
                </div>


                <!-- ACTIVE -->
                <div class="rounded-2xl border border-slate-200
                            bg-white p-5 shadow-sm">

                    <div class="flex items-center justify-between">

                        <div class="flex h-12 w-12 items-center
                                    justify-center rounded-xl
                                    bg-emerald-50 text-emerald-600">

                            <i data-lucide="user-check"
                               class="h-6 w-6"></i>
                        </div>

                        <span class="text-xs text-slate-400">
                            Enabled
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-extrabold">
                        {{ $stats['active'] }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Active Accounts
                    </p>
                </div>


                <!-- INACTIVE -->
                <div class="rounded-2xl border border-slate-200
                            bg-white p-5 shadow-sm">

                    <div class="flex items-center justify-between">

                        <div class="flex h-12 w-12 items-center
                                    justify-center rounded-xl
                                    bg-red-50 text-red-600">

                            <i data-lucide="user-x"
                               class="h-6 w-6"></i>
                        </div>

                        <span class="text-xs text-slate-400">
                            Disabled
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-extrabold">
                        {{ $stats['inactive'] }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Inactive Accounts
                    </p>
                </div>
            </div>


            <!-- ACCOUNT TABLE -->
            <section class="overflow-hidden rounded-2xl
                            border border-slate-200
                            bg-white shadow-sm">

                <!-- TABLE HEADER -->
                <div class="flex flex-col gap-4
                            border-b border-slate-100
                            px-6 py-5 sm:flex-row
                            sm:items-center sm:justify-between">

                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Registered Accounts
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Showing Teacher and Principal accounts
                        </p>
                    </div>

                    <span class="rounded-full bg-slate-100
                                 px-3 py-1.5 text-xs font-semibold
                                 text-slate-600">

                        {{ $users->total() }} Results
                    </span>
                </div>


                <!-- SEARCH & FILTER -->
                <form method="GET"
                      action="{{ route('admin.users.index') }}"
                      class="grid grid-cols-1 gap-3
                             border-b border-slate-100
                             p-5 md:grid-cols-4">

                    <!-- SEARCH -->
                    <div class="md:col-span-2">

                        <input type="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search name or email..."
                               class="form-input">
                    </div>

                    <!-- ROLE -->
                    <div>
                        <select name="role" class="form-input">
                            <option value="">All Roles</option>

                            <option value="teacher"
                                @selected(request('role') === 'teacher')>
                                Teacher
                            </option>

                            <option value="principal"
                                @selected(request('role') === 'principal')>
                                Principal
                            </option>
                        </select>
                    </div>

                    <!-- STATUS -->
                    <div>
                        <select name="status" class="form-input">
                            <option value="">All Statuses</option>

                            <option value="active"
                                @selected(request('status') === 'active')>
                                Active
                            </option>

                            <option value="inactive"
                                @selected(request('status') === 'inactive')>
                                Inactive
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center gap-3
                                md:col-span-4">

                        <button type="submit"
                                class="btn-primary">

                            <i data-lucide="search"
                               class="h-4 w-4"></i>

                            Search Accounts
                        </button>

                        <a href="{{ route('admin.users.index') }}"
                           class="rounded-xl border
                                  border-slate-200 px-4 py-3
                                  text-sm font-semibold
                                  text-slate-600 hover:bg-slate-50">

                            Reset
                        </a>
                    </div>
                </form>


                <!-- RESPONSIVE TABLE -->
                <div class="overflow-x-auto">

                    <table class="w-full min-w-210 text-left">

                        <thead class="bg-slate-50
                                      text-xs font-bold uppercase
                                      tracking-wider text-slate-500">

                            <tr>
                                <th class="px-6 py-4">Account</th>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4">Grade Access</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Created</th>
                                <th class="px-6 py-4 text-right">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            @forelse ($users as $user)

                                <tr class="hover:bg-slate-50/70">

                                    <!-- ACCOUNT -->
                                    <td class="px-6 py-4">

                                        <div class="flex items-center gap-3">

                                            <div class="flex h-11 w-11
                                                        shrink-0 items-center
                                                        justify-center
                                                        rounded-xl font-bold
                                                        {{ $user->role === 'principal'
                                                            ? 'bg-violet-100 text-violet-700'
                                                            : 'bg-blue-100 text-blue-700' }}">

                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>

                                            <div class="min-w-0">

                                                <p class="max-w-52 truncate
                                                          text-sm font-semibold
                                                          text-slate-800">

                                                    {{ $user->name }}
                                                </p>

                                                <p class="max-w-52 truncate
                                                          text-xs text-slate-500">

                                                    {{ $user->email }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>


                                    <!-- ROLE -->
                                    <td class="px-6 py-4">

                                        <span class="inline-flex rounded-full
                                                     px-3 py-1 text-xs
                                                     font-semibold
                                                     {{ $user->role === 'principal'
                                                        ? 'bg-violet-50 text-violet-700'
                                                        : 'bg-blue-50 text-blue-700' }}">

                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>


                                    <!-- GRADE ACCESS -->
                                    <td class="px-6 py-4
                                               text-sm text-slate-600">

                                        @if ($user->role === 'principal')
                                            —
                                        @elseif ($user->grade_access === 'both')
                                            Grade 11 & 12
                                        @elseif ($user->grade_access)
                                            Grade {{ $user->grade_access }}
                                        @else
                                            Not Assigned
                                        @endif
                                    </td>


                                    <!-- ACCOUNT STATUS -->
                                    <td class="px-6 py-4">

                                        <span class="inline-flex items-center
                                                     gap-2 rounded-full
                                                     px-3 py-1 text-xs
                                                     font-semibold
                                                     {{ $user->is_active
                                                        ? 'bg-emerald-50 text-emerald-700'
                                                        : 'bg-red-50 text-red-700' }}">

                                            <span class="h-1.5 w-1.5 rounded-full
                                                         {{ $user->is_active
                                                            ? 'bg-emerald-500'
                                                            : 'bg-red-500' }}">
                                            </span>

                                            {{ $user->is_active
                                                ? 'Active'
                                                : 'Inactive' }}
                                        </span>
                                    </td>


                                    <!-- CREATED DATE -->
                                    <td class="px-6 py-4
                                               text-sm text-slate-500">

                                        {{ $user->created_at?->format('M d, Y') ?? '—' }}
                                    </td>


                                    <!-- ACTIONS -->
                                    <td class="px-6 py-4">

                                        <div class="flex items-center
                                                    justify-end gap-2">

                                            <!-- EDIT ACCOUNT -->
                                            <button type="button"
                                                    data-edit-user
                                                    data-id="{{ $user->id }}"
                                                    data-edit-url="{{ route('admin.users.update', $user) }}"
                                                    data-name="{{ $user->name }}"
                                                    data-email="{{ $user->email }}"
                                                    data-role="{{ $user->role }}"
                                                    data-grade="{{ $user->grade_access }}"
                                                    class="flex cursor-pointer
                                                           items-center gap-1
                                                           rounded-lg bg-blue-50
                                                           px-3 py-2 text-xs
                                                           font-semibold text-blue-700
                                                           hover:bg-blue-100">

                                                <i data-lucide="pencil"
                                                   class="h-4 w-4"></i>

                                                Edit
                                            </button>


                                            <!-- ACCOUNT STATUS FORM -->
                                            <form method="POST"
                                                  action="{{ route('admin.users.status', $user) }}"
                                                  onsubmit="return confirm('Are you sure you want to change this account status?')">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="cursor-pointer
                                                               rounded-lg px-3 py-2
                                                               text-xs font-semibold
                                                               {{ $user->is_active
                                                                  ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                                                  : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">

                                                    {{ $user->is_active
                                                        ? 'Deactivate'
                                                        : 'Activate' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6"
                                        class="px-6 py-16 text-center">

                                        <div class="mx-auto flex
                                                    h-14 w-14 items-center
                                                    justify-center rounded-2xl
                                                    bg-slate-100 text-slate-400">

                                            <i data-lucide="users"
                                               class="h-7 w-7"></i>
                                        </div>

                                        <h4 class="mt-4 font-bold text-slate-700">
                                            No accounts found
                                        </h4>

                                        <p class="mt-2 text-sm text-slate-500">
                                            Try another search or add a new account.
                                        </p>
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>
                    </table>
                </div>


                <!-- PAGINATION -->
                @if ($users->hasPages())
                    <div class="border-t border-slate-100
                                px-6 py-5">

                        {{ $users->links() }}
                    </div>
                @endif

            </section>


            <!-- FOOTER -->
            <footer class="border-t border-slate-200
                           py-6 text-center text-xs text-slate-400">

                SHS Forms Management System
                &copy; {{ date('Y') }}
            </footer>

        </main>
    </div>


    <!-- CREATE USER MODAL -->
    <div id="createModal"
         role="dialog"
         aria-modal="true"
         aria-labelledby="createModalTitle"
         class="fixed inset-0 z-60 hidden
                items-center justify-center
                bg-slate-950/60 p-4">

        <div class="max-h-[90vh] w-full max-w-xl
                    overflow-y-auto rounded-2xl
                    bg-white shadow-2xl">

            <!-- MODAL HEADER -->
            <div class="flex items-center justify-between
                        border-b border-slate-100
                        px-6 py-5">

                <div>
                    <h3 id="createModalTitle"
                        class="text-lg font-bold text-slate-900">

                        Create New Account
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Register a Teacher or Principal account.
                    </p>
                </div>

                <button type="button"
                        data-close-modal="createModal"
                        aria-label="Close modal"
                        class="cursor-pointer text-slate-400
                               hover:text-slate-700">

                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>


            <!-- CREATE FORM -->
            <form action="{{ route('admin.users.store') }}"
                  method="POST">

                @csrf

                <div class="space-y-5 p-6">

                    @if ($errors->createUser->any())
                        <div class="rounded-xl border
                                    border-red-200 bg-red-50
                                    p-4 text-sm text-red-700">

                            @foreach ($errors->createUser->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif


                    <!-- FULL NAME -->
                    <div>
                        <label class="form-label" for="createName">
                            Full Name
                        </label>

                        <input id="createName"
                               name="name"
                               type="text"
                               required
                               maxlength="255"
                               value="{{ old('name') }}"
                               class="form-input"
                               placeholder="Enter full name">
                    </div>


                    <!-- EMAIL -->
                    <div>
                        <label class="form-label" for="createEmail">
                            Email Address
                        </label>

                        <input id="createEmail"
                               name="email"
                               type="email"
                               required
                               value="{{ old('email') }}"
                               class="form-input"
                               placeholder="name@school.edu.ph">
                    </div>


                    <!-- ROLE -->
                    <div>
                        <label class="form-label" for="createRole">
                            Account Role
                        </label>

                        <select id="createRole"
                                name="role"
                                required
                                class="form-input">

                            <option value="teacher"
                                @selected(old('role') === 'teacher')>
                                Teacher
                            </option>

                            <option value="principal"
                                @selected(old('role') === 'principal')
                                @disabled($principalExists)>
                                Principal
                            </option>
                        </select>

                        @if ($principalExists)
                            <p class="mt-2 text-xs text-amber-600">
                                A Principal account already exists.
                                Only one is allowed.
                            </p>
                        @endif
                    </div>


                    <!-- GRADE ACCESS -->
                    <div id="createGradeWrap">

                        <label class="form-label"
                               for="createGrade">

                            Grade Level Access
                        </label>

                        <select id="createGrade"
                                name="grade_access"
                                class="form-input">

                            <option value="">
                                Select grade access
                            </option>

                            <option value="11"
                                @selected(old('grade_access') === '11')>
                                Grade 11
                            </option>

                            <option value="12"
                                @selected(old('grade_access') === '12')>
                                Grade 12
                            </option>

                            <option value="both"
                                @selected(old('grade_access') === 'both')>
                                Grade 11 & Grade 12
                            </option>
                        </select>
                    </div>


                    <!-- PASSWORD -->
                    <div>
                        <label class="form-label"
                               for="createPassword">
                            Temporary Password
                        </label>

                        <input id="createPassword"
                               name="password"
                               type="password"
                               required
                               minlength="12"
                               autocomplete="new-password"
                               class="form-input"
                               placeholder="Enter secure password">
                    </div>


                    <!-- CONFIRM PASSWORD -->
                    <div>
                        <label class="form-label"
                               for="createPasswordConfirm">
                            Confirm Password
                        </label>

                        <input id="createPasswordConfirm"
                               name="password_confirmation"
                               type="password"
                               required
                               minlength="12"
                               autocomplete="new-password"
                               class="form-input"
                               placeholder="Confirm password">
                    </div>


                    <div class="rounded-xl bg-blue-50 p-4
                                text-xs leading-6 text-blue-700">

                        Password must have at least 12 characters
                        with uppercase and lowercase letters,
                        numbers, and symbols.
                    </div>
                </div>


                <!-- MODAL FOOTER -->
                <div class="flex justify-end gap-3
                            border-t border-slate-100
                            px-6 py-5">

                    <button type="button"
                            data-close-modal="createModal"
                            class="cursor-pointer rounded-xl
                                   border border-slate-200
                                   px-5 py-3 text-sm font-semibold
                                   text-slate-600">

                        Cancel
                    </button>

                    <button type="submit"
                            class="btn-primary">

                        <i data-lucide="user-plus"
                           class="h-4 w-4"></i>

                        Create Account
                    </button>
                </div>

            </form>
        </div>
    </div>


    <!-- EDIT USER MODAL -->
    <div id="editModal"
         role="dialog"
         aria-modal="true"
         aria-labelledby="editModalTitle"
         class="fixed inset-0 z-60 hidden
                items-center justify-center
                bg-slate-950/60 p-4">

        <div class="max-h-[90vh] w-full max-w-xl
                    overflow-y-auto rounded-2xl
                    bg-white shadow-2xl">

            <!-- HEADER -->
            <div class="flex items-center justify-between
                        border-b border-slate-100
                        px-6 py-5">

                <div>
                    <h3 id="editModalTitle"
                        class="text-lg font-bold text-slate-900">
                        Edit Account
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Update user information or reset password.
                    </p>
                </div>

                <button type="button"
                        data-close-modal="editModal"
                        aria-label="Close modal"
                        class="cursor-pointer text-slate-400">

                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>


            <!-- EDIT FORM -->
            <form id="editForm" method="POST">

                @csrf
                @method('PUT')

                <input type="hidden"
                       id="editUserId"
                       name="_editing_user_id">

                <div class="space-y-5 p-6">

                    @if ($errors->updateUser->any())
                        <div class="rounded-xl border border-red-200
                                    bg-red-50 p-4 text-sm text-red-700">

                            @foreach ($errors->updateUser->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif


                    <!-- NAME -->
                    <div>
                        <label class="form-label" for="editName">
                            Full Name
                        </label>

                        <input id="editName"
                               name="name"
                               type="text"
                               required
                               maxlength="255"
                               class="form-input">
                    </div>


                    <!-- EMAIL -->
                    <div>
                        <label class="form-label" for="editEmail">
                            Email Address
                        </label>

                        <input id="editEmail"
                               name="email"
                               type="email"
                               required
                               class="form-input">
                    </div>


                    <!-- ROLE -->
                    <div>
                        <label class="form-label" for="editRole">
                            Account Role
                        </label>

                        <input id="editRole"
                               type="text"
                               disabled
                               class="form-input">

                        <p class="mt-2 text-xs text-slate-400">
                            Account roles cannot be changed here.
                        </p>
                    </div>


                    <!-- GRADE -->
                    <div id="editGradeWrap">

                        <label class="form-label"
                               for="editGrade">

                            Grade Level Access
                        </label>

                        <select id="editGrade"
                                name="grade_access"
                                class="form-input">

                            <option value="11">Grade 11</option>
                            <option value="12">Grade 12</option>
                            <option value="both">
                                Grade 11 & Grade 12
                            </option>
                        </select>
                    </div>


                    <!-- OPTIONAL PASSWORD RESET -->
                    <div class="border-t border-slate-100 pt-5">

                        <h4 class="text-sm font-bold text-slate-800">
                            Reset Account Password
                        </h4>

                        <p class="mt-1 text-xs text-slate-500">
                            Leave these fields blank to keep the
                            current password.
                        </p>
                    </div>


                    <div>
                        <label class="form-label"
                               for="editPassword">

                            New Password (Optional)
                        </label>

                        <input id="editPassword"
                               name="password"
                               type="password"
                               minlength="12"
                               autocomplete="new-password"
                               class="form-input"
                               placeholder="Enter new password">
                    </div>


                    <div>
                        <label class="form-label"
                               for="editPasswordConfirm">

                            Confirm New Password
                        </label>

                        <input id="editPasswordConfirm"
                               name="password_confirmation"
                               type="password"
                               minlength="12"
                               autocomplete="new-password"
                               class="form-input"
                               placeholder="Confirm new password">
                    </div>

                </div>


                <!-- FOOTER -->
                <div class="flex justify-end gap-3
                            border-t border-slate-100
                            px-6 py-5">

                    <button type="button"
                            data-close-modal="editModal"
                            class="cursor-pointer rounded-xl
                                   border border-slate-200
                                   px-5 py-3 text-sm font-semibold
                                   text-slate-600">

                        Cancel
                    </button>

                    <button type="submit"
                            class="btn-primary">

                        <i data-lucide="save"
                           class="h-4 w-4"></i>

                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- JAVASCRIPT -->
    <script>
        lucide.createIcons();

        // SIDEBAR
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }

        document.getElementById('openSidebar')
            .addEventListener('click', openSidebar);

        document.getElementById('closeSidebar')
            .addEventListener('click', closeSidebar);

        overlay.addEventListener('click', closeSidebar);


        // MODAL HELPERS
        function showModal(id) {
            const modal = document.getElementById(id);

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            document.body.classList.add('overflow-hidden');
        }

        function hideModal(id) {
            const modal = document.getElementById(id);

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            document.body.classList.remove('overflow-hidden');
        }

        document.querySelectorAll('[data-close-modal]')
            .forEach(button => {

                button.addEventListener('click', () => {
                    hideModal(button.dataset.closeModal);
                });
            });


        // CLOSE MODAL WHEN CLICKING BACKDROP
        ['createModal', 'editModal'].forEach(id => {

            document.getElementById(id)
                .addEventListener('click', function (event) {

                    if (event.target === this) {
                        hideModal(id);
                    }
                });
        });


        // CREATE ACCOUNT MODAL
        document.getElementById('openCreateModal')
            .addEventListener('click', () => {
                showModal('createModal');
            });


        const createRole = document.getElementById('createRole');
        const createGrade = document.getElementById('createGrade');
        const createGradeWrap =
            document.getElementById('createGradeWrap');

        function updateCreateGrade() {
            const isTeacher = createRole.value === 'teacher';

            createGradeWrap.classList.toggle('hidden', !isTeacher);
            createGrade.disabled = !isTeacher;
            createGrade.required = isTeacher;
        }

        createRole.addEventListener('change', updateCreateGrade);
        updateCreateGrade();


        // EDIT ACCOUNT MODAL
        const editForm = document.getElementById('editForm');
        const editName = document.getElementById('editName');
        const editEmail = document.getElementById('editEmail');
        const editRole = document.getElementById('editRole');
        const editGrade = document.getElementById('editGrade');
        const editGradeWrap =
            document.getElementById('editGradeWrap');

        function openEditAccount(button) {

            editForm.action = button.dataset.editUrl;

            document.getElementById('editUserId').value =
                button.dataset.id;

            editName.value = button.dataset.name;
            editEmail.value = button.dataset.email;

            const isTeacher = button.dataset.role === 'teacher';

            editRole.value = isTeacher
                ? 'SHS Teacher'
                : 'School Principal';

            editGradeWrap.classList.toggle('hidden', !isTeacher);
            editGrade.disabled = !isTeacher;
            editGrade.required = isTeacher;

            if (isTeacher) {
                editGrade.value = button.dataset.grade || '';
            }

            document.getElementById('editPassword').value = '';
            document.getElementById('editPasswordConfirm').value = '';

            showModal('editModal');
        }

        document.querySelectorAll('[data-edit-user]')
            .forEach(button => {

                button.addEventListener('click', () => {
                    openEditAccount(button);
                });
            });


        // REOPEN CREATE MODAL IF VALIDATION FAILS
        @if ($errors->createUser->any())
            showModal('createModal');
        @endif


        // REOPEN EDIT MODAL IF VALIDATION FAILS
        @if ($errors->updateUser->any())

            const editingUserId = @json(old('_editing_user_id'));

            const matchingButton = Array.from(
                document.querySelectorAll('[data-edit-user]')
            ).find(button => button.dataset.id === editingUserId);

            if (matchingButton) {
                openEditAccount(matchingButton);

                editName.value = @json(old('name'));
                editEmail.value = @json(old('email'));

                if (matchingButton.dataset.role === 'teacher') {
                    editGrade.value = @json(old('grade_access'));
                }
            }

        @endif


        // ESCAPE KEY
        document.addEventListener('keydown', event => {

            if (event.key === 'Escape') {
                hideModal('createModal');
                hideModal('editModal');
            }
        });

    </script>

</body>
</html>
