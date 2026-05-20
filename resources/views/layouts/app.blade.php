<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'IDDS Starter')</title>
    @include('partials.styles')
  </head>
  <body class="bg-gray-50 min-h-screen flex">

    <!-- Sidebar Overlay (Mobile) -->
    <div
      id="sidebar-overlay"
      class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"
      onclick="toggleSidebar()"
    ></div>

    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="main-wrapper flex-1 flex flex-col min-w-0 transition-all duration-300">
      @include('partials.header')

      <main class="p-6 lg:p-8 flex-1">
        @yield('content')
      </main>
    </div>

    <!-- Toast Container -->
    <div
      class="ina-toast-container ina-toast-container--top-right"
      id="toast-container"
      style="display: none; max-height: calc(100vh - 32px); overflow: auto"
    ></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @include('partials.scripts')
  </body>
</html>
