
<header
  class="h-20 bg-white border-b border-slate-200
         sticky top-0 z-30 px-5 sm:px-8
         flex items-center justify-between gap-4">

  <!-- LEFT -->
  <div class="flex items-center gap-3 sm:gap-4">

    <!-- MOBILE MENU -->
    <button
      type="button"
      data-sidebar-open
      aria-label="Open sidebar"
      class="lg:hidden rounded-lg p-2
             hover:bg-slate-100 cursor-pointer">

      <i data-lucide="menu"
         class="w-6 h-6"></i>
    </button>

    <div>
      <h2 class="text-base sm:text-lg
                 font-bold text-slate-900">
        Administration
      </h2>

      <p class="text-xs text-slate-500 mt-1">
        School Forms Management System
      </p>
    </div>

  </div>


  <!-- RIGHT -->
  <div class="flex items-center gap-4">

    <!-- SCHOOL YEAR -->
    <div
      class="hidden sm:flex items-center gap-2
             px-3 py-2 rounded-xl bg-teal-50
             text-teal-700 text-xs font-semibold">

      <span class="w-2 h-2 rounded-full bg-teal-500"></span>

      <span id="headerSchoolYear">
        SY 2026–2027
      </span>

    </div>

    <div class="hidden md:block h-9
                w-px bg-slate-200"></div>

    <!-- PROFILE -->
    <div class="flex items-center gap-3">

      <div class="w-10 h-10 rounded-full
                  bg-[#102C45] text-white
                  flex items-center justify-center
                  font-bold text-sm">
        AD
      </div>

      <div class="hidden md:block">

        <p class="text-sm font-semibold text-slate-800">
          Administrator
        </p>

        <p class="text-xs text-slate-500 mt-1">
          System Admin
        </p>

      </div>

    </div>

  </div>

</header>
