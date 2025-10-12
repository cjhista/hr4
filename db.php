<?php
$host = "localhost";
$user = "atiera_hr4_user";  // your exact cPanel username prefix + user
$pass = "YourStrongPassword";
$db   = "atiera_hr4_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    error_log("DB connection failed: " . $conn->connect_error);
    die("Database connection failed.");
}
$conn->set_charset("utf8mb4");
?>