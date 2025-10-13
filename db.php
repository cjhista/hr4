<?php
// connection.php
// Secure MySQL connection with error logging (CyberPanel)

// Database credentials
$servername = "localhost";        // host ng database
$username   = "hr4_hr4_user";     // user mula sa CyberPanel
$password   = "hr4123"; // <-- palitan ng totoong password
$database   = "hr4_hr4_db";       // pangalan ng database

// Optional: Enable detailed error logging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/db_error.log'); // log file in same folder
error_reporting(E_ALL);

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    // Log error message with timestamp
    $error_message = "[" . date("Y-m-d H:i:s") . "] Database connection failed: " . $conn->connect_error . "\n";
    error_log($error_message, 3, __DIR__ . '/db_error.log');

    // Optionally show friendly message to users
    die("⚠️ Unable to connect to the database. Please contact the administrator.");
} else {
    // Uncomment line below only for testing
    // echo "✅ Database connected successfully!";
}
?>
