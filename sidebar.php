<?php
$currentPage = basename($_SERVER['PHP_SELF']); 
?>
<aside id="sidebar" class="w-64 bg-gray-800 text-gray-200 flex flex-col transition-all duration-300 ease-in-out">
  <!-- Header with Logo and Toggle Button -->
  <div class="flex items-center justify-between px-4 py-6 border-b border-gray-700">
    <div class="flex items-center">
      <img src="logo.png" alt="Logo" class="h-12 sidebar-logo-expanded transition-opacity duration-300" />
      <img src="logo2.png" alt="Logo" class="h-12 sidebar-logo-collapsed hidden transition-opacity duration-300" />
    </div>
    <!-- Toggle Button -->
    <button id="sidebar-toggle" class="p-1 rounded-lg hover:bg-gray-700 transition-colors">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
      </svg>
    </button>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 px-3 py-6 space-y-6 overflow-y-auto">
    
    <!-- HR Analytics -->
    <div>
      <p class="sidebar-text text-xs font-semibold text-gray-400 px-3 mb-2 uppercase tracking-wide">HR Analytics</p>
      <a href="dashboard.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors
        <?php echo ($currentPage == 'dashboard.php') ? 'bg-gray-700 text-white font-semibold' : 'text-gray-300'; ?>">
        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
        <span class="sidebar-text">Dashboard</span>
      </a>
    </div>

    <!-- Human Capital -->
    <div>
      <p class="sidebar-text text-xs font-semibold text-gray-400 px-3 mb-2 uppercase tracking-wide">Core Human Capital</p>
      <a href="employees.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors
        <?php echo ($currentPage == 'employees.php') ? 'bg-gray-700 text-white font-semibold' : 'text-gray-300'; ?>">
        <i data-lucide="users" class="w-5 h-5"></i>
        <span class="sidebar-text">Employees</span>
      </a>
      <a href="attendance.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors
        <?php echo ($currentPage == 'attendance.php') ? 'bg-gray-700 text-white font-semibold' : 'text-gray-300'; ?>">
        <i data-lucide="clock" class="w-5 h-5"></i>
        <span class="sidebar-text">Attendance</span>
      </a>
    </div>

    <!-- Compensation -->
    <div>
      <p class="sidebar-text text-xs font-semibold text-gray-400 px-3 mb-2 uppercase tracking-wide">Compensation And Planning</p>
      <a href="compensation.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors
        <?php echo ($currentPage == 'compensation.php') ? 'bg-gray-700 text-white font-semibold' : 'text-gray-300'; ?>">
        <i data-lucide="trending-up" class="w-5 h-5"></i>
        <span class="sidebar-text">Compensation</span>
      </a>
    </div>

    <!-- Payroll -->
    <div>
      <p class="sidebar-text text-xs font-semibold text-gray-400 px-3 mb-2 uppercase tracking-wide">Process Payroll</p>
      <a href="payroll.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors
        <?php echo ($currentPage == 'payroll.php') ? 'bg-gray-700 text-white font-semibold' : 'text-gray-300'; ?>">
        <i data-lucide="wallet" class="w-5 h-5"></i>
        <span class="sidebar-text">Payroll</span>
      </a>
    </div>

    <!-- Benefits -->
    <div>
      <p class="sidebar-text text-xs font-semibold text-gray-400 px-3 mb-2 uppercase tracking-wide">HMO And Benefits</p>
      <a href="benefits.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors
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
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors
        <?php echo ($currentPage == 'profile.php') ? 'bg-gray-700 text-white font-semibold' : 'text-gray-300'; ?>">
        <i data-lucide="settings" class="w-5 h-5"></i>
        <span class="sidebar-text">Profile Settings</span>
      </a>

      <!-- Logout -->
      <a href="logout.php"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-red-700 text-red-400 mt-2 transition-colors">
        <i data-lucide="log-out" class="w-5 h-5"></i>
        <span class="sidebar-text">Logout</span>
      </a>
    </div>

  </nav>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById('sidebar');
  const toggleButton = document.getElementById('sidebar-toggle');
  const sidebarTexts = document.querySelectorAll('.sidebar-text');
  const logoExpanded = document.querySelector('.sidebar-logo-expanded');
  const logoCollapsed = document.querySelector('.sidebar-logo-collapsed');
  
  // Check if sidebar state is saved in localStorage
  const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
  
  // Apply initial state
  if (isCollapsed) {
    collapseSidebar();
  }
  
  // Toggle sidebar
  toggleButton.addEventListener('click', function() {
    if (sidebar.classList.contains('w-64')) {
      collapseSidebar();
      localStorage.setItem('sidebarCollapsed', 'true');
    } else {
      expandSidebar();
      localStorage.setItem('sidebarCollapsed', 'false');
    }
  });
  
  function collapseSidebar() {
    sidebar.classList.remove('w-64');
    sidebar.classList.add('w-16');
    
    // Hide text elements
    sidebarTexts.forEach(text => {
      text.classList.add('hidden');
    });
    
    // Toggle logos
    logoExpanded.classList.add('hidden');
    logoCollapsed.classList.remove('hidden');
    
    // Rotate toggle button
    toggleButton.querySelector('svg').style.transform = 'rotate(180deg)';
  }
  
  function expandSidebar() {
    sidebar.classList.remove('w-16');
    sidebar.classList.add('w-64');
    
    // Show text elements
    sidebarTexts.forEach(text => {
      text.classList.remove('hidden');
    });
    
    // Toggle logos
    logoExpanded.classList.remove('hidden');
    logoCollapsed.classList.add('hidden');
    
    // Rotate toggle button back
    toggleButton.querySelector('svg').style.transform = 'rotate(0deg)';
  }

  // ================= AUTO LOGOUT FUNCTIONALITY =================
  let warningTimer;
  let logoutTimer;

  function startTimers() {
    // Show warning after 25 minutes
    warningTimer = setTimeout(showWarning,  60 * 1000);
    // Logout after 30 minutes
    logoutTimer = setTimeout(logoutUser, 2 * 60 * 1000);
  }

  function resetTimers() {
    clearTimeout(warningTimer);
    clearTimeout(logoutTimer);
    startTimers();
    hideWarning();
  }

  function showWarning() {
    // Create warning modal
    const warningModal = document.createElement('div');
    warningModal.innerHTML = `
      <div id="inactivity-warning" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-md">
          <h3 class="text-lg font-bold text-gray-800 mb-2">Session Timeout Warning</h3>
          <p class="text-gray-600 mb-4">You will be logged out due to inactivity in 1 minutes. Move your mouse or click anywhere to continue.</p>
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">Time remaining: </strong>
            <span id="countdown">5:00</span>
          </div>
          <button onclick="continueSession()" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Continue Session
          </button>
        </div>
      </div>
    `;
    document.body.appendChild(warningModal);
    
    // Start countdown
    let timeLeft = 60; // 5 minutes in seconds
    const countdownElement = document.getElementById('countdown');
    
    const countdownInterval = setInterval(() => {
      timeLeft--;
      const minutes = Math.floor(timeLeft / 60);
      const seconds = timeLeft % 60;
      countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
      
      if (timeLeft <= 0) {
        clearInterval(countdownInterval);
      }
    }, 1000);
  }

  function hideWarning() {
    const warning = document.getElementById('inactivity-warning');
    if (warning) {
      warning.remove();
    }
  }

  window.continueSession = function() {
    resetTimers();
  }

  function logoutUser() {
    window.location.href = 'logout.php';
  }

  // Event listeners for user activity
  window.addEventListener('load', resetTimers);
  window.addEventListener('mousemove', resetTimers);
  window.addEventListener('mousedown', resetTimers);
  window.addEventListener('touchstart', resetTimers);
  window.addEventListener('click', resetTimers);
  window.addEventListener('keypress', resetTimers);
  window.addEventListener('scroll', resetTimers);

  // Start the timers initially
  startTimers();
});
</script>