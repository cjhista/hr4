<?php
$currentPage = basename($_SERVER['PHP_SELF']); 
?>
<aside id="sidebar" class="w-64 bg-gray-800 text-gray-200 flex flex-col transition-[width] duration-300 ease-in-out">
  <!-- Logo Row -->
  <div class="flex items-center justify-center px-4 py-6 border-b border-gray-700">
    <img src="logo.png" alt="Logo" class="h-12 sidebar-logo-expanded transition-opacity duration-300" />
    <img src="logo2.png" alt="Logo" class="h-12 sidebar-logo-collapsed hidden transition-opacity duration-300" />
  </div>

  <!-- Navigation -->
  <nav class="flex-1 px-3 py-6 space-y-6">
    
    <!-- HR Analytics -->
    <div>
      <p class="sidebar-text text-xs font-semibold text-gray-400 px-3 mb-2 uppercase tracking-wide">HR Analytics</p>
      <a href="dashboard.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 
        <?php echo ($currentPage == 'dashboard.php') ? 'bg-gray-700 text-white font-semibold' : 'text-gray-300'; ?>">
        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
        <span class="sidebar-text">Dashboard</span>
      </a>
    </div>

    <!-- Human Capital -->
    <div>
      <p class="sidebar-text text-xs font-semibold text-gray-400 px-3 mb-2 uppercase tracking-wide">Core Human Capital</p>
      <a href="employees.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 
        <?php echo ($currentPage == 'employees.php') ? 'bg-gray-700 text-white font-semibold' : 'text-gray-300'; ?>">
        <i data-lucide="users" class="w-5 h-5"></i>
        <span class="sidebar-text">Employees</span>
      </a>
      <a href="attendance.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 
        <?php echo ($currentPage == 'attendance.php') ? 'bg-gray-700 text-white font-semibold' : 'text-gray-300'; ?>">
        <i data-lucide="clock" class="w-5 h-5"></i>
        <span class="sidebar-text">Attendance</span>
      </a>
    </div>

    <!-- Compensation -->
    <div>
      <p class="sidebar-text text-xs font-semibold text-gray-400 px-3 mb-2 uppercase tracking-wide">Compensation And Planning</p>
      <a href="compensation.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 
        <?php echo ($currentPage == 'compensation.php') ? 'bg-gray-700 text-white font-semibold' : 'text-gray-300'; ?>">
        <i data-lucide="trending-up" class="w-5 h-5"></i>
        <span class="sidebar-text">Compensation</span>
      </a>
    </div>

    <!-- Payroll -->
    <div>
      <p class="sidebar-text text-xs font-semibold text-gray-400 px-3 mb-2 uppercase tracking-wide">Process Payroll</p>
      <a href="payroll.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 
        <?php echo ($currentPage == 'payroll.php') ? 'bg-gray-700 text-white font-semibold' : 'text-gray-300'; ?>">
        <i data-lucide="wallet" class="w-5 h-5"></i>
        <span class="sidebar-text">Payroll</span>
      </a>
    </div>

    <!-- Benefits -->
    <div>
      <p class="sidebar-text text-xs font-semibold text-gray-400 px-3 mb-2 uppercase tracking-wide">HMO And Benefits</p>
      <a href="benefits.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 
        <?php echo ($currentPage == 'benefits.php') ? 'bg-gray-700 text-white font-semibold' : 'text-gray-300'; ?>">
        <i data-lucide="heart" class="w-5 h-5"></i>
        <span class="sidebar-text">Benefits</span>
      </a>
    </div>

    <!-- Profile Settings -->
    <div class="border-t border-gray-700 pt-4">
      <p class="sidebar-text text-xs font-semibold text-gray-400 px-3 mb-2 uppercase tracking-wide">User</p>

      <!-- Clickable Profile Settings -->
      <a href="profile.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 
        <?php echo ($currentPage == 'profile.php') ? 'bg-gray-700 text-white font-semibold' : 'text-gray-300'; ?>">
        <i data-lucide="settings" class="w-5 h-5"></i>
        <span class="sidebar-text">Profile Settings</span>
      </a>

      <!-- Logout -->
      <a href="logout.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-red-700 text-red-400 mt-2">
        <i data-lucide="log-out" class="w-5 h-5"></i>
        <span class="sidebar-text">Logout</span>
      </a>
    </div>

  </nav>
</aside>
