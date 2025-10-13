<?php
// connection.php
// Database connection for CyberPanel MySQL

$servername = "localhost";        // host ng database mo
$username   = "hr4_hr4_user";     // user mula sa CyberPanel
$password   = "hr4123"; // palitan ng totoong password
$database   = "hr4_hr4_db";       // pangalan ng database mo

// Gumamit ng mysqli object-oriented connection
$conn = new mysqli($servername, $username, $password, $database);

// I-check kung may error sa connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
    // Optional: echo mo lang ito for testing
    // echo "✅ Connected successfully to database!";
}
?>
