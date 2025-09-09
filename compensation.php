<?php
$pageTitle = "HR 4 Compensation";
$userName  = "User"; 
$hotelName = "Hotel & Restaurant NAME";


include 'db.php';


// Total Compensation
$sql = "SELECT SUM(salary) AS total FROM compensation";
$totalComp = $conn->query($sql)->fetch_assoc()['total'];
$totalCompensation = "₱" . number_format($totalComp, 0);

// Average Salary
$sql = "SELECT AVG(salary) AS avg FROM compensation";
$avgSalary = $conn->query($sql)->fetch_assoc()['avg'];
$averageSalary = "₱" . number_format($avgSalary, 0);

// Reviews Due in next 30 days
$sql = "SELECT COUNT(*) AS due FROM compensation WHERE next_review <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
$reviewsDue = $conn->query($sql)->fetch_assoc()['due'];

// High Performers
$sql = "SELECT COUNT(*) AS high FROM compensation WHERE rating >= 4.5";
$highPerformers = $conn->query($sql)->fetch_assoc()['high'];


$result = $conn->query($sql);
$employees = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "role" => $row['role'],
            "salary" => "₱" . number_format($row['salary'], 0),
            "rating" => $row['rating'] . "/5.0",
            "lastIncrease" => "₱" . number_format($row['last_increase'], 0) . 
                              " (" . $row['last_increase_percent'] . "%)",
            "lastIncreaseDate" => date("m/d/Y", strtotime($row['last_increase_date'])) . 
                                  " - " . $row['increase_reason'],
            "marketRange" => "₱" . number_format($row['market_min'], 0) . 
                             " - ₱" . number_format($row['market_max'], 0),
            "median" => "₱" . number_format($row['median'], 0),
            "nextReview" => date("m/d/Y", strtotime($row['next_review'])),
            "marketPos" => $row['market_pos']
        ];
    }
}

// ===================
// Helper function for rating color
// ===================
function getRatingColor($rating) {
  $value = floatval(substr($rating, 0, 3)); 
  if ($value >= 4.0) {
    return "text-green-600 font-semibold";
  } elseif ($value >= 3.0) {
    return "text-yellow-600 font-semibold";
  } else {
    return "text-red-600 font-semibold";
  }
}
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
   
    <?php include 'sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-y-auto">

      <!-- Sticky Header -->
      <div class="flex items-center justify-between border-b py-4 bg-white sticky top-0 z-50 px-6">
        <div class="flex items-center gap-4">
          <button id="sidebarToggle" class="text-gray-600 hover:text-gray-800 focus:outline-none">
            <i id="toggleIcon" data-lucide="menu" class="w-6 h-6"></i>
          </button>
          <h1 class="text-lg font-bold text-gray-800">HR 4 MANAGEMENT SYSTEM</h1>
        </div>
        <h1 class="text-lg font-semibold text-gray-600"><?php echo $hotelName; ?></h1>
      </div>

      <main class="p-6 space-y-6">
        
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Compensation Planning</h1>
            <p class="text-gray-500 text-sm">Manage salary adjustments and performance-based incentives</p>
          </div>
          <button class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-5 h-5"></i> Salary Adjustment
          </button>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="shadow bg-white rounded-2xl p-6 flex items-center justify-between">
            <div>
              <h2 class="text-sm font-medium text-gray-500">Total Compensation</h2>
              <div class="text-2xl font-bold text-gray-900"><?php echo $totalCompensation; ?></div>
              <p class="text-xs text-gray-500">Annual</p>
            </div>
            <div class="bg-green-100 text-green-600 p-3 rounded-full">
              <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
          </div>

          <div class="shadow bg-white rounded-2xl p-6 flex items-center justify-between">
            <div>
              <h2 class="text-sm font-medium text-gray-500">Average Salary</h2>
              <div class="text-2xl font-bold text-gray-900"><?php echo $averageSalary; ?></div>
              <p class="text-xs text-gray-500">Monthly</p>
            </div>
            <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
              <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
            </div>
          </div>

          <div class="shadow bg-white rounded-2xl p-6 flex items-center justify-between">
            <div>
              <h2 class="text-sm font-medium text-gray-500">Reviews Due</h2>
              <div class="text-2xl font-bold text-gray-900"><?php echo $reviewsDue; ?></div>
              <p class="text-xs text-gray-500">Next 30 days</p>
            </div>
            <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full">
              <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
          </div>

          <div class="shadow bg-white rounded-2xl p-6 flex items-center justify-between">
            <div>
              <h2 class="text-sm font-medium text-gray-500">High Performers</h2>
              <div class="text-2xl font-bold text-gray-900"><?php echo $highPerformers; ?></div>
              <p class="text-xs text-gray-500">Rating ≥ 4.5</p>
            </div>
            <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
              <i data-lucide="award" class="w-6 h-6"></i>
            </div>
          </div>
        </div>

        <!-- Employee Compensation Overview -->
        <div class="space-y-6">
          <h2 class="text-xl font-semibold text-gray-800">Employee Compensation Overview</h2>

          <?php foreach ($employees as $emp): ?>
            <div class="bg-white rounded-2xl shadow p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                  <!-- Avatar pulled from user_profile.php -->
                  <img src="user_profile.php?id=<?php echo $emp['id']; ?>&type=avatar"
                       alt="<?php echo $emp['name']; ?> Avatar"
                       class="w-10 h-10 rounded-full border border-gray-300 object-cover">
                  <div>
                    <h3 class="text-lg font-semibold text-gray-800"><?php echo $emp['name']; ?></h3>
                    <p class="text-sm text-gray-500"><?php echo $emp['role']; ?></p>
                  </div>
                </div>
                <div class="flex gap-2">
                  <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-lg text-sm">Review Due</span>
                  <button class="bg-blue-900 hover:bg-blue-800 text-white px-3 py-1 rounded-lg text-sm">Adjust Salary</button>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div>
                  <p class="text-gray-500">Current Salary</p>
                  <p class="font-bold text-gray-900"><?php echo $emp['salary']; ?></p>
                  <div class="mt-2"></div> 
                  <p class="text-gray-500">Performance Rating</p>
                  <p class="<?php echo getRatingColor($emp['rating']); ?>"><?php echo $emp['rating']; ?></p>
                </div>
                <div>
                  <p class="text-gray-500">Last Increase</p>
                  <p class="font-bold text-gray-900"><?php echo $emp['lastIncrease']; ?></p>
                  <p class="text-gray-400"><?php echo $emp['lastIncreaseDate']; ?></p>
                </div>
                <div>
                  <p class="text-gray-500">Market Range</p>
                  <p class="font-bold text-gray-900"><?php echo $emp['marketRange']; ?></p>
                  <p class="text-gray-400">Median: <?php echo $emp['median']; ?></p>
                </div>
                <div>
                  <p class="text-gray-500">Next Review</p>
                  <p class="font-bold text-gray-900"><?php echo $emp['nextReview']; ?></p>
                  <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                    <div class="bg-blue-900 h-2 rounded-full" style="width: <?php echo $emp['marketPos']; ?>%"></div>
                  </div>
                  <p class="text-xs text-gray-400">Market position: <?php echo $emp['marketPos']; ?>%</p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
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
