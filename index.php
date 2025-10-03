<?php
$pageTitle = "HR 4 Dashboard";
$userName  = "User"; 

// Database connection
include 'db.php';

// Fetch total employees
$totalEmployees = 0;
$result = $conn->query("SELECT COUNT(*) AS total FROM employees");
if ($result && $row = $result->fetch_assoc()) {
    $totalEmployees = $row['total'];
}

// Example dynamic values (replace with DB queries later)
$presentToday     = 0;  // Could fetch dynamically from attendance table
$monthlyPayroll   = "₱145K";
$avgPerformance   = "4.2/5";
$benefitsEnrolled = 13;
$pendingReviews   = 3;

// Fetch recent activities from DB (latest 10)
$recentActivities = [];
$sql = "SELECT * FROM activities ORDER BY created_at DESC LIMIT 10";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recentActivities[] = [
            'id' => $row['id'],
            'icon' => ($row['status'] == 'warning') ? 'alert-triangle' : (($row['status'] == 'success') ? 'dollar-sign' : 'user-plus'),
            'text' => $row['text'],
            'time' => date('g:i A, M d', strtotime($row['created_at'])),
            'status' => $row['status']
        ];
    }
}

// Quick actions
$quickActions = ["Add New Employee", "Process Payroll", "Generate Reports", "Manage Benefits"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $pageTitle; ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="icon" type="png" href="logo2.png" />
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      lucide.createIcons();
    });
  </script>
</head>
<body class="h-screen overflow-hidden">
  <div class="flex h-full">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-y-auto">

      <!-- Header -->
      <div class="flex items-center justify-between border-b py-4 bg-white sticky top-0 z-50 px-6">
        <div class="flex items-center gap-4">
          <button id="sidebarToggle" class="text-gray-600 hover:text-gray-800 focus:outline-none">
            <i id="toggleIcon" data-lucide="menu" class="w-6 h-6"></i>
          </button>
          <h1 class="text-lg font-bold text-gray-800">HR 4 MANAGEMENT SYSTEM</h1>
        </div>
        <h1 class="text-lg font-semibold text-gray-600">Hotel & Restaurant NAME</h1>
      </div>

      <main class="p-6 space-y-4">

        <div class="flex items-center justify-between border-b py-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
            <p class="text-gray-500 text-sm">Welcome to your HR management system</p>
          </div>

          <!-- User Dropdown -->
          <div class="relative">
            <button id="userDropdownToggle" class="flex items-center gap-2 px-3 py-2 rounded bg-gray-200 hover:bg-gray-300">
              <i data-lucide="user" class="w-5 h-5"></i>
              <span><?php echo $userName; ?></span>
            </button>
            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg">
              <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
              <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Settings</a>
              <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
            </div>
          </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="px-6 mt-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
            
            <!-- Total Employees -->
            <div class="shadow hover:shadow-lg transition-shadow duration-200 bg-white rounded-2xl p-6">
              <div class="flex items-center justify-between pb-2">
                <h2 class="text-sm font-medium text-gray-500">Total Employees</h2>
                <i data-lucide="users" class="h-5 w-5 text-gray-500"></i>
              </div>
              <div class="text-2xl font-bold text-gray-900 mb-1"><?php echo $totalEmployees; ?></div>
              <p class="text-xs text-green-600 flex items-center gap-1">
                <i data-lucide="arrow-up-right" class="h-4 w-4"></i> +5.2% from last month
              </p>
            </div>

            <!-- Present Today -->
            <div class="shadow hover:shadow-lg transition-shadow duration-200 bg-white rounded-2xl p-6">
              <div class="flex items-center justify-between pb-2">
                <h2 class="text-sm font-medium text-gray-500">Present Today</h2>
                <i data-lucide="calendar-check" class="h-5 w-5 text-gray-500"></i>
              </div>
              <div class="text-2xl font-bold text-gray-900 mb-1"><?php echo $presentToday; ?></div>
              <p class="text-xs text-red-600 flex items-center gap-1">
                <i data-lucide="arrow-down-right" class="h-4 w-4"></i> -2.1% from yesterday
              </p>
            </div>

            <!-- Monthly Payroll -->
            <div class="shadow hover:shadow-lg transition-shadow duration-200 bg-white rounded-2xl p-6">
              <div class="flex items-center justify-between pb-2">
                <h2 class="text-sm font-medium text-gray-500">Monthly Payroll</h2>
                <i data-lucide="wallet" class="h-5 w-5 text-gray-500"></i>
              </div>
              <div class="text-2xl font-bold text-gray-900 mb-1"><?php echo $monthlyPayroll; ?></div>
              <p class="text-xs text-green-600 flex items-center gap-1">
                <i data-lucide="arrow-up-right" class="h-4 w-4"></i> +3.8% from last month
              </p>
            </div>

            <!-- Avg Performance -->
            <div class="shadow hover:shadow-lg transition-shadow duration-200 bg-white rounded-2xl p-6">
              <div class="flex items-center justify-between pb-2">
                <h2 class="text-sm font-medium text-gray-500">Avg Performance</h2>
                <i data-lucide="trending-up" class="h-5 w-5 text-gray-500"></i>
              </div>
              <div class="text-2xl font-bold text-gray-900 mb-1"><?php echo $avgPerformance; ?></div>
              <p class="text-xs text-green-600 flex items-center gap-1">
                <i data-lucide="arrow-up-right" class="h-4 w-4"></i> +1.5% from last quarter
              </p>
            </div>

            <!-- Benefits Enrolled -->
            <div class="shadow hover:shadow-lg transition-shadow duration-200 bg-white rounded-2xl p-6">
              <div class="flex items-center justify-between pb-2">
                <h2 class="text-sm font-medium text-gray-500">Benefits Enrolled</h2>
                <i data-lucide="heart" class="h-5 w-5 text-gray-500"></i>
              </div>
              <div class="text-2xl font-bold text-gray-900 mb-1"><?php echo $benefitsEnrolled; ?></div>
              <p class="text-xs text-green-600 flex items-center gap-1">
                <i data-lucide="arrow-up-right" class="h-4 w-4"></i> +8.7% from last month
              </p>
            </div>

            <!-- Pending Reviews -->
            <div class="shadow hover:shadow-lg transition-shadow duration-200 bg-white rounded-2xl p-6">
              <div class="flex items-center justify-between pb-2">
                <h2 class="text-sm font-medium text-gray-500">Pending Reviews</h2>
                <i data-lucide="alert-triangle" class="h-5 w-5 text-gray-500"></i>
              </div>
              <div class="text-2xl font-bold text-gray-900 mb-1"><?php echo $pendingReviews; ?></div>
              <p class="text-xs text-gray-500 flex items-center gap-1">
                <i data-lucide="minus" class="h-4 w-4"></i> Awaiting feedback
              </p>
            </div>

          </div>

          <!-- Recent Activity & Quick Actions -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow p-6">
              <h2 class="text-base font-semibold text-gray-800 mb-4">Recent Activity</h2>
              <ul class="divide-y divide-gray-100">
                <?php foreach ($recentActivities as $activity): ?>
                  <li class="flex items-center justify-between px-3 py-3 hover:bg-gray-100 rounded-lg transition">
                    <div class="flex items-center gap-3">
                      <i data-lucide="<?php echo $activity['icon']; ?>" class="h-5 w-5 text-gray-500"></i>
                      <div>
                        <p class="text-gray-700"><?php echo $activity['text']; ?></p>
                        <p class="text-xs text-gray-400"><?php echo $activity['time']; ?></p>
                      </div>
                    </div>
                    <?php if ($activity['status'] === "warning"): ?>
                      <span class="text-xs px-2 py-1 rounded-md bg-yellow-200 text-yellow-800 font-medium">warning</span>
                    <?php elseif ($activity['status'] === "success"): ?>
                      <span class="text-xs px-2 py-1 rounded-md bg-green-200 text-green-800 font-medium">success</span>
                    <?php elseif ($activity['status'] === "info"): ?>
                      <span class="text-xs px-2 py-1 rounded-md bg-blue-200 text-blue-800 font-medium">info</span>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow p-4 max-h-72">
              <h2 class="text-sm font-semibold text-gray-800 mb-3">Quick Actions</h2>
              <ul class="divide-y divide-gray-100 text-sm">
                <?php foreach ($quickActions as $action): ?>
                  <li class="px-3 py-2 hover:bg-gray-100 rounded-lg transition cursor-pointer">
                    <?php echo $action; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>

        </div>
      </main>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const sidebarToggle = document.getElementById("sidebarToggle");
      const sidebar = document.getElementById("sidebar");
      const sidebarTexts = document.querySelectorAll(".sidebar-text");
      const userDropdownToggle = document.getElementById("userDropdownToggle");
      const userDropdown = document.getElementById("userDropdown");
      const logoExpanded = document.querySelector(".sidebar-logo-expanded");
      const logoCollapsed = document.querySelector(".sidebar-logo-collapsed");

      sidebarToggle.addEventListener("click", function () {
        sidebar.classList.toggle("w-64");
        sidebar.classList.toggle("w-20");

        if (sidebar.classList.contains("w-20")) {
          sidebarTexts.forEach(el => el.classList.add("hidden"));
          logoExpanded.classList.add("hidden");
          logoCollapsed.classList.remove("hidden");
        } else {
          sidebarTexts.forEach(el => el.classList.remove("hidden"));
          logoExpanded.classList.remove("hidden");
          logoCollapsed.classList.add("hidden");
        }
        lucide.createIcons();
      });

      userDropdownToggle.addEventListener("click", function () {
        userDropdown.classList.toggle("hidden");
      });

      document.addEventListener("click", function (event) {
        if (!userDropdown.contains(event.target) && !userDropdownToggle.contains(event.target)) {
          userDropdown.classList.add("hidden");
        }
      });
    });
  </script>
</body>
</html>
