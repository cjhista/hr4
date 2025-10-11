<?php
$host = "localhost";
$user = "your_live_db_user";
$pass = "your_live_db_password";
$db   = "your_live_db_name";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "✅ Connected successfully!";
?>
