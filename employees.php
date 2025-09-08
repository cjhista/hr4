<?php
$pageTitle = "HR 4 Employees";
$userName  = "User"; 
$hotelName = "Hotel & Restaurant NAME"; 

// Database connection
$host = "localhost:3307";
$user = "root";
$pass = "";
$db   = "hr4_system";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch employees from DB
$sql = "SELECT id, first_name, last_name, position, department, status, salary, email, phone FROM employees ORDER BY id DESC";
$result = $conn->query($sql);

$employees = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
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

      <!-- Header -->
      <div class="flex items-center justify-between border-b py-4 bg-white sticky top-0 z-50 px-6">
        <div class="flex items-center gap-4">
          <button id="sidebarToggle" class="text-gray-600 hover:text-gray-800 focus:outline-none">
            <i id="toggleIcon" data-lucide="menu" class="w-6 h-6"></i>
          </button>
          <h1 class="text-lg font-bold text-gray-800">HR 4 MANAGEMENT SYSTEM</h1>
        </div>
        <h1 class="text-lg font-semibold text-gray-600"><?php echo $hotelName; ?></h1>
      </div>

      <!-- Main -->
      <main class="p-6 space-y-4" x-data="{ filter: 'all' }">

        <!-- Employees Header -->
        <div class="flex items-center justify-between border-b py-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Employees</h1>
            <p class="text-gray-500 text-sm">Manage your hotel and restaurant staff</p>
          </div>
          <button 
            onclick="document.getElementById('addEmployeeModal').classList.remove('hidden')"
            class="bg-blue-900 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-800">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Employee
          </button>
        </div>

        <!-- Employee Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-2 pb-6">
          <?php if (count($employees) > 0): ?>
            <?php foreach ($employees as $emp): ?>
            <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                  <img src="user_profile.php?name=<?php echo urlencode($emp['first_name'].' '.$emp['last_name']); ?>&type=avatar" 
                       alt="<?php echo $emp['first_name'].' '.$emp['last_name']; ?>" 
                       class="w-10 h-10 rounded-full border object-cover" />
                  <div>
                    <h2 class="font-semibold text-gray-800"><?php echo $emp['first_name'].' '.$emp['last_name']; ?></h2>
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
                <span class="font-semibold text-gray-800">₱<?php echo number_format($emp['salary'], 2); ?></span>
              </div>
              <p class="text-sm text-gray-500 mb-2"><?php echo $emp['email']; ?></p>
              <p class="text-sm text-gray-500 mb-4"><?php echo $emp['phone']; ?></p>
              <div class="flex gap-2">
                <button class="flex-1 border border-gray-300 rounded-lg py-2 text-sm hover:bg-gray-100">View</button>
                <button class="flex-1 border border-gray-300 rounded-lg py-2 text-sm hover:bg-gray-100">Edit</button>
              </div>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-gray-500">No employees found.</p>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <?php include 'add_employee.php'; ?> <!-- Modal -->

</body>
</html>
