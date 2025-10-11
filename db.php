<?php
// db.php - Live Server Database Connection
$host = "localhost";     // check in cPanel if different
$user = "hr4_user";      // your actual MySQL username
$pass = "StrongPassword123!";  // your MySQL password
$db   = "hr4_db";        // your actual database name

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    error_log("DB connection failed: " . $conn->connect_error);
    die("Database connection failed: " . $conn->connect_error);
}
?>
