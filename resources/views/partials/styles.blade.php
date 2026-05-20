<!-- IDDS Styles -->
<link
  rel="stylesheet"
  href="https://unpkg.com/@idds/styles@latest/dist/index.min.css"
/>

<!-- Tailwind CSS (CDN) — harus di <head> agar DOM di-scan sebelum render -->
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<!-- Tabler Icons -->
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"
/>

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

  /* Tooltip hanya tampil saat sidebar collapsed */
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

<style>
  :root,
  [data-brand] {
    --ina-primary-primary: #2596be;
    --ina-primary-600: #1c7aa8;
    --ina-primary-700: #166d96;
  }
</style>

@stack('styles')
