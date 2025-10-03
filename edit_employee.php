<?php
include 'db.php';
if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['id'])){
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $department = $_POST['department'];
    $position   = $_POST['position'];
    $status     = $_POST['status'];

    // Get old name for logging
    $result = $conn->query("SELECT first_name, last_name FROM employees WHERE id=$id");
    $row = $result->fetch_assoc();
    $oldName = $row['first_name'] . " " . $row['last_name'];
    $newName = $first_name . " " . $last_name;

    $sql = "UPDATE employees SET first_name=?, last_name=?, email=?, phone=?, department=?, position=?, status=? WHERE id=?";   
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $first_name, $last_name, $email, $phone, $department, $position, $status, $id);
    $stmt->execute();

    // Log activity
    $conn->query("INSERT INTO activities (text, status) VALUES ('Employee $oldName updated to $newName', 'info')");

    header("Location: employees.php");
    exit;
}
?>
