<?php
// db.php - Database connection
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
?>