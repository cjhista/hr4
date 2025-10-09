<?php
// db.php - Database Connection File (LIVE SERVER SETUP)
// I-update ang values na ito batay sa cPanel mo
$host = "localhost";  // Karaniwang "localhost" sa shared hosting. Kung may specific host (e.g., "mysql.hostinger.com"), gamitin mo. Huwag maglagay ng port kung hindi kailangan.
$user = "your_cpanel_username_dbuser";  // E.g., "user123_dbadmin" (hindi "root")
$pass = "your_strong_password_here";   // Ang password na ginawa mo sa cPanel
$db   = "your_cpanel_username_hr4_db"; // E.g., "user123_hr4_db" (prefix + db name)

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection (improve error handling for live)
if ($conn->connect_error) {
    error_log("DB connection failed: " . $conn->connect_error);  // Logs sa server error log
    // Para sa live, huwag mag-die agad—pero para sa testing, pwede mo i-uncomment ang die()
    // die("Database connection failed: " . $conn->connect_error);
    $conn = false;  // Set to false para ma-detect sa auth.php
}

// Set charset para sa UTF-8 (optional, pero good practice)
if ($conn) {
    $conn->set_charset("utf8mb4");
}
?>