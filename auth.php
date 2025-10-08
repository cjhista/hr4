<?php
session_start();
require "db.php";

// Ensure JSON input
$input = json_decode(file_get_contents("php://input"), true);
$username = trim($input["username"] ?? "");
$password = $input["password"] ?? "";

$response = ["success" => false, "message" => "Invalid request."];

if ($username && $password) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // ⚠️ TEMPORARY: Plain text password check (for testing only)
        if ($password === $user["password"]) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            $response = ["success" => true];
        } else {
            $response = ["success" => false, "message" => "Incorrect password."];
        }
    } else {
        $response = ["success" => false, "message" => "User not found."];
    }
}

header("Content-Type: application/json");
echo json_encode($response);
exit;
