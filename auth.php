<?php
session_start();
require "db.php";

// ✅ Basahin JSON body
$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if (!$username || !$password) {
    echo json_encode(["success" => false, "message" => "Missing username or password."]);
    exit;
}

// ✅ Hanapin user by username or email
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $user = $res->fetch_assoc()) {
    // ✅ Verify password
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        echo json_encode(["success" => true]);
        exit;
    } else {
        echo json_encode(["success" => false, "message" => "Incorrect password."]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "User not found."]);
    exit;
}
