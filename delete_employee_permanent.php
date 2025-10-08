<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    // Get employee name before delete (for logging)
    $result = $conn->prepare("SELECT first_name, last_name FROM employees WHERE id=?");
    $result->bind_param("i", $id);
    $result->execute();
    $res = $result->get_result();

    if ($res && $row = $res->fetch_assoc()) {
        $fullName = $row['first_name'] . " " . $row['last_name'];

        // Permanent delete employee
        $stmt = $conn->prepare("DELETE FROM employees WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Log permanent deletion
        $logText = "Employee $fullName permanently deleted (ID: $id)";
        $stmtLog = $conn->prepare("INSERT INTO activities (ref_id, text, status) VALUES (?, ?, 'warning')");
        $stmtLog->bind_param("is", $id, $logText);
        $stmtLog->execute();
    }

    header("Location: employees.php");
    exit;
}
?>
