<?php
header("Content-Type: application/json");
include 'db_connect.php';

try {
    $payroll_id = $_POST['payroll_id'];
    $employee_id = $_POST['employee_id'];
    $employee_name = $_POST['employee_name'];
    $pay_period = $_POST['pay_period'];
    $gross_pay = floatval($_POST['gross_pay']);
    $deductions = floatval($_POST['deductions']);
    $net_pay = $gross_pay - $deductions;

    if ($payroll_id) {
        // Update existing payroll
        $stmt = $conn->prepare("UPDATE payroll SET employee_id = ?, employee_name = ?, pay_period = ?, gross_pay = ?, deductions = ?, net_pay = ? WHERE payroll_id = ?");
        $stmt->bind_param("ssssddi", $employee_id, $employee_name, $pay_period, $gross_pay, $deductions, $net_pay, $payroll_id);
        $stmt->execute();
        echo json_encode(["status" => "success", "message" => "Payroll updated successfully."]);
    } else {
        // Insert new payroll
        $stmt = $conn->prepare("INSERT INTO payroll (employee_id, employee_name, pay_period, gross_pay, deductions, net_pay) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssddd", $employee_id, $employee_name, $pay_period, $gross_pay, $deductions, $net_pay);
        $stmt->execute();
        echo json_encode(["status" => "success", "message" => "Payroll added successfully."]);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
}
