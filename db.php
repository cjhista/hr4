<?php
$host = "localhost:3307";
$user = "root";      
$pass = "";          
$db   = "hr4_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>