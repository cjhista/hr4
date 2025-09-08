<?php
$pageTitle = "HR 4 Employees";
$userName  = "User"; 
$hotelName = "Hotel & Restaurant NAME"; 

// Example employee data (replace with DB later)
$employees = [
    [
        "name" => "Maria Santos",
        "position" => "Front Desk Manager",
        "department" => "Hotel Operations",
        "status" => "ACTIVE",
        "salary" => "₱35,000",
        "email" => "maria.santos@grandhotel.com",
        "phone" => "+63 912 345 6789"
    ],
    [
        "name" => "Juan Dela Cruz",
        "position" => "Head Chef",
        "department" => "Kitchen",
        "status" => "ACTIVE",
        "salary" => "₱45,000",
        "email" => "juan.delacruz@grandhotel.com",
        "phone" => "+63 917 123 4567"
    ],
    [
        "name" => "Rosa Cruz",
        "position" => "Housekeeping Supervisor",
        "department" => "Housekeeping",
        "status" => "ON LEAVE",
        "salary" => "₱28,000",
        "email" => "rosa.cruz@grandhotel.com",
        "phone" => "+63 915 654 3210"
    ],
    [
        "name" => "Luis Perez",
        "position" => "Waiter",
        "department" => "Restaurant",
        "status" => "ACTIVE",
        "salary" => "₱18,000",
        "email" => "luis.perez@grandhotel.com",
        "phone" => "+63 918 222 3344"
    ],
    [
        "name" => "Ana Garcia",
        "position" => "HR Assistant",
        "department" => "Human Resources",
        "status" => "ON LEAVE",
        "salary" => "₱25,000",
        "email" => "ana.garcia@grandhotel.com",
        "phone" => "+63 919 111 5566"
    ]
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

      
      <div class="flex items-center justify-between border-b py-4 bg-white sticky top-0 z-50 px-6">
        <div class="flex items-center gap-4">
          
          <button id="sidebarToggle" class="text-gray-600 hover:text-gray-800 focus:outline-none">
            <i id="toggleIcon" data-lucide="menu" class="w-6 h-6"></i>
          </button>
          <h1 class="text-lg font-bold text-gray-800">HR 4 MANAGEMENT SYSTEM</h1>
        </div>
        <h1 class="text-lg font-semibold text-gray-600"><?php echo $hotelName; ?></h1>
      </div>

      <!-- Main Wrapper -->
      <main class="p-6 space-y-4" x-data="{ filter: 'all' }">

        <!-- Employees Header -->
        <div class="flex items-center justify-between border-b py-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Employees</h1>
            <p class="text-gray-500 text-sm">Manage your hotel and restaurant staff</p>
          </div>
          <button 
            class="bg-blue-900 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-800">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Employee
          </button>
        </div>

        <!-- Search and Filters -->
        <div class="flex items-center gap-4 mb-6 px-2 mt-2">
          <div class="flex-1">
            <input type="text" placeholder="Search employees..."
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none">
          </div>
          <div class="flex gap-2">
            <button class="px-3 py-1 rounded-full bg-gray-200 text-gray-700 text-sm">All (<?php echo count($employees); ?>)</button>
            <button class="px-3 py-1 rounded-full bg-gray-200 text-gray-700 text-sm">
              Active (<?php echo count(array_filter($employees, fn($e) => $e['status'] === 'ACTIVE')); ?>)
            </button>
            <button class="px-3 py-1 rounded-full bg-gray-200 text-gray-700 text-sm">
              On Leave (<?php echo count(array_filter($employees, fn($e) => $e['status'] === 'ON LEAVE')); ?>)
            </button>
          </div>
        </div>

        <!-- Employee Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-2 pb-6">
          <?php foreach ($employees as $emp): ?>
          <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">

                <!-- Profile Avatar -->
                <img src="user_profile.php?name=<?php echo urlencode($emp['name']); ?>&type=avatar" 
                     alt="<?php echo $emp['name']; ?>" 
                     class="w-10 h-10 rounded-full border object-cover" />
                <div>
                  <h2 class="font-semibold text-gray-800"><?php echo $emp['name']; ?></h2>
                  <p class="text-sm text-gray-500"><?php echo $emp['position']; ?></p>
                </div>
              </div>
              <span class="text-sm font-medium text-gray-600"><?php echo $emp['department']; ?></span>
            </div>
            <div class="flex items-center justify-between mb-4">
              <?php if ($emp['status'] === "ACTIVE"): ?>
                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">ACTIVE</span>
              <?php else: ?>
                <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">ON LEAVE</span>
              <?php endif; ?>
              <span class="font-semibold text-gray-800"><?php echo $emp['salary']; ?></span>
            </div>
            <p class="text-sm text-gray-500 mb-2"><?php echo $emp['email']; ?></p>
            <p class="text-sm text-gray-500 mb-4"><?php echo $emp['phone']; ?></p>
            <div class="flex gap-2">
              <button class="flex-1 border border-gray-300 rounded-lg py-2 text-sm hover:bg-gray-100">View Details</button>
              <button class="flex-1 border border-gray-300 rounded-lg py-2 text-sm hover:bg-gray-100">Edit</button>
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

  <!-- Add Employee Modal -->
<div id="addEmployeeModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
  <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
    <!-- Close Button -->
    <button id="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
      <i data-lucide="x" class="w-5 h-5"></i>
    </button>

    <h2 class="text-xl font-bold text-gray-800 mb-4">Add New Employee</h2>
    <form id="addEmployeeForm" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Full Name</label>
        <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none" required>
      </div>

      <div>
  <label class="block text-sm font-medium text-gray-700">Position</label>
  <select name="position" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none" required>
    <option value="">-- Select Position --</option>
    <option value="Front Desk Manager">Front Desk Manager</option>
    <option value="Chef">Head Chef</option>
    <option value="Chef">Chef</option>
    <option value="Waiter">Waiter</option>
    <option value="Housekeeper">Housekeeping Supervisor</option>
    <option value="Housekeeper">Housekeeper</option>
    <option value="HR Officer">HR Officer</option>
    <option value="HR Officer">HR Assistant</option>
    <option value="Security Guard">Security Guard</option>
  </select>
</div>

<div>
  <label class="block text-sm font-medium text-gray-700">Department</label>
  <select name="department" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none" required>
    <option value="">-- Select Department --</option>
    <option value="Hotel Operations">Hotel Operations</option>
    <option value="Hotel Operations">Kitchen</option>
    <option value="Food & Beverage">Food & Beverage</option>
    <option value="Food & Beverage">Restaurant</option>
    <option value="Housekeeping">Housekeeping</option>
    <option value="Human Resources">Human Resources</option>
    <option value="Security">Security</option>
    <option value="Maintenance">Maintenance</option>
  </select>
</div>


      <div>
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none" required>
          <option value="ACTIVE">ACTIVE</option>
          <option value="ON LEAVE">ON LEAVE</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Salary</label>
        <input type="number" name="salary" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Phone</label>
        <input type="text" name="phone" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none" required>
      </div>

      <div class="flex justify-end gap-2 mt-4">
        <button type="button" id="cancelBtn" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100">Cancel</button>
        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-900 text-white hover:bg-blue-800">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const addBtn = document.querySelector(".bg-blue-900"); 
    const modal = document.getElementById("addEmployeeModal");
    const closeModal = document.getElementById("closeModal");
    const cancelBtn = document.getElementById("cancelBtn");

    // Open Modal
    addBtn.addEventListener("click", () => {
      modal.classList.remove("hidden");
    });

    // Close Modal
    closeModal.addEventListener("click", () => modal.classList.add("hidden"));
    cancelBtn.addEventListener("click", () => modal.classList.add("hidden"));

    
    document.getElementById("addEmployeeForm").addEventListener("submit", function(e) {
      e.preventDefault();
      alert("New employee added! (You can connect this to PHP backend)");
      modal.classList.add("hidden");
    });
  });
</script>
</body>
</html>
