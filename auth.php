<?php
// auth.php
session_start();
header('Content-Type: application/json; charset=utf-8');

// require db connection
require 'db.php';

// read JSON body
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if (!$username || !$password) {
    echo json_encode(["success" => false, "message" => "Please enter username/email and password."]);
    exit;
}

// fetch user by username OR email
$stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ? LIMIT 1");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(["success" => false, "message" => "Server error."]);
    exit;
}
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(["success" => false, "message" => "Invalid username or password."]);
    exit;
}

$hash = $user['password'];
if (password_verify($password, $hash)) {
    // login success
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['username'] = $user['username'];
    echo json_encode(["success" => true]);
    exit;
} else {
    echo json_encode(["success" => false, "message" => "Invalid username or password."]);
    exit;
}
