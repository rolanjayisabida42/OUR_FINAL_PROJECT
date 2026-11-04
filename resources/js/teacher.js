
document.addEventListener('DOMContentLoaded', function () {

    // LUCIDE ICONS
    if (window.lucide) {
        lucide.createIcons();
    }

    // ============================
    // MOBILE SIDEBAR
    // ============================
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const openBtn = document.getElementById('openSidebar');
    const closeBtn = document.getElementById('closeSidebar');

    function openSidebar() {
        sidebar?.classList.remove('-translate-x-full');
        overlay?.classList.remove('hidden');
        openBtn?.setAttribute('aria-expanded', 'true');
    }

    function closeSidebar() {
        sidebar?.classList.add('-translate-x-full');
        overlay?.classList.add('hidden');
        openBtn?.setAttribute('aria-expanded', 'false');
    }

    openBtn?.addEventListener('click', openSidebar);
    closeBtn?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    // CLOSE SIDEBAR WHEN NAVIGATION IS CLICKED
    document.querySelectorAll('#sidebar a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 1024) {
                closeSidebar();
            }
        });
    });

    // ============================
    // NOTIFICATION DROPDOWN
    // ============================
    const notificationBtn =
        document.getElementById('notificationBtn');

    const notificationPanel =
        document.getElementById('notificationPanel');

    notificationBtn?.addEventListener('click', function (event) {
        if (!notificationPanel) return;

        event.stopPropagation();

        notificationPanel.classList.toggle('hidden');

        notificationBtn.setAttribute(
            'aria-expanded',
            String(!notificationPanel.classList.contains('hidden'))
        );
    });

    document.addEventListener('click', function (event) {
        if (
            notificationBtn &&
            notificationPanel &&
            !notificationBtn.contains(event.target) &&
            !notificationPanel.contains(event.target)
        ) {
            notificationPanel.classList.add('hidden');
            notificationBtn.setAttribute('aria-expanded', 'false');
        }
    });

    // ============================
    // LIVE SEARCH (DASHBOARD TABLE)
    // ============================
    const classroomSearch =
        document.getElementById('classroomSearch');

    const classroomRows =
        document.querySelectorAll('.classroom-row');

    const noResults =
        document.getElementById('noClassroomResults');

    classroomSearch?.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        let visible = 0;

        classroomRows.forEach(row => {
            const matches =
                row.textContent.toLowerCase().includes(keyword);

            row.classList.toggle('hidden', !matches);

            if (matches) visible++;
        });

        noResults?.classList.toggle('hidden', visible > 0);
    });

    // ============================
    // CURRENT DATE AND YEAR
    // ============================
    const today = new Date();

    const currentDate =
        document.getElementById('currentDate');

    const currentYear =
        document.getElementById('currentYear');

    if (currentDate) {
        currentDate.textContent =
            today.toLocaleDateString('en-PH', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });
    }

    if (currentYear) {
        currentYear.textContent = today.getFullYear();
    }

    // ============================
    // ESCAPE KEY
    // ============================
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeSidebar();

            notificationPanel?.classList.add('hidden');
            notificationBtn?.setAttribute('aria-expanded', 'false');
        }
    });

});
