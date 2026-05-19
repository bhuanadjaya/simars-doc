<!-- IDDS JS -->
<script src="https://unpkg.com/@idds/js@latest/dist/index.iife.js"></script>

<script>
  // --- DATA CONSTANTS ---
  const metricsData = [
    { label: 'Komponen Digunakan', value: '128', subtitle: '12 komponen ditambahkan bulan ini', icon: 'ti-package', colorClass: 'bg-orange-100 text-orange-600' },
    { label: 'Halaman Teradopsi', value: '342', subtitle: '45 halaman diperbarui', icon: 'ti-files', colorClass: 'bg-blue-100 text-blue-600' },
    { label: 'Konsistensi Desain', value: '92%', subtitle: '3% dibanding periode sebelumnya', icon: 'ti-check', colorClass: 'bg-green-100 text-green-600' },
  ];

  const articlesData = [
    { id: '1', title: 'Pemerintah Perkuat Layanan Digital ASN', excerpt: 'Integrasi layanan ke dalam INAgov diharapkan meningkatkan efisiensi dan akses informasi bagi Aparatur Sipil Negara.', category: 'Kebijakan & Regulasi', image: 'https://picsum.photos/id/1018/800/600', date: '2025-01-24', author: 'Haechal', content: '<p>Pemerintah terus berupaya memperkuat layanan digital bagi Aparatur Sipil Negara (ASN) melalui berbagai inisiatif baru. Salah satu langkah strategis yang diambil adalah mengintegrasikan berbagai layanan kepegawaian ke dalam platform INAgov.</p><p>Langkah ini diambil untuk meningkatkan efisiensi administrasi dan memudahkan ASN dalam mengakses informasi terkait hak dan kewajiban mereka. "Transformasi digital ini bukan hanya tentang teknologi, tetapi juga tentang perubahan budaya kerja," ujar salah satu pejabat terkait.</p>' },
    { id: '2', title: 'Startups Lokal Menggagas Inovasi Baru', excerpt: 'Berbagai startup lokal kini mulai unjuk gigi dengan inovasi digital yang solutif bagi masyarakat.', category: 'Layanan Publik', image: 'https://picsum.photos/id/1015/800/600', date: '2025-01-20', author: 'Sarah Jenkins', content: '<p>Startup lokal di Indonesia semakin menunjukkan eksistensinya dengan berbagai inovasi yang relevan bagi kebutuhan masyarakat.</p>' },
    { id: '3', title: 'AI untuk Pelayanan Publik yang Lebih Baik', excerpt: 'Penerapan kecerdasan buatan dalam sektor publik dapat mempercepat proses birokrasi.', category: 'Layanan Publik', image: 'https://picsum.photos/id/1019/800/600', date: '2025-01-18', author: 'Tech Daily', content: '<p>Kecerdasan Buatan (AI) mulai diterapkan di berbagai sektor pelayanan publik untuk meningkatkan efisiensi dan responsivitas.</p>' },
    { id: '4', title: 'Keamanan Data di Era Digital', excerpt: 'Pentingnya menjaga privasi dan keamanan data pribadi di tengah maraknya layanan digital.', category: 'Kebijakan & Regulasi', image: 'https://picsum.photos/id/1021/800/600', date: '2025-01-15', author: 'Security Watch', content: '<p>Di era digital yang serba terhubung, keamanan data menjadi isu krusial yang tidak bisa diabaikan.</p>' },
    { id: '5', title: 'Panduan Menggunakan INAgov', excerpt: 'Langkah mudah untuk mengakses berbagai layanan pemerintah melalui satu pintu.', category: 'Panduan Pengguna', image: 'https://picsum.photos/id/1025/800/600', date: '2025-01-12', author: 'Helpdesk INAgov', content: '<p>INAgov hadir sebagai solusi satu pintu untuk berbagai layanan pemerintah.</p>' },
    { id: '6', title: 'Masa Depan Identitas Digital', excerpt: 'Identitas Kependudukan Digital (IKD) akan menjadi kunci akses layanan publik terintegrasi.', category: 'Kebijakan & Regulasi', image: 'https://picsum.photos/id/1020/800/600', date: '2025-01-10', author: 'Digital ID Team', content: '<p>Identitas Kependudukan Digital (IKD) diproyeksikan akan menggantikan KTP fisik di masa depan.</p>' },
  ];

  // --- HELPER: TOAST ---
  function showToast(state, title, description = '') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    container.style.display = 'flex';
    const toast = document.createElement('div');
    toast.className = `ina-toast ina-toast--${state}`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
      <div class="ina-toast__icon">
        ${state === 'positive' ? '<i class="ti ti-circle-check"></i>' : state === 'destructive' ? '<i class="ti ti-alert-circle"></i>' : state === 'warning' ? '<i class="ti ti-alert-triangle"></i>' : '<i class="ti ti-info-circle"></i>'}
      </div>
      <div class="ina-toast__content">
        <div class="ina-toast__title">${title}</div>
        ${description ? `<div class="ina-toast__description">${description}</div>` : ''}
      </div>
      <button class="ina-toast__close" aria-label="Close"><i class="ti ti-x"></i></button>
    `;
    container.appendChild(toast);
    toast.querySelector('.ina-toast__close').onclick = () => toast.remove();
    setTimeout(() => { if (toast.isConnected) toast.remove(); }, 5000);
  }

  // --- NAVIGATION ---
  function navigateTo(page) {
    document.querySelectorAll('.nav-item').forEach((el) => {
      el.classList.remove('active', 'bg-blue-50', 'text-blue-700');
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
    if (window.innerWidth <= 1024) {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.add('-translate-x-full');
      sidebar.classList.remove('translate-x-0');
      document.getElementById('sidebar-overlay').classList.add('hidden');
    }
  }

  // --- SIDEBAR ---
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
    const isExpanded = sidebar.classList.contains('w-[248px]') || sidebar.classList.contains('xl:w-[264px]');
    const toggleEls = ['sidebar-brand-text', 'nav-group-title', 'collapse-text', 'user-details'];
    const header = document.getElementById('sidebar-header');
    const logoContainer = document.getElementById('sidebar-logo-container');
    const collapseBtn = document.getElementById('header-collapse-btn');
    const userProfile = document.getElementById('user-profile');
    const iconCollapse = document.getElementById('icon-collapse-header');
    const iconExpand = document.getElementById('icon-expand-header');
    const mainWrapper = document.querySelector('.main-wrapper');

    if (isExpanded) {
      sidebar.classList.remove('w-[248px]', 'xl:w-[264px]');
      sidebar.classList.add('w-20', 'collapsed');
      toggleEls.forEach(id => document.getElementById(id)?.classList.add('hidden'));
      header?.classList.replace('px-6', 'px-2');
      header?.classList.add('justify-center', 'gap-1');
      header?.classList.remove('gap-3');
      logoContainer?.classList.remove('flex-1');
      collapseBtn?.classList.add('justify-center');
      userProfile?.classList.add('justify-center');
      iconCollapse?.classList.add('hidden');
      iconExpand?.classList.remove('hidden');
      mainWrapper?.classList.add('lg:ml-20');
    } else {
      sidebar.classList.add('w-[248px]', 'xl:w-[264px]');
      sidebar.classList.remove('w-20', 'collapsed');
      toggleEls.forEach(id => document.getElementById(id)?.classList.remove('hidden'));
      header?.classList.replace('px-2', 'px-6');
      header?.classList.remove('justify-center', 'gap-1');
      header?.classList.add('gap-3');
      logoContainer?.classList.add('flex-1');
      collapseBtn?.classList.remove('justify-center');
      userProfile?.classList.remove('justify-center');
      iconCollapse?.classList.remove('hidden');
      iconExpand?.classList.add('hidden');
      mainWrapper?.classList.remove('lg:ml-20');
    }
  }

  // --- DASHBOARD ---
  function initDashboard() {
    // Metrics
    const metricsContainer = document.getElementById('dashboard-metrics');
    if (metricsContainer) {
      metricsContainer.innerHTML = metricsData.map(m => `
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex items-start gap-4">
          <div class="w-10 h-10 rounded-lg flex items-center justify-center ${m.colorClass}">
            <i class="ti ${m.icon} text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-500">${m.label}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">${m.value}</p>
            <p class="text-xs text-gray-400 mt-1">${m.subtitle}</p>
          </div>
        </div>
      `).join('');
    }

    // Chart
    if (document.querySelector('#chart-adoption')) {
      const chart = new ApexCharts(document.querySelector('#chart-adoption'), {
        series: [{ name: 'Peningkatan Adopsi', data: [5.3, 3.8, 6.5, 4.1, 5.3, 2.3] }],
        chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '40%' } },
        dataLabels: { enabled: false },
        xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], axisBorder: { show: false }, axisTicks: { show: false } },
        colors: ['#2563EB'],
        grid: { borderColor: '#F1F5F9', strokeDashArray: 4 },
      });
      chart.render();
    }

    // Table state
    const FALLBACK = [
      { id: 1, title: 'iPhone 9', description: 'An apple mobile which is nothing like apple', category: 'smartphones', price: 549, discountPercentage: 12.96, rating: 4.69, stock: 94, brand: 'Apple', thumbnail: 'https://cdn.dummyjson.com/products/images/smartphones/iPhone%209/thumbnail.jpg' },
      { id: 2, title: 'iPhone X', description: 'SIM-Free, Model A19211 6.5-inch Super Retina HD display', category: 'smartphones', price: 899, discountPercentage: 17.94, rating: 4.44, stock: 34, brand: 'Apple', thumbnail: 'https://cdn.dummyjson.com/products/images/smartphones/iPhone%20X/thumbnail.jpg' },
    ];

    async function fetchProducts({ page, pageSize, searchTerm, sortField, sortOrder }) {
      const skip = (page - 1) * pageSize;
      const base = 'https://dummyjson.com/products';
      try {
        let url = searchTerm
          ? `${base}/search?q=${encodeURIComponent(searchTerm)}&limit=${pageSize}&skip=${skip}`
          : `${base}?limit=${pageSize}&skip=${skip}`;
        if (sortField && sortOrder) url += `&sortBy=${sortField}&order=${sortOrder}`;
        const res = await fetch(url);
        if (!res.ok) throw new Error(`API error: ${res.status}`);
        const json = await res.json();
        return { data: json.products || [], total: json.total || 0 };
      } catch {
        let data = [...FALLBACK];
        if (searchTerm) {
          const q = searchTerm.toLowerCase();
          data = data.filter(p => [p.title, p.description, p.brand, p.category].some(v => v.toLowerCase().includes(q)));
        }
        if (sortField) data.sort((a, b) => (a[sortField] < b[sortField] ? (sortOrder === 'asc' ? -1 : 1) : a[sortField] > b[sortField] ? (sortOrder === 'asc' ? 1 : -1) : 0));
        return { data: data.slice(skip, skip + pageSize), total: data.length };
      }
    }

    let state = { page: 1, pageSize: 10, total: 0, sortField: null, sortOrder: null, searchTerm: '', loading: false, data: [] };

    const $body = document.getElementById('table-body');
    const $progress = document.getElementById('table-progress-bar');
    const $fill = document.getElementById('table-progress-fill');
    const $info = document.getElementById('table-page-info');
    const $btns = document.getElementById('table-pagination-buttons');
    const $size = document.getElementById('table-page-size');
    const $search = document.getElementById('table-search-input');
    const $sorts = document.querySelectorAll('.ina-table__header-cell--sortable');

    function setLoading(on) {
      state.loading = on;
      if (!$progress) return;
      if (on) {
        $progress.style.display = 'block';
        let p = 0;
        const iv = setInterval(() => {
          if (p >= 90 || !state.loading) { clearInterval(iv); if (!state.loading) { $fill.style.width = '100%'; setTimeout(() => { $progress.style.display = 'none'; $fill.style.width = '0%'; }, 300); } return; }
          p += Math.random() * 15;
          $fill.style.width = p + '%';
        }, 200);
      }
    }

    function renderRows(data) {
      if (!$body) return;
      if (!data.length) { $body.innerHTML = `<tr><td colspan="6" class="ina-table__empty-cell" style="text-align:center;padding:24px;color:#6b7280;">No data found</td></tr>`; return; }
      $body.innerHTML = data.map(p => `
        <tr class="ina-table__row" style="border-bottom:1px solid #F3F4F6;cursor:pointer;" onclick="window.openDrawer(${p.id},'product')">
          <td class="ina-table__cell" style="padding:16px 24px;"><div style="display:flex;align-items:center;gap:12px;"><img src="${p.thumbnail}" alt="${p.title}" style="width:40px;height:40px;border-radius:4px;object-fit:cover;" loading="lazy"><span style="font-weight:500;font-size:14px;color:#111827;">${p.title}</span></div></td>
          <td class="ina-table__cell" style="padding:16px 24px;"><span style="font-size:14px;color:#6B7280;">${p.brand || '-'}</span></td>
          <td class="ina-table__cell" style="padding:16px 24px;"><span style="font-weight:600;font-size:14px;">$${p.price}</span></td>
          <td class="ina-table__cell" style="padding:16px 24px;"><span style="font-weight:500;font-size:14px;color:${p.stock > 50 ? '#10B981' : '#EF4444'};">${p.stock}</span></td>
          <td class="ina-table__cell" style="padding:16px 24px;"><div style="display:flex;align-items:center;gap:4px;"><span style="font-size:14px;">${p.rating}</span><span style="color:#f6da09;">★</span></div></td>
          <td class="ina-table__cell" style="padding:16px 24px;"><span class="ina-badge ina-badge--info ina-badge--sm">${p.category}</span></td>
        </tr>
      `).join('');
    }

    function renderPagination() {
      if (!$info || !$btns) return;
      const totalPages = Math.ceil(state.total / state.pageSize);
      $info.textContent = `Halaman ${state.page} dari ${totalPages}`;
      $btns.innerHTML = '';
      const btn = (icon, onClick, disabled, active = false) => {
        const el = document.createElement('button');
        el.type = 'button';
        el.className = active ? 'ina-pagination__page-button ina-pagination__page-button--active' : (icon ? 'ina-pagination__nav-button' : 'ina-pagination__page-button');
        if (disabled) { el.setAttribute('disabled', 'true'); el.classList.add('ina-pagination__nav-button--disabled'); }
        else el.classList.add(icon ? 'ina-pagination__nav-button--enabled' : 'ina-pagination__page-button--enabled');
        el.innerHTML = icon ? icon : String(onClick);
        el.onclick = () => { if (!disabled) { if (typeof onClick === 'function') onClick(); else { state.page = onClick; load(); } } };
        $btns.appendChild(el);
      };
      btn('<i class="ti ti-chevrons-left text-lg"></i>', () => { state.page = 1; load(); }, state.page === 1);
      btn('<i class="ti ti-chevron-left text-lg"></i>', () => { state.page--; load(); }, state.page === 1);
      const max = 5;
      let start = Math.max(1, state.page - Math.floor(max / 2));
      let end = Math.min(totalPages, start + max - 1);
      if (end - start < max - 1) start = Math.max(1, end - max + 1);
      for (let i = start; i <= end; i++) btn(null, i, false, i === state.page);
      btn('<i class="ti ti-chevron-right text-lg"></i>', () => { state.page++; load(); }, state.page === totalPages);
      btn('<i class="ti ti-chevrons-right text-lg"></i>', () => { state.page = totalPages; load(); }, state.page === totalPages);
    }

    function updateSortIndicators() {
      $sorts.forEach(th => {
        const icon = th.querySelector('.ina-table__sort-icon');
        if (!icon) return;
        icon.innerHTML = state.sortField === th.dataset.sort
          ? (state.sortOrder === 'asc' ? '<i class="ti ti-arrow-up text-lg text-gray-900"></i>' : '<i class="ti ti-arrow-down text-lg text-gray-900"></i>')
          : '<i class="ti ti-arrows-sort text-lg text-gray-500"></i>';
      });
    }

    async function load() {
      setLoading(true);
      try {
        const res = await fetchProducts({ page: state.page, pageSize: state.pageSize, searchTerm: state.searchTerm, sortField: state.sortField, sortOrder: state.sortOrder });
        state.total = res.total;
        state.data = res.data;
        renderRows(res.data);
        renderPagination();
        updateSortIndicators();
      } finally { setLoading(false); }
    }

    // Drawer
    const drawer = document.getElementById('drawer-overlay');
    const $drawerContent = document.getElementById('drawer-content');
    const $drawerTitle = document.getElementById('drawer-title');

    window.openDrawer = (id, type = 'product') => {
      if (!drawer) return;
      if (type === 'product') {
        const row = state.data.find(r => r.id === id);
        if (!row) return;
        if ($drawerTitle) $drawerTitle.textContent = 'Detail Produk';
        $drawerContent.innerHTML = `
          <div class="flex flex-col gap-6">
            <div class="flex justify-center mb-4"><img src="${row.thumbnail}" alt="${row.title}" class="w-[200px] h-[200px] object-contain rounded-lg bg-gray-100"></div>
            <div><div class="text-sm text-gray-500 mb-1">Nama Produk</div><div class="font-semibold text-lg text-gray-900">${row.title}</div></div>
            <div><div class="text-sm text-gray-500 mb-1">Brand</div><div class="font-medium text-gray-900">${row.brand || '-'}</div></div>
            <div><div class="text-sm text-gray-500 mb-1">Kategori</div><span class="ina-badge ina-badge--info ina-badge--sm">${row.category}</span></div>
            <div class="grid grid-cols-2 gap-4">
              <div><div class="text-sm text-gray-500 mb-1">Harga</div><div class="font-semibold text-lg text-gray-900">$${row.price}</div></div>
              <div><div class="text-sm text-gray-500 mb-1">Rating</div><div class="flex items-center gap-1"><span class="font-medium text-gray-900">${row.rating}</span><span class="text-yellow-400">★</span></div></div>
            </div>
            <div><div class="text-sm text-gray-500 mb-1">Stok</div><div class="font-medium ${row.stock > 50 ? 'text-green-600' : 'text-red-500'}">${row.stock} unit</div></div>
            <div><div class="text-sm text-gray-500 mb-1">Deskripsi</div><p class="text-sm text-gray-700 leading-relaxed">${row.description}</p></div>
          </div>
        `;
      }
      drawer.classList.remove('hidden');
      drawer.classList.add('ina-drawer--open');
      document.body.style.overflow = 'hidden';
    };

    document.getElementById('btn-target-detail')?.addEventListener('click', () => {
      if ($drawerTitle) $drawerTitle.textContent = 'Detail Target Tahunan';
      $drawerContent.innerHTML = `
        <div class="flex flex-col gap-4">
          <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
            <h4 class="font-semibold text-blue-800 mb-2">Status Pencapaian</h4>
            <div class="flex items-end gap-2 mb-1"><span class="text-3xl font-bold text-blue-600">75%</span><span class="text-sm text-blue-600 mb-1">dari target tahunan</span></div>
            <div class="w-full bg-blue-200 rounded-full h-2.5"><div class="bg-blue-600 h-2.5 rounded-full" style="width:75%"></div></div>
          </div>
          <div>
            <h4 class="font-semibold text-gray-900 mb-2">Rincian KPI</h4>
            <ul class="space-y-3">
              <li class="flex justify-between items-center border-b border-gray-100 pb-2"><span class="text-sm text-gray-600">Adopsi Komponen</span><span class="text-sm font-medium text-green-600">On Track (+12%)</span></li>
              <li class="flex justify-between items-center border-b border-gray-100 pb-2"><span class="text-sm text-gray-600">Konsistensi Desain</span><span class="text-sm font-medium text-green-600">On Track (+5%)</span></li>
              <li class="flex justify-between items-center border-b border-gray-100 pb-2"><span class="text-sm text-gray-600">Efisiensi Development</span><span class="text-sm font-medium text-yellow-600">Perlu Perhatian (-2%)</span></li>
            </ul>
          </div>
          <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="font-semibold text-gray-900 mb-2">Rekomendasi Tindakan</h4>
            <p class="text-sm text-gray-600">Tingkatkan penggunaan template halaman untuk mempercepat proses development dan memastikan konsistensi layout di seluruh aplikasi.</p>
          </div>
        </div>
      `;
    });

    $search?.addEventListener('keydown', (e) => { if (e.key === 'Enter') { state.searchTerm = e.target.value.trim(); state.page = 1; load(); } });
    $size?.addEventListener('change', (e) => { state.pageSize = parseInt(e.target.value); state.page = 1; load(); });
    $sorts.forEach(th => th.addEventListener('click', () => {
      const field = th.dataset.sort;
      if (state.sortField === field) state.sortOrder = state.sortOrder === 'asc' ? 'desc' : 'asc';
      else { state.sortField = field; state.sortOrder = 'asc'; }
      state.page = 1;
      load();
    }));

    load();
  }

  // --- ARTICLES ---
  function initArticles() {
    let currentPage = 1;
    let selectedCategory = 'Semua';
    const itemsPerPage = 8;
    const categories = [
      { label: 'Semua Topik', value: 'Semua' },
      { label: 'Layanan Publik', value: 'Layanan Publik' },
      { label: 'Kebijakan & Regulasi', value: 'Kebijakan & Regulasi' },
      { label: 'Panduan Pengguna', value: 'Panduan Pengguna' },
    ];

    function renderFilter() {
      const el = document.getElementById('category-filter-container');
      if (!el) return;
      el.innerHTML = `<div class="ina-chip"><div class="ina-chip__list">${categories.map(c => `
        <button class="ina-chip__item ina-chip__item--size-medium ina-chip__item--variant-outline ${selectedCategory === c.value ? 'ina-chip__item--selected' : ''}" onclick="setArticleCategory('${c.value}')">${c.label}</button>
      `).join('')}</div></div>`;
    }

    window.setArticleCategory = (cat) => { selectedCategory = cat; currentPage = 1; renderFilter(); renderArticles(); };

    function renderArticles() {
      const grid = document.getElementById('articles-grid');
      if (!grid) return;
      const filtered = selectedCategory === 'Semua' ? articlesData : articlesData.filter(a => a.category === selectedCategory);
      const totalPages = Math.ceil(filtered.length / itemsPerPage);
      const items = filtered.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);
      grid.innerHTML = items.length
        ? items.map(a => `
          <div class="ina-card ina-card--variant-basic ina-card--media-top ina-card--clickable ina-card--hoverable h-full group cursor-pointer" onclick="viewArticleDetail(${a.id})">
            <div class="ina-card__media"><img src="${a.image}" alt="${a.title}"></div>
            <div class="ina-card__content">
              <h3 class="ina-card__title"><span class="text-base md:text-lg font-semibold text-content-secondary line-clamp-1 group-hover:underline group-hover:text-blue-700 transition-colors">${a.title}</span></h3>
              <p class="ina-card__description"><span class="text-sm md:text-base text-content-secondary line-clamp-2">${a.excerpt}</span></p>
            </div>
          </div>
        `).join('')
        : `<div class="col-span-full text-center py-12 text-gray-500">Tidak ada artikel ditemukan.</div>`;
      renderArticlePagination(totalPages);
    }

    function renderArticlePagination(totalPages) {
      const el = document.getElementById('pagination-container');
      if (!el || totalPages <= 1) { if (el) el.innerHTML = ''; return; }
      let html = '';
      for (let i = 1; i <= totalPages; i++) html += `<button class="ina-pagination__button ${currentPage === i ? 'ina-pagination__button--active' : ''}" onclick="goToArticlePage(${i})">${i}</button>`;
      el.innerHTML = `<div class="ina-pagination justify-center"><button class="ina-pagination__button" ${currentPage === 1 ? 'disabled' : ''} onclick="goToArticlePage(${currentPage - 1})"><i class="ti ti-chevron-left"></i></button>${html}<button class="ina-pagination__button" ${currentPage === totalPages ? 'disabled' : ''} onclick="goToArticlePage(${currentPage + 1})"><i class="ti ti-chevron-right"></i></button></div>`;
    }

    window.goToArticlePage = (page) => { currentPage = page; renderArticles(); };

    window.viewArticleDetail = (id) => {
      const article = articlesData.find(a => a.id == id);
      if (!article) return;
      const detail = document.getElementById('articles-detail-view');
      detail.innerHTML = `
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
      document.getElementById('articles-list-view').style.display = 'none';
      detail.style.display = 'block';
      window.scrollTo(0, 0);
    };

    window.closeArticleDetail = () => {
      document.getElementById('articles-list-view').style.display = 'block';
      document.getElementById('articles-detail-view').style.display = 'none';
    };

    renderFilter();
    renderArticles();
  }

  // --- FORM ---
  function initForm() {
    const form = document.getElementById('form-submission');
    if (!form) return;
    let submitted = false;
    const showError = (field, msg) => {
      const err = document.getElementById(`${field}-error`);
      if (err) { err.textContent = msg; err.classList.remove('hidden'); }
      document.getElementById(`${field}-wrapper`)?.classList.add('ina-text-field__wrapper--error');
    };
    const showSuccess = (field) => {
      document.getElementById(`${field}-error`)?.classList.add('hidden');
      document.getElementById(`${field}-wrapper`)?.classList.remove('ina-text-field__wrapper--error');
    };
    ['name', 'email', 'password', 'confirmPassword'].forEach(id => {
      document.getElementById(id)?.addEventListener('input', () => { if (submitted) showSuccess(id); });
    });
    document.querySelectorAll('.ina-password-input__toggle-button').forEach(btn => {
      btn.addEventListener('click', () => {
        const input = document.getElementById(btn.getAttribute('data-target'));
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
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError('email', 'Format email tidak valid'); hasError = true; } else showSuccess('email');
      const pass = document.getElementById('password').value;
      if (!pass) { showError('password', 'Kata sandi wajib diisi'); hasError = true; }
      else if (pass.length < 8) { showError('password', 'Minimal 8 karakter'); hasError = true; } else showSuccess('password');
      const confirm = document.getElementById('confirmPassword').value;
      if (confirm !== pass) { showError('confirmPassword', 'Kata sandi tidak cocok'); hasError = true; } else showSuccess('confirmPassword');
      if (!hasError) { showToast('positive', 'Akun berhasil dibuat!', 'Silakan cek email Anda untuk verifikasi.'); form.reset(); submitted = false; }
      else showToast('destructive', 'Gagal membuat akun', 'Mohon periksa kembali inputan Anda.');
    });
  }

  // --- INIT ---
  document.addEventListener('DOMContentLoaded', () => {
    navigateTo('dashboard');
    initDashboard();
    initArticles();
    initForm();
    window.InaUI?.initDrawer?.();
    document.querySelector('.main-wrapper')?.classList.add('transition-[margin]', 'duration-300');
  });
</script>
