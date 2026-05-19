<div id="page-dashboard" class="page hidden active">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <div>
      <h1 class="text-2xl font-semibold m-0 text-gray-900">Dashboard Overview</h1>
      <p class="text-sm text-gray-500 mt-1">Pantau metrik utama dan kinerja sistem desain Anda.</p>
    </div>
  </div>

  <!-- Metrics -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6" id="dashboard-metrics">
    <!-- Injected by JS -->
  </div>

  <!-- Charts -->
  <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-6 mb-6">
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h3 class="text-lg font-semibold m-0 text-gray-900">Tren Adopsi Komponen</h3>
          <p class="text-sm text-gray-500 m-0">Perbandingan per kategori komponen</p>
        </div>
      </div>
      <div id="chart-adoption" class="min-h-[300px]"></div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex flex-col items-center justify-center text-center">
      <h3 class="text-lg font-semibold mb-2 text-gray-900">Target Tahunan</h3>
      <p class="text-sm text-gray-500 mb-6">Pencapaian adopsi sistem desain</p>
      <div class="relative w-40 h-40">
        <svg viewBox="0 0 36 36" class="block w-full">
          <path
            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
            fill="none" stroke="#F1F5F9" stroke-width="3"
          />
          <path
            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
            fill="none" stroke="#2563EB" stroke-width="3" stroke-dasharray="75, 100"
          />
        </svg>
        <div class="absolute inset-0 flex items-center justify-center text-3xl font-bold text-gray-900">75%</div>
      </div>
      <button
        class="ina-button ina-button--secondary w-full mt-6"
        data-trigger="drawer"
        data-target="#drawer-overlay"
        id="btn-target-detail"
      >
        Lihat Detail
      </button>
    </div>
  </div>

  <!-- Table -->
  <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center flex-wrap gap-4">
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

    <div class="ina-table" id="table-container">
      <div class="ina-table__progress-bar hidden" id="table-progress-bar">
        <div class="ina-progress-bar ina-progress-bar--variant-primary ina-progress-bar--height-sm">
          <div class="ina-progress-bar__track">
            <div class="ina-progress-bar__fill" id="table-progress-fill" style="width: 0%"></div>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="ina-table__container" id="table-api">
          <thead class="ina-table__header">
            <tr>
              <th class="ina-table__header-cell ina-table__header-cell--sortable cursor-pointer" data-sort="title">
                <div class="ina-table__sort-controls">Product<div class="ina-table__sort-icon"><i class="ti ti-arrows-sort text-lg text-gray-500"></i></div></div>
              </th>
              <th class="ina-table__header-cell ina-table__header-cell--sortable cursor-pointer" data-sort="brand">
                <div class="ina-table__sort-controls">Brand<div class="ina-table__sort-icon"><i class="ti ti-arrows-sort text-lg text-gray-500"></i></div></div>
              </th>
              <th class="ina-table__header-cell ina-table__header-cell--sortable cursor-pointer" data-sort="price">
                <div class="ina-table__sort-controls">Price<div class="ina-table__sort-icon"><i class="ti ti-arrows-sort text-lg text-gray-500"></i></div></div>
              </th>
              <th class="ina-table__header-cell ina-table__header-cell--sortable cursor-pointer" data-sort="stock">
                <div class="ina-table__sort-controls">Stock<div class="ina-table__sort-icon"><i class="ti ti-arrows-sort text-lg text-gray-500"></i></div></div>
              </th>
              <th class="ina-table__header-cell ina-table__header-cell--sortable cursor-pointer" data-sort="rating">
                <div class="ina-table__sort-controls">Rating<div class="ina-table__sort-icon"><i class="ti ti-arrows-sort text-lg text-gray-500"></i></div></div>
              </th>
              <th class="ina-table__header-cell ina-table__header-cell--sortable cursor-pointer" data-sort="category">
                <div class="ina-table__sort-controls">Category<div class="ina-table__sort-icon"><i class="ti ti-arrows-sort text-lg text-gray-500"></i></div></div>
              </th>
            </tr>
          </thead>
          <tbody class="ina-table__body" id="table-body">
            <!-- Injected by JS -->
          </tbody>
        </table>
      </div>

      <div class="ina-table__pagination border-t border-gray-200">
        <div class="ina-pagination">
          <div class="ina-pagination__nav-container">
            <div class="ina-pagination__page-info" id="table-page-info">Halaman 1 dari 1</div>
            <div class="ina-pagination__nav-buttons" id="table-pagination-buttons"></div>
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

  <!-- Drawer -->
  <div id="drawer-overlay" class="ina-drawer hidden" style="display: none;">
    <div class="ina-drawer__backdrop" id="drawer-backdrop"></div>
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
        <!-- Injected by JS -->
      </div>
    </div>
  </div>
</div>
