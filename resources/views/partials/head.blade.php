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

  /* Tooltip only visible when sidebar is collapsed */
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
