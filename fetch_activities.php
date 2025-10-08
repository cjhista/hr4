<?php
include 'db.php';
$recentActivities = [];
$sql = "SELECT * FROM activities ORDER BY created_at DESC LIMIT 15";
$res = $conn->query($sql);
if ($res) while ($r = $res->fetch_assoc()) {
    $recentActivities[] = [
        'icon' => ($r['status']=='warning')?'alert-triangle':(($r['status']=='success')?'check-circle':(($r['status']=='danger')?'trash-2':'info')),
        'text' => $r['text'],
        'time' => date('M d, Y g:i A', strtotime($r['created_at'])),
        'status' => $r['status']
    ];
}

if (empty($recentActivities)) {
    echo "<p class='text-gray-500 text-sm text-center'>No recent activities</p>";
} else {
    foreach ($recentActivities as $a) {
        echo "<li class='flex items-center justify-between px-3 py-3 hover:bg-gray-100 rounded-lg transition'>
                <div class='flex items-center gap-3'>
                  <i data-lucide='{$a['icon']}' class='h-5 w-5 text-gray-500'></i>
                  <div>
                    <p class='text-gray-700'>{$a['text']}</p>
                    <p class='text-xs text-gray-400'>{$a['time']}</p>
                  </div>
                </div>
                <span class='text-xs px-2 py-1 rounded-md font-medium " .
                  ($a['status']=='warning'?'bg-yellow-200 text-yellow-800':($a['status']=='success'?'bg-green-200 text-green-800':($a['status']=='danger'?'bg-red-200 text-red-800':'bg-blue-200 text-blue-800'))) . "'>
                  {$a['status']}
                </span>
              </li>";
    }
}
?>