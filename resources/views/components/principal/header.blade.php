<header class="sticky top-0 z-30
                       flex h-20 items-center justify-between
                       border-b border-slate-200
                       bg-white px-5 lg:px-8">

            <div class="flex items-center gap-4">

                <button id="openSidebar"
                        type="button"
                        aria-label="Open sidebar"
                        aria-expanded="false"
                        class="rounded-lg p-2 text-slate-600
                               hover:bg-slate-100 lg:hidden">
                    <i data-lucide="menu"
                       class="h-6 w-6"></i>
                </button>

                <div>
                    <h2 class="text-lg font-bold
                               text-slate-900 sm:text-xl">
                        Principal Dashboard
                    </h2>

                    <p class="hidden text-xs
                              text-slate-500 sm:block">
                        SHS Forms Management System
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4">

                <div class="relative">

                    <button id="notificationBtn"
                            type="button"
                            aria-label="Notifications"
                            aria-expanded="false"
                            class="relative rounded-xl p-2.5
                                   text-slate-500
                                   hover:bg-slate-100">
                        <i data-lucide="bell"
                           class="h-5 w-5"></i>

                        <span id="notificationDot"
                              class="absolute right-2 top-2
                                     h-2 w-2 rounded-full
                                     bg-red-500"></span>
                    </button>

                    <div id="notificationPanel"
                         class="absolute right-0 top-13 z-50
                                hidden w-72 rounded-xl
                                border border-slate-200
                                bg-white p-5 shadow-xl">

                        <h3 class="text-sm font-bold">
                            Notifications
                        </h3>

                        <p id="notificationText"
                           class="mt-3 text-sm
                                  leading-6 text-slate-600">
                            You have pending submissions.
                        </p>

                        <a href="#submissions"
                           id="notificationLink"
                           class="mt-4 inline-block text-sm
                                  font-semibold text-blue-700">
                            View submissions
                        </a>
                    </div>
                </div>

                <div class="hidden text-right sm:block">
                    <p class="text-sm font-semibold">
                        School Principal
                    </p>
                    <p class="text-xs text-slate-500">
                        Principal Account
                    </p>
                </div>

                <div class="flex h-10 w-10 items-center
                            justify-center rounded-full
                            bg-blue-100 font-bold text-blue-700">
                    PR
                </div>
            </div>
        </header>