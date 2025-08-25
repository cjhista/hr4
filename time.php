

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Time and Attendance</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="icon" type="image/png" href="/web/picture/logo2.png" />

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      lucide.createIcons();
    });
  </script>
</head>
<body class="h-screen overflow-hidden">

  <!-- FLEX LAYOUT: Sidebar + Main -->
  <div class="flex h-full">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-y-auto">

      <!-- Main Top Header (inside content) -->
      <main class="p-6 space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between border-b py-6">
          <!-- Left: Title -->
          <h2 class="text-xl font-semibold text-gray-800" id="main-content-title">Time and Attendance </h2>

  <?php include 'profile.php'; ?>


        </div>
<!-- Second Header: Submodules -->
<div class="bg-gray-100 border-b px-6 py-3 flex gap-4 text-sm font-medium text-gray-700">
  <a href="time.php" class="hover:text-blue-600 transition-colors">Attendance Logs</a>
  <a href="#" class="hover:text-blue-600 transition-colors">Shift Management</a>
  <a href="#" class="hover:text-blue-600 transition-colors">Overtime Requests</a>
  <a href="#" class="hover:text-blue-600 transition-colors">Holiday Setup</a>
  <a href="#" class="hover:text-blue-600 transition-colors">Late In / Early Out</a>
</div>
        <!-- Page Body -->
        <p class="text-gray-600"></p>
      </main>
  

    </div>


    
  </div>


  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const userDropdownToggle = document.getElementById("userDropdownToggle");
      const userDropdown = document.getElementById("userDropdown");

      userDropdownToggle.addEventListener("click", function () {
        userDropdown.classList.toggle("hidden");
      });

      // Close dropdown when clicking outside
      document.addEventListener("click", function (event) {
        if (!userDropdown.contains(event.target) && !userDropdownToggle.contains(event.target)) {
          userDropdown.classList.add("hidden");
        }
      });
    });
  </script>
</body>
</html>
