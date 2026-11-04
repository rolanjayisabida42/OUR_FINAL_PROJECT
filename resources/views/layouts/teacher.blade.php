
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Teacher Portal') | SHS Forms</title>

    <!-- TAILWIND CSS V4 -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- INTER FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- LUCIDE ICONS -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>

    
<style>
    body {
        font-family: 'Inter', sans-serif;
    }

    html {
        scroll-behavior: smooth;
    }

    /* SIDEBAR NAVIGATION */
    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 12px;
        color: #dbeafe;
        font-size: 14px;
        font-weight: 500;
        transition: all .2s;
    }

    .nav-item:hover {
        background: rgba(255, 255, 255, .1);
        color: white;
    }

    .nav-active {
        background: rgba(255, 255, 255, .15);
        color: white;
        font-weight: 600;
    }

    /* PREVENT SIDEBAR ICONS FROM SHRINKING */
    .nav-item svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }
</style>


    @stack('styles')
</head>

<body class="bg-[#f5f7fb] text-slate-800 antialiased">

    <!-- MOBILE OVERLAY -->
    <div id="sidebarOverlay"
         class="fixed inset-0 z-40 hidden bg-slate-950/50 lg:hidden">
    </div>

    <!-- YOUR EXISTING SIDEBAR -->
    <x-teacher.sidebar />

    <!-- MAIN CONTENT -->
    <div class="min-h-screen lg:ml-70">

        <!-- YOUR EXISTING HEADER -->
        <x-teacher.header />

        <!-- DYNAMIC PAGE CONTENT -->
        <main class="mx-auto max-w-7xl p-5 lg:p-8">
            @yield('content')
        </main>

    </div>

    <script>
        lucide.createIcons();

        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        document.getElementById('openSidebar')
            ?.addEventListener('click', () => {
                sidebar?.classList.remove('-translate-x-full');
                overlay?.classList.remove('hidden');
            });

        document.getElementById('closeSidebar')
            ?.addEventListener('click', () => {
                sidebar?.classList.add('-translate-x-full');
                overlay?.classList.add('hidden');
            });

        overlay?.addEventListener('click', () => {
            sidebar?.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    </script>

    <script src="{{ asset('js/teacher.js') }}"></script>

    @stack('scripts')

</body>
</html>



