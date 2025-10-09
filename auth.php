<?php
// auth.php - JSON login endpoint
// Debugging: set to 0 in production
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

session_start();
require_once "db.php";

header('Content-Type: application/json');

// Helper: detect likely password hash format
function looks_like_hash($s) {
    if (!is_string($s) || $s === '') return false;
    return (strpos($s, '$2y$') === 0 || strpos($s, '$2a$') === 0 || strpos($s, '$2b$') === 0
            || strpos($s, '$argon2i$') === 0 || strpos($s, '$argon2id$') === 0);
}

// Only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Read JSON payload
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

// Basic input check
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Username/email and password are required.']);
    exit;
}

// DB connection sanity check
if (!isset($conn) || !$conn) {
    error_log("auth.php: Missing DB connection.");
    echo json_encode(['success' => false, 'message' => 'Server configuration error.']);
    exit;
}

// Prepare query
$stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ? LIMIT 1");
if (!$stmt) {
    error_log("auth.php prepare failed: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Database query error.']);
    exit;
}
$stmt->bind_param('ss', $username, $username);
if (!$stmt->execute()) {
    error_log("auth.php execute failed: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Database execution error.']);
    exit;
}
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

$dbPass = (string)($user['password'] ?? '');

// 1) If DB password looks like a modern hash -> verify with password_verify
if (looks_like_hash($dbPass)) {
    if (password_verify($password, $dbPass)) {
        // optionally rehash if algorithm changed
        if (password_needs_rehash($dbPass, PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $u = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($u) {
                $u->bind_param('si', $newHash, $user['id']);
                @$u->execute();
                $u->close();
            } else {
                error_log("auth.php: failed to prepare rehash update: " . $conn->error);
            }
        }
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'] ?? '';
        echo json_encode(['success' => true, 'message' => 'Login successful.']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
        exit;
    }
}

// 2) DB password does NOT look like hash -> fallback to plain text compare
if ($password === $dbPass) {
    // Migrate to hash for future
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $u = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    if ($u) {
        $u->bind_param('si', $newHash, $user['id']);
        if (!@$u->execute()) {
            error_log("auth.php: Failed to update password hash for user {$user['id']}: " . $u->error);
        }
        $u->close();
    } else {
        error_log("auth.php: Prepare failed for updating hash: " . $conn->error);
    }

    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'] ?? '';
    echo json_encode(['success' => true, 'message' => 'Login successful.']);
    exit;
}

// Wrong password
echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
exit;
