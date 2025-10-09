<?php
// auth.php - JSON login endpoint
session_start();
require_once "db.php";

header('Content-Type: application/json');

// Helper: detect likely password hash format
function looks_like_hash($s) {
    if (!is_string($s) || $s === '') return false;
    return (strpos($s, '$2y$') === 0 || strpos($s, '$2a$') === 0 || strpos($s, '$2b$') === 0
            || strpos($s, '$argon2i$') === 0 || strpos($s, '$argon2id$') === 0);
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Read JSON payload
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Username/email and password are required.']);
    exit;
}

// Query user
$stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ? LIMIT 1");
if (!$stmt) {
    error_log("auth.php prepare failed: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Database query error.']);
    exit;
}
$stmt->bind_param('ss', $username, $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

$dbPass = (string)($user['password'] ?? '');

// Case 1: Hashed password
if (looks_like_hash($dbPass)) {
    if (password_verify($password, $dbPass)) {
        // Update hash if needed
        if (password_needs_rehash($dbPass, PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $u = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($u) {
                $u->bind_param('si', $newHash, $user['id']);
                $u->execute();
                $u->close();
            }
        }

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

// Case 2: Plain text password (migrate to hash)
if ($password === $dbPass) {
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $u = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    if ($u) {
        $u->bind_param('si', $newHash, $user['id']);
        $u->execute();
        $u->close();
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'] ?? '';
    echo json_encode(['success' => true, 'message' => 'Login successful.']);
    exit;
}

// Wrong password
echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
exit;
?>
