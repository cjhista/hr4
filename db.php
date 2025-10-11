<?php
$servername = "localhost"; // or your hosting MySQL host
$username = "hr4_admin";   // your MySQL username
$password = "your_password_here"; // your MySQL password
$dbname = "hr4_db";        // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
