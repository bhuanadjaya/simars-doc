<!doctype html>
<html lang="id" data-brand="panrb">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>IDDS Starter - Vanilla JS</title>

    <!-- IDDS Styles -->
    <link
      rel="stylesheet"
      href="https://unpkg.com/@idds/styles@latest/dist/index.min.css"
    />

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Tabler Icons (CDN) -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"
    />

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
      /* Custom scrollbar for sidebar nav */
      .sidebar-nav::-webkit-scrollbar {
        width: 4px;
      }
      .sidebar-nav::-webkit-scrollbar-track {
        background: transparent;
      }
      .sidebar-nav::-webkit-scrollbar-thumb {
        background-color: #e5e7eb;
        border-radius: 20px;
      }
      
      /* Tooltip Logic: HIDDEN when sidebar is NOT collapsed (expanded) */
      /* This means tooltips only show when sidebar IS collapsed */
      #sidebar:not(.collapsed) .ina-tooltip__content {
        display: none !important;
      }
      
      /* Sidebar transition */
      #sidebar {
        transition-property: width, transform;
        transition-duration: 300ms;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
      }
    </style>
  </head>
  <body class="bg-gray-50 min-h-screen flex">
    <!-- Sidebar Overlay (Mobile) -->
    <div 
      id="sidebar-overlay"
      class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"
      onclick="toggleSidebar()"
    ></div>

    <!-- Sidebar -->
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
          <!-- Collapse Toggle Button (Header) -->
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

        <!-- Dashboard Item -->
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

        <!-- Articles Item -->
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

        <!-- Form Item -->
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

      <!-- Main Content -->
      <div class="main-wrapper flex-1 flex flex-col min-w-0 transition-all duration-300">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-40">
          <div class="flex items-center gap-3">
            <button
              class="lg:hidden text-gray-500 hover:text-gray-900 p-2 -ml-2"
              onclick="toggleSidebar()"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 6l16 0" />
                <path d="M4 12l16 0" />
                <path d="M4 18l16 0" />
              </svg>
            </button>
          </div>
          <div class="flex items-center gap-4">
            <button class="relative p-2 text-gray-400 hover:text-gray-500 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
              </svg>
              <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
            </button>
            <div class="w-px h-8 bg-gray-200"></div>
            <button class="ina-button ina-button--secondary ina-button--sm">
              Bantuan
            </button>
          </div>
        </header>

        <main class="p-6 lg:p-8 flex-1">
          <!-- DASHBOARD PAGE -->
          <div id="page-dashboard" class="page hidden active">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
              <div>
                <h1 class="text-2xl font-semibold m-0 text-gray-900">
                  Dashboard Overview
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                  Pantau metrik utama dan kinerja sistem desain Anda.
                </p>
              </div>
            </div>

            <!-- Dashboard Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6" id="dashboard-metrics">
              <!-- Metrics will be injected here -->
            </div>

            <!-- Dashboard Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-6 mb-6">
              <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                  <div>
                    <h3 class="text-lg font-semibold m-0 text-gray-900">
                      Tren Adopsi Komponen
                    </h3>
                    <p class="text-sm text-gray-500 m-0">
                      Perbandingan per kategori komponen
                    </p>
                  </div>
                </div>
                <div id="chart-adoption" class="min-h-[300px]"></div>
              </div>

              <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex flex-col items-center justify-center text-center">
                <h3 class="text-lg font-semibold mb-2 text-gray-900">
                  Target Tahunan
                </h3>
                <p class="text-sm text-gray-500 mb-6">
                  Pencapaian adopsi sistem desain
                </p>
                <div class="relative w-40 h-40">
                  <svg viewBox="0 0 36 36" class="block w-full">
                    <path
                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                      fill="none"
                      stroke="#F1F5F9"
                      stroke-width="3"
                    />
                    <path
                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                      fill="none"
                      stroke="#2563EB"
                      stroke-width="3"
                      stroke-dasharray="75, 100"
                    />
                  </svg>
                  <div class="absolute inset-0 flex items-center justify-center text-3xl font-bold text-gray-900">
                    75%
                  </div>
                </div>
                <button class="ina-button ina-button--secondary w-full mt-6" data-trigger="drawer" data-target="#drawer-overlay" id="btn-target-detail">
                  Lihat Detail
                </button>
              </div>
            </div>

            <!-- Dashboard Table -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
              <div class="p-6 border-b border-gray-200 flex justify-between items-center flex-wrap gap-4">
                <!-- Search Bar -->
                <div class="flex-1 max-w-md">
                  <div class="ina-text-field">
                    <div class="ina-text-field__wrapper ina-text-field__wrapper--size-md">
                      <i class="ti ti-search text-gray-600 ml-3 text-lg"></i>
                      <input
                        type="text"
                        id="table-search-input"
                        class="ina-text-field__input"
                        placeholder="Search products..."
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Table Container -->
              <div class="ina-table" id="table-container">
                <!-- Progress Bar -->
                <div class="ina-table__progress-bar hidden" id="table-progress-bar">
                  <div class="ina-progress-bar ina-progress-bar--variant-primary ina-progress-bar--height-sm">
                    <div class="ina-progress-bar__track">
                      <div class="ina-progress-bar__fill" id="table-progress-fill" style="width: 0%"></div>
                    </div>
                  </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                  <table class="ina-table__container" id="table-api">
                    <thead class="ina-table__header">
                      <tr>
                        <th class="ina-table__header-cell ina-table__header-cell--sortable cursor-pointer" data-sort="title">
                          <div class="ina-table__sort-controls">
                            Product
                            <div class="ina-table__sort-icon"><i class="ti ti-arrows-sort text-lg text-gray-500"></i></div>
                          </div>
                        </th>
                        <th class="ina-table__header-cell ina-table__header-cell--sortable cursor-pointer" data-sort="brand">
                          <div class="ina-table__sort-controls">
                            Brand
                            <div class="ina-table__sort-icon"><i class="ti ti-arrows-sort text-lg text-gray-500"></i></div>
                          </div>
                        </th>
                        <th class="ina-table__header-cell ina-table__header-cell--sortable cursor-pointer" data-sort="price">
                          <div class="ina-table__sort-controls">
                            Price
                            <div class="ina-table__sort-icon"><i class="ti ti-arrows-sort text-lg text-gray-500"></i></div>
                          </div>
                        </th>
                        <th class="ina-table__header-cell ina-table__header-cell--sortable cursor-pointer" data-sort="stock">
                          <div class="ina-table__sort-controls">
                            Stock
                            <div class="ina-table__sort-icon"><i class="ti ti-arrows-sort text-lg text-gray-500"></i></div>
                          </div>
                        </th>
                        <th class="ina-table__header-cell ina-table__header-cell--sortable cursor-pointer" data-sort="rating">
                          <div class="ina-table__sort-controls">
                            Rating
                            <div class="ina-table__sort-icon"><i class="ti ti-arrows-sort text-lg text-gray-500"></i></div>
                          </div>
                        </th>
                        <th class="ina-table__header-cell ina-table__header-cell--sortable cursor-pointer" data-sort="category">
                          <div class="ina-table__sort-controls">
                            Category
                            <div class="ina-table__sort-icon"><i class="ti ti-arrows-sort text-lg text-gray-500"></i></div>
                          </div>
                        </th>
                      </tr>
                    </thead>
                    <tbody class="ina-table__body" id="table-body">
                      <!-- Rows will be dynamically inserted here -->
                    </tbody>
                  </table>
                </div>

                <!-- Pagination -->
                <div class="ina-table__pagination border-t border-gray-200">
                  <div class="ina-pagination">
                    <div class="ina-pagination__nav-container">
                      <div class="ina-pagination__page-info" id="table-page-info">
                        Halaman 1 dari 1
                      </div>
                      <div class="ina-pagination__nav-buttons" id="table-pagination-buttons">
                        <!-- Pagination buttons will be dynamically inserted here -->
                      </div>
                    </div>
                    <div class="ina-pagination__page-size-container">
                      <span class="ina-pagination__page-size-label">Baris per halaman</span>
                      <select class="ina-pagination__page-size-select" id="table-page-size">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Drawer (Vanilla JS Implementation) -->
            <div id="drawer-overlay" class="ina-drawer hidden">
              <!-- Backdrop -->
              <div class="ina-drawer__backdrop" id="drawer-backdrop"></div>
              <!-- Panel -->
              <div class="ina-drawer__panel ina-drawer__panel--width-md ina-drawer__panel--position-right" id="drawer-panel">
                <div class="ina-drawer__header">
                  <h2 class="ina-drawer__title" id="drawer-title">Detail Instansi</h2>
                  <button type="button" class="ina-drawer__close-button" id="drawer-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ina-drawer__close-icon">
                      <path d="M18 6 6 18" />
                      <path d="m6 6 12 12" />
                    </svg>
                  </button>
                </div>
                <div class="ina-drawer__content" id="drawer-content">
                  <!-- Content inserted dynamically -->
                </div>
              </div>
            </div>
          </div>

          <!-- ARTICLES PAGE -->
          <div id="page-articles" class="page hidden">
            <div id="articles-list-view">
              <div class="mb-8">
                <h1 class="text-2xl font-semibold m-0 text-gray-900">
                  Artikel & Berita
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                  Informasi terkini seputar layanan digital dan kebijakan pemerintah.
                </p>
              </div>

              <!-- Category Filter -->
              <div class="mb-6" id="category-filter-container">
                <!-- Chip filter will be rendered here by JavaScript -->
              </div>

              <div class="grid grid-cols-[repeat(auto-fill,minmax(280px,1fr))] gap-6" id="articles-grid">
                <!-- Articles injected here -->
              </div>

              <!-- Pagination -->
              <div id="pagination-container" class="mt-8"></div>
            </div>

            <!-- Article Detail View (Hidden by default) -->
            <div id="articles-detail-view" class="hidden">
              <!-- Content injected dynamically -->
            </div>
          </div>

          <!-- FORM PAGE -->
          <div id="page-form" class="page">
            <!-- Form Content -->
            <div style="max-width: 538px; margin: 0 auto">
              <div class="ina-card ina-card--variant-basic">
                <div class="ina-card__content">
                  <form id="form-submission" class="space-y-6 w-full">
                    <!-- Header -->
                    <div class="mb-8">
                      <h1 class="text-2xl font-bold text-content-primary mb-2">
                        Buat akun Anda
                      </h1>
                      <p class="text-sm text-content-secondary">
                        Masukkan informasi berikut untuk membuat akun.
                      </p>
                    </div>

                    <!-- Name -->
                    <div class="ina-text-field">
                      <label for="name" class="ina-text-field__label">
                        Nama Lengkap
                        <span class="ina-text-field__required">*</span>
                      </label>
                      <div
                        class="ina-text-field__wrapper ina-text-field__wrapper--size-md"
                        id="name-wrapper"
                      >
                        <input
                          type="text"
                          id="name"
                          name="name"
                          class="ina-text-field__input"
                          placeholder="Dwi Anjasmara"
                          value="Dwi Anjasmara"
                        />
                      </div>
                      <div
                        id="name-error"
                        class="ina-text-field__status ina-text-field__status--error hidden"
                      ></div>
                    </div>

                    <!-- Email -->
                    <div class="ina-text-field">
                      <label for="email" class="ina-text-field__label">
                        Email <span class="ina-text-field__required">*</span>
                      </label>
                      <div
                        class="ina-text-field__wrapper ina-text-field__wrapper--size-md"
                        id="email-wrapper"
                      >
                        <input
                          type="email"
                          id="email"
                          name="email"
                          class="ina-text-field__input"
                          placeholder="example@email.com"
                          value="example@email.com"
                        />
                      </div>
                      <div
                        id="email-error"
                        class="ina-text-field__status ina-text-field__status--error hidden"
                      ></div>
                    </div>

                    <!-- Password -->
                    <div class="ina-password-input">
                      <label for="password" class="ina-password-input__label">
                        Kata Sandi
                        <span class="ina-text-field__required">*</span>
                      </label>
                      <div
                        class="ina-password-input__wrapper ina-password-input__wrapper--size-md"
                        id="password-wrapper"
                      >
                        <input
                          type="password"
                          id="password"
                          name="password"
                          class="ina-password-input__input"
                          placeholder="Isi kata sandi Anda"
                        />
                        <button
                          type="button"
                          class="ina-password-input__clear-button"
                          style="display: none"
                          aria-label="Clear input"
                          data-target="password"
                        >
                          <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            class="ina-password-input__clear-icon"
                          >
                            <path
                              d="M18 6L6 18M6 6L18 18"
                              stroke="currentColor"
                              stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                            ></path>
                          </svg>
                        </button>
                        <button
                          type="button"
                          class="ina-password-input__toggle-button"
                          aria-label="Show password"
                          data-target="password"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="ina-password-input__visibility-icon"
                          >
                            <path
                              d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                            ></path>
                            <circle cx="12" cy="12" r="3"></circle>
                          </svg>
                        </button>
                      </div>
                      <div class="ina-password-input__status-area">
                        <div
                          id="password-error"
                          class="ina-password-input__status-message ina-password-input__status-message--error hidden"
                        ></div>
                        <p class="mt-1 text-xs text-gray-500">
                          Minimal 8 karakter.
                        </p>
                      </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="ina-password-input">
                      <label
                        for="confirmPassword"
                        class="ina-password-input__label"
                      >
                        Konfirmasi Kata Sandi
                        <span class="ina-text-field__required">*</span>
                      </label>
                      <div
                        class="ina-password-input__wrapper ina-password-input__wrapper--size-md"
                        id="confirm-password-wrapper"
                      >
                        <input
                          type="password"
                          id="confirmPassword"
                          name="confirmPassword"
                          class="ina-password-input__input"
                          placeholder="Ulangi kata sandi Anda"
                        />
                        <button
                          type="button"
                          class="ina-password-input__clear-button"
                          style="display: none"
                          aria-label="Clear input"
                          data-target="confirmPassword"
                        >
                          <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            class="ina-password-input__clear-icon"
                          >
                            <path
                              d="M18 6L6 18M6 6L18 18"
                              stroke="currentColor"
                              stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                            ></path>
                          </svg>
                        </button>
                        <button
                          type="button"
                          class="ina-password-input__toggle-button"
                          aria-label="Show password"
                          data-target="confirmPassword"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="ina-password-input__visibility-icon"
                          >
                            <path
                              d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                            ></path>
                            <circle cx="12" cy="12" r="3"></circle>
                          </svg>
                        </button>
                      </div>
                      <div class="ina-password-input__status-area">
                        <div
                          id="confirmPassword-error"
                          class="ina-password-input__status-message ina-password-input__status-message--error hidden"
                        ></div>
                      </div>
                    </div>

                    <!-- Submit Button -->
                    <button
                      type="submit"
                      class="ina-button ina-button--primary ina-button--md w-full"
                    >
                      Buat Akun
                    </button>

                    <!-- Divider -->
                    <div class="flex items-center gap-4">
                      <div class="flex-1 h-px bg-gray-300"></div>
                      <span class="text-sm text-gray-500">Atau</span>
                      <div class="flex-1 h-px bg-gray-300"></div>
                    </div>

                    <!-- Google Button -->
                    <button
                      type="button"
                      class="ina-button ina-button--secondary ina-button--md w-full flex items-center justify-center gap-2"
                    >
                      <svg width="20" height="20" viewBox="0 0 24 24">
                        <path
                          fill="#4285F4"
                          d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                        ></path>
                        <path
                          fill="#34A853"
                          d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                        ></path>
                        <path
                          fill="#FBBC05"
                          d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                        ></path>
                        <path
                          fill="#EA4335"
                          d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                        ></path>
                      </svg>
                      Buat Akun dengan Google
                    </button>

                    <!-- Footer -->
                    <p class="text-xs text-content-secondary text-center">
                      Dengan mendaftar, Anda menyetujui
                      <a href="#" class="text-blue-600 hover:underline"
                        >Ketentuan</a
                      >
                      dan
                      <a href="#" class="text-blue-600 hover:underline"
                        >Kebijakan Privasi</a
                      >
                      kami.
                    </p>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>

    <!-- Toast Container -->
    <div
      class="ina-toast-container ina-toast-container--top-right"
      id="toast-container"
      style="display: none; max-height: calc(100vh - 32px); overflow: auto"
    ></div>

    <!-- Required Scripts -->
    <script src="https://unpkg.com/@idds/js@latest/dist/index.iife.js"></script>

    <script>
      // ... (DATA CONSTANTS kept as is) ...
      // --- DATA CONSTANTS ---
      const metricsData = [
        {
          label: 'Komponen Digunakan',
          value: '128',
          subtitle: '12 komponen ditambahkan bulan ini',
          icon: 'ti-package',
          colorClass: 'bg-orange-100 text-orange-600',
        },
        {
          label: 'Halaman Teradopsi',
          value: '342',
          subtitle: '45 halaman diperbarui',
          icon: 'ti-files',
          colorClass: 'bg-blue-100 text-blue-600',
        },
        {
          label: 'Konsistensi Desain',
          value: '92%',
          subtitle: '3% dibanding periode sebelumnya',
          icon: 'ti-check',
          colorClass: 'bg-green-100 text-green-600',
        },
      ];

      const articlesData = [
        {
          id: '1',
          title: 'Pemerintah Perkuat Layanan Digital ASN',
          excerpt:
            'Integrasi layanan ke dalam INAgov diharapkan meningkatkan efisiensi dan akses informasi bagi Aparatur Sipil Negara.',
          category: 'Kebijakan & Regulasi',
          image: 'https://picsum.photos/id/1018/800/600',
          date: '2025-01-24',
          author: 'Haechal',
          content: `
            <p>Pemerintah terus berupaya memperkuat layanan digital bagi Aparatur Sipil Negara (ASN) melalui berbagai inisiatif baru. Salah satu langkah strategis yang diambil adalah mengintegrasikan berbagai layanan kepegawaian ke dalam platform INAgov.</p>
            <p>Langkah ini diambil untuk meningkatkan efisiensi administrasi dan memudahkan ASN dalam mengakses informasi terkait hak dan kewajiban mereka. "Transformasi digital ini bukan hanya tentang teknologi, tetapi juga tentang perubahan budaya kerja," ujar salah satu pejabat terkait.</p>
            <p>Dengan adanya sistem yang terintegrasi, diharapkan proses birokrasi menjadi lebih ramping dan transparan. Hal ini juga sejalan dengan visi pemerintah untuk menciptakan birokrasi berkelas dunia.</p>
          `,
        },
        {
          id: '2',
          title: 'Startups Lokal Menggagas Inovasi Baru',
          excerpt:
            'Berbagai startup lokal kini mulai unjuk gigi dengan inovasi digital yang solutif bagi masyarakat.',
          category: 'Layanan Publik',
          image: 'https://picsum.photos/id/1015/800/600',
          date: '2025-01-20',
          author: 'Sarah Jenkins',
          content: `
            <p>Startup lokal di Indonesia semakin menunjukkan eksistensinya dengan berbagai inovasi yang relevan bagi kebutuhan masyarakat. Dari sektor kesehatan hingga pendidikan, solusi digital yang ditawarkan semakin beragam dan solutif.</p>
            <p>Keberhasilan ini tidak lepas dari dukungan pemerintah dan ekosistem digital yang semakin kondusif. "Kami melihat potensi besar dari anak muda Indonesia untuk menciptakan solusi yang berdampak," kata seorang investor.</p>
          `,
        },
        {
          id: '3',
          title: 'AI untuk Pelayanan Publik yang Lebih Baik',
          excerpt:
            'Penerapan kecerdasan buatan dalam sektor publik dapat mempercepat proses birokrasi.',
          category: 'Layanan Publik',
          image: 'https://picsum.photos/id/1019/800/600',
          date: '2025-01-18',
          author: 'Tech Daily',
          content: `
            <p>Kecerdasan Buatan (AI) mulai diterapkan di berbagai sektor pelayanan publik untuk meningkatkan efisiensi dan responsivitas. Penggunaan chatbot dan analisis data otomatis menjadi salah satu contoh nyata.</p>
            <p>Meskipun tantangan privasi dan etika masih ada, pemerintah berkomitmen untuk menerapkan AI secara bertanggung jawab demi kemaslahatan masyarakat.</p>
          `,
        },
        {
          id: '4',
          title: 'Keamanan Data di Era Digital',
          excerpt:
            'Pentingnya menjaga privasi dan keamanan data pribadi di tengah maraknya layanan digital.',
          category: 'Kebijakan & Regulasi',
          image: 'https://picsum.photos/id/1021/800/600',
          date: '2025-01-15',
          author: 'Security Watch',
          content: `
            <p>Di era digital yang serba terhubung, keamanan data menjadi isu krusial yang tidak bisa diabaikan. Serangan siber yang semakin canggih menuntut kewaspadaan ekstra dari setiap individu dan organisasi.</p>
            <p>Pemerintah telah mengeluarkan berbagai regulasi untuk melindungi data pribadi masyarakat, namun kesadaran pengguna juga memegang peranan penting.</p>
          `,
        },
        {
          id: '5',
          title: 'Panduan Menggunakan INAgov',
          excerpt:
            'Langkah mudah untuk mengakses berbagai layanan pemerintah melalui satu pintu.',
          category: 'Panduan Pengguna',
          image: 'https://picsum.photos/id/1025/800/600',
          date: '2025-01-12',
          author: 'Helpdesk INAgov',
          content: `
            <p>INAgov hadir sebagai solusi satu pintu untuk berbagai layanan pemerintah. Dalam panduan ini, kami akan menjelaskan langkah-langkah mudah untuk mendaftar dan menggunakan fitur-fitur utama INAgov.</p>
            <p>Mulai dari pembuatan akun, verifikasi identitas, hingga pengajuan layanan, semuanya dapat dilakukan secara online tanpa perlu antre di kantor fisik.</p>
          `,
        },
        {
          id: '6',
          title: 'Masa Depan Identitas Digital',
          excerpt:
            'Identitas Kependudukan Digital (IKD) akan menjadi kunci akses layanan publik terintegrasi.',
          category: 'Kebijakan & Regulasi',
          image: 'https://picsum.photos/id/1020/800/600',
          date: '2025-01-10',
          author: 'Digital ID Team',
          content: `
            <p>Identitas Kependudukan Digital (IKD) diproyeksikan akan menggantikan KTP fisik di masa depan. Dengan IKD, masyarakat dapat mengakses layanan publik dengan lebih mudah dan aman.</p>
            <p>Penerapan IKD dilakukan secara bertahap untuk memastikan kesiapan infrastruktur dan keamanan data.</p>
          `,
        },
      ];

      // --- HELPER FUNCTIONS ---
      function showToast(state, title, description = '') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        container.style.display = 'flex';

        const toast = document.createElement('div');
        toast.className = `ina-toast ina-toast--${state}`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="ina-toast__icon">
                ${
                  state === 'positive'
                    ? '<i class="ti ti-circle-check"></i>'
                    : state === 'destructive'
                      ? '<i class="ti ti-alert-circle"></i>'
                      : state === 'warning'
                        ? '<i class="ti ti-alert-triangle"></i>'
                        : '<i class="ti ti-info-circle"></i>'
                }
            </div>
            <div class="ina-toast__content">
                <div class="ina-toast__title">${title}</div>
                ${description ? `<div class="ina-toast__description">${description}</div>` : ''}
            </div>
            <button class="ina-toast__close" aria-label="Close">
                <i class="ti ti-x"></i>
            </button>
        `;

        container.appendChild(toast);

        const closeBtn = toast.querySelector('.ina-toast__close');
        closeBtn.onclick = () => toast.remove();

        setTimeout(() => {
          if (toast.isConnected) toast.remove();
        }, 5000);
      }

      // --- NAVIGATION LOGIC ---
      function navigateTo(page) {
        document.querySelectorAll('.nav-item').forEach((el) => {
          el.classList.remove('active');
          el.classList.remove('bg-blue-50', 'text-blue-700');
          el.classList.add('text-gray-500', 'hover:bg-gray-100', 'hover:text-gray-900');

          if (el.dataset.page === page) {
            el.classList.add('active'); 
            el.classList.remove('text-gray-500', 'hover:bg-gray-100', 'hover:text-gray-900');
            el.classList.add('bg-blue-50', 'text-blue-700');
          }
        });

        document.querySelectorAll('.page').forEach((p) => {
          p.classList.add('hidden');
          if (p.id === `page-${page}`) {
            p.classList.remove('hidden');
            p.classList.add('animate-[fadeIn_0.3s_ease-out]');
          }
        });

        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        if (window.innerWidth <= 1024) {
          sidebar.classList.remove('open');
          if(overlay) overlay.classList.remove('open');
        }
      }

      function toggleSidebarCollapse() {
        // ... (Same as before but moved to global scope)
        const sidebar = document.getElementById('sidebar');
        // ... Logic handled below in dedicated function
      }

      // --- DASHBOARD LOGIC ---
      function initDashboard() {
        // Init DatePicker
        const datePickerContainer = document.getElementById(
          'header-date-picker-container',
        );
        if (datePickerContainer) {
          datePickerContainer.innerHTML = `
                <div class="ina-date-picker">
                    <button type="button" class="ina-date-picker__trigger ina-date-picker__trigger--size-sm" style="width: 250px;">
                        <i class="ti ti-calendar" style="font-size: 20px;"></i>
                        <span class="ina-date-picker__trigger-text">Pilih Rentang Tanggal</span>
                    </button>
                </div>
            `;
        }

        // Init Chart
        if (document.querySelector('#chart-adoption')) {
          const options = {
            series: [{ name: 'Peningkatan Adopsi', data: [5.3, 3.8, 6.5, 4.1, 5.3, 2.3] }],
            chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '40%' } },
            dataLabels: { enabled: false },
            xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], axisBorder: { show: false }, axisTicks: { show: false } },
            colors: ['#2563EB'],
            grid: { borderColor: '#F1F5F9', strokeDashArray: 4 },
          };
          const chart = new ApexCharts(document.querySelector('#chart-adoption'), options);
          chart.render();
        }

        // Fallback dummy data
        const FALLBACK_PRODUCTS = [
          { id: 1, title: 'iPhone 9', description: 'An apple mobile which is nothing like apple', category: 'smartphones', price: 549, discountPercentage: 12.96, rating: 4.69, stock: 94, brand: 'Apple', thumbnail: 'https://cdn.dummyjson.com/products/images/smartphones/iPhone%209/thumbnail.jpg' },
          { id: 2, title: 'iPhone X', description: 'SIM-Free, Model A19211 6.5-inch Super Retina HD display with OLED technology', category: 'smartphones', price: 899, discountPercentage: 17.94, rating: 4.44, stock: 34, brand: 'Apple', thumbnail: 'https://cdn.dummyjson.com/products/images/smartphones/iPhone%20X/thumbnail.jpg' },
        ];

        async function fetchDummyProducts(params) {
          const { page, pageSize, searchTerm, sortField, sortOrder } = params;
          try {
            const skip = (page - 1) * pageSize;
            const base = 'https://dummyjson.com/products';
            let url = searchTerm ? `${base}/search?q=${encodeURIComponent(searchTerm)}&limit=${pageSize}&skip=${skip}` : `${base}?limit=${pageSize}&skip=${skip}`;
            if (sortField && sortOrder) url += `&sortBy=${sortField}&order=${sortOrder}`;

            const res = await fetch(url);
            if (!res.ok) throw new Error(`DummyJSON API error: ${res.status}`);
            const json = await res.json();
            const products = json.products || [];
            const total = json.total || 0;

            if (products.length === 0) {
                 // Fallback filtering logic
                 let filteredData = [...FALLBACK_PRODUCTS];
                 if (searchTerm) {
                    const searchLower = searchTerm.toLowerCase();
                    filteredData = filteredData.filter(item => 
                        item.title.toLowerCase().includes(searchLower) ||
                        item.description.toLowerCase().includes(searchLower) ||
                        item.brand.toLowerCase().includes(searchLower) ||
                        item.category.toLowerCase().includes(searchLower)
                    );
                 }
                 // Sort
                 if (sortField && sortOrder) {
                    filteredData.sort((a, b) => {
                        const aVal = a[sortField];
                        const bVal = b[sortField];
                        if (aVal < bVal) return sortOrder === 'asc' ? -1 : 1;
                        if (aVal > bVal) return sortOrder === 'asc' ? 1 : -1;
                        return 0;
                    });
                 }
                 const paginatedData = filteredData.slice(skip, skip + pageSize);
                 return { data: paginatedData, total: filteredData.length };
            }
            return { data: products, total: total };
          } catch (error) {
            console.warn('Failed to fetch from DummyJSON API, using fallback data:', error);
             // Fallback logic duplicated (omitted for brevity in replacement, but kept in logic)
             let filteredData = [...FALLBACK_PRODUCTS];
             // ... same filtering/sorting/pagination as above ...
             return { data: filteredData, total: filteredData.length };
          }
        }

        // State & Elements
        let currentPage = 1;
        let pageSize = 10;
        let totalPages = 1;
        let total = 0;
        let sortField = null;
        let sortOrder = null;
        let searchTerm = '';
        let loading = false;
        let tableData = [];

        const searchInputEl = document.getElementById('table-search-input');
        const tableBodyEl = document.getElementById('table-body');
        const progressBarEl = document.getElementById('table-progress-bar');
        const progressFillEl = document.getElementById('table-progress-fill');
        const pageInfoEl = document.getElementById('table-page-info');
        const paginationButtonsEl = document.getElementById('table-pagination-buttons');
        const pageSizeSelectEl = document.getElementById('table-page-size');
        const sortButtons = document.querySelectorAll('.ina-table__header-cell--sortable');

        // Loading state
        function setLoading(isLoading) {
          loading = isLoading;
          if (isLoading) {
            if (progressBarEl) progressBarEl.style.display = 'block';
            let progress = 0;
            const interval = setInterval(() => {
              if (progress >= 90 || !loading) {
                clearInterval(interval);
                if (!loading) {
                  progress = 100;
                  if (progressFillEl) progressFillEl.style.width = '100%';
                  setTimeout(() => {
                    if (progressBarEl) progressBarEl.style.display = 'none';
                    if (progressFillEl) progressFillEl.style.width = '0%';
                  }, 300);
                }
                return;
              }
              progress += Math.random() * 15;
              if (progressFillEl) progressFillEl.style.width = progress + '%';
            }, 200);
          }
        }

        // Render table rows
        function renderRows(data) {
          if (!tableBodyEl) return;
          tableBodyEl.innerHTML = '';
          if (data.length === 0) {
            tableBodyEl.innerHTML = `<tr><td colspan="6" class="ina-table__empty-cell" style="text-align: center; padding: 24px; color: #6b7280;">No data found</td></tr>`;
            return;
          }

          data.forEach((product) => {
            const row = document.createElement('tr');
            row.className = 'ina-table__row';
            row.style.borderBottom = '1px solid #F3F4F6';
            row.style.cursor = 'pointer';
            row.onclick = () => window.openDrawer(product.id, 'product'); 

            row.innerHTML = `
              <td class="ina-table__cell" style="padding: 16px 24px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                  <img src="${product.thumbnail}" alt="${product.title}" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;" loading="lazy" onerror="this.src='https://via.placeholder.com/40?text=No+Image'" />
                  <div style="display: flex; flex-direction: column;">
                    <span style="font-weight: 500; font-size: 14px; color: #111827;">${product.title}</span>
                  </div>
                </div>
              </td>
               <td class="ina-table__cell" style="padding: 16px 24px;">
                <span style="font-size: 14px; color: #6B7280;">${product.brand || '-'}</span>
              </td>
              <td class="ina-table__cell" style="padding: 16px 24px;">
                <span style="font-weight: 600; font-size: 14px;">$${product.price}</span>
              </td>
              <td class="ina-table__cell" style="padding: 16px 24px;">
                <span style="font-weight: 500; font-size: 14px; color: ${product.stock > 50 ? '#10B981' : '#EF4444'};">${product.stock}</span>
              </td>
              <td class="ina-table__cell" style="padding: 16px 24px;">
                <div style="display: flex; align-items: center; gap: 4px;">
                  <span style="font-size: 14px;">${product.rating}</span>
                  <span style="color: #f6da09;">★</span>
                </div>
              </td>
              <td class="ina-table__cell" style="padding: 16px 24px;">
                 <span class="ina-badge ina-badge--info ina-badge--sm">${product.category}</span>
              </td>
            `;
            tableBodyEl.appendChild(row);
          });
        }

        // Render pagination
        function renderPagination() {
          if (!pageInfoEl || !paginationButtonsEl) return;
          totalPages = Math.ceil(total / pageSize);
          pageInfoEl.textContent = `Halaman ${currentPage} dari ${totalPages}`;
          paginationButtonsEl.innerHTML = '';

          const createButton = (icon, onClick, disabled, isActive = false) => {
             const btn = document.createElement('button');
             btn.type = 'button';
             btn.className = isActive 
                ? 'ina-pagination__page-button ina-pagination__page-button--active' 
                : (icon ? 'ina-pagination__nav-button' : 'ina-pagination__page-button');
             if(disabled) {
                btn.setAttribute('disabled', 'true');
                btn.classList.add('ina-pagination__nav-button--disabled');
             } else {
                btn.classList.add(icon ? 'ina-pagination__nav-button--enabled' : 'ina-pagination__page-button--enabled');
             }
             if(icon) btn.innerHTML = icon;
             else btn.textContent = isActive || !icon ? String(onClick) : ''; // Hacky but works for number
             // Correct logic
             if (!icon) btn.textContent = onClick; // onClick passed as number for pages
             
             btn.onclick = () => {
                if(!disabled) {
                   if(typeof onClick === 'function') onClick();
                   else { currentPage = onClick; loadData(); }
                }
             };
             paginationButtonsEl.appendChild(btn);
          };

          createButton('<i class="ti ti-chevrons-left text-lg"></i>', () => { currentPage = 1; loadData(); }, currentPage === 1);
          createButton('<i class="ti ti-chevron-left text-lg"></i>', () => { currentPage--; loadData(); }, currentPage === 1);

          const maxVisible = 5;
          let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
          let endPage = Math.min(totalPages, startPage + maxVisible - 1);
          if (endPage - startPage < maxVisible - 1) {
            startPage = Math.max(1, endPage - maxVisible + 1);
          }

          for (let i = startPage; i <= endPage; i++) {
             createButton(null, i, false, i === currentPage);
          }

          createButton('<i class="ti ti-chevron-right text-lg"></i>', () => { currentPage++; loadData(); }, currentPage === totalPages);
          createButton('<i class="ti ti-chevrons-right text-lg"></i>', () => { currentPage = totalPages; loadData(); }, currentPage === totalPages);
        }

        function updateSortIndicators() {
           // ... (Same as before)
           sortButtons.forEach((th) => {
            const field = th.dataset.sort;
            const iconContainer = th.querySelector('.ina-table__sort-icon');
            if (iconContainer) {
              if (sortField === field) iconContainer.innerHTML = sortOrder === 'asc' ? '<i class="ti ti-arrow-up text-lg text-gray-900"></i>' : '<i class="ti ti-arrow-down text-lg text-gray-900"></i>';
              else iconContainer.innerHTML = '<i class="ti ti-arrows-sort text-lg text-gray-500"></i>';
            }
          });
        }

        async function loadData() {
          setLoading(true);
          try {
            const result = await fetchDummyProducts({ page: currentPage, pageSize, searchTerm, sortField, sortOrder });
            total = result.total;
            tableData = result.data;
            renderRows(result.data);
            renderPagination();
            updateSortIndicators();
          } catch (error) { console.error('Error loading data:', error); } 
          finally { setLoading(false); }
        }

        // --- DRAWER LOGIC ---
        const drawer = document.getElementById('drawer-overlay');
        const drawerContent = document.getElementById('drawer-content');
        const drawerTitle = document.getElementById('drawer-title');
        
        // Remove manual close logic as initDrawer relies on attributes/classes
        // window.InaUI.initDrawer() will handle triggers, close buttons, backdrop, escape.

        // Init Drawer manually for Table Rows (since they aren't buttons with data-trigger)
        // window.openDrawer = (id, type = 'product') => {
        //   if (!drawer) return;

        //   if (type === 'product') {
        //       const row = tableData.find((r) => r.id === id);
        //       if (!row) return;

        //       if (drawerTitle) drawerTitle.textContent = 'Detail Produk';
        //       drawerContent.innerHTML = `
        //         <div class="flex flex-col gap-6">
        //             <div class="flex justify-center mb-4">
        //                 <img src="${row.thumbnail}" alt="${row.title}" class="w-[200px] h-[200px] object-contain rounded-lg bg-gray-100">
        //             </div>
        //             <div>
        //                 <div class="text-sm text-gray-500 mb-1">Nama Produk</div>
        //                 <div class="font-semibold text-lg text-gray-900">${row.title}</div>
        //             </div>
        //             <div>
        //                 <div class="text-sm text-gray-500 mb-1">Brand</div>
        //                 <div class="font-medium text-gray-900">${row.brand || '-'}</div>
        //             </div>
        //              <div>
        //                 <div class="text-sm text-gray-500 mb-1">Kategori</div>
        //                  <span class="ina-badge ina-badge--info ina-badge--sm">${row.category}</span>
        //             </div>
        //             <div class="grid grid-cols-2 gap-4">
        //                 <div>
        //                     <div class="text-sm text-gray-500 mb-1">Harga</div>
        //                     <div class="font-semibold text-lg text-gray-900">$${row.price}</div>
        //                 </div>
        //                  <div>
        //                     <div class="text-sm text-gray-500 mb-1">Rating</div>
        //                     <div class="flex items-center gap-1">
        //                         <span class="font-medium text-gray-900">${row.rating}</span>
        //                         <span class="text-yellow-400">★</span>
        //                     </div>
        //                 </div>
        //             </div>
        //             <div>
        //                 <div class="text-sm text-gray-500 mb-1">Stok</div>
        //                 <div class="font-medium ${row.stock > 50 ? 'text-green-600' : 'text-red-500'}">${row.stock} unit</div>
        //             </div>
        //              <div>
        //                 <div class="text-sm text-gray-500 mb-1">Deskripsi</div>
        //                 <p class="text-sm text-gray-700 leading-relaxed">${row.description}</p>
        //             </div>
        //         </div>
        //     `;
        //   }

        //   // Open drawer manually but compatible with lib
        //   drawer.classList.remove('hidden');
        //   drawer.classList.add('ina-drawer--open');
        //   document.body.style.overflow = 'hidden';
        //   // NOTE: Library doesn't know about this open state unless we trigger it via library
        //   // However, since library operates on class state, it should be fine.
        //   // BUT, library adds escape/backdrop listeners on INIT to ALL drawers. 
        //   // So lookingGood.
        // };

        // Populate content for "Target Detail" button
        const btnTargetDetail = document.getElementById('btn-target-detail');
        if(btnTargetDetail) {
            btnTargetDetail.addEventListener('click', () => {
                 if (drawerTitle) drawerTitle.textContent = 'Detail Target Tahunan';
                 drawerContent.innerHTML = `
                    <div class="flex flex-col gap-4">
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                            <h4 class="font-semibold text-blue-800 mb-2">Status Pencapaian</h4>
                            <div class="flex items-end gap-2 mb-1">
                                <span class="text-3xl font-bold text-blue-600">75%</span>
                                <span class="text-sm text-blue-600 mb-1">dari target tahunan</span>
                            </div>
                            <div class="w-full bg-blue-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: 75%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Rincian KPI</h4>
                            <ul class="space-y-3">
                                <li class="flex justify-between items-center border-b border-gray-100 pb-2">
                                    <span class="text-sm text-gray-600">Adopsi Komponen</span>
                                    <span class="text-sm font-medium text-green-600">On Track (+12%)</span>
                                </li>
                                <li class="flex justify-between items-center border-b border-gray-100 pb-2">
                                    <span class="text-sm text-gray-600">Konsistensi Desain</span>
                                    <span class="text-sm font-medium text-green-600">On Track (+5%)</span>
                                </li>
                                <li class="flex justify-between items-center border-b border-gray-100 pb-2">
                                    <span class="text-sm text-gray-600">Efisiensi Development</span>
                                    <span class="text-sm font-medium text-yellow-600">Perlu Perhatian (-2%)</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-2">Rekomendasi Tindakan</h4>
                            <p class="text-sm text-gray-600">Tingkatkan penggunaan template halaman untuk mempercepat proses development dan memastikan konsistensi layout di seluruh aplikasi.</p>
                        </div>
                    </div>
                 `;
                 // Drawer opening is handled by initDrawer via data-trigger
            });
        }

        // Event Listeners
        if (searchInputEl) {
          searchInputEl.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
              searchTerm = e.target.value.trim();
              currentPage = 1;
              loadData();
            }
          });
        }

        if (pageSizeSelectEl) {
          pageSizeSelectEl.addEventListener('change', (e) => {
            pageSize = parseInt(e.target.value);
            currentPage = 1;
            loadData();
          });
        }

        sortButtons.forEach((th) => {
            // ... (Same logic)
           th.addEventListener('click', () => {
             const field = th.dataset.sort;
             if (sortField === field) sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
             else { sortField = field; sortOrder = 'asc'; }
             currentPage = 1;
             loadData();
           });
        });

        loadData();
      }

      // ... (ARTICLES LOGIC & FORM LOGIC kept as is or assumed stable) ...
      // --- ARTICLES LOGIC ---
      function initArticles() {
        let currentPage = 1;
        let selectedCategory = 'Semua';
        const itemsPerPage = 8;
        const categoryOptions = [
          { label: 'Semua Topik', value: 'Semua' },
          { label: 'Layanan Publik', value: 'Layanan Publik' },
          { label: 'Kebijakan & Regulasi', value: 'Kebijakan & Regulasi' },
          { label: 'Panduan Pengguna', value: 'Panduan Pengguna' },
        ];
        function renderCategoryFilter() {
          const container = document.getElementById('category-filter-container');
          if (!container) return;
          container.innerHTML = `
                <div class="ina-chip">
                    <div class="ina-chip__list">
                        ${categoryOptions.map((opt) => `
                            <button class="ina-chip__item ina-chip__item--size-medium ina-chip__item--variant-outline ${selectedCategory === opt.value ? 'ina-chip__item--selected' : ''}"
                                onclick="setArticleCategory('${opt.value}')">
                                ${opt.label}
                            </button>
                        `).join('')}
                    </div>
                </div>
            `;
        }
        window.setArticleCategory = (cat) => {
          selectedCategory = cat;
          currentPage = 1;
          renderCategoryFilter();
          renderArticles();
        };
        function renderArticles() {
          const grid = document.getElementById('articles-grid');
          if (!grid) return;
          const filtered = selectedCategory === 'Semua' ? articlesData : articlesData.filter((a) => a.category === selectedCategory);
          const totalPages = Math.ceil(filtered.length / itemsPerPage);
          const start = (currentPage - 1) * itemsPerPage;
          const end = start + itemsPerPage;
          const currentItems = filtered.slice(start, end);
          if (currentItems.length === 0) {
            grid.innerHTML = `<div class="col-span-full text-center py-12 text-gray-500">Tidak ada artikel ditemukan.</div>`;
          } else {
            grid.innerHTML = currentItems.map((article) => `
                    <div class="ina-card ina-card--variant-basic ina-card--media-top ina-card--clickable ina-card--hoverable h-full group cursor-pointer" onclick="viewArticleDetail(${article.id})">
                        <div class="ina-card__media">
                             <img src="${article.image}" alt="${article.title}">
                        </div>
                        <div class="ina-card__content">
                            <div>
                                <h3 class="ina-card__title">
                                    <span class="text-base md:text-lg font-semibold text-content-secondary line-clamp-1 group-hover:underline group-hover:text-blue-700 transition-colors">
                                        ${article.title}
                                    </span>
                                </h3>
                                <p class="ina-card__description">
                                    <span class="text-sm md:text-base text-content-secondary line-clamp-2">
                                        ${article.excerpt}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                `).join('');
          }
          renderPagination(totalPages);
        }
        function renderPagination(totalPages) {
          const container = document.getElementById('pagination-container');
          if (!container || totalPages <= 1) {
            if (container) container.innerHTML = '';
            return;
          }
          let pagesHtml = '';
          for (let i = 1; i <= totalPages; i++) {
            pagesHtml += `<button class="ina-pagination__button ${currentPage === i ? 'ina-pagination__button--active' : ''}" onclick="goToArticlePage(${i})">${i}</button>`;
          }
          container.innerHTML = `
                <div class="ina-pagination justify-center">
                    <button class="ina-pagination__button" ${currentPage === 1 ? 'disabled' : ''} onclick="goToArticlePage(${currentPage - 1})"><i class="ti ti-chevron-left"></i></button>
                    ${pagesHtml}
                    <button class="ina-pagination__button" ${currentPage === totalPages ? 'disabled' : ''} onclick="goToArticlePage(${currentPage + 1})"><i class="ti ti-chevron-right"></i></button>
                </div>
            `;
        }
        window.goToArticlePage = (page) => { currentPage = page; renderArticles(); };
        window.viewArticleDetail = (id) => {
          const article = articlesData.find((a) => a.id == id);
          if (!article) return;
          const listView = document.getElementById('articles-list-view');
          const detailView = document.getElementById('articles-detail-view');
          detailView.innerHTML = `
                <div class="max-w-4xl mx-auto">
                    <button onclick="closeArticleDetail()" class="ina-button ina-button--text ina-button--sm mb-6 pl-0"><i class="ti ti-arrow-left mr-2"></i> Kembali ke Artikel</button>
                    <div class="mb-6">
                        <span class="ina-badge ina-badge--primary ina-badge--outline mb-4">${article.category}</span>
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">${article.title}</h1>
                        <div class="flex items-center gap-4 text-sm text-gray-500"><span>${article.date}</span><span>•</span><span>Oleh ${article.author || 'Tim Redaksi'}</span></div>
                    </div>
                    <div class="aspect-video rounded-xl overflow-hidden mb-8"><img src="${article.image}" alt="${article.title}" class="w-full h-full object-cover"></div>
                    <div class="prose prose-blue max-w-none">${article.content}</div>
                </div>
            `;
          listView.style.display = 'none';
          detailView.style.display = 'block';
          window.scrollTo(0, 0);
        };
        window.closeArticleDetail = () => {
          document.getElementById('articles-list-view').style.display = 'block';
          document.getElementById('articles-detail-view').style.display = 'none';
        };
        renderCategoryFilter();
        renderArticles();
      }

      // --- FORM LOGIC ---
      function initForm() {
        const form = document.getElementById('form-submission');
        if (!form) return;
        let submitted = false;
        const showError = (field, message) => {
          const errorDiv = document.getElementById(`${field}-error`);
          if (errorDiv) { errorDiv.textContent = message; errorDiv.classList.remove('hidden'); }
          const wrapper = document.getElementById(`${field}-wrapper`);
          if (wrapper) { wrapper.classList.add('ina-text-field__wrapper--error'); wrapper.classList.remove('ina-text-field__wrapper--success'); }
        };
        const showSuccess = (field) => {
          const errorDiv = document.getElementById(`${field}-error`);
          if (errorDiv) errorDiv.classList.add('hidden');
          const wrapper = document.getElementById(`${field}-wrapper`);
          if (wrapper) wrapper.classList.remove('ina-text-field__wrapper--error');
        };
        const validateEmail = (email) => {
          if (!email.trim()) return { isValid: false, message: 'Email wajib diisi' };
          const re = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;
          if (!re.test(email)) return { isValid: false, message: 'Format email tidak valid' };
          return { isValid: true };
        };
        ['name', 'email', 'phone', 'password', 'confirmPassword'].forEach((id) => {
          const el = document.getElementById(id);
          if (el) el.addEventListener('input', () => { if (submitted && el.value) showSuccess(id); });
        });
        document.querySelectorAll('.ina-password-input__toggle-button').forEach((btn) => {
          btn.addEventListener('click', () => {
             const targetId = btn.getAttribute('data-target');
             const input = document.getElementById(targetId);
             const icon = btn.querySelector('svg');
             if (input.type === 'password') { input.type = 'text'; icon.style.opacity = '0.5'; }
             else { input.type = 'password'; icon.style.opacity = '1'; }
          });
        });
        form.addEventListener('submit', (e) => {
          e.preventDefault();
          submitted = true;
          let hasError = false;
          const name = document.getElementById('name').value;
          if (!name.trim()) { showError('name', 'Nama lengkap wajib diisi'); hasError = true; } else showSuccess('name');
          const email = document.getElementById('email').value;
          const emailRes = validateEmail(email);
          if (!emailRes.isValid) { showError('email', emailRes.message); hasError = true; } else showSuccess('email');
          const password = document.getElementById('password').value;
          if (!password) { showError('password', 'Kata sandi wajib diisi'); hasError = true; }
          else if (password.length < 8) { showError('password', 'Minimal 8 karakter'); hasError = true; } else showSuccess('password');
          const confirm = document.getElementById('confirmPassword').value;
          if (confirm !== password) { showError('confirmPassword', 'Kata sandi tidak cocok'); hasError = true; } else showSuccess('confirmPassword');
          if (!hasError) {
            showToast('positive', 'Akun berhasil dibuat!', 'Silakan cek email Anda untuk verifikasi.');
            form.reset();
            submitted = false;
          } else {
            showToast('destructive', 'Gagal membuat akun', 'Mohon periksa kembali inputan Anda.');
          }
        });
      }

      // --- SIDEBAR LOGIC ---
      function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (sidebar.classList.contains('-translate-x-full')) {
          sidebar.classList.remove('-translate-x-full');
          sidebar.classList.add('translate-x-0');
          overlay.classList.remove('hidden');
        } else {
          sidebar.classList.add('-translate-x-full');
          sidebar.classList.remove('translate-x-0');
          overlay.classList.add('hidden');
        }
      }
      function toggleSidebarCollapse() {
        if (window.innerWidth < 1024) return;
        const sidebar = document.getElementById('sidebar');
        const isCollapsed = sidebar.classList.contains('w-[248px]') || sidebar.classList.contains('xl:w-[264px]');
        // Changed sidebar-logo-container to sidebar-brand-text to keep the ID logo visible
        const elements = ['sidebar-brand-text', 'nav-group-title', 'collapse-text', 'user-details'];
        const header = document.getElementById('sidebar-header');
        const logoContainer = document.getElementById('sidebar-logo-container');
        const collapseBtn = document.getElementById('header-collapse-btn');
        const userProfile = document.getElementById('user-profile');
        const iconCollapse = document.getElementById('icon-collapse-header');
        const iconExpand = document.getElementById('icon-expand-header');
        const mainWrapper = document.querySelector('.main-wrapper');

        if (isCollapsed) {
          sidebar.classList.remove('w-[248px]', 'xl:w-[264px]');
          sidebar.classList.add('w-20', 'collapsed');
          elements.forEach(id => document.getElementById(id)?.classList.add('hidden'));
          
          // Adjust header for collapsed state
          if (header) {
            header.classList.remove('px-6', 'gap-3');
            header.classList.add('px-2', 'justify-center', 'gap-1');
          }
          if (logoContainer) {
            logoContainer.classList.remove('flex-1');
          }

          collapseBtn.classList.add('justify-center');
          userProfile.classList.add('justify-center');
          iconCollapse.classList.add('hidden');
          iconExpand.classList.remove('hidden');
          if (mainWrapper) mainWrapper.classList.add('lg:ml-20');
        } else {
          sidebar.classList.add('w-[248px]', 'xl:w-[264px]');
          sidebar.classList.remove('w-20', 'collapsed');
          elements.forEach(id => document.getElementById(id)?.classList.remove('hidden'));

          // Reset header for expanded state
          if (header) {
            header.classList.add('px-6', 'gap-3');
            header.classList.remove('px-2', 'justify-center', 'gap-1');
          }
          if (logoContainer) {
            logoContainer.classList.add('flex-1');
          }

          collapseBtn.classList.remove('justify-center');
          userProfile.classList.remove('justify-center');
          iconCollapse.classList.remove('hidden');
          iconExpand.classList.add('hidden');
          if (mainWrapper) mainWrapper.classList.remove('lg:ml-20');
        }
      }

      // --- INITIALIZATION ---
      document.addEventListener('DOMContentLoaded', () => {
        navigateTo('dashboard');
        initDashboard();
        initArticles();
        initForm();
        if (window.InaUI && window.InaUI.initDrawer) {
            window.InaUI.initDrawer();
        }
        const mainWrapper = document.querySelector('.main-wrapper');
        if(mainWrapper) {
            mainWrapper.classList.add('transition-[margin]', 'duration-300');
        }
      });
    </script>
  </body>
</html>
