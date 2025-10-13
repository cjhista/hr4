<?php
$servername = "localhost";  // CyberPanel uses localhost for MySQL
$username   = "hr4_hr4_user";
$password   = "hr4123";
$dbname     = "hr4_hr4_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>