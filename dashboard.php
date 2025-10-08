<?php
// dashboard.php
session_start();

// Protect page: redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Basic page meta
$pageTitle = "HR 4 Dashboard";
$userName  = htmlspecialchars($_SESSION['username'] ?? 'User', ENT_QUOTES, 'UTF-8');

require_once 'db.php'; // expects $conn

// ---------------------
// Handle actions (soft delete / restore / permanent delete)
// ---------------------
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("UPDATE employees SET is_deleted = 1 WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // log activity (if activities table exists)
    $stmt2 = $conn->prepare("INSERT INTO activities (ref_id, text, status) VALUES (?, ?, 'warning')");
    $text = "Employee soft deleted (ID: $id)";
    $stmt2->bind_param('is', $id, $text);
    @$stmt2->execute();

    header("Location: dashboard.php");
    exit;
}

if (isset($_GET['restore_id'])) {
    $id = intval($_GET['restore_id']);
    $stmt = $conn->prepare("UPDATE employees SET is_deleted = 0 WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt2 = $conn->prepare("INSERT INTO activities (ref_id, text, status) VALUES (?, ?, 'success')");
    $text = "Employee restored (ID: $id)";
    $stmt2->bind_param('is', $id, $text);
    @$stmt2->execute();

    header("Location: dashboard.php");
    exit;
}

if (isset($_GET['permanent_delete_id'])) {
    $id = intval($_GET['permanent_delete_id']);
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // delete related activities
    $stmt2 = $conn->prepare("DELETE FROM activities WHERE ref_id = ?");
    $stmt2->bind_param('i', $id);
    @$stmt2->execute();

    $stmt3 = $conn->prepare("INSERT INTO activities (ref_id, text, status) VALUES (?, ?, 'danger')");
    $text = "Employee permanently deleted (ID: $id)";
    $stmt3->bind_param('is', $id, $text);
    @$stmt3->execute();

    header("Location: dashboard.php");
    exit;
}

// ---------------------
// Fetch totals & summary values
// ---------------------
$totalEmployees = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE is_deleted = 0");
if ($res) {
    $row = $res->fetch_assoc();
    $totalEmployees = (int)($row['total'] ?? 0);
}

// sample KPIs (you can replace with real calculations)
$presentToday = $totalEmployees;             // placeholder
$monthlyPayroll = "₱145K";                   // placeholder
$avgPerformance = "4.2/5";                   // placeholder
$benefitsEnrolled = 13;                      // placeholder
$pendingReviews = 3;                         // placeholder

// ---------------------
// Department chart data
// ---------------------
$deptData = [];
$stmt = $conn->prepare("SELECT department, COUNT(*) AS count FROM employees WHERE is_deleted = 0 GROUP BY department");
if ($stmt) {
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $deptData[] = ['label' => $r['department'] ?? 'N/A', 'value' => (int)$r['count']];
    }
    $stmt->close();
}
$deptLabels = array_column($deptData, 'label');
$deptValues = array_column($deptData, 'value');

// ---------------------
// Status chart data
// ---------------------
$statusData = ['Active' => 0, 'Inactive' => 0, 'Deleted' => 0];
$stmt = $conn->prepare("SELECT status, is_deleted, COUNT(*) as count FROM employees GROUP BY status, is_deleted");
if ($stmt) {
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $is_deleted = (int)$r['is_deleted'];
        $count = (int)$r['count'];
        $status = strtoupper($r['status'] ?? 'INACTIVE');
        if ($is_deleted) {
            $statusData['Deleted'] += $count;
        } else {
            if ($status === 'ACTIVE') $statusData['Active'] += $count;
            else $statusData['Inactive'] += $count;
        }
    }
    $stmt->close();
}

// ---------------------
// Recent activities
// ---------------------
$recentActivities = [];
$stmt = $conn->prepare("SELECT * FROM activities ORDER BY created_at DESC LIMIT 15");
if ($stmt) {
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $icon = 'info';
        if ($r['status'] === 'warning') $icon = 'alert-triangle';
        if ($r['status'] === 'success') $icon = 'check-circle';
        if ($r['status'] === 'danger') $icon = 'trash-2';
        $recentActivities[] = [
            'icon' => $icon,
            'text' => $r['text'],
            'time' => date('M d, Y g:i A', strtotime($r['created_at'])),
            'status' => $r['status']
        ];
    }
    $stmt->close();
}

// ---------------------
// Employees list
// ---------------------
$employees = [];
$stmt = $conn->prepare("SELECT * FROM employees ORDER BY id DESC");
if ($stmt) {
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $employees[] = $r;
    }
    $stmt->close();
}

// ---------------------
// JSON for charts
// ---------------------
$deptJson = json_encode(['labels' => $deptLabels, 'data' => $deptValues]);
$statusJson = json_encode(['labels' => array_keys($statusData), 'data' => array_values($statusData)]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="icon" type="image/png" href="logo2.png" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    /* small safe layout tweaks */
    body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; }
  </style>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      lucide.createIcons();

      // Department pie
      const dept = <?= $deptJson ?>;
      const deptCtx = document.getElementById('deptChart');
      if (deptCtx && dept.labels.length) {
        new Chart(deptCtx, {
          type: 'pie',
          data: {
            labels: dept.labels,
            datasets: [{ data: dept.data }]
          },
          options: { responsive: true, plugins: { legend: { display: false } } }
        });
      }

      // Status bar
      const status = <?= $statusJson ?>;
      const statusCtx = document.getElementById('statusChart');
      if (statusCtx) {
        new Chart(statusCtx, {
          type: 'bar',
          data: {
            labels: status.labels,
            datasets: [{ label: 'Count', data: status.data }]
          },
          options: { responsive: true, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
        });
      }
    });
  </script>
</head>
<body class="h-screen overflow-hidden bg-gray-50">
  <div class="flex h-full">
    <!-- Sidebar (optional) -->
    <?php if (file_exists('sidebar.php')) include 'sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-y-auto">
      <!-- Header -->
      <div class="flex items-center justify-between border-b py-4 bg-white sticky top-0 z-50 px-6">
        <div class="flex items-center gap-4">
          <button id="sidebarToggle" class="text-gray-600 hover:text-gray-800 focus:outline-none">
            <i id="toggleIcon" data-lucide="menu" class="w-6 h-6"></i>
          </button>
          <h1 class="text-lg font-bold text-gray-800">HR 4 MANAGEMENT SYSTEM</h1>
        </div>
        <div class="flex items-center gap-4">
          <div class="text-sm text-gray-600">Hello, <strong><?= $userName ?></strong></div>
          <a href="logout.php" class="text-sm text-red-600 hover:underline">Sign out</a>
        </div>
      </div>

      <!-- Dashboard Overview -->
      <div class="px-6 py-4 bg-gray-50 border-b">
        <h2 class="text-2xl font-bold text-gray-800">Dashboard Overview</h2>
        <p class="text-gray-500 text-sm">Welcome to your HR management system</p>
      </div>

      <main class="p-6 space-y-4">
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
          <?php
          $cards = [
            ["Employees", $totalEmployees],
            ["Present Today", $presentToday],
            ["Monthly Payroll", $monthlyPayroll],
            ["Performance", $avgPerformance],
            ["Benefits Enrolled", $benefitsEnrolled],
            ["Pending Reviews", $pendingReviews]
          ];
          foreach ($cards as $c): ?>
            <div class="bg-white rounded-xl shadow p-6 text-center">
              <h3 class="mt-2 text-sm font-medium text-gray-500"><?= htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8') ?></h3>
              <p class="mt-1 text-2xl font-bold text-gray-800"><?= htmlspecialchars((string)$c[1], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
          <div class="bg-white rounded-xl shadow p-4 flex items-center justify-between">
            <div class="w-1/2 space-y-2">
              <h3 class="text-base font-semibold text-gray-800 mb-3">Employees by Department</h3>
              <?php if (!empty($deptLabels)): ?>
                <ul class="text-gray-700 text-sm space-y-1">
                  <?php foreach ($deptLabels as $index => $label): ?>
                    <li class="flex items-center gap-2">
                      <span class="inline-block w-3 h-3 rounded-full" style="background-color: <?= ['#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6'][$index % 5] ?>;"></span>
                      <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
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

          <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Employee Status</h3>
            <div class="w-full h-80">
              <canvas id="statusChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Employee list & Recent activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
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
                      <td class="border p-2"><?= (int)$emp['id'] ?></td>
                      <td class="border p-2"><?= htmlspecialchars(($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                      <td class="border p-2"><?= htmlspecialchars($emp['department'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                      <td class="border p-2">
                        <?php if (!empty($emp['is_deleted'])): ?>
                          <span class="text-red-600 font-semibold">Deleted</span>
                        <?php else: ?>
                          <?= (isset($emp['status']) && strtoupper($emp['status']) === 'ACTIVE')
                             ? "<span class='text-green-600 font-semibold'>Active</span>"
                             : "<span class='text-yellow-600 font-semibold'>Inactive</span>" ?>
                        <?php endif; ?>
                      </td>
                      <td class="border p-2 text-center space-x-2">
                        <?php if (!empty($emp['is_deleted'])): ?>
                          <a href="?restore_id=<?= (int)$emp['id'] ?>" class="text-green-500 hover:underline">Restore</a>
                          <a href="?permanent_delete_id=<?= (int)$emp['id'] ?>" class="text-red-600 hover:underline">Delete</a>
                        <?php else: ?>
                          <a href="?delete_id=<?= (int)$emp['id'] ?>" class="text-red-500 hover:underline">Delete</a>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow p-6 flex flex-col">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h2>
            <ul class="divide-y divide-gray-100 overflow-y-auto pr-2" style="max-height: 27rem;">
              <?php if (empty($recentActivities)): ?>
                <p class="text-gray-500 text-sm text-center">No recent activities</p>
              <?php else: foreach ($recentActivities as $a): ?>
                <li class="flex items-center justify-between px-3 py-3 hover:bg-gray-100 rounded-lg transition">
                  <div class="flex items-center gap-3">
                    <i data-lucide="<?= htmlspecialchars($a['icon'], ENT_QUOTES, 'UTF-8') ?>" class="h-5 w-5 text-gray-500"></i>
                    <div>
                      <p class="text-gray-700"><?= htmlspecialchars($a['text'], ENT_QUOTES, 'UTF-8') ?></p>
                      <p class="text-xs text-gray-400"><?= htmlspecialchars($a['time'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                  </div>
                  <span class="text-xs px-2 py-1 rounded-md font-medium 
                    <?= $a['status'] == 'warning' ? 'bg-yellow-200 text-yellow-800' :
                       ($a['status'] == 'success' ? 'bg-green-200 text-green-800' :
                       ($a['status'] == 'danger' ? 'bg-red-200 text-red-800' : 'bg-blue-200 text-blue-800')) ?>">
                    <?= ucfirst(htmlspecialchars($a['status'], ENT_QUOTES, 'UTF-8')) ?>
                  </span>
                </li>
              <?php endforeach; endif; ?>
            </ul>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Sidebar toggle script (if sidebar exists) -->
  <script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const expandedLogo = document.querySelector('.sidebar-logo-expanded');
    const collapsedLogo = document.querySelector('.sidebar-logo-collapsed');

    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        if (!sidebar) return;
        const texts = document.querySelectorAll('.sidebar-text');
        if (sidebar.classList.contains('w-64')) {
          sidebar.classList.replace('w-64', 'w-20');
          if (expandedLogo) expandedLogo.classList.add('hidden');
          if (collapsedLogo) collapsedLogo.classList.remove('hidden');
          texts.forEach(t => t.classList.add('hidden'));
        } else {
          sidebar.classList.replace('w-20', 'w-64');
          if (expandedLogo) expandedLogo.classList.remove('hidden');
          if (collapsedLogo) collapsedLogo.classList.add('hidden');
          texts.forEach(t => t.classList.remove('hidden'));
        }
        lucide.createIcons();
      });
    }

    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
  </script>
</body>
</html>
