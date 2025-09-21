<?php
$host = "localhost:3307";
$user = "root";
$pass = "";
$db   = "compensation";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $employeeId    = intval($_POST['employeeId']);
  $newSalary     = intval($_POST['newSalary']);
  $effectiveDate = $_POST['effectiveDate'];

  // Update employees table
  $sql = "UPDATE employees 
          SET salary = ?, lastIncrease = ?, lastIncreaseDate = ? 
          WHERE id = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iisi", $newSalary, $newSalary, $effectiveDate, $employeeId);

  if ($stmt->execute()) {
    header("Location: compensation.php?success=1");
    exit();
  } else {
    echo "Error: " . $stmt->error;
  }
}

$conn->close();
?>