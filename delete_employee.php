<?php
include 'db.php';
if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['id'])){
    $id = $_POST['id'];

    // Get employee name for logging
    $result = $conn->query("SELECT first_name, last_name FROM employees WHERE id=$id");
    $row = $result->fetch_assoc();
    $fullName = $row['first_name'] . " " . $row['last_name'];

    $stmt = $conn->prepare("DELETE FROM employees WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Log activity
    $conn->query("INSERT INTO activities (text, status) VALUES ('Employee $fullName deleted', 'warning')");

    header("Location: employees.php");
    exit;
}
?>