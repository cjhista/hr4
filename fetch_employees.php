
<?php
include 'db.php';

$res = $conn->query("SELECT id, first_name, last_name, department, status, is_deleted FROM employees ORDER BY id DESC LIMIT 10");

if ($res && $res->num_rows > 0) {
    while ($emp = $res->fetch_assoc()) {
        echo '<tr class="border-b hover:bg-gray-50">';
        echo '<td class="px-4 py-3 font-medium text-gray-700">' . $emp['id'] . '</td>';
        echo '<td class="px-4 py-3">' . htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) . '</td>';
        echo '<td class="px-4 py-3">' . htmlspecialchars($emp['department'] ?? 'N/A') . '</td>';
        echo '<td class="px-4 py-3">';
        echo $emp['is_deleted'] 
            ? '<span class="text-yellow-700 bg-yellow-100 px-2 py-1 rounded-md text-xs">Inactive</span>'
            : '<span class="text-green-700 bg-green-100 px-2 py-1 rounded-md text-xs">Active</span>';
        echo '</td>';
        echo '<td class="px-4 py-3 text-center space-x-2">';
        if ($emp['is_deleted']) {
            echo '<a href="?restore_id=' . $emp['id'] . '" class="text-blue-600 hover:underline">Restore</a>';
            echo '<a href="?permanent_delete_id=' . $emp['id'] . '" class="text-red-600 hover:underline">Permanent Delete</a>';
        } else {
            echo '<a href="?delete_id=' . $emp['id'] . '" class="text-red-600 hover:underline">Delete</a>';
        }
        echo '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="5" class="text-center py-4 text-gray-500">No employees found</td></tr>';
}
?>