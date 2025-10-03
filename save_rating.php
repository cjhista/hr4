<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['employeeId'];
  $newRating = $_POST['newRating'];

  $conn = new mysqli("localhost:3307", "root", "", "hr4_db");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql = "UPDATE employees SET rating=? WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("di", $newRating, $id);
  $stmt->execute();
  $stmt->close();
  $conn->close();

  header("Location: compensation.php");
  exit;
}
?>
