
@php
    $headerTeacher = auth()->user();

    $headerInitials = collect(
        preg_split('/\s+/u', trim($headerTeacher->name ?? 'Teacher'))
    )
        ->filter()
        ->take(2)
        ->map(fn ($name) => mb_strtoupper(mb_substr($name, 0, 1)))
        ->implode('');
@endphp

<!-- ==========================================
     TEACHER HEADER
=========================================== -->

<header class="sticky top-0 z-30 flex h-20
               items-center justify-between
               border-b border-slate-200
               bg-white px-5 lg:px-8">

    <!-- LEFT SIDE -->

    <div class="flex min-w-0 items-center gap-4">

        <!-- MOBILE SIDEBAR -->

        <button id="openSidebar"
                type="button"
                aria-label="Open sidebar"
                aria-expanded="false"
                class="rounded-lg p-2 text-slate-600
                       hover:bg-slate-100 lg:hidden">

            <i data-lucide="menu" class="h-6 w-6"></i>

        </button>

        <!-- PAGE TITLE -->

        <div class="min-w-0">

            <h2 class="truncate text-lg font-bold
                       text-slate-900 sm:text-xl">

                @yield('page_heading', 'Teacher Dashboard')

            </h2>

            <p class="hidden text-xs text-slate-500 sm:block">
                SHS Forms Management System
            </p>

        </div>

    </div>


    <!-- RIGHT SIDE -->

    <div class="flex shrink-0 items-center gap-3 sm:gap-4">

        <!-- ==================================
             NOTIFICATIONS
        =================================== -->

        <div class="relative" id="notificationWrapper">

            <!-- NOTIFICATION BELL -->

            <button id="notificationBtn"
                    type="button"
                    aria-label="Open notifications"
                    aria-expanded="false"
                    aria-controls="notificationPanel"
                    class="relative flex h-11 w-11
                           items-center justify-center
                           rounded-xl text-slate-600
                           transition hover:bg-slate-100">

                <i data-lucide="bell"
                   class="h-5 w-5"></i>

                <!-- UNREAD COUNT -->

                <span id="notificationBadge"
                      class="absolute -right-1 -top-1
                             hidden min-w-5 items-center
                             justify-center rounded-full
                             bg-red-500 px-1.5 py-0.5
                             text-[10px] font-extrabold
                             leading-none text-white
                             ring-2 ring-white">
                    0
                </span>

            </button>


            <!-- ==================================
                 NOTIFICATION DROPDOWN
            =================================== -->

            <div id="notificationPanel"
                 class="absolute right-0 top-full z-50
                        mt-3 hidden w-80
                        max-w-[calc(100vw-2rem)]
                        overflow-hidden rounded-2xl
                        border border-slate-200
                        bg-white shadow-2xl sm:w-96">

                <!-- PANEL HEADER -->

                <div class="border-b border-slate-100 p-5">

                    <div class="flex items-center
                                justify-between gap-3">

                        <div>

                            <h3 class="text-base font-extrabold
                                       text-slate-900">

                                Notifications

                            </h3>

                            <p id="notificationSubtitle"
                               class="mt-1 text-xs text-slate-500">

                                Checking for updates...

                            </p>

                        </div>

                        <div class="flex h-10 w-10
                                    items-center justify-center
                                    rounded-xl bg-teal-50">

                            <i data-lucide="bell-ring"
                               class="h-5 w-5 text-teal-600"></i>

                        </div>

                    </div>

                    <!-- MARK ALL AS READ -->

                    <button id="markAllReadBtn"
                            type="button"
                            class="mt-4 inline-flex
                                   items-center gap-2
                                   text-xs font-bold text-teal-700
                                   transition hover:text-teal-900">

                        <i data-lucide="check-check"
                           class="h-4 w-4"></i>

                        Mark all as read

                    </button>

                </div>


                <!-- NOTIFICATION LIST -->

                <div id="notificationList"
                     aria-live="polite"
                     class="max-h-[420px]
                            overflow-y-auto">

                    <div class="px-5 py-10 text-center">

                        <p class="text-sm text-slate-500">
                            Loading notifications...
                        </p>

                    </div>

                </div>


                <!-- ERROR MESSAGE -->

                <p id="notificationError"
                   role="alert"
                   class="hidden border-t border-red-100
                          bg-red-50 px-4 py-3
                          text-xs text-red-700">
                </p>


                <!-- PANEL FOOTER -->

                <div class="flex items-center
                            justify-between gap-3
                            border-t border-slate-100
                            bg-slate-50 px-5 py-3">

                    <span class="text-[11px] text-slate-500">
                        Latest 20 notifications
                    </span>

                    <button id="refreshNotifications"
                            type="button"
                            class="inline-flex items-center
                                   gap-1.5 text-xs font-bold
                                   text-teal-700 hover:text-teal-900">

                        <i data-lucide="refresh-cw"
                           class="h-3.5 w-3.5"></i>

                        Refresh

                    </button>

                </div>

            </div>

        </div>


        <!-- ==================================
             TEACHER PROFILE
        =================================== -->

        <div class="hidden text-right sm:block">

            <p class="max-w-44 truncate
                      text-sm font-semibold
                      text-slate-900">

                {{ $headerTeacher->name }}

            </p>

            <p class="text-xs text-slate-500">
                Teacher Account
            </p>

        </div>

        <div class="flex h-10 w-10 shrink-0
                    items-center justify-center
                    rounded-full bg-blue-100
                    text-sm font-bold text-blue-700">

            {{ $headerInitials ?: 'TC' }}

        </div>

    </div>

</header>


<!-- ==========================================
     NOTIFICATION JAVASCRIPT
=========================================== -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    const btn = document.getElementById('notificationBtn');
    const panel = document.getElementById('notificationPanel');
    const wrapper = document.getElementById('notificationWrapper');

    const badge = document.getElementById('notificationBadge');
    const subtitle = document.getElementById('notificationSubtitle');
    const list = document.getElementById('notificationList');
    const errorBox = document.getElementById('notificationError');

    const markAllBtn = document.getElementById('markAllReadBtn');
    const refreshBtn = document.getElementById('refreshNotifications');

    if (!btn || !panel || !list) {
        return;
    }

    const urls = {
        index: @json(route('teacher.notifications.index')),

        read: @json(
            route('teacher.notifications.read', [
                'notification' => '__ID__'
            ])
        ),

        readAll: @json(route('teacher.notifications.read-all'))
    };

    const csrfToken = @json(csrf_token());

    let items = [];
    let loading = false;


    // =========================================
    // ERROR HANDLING
    // =========================================

    function showError(message) {
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
    }

    function clearError() {
        errorBox.textContent = '';
        errorBox.classList.add('hidden');
    }


    // =========================================
    // HELPER: CREATE ICON
    // =========================================

    function makeIcon(name, colorClass) {

        const icon = document.createElement('i');

        icon.setAttribute('data-lucide', name);

        icon.className =
            'h-5 w-5 shrink-0 ' + colorClass;

        return icon;
    }


    // =========================================
    // NOTIFICATION TYPES
    // =========================================

    function getStyle(kind) {

        const styles = {

            approved: {
                icon: 'check-circle-2',
                color: 'text-emerald-600',
                bg: 'bg-emerald-50'
            },

            returned: {
                icon: 'alert-triangle',
                color: 'text-amber-600',
                bg: 'bg-amber-50'
            },

            submitted: {
                icon: 'clipboard-check',
                color: 'text-blue-600',
                bg: 'bg-blue-50'
            },

            resubmitted: {
                icon: 'refresh-cw',
                color: 'text-teal-600',
                bg: 'bg-teal-50'
            },

            deadline: {
                icon: 'calendar-clock',
                color: 'text-amber-600',
                bg: 'bg-amber-50'
            },

            overdue: {
                icon: 'alert-circle',
                color: 'text-red-600',
                bg: 'bg-red-50'
            },

            period: {
                icon: 'calendar-days',
                color: 'text-indigo-600',
                bg: 'bg-indigo-50'
            },

            principal_submission: {
                icon: 'file-check-2',
                color: 'text-blue-600',
                bg: 'bg-blue-50'
            }
        };

        return styles[kind] || {
            icon: 'info',
            color: 'text-slate-600',
            bg: 'bg-slate-100'
        };
    }


    // =========================================
    // UPDATE BELL BADGE
    // =========================================

    function updateBadge(count) {

        const unread = Number(count) || 0;

        badge.textContent =
            unread > 99 ? '99+' : String(unread);

        badge.classList.toggle('hidden', unread === 0);
        badge.classList.toggle('flex', unread > 0);

        subtitle.textContent =
            unread === 0
                ? 'You are all caught up'
                : unread + ' unread notification' +
                  (unread === 1 ? '' : 's');

        markAllBtn.disabled = unread === 0;

        markAllBtn.classList.toggle(
            'opacity-40',
            unread === 0
        );
    }


    // =========================================
    // RENDER NOTIFICATIONS
    // =========================================

    function renderNotifications(notifications) {

        list.replaceChildren();

        if (!notifications.length) {

            const empty = document.createElement('div');

            empty.className =
                'flex flex-col items-center ' +
                'justify-center px-5 py-12 text-center';

            const icon = document.createElement('div');

            icon.className =
                'mb-3 flex h-14 w-14 items-center ' +
                'justify-center rounded-2xl bg-slate-100';

            icon.appendChild(
                makeIcon('bell-off', 'text-slate-400')
            );

            const title = document.createElement('p');

            title.className =
                'text-sm font-bold text-slate-700';

            title.textContent = 'No notifications yet';

            const description = document.createElement('p');

            description.className =
                'mt-2 text-xs leading-5 text-slate-500';

            description.textContent =
                'Submission updates and deadline reminders ' +
                'will appear here.';

            empty.append(icon, title, description);

            list.appendChild(empty);

            if (window.lucide) {
                window.lucide.createIcons();
            }

            return;
        }


        notifications.forEach(function (item) {

            const style = getStyle(item.kind);

            const row = document.createElement('button');

            row.type = 'button';
            row.dataset.id = item.id;

            row.className =
                'flex w-full items-start gap-3 ' +
                'border-b border-slate-100 px-5 py-4 ' +
                'text-left transition hover:bg-slate-50 ' +
                (item.read ? 'bg-white' : 'bg-teal-50/40');


            // ICON BACKGROUND

            const iconBox = document.createElement('div');

            iconBox.className =
                'flex h-10 w-10 shrink-0 items-center ' +
                'justify-center rounded-xl ' + style.bg;

            iconBox.appendChild(
                makeIcon(style.icon, style.color)
            );


            // TEXT CONTENT

            const content = document.createElement('div');

            content.className = 'min-w-0 flex-1';

            const title = document.createElement('p');

            title.className =
                'text-sm font-bold text-slate-900';

            title.textContent = item.title;


            const message = document.createElement('p');

            message.className =
                'mt-1 text-xs leading-5 text-slate-600';

            message.textContent = item.message;


            const date = document.createElement('p');

            date.className =
                'mt-2 text-[11px] text-slate-400';

            date.textContent = item.time || item.date || '';


            content.append(title, message, date);

            row.append(iconBox, content);


            // UNREAD INDICATOR

            if (!item.read) {

                const dot = document.createElement('span');

                dot.className =
                    'mt-2 h-2 w-2 shrink-0 ' +
                    'rounded-full bg-teal-500';

                row.appendChild(dot);
            }

            list.appendChild(row);
        });

        if (window.lucide) {
            window.lucide.createIcons();
        }
    }


    // =========================================
    // FETCH NOTIFICATIONS
    // =========================================

    async function loadNotifications() {

        if (loading) {
            return;
        }

        loading = true;
        clearError();

        try {

            const response = await fetch(urls.index, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(
                    'Unable to load notifications.'
                );
            }

            const data = await response.json();

            items = data.notifications || [];

            updateBadge(data.unread_count);

            renderNotifications(items);

        } catch (error) {

            if (!panel.classList.contains('hidden')) {

                showError(
                    'Could not refresh notifications. Try again.'
                );
            }

        } finally {

            loading = false;
        }
    }


    // =========================================
    // SEND SECURE POST REQUEST
    // =========================================

    async function postRequest(url) {

        const response = await fetch(url, {

            method: 'POST',

            credentials: 'same-origin',

            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }

        });

        if (!response.ok) {
            throw new Error('Request failed.');
        }

        return response.json();
    }


    // =========================================
    // OPEN/CLOSE NOTIFICATIONS
    // =========================================

    btn.addEventListener('click', function () {

        const opening = panel.classList.contains('hidden');

        panel.classList.toggle('hidden', !opening);

        btn.setAttribute(
            'aria-expanded',
            opening ? 'true' : 'false'
        );

        if (opening) {
            loadNotifications();
        }
    });


    document.addEventListener('click', function (event) {

        if (!wrapper.contains(event.target)) {

            panel.classList.add('hidden');

            btn.setAttribute('aria-expanded', 'false');
        }
    });


    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {

            panel.classList.add('hidden');

            btn.setAttribute('aria-expanded', 'false');
        }
    });


    // =========================================
    // MARK ONE NOTIFICATION AS READ
    // =========================================

    list.addEventListener('click', async function (event) {

        const row = event.target.closest('button[data-id]');

        if (!row) {
            return;
        }

        const item = items.find(
            notification => notification.id === row.dataset.id
        );

        if (!item) {
            return;
        }

        row.disabled = true;

        try {

            const readUrl = urls.read.replace(
                '__ID__',
                encodeURIComponent(item.id)
            );

            await postRequest(readUrl);

            // Only navigate to a safe internal Teacher page.
            if (item.url) {

                const destination = new URL(
                    item.url,
                    window.location.origin
                );

                if (
                    destination.origin === window.location.origin &&
                    destination.pathname.startsWith('/teacher/')
                ) {

                    window.location.assign(destination.href);
                    return;
                }
            }

            await loadNotifications();

        } catch (error) {

            showError(
                'Could not open the notification. Try again.'
            );

        } finally {

            row.disabled = false;
        }
    });


    // =========================================
    // MARK ALL AS READ
    // =========================================

    markAllBtn.addEventListener('click', async function () {

        markAllBtn.disabled = true;

        try {

            await postRequest(urls.readAll);

            await loadNotifications();

        } catch (error) {

            showError(
                'Unable to mark notifications as read.'
            );

        } finally {

            markAllBtn.disabled = false;
        }
    });


    // =========================================
    // MANUAL REFRESH
    // =========================================

    refreshBtn.addEventListener(
        'click',
        loadNotifications
    );


    // =========================================
    // AUTOMATIC REFRESH EVERY 30 SECONDS
    // =========================================

    loadNotifications();

    setInterval(function () {

        if (!document.hidden) {
            loadNotifications();
        }

    }, 30000);

});
</script>
