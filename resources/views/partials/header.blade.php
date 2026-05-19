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
      <div class="relative" id="notif-wrapper">
        <button id="btn-notif-bell"
          class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors rounded-lg hover:bg-gray-100">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
            <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
          </svg>
          <span id="notif-badge"
            class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center hidden">
            0
          </span>
        </button>

        {{-- Dropdown --}}
        <div id="notif-dropdown"
          class="hidden absolute right-0 top-full mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-lg z-50 overflow-hidden">
          <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <span class="text-sm font-semibold text-gray-900">Notifikasi</span>
            <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
              @csrf
              <button type="submit" class="text-xs text-blue-600 hover:underline">Tandai semua dibaca</button>
            </form>
          </div>
          <div id="notif-list" class="max-h-80 overflow-y-auto divide-y divide-gray-50">
            <div class="flex flex-col items-center justify-center py-8 text-gray-400 text-sm" id="notif-empty">
              <i class="ti ti-bell-off text-3xl mb-2"></i>
              Tidak ada notifikasi baru
            </div>
          </div>
        </div>
      </div>

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

@push('scripts')
<script>
$(document).ready(function () {
    function loadNotifications() {
        $.getJSON('{{ route("notifications.unread-count") }}', function (data) {
            var count = data.count;
            if (count > 0) {
                $('#notif-badge').text(count > 9 ? '9+' : count).removeClass('hidden');
            } else {
                $('#notif-badge').addClass('hidden');
            }
        });
    }

    loadNotifications();
    setInterval(loadNotifications, 60000);

    $('#btn-notif-bell').on('click', function (e) {
        e.stopPropagation();
        var $dd = $('#notif-dropdown');
        if ($dd.hasClass('hidden')) {
            loadNotifDropdown();
            $dd.removeClass('hidden');
        } else {
            $dd.addClass('hidden');
        }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#notif-wrapper').length) {
            $('#notif-dropdown').addClass('hidden');
        }
    });

    function loadNotifDropdown() {
        $.getJSON('{{ route("notifications.list") }}', function (data) {
            var $list = $('#notif-list');
            if (data.length === 0) {
                $list.html('<div class="flex flex-col items-center justify-center py-8 text-gray-400 text-sm"><i class="ti ti-bell-off text-3xl mb-2"></i>Tidak ada notifikasi baru</div>');
                return;
            }
            var html = '';
            $.each(data, function (i, n) {
                html += '<a href="' + n.read_url + '" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors' + (n.is_read ? ' opacity-60' : '') + '">';
                html += '<div class="w-8 h-8 rounded-full ' + notifColor(n.type) + ' flex items-center justify-center shrink-0 mt-0.5">';
                html += '<i class="ti ' + notifIcon(n.type) + ' text-sm"></i></div>';
                html += '<div class="flex-1 min-w-0">';
                html += '<p class="text-xs font-semibold text-gray-800 leading-snug">' + escHtml(n.title) + '</p>';
                html += '<p class="text-xs text-gray-500 mt-0.5 line-clamp-2">' + escHtml(n.message) + '</p>';
                html += '<p class="text-[10px] text-gray-400 mt-0.5">' + n.created_at + '</p>';
                html += '</div></a>';
            });
            $list.html(html);
        });
    }

    function notifIcon(type) {
        if (type === 'new_document') return 'ti-file-plus';
        if (type === 'document_obsolete') return 'ti-file-x';
        if (type === 'new_regulation') return 'ti-gavel';
        return 'ti-bell';
    }

    function notifColor(type) {
        if (type === 'new_document') return 'bg-blue-50 text-blue-500';
        if (type === 'document_obsolete') return 'bg-orange-50 text-orange-500';
        if (type === 'new_regulation') return 'bg-purple-50 text-purple-500';
        return 'bg-gray-100 text-gray-500';
    }

    function escHtml(text) {
        return $('<div>').text(text).html();
    }
});
</script>
@endpush
