<?php
// db.php - Database Connection File
// UPDATE these values for your environment (localhost vs live)
$host = "localhost:3306; 
$user = "root";
$pass = "";
$db   = "hr4_db";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    // In production you might not want to echo this — we return JSON from endpoints instead.
    error_log("DB connection failed: " . $conn->connect_error);
    // For scripts that expect mysqli $conn, fail early:
    die("Database connection failed.");
}
?>