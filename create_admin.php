<?php
require 'db.php';

$username = 'admin';
$email = 'admin@example.com';
$password = '123'; // palitan in production

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $username, $email, $hash);

if ($stmt->execute()) {
    echo "Admin created.";
} else {
    echo "Error: " . $stmt->error;
}
