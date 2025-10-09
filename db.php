<?php
// db.php - Database Connection File (production-friendly)
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: 3306;      // default MySQL port
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'hr4_db';

// Create connection (use numeric port param)
$conn = new mysqli($host, $user, $pass, $db, (int)$port);

// Check connection
if ($conn->connect_error) {
    // log error for admin, but DO NOT echo/die (so auth.php can return JSON)
    error_log("DB connection failed: " . $conn->connect_error);
    // mark as false so auth.php can detect it
    $conn = false;
}
?>
