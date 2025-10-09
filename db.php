<?php
// db.php - Database Connection File
// Baguhin ang values ayon sa cPanel MySQL credentials mo

$host = "localhost";             // Kadalasan "localhost" lang sa cPanel
$user = "cpanel_mysql_user";     // Halimbawa: "mycpanel_hr4user"
$pass = "your_mysql_password";   // Yung password na nilagay mo nung nag create ng DB user
$db   = "cpanelprefix_hr4_db";   // Halimbawa: "mycpanel_hr4_db"

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
