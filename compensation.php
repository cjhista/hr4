<?php
$host = "localhost:3307";
$user = "root";
$pass = "";
$db   = "hr4_db";

function getDbConnection() {
  global $host, $user, $pass, $db;
  $conn = new mysqli($host, $user, $pass, $db);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  $conn->set_charset("utf8mb4");
  return $conn;
}

function getPageTitle() { return "HR 4 Compensation"; }
function getHotelName() { return "Hotel & Restaurant NAME"; }

function maskSalary($salary) {
  $digits = strlen((string)intval($salary));
  return str_repeat("*", max(3, min(6, $digits))) . " (hidden)";
}

function getCompensationSummary() {
  $conn = getDbConnection();
  $sql = "SELECT 
            SUM(salary) AS totalComp,
            AVG(salary) AS avgSalary,
            SUM(CASE WHEN nextReview <= CURDATE() + INTERVAL 30 DAY THEN 1 ELSE 0 END) AS reviewsDue,
            SUM(CASE WHEN rating >= 4.5 THEN 1 ELSE 0 END) AS highPerformers
          FROM employees";
  $res = $conn->query($sql);
  $row = $res ? $res->fetch_assoc() : null;
  $conn->close();
  return [
    "totalCompensation" => "₱" . number_format($row['totalComp'] ?? 0, 2),
    "averageSalary"     => "₱" . number_format($row['avgSalary'] ?? 0, 2),
    "reviewsDue"        => $row['reviewsDue'] ?? 0,
    "highPerformers"    => $row['highPerformers'] ?? 0
  ];
}

function getEmployees() {
  $conn = getDbConnection();
  $sql = "SELECT id, first_name, last_name, position, department, salary, rating, nextReview 
          FROM employees WHERE is_deleted = 0 OR is_deleted IS NULL";
  $res = $conn->query($sql);
  $employees = [];
  while ($r = $res->fetch_assoc()) {
    $full = trim(($r["first_name"] ?? '') . " " . ($r["last_name"] ?? ''));
    $salaryVal = floatval($r["salary"] ?? 0);
    $ratingVal = number_format($r["rating"] ?? 0, 1);

    $due = "";
    if (!empty($r['nextReview']) && strtotime($r['nextReview']) <= strtotime("+30 days")) {
      $due = "Review Due";
    }

    $employees[] = [
      "id" => $r["id"],
      "name" => $full,
      "role" => trim(($r["position"] ?? '') . " - " . ($r["department"] ?? '')),
      "salary" => $salaryVal,
      "salaryMasked" => maskSalary($salaryVal),
      "rating" => $ratingVal,
      "nextReview" => $r["nextReview"] ?? "",
      "reviewDueLabel" => $due
    ];
  }
  $conn->close();
  return $employees;
}

function getRatingColor($r) {
  $r = floatval($r);
  if ($r >= 4.0) return "text-green-600 font-semibold";
  if ($r >= 3.0) return "text-yellow-600 font-semibold";
  return "text-red-600 font-semibold";
}

$pageTitle = getPageTitle();
$hotelName = getHotelName();
$summary = getCompensationSummary();
$employees = getEmployees();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $pageTitle ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="icon" type="image/png" href="logo2.png" />
</head>
<body class="h-screen flex overflow-hidden">

  <?php include 'sidebar.php'; ?>

  <div class="flex-1 flex flex-col overflow-y-auto">

    <!-- HEADER -->
    <div class="flex items-center justify-between bg-white border-b px-6 py-4 sticky top-0 z-50">
      <div class="flex items-center gap-4">
        <button id="sidebarToggle" class="text-gray-600 hover:text-gray-800 focus:outline-none">
          <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <h1 class="text-lg font-bold text-gray-800">HR 4 MANAGEMENT SYSTEM</h1>
      </div>
      <h1 class="text-lg font-semibold text-gray-600"><?= $hotelName ?></h1>
    </div>

    <!-- MAIN CONTENT -->
    <main class="p-6 space-y-6">

      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Compensation Planning</h1>
          <p class="text-gray-500 text-sm">Manage salary adjustments and performance-based incentives</p>
        </div>
      </div>

      <!-- SUMMARY CARDS -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
        $cards = [
          ["Total Compensation", $summary["totalCompensation"], "Total", "wallet", "green"],
          ["Average Salary", $summary["averageSalary"], "Average", "bar-chart-3", "blue"],
          ["Reviews Due", $summary["reviewsDue"], "Next 30 days", "calendar", "yellow"],
          ["High Performers", $summary["highPerformers"], "Rating ≥ 4.5", "award", "purple"]
        ];
        foreach ($cards as [$title, $value, $subtitle, $icon, $color]): ?>
        <div class="shadow bg-white rounded-2xl p-6 flex items-center justify-between">
          <div>
            <h2 class="text-sm font-medium text-gray-500"><?= $title ?></h2>
            <div class="text-2xl font-bold text-gray-900"><?= $value ?></div>
            <p class="text-xs text-gray-500"><?= $subtitle ?></p>
          </div>
          <div class="bg-<?= $color ?>-100 text-<?= $color ?>-600 p-3 rounded-full">
            <i data-lucide="<?= $icon ?>" class="w-6 h-6"></i>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- EMPLOYEE LIST -->
      <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
          <h2 class="text-xl font-semibold text-gray-800 mb-3 sm:mb-0">Employee Compensation Overview</h2>
          <!-- 🔍 Search Bar -->
          <input 
            type="text" 
            id="employeeSearch" 
            placeholder="Search employee..." 
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full sm:w-64"
          >
        </div>

        <?php foreach ($employees as $emp): ?>
        <div class="bg-white rounded-2xl shadow p-6 employee-card">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
              <img src="user_profile.php?id=<?= $emp['id'] ?>&type=avatar" class="w-10 h-10 rounded-full border object-cover" alt="">
              <div>
                <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($emp['name']) ?></h3>
                <p class="text-sm text-gray-500"><?= htmlspecialchars($emp['role']) ?></p>
              </div>
            </div>
            <div class="flex gap-2">
              <?php if (!empty($emp['reviewDueLabel'])): ?>
                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-lg text-sm"><?= $emp['reviewDueLabel'] ?></span>
              <?php endif; ?>
              <button class="openSalaryModal bg-blue-900 hover:bg-blue-800 text-white px-3 py-1 rounded-lg text-sm"
                data-id="<?= $emp['id'] ?>"
                data-name="<?= htmlspecialchars($emp['name']) ?>"
                data-current="<?= number_format($emp['salary'], 2, '.', '') ?>"
                data-rating="<?= $emp['rating'] ?>">
                Adjust Salary
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div>
              <p class="text-gray-500">Current Salary</p>
              <p class="font-bold text-gray-900"><?= $emp['salaryMasked'] ?></p>
              <p class="text-gray-500 mt-2">Performance Rating</p>
              <p class="<?= getRatingColor($emp['rating']) ?>"><?= $emp['rating'] ?></p>
              <!-- Removed the Edit button here -->
            </div>
            <div>
              <p class="text-gray-500">Next Review</p>
              <p class="font-semibold text-gray-900"><?= htmlspecialchars($emp['nextReview'] ?: "N/A") ?></p>
            </div>
            <div class="col-span-2"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </main>
  </div>
</div>

<!-- === MODALS BELOW (UNCHANGED) === -->

<!-- Salary Modal -->
<div id="salaryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-lg w-full max-w-lg p-6">
    <div class="flex justify-between items-center border-b pb-3 mb-4">
      <h2 class="text-xl font-bold text-gray-800">Adjust Salary</h2>
      <button id="closeSalaryModal" class="text-gray-500 hover:text-gray-700">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>
    <form method="POST" action="save_salary.php" class="space-y-4">
      <input type="hidden" name="employeeId" id="modalEmpId">
      <div>
        <label class="block text-sm font-medium text-gray-600">Employee Name</label>
        <input type="text" id="modalEmpName" class="w-full px-3 py-2 border rounded-lg bg-gray-100" readonly>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-600">Current Salary</label>
        <input type="text" id="modalCurrentSalary" class="w-full px-3 py-2 border rounded-lg bg-gray-100" readonly>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-600">New Salary</label>
        <input type="text" name="newSalary" id="modalNewSalary" class="w-full px-3 py-2 border rounded-lg" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-600">Effective Date</label>
        <input type="date" name="effectiveDate" class="w-full px-3 py-2 border rounded-lg" required>
      </div>
      <div class="flex justify-end gap-2 pt-3">
        <button type="button" id="cancelSalaryModal" class="px-4 py-2 border rounded-lg">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Rating Modal (kept but not triggered since Edit button was removed) -->
<div id="ratingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6">
    <div class="flex justify-between items-center border-b pb-3 mb-4">
      <h2 class="text-xl font-bold text-gray-800">Edit Performance Rating</h2>
      <button id="closeRatingModal" class="text-gray-500 hover:text-gray-700">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>
    <form method="POST" action="save_rating.php" class="space-y-4">
      <input type="hidden" name="employeeId" id="modalRatingEmpId">
      <div>
        <label class="block text-sm font-medium text-gray-600">Employee Name</label>
        <input type="text" id="modalRatingEmpName" class="w-full px-3 py-2 border rounded-lg bg-gray-100" readonly>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-600">Performance Rating</label>
        <input type="number" name="newRating" id="modalNewRating" class="w-full px-3 py-2 border rounded-lg" step="0.1" min="1" max="5" required>
      </div>
      <div class="flex justify-end gap-2 pt-3">
        <button type="button" id="cancelRatingModalBtn" class="px-4 py-2 border rounded-lg">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  lucide.createIcons();

  // Sidebar toggle
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const expandedLogo = document.querySelector('.sidebar-logo-expanded');
  const collapsedLogo = document.querySelector('.sidebar-logo-collapsed');

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', () => {
      const texts = document.querySelectorAll('.sidebar-text');
      if (sidebar.classList.contains('w-64')) {
        sidebar.classList.replace('w-64','w-20');
        if (expandedLogo) expandedLogo.classList.add('hidden');
        if (collapsedLogo) collapsedLogo.classList.remove('hidden');
        texts.forEach(t => t.classList.add('hidden'));
      } else {
        sidebar.classList.replace('w-20','w-64');
        if (expandedLogo) expandedLogo.classList.remove('hidden');
        if (collapsedLogo) collapsedLogo.classList.add('hidden');
        texts.forEach(t => t.classList.remove('hidden'));
      }
      lucide.createIcons();
    });
  }

  // Salary modal handling
  const salaryModal = document.getElementById("salaryModal");
  const openButtons = document.querySelectorAll(".openSalaryModal");
  const closeSalaryModal = document.getElementById("closeSalaryModal");
  const cancelSalaryModal = document.getElementById("cancelSalaryModal");

  const modalEmpId = document.getElementById("modalEmpId");
  const modalEmpName = document.getElementById("modalEmpName");
  const modalCurrentSalary = document.getElementById("modalCurrentSalary");
  const modalNewSalary = document.getElementById("modalNewSalary");

  openButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      modalEmpId.value = btn.dataset.id;
      modalEmpName.value = btn.dataset.name;
      modalCurrentSalary.value = formatCurrency(btn.dataset.current);
      modalNewSalary.value = "";
      salaryModal.classList.remove("hidden");
      salaryModal.classList.add("flex");
    });
  });
  if (closeSalaryModal) closeSalaryModal.addEventListener("click", () => salaryModal.classList.add("hidden"));
  if (cancelSalaryModal) cancelSalaryModal.addEventListener("click", () => salaryModal.classList.add("hidden"));

  if (modalNewSalary) {
    modalNewSalary.addEventListener("input", () => {
      let v = modalNewSalary.value.replace(/[^0-9]/g, "");
      if (v === "") return modalNewSalary.value = "";
      const num = parseFloat(v) / 100;
      modalNewSalary.value = "₱" + num.toLocaleString(undefined, { minimumFractionDigits: 2 });
    });
    const salaryForm = salaryModal.querySelector("form");
    if (salaryForm) salaryForm.addEventListener("submit", () => {
      modalNewSalary.value = modalNewSalary.value.replace(/[^0-9.]/g, "");
    });
  }

  // Rating modal exists but no open button now

  // Auto-focus search input
  const searchInput = document.getElementById("employeeSearch");
  if (searchInput) {
    searchInput.addEventListener("input", e => {
      const q = e.target.value.toLowerCase();
      document.querySelectorAll(".employee-card").forEach(card => {
        const name = card.querySelector("h3")?.textContent.toLowerCase() || "";
        card.style.display = name.includes(q) ? "" : "none";
      });
    });
  }
});

function formatCurrency(raw) {
  let n = parseFloat(raw);
  if (isNaN(n)) n = 0;
  return "₱" + n.toLocaleString(undefined, { minimumFractionDigits: 2 });
}
</script>
</body>
</html>
