<?php
$pageTitle = "HR 4 Dashboard";
$userName  = "User";

include 'db.php';

// ✅ Handle Soft Delete
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("UPDATE employees SET is_deleted = 1 WHERE id = $id");
    $conn->query("INSERT INTO activities (ref_id, text, status) VALUES ($id, 'Employee soft deleted (ID: $id)', 'warning')");
    header("Location: dashboard.php");
    exit;
}

// ✅ Handle Restore
if (isset($_GET['restore_id'])) {
    $id = intval($_GET['restore_id']);
    $conn->query("UPDATE employees SET is_deleted = 0 WHERE id = $id");
    $conn->query("INSERT INTO activities (ref_id, text, status) VALUES ($id, 'Employee restored (ID: $id)', 'success')");
    header("Location: dashboard.php");
    exit;
}

// ✅ Handle Permanent Delete
if (isset($_GET['permanent_delete_id'])) {
    $id = intval($_GET['permanent_delete_id']);
    $conn->query("DELETE FROM employees WHERE id = $id");
    $conn->query("DELETE FROM activities WHERE ref_id = $id");
    $conn->query("INSERT INTO activities (ref_id, text, status) VALUES ($id, 'Employee permanently deleted (ID: $id)', 'danger')");
    header("Location: dashboard.php");
    exit;
}

// ✅ Fetch totals
$totalEmployees = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE is_deleted = 0")->fetch_assoc()['total'] ?? 0;

// ✅ Dashboard values
$presentToday = $totalEmployees;
$monthlyPayroll = "₱145K";
$avgPerformance = "4.2/5";
$benefitsEnrolled = 13;
$pendingReviews = 3;

// ✅ Department Chart
$deptData = [];
$resDept = $conn->query("SELECT department, COUNT(*) as count FROM employees WHERE is_deleted = 0 GROUP BY department");
if ($resDept) while ($r = $resDept->fetch_assoc()) $deptData[] = ['label' => $r['department'], 'value' => $r['count']];
$deptLabels = array_column($deptData, 'label');
$deptValues = array_column($deptData, 'value');

// ✅ Status Chart
$statusData = ['Active' => 0, 'Inactive' => 0, 'Deleted' => 0];
$resStatus = $conn->query("SELECT status, is_deleted, COUNT(*) as count FROM employees GROUP BY status, is_deleted");
if ($resStatus) {
    while ($r = $resStatus->fetch_assoc()) {
        if ($r['is_deleted']) $statusData['Deleted'] += $r['count'];
        else $statusData[$r['status'] === 'ACTIVE' ? 'Active' : 'Inactive'] += $r['count'];
    }
}

// ✅ Recent Activities
$recentActivities = [];
$resAct = $conn->query("SELECT * FROM activities ORDER BY created_at DESC LIMIT 15");
if ($resAct) {
    while ($r = $resAct->fetch_assoc()) {
        $recentActivities[] = [
            'icon' => ($r['status'] == 'warning') ? 'alert-triangle' :
                      (($r['status'] == 'success') ? 'check-circle' :
                      (($r['status'] == 'danger') ? 'trash-2' : 'info')),
            'text' => $r['text'],
            'time' => date('M d, Y g:i A', strtotime($r['created_at'])),
            'status' => $r['status']
        ];
    }
}

// ✅ Employees
$employees = [];
$resEmp = $conn->query("SELECT * FROM employees ORDER BY id DESC");
if ($resEmp) while ($r = $resEmp->fetch_assoc()) $employees[] = $r;

// ✅ Chart JSON
$deptJson = json_encode(['labels' => $deptLabels, 'data' => $deptValues]);
$statusJson = json_encode(['labels' => array_keys($statusData), 'data' => array_values($statusData)]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $pageTitle ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="icon" type="image/png" href="logo2.png" />
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      lucide.createIcons();

      const dept = <?= $deptJson ?>;
      new Chart(document.getElementById('deptChart'), {
        type: 'pie',
        data: {
          labels: dept.labels,
          datasets: [{
            data: dept.data,
            backgroundColor: ['#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6']
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } }
        }
      });

      const status = <?= $statusJson ?>;
      new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: {
          labels: status.labels,
          datasets: [{
            label: 'Count',
            data: status.data,
            backgroundColor: ['#10B981','#FBBF24','#EF4444']
          }]
        },
        options: {
          responsive: true,
          scales: { y: { beginAtZero: true } },
          plugins: { legend: { display: false } }
        }
      });
    });
  </script>
</head>

<body class="h-screen overflow-hidden bg-gray-50">
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

<!-- ✅ Dashboard Overview Section -->
<div class="px-6 py-4 bg-gray-50 border-b">
  <h2 class="text-2xl font-bold text-gray-800">Dashboard Overview</h2>
  <p class="text-gray-500 text-sm">Welcome to your HR management system</p>
</div>


      <!-- Main -->
      <main class="p-6 space-y-4">
        <!-- Dashboard Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
          <?php
          $cards = [
            ["Employees", $totalEmployees, "users", "blue"],
            ["Present Today", $presentToday, "check-circle", "green"],
            ["Monthly Payroll", $monthlyPayroll, "dollar-sign", "yellow"],
            ["Performance", $avgPerformance, "bar-chart-2", "purple"],
            ["Benefits Enrolled", $benefitsEnrolled, "heart", "pink"],
            ["Pending Reviews", $pendingReviews, "clipboard-list", "indigo"]
          ];
          foreach ($cards as $c): ?>
            <div class="bg-white rounded-xl shadow p-6 text-center">
              <i data-lucide="<?= $c[2] ?>" class="w-8 h-8 mx-auto text-<?= $c[3] ?>-500"></i>
              <h3 class="mt-2 text-sm font-medium text-gray-500"><?= $c[0] ?></h3>
              <p class="mt-1 text-2xl font-bold text-gray-800"><?= $c[1] ?></p>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
          <!-- Employees by Department -->
          <div class="bg-white rounded-xl shadow p-4 flex items-center justify-between">
            <div class="w-1/2 space-y-2">
              <h3 class="text-base font-semibold text-gray-800 mb-3">Employees by Department</h3>
              <?php if (!empty($deptLabels)): ?>
                <ul class="text-gray-700 text-sm space-y-1">
                  <?php foreach ($deptLabels as $index => $label): ?>
                    <li class="flex items-center gap-2">
                      <span class="inline-block w-3 h-3 rounded-full" style="background-color: <?= ['#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6'][$index % 5] ?>;"></span>
                      <?= htmlspecialchars($label) ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <p class="text-gray-500 text-sm">No data available</p>
              <?php endif; ?>
            </div>
            <div class="w-1/2 flex justify-center">
              <div class="w-64 h-64">
                <canvas id="deptChart"></canvas>
              </div>
            </div>
          </div>

          <!-- Employee Status -->
          <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Employee Status</h3>
            <div class="w-full h-80">
              <canvas id="statusChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Employee List + Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
          <!-- Employee List -->
          <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Employee Recent List</h3>
            <div class="overflow-x-auto">
              <table class="table-auto w-full border-collapse border border-gray-200 text-sm">
                <thead>
                  <tr class="bg-gray-100 text-left text-gray-600">
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Department</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2 text-center">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($employees)): ?>
                    <tr><td colspan="5" class="text-center py-3 text-gray-500">No employees found</td></tr>
                  <?php else: foreach ($employees as $emp): ?>
                    <tr class="hover:bg-gray-50">
                      <td class="border p-2"><?= $emp['id'] ?></td>
                      <td class="border p-2"><?= htmlspecialchars($emp['first_name'].' '.$emp['last_name']) ?></td>
                      <td class="border p-2"><?= htmlspecialchars($emp['department'] ?? 'N/A') ?></td>
                      <td class="border p-2">
                        <?php if ($emp['is_deleted']): ?>
                          <span class="text-red-600 font-semibold">Deleted</span>
                        <?php else: ?>
                          <?= $emp['status'] === 'ACTIVE'
                            ? "<span class='text-green-600 font-semibold'>Active</span>"
                            : "<span class='text-yellow-600 font-semibold'>Inactive</span>" ?>
                        <?php endif; ?>
                      </td>
                      <td class="border p-2 text-center space-x-2">
                        <?php if ($emp['is_deleted']): ?>
                          <a href="?restore_id=<?= $emp['id'] ?>" class="text-green-500 hover:underline">Restore</a>
                          <a href="?permanent_delete_id=<?= $emp['id'] ?>" class="text-red-600 hover:underline">Delete</a>
                        <?php else: ?>
                          <a href="?delete_id=<?= $emp['id'] ?>" class="text-red-500 hover:underline">Delete</a>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="bg-white rounded-xl shadow p-6 flex flex-col">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h2>
            <ul class="divide-y divide-gray-100 overflow-y-auto pr-2" style="max-height: 27rem;">
              <?php if (empty($recentActivities)): ?>
                <p class="text-gray-500 text-sm text-center">No recent activities</p>
              <?php else: foreach ($recentActivities as $a): ?>
                <li class="flex items-center justify-between px-3 py-3 hover:bg-gray-100 rounded-lg transition">
                  <div class="flex items-center gap-3">
                    <i data-lucide="<?= $a['icon'] ?>" class="h-5 w-5 text-gray-500"></i>
                    <div>
                      <p class="text-gray-700"><?= $a['text'] ?></p>
                      <p class="text-xs text-gray-400"><?= $a['time'] ?></p>
                    </div>
                  </div>
                  <span class="text-xs px-2 py-1 rounded-md font-medium 
                    <?= $a['status'] == 'warning' ? 'bg-yellow-200 text-yellow-800' :
                       ($a['status'] == 'success' ? 'bg-green-200 text-green-800' :
                       ($a['status'] == 'danger' ? 'bg-red-200 text-red-800' : 'bg-blue-200 text-blue-800')) ?>">
                    <?= ucfirst($a['status']) ?>
                  </span>
                </li>
              <?php endforeach; endif; ?>
            </ul>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- ✅ Sidebar Toggle Script (from employees.php) -->
  <script>
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const expandedLogo = document.querySelector('.sidebar-logo-expanded');
  const collapsedLogo = document.querySelector('.sidebar-logo-collapsed');

  toggleBtn.addEventListener('click', () => {
    const texts = document.querySelectorAll('.sidebar-text');
    if (sidebar.classList.contains('w-64')) {
      sidebar.classList.replace('w-64', 'w-20');
      expandedLogo.classList.add('hidden');
      collapsedLogo.classList.remove('hidden');
      texts.forEach(t => t.classList.add('hidden'));
    } else {
      sidebar.classList.replace('w-20', 'w-64');
      expandedLogo.classList.remove('hidden');
      collapsedLogo.classList.add('hidden');
      texts.forEach(t => t.classList.remove('hidden'));
    }
    lucide.createIcons();
  });

  document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
  </script>
</body>
</html>