<aside
  id="sidebar"
  class="fixed lg:sticky top-0 left-0 h-screen bg-white border-r border-gray-200 z-50 flex flex-col -translate-x-full lg:translate-x-0 w-[248px] xl:w-[264px]"
>
  <!-- Sidebar Header -->
  <div id="sidebar-header" class="h-16 flex items-center border-b border-gray-200 px-6 gap-3 transition-[padding] duration-300">
    <div class="flex items-center gap-2 overflow-hidden flex-1" id="sidebar-logo-container">
      <img src="/images/logo.png" class="h-12 w-auto" alt="Logo">
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
  <nav class="flex-1 p-4 space-y-0.5 overflow-y-auto overflow-x-hidden sidebar-nav">

    @auth
      @php $role = auth()->user()->role?->name; @endphp

      {{-- Admin area --}}
      @if (in_array($role, ['super_admin', 'admin_unit', 'auditor']))
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1 px-2 pt-1">Admin</div>

        <a href="{{ route('admin.dashboard') }}"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#2596be]/10 text-[#2596be]' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
          <i class="ti ti-layout-dashboard text-lg min-w-[20px]"></i>
          <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.documents.index') }}"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.documents.*') ? 'bg-[#2596be]/10 text-[#2596be]' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
          <i class="ti ti-files text-lg min-w-[20px]"></i>
          <span>Dokumen</span>
        </a>

        @if ($role === 'super_admin')
          <a href="{{ route('admin.external-regulations.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.external-regulations.*') ? 'bg-[#2596be]/10 text-[#2596be]' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="ti ti-gavel text-lg min-w-[20px]"></i>
            <span>Regulasi Eksternal</span>
          </a>
        @endif

        @if ($role === 'super_admin')
          <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1 px-2 pt-3">Pengaturan</div>

          <a href="{{ route('admin.users.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-[#2596be]/10 text-[#2596be]' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="ti ti-users text-lg min-w-[20px]"></i>
            <span>Pengguna</span>
          </a>

          <a href="{{ route('admin.units.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.units.*') ? 'bg-[#2596be]/10 text-[#2596be]' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="ti ti-building-hospital text-lg min-w-[20px]"></i>
            <span>Unit</span>
          </a>

          <a href="{{ route('admin.document-types.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.document-types.*') ? 'bg-[#2596be]/10 text-[#2596be]' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="ti ti-file-description text-lg min-w-[20px]"></i>
            <span>Jenis Dokumen</span>
          </a>
        @endif

        @if (in_array($role, ['super_admin', 'auditor']))
          <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1 px-2 pt-3">Laporan</div>

          <a href="{{ route('admin.reports.master-document-list') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.reports.master-document-list') ? 'bg-[#2596be]/10 text-[#2596be]' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="ti ti-report text-lg min-w-[20px]"></i>
            <span>Daftar Induk Dokumen</span>
          </a>

          <a href="{{ route('admin.reports.activity-log') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.reports.activity-log') ? 'bg-[#2596be]/10 text-[#2596be]' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="ti ti-list-details text-lg min-w-[20px]"></i>
            <span>Log Aktivitas</span>
          </a>

          <a href="{{ route('admin.reports.usage-statistics') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.reports.usage-statistics') ? 'bg-[#2596be]/10 text-[#2596be]' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="ti ti-chart-bar text-lg min-w-[20px]"></i>
            <span>Statistik Penggunaan</span>
          </a>
        @endif
      @endif

      {{-- Portal link --}}
      <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1 px-2 pt-3">Portal</div>

      <a href="{{ route('portal.documents.index') }}"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('portal.*') ? 'bg-[#2596be]/10 text-[#2596be]' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
        <i class="ti ti-search text-lg min-w-[20px]"></i>
        <span>Cari Dokumen</span>
      </a>
    @endauth

  </nav>

  <!-- Sidebar Footer -->
  @auth
  {{-- <div class="p-4 border-t border-gray-200">
    <div class="flex items-center gap-3 p-2 rounded-lg bg-gray-50">
      <div class="w-8 h-8 min-w-[32px] bg-[#2596be] text-white rounded-full flex items-center justify-center text-sm font-semibold">
        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->role?->name }}</p>
      </div>
    </div>
  </div> --}}
  @endauth
</aside>
