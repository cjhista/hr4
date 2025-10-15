<?php
// db.php - Simple database connection
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "hr4_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to prevent issues
$conn->set_charset("utf8mb4");
?>