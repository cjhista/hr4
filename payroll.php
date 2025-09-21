<?php
$pageTitle = "HR 4 Payroll";
$userName  = "User"; 
$hotelName = "Hotel & Restaurant NAME"; 

// Example Payroll Data
$payrollData = [
    "2025-09" => [
        [
            "id" => 1,
            "name" => "Maria Santos",
            "position" => "Front Desk Manager",
            "base_salary" => 35000,
            "overtime" => 3500,
            "bonuses" => 5000,
            "deductions" => [
                "SSS" => 1400,
                "PhilHealth" => 875,
                "Pag-IBIG" => 200,
                "Tax" => 4200
            ],
            "status" => "PROCESSED"
        ],
        [
            "id" => 2,
            "name" => "Luis Perez",
            "position" => "Waiter",
            "base_salary" => 18000,
            "overtime" => 1200,
            "bonuses" => 2000,
            "deductions" => [
                "SSS" => 800,
                "PhilHealth" => 400,
                "Pag-IBIG" => 200,
                "Tax" => 2000
            ],
            "status" => "PENDING"
        ]
    ],
    "2025-08" => [
        [
            "id" => 3,
            "name" => "John Dela Cruz",
            "position" => "Head Chef",
            "base_salary" => 45000,
            "overtime" => 4200,
            "bonuses" => 8000,
            "deductions" => [
                "SSS" => 1800,
                "PhilHealth" => 1125,
                "Pag-IBIG" => 200,
                "Tax" => 6800
            ],
            "status" => "PROCESSED"
        ]
    ],
    "2025-07" => [
        [
            "id" => 4,
            "name" => "Anna Reyes",
            "position" => "Housekeeping Supervisor",
            "base_salary" => 28000,
            "overtime" => 1500,
            "bonuses" => 3000,
            "deductions" => [
                "SSS" => 1100,
                "PhilHealth" => 700,
                "Pag-IBIG" => 200,
                "Tax" => 3000
            ],
            "status" => "PROCESSED"
        ],
        [
            "id" => 5,
            "name" => "Carlos Mendoza",
            "position" => "Bartender",
            "base_salary" => 22000,
            "overtime" => 1800,
            "bonuses" => 2500,
            "deductions" => [
                "SSS" => 950,
                "PhilHealth" => 600,
                "Pag-IBIG" => 200,
                "Tax" => 2500
            ],
            "status" => "PENDING"
        ]
    ],
    "2025-04" => [
        [
            "id" => 6,
            "name" => "Sophia Cruz",
            "position" => "HR Specialist",
            "base_salary" => 30000,
            "overtime" => 1000,
            "bonuses" => 2000,
            "deductions" => [
                "SSS" => 1200,
                "PhilHealth" => 750,
                "Pag-IBIG" => 200,
                "Tax" => 3500
            ],
            "status" => "PROCESSED"
        ],
        [
            "id" => 7,
            "name" => "Mark Villanueva",
            "position" => "Security Guard",
            "base_salary" => 16000,
            "overtime" => 900,
            "bonuses" => 1000,
            "deductions" => [
                "SSS" => 700,
                "PhilHealth" => 400,
                "Pag-IBIG" => 200,
                "Tax" => 1500
            ],
            "status" => "PROCESSED"
        ]
    ]
];

// Selected Month
$selectedMonth = $_GET['month'] ?? date("Y-m");
$payroll = $payrollData[$selectedMonth] ?? [];

// Totals

$totalPayroll = 0;
$processedCount = 0;
$pendingCount = 0;
foreach ($payroll as $p) {
    $gross = $p['base_salary'] + $p['overtime'] + $p['bonuses'];
    $deductions = array_sum($p['deductions']);
    $net = $gross - $deductions;
    $totalPayroll += $net;
    if ($p['status'] === "PROCESSED") $processedCount++;
    if ($p['status'] === "PENDING") $pendingCount++;
}
?>

<!DOCTYPE html>
<html lang="en" x-data="{ showPayslip: null, showProcess: false, showExport: false }">
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

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-y-auto">

      <!-- Sticky Header -->
      <div class="flex items-center justify-between border-b py-4 bg-white sticky top-0 z-50 px-6">
        <div class="flex items-center gap-4">
          <button id="sidebarToggle" class="text-gray-600 hover:text-gray-800 focus:outline-none">
            <i id="toggleIcon" data-lucide="menu" class="w-6 h-6"></i>
          </button>
          <h1 class="text-lg font-bold text-gray-800">HR 4 MANAGEMENT SYSTEM supot</h1>
        </div>
        <h1 class="text-lg font-semibold text-gray-600"><?php echo $hotelName; ?></h1>
      </div>

      <!-- Main Wrapper -->
      <main class="p-6 space-y-6">

       
        <div class="flex items-center justify-between border-b pb-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Payroll</h1>
            <p class="text-gray-500 text-sm">Manage employee compensation and payroll processing</p>
          </div>
          <div class="flex gap-3">
            <!-- Export Reports -->
            <button @click="showExport = true"
               class="border border-gray-300 px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-gray-100">
              <i data-lucide="download" class="w-4 h-4"></i> Export Reports
            </button>
            <!-- Process Payroll -->
            <button @click="showProcess = true"
               class="bg-blue-900 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-800">
              <i data-lucide="play" class="w-4 h-4"></i> Process Payroll
            </button>
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="bg-white rounded-xl border p-4">
            <p class="text-sm text-gray-500">Total Payroll</p>
            <h2 class="text-xl font-bold text-gray-800">₱<?php echo number_format($totalPayroll, 2); ?></h2>
          </div>
          <div class="bg-white rounded-xl border p-4">
            <p class="text-sm text-gray-500">Processed</p>
            <h2 class="text-xl font-bold text-green-600"><?php echo $processedCount; ?></h2>
          </div>
          <div class="bg-white rounded-xl border p-4">
            <p class="text-sm text-gray-500">Pending</p>
            <h2 class="text-xl font-bold text-yellow-600"><?php echo $pendingCount; ?></h2>
          </div>
          <div class="bg-white rounded-xl border p-4 relative" x-data="{ openMonthPicker: false }">
            <p class="text-sm text-gray-500">This Month</p>
            <div class="flex items-center justify-between">
              <h2 class="text-xl font-bold text-gray-800">
                <?php echo date("F Y", strtotime($selectedMonth)); ?>
              </h2>
              <button @click="openMonthPicker = !openMonthPicker" class="text-gray-600 hover:text-gray-800">
                <i data-lucide="calendar" class="w-5 h-5"></i>
              </button>
            </div>
            <div x-show="openMonthPicker" @click.away="openMonthPicker=false" 
                 class="absolute bg-white shadow rounded-lg mt-2 p-4 right-0 w-64 z-50">
              <form method="GET">
                <label for="month" class="block text-sm font-medium text-gray-700">Select Month</label>
                <input type="month" id="month" name="month" value="<?php echo $selectedMonth; ?>" 
                       class="mt-2 w-full border-gray-300 rounded-lg">
                <button type="submit" class="mt-3 w-full bg-blue-900 text-white px-3 py-2 rounded-lg hover:bg-blue-800">Apply</button>
              </form>
            </div>
          </div>
        </div>

<!-- Payroll List -->
<div class="space-y-6">
  <h2 class="text-lg font-semibold text-gray-700"><?php echo date("F Y", strtotime($selectedMonth)); ?> Payroll</h2>

  <?php if (!empty($payroll)): ?>
    <?php foreach ($payroll as $p): 
      $gross = $p['base_salary'] + $p['overtime'] + $p['bonuses'];
      $deductions = array_sum($p['deductions']);
      $net = $gross - $deductions;
    ?>
    <div class="bg-white rounded-xl border p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
          <!-- Avatar pulled from user_profile.php -->
          <img src="user_profile.php?id=<?php echo $p['id']; ?>&type=avatar"
               alt="<?php echo $p['name']; ?> Avatar"
               class="w-10 h-10 rounded-full border border-gray-300 object-cover">
          <div>
            <h3 class="font-semibold text-gray-800"><?php echo $p['name']; ?></h3>
            <p class="text-sm text-gray-500"><?php echo $p['position']; ?></p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <?php if ($p['status'] === "PROCESSED"): ?>
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">PROCESSED</span>
          <?php else: ?>
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">PENDING</span>
          <?php endif; ?>
          <!-- Payslip -->
          <button @click="showPayslip = <?php echo $p['id']; ?>" 
             class="border border-gray-300 px-3 py-1 rounded-lg text-sm hover:bg-gray-100 flex items-center gap-1">
            <i data-lucide="file-text" class="w-4 h-4"></i> Payslip
          </button>
        </div>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        <div><p class="text-sm text-gray-500">Base Salary</p><p class="font-semibold">₱<?php echo number_format($p['base_salary'], 2); ?></p></div>
        <div><p class="text-sm text-gray-500">Overtime</p><p class="font-semibold">₱<?php echo number_format($p['overtime'], 2); ?></p></div>
        <div><p class="text-sm text-gray-500">Bonuses</p><p class="font-semibold">₱<?php echo number_format($p['bonuses'], 2); ?></p></div>
        <div><p class="text-sm text-gray-500">Gross Pay</p><p class="font-semibold">₱<?php echo number_format($gross, 2); ?></p></div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4">
        <div>
          <p class="text-sm text-gray-500 mb-1">Deduction Breakdown:</p>
          <?php foreach ($p['deductions'] as $key => $value): ?>
            <p class="text-sm text-gray-600"><?php echo $key; ?>: ₱<?php echo number_format($value, 2); ?></p>
          <?php endforeach; ?>
        </div>
        <div>
          <p class="text-sm text-gray-500">Deductions</p>
          <p class="font-semibold text-red-600">-₱<?php echo number_format($deductions, 2); ?></p>
          <p class="text-sm text-gray-500 mt-2">Net Pay</p>
          <p class="font-semibold text-green-600">₱<?php echo number_format($net, 2); ?></p>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="text-gray-500">No payroll records found for this month.</p>
  <?php endif; ?>
</div>


  <!-- Payslip Modal -->
  <?php foreach ($payroll as $emp): 
      $gross = $emp['base_salary'] + $emp['overtime'] + $emp['bonuses'];
      $totalDeductions = array_sum($emp['deductions']);
      $net = $gross - $totalDeductions;
  ?>
  <div x-show="showPayslip === <?php echo $emp['id']; ?>" 
       class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-2xl" @click.away="showPayslip = null">
      <div class="flex justify-between items-center border-b pb-2 mb-4">
        <h2 class="text-xl font-bold">Payslip - <?php echo $emp['name']; ?></h2>
        <button @click="showPayslip = null" class="text-gray-500 hover:text-gray-700">✖</button>
      </div>
      <div class="mb-3">
        <p class="font-semibold"><?php echo $emp['position']; ?></p>
        <p class="text-sm text-gray-500"><?php echo date("F Y", strtotime($selectedMonth)); ?></p>
      </div>
      <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="p-2 border rounded">Base: ₱<?php echo number_format($emp['base_salary'],2); ?></div>
        <div class="p-2 border rounded">Overtime: ₱<?php echo number_format($emp['overtime'],2); ?></div>
        <div class="p-2 border rounded">Bonuses: ₱<?php echo number_format($emp['bonuses'],2); ?></div>
        <div class="p-2 border rounded font-semibold text-green-700">Gross: ₱<?php echo number_format($gross,2); ?></div>
      </div>
      <h3 class="font-semibold mb-2">Deductions</h3>
      <ul class="text-sm mb-3">
        <?php foreach ($emp['deductions'] as $key=>$val): ?>
          <li class="flex justify-between border-b"><span><?php echo $key; ?></span><span>₱<?php echo number_format($val,2); ?></span></li>
        <?php endforeach; ?>
      </ul>
      <div class="flex justify-between font-bold text-green-700 border-t pt-2">
        <span>Net Pay</span>
        <span>₱<?php echo number_format($net,2); ?></span>
      </div>
      <div class="flex justify-end gap-2 mt-4">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded">Print</button>
        <button @click="showPayslip = null" class="bg-gray-400 text-white px-4 py-2 rounded">Close</button>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  <!-- Process Payroll Modal -->
  <div x-show="showProcess" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-3xl" @click.away="showProcess = false">
      <div class="flex justify-between items-center border-b pb-2 mb-4">
        <h2 class="text-xl font-bold">Payroll Summary - <?php echo date("F Y", strtotime($selectedMonth)); ?></h2>
        <button @click="showProcess=false" class="text-gray-500 hover:text-gray-700">✖</button>
      </div>
      <table class="w-full text-sm border">
        <thead class="bg-gray-100">
          <tr>
            <th class="border p-2 text-left">Name</th>
            <th class="border p-2 text-left">Position</th>
            <th class="border p-2 text-left">Net Pay</th>
            <th class="border p-2 text-left">Status</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($payroll as $p): 
          $gross = $p['base_salary'] + $p['overtime'] + $p['bonuses'];
          $deductions = array_sum($p['deductions']);
          $net = $gross - $deductions;
        ?>
          <tr>
            <td class="border p-2"><?php echo $p['name']; ?></td>
            <td class="border p-2"><?php echo $p['position']; ?></td>
            <td class="border p-2">₱<?php echo number_format($net,2); ?></td>
            <td class="border p-2"><?php echo $p['status']; ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <div class="flex justify-end mt-4">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded">Print</button>
      </div>
    </div>
  </div>

  <!-- Export Report Modal -->
  <div x-show="showExport" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-lg" @click.away="showExport = false">
      <div class="flex justify-between items-center border-b pb-2 mb-4">
        <h2 class="text-xl font-bold">Export Report</h2>
        <button @click="showExport=false" class="text-gray-500 hover:text-gray-700">✖</button>
      </div>
      <p class="mb-4">Download payroll report for <?php echo date("F Y", strtotime($selectedMonth)); ?>.</p>
      <a href="export_reports.php?month=<?php echo $selectedMonth; ?>" 
         class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Download CSV</a>
    </div>
  </div>

  <script>
document.addEventListener("DOMContentLoaded", function () {
  const sidebarToggle = document.getElementById("sidebarToggle");
  const sidebar = document.getElementById("sidebar");
  const sidebarTexts = document.querySelectorAll(".sidebar-text");
  const logoExpanded = document.querySelector(".sidebar-logo-expanded");
  const logoCollapsed = document.querySelector(".sidebar-logo-collapsed");

  const userDropdownToggle = document.getElementById("userDropdownToggle");
  const userDropdown = document.getElementById("userDropdown");

 
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

  // User dropdown
  if (userDropdownToggle && userDropdown) {
    userDropdownToggle.addEventListener("click", function () {
      userDropdown.classList.toggle("hidden");
    });

    document.addEventListener("click", function (event) {
      if (!userDropdown.contains(event.target) && !userDropdownToggle.contains(event.target)) {
        userDropdown.classList.add("hidden");
      }
    });
  }
});
</script>

</body>
</html>