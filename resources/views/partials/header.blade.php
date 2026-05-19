<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-40">
  <div class="flex items-center gap-3">
    <!-- Hamburger (Mobile) -->
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

  <div class="flex items-center gap-3">
    @auth
      {{-- Notification Bell --}}
      <button class="relative p-2 text-gray-400 hover:text-gray-500 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
          <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
        </svg>
      </button>

      <div class="w-px h-8 bg-gray-200"></div>

      {{-- User info --}}
      <div class="flex items-center gap-2 text-sm">
        <div class="w-8 h-8 rounded-full bg-[#b42b2d] text-white flex items-center justify-center font-semibold text-xs shrink-0">
          {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <span class="text-gray-700 font-medium hidden md:block max-w-[120px] truncate">{{ auth()->user()->name }}</span>
      </div>

      <div class="w-px h-8 bg-gray-200"></div>

      {{-- Logout --}}
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="ina-button ina-button--secondary ina-button--sm flex items-center gap-1.5">
          <i class="ti ti-logout text-sm"></i>
          <span>Keluar</span>
        </button>
      </form>
    @endauth
  </div>
</header>
