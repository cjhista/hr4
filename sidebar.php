
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sidebar</title>
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- In your <head> tag -->
<link rel="icon" type="image/png" href="/web/picture/logo2.png" />

  <!-- Tailwind CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<!-- Include this in the <head> if Tailwind isn't already loaded -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">


</head>
<body class="flex">
<!-- Sidebar -->
<div id="sidebar" class="bg-gray-800 text-white w-64 transition-all duration-300 h-screen flex flex-col">

  <!-- Logo & Toggle -->
  <div class="flex items-center justify-between px-4 py-4 border-b border-gray-700">
    
    <!-- Large logo for expanded sidebar -->
    <img src="logo.png" alt="Logo" class="h-14 sidebar-logo-expanded" />

    <!-- Small logo for collapsed sidebar (initially hidden) -->
    <img src="logo2.png" alt="Logo" class="h-14 sidebar-logo-collapsed hidden" />

    <button id="sidebar-toggle" class="text-white focus:outline-none">
      <i data-lucide="chevron-left" class="w-5 h-5 transition-transform"></i>
    </button>
  </div>
 <?php include 'chatbot.php'; ?>

   <script>
  document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("sidebar-toggle");
    const sidebar = document.getElementById("sidebar");
    const logoExpanded = document.querySelector(".sidebar-logo-expanded");
    const logoCollapsed = document.querySelector(".sidebar-logo-collapsed");
    const sidebarText = document.querySelectorAll(".sidebar-text");

    toggleBtn.addEventListener("click", () => {
      // Width & overflow toggle
      sidebar.classList.toggle("w-64");
      sidebar.classList.toggle("w-20");
      sidebar.classList.toggle("overflow-hidden");

      // Toggle logos
      if (logoExpanded && logoCollapsed) {
        logoExpanded.classList.toggle("hidden");
        logoCollapsed.classList.toggle("hidden");
      }

      // Toggle sidebar text
      sidebarText.forEach(el => {
        el.classList.toggle("hidden");
      });

      // Rotate the toggle icon (assuming <i> inside button)
      const icon = toggleBtn.querySelector("i");
      if (icon) {
        icon.classList.toggle("rotate-180");
      }
    });

    // Initialize Lucide icons
    if (typeof lucide !== "undefined" && lucide.createIcons) {
      lucide.createIcons();
    }
  });
</script>

<!-- Navigation -->
<nav class="flex-1 px-2 py-4 space-y-2">
  <?php
    $currentPage = $_SERVER['PHP_SELF'];
  ?>

<a href="/web/hr3/index.php"
   class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 <?php echo ($currentPage == '/web/hr3/index.php') ? 'bg-gray-700 text-white' : ''; ?>">
  <i data-lucide="Bar-chart-3" class="w-5 h-5"></i>
  <span class="sidebar-text">HR Analytics Dashboard</span>
</a>


  <a href="/web/hr3/timeAndattendance/time.php"
     class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 <?php echo ($currentPage == '/web/hr3/timeAndattendance/time.php') ? 'bg-gray-700 text-white' : ''; ?>">
    <i data-lucide="users" class="w-5 h-5"></i>
    <span class="sidebar-text">Core Human Capital</span>
  </a>

  <a href="/web/hr3/shift/shift.php"
     class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 <?php echo ($currentPage == '/web/hr3/shift/shift.php') ? 'bg-gray-700 text-white' : ''; ?>">
    <i data-lucide="wallet" class="w-5 h-5"></i>
    <span class="sidebar-text">Payroll Management</span>
  </a>

  <a href="/web/hr3/timesheet/timesheet.php"
     class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 <?php echo ($currentPage == '/web/hr3/timesheet/timesheet.php') ? 'bg-gray-700 text-white' : ''; ?>">
    <i data-lucide="clipboard-list" class="w-5 h-5"></i>
    <span class="sidebar-text">Compensation Planning</span>
  </a>


  <a href="/web/hr3/claims/claims.php"
     class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 <?php echo ($currentPage == '/web/hr3/claims/claims.php') ? 'bg-gray-700 text-white' : ''; ?>">
    <i data-lucide="heart-pulse" class="w-5 h-5"></i>
    <span class="sidebar-text">HMO & Benefits Administration</span>
  </a>
</nav>


  </div>

</body>
</html>
