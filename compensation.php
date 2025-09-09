<?php
$pageTitle = "HR 4 Compensation";
$userName  = "User"; 
$hotelName = "Hotel & Restaurant NAME";

// Database connection
$host = "localhost:3307"; 
$user = "root"; 
$pass = ""; 
$db   = "hr4_db";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Compensation summary
$summaryQuery = "
  SELECT 
    SUM(salary) AS totalCompensation,
    AVG(salary) AS averageSalary,
    COUNT(CASE WHEN next_review <= CURDATE() + INTERVAL 30 DAY THEN 1 END) AS reviewsDue,
    COUNT(CASE WHEN rating >= 4.5 THEN 1 END) AS highPerformers
  FROM employees
";
$summary = $conn->query($summaryQuery)->fetch_assoc();

$totalCompensation = "₱" . number_format($summary['totalCompensation'], 0);
$averageSalary     = "₱" . number_format($summary['averageSalary'], 0);
$reviewsDue        = $summary['reviewsDue'];
$highPerformers    = $summary['highPerformers'];

// Employee compensation data
$employees = [];
$employeeQuery = "SELECT id, CONCAT(first_name, ' ', last_name) AS name, position, salary, rating, 
                         last_increase, last_increase_date, market_min, market_max, market_median, next_review, market_position
                  FROM employees";
$result = $conn->query($employeeQuery);

while ($row = $result->fetch_assoc()) {
    $employees[] = [
        "id" => $row['id'],
        "name" => $row['name'],
        "role" => $row['position'],
        "salary" => "₱" . number_format($row['salary'], 0),
        "rating" => $row['rating'] . "/5.0",
        "lastIncrease" => "₱" . number_format($row['last_increase'], 0),
        "lastIncreaseDate" => $row['last_increase_date'],
        "marketRange" => "₱" . number_format($row['market_min'], 0) . " - ₱" . number_format($row['market_max'], 0),
        "median" => "₱" . number_format($row['market_median'], 0),
        "nextReview" => $row['next_review'],
        "marketPos" => $row['market_position']
    ];
}

// Helper function for rating color
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
</body>
</html>
