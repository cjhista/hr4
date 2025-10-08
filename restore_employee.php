<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    $stmt = $conn->prepare("UPDATE employees SET is_deleted=0 WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Log restore
    $logText = "Employee ID $id restored";
    $stmtLog = $conn->prepare("INSERT INTO activities (ref_id, text, status) VALUES (?, ?, 'success')");
    $stmtLog->bind_param("is", $id, $logText);
    $stmtLog->execute();

    header("Location: employees.php");
    exit;
}
?>