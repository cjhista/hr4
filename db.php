<?php
/**
 * db.php
 * Secure MySQL connection file for CyberPanel hosting
 * Author: ChatGPT (verified configuration)
 * Updated: 2025-10-13
 */

// === Database credentials ===
$servername = "localhost";          // Hostname (CyberPanel same server)
$username   = "hr4_hr4_user";       // Database user (from CyberPanel)
$password   = "hr4123"; // ⚠️ Palitan ng totoong password mo
$database   = "hr4_hr4_db";         // Database name (from CyberPanel)

// === Error logging setup ===
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/db_error.log'); // Log file will be saved here
error_reporting(E_ALL);

// === Connect to MySQL ===
$conn = new mysqli($servername, $username, $password, $database);

// === Check connection ===
if ($conn->connect_error) {
    // Log connection error with timestamp
    $error_message = "[" . date("Y-m-d H:i:s") . "] Database connection failed: " . $conn->connect_error . "\n";
    error_log($error_message, 3, __DIR__ . '/db_error.log');

    // Show safe message (no sensitive info)
    die("⚠️ Unable to connect to the database. Please contact the administrator.");
}

// === Set UTF-8 charset (important for special characters) ===
if (!$conn->set_charset("utf8mb4")) {
    $charset_error = "[" . date("Y-m-d H:i:s") . "] Charset load error: " . $conn->error . "\n";
    error_log($charset_error, 3, __DIR__ . '/db_error.log');
}

// === Optional: Auto-reconnect system (helps if MySQL drops connection) ===
$retries = 0;
$max_retries = 3;
while ($conn->connect_errno && $retries < $max_retries) {
    $retries++;
    sleep(2); // wait 2 seconds
    $conn = new mysqli($servername, $username, $password, $database);
}

// === Final connection check ===
if ($conn->connect_errno) {
    $final_error = "[" . date("Y-m-d H:i:s") . "] Final connection failure after retries: " . $conn->connect_error . "\n";
    error_log($final_error, 3, __DIR__ . '/db_error.log');
    die("⚠️ Database connection failed after multiple attempts.");
}

// === (Optional) Test message — comment this out for production ===
// echo "✅ Successfully connected to hr4_hr4_db";

?>
