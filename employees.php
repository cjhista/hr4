<?php
$pageTitle = "HR 4 Employees";
$userName  = "User"; 
$hotelName = "Hotel & Restaurant NAME"; 

// Database connection
include 'db.php';

// Handle Add Employee
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_employee'])) {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $department = $_POST['department'];
    $position   = $_POST['position'];
    $salary     = $_POST['salary'];
    $status     = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, email, phone, department, position, salary, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssis", $first_name, $last_name, $email, $phone, $department, $position, $salary, $status);
    
    if ($stmt->execute()) {
        echo "<script>alert('Employee added successfully!'); window.location.href='employees.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error: ".$stmt->error."');</script>";
    }
}

// Handle Search and Filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$sql = "SELECT id, first_name, last_name, position, department, status, salary, email, phone 
        FROM employees 
        WHERE 1";

$params = [];
$types = "";

// Add search
if (!empty($search)) {
    $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}

// Add filter
if (!empty($filter)) {
    if ($filter === 'ACTIVE' || $filter === 'INACTIVE') {
        $sql .= " AND status = ?";
        $params[] = $filter;
        $types .= "s";
    } else {
        $sql .= " AND department = ?";
        $params[] = $filter;
        $types .= "s";
    }
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$employees = [];
if ($result && $result->num_rows > 0) {
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
    <main class="p-6 space-y-4">

      <!-- Employees Header with Search and Filter -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between border-b py-6 gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Employees</h1>
          <p class="text-gray-500 text-sm">Manage your hotel and restaurant staff</p>
        </div>

        <div class="flex flex-col md:flex-row items-start md:items-center gap-2">

          <!-- Search & Filter Form -->
          <form method="GET" class="flex flex-col md:flex-row items-start md:items-center gap-2">
            <input type="text" name="search" placeholder="Search employees..." value="<?php echo htmlspecialchars($search); ?>" 
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full md:w-64">
            <select name="filter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
              <option value="">All Departments/Status</option>
              <option value="Hotel Operations" <?php if($filter==='Hotel Operations') echo 'selected'; ?>>Hotel Operations</option>
              <option value="Kitchen" <?php if($filter==='Kitchen') echo 'selected'; ?>>Kitchen</option>
              <option value="Housekeeping" <?php if($filter==='Housekeeping') echo 'selected'; ?>>Housekeeping</option>
              <option value="Restaurant" <?php if($filter==='Restaurant') echo 'selected'; ?>>Restaurant</option>
              <option value="Human Resources" <?php if($filter==='Human Resources') echo 'selected'; ?>>Human Resources</option>
              <option value="ACTIVE" <?php if($filter==='ACTIVE') echo 'selected'; ?>>Active</option>
              <option value="INACTIVE" <?php if($filter==='INACTIVE') echo 'selected'; ?>>Inactive</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700">Apply</button>
          </form>

          <!-- Add Employee Button -->
          <button onclick="openModal('add')" class="bg-blue-900 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-800">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Employee
          </button>
        </div>
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
                <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">INACTIVE</span>
              <?php endif; ?>
              <span class="font-semibold text-gray-800">₱<?php echo number_format($emp['salary'], 2); ?></span>
            </div>
            <p class="text-sm text-gray-500 mb-2"><?php echo $emp['email']; ?></p>
            <p class="text-sm text-gray-500 mb-4"><?php echo $emp['phone']; ?></p>
            <div class="flex gap-2">
              <button onclick="openModal('view', <?php echo $emp['id']; ?>)" class="flex-1 border border-gray-300 rounded-lg py-2 text-sm hover:bg-gray-100">View</button>
              <button onclick="openModal('edit', <?php echo $emp['id']; ?>)" class="flex-1 border border-gray-300 rounded-lg py-2 text-sm hover:bg-gray-100">Edit</button>
              <form method="POST" action="delete_employee.php" class="flex-1">
                <input type="hidden" name="id" value="<?php echo $emp['id']; ?>">
                <button type="submit" onclick="return confirm('Are you sure you want to delete this employee?');" 
                        class="w-full border border-red-500 text-red-500 rounded-lg py-2 text-sm hover:bg-red-100">
                  Delete
                </button>
              </form>
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

<!-- Employee Modal -->
<div id="employeeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
  <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">
    <h2 id="employeeModalTitle" class="text-xl font-bold mb-4"></h2>
    <div id="employeeModalContent"></div>
  </div>
</div>

<script>
const modal = document.getElementById('employeeModal');

function closeModal(){
  modal.classList.add('hidden');
}

function openModal(mode, id = null) {
  const title = document.getElementById('employeeModalTitle');
  const content = document.getElementById('employeeModalContent');
  content.innerHTML = '';

  if(mode === 'add') {
    title.textContent = 'Add New Employee';
    content.innerHTML = `
      <form method="POST">
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
        <div class="flex justify-end gap-2 mt-4">
          <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">Cancel</button>
          <button type="submit" name="save_employee" class="px-4 py-2 bg-blue-900 text-white rounded-lg">Save</button>
        </div>
      </form>
    `;
  } else {
    fetch('get_employee.php?id=' + id)
      .then(res => res.json())
      .then(emp => {
        if(emp.error){
          content.innerHTML = `<p class="text-red-500">${emp.error}</p>`;
        } else if(mode === 'view') {
          title.textContent = 'View Employee';
          content.innerHTML = `
            <p><strong>Name:</strong> ${emp.first_name} ${emp.last_name}</p>
            <p><strong>Email:</strong> ${emp.email}</p>
            <p><strong>Phone:</strong> ${emp.phone}</p>
            <p><strong>Department:</strong> ${emp.department}</p>
            <p><strong>Position:</strong> ${emp.position}</p>
            <p><strong>Salary:</strong> ₱${parseFloat(emp.salary).toFixed(2)}</p>
            <p><strong>Status:</strong> ${emp.status}</p>
            <div class="flex justify-end mt-4">
              <button onclick="closeModal()" class="px-4 py-2 border rounded-lg">Close</button>
            </div>
          `;
        } else if(mode === 'edit') {
          title.textContent = 'Edit Employee';
          content.innerHTML = `
            <form method="POST" action="edit_employee.php">
              <input type="hidden" name="id" value="${emp.id}">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">First Name</label>
                  <input type="text" name="first_name" value="${emp.first_name}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Last Name</label>
                  <input type="text" name="last_name" value="${emp.last_name}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
                </div>
              </div>
              <div class="mt-3">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="${emp.email}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
              </div>
              <div class="mt-3">
                <label class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" value="${emp.phone}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
              </div>
              <div class="grid grid-cols-2 gap-4 mt-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Department</label>
                  <select name="department" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
                    <option value="Hotel Operations" ${emp.department==='Hotel Operations'?'selected':''}>Hotel Operations</option>
                    <option value="Kitchen" ${emp.department==='Kitchen'?'selected':''}>Kitchen</option>
                    <option value="Housekeeping" ${emp.department==='Housekeeping'?'selected':''}>Housekeeping</option>
                    <option value="Restaurant" ${emp.department==='Restaurant'?'selected':''}>Restaurant</option>
                    <option value="Human Resources" ${emp.department==='Human Resources'?'selected':''}>Human Resources</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Position</label>
                  <select name="position" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
                    <option value="Front Desk Manager" ${emp.position==='Front Desk Manager'?'selected':''}>Front Desk Manager</option>
                    <option value="Head Chef" ${emp.position==='Head Chef'?'selected':''}>Head Chef</option>
                    <option value="Housekeeping Supervisor" ${emp.position==='Housekeeping Supervisor'?'selected':''}>Housekeeping Supervisor</option>
                    <option value="Waiter" ${emp.position==='Waiter'?'selected':''}>Waiter</option>
                    <option value="HR Assistant" ${emp.position==='HR Assistant'?'selected':''}>HR Assistant</option>
                  </select>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-4 mt-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Salary</label>
                  <input type="number" name="salary" value="${emp.salary}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Status</label>
                  <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
                    <option value="ACTIVE" ${emp.status==='ACTIVE'?'selected':''}>Active</option>
                    <option value="INACTIVE" ${emp.status==='INACTIVE'?'selected':''}>Inactive</option>
                  </select>
                </div>
              </div>
              <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded-lg">Save Changes</button>
              </div>
            </form>
          `;
        }
      });
  }
  modal.classList.remove('hidden');
}

// Sidebar Toggle
document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.getElementById("sidebarToggle");
  const sidebar = document.getElementById("sidebar");
  const expandedLogo = document.querySelector(".sidebar-logo-expanded");
  const collapsedLogo = document.querySelector(".sidebar-logo-collapsed");
  const sidebarTexts = document.querySelectorAll(".sidebar-text");

  toggleBtn.addEventListener("click", () => {
    sidebar.classList.toggle("w-64");
    sidebar.classList.toggle("w-20");
    expandedLogo.classList.toggle("hidden");
    collapsedLogo.classList.toggle("hidden");
    sidebarTexts.forEach(el => el.classList.toggle("hidden"));
  });
});
</script>
</body>
</html>