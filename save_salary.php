<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['employeeId'];
  $newSalary = $_POST['newSalary'];
  $effectiveDate = $_POST['effectiveDate'];

  $conn = new mysqli("localhost:3307", "root", "", "compensation");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  $salaryStr = "₱" . number_format($newSalary, 0);
  $sql = "UPDATE employees SET salary=?, nextReview=? WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssi", $salaryStr, $effectiveDate, $id);
  $stmt->execute();
  $stmt->close();
  $conn->close();
  header("Location: compensation.php");
  exit;
}
?>