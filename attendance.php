<?php
$pageTitle = "HR 4 Attendance";
$userName  = "User"; 
$hotelName = "Hotel & Restaurant NAME"; 

// Example attendance data (replace with DB later)
$attendanceSummary = [
    "present"  => 4,
    "absent"   => 1,
    "late"     => 1,
    "overtime" => 1
];

$attendanceRecords = [
    [
        "name" => "Maria Santos",
        "position" => "Front Desk Manager",
        "check_in" => "08:00",
        "check_out" => "17:00",
        "total_hours" => "9h 0m",
        "status" => "PRESENT"
    ],
    [
        "name" => "John Dela Cruz",
        "position" => "Head Chef",
        "check_in" => "09:15",
        "check_out" => "18:30",
        "total_hours" => "9h 15m",
        "status" => "LATE"
    ],
    [
        "name" => "Sarah Wilson",
        "position" => "Housekeeping Supervisor",
        "check_in" => "-",
        "check_out" => "-",
        "total_hours" => "-",
        "status" => "ABSENT"
    ],
    [
        "name" => "Robert Garcia",
        "position" => "Server",
        "check_in" => "07:45",
        "check_out" => "19:30",
        "total_hours" => "11h 45m",
        "status" => "OVERTIME"
    ],
    [
        "name" => "Lisa Reyes",
        "position" => "Receptionist",
        "check_in" => "08:30",
        "check_out" => "17:30",
        "total_hours" => "9h 0m",
        "status" => "PRESENT"
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $pageTitle; ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <link rel="icon" type="image/png" href="logo2.png" />
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      lucide.createIcons();
    });
  </script>
</head>
<body class="h-screen overflow-hidden">
  <div class="flex h-full">

    
    <?php include 'sidebar.php'; ?>

    
    <div class="flex-1 flex flex-col overflow-y-auto">

      <!-- Sticky Header -->
      <div class="flex items-center justify-between border-b py-4 bg-white sticky top-0 z-50 px-6">
        <div class="flex items-center gap-4">
          <!-- Sidebar Toggle Button -->
          <button id="sidebarToggle" class="text-gray-600 hover:text-gray-800 focus:outline-none">
            <i id="toggleIcon" data-lucide="menu" class="w-6 h-6"></i>
          </button>
          <h1 class="text-lg font-bold text-gray-800">HR 4 MANAGEMENT SYSTEM</h1>
        </div>
        <h1 class="text-lg font-semibold text-gray-600"><?php echo $hotelName; ?></h1>
      </div>

      <!-- Alpine wrapper -->
      <main class="p-6 space-y-4" x-data="{ filter: 'all', openFilter: false, openExport: false }">
        
        
        <div class="flex items-center justify-between border-b py-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Attendance</h1>
            <p class="text-gray-500 text-sm">Track employee attendance and working hours</p>
          </div>

          <div class="flex gap-2">
            <!-- Filter Dropdown -->
            <div class="relative" @click.away="openFilter = false">
              <button @click="openFilter = !openFilter"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4"></i> Filter
              </button>
              <div x-show="openFilter" class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                <ul class="py-2 text-sm text-gray-700">
                  <li><button @click="filter='all'; openFilter=false" class="w-full text-left px-4 py-2 hover:bg-gray-100">All</button></li>
                  <li><button @click="filter='PRESENT'; openFilter=false" class="w-full text-left px-4 py-2 hover:bg-gray-100">Present</button></li>
                  <li><button @click="filter='ABSENT'; openFilter=false" class="w-full text-left px-4 py-2 hover:bg-gray-100">Absent</button></li>
                  <li><button @click="filter='LATE'; openFilter=false" class="w-full text-left px-4 py-2 hover:bg-gray-100">Late</button></li>
                  <li><button @click="filter='OVERTIME'; openFilter=false" class="w-full text-left px-4 py-2 hover:bg-gray-100">Overtime</button></li>
                </ul>
              </div>
            </div>

            <!-- Export Dropdown -->
            <div class="relative" @click.away="openExport = false">
              <button @click="openExport = !openExport"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i> Export
              </button>
              <div x-show="openExport" class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                <ul class="py-2 text-sm text-gray-700">
                  <li>
                    <a 
                      :href="'export_attendance.php?type=csv&filter=' + filter + '&hotel=<?php echo urlencode($hotelName); ?>'"
                      class="block px-4 py-2 hover:bg-gray-100"
                    >
                      Export CSV
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="shadow bg-white rounded-2xl p-6 flex justify-between items-center">
            <div>
              <h2 class="text-sm text-gray-500">Present</h2>
              <div class="text-2xl font-bold text-green-600"><?php echo $attendanceSummary['present']; ?></div>
            </div>
            <i data-lucide="clock" class="w-8 h-8 text-green-500"></i>
          </div>
          <div class="shadow bg-white rounded-2xl p-6 flex justify-between items-center">
            <div>
              <h2 class="text-sm text-gray-500">Absent</h2>
              <div class="text-2xl font-bold text-red-600"><?php echo $attendanceSummary['absent']; ?></div>
            </div>
            <i data-lucide="calendar-x" class="w-8 h-8 text-red-500"></i>
          </div>
          <div class="shadow bg-white rounded-2xl p-6 flex justify-between items-center">
            <div>
              <h2 class="text-sm text-gray-500">Late</h2>
              <div class="text-2xl font-bold text-yellow-600"><?php echo $attendanceSummary['late']; ?></div>
            </div>
            <i data-lucide="clock-3" class="w-8 h-8 text-yellow-500"></i>
          </div>
          <div class="shadow bg-white rounded-2xl p-6 flex justify-between items-center">
            <div>
              <h2 class="text-sm text-gray-500">Overtime</h2>
              <div class="text-2xl font-bold text-blue-600"><?php echo $attendanceSummary['overtime']; ?></div>
            </div>
            <i data-lucide="clock-9" class="w-8 h-8 text-blue-500"></i>
          </div>
        </div>

        <!-- Attendance List -->
        <div class="bg-white shadow rounded-2xl mt-6 p-6">
          <h2 class="text-base font-semibold text-gray-800 mb-4">Today's Attendance - <?php echo date("F d, Y"); ?></h2>
          <ul class="divide-y divide-gray-100">
            <?php foreach ($attendanceRecords as $record): ?>
              <li 
                x-show="filter === 'all' || filter === '<?php echo $record['status']; ?>'" 
                class="flex items-center justify-between py-4 px-2 hover:bg-gray-50 rounded-lg transition">
                <div class="flex items-center gap-4">
                  <!-- Replace initials with profile image -->
                  <img src="user_profile.php?name=<?php echo urlencode($record['name']); ?>&type=avatar" 
                       alt="<?php echo $record['name']; ?>" 
                       class="w-10 h-10 rounded-full object-cover border" />
                  <div>
                    <p class="font-medium text-gray-800"><?php echo $record['name']; ?></p>
                    <p class="text-xs text-gray-500"><?php echo $record['position']; ?></p>
                  </div>
                </div>
                <div class="grid grid-cols-4 gap-4 text-center text-sm">
                  <div>
                    <p class="text-gray-500">Check In</p>
                    <p class="font-medium"><?php echo $record['check_in']; ?></p>
                  </div>
                  <div>
                    <p class="text-gray-500">Check Out</p>
                    <p class="font-medium"><?php echo $record['check_out']; ?></p>
                  </div>
                  <div>
                    <p class="text-gray-500">Total Hours</p>
                    <p class="font-medium"><?php echo $record['total_hours']; ?></p>
                  </div>
                  <div>
                    <p class="text-gray-500">Status</p>
                    <?php if($record['status'] === "PRESENT"): ?>
                      <span class="text-xs px-2 py-1 rounded-md bg-green-200 text-green-800 font-medium">PRESENT</span>
                    <?php elseif($record['status'] === "LATE"): ?>
                      <span class="text-xs px-2 py-1 rounded-md bg-yellow-200 text-yellow-800 font-medium">LATE</span>
                    <?php elseif($record['status'] === "ABSENT"): ?>
                      <span class="text-xs px-2 py-1 rounded-md bg-red-200 text-red-800 font-medium">ABSENT</span>
                    <?php elseif($record['status'] === "OVERTIME"): ?>
                      <span class="text-xs px-2 py-1 rounded-md bg-blue-200 text-blue-800 font-medium">OVERTIME</span>
                    <?php endif; ?>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </main>
    </div>
  </div>

  
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const sidebarToggle = document.getElementById("sidebarToggle");
      const sidebar = document.getElementById("sidebar");
      const sidebarTexts = document.querySelectorAll(".sidebar-text");
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
    });
  </script>
</body>
</html>