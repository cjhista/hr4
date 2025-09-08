<?php
$pageTitle = "HR 4 Benefits";
$userName  = "User"; 

// Example dynamic values (replace with DB queries later)
$totalBenefitsCost = "₱15,500";
$enrolledEmployees = 3;
$pendingApplications = 1;
$enrollmentRate = 75;

// Example employees enrollment status
$employeeEnrollments = [
  [
    "name" => "Maria Santos",
    "role" => "Front Desk Manager",
    "dependents" => 2,
    "hmo" => "PhilCare",
    "sss" => true,
    "philhealth" => true,
    "pagibig" => true,
    "status" => "ENROLLED",
    "cost" => "₱4,500"
  ],
  [
    "name" => "John Dela Cruz",
    "role" => "Head Chef",
    "dependents" => 3,
    "hmo" => "Maxicare",
    "sss" => true,
    "philhealth" => true,
    "pagibig" => false,
    "status" => "ENROLLED",
    "cost" => "₱5,200"
  ],
  [
    "name" => "Sarah Wilson",
    "role" => "Housekeeping Supervisor",
    "dependents" => 1,
    "hmo" => "Intellicare",
    "sss" => true,
    "philhealth" => false,
    "pagibig" => false,
    "status" => "PENDING",
    "cost" => "₱2,800"
  ]
];

// Example providers
$providers = [
  [
    "name" => "PhilCare",
    "coverage" => "Comprehensive",
    "premium" => "₱2,500",
    "benefit" => "₱150,000",
    "network" => "500+ hospitals nationwide"
  ],
  [
    "name" => "Maxicare",
    "coverage" => "Premium",
    "premium" => "₱3,200",
    "benefit" => "₱300,000",
    "network" => "800+ hospitals nationwide"
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
        <h1 class="text-lg font-semibold text-gray-600">Hotel & Restaurant NAME</h1>
      </div>

      <main class="p-6 space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between border-b pb-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Benefits Administration</h1>
            <p class="text-gray-500 text-sm">Manage employee benefits enrollment and healthcare providers</p>
          </div>
          <div class="flex gap-2">
            <button id="btnReport" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-sm">Generate Report</button>
            <button id="btnAddProvider" class="px-4 py-2 rounded-lg bg-blue-900 text-white hover:bg-blue-800 text-sm">+ Add Provider</button>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white p-6 rounded-2xl shadow">
            <p class="text-sm text-gray-500">Total Benefits Cost</p>
            <h2 class="text-2xl font-bold"><?php echo $totalBenefitsCost; ?></h2>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow">
            <p class="text-sm text-gray-500">Enrolled</p>
            <h2 class="text-2xl font-bold text-green-600"><?php echo $enrolledEmployees; ?></h2>
            <p class="text-xs text-gray-400">Employees</p>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow">
            <p class="text-sm text-gray-500">Pending</p>
            <h2 class="text-2xl font-bold text-yellow-600"><?php echo $pendingApplications; ?></h2>
            <p class="text-xs text-gray-400">Applications</p>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow">
            <p class="text-sm text-gray-500">Enrollment Rate</p>
            <h2 class="text-2xl font-bold"><?php echo $enrollmentRate; ?>%</h2>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
              <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $enrollmentRate; ?>%"></div>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <div class="w-full flex justify-center">
          <div class="flex bg-gray-100 rounded-lg overflow-hidden text-sm font-medium text-gray-600">
            <button id="tabEnrollments" class="px-6 py-2 border-b-2 border-blue-600 text-blue-600">Employee Enrollments</button>
            <button id="tabProviders" class="px-6 py-2">Healthcare Providers</button>
          </div>
        </div>

        <!-- Employee Enrollments -->
        <div id="enrollmentsSection" class="space-y-4">
          <?php foreach ($employeeEnrollments as $emp): ?>
            <div class="bg-white shadow rounded-xl p-6 flex justify-between items-center">
              <div>
                <h2 class="font-bold text-gray-800"><?php echo $emp['name']; ?></h2>
                <p class="text-sm text-gray-500"><?php echo $emp['role']; ?> · <?php echo $emp['dependents']; ?> dependent(s)</p>
                <p class="text-xs text-gray-500">HMO: <?php echo $emp['hmo']; ?></p>
                <div class="flex flex-wrap gap-4 text-xs mt-2">
                  <p><?php echo $emp['sss'] ? "✓ SSS" : "✗ SSS"; ?></p>
                  <p><?php echo $emp['philhealth'] ? "✓ PhilHealth" : "✗ PhilHealth"; ?></p>
                  <p><?php echo $emp['pagibig'] ? "✓ Pag-IBIG" : "✗ Pag-IBIG"; ?></p>
                </div>
              </div>
              <div class="text-right">
                <span class="px-3 py-1 rounded-md text-xs font-semibold
                  <?php echo $emp['status'] == 'ENROLLED' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                  <?php echo $emp['status']; ?>
                </span>
                <p class="font-bold mt-2"><?php echo $emp['cost']; ?></p>
                <button onclick="openManageModal('<?php echo $emp['name']; ?>')" class="mt-2 text-sm text-blue-600 hover:underline">Manage</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Providers -->
        <div id="providersSection" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($providers as $prov): ?>
            <div class="bg-white shadow rounded-xl p-6">
              <h2 class="font-bold text-gray-800"><?php echo $prov['name']; ?></h2>
              <p class="text-sm text-gray-500"><?php echo $prov['coverage']; ?> Coverage</p>
              <p class="mt-2 text-sm">Monthly Premium: <span class="font-bold"><?php echo $prov['premium']; ?></span></p>
              <p class="text-sm">Max Benefit: <span class="font-bold"><?php echo $prov['benefit']; ?></span></p>
              <p class="text-sm">Network: <?php echo $prov['network']; ?></p>
              <div class="flex gap-2 mt-4">
                <button class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded">View Details</button>
                <button class="px-3 py-1 text-sm bg-blue-100 text-blue-700 hover:bg-blue-200 rounded">Edit</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      </main>
    </div>
  </div>

  <!-- Modals -->
  <!-- Generate Report Modal -->
  <div id="modalReport" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-xl shadow-lg w-96 p-6">
      <h2 class="text-lg font-bold mb-4">Generate Report</h2>
      <p class="text-sm text-gray-600 mb-4">Download employee benefits report as PDF or Excel.</p>
      <div class="flex justify-end gap-2">
        <button onclick="closeModal('modalReport')" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
        <button class="px-4 py-2 rounded bg-blue-900 text-white hover:bg-blue-800">Download PDF</button>
      </div>
    </div>
  </div>

  <!-- Add Provider Modal -->
  <div id="modalAddProvider" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-xl shadow-lg w-96 p-6">
      <h2 class="text-lg font-bold mb-4">Add Healthcare Provider</h2>
      <form class="space-y-3">
        <input type="text" placeholder="Provider Name" class="w-full border rounded px-3 py-2 text-sm" />
        <input type="text" placeholder="Coverage" class="w-full border rounded px-3 py-2 text-sm" />
        <input type="text" placeholder="Premium" class="w-full border rounded px-3 py-2 text-sm" />
        <input type="text" placeholder="Max Benefit" class="w-full border rounded px-3 py-2 text-sm" />
        <input type="text" placeholder="Network" class="w-full border rounded px-3 py-2 text-sm" />
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeModal('modalAddProvider')" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded bg-blue-900 text-white hover:bg-blue-800">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Manage Employee Modal -->
  <div id="modalManage" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-xl shadow-lg w-96 p-6">
      <h2 class="text-lg font-bold mb-4">Manage Employee</h2>
      <p id="manageEmployeeName" class="text-gray-700 mb-4"></p>
      <div class="flex justify-end gap-2">
        <button onclick="closeModal('modalManage')" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Close</button>
        <button class="px-4 py-2 rounded bg-blue-900 text-white hover:bg-blue-800">Edit</button>
      </div>
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

      // Tabs
      const tabEnrollments = document.getElementById("tabEnrollments");
      const tabProviders = document.getElementById("tabProviders");
      const enrollmentsSection = document.getElementById("enrollmentsSection");
      const providersSection = document.getElementById("providersSection");

      tabEnrollments.addEventListener("click", () => {
        enrollmentsSection.classList.remove("hidden");
        providersSection.classList.add("hidden");
        tabEnrollments.classList.add("border-b-2", "border-blue-600", "text-blue-600");
        tabProviders.classList.remove("border-b-2", "border-blue-600", "text-blue-600");
      });

      tabProviders.addEventListener("click", () => {
        providersSection.classList.remove("hidden");
        enrollmentsSection.classList.add("hidden");
        tabProviders.classList.add("border-b-2", "border-blue-600", "text-blue-600");
        tabEnrollments.classList.remove("border-b-2", "border-blue-600", "text-blue-600");
      });

      // Modals
      function openModal(id) {
        document.getElementById(id).classList.remove("hidden");
      }
      function closeModal(id) {
        document.getElementById(id).classList.add("hidden");
      }

      document.getElementById("btnReport").addEventListener("click", () => openModal("modalReport"));
      document.getElementById("btnAddProvider").addEventListener("click", () => openModal("modalAddProvider"));

      window.openManageModal = function(name) {
        document.getElementById("manageEmployeeName").textContent = "You are managing: " + name;
        openModal("modalManage");
      };

      window.closeModal = closeModal;
    });
  </script>
</body>
</html>
