<?php
$host = "localhost:3307";
$user = "root";
$pass = "";
$db   = "compensation";

function getDbConnection() {
  global $host, $user, $pass, $db;
  $conn = new mysqli($host, $user, $pass, $db);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  return $conn;
}

function getPageTitle() {
  return "HR 4 Compensation";
}

function getUserName() {
  return "User";
}

function getHotelName() {
  return "Hotel & Restaurant NAME";
}

function getCompensationSummary() {
  $conn = getDbConnection();
  $sql = "SELECT totalCompensation, averageSalary, reviewsDue, highPerformers FROM compensation_summary LIMIT 1";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $conn->close();
  return $row ? $row : [
    "totalCompensation" => "₱1,000",
    "averageSalary"     => "₱31,500",
    "reviewsDue"        => 4,
    "highPerformers"    => 2
  ];
}

function getEmployees() {
  $conn = getDbConnection();
  $sql = "SELECT * FROM employees";
  $result = $conn->query($sql);
  $employees = [];
  while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
  }
  $conn->close();
  return $employees;
}

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



// Usage example:
$pageTitle = getPageTitle();
$userName = getUserName();
$hotelName = getHotelName();
$summary = getCompensationSummary();
$totalCompensation = $summary['totalCompensation'];
$averageSalary = $summary['averageSalary'];
$reviewsDue = $summary['reviewsDue'];
$highPerformers = $summary['highPerformers'];
$employees = getEmployees();
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
          <button id="openSalaryModalMain" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg flex items-center gap-2">
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
                  <button
  class="openSalaryModal bg-blue-900 hover:bg-blue-800 text-white px-3 py-1 rounded-lg text-sm"
  data-id="<?php echo $emp['id']; ?>"
  data-name="<?php echo $emp['name']; ?>"
  data-salary="<?php echo preg_replace('/[^0-9]/', '', $emp['salary']); ?>"
>
  Adjust Salary
</button>
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

        <!-- Salary Adjustment Modal -->
<div id="salaryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-lg w-full max-w-lg p-6">
    <div class="flex justify-between items-center border-b pb-3">
      <h2 class="text-xl font-bold text-gray-800">Salary Adjustment</h2>
      <button id="closeModal" class="text-gray-500 hover:text-gray-700">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>
    <form method="POST" action="save_salary.php" class="mt-4 space-y-4">
      <input type="hidden" name="employeeId" id="modalEmployeeId">
      <div>
        <label class="block text-sm font-medium text-gray-600">Employee Name</label>
        <input type="text" id="modalEmployeeName" class="w-full mt-1 px-3 py-2 border rounded-lg bg-gray-100" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-600">Current Salary</label>
        <input type="text" id="modalCurrentSalary" class="w-full mt-1 px-3 py-2 border rounded-lg bg-gray-100" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-600">New Salary</label>
        <input type="number" name="newSalary" id="modalNewSalary" class="w-full mt-1 px-3 py-2 border rounded-lg" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-600">Effective Date</label>
        <input type="date" name="effectiveDate" id="modalEffectiveDate" class="w-full mt-1 px-3 py-2 border rounded-lg" required>
      </div>
      <div class="flex justify-end mt-6 gap-2">
        <button id="cancelModal" type="button" class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100">Cancel</button>
        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-900 hover:bg-blue-800 text-white">Save Adjustment</button>
      </div>
    </form>
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
  <script>
document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("salaryModal");
  const closeModal = document.getElementById("closeModal");
  const cancelModal = document.getElementById("cancelModal");
  const openButtons = document.querySelectorAll(".openSalaryModal");

  const employeeIdInput = document.getElementById("modalEmployeeId");
  const employeeNameInput = document.getElementById("modalEmployeeName");
  const currentSalaryInput = document.getElementById("modalCurrentSalary");
  const newSalaryInput = document.getElementById("modalNewSalary");
  const effectiveDateInput = document.getElementById("modalEffectiveDate");

  // Open modal and fill data
  openButtons.forEach(button => {
    button.addEventListener("click", function () {
      employeeIdInput.value = this.dataset.id || "";
      employeeNameInput.value = this.dataset.name || "";
      currentSalaryInput.value = this.dataset.salary ? "₱" + parseInt(this.dataset.salary).toLocaleString() : "";
      newSalaryInput.value = "";
      effectiveDateInput.value = "";
      modal.classList.remove("hidden");
      modal.classList.add("flex");
    });
  });

  // Close modal
  [closeModal, cancelModal].forEach(btn => {
    btn.addEventListener("click", () => {
      modal.classList.add("hidden");
      modal.classList.remove("flex");
    });
  });
});
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("salaryModal");
  const openMainButton = document.getElementById("openSalaryModalMain");
  const employeeIdInput = document.getElementById("modalEmployeeId");
  const employeeNameInput = document.getElementById("modalEmployeeName");
  const currentSalaryInput = document.getElementById("modalCurrentSalary");
  const newSalaryInput = document.getElementById("modalNewSalary");
  const effectiveDateInput = document.getElementById("modalEffectiveDate");

  if (openMainButton) {
    openMainButton.addEventListener("click", function () {
      employeeIdInput.value = "";
      employeeNameInput.value = "";
      currentSalaryInput.value = "";
      newSalaryInput.value = "";
      effectiveDateInput.value = "";
      modal.classList.remove("hidden");
      modal.classList.add("flex");
    });
  }
});
</script>
</body>
</html>