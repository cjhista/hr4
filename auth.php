<?php
// auth.php - Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "db.php";

header('Content-Type: application/json');

// Simple debug function
function debug_log($message) {
    error_log("AUTH DEBUG: " . $message);
}

debug_log("Auth script started");

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

debug_log("Raw input: " . $raw);

// Check if JSON decoding failed
if (json_last_error() !== JSON_ERROR_NONE) {
    debug_log("JSON decode error: " . json_last_error_msg());
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data.']);
    exit;
}

// Check if this is OTP verification step
if (isset($input['otp_step']) && $input['otp_step'] === 'verify') {
    debug_log("OTP verification step");
    
    $user_id = $input['user_id'] ?? null;
    $otp_code = trim($input['otp_code'] ?? '');
    
    if (!$user_id || empty($otp_code)) {
        echo json_encode(['success' => false, 'message' => 'User ID and OTP code required.']);
        exit;
    }
    
    // Verify OTP
    $stmt = $conn->prepare("SELECT id, username, email, role, otp_code, otp_expires FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit;
    }
    
    $current_time = date('Y-m-d H:i:s');
    
    if ($user['otp_code'] === $otp_code && $current_time < $user['otp_expires']) {
        // OTP is valid - complete login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'] ?? '';
        
        // Clear OTP after successful verification
        $clear_stmt = $conn->prepare("UPDATE users SET otp_code = NULL, otp_expires = NULL WHERE id = ?");
        $clear_stmt->bind_param('i', $user['id']);
        $clear_stmt->execute();
        $clear_stmt->close();
        
        echo json_encode(['success' => true, 'message' => 'Login successful.']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP code.']);
        exit;
    }
}

// Regular login step
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';
$captcha = trim($input['captcha'] ?? '');

debug_log("Login attempt for: " . $username);

// CAPTCHA verification
if (!isset($_SESSION['captcha_code']) || empty($captcha)) {
    debug_log("CAPTCHA session missing or empty");
    echo json_encode(['success' => false, 'message' => 'CAPTCHA verification failed.', 'captcha_error' => true]);
    exit;
}

if (strtoupper($captcha) !== strtoupper($_SESSION['captcha_code'])) {
    debug_log("CAPTCHA mismatch: " . $captcha . " vs " . $_SESSION['captcha_code']);
    unset($_SESSION['captcha_code']);
    echo json_encode(['success' => false, 'message' => 'Invalid CAPTCHA code.', 'captcha_error' => true]);
    exit;
}

// Clear CAPTCHA after successful verification
unset($_SESSION['captcha_code']);

if ($username === '' || $password === '') {
    debug_log("Empty username or password");
    echo json_encode(['success' => false, 'message' => 'Username/email and password are required.']);
    exit;
}

// DB connection sanity check
if (!isset($conn) || !$conn) {
    debug_log("No database connection");
    echo json_encode(['success' => false, 'message' => 'Server configuration error.']);
    exit;
}

// Prepare query
$stmt = $conn->prepare("SELECT id, username, email, password, role, two_factor_enabled FROM users WHERE username = ? OR email = ? LIMIT 1");
if (!$stmt) {
    debug_log("Prepare failed: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Database query error.']);
    exit;
}

$stmt->bind_param('ss', $username, $username);
if (!$stmt->execute()) {
    debug_log("Execute failed: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Database execution error.']);
    exit;
}

$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    debug_log("User not found: " . $username);
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

debug_log("User found: " . $user['username']);

$dbPass = (string)($user['password'] ?? '');

// Verify password
$password_valid = false;

// 1) If DB password looks like a modern hash -> verify with password_verify
if (looks_like_hash($dbPass)) {
    debug_log("Using password_verify for hash");
    $password_valid = password_verify($password, $dbPass);
    
    if ($password_valid) {
        debug_log("Password verified successfully");
        // optionally rehash if algorithm changed
        if (password_needs_rehash($dbPass, PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $u = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($u) {
                $u->bind_param('si', $newHash, $user['id']);
                @$u->execute();
                $u->close();
            } else {
                debug_log("Rehash prepare failed: " . $conn->error);
            }
        }
    } else {
        debug_log("Password verification failed");
    }
} 
// 2) DB password does NOT look like hash -> fallback to plain text compare
else {
    debug_log("Using plain text comparison");
    $password_valid = ($password === $dbPass);
    
    // Migrate to hash for future
    if ($password_valid) {
        debug_log("Plain text password matched, migrating to hash");
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $u = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($u) {
            $u->bind_param('si', $newHash, $user['id']);
            if (!@$u->execute()) {
                debug_log("Hash update failed: " . $u->error);
            }
            $u->close();
        } else {
            debug_log("Hash update prepare failed: " . $conn->error);
        }
    } else {
        debug_log("Plain text password mismatch");
    }
}

if (!$password_valid) {
    debug_log("Invalid password for user: " . $username);
    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
    exit;
}

debug_log("Password valid, checking OTP requirement");

// Check if OTP is enabled for this user
if ($user['two_factor_enabled']) {
    debug_log("OTP enabled for user");
    
    // Send OTP
    $otp_response = sendOTP($user['id']);
    
    if ($otp_response['success']) {
        echo json_encode([
            'success' => true, 
            'requires_otp' => true,
            'user_id' => $user['id'],
            'message' => 'Verification code sent to your email.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send verification code. Please try again.'
        ]);
    }
    exit;
}

// No OTP required - login directly
debug_log("Login successful for user: " . $user['username']);
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'] ?? '';
echo json_encode(['success' => true, 'message' => 'Login successful.']);

function sendOTP($user_id) {
    $ch = curl_init();
    $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/send_otp.php';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['user_id' => $user_id]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
?>