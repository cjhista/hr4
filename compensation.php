<?php
$pageTitle = "HR 4 Compensation";
$userName  = "User"; 
$hotelName = "Hotel & Restaurant NAME";

// Example Compensation Summary (replace with DB later)
$totalCompensation = "₱1,512,000";
$averageSalary     = "₱31,500";
$reviewsDue        = 4;
$highPerformers    = 2;

// Example Employee Compensation Data
$employees = [
  [
    "id" => 1,
    "name" => "Maria Santos",
    "role" => "Front Desk Manager",
    "salary" => "₱35,000",
    "rating" => "4.5/5.0",
    "lastIncrease" => "₱3,000 (9.4%)",
    "lastIncreaseDate" => "1/15/2024 - Performance Review",
    "marketRange" => "₱30,000 - ₱40,000",
    "median" => "₱35,000",
    "nextReview" => "1/15/2025",
    "marketPos" => 50
  ],
  [
    "id" => 2,
    "name" => "John Dela Cruz",
    "role" => "Head Chef",
    "salary" => "₱45,000",
    "rating" => "3.6/5.0",
    "lastIncrease" => "₱5,000 (12.5%)",
    "lastIncreaseDate" => "8/20/2023 - Promotion",
    "marketRange" => "₱40,000 - ₱55,000",
    "median" => "₱47,500",
    "nextReview" => "12/20/2024",
    "marketPos" => 33
  ],
  [
    "id" => 3,
    "name" => "Ana Reyes",
    "role" => "Housekeeping Supervisor",
    "salary" => "₱28,000",
    "rating" => "2.8/5.0",
    "lastIncrease" => "₱1,500 (5.6%)",
    "lastIncreaseDate" => "10/01/2023 - Performance Review",
    "marketRange" => "₱25,000 - ₱32,000",
    "median" => "₱28,500",
    "nextReview" => "10/01/2024",
    "marketPos" => 45
  ]
];

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
        
        <!-- Page Title -->
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Compensation Planning</h1>
            <p class="text-gray-500 text-sm">Manage salary adjustments and performance-based incentives</p>
          </div>
          <!-- ✅ Added Create New Salary button -->
          <button class="createNewSalary bg-blue-900 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-800">
            <i data-lucide="plus" class="w-5 h-5"></i> Create New Salary
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
                  <!-- Adjust Salary button now has modal trigger -->
                  <button class="openSalaryModal bg-blue-900 hover:bg-blue-800 text-white px-3 py-1 rounded-lg text-sm"
                    data-name="<?php echo $emp['name']; ?>"
                    data-salary="<?php echo $emp['salary']; ?>">
                    Adjust Salary
                  </button>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div>
                  <p class="text-gray-500">Current Salary</p>
                  <p class="font-bold text-gray-900"><?php echo $emp['salary']; ?></p>
                  <p class="text-gray-500 mt-2">Performance Rating</p>
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

<!-- Salary Adjustment Modal -->
<div id="salaryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-lg w-full max-w-lg p-6">
    <div class="flex justify-between items-center border-b pb-3">
      <h2 class="text-xl font-bold text-gray-800">Salary Adjustment</h2>
      <button id="closeModal" class="text-gray-500 hover:text-gray-700">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>

    <!-- ✅ Connected form -->
    <form method="POST" action="save_salary.php" class="mt-4 space-y-4">
      <!-- hidden employee id -->
      <input type="hidden" name="employeeId" id="employeeId">

      <div>
        <label class="block text-sm font-medium text-gray-600">Employee Name</label>
        <input type="text" id="employeeName" class="w-full mt-1 px-3 py-2 border rounded-lg bg-gray-100" readonly>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-600">Current Salary</label>
        <input type="text" id="currentSalary" class="w-full mt-1 px-3 py-2 border rounded-lg bg-gray-100" readonly>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-600">New Salary</label>
        <input type="number" name="newSalary" id="newSalary" class="w-full mt-1 px-3 py-2 border rounded-lg" placeholder="Enter new salary" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-600">Effective Date</label>
        <input type="date" name="effectiveDate" id="effectiveDate" class="w-full mt-1 px-3 py-2 border rounded-lg" required>
      </div>

      <div class="flex justify-end mt-6 gap-2">
        <button id="cancelModal" type="button" class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100">
          Cancel
        </button>
        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-900 hover:bg-blue-800 text-white">
          Save Adjustment
        </button>
      </div>
    </form>
  </div>
</div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const modal = document.getElementById("salaryModal");
      const closeModal = document.getElementById("closeModal");
      const cancelModal = document.getElementById("cancelModal");
      const openButtons = document.querySelectorAll(".openSalaryModal");
      const createNewButton = document.querySelector(".createNewSalary");

     // For Adjust Salary buttons
openButtons.forEach(button => {
  button.addEventListener("click", function () {
    const id = this.getAttribute("data-id");
    const name = this.getAttribute("data-name");
    const salary = this.getAttribute("data-salary");

    document.getElementById("employeeId").value = id;
    document.getElementById("employeeName").value = name;
    document.getElementById("currentSalary").value = salary;

    modal.classList.remove("hidden");
    modal.classList.add("flex");
  });
});

      // For Create New Salary button
      if (createNewButton) {
        createNewButton.addEventListener("click", function () {
          document.getElementById("employeeName").value = "";
          document.getElementById("currentSalary").value = "";
          document.getElementById("newSalary").value = "";
          document.getElementById("effectiveDate").value = "";

          modal.classList.remove("hidden");
          modal.classList.add("flex");
        });
      }

      // Close modal
      [closeModal, cancelModal].forEach(btn => {
        btn.addEventListener("click", () => {
          modal.classList.add("hidden");
          modal.classList.remove("flex");
        });
      });
    });
  </script>

</body>
</html>
