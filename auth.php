<?php
// ===============================
// auth.php - JSON login endpoint
// Debug Mode: set to 1 to see actual PHP errors
// ===============================

// 🧩 Enable full error display temporarily
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
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

// ✅ Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ✅ Read JSON payload safely
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

// ✅ Basic input validation
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Username/email and password are required.']);
    exit;
}

// ✅ Check DB connection
if (!isset($conn) || !$conn) {
    error_log("auth.php: Missing DB connection.");
    echo json_encode(['success' => false, 'message' => 'Server configuration error.']);
    exit;
}

// ✅ Prepare and execute query
$stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ? LIMIT 1");
if (!$stmt) {
    error_log("auth.php prepare failed: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Database query error: ' . $conn->error]);
    exit;
}

$stmt->bind_param('ss', $username, $username);

if (!$stmt->execute()) {
    error_log("auth.php execute failed: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Database execution error: ' . $stmt->error]);
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

// ✅ If password is a hash, verify normally
if (looks_like_hash($dbPass)) {
    if (password_verify($password, $dbPass)) {
        // Optionally rehash if algorithm updated
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

        // ✅ Set session variables
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

// ✅ Fallback: plain-text password match (for legacy users)
if ($password === $dbPass) {
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

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'] ?? '';
    echo json_encode(['success' => true, 'message' => 'Login successful.']);
    exit;
}

// ❌ If password incorrect
echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
exit;
