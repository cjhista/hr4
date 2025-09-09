<?php
$pageTitle = "HR 4 Dashboard";
$userName  = "User"; 

include 'db.php';

// Handle Add Employee POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_employee'])) {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $department = $_POST['department'];
    $position   = $_POST['position'];
    $salary     = $_POST['salary'];
    $status     = $_POST['status'];

    // Insert employee
    $sql = "INSERT INTO employees (first_name, last_name, email, phone, department, position, salary, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssis", $first_name, $last_name, $email, $phone, $department, $position, $salary, $status);

    if ($stmt->execute()) {
        // Log activity
        $fullName = $first_name . " " . $last_name;
        $activityText = "New employee $fullName onboarded";

        $actSql = "INSERT INTO activities (text, status) VALUES (?, 'info')";
        $actStmt = $conn->prepare($actSql);
        $actStmt->bind_param("s", $activityText);
        $actStmt->execute();

        // Redirect back to dashboard
        header("Location: dashboard.php");
        exit;
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
        exit;
    }
}
?>

// Fetch total employees
$totalEmployees = 0;
$result = $conn->query("SELECT COUNT(*) AS total FROM employees");
if ($result && $row = $result->fetch_assoc()) {
    $totalEmployees = $row['total'];
}

// Example dynamic values
$presentToday     = 0;
$monthlyPayroll   = "₱145K";
$avgPerformance   = "4.2/5";
$benefitsEnrolled = 13;
$pendingReviews   = 3;

// Fetch recent activities (latest 10)
$recentActivities = [];
$sql = "SELECT * FROM activities ORDER BY created_at DESC LIMIT 10";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recentActivities[] = [
            'id' => $row['id'],
            'icon' => ($row['status'] == 'warning') ? 'alert-triangle' : 'user-plus',
            'text' => $row['text'],
            'time' => date('g:i A, M d', strtotime($row['created_at'])),
            'status' => $row['status']
        ];
    }
}

// Quick actions
$quickActions = ["Add New Employee", "Process Payroll", "Generate Reports", "Manage Benefits"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $pageTitle; ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<link rel="icon" type="image/png" href="logo2.png" />
<script>
document.addEventListener("DOMContentLoaded", function () {
    lucide.createIcons();
});
</script>
</head>
<body class="h-screen overflow-hidden">
<div class="flex h-full">

    <!-- Sidebar -->
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
            <h1 class="text-lg font-semibold text-gray-600">Hotel & Restaurant NAME</h1>
        </div>

        <main class="p-6 space-y-4">

            <div class="flex items-center justify-between border-b py-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
                    <p class="text-gray-500 text-sm">Welcome to your HR management system</p>
                </div>

                <!-- User Dropdown -->
                <div class="relative">
                    <button id="userDropdownToggle" class="flex items-center gap-2 px-3 py-2 rounded bg-gray-200 hover:bg-gray-300">
                        <i data-lucide="user" class="w-5 h-5"></i>
                        <span><?php echo $userName; ?></span>
                    </button>
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg">
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Settings</a>
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
                    </div>
                </div>
            </div>

            <!-- Dashboard Cards -->
            <div class="px-6 mt-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
                    <!-- Total Employees -->
                    <div class="shadow hover:shadow-lg transition-shadow duration-200 bg-white rounded-2xl p-6">
                        <div class="flex items-center justify-between pb-2">
                            <h2 class="text-sm font-medium text-gray-500">Total Employees</h2>
                            <i data-lucide="users" class="h-5 w-5 text-gray-500"></i>
                        </div>
                        <div class="text-2xl font-bold text-gray-900 mb-1"><?php echo $totalEmployees; ?></div>
                    </div>
                    <!-- Add other cards similarly... -->
                </div>

                <!-- Recent Activity & Quick Actions -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                    <!-- Recent Activity -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-base font-semibold text-gray-800 mb-4">Recent Activity</h2>
                        <ul class="divide-y divide-gray-100">
                            <?php foreach ($recentActivities as $activity): ?>
                                <li class="flex items-center justify-between px-3 py-3 hover:bg-gray-100 rounded-lg transition">
                                    <div class="flex items-center gap-3">
                                        <i data-lucide="<?php echo $activity['icon']; ?>" class="h-5 w-5 text-gray-500"></i>
                                        <div>
                                            <p class="text-gray-700"><?php echo $activity['text']; ?></p>
                                            <p class="text-xs text-gray-400"><?php echo $activity['time']; ?></p>
                                        </div>
                                    </div>
                                    <?php if ($activity['status'] === "warning"): ?>
                                        <span class="text-xs px-2 py-1 rounded-md bg-yellow-200 text-yellow-800 font-medium">warning</span>
                                    <?php elseif ($activity['status'] === "success"): ?>
                                        <span class="text-xs px-2 py-1 rounded-md bg-green-200 text-green-800 font-medium">success</span>
                                    <?php elseif ($activity['status'] === "info"): ?>
                                        <span class="text-xs px-2 py-1 rounded-md bg-blue-200 text-blue-800 font-medium">info</span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow p-4 max-h-72">
                        <h2 class="text-sm font-semibold text-gray-800 mb-3">Quick Actions</h2>
                        <ul class="divide-y divide-gray-100 text-sm">
                            <?php foreach ($quickActions as $action): ?>
                                <li class="px-3 py-2 hover:bg-gray-100 rounded-lg transition cursor-pointer" onclick="handleQuickAction('<?php echo $action; ?>')">
                                    <?php echo $action; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add Employee Modal -->
<div id="addEmployeeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
  <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">
    <h2 class="text-xl font-bold mb-4">Add New Employee</h2>
    <form method="POST" action="">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">First Name</label>
          <input type="text" name="first_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Last Name</label>
          <input type="text" name="last_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
        </div>
      </div>
      <div class="mt-3">
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
      </div>
      <div class="mt-3">
        <label class="block text-sm font-medium text-gray-700">Phone</label>
        <input type="text" name="phone" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
      </div>
      <div class="grid grid-cols-2 gap-4 mt-3">
        <div>
          <label class="block text-sm font-medium text-gray-700">Department</label>
          <select name="department" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
            <option value="">-- Select --</option>
            <option value="Hotel Operations">Hotel Operations</option>
            <option value="Kitchen">Kitchen</option>
            <option value="Housekeeping">Housekeeping</option>
            <option value="Restaurant">Restaurant</option>
            <option value="Human Resources">Human Resources</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Position</label>
          <select name="position" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
            <option value="">-- Select --</option>
            <option value="Front Desk Manager">Front Desk Manager</option>
            <option value="Head Chef">Head Chef</option>
            <option value="Housekeeping Supervisor">Housekeeping Supervisor</option>
            <option value="Waiter">Waiter</option>
            <option value="HR Assistant">HR Assistant</option>
          </select>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4 mt-3">
        <div>
          <label class="block text-sm font-medium text-gray-700">Salary</label>
          <input type="number" name="salary" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
            <option value="ACTIVE">Active</option>
            <option value="INACTIVE">Inactive</option>
          </select>
        </div>
      </div>
      <div class="flex justify-end gap-2 mt-6">
        <button type="button" onclick="document.getElementById('addEmployeeModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg">Cancel</button>
        <button type="submit" name="save_employee" class="px-4 py-2 bg-blue-900 text-white rounded-lg">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const sidebarTexts = document.querySelectorAll(".sidebar-text");
    const userDropdownToggle = document.getElementById("userDropdownToggle");
    const userDropdown = document.getElementById("userDropdown");
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

    userDropdownToggle.addEventListener("click", function () {
        userDropdown.classList.toggle("hidden");
    });

    document.addEventListener("click", function (event) {
        if (!userDropdown.contains(event.target) && !userDropdownToggle.contains(event.target)) {
            userDropdown.classList.add("hidden");
        }
    });
});

// Quick action handler
function handleQuickAction(action) {
    if(action === "Add New Employee") {
        document.getElementById('addEmployeeModal').classList.remove('hidden');
    } else {
        alert(action + " clicked!");
    }
}
</script>
</body>
</html>