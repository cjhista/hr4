<?php
// db.php - Database Connection File

$host = "localhost:5507";   // MySQL host (karaniwan: localhost o 127.0.0.1)
$user = "root";        // MySQL username (default sa XAMPP/MAMP ay root)
$pass = "";            // MySQL password (lagyan kung meron)
$db   = "hr4_db";      // Database name

// Gumawa ng koneksyon
$conn = new mysqli($host, $user, $pass, $db);

// I-check kung may error sa koneksyon
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
