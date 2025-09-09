<?php
include 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeeId    = intval($_POST['employeeId']);
    $newSalary     = floatval($_POST['newSalary']);
    $effectiveDate = $_POST['effectiveDate'];

    if (empty($employeeId) || empty($newSalary) || empty($effectiveDate)) {
        die("⚠️ Missing required fields.");
    }

    // Get current salary
    $sql = "SELECT salary FROM employees WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $currentSalary = $row['salary'];
    } else {
        die("⚠️ Employee not found.");
    }
    $stmt->close();

    // Insert salary adjustment history
    $sql = "INSERT INTO salary_adjustments (employee_id, old_salary, new_salary, effective_date) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $employeeId, $currentSalary, $newSalary, $effectiveDate);

    if ($stmt->execute()) {
        // Update employee salary
        $update = $conn->prepare("UPDATE employees SET salary = ?, last_increase = ?, last_increase_date = ? WHERE id = ?");
        $increase = $newSalary - $currentSalary;
        $update->bind_param("ddsi", $newSalary, $increase, $effectiveDate, $employeeId);
        $update->execute();
        $update->close();

        header("Location: compensation.php?success=1");
        exit;
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    die("⚠️ Invalid request.");
}
?>
