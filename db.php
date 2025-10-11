<?php
// db.php - Database Connection File
// Baguhin ang values ayon sa cPanel MySQL credentials mo

$host = "localhost";
$user = "cpanelusername_dbuser";
$pass = "db_password";
$db   = "hr4_db";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    error_log("DB connection failed: " . $conn->connect_error);
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}
?>
