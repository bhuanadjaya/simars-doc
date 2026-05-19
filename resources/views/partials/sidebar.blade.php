<aside
  id="sidebar"
  class="fixed lg:sticky top-0 left-0 h-screen bg-white border-r border-gray-200 z-50 flex flex-col -translate-x-full lg:translate-x-0 w-[248px] xl:w-[264px]"
>
  <!-- Sidebar Header -->
  <div id="sidebar-header" class="h-16 flex items-center border-b border-gray-200 px-6 gap-3 transition-[padding] duration-300">
    <div class="flex items-center gap-2 overflow-hidden flex-1" id="sidebar-logo-container">
      <div class="w-8 h-8 min-w-[32px] bg-[#b42b2d] rounded-lg flex items-center justify-center text-white font-bold shrink-0">
        ID
      </div>
      <span class="font-bold text-lg text-gray-900 whitespace-nowrap" id="sidebar-brand-text">IDDS Starter</span>
    </div>
    <div class="flex items-center gap-1">
      <!-- Collapse Toggle Button -->
      <button
        type="button"
        class="hidden lg:flex ml-1 rounded-md hover:bg-gray-100 text-gray-500 transition-colors"
        onclick="toggleSidebarCollapse()"
        id="header-collapse-btn"
        aria-label="Toggle Sidebar"
      >
        <svg id="icon-collapse-header" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="4" y="4" width="16" height="16" rx="2" />
          <line x1="9" y1="4" x2="9" y2="20" />
          <path d="M14 10l-2 2l2 2" />
        </svg>
        <svg id="icon-expand-header" class="hidden" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="4" y="4" width="16" height="16" rx="2" />
          <line x1="9" y1="4" x2="9" y2="20" />
          <path d="M16 10l2 2l-2 2" />
        </svg>
      </button>
    </div>
    <button
      class="lg:hidden text-gray-500 hover:text-gray-900"
      onclick="toggleSidebar()"
    >
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 6l-12 12" />
        <path d="M6 6l12 12" />
      </svg>
    </button>
  </div>

  <!-- Sidebar Navigation -->
  <nav class="flex-1 p-4 space-y-1 overflow-y-auto overflow-x-hidden sidebar-nav">
    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-2 whitespace-nowrap" id="nav-group-title">
      Menu Utama
    </div>

    <!-- Dashboard -->
    <div class="ina-tooltip ina-tooltip--placement-right w-full">
      <button
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-gray-500 hover:bg-gray-100 hover:text-gray-900 active"
        onclick="navigateTo('dashboard')"
        data-page="dashboard"
        id="nav-dashboard"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="min-w-[20px]">
          <rect x="4" y="4" width="6" height="6" rx="1" />
          <rect x="14" y="4" width="6" height="6" rx="1" />
          <rect x="4" y="14" width="6" height="6" rx="1" />
          <rect x="14" y="14" width="6" height="6" rx="1" />
        </svg>
        <span class="whitespace-nowrap truncate">Dashboard</span>
      </button>
      <div class="ina-tooltip__content ina-tooltip__content--show-arrow hidden">
        <div class="ina-tooltip__bubble">Dashboard</div>
      </div>
    </div>

    <!-- Artikel & Berita -->
    <div class="ina-tooltip ina-tooltip--placement-right w-full">
      <button
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-gray-500 hover:bg-gray-100 hover:text-gray-900"
        onclick="navigateTo('articles')"
        data-page="articles"
        id="nav-articles"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="min-w-[20px]">
          <path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
        </svg>
        <span class="whitespace-nowrap truncate">Artikel & Berita</span>
      </button>
      <div class="ina-tooltip__content ina-tooltip__content--show-arrow hidden">
        <div class="ina-tooltip__bubble">Artikel & Berita</div>
      </div>
    </div>

    <!-- Form Pengajuan -->
    <div class="ina-tooltip ina-tooltip--placement-right w-full">
      <button
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-gray-500 hover:bg-gray-100 hover:text-gray-900"
        onclick="navigateTo('form')"
        data-page="form"
        id="nav-form"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="min-w-[20px]">
          <path d="M14 3v4a1 1 0 0 0 1 1h4" />
          <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
          <line x1="9" y1="9" x2="10" y2="9" />
          <line x1="9" y1="13" x2="15" y2="13" />
          <line x1="9" y1="17" x2="15" y2="17" />
        </svg>
        <span class="whitespace-nowrap truncate">Form Pengajuan</span>
        <span class="ml-auto ina-badge ina-badge--error ina-badge--sm">New</span>
      </button>
      <div class="ina-tooltip__content ina-tooltip__content--show-arrow hidden">
        <div class="ina-tooltip__bubble">Form Pengajuan</div>
      </div>
    </div>
  </nav>

  <!-- Sidebar Footer -->
  <div class="p-4 border-t border-gray-200 flex flex-col gap-2">
    <!-- User Profile -->
    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer w-full" id="user-profile">
      <div class="w-8 h-8 min-w-[32px] bg-[#b42b2d] text-white rounded-full flex items-center justify-center text-sm font-medium">
        AD
      </div>
      <div class="flex-1 min-w-0 flex items-center justify-between" id="user-details">
        <div class="flex-1 min-w-0 mr-2">
          <p class="text-sm font-medium text-gray-900 truncate">Admin User</p>
          <p class="text-xs text-gray-500 truncate">admin@inadigital.co.id</p>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 hover:text-red-600">
          <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
          <path d="M7 12h14l-3 -3m0 6l3 -3" />
        </svg>
      </div>
    </div>
  </div>
</aside>
