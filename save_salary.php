<?php
include 'db.php'; // DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeeId   = $_POST['employeeId'];
    $newSalary    = $_POST['newSalary'];
    $effectiveDate = $_POST['effectiveDate'];

    // Validation (basic)
    if (empty($employeeId) || empty($newSalary) || empty($effectiveDate)) {
        die("⚠️ Missing required fields.");
    }

    // 1. Kunin muna yung current salary ng employee
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

    // 2. I-save sa salary_adjustments table (para may history)
    $sql = "INSERT INTO salary_adjustments (employee_id, old_salary, new_salary, effective_date) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $employeeId, $currentSalary, $newSalary, $effectiveDate);

    if ($stmt->execute()) {
        // 3. Update employee table with new salary
        $update = $conn->prepare("UPDATE employees SET salary = ? WHERE id = ?");
        $update->bind_param("ii", $newSalary, $employeeId);
        $update->execute();
        $update->close();

        // Redirect back to compensation page
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
