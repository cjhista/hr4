<?php
// send_otp.php - Send OTP to user via Gmail
session_start();
require_once "db.php";
require_once "mail_config.php";

header('Content-Type: application/json');

function generateOTP($length = 6) {
    return sprintf("%0" . $length . "d", mt_rand(0, pow(10, $length) - 1));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = $input['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User ID required.']);
    exit;
}

// Get user details
$stmt = $conn->prepare("SELECT id, username, email, full_name FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

// Generate OTP
$otp_code = generateOTP();
$otp_expires = date('Y-m-d H:i:s', time() + 600); // 10 minutes

// Save OTP to database
$stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expires = ? WHERE id = ?");
$stmt->bind_param('ssi', $otp_code, $otp_expires, $user_id);

if ($stmt->execute()) {
    $stmt->close();
    
    // Send OTP via Email
    $mailer = new Mailer();
    $emailResult = $mailer->sendOTP($user['email'], $user['full_name'] ?? $user['username'], $otp_code);
    
    if ($emailResult['success']) {
        echo json_encode([
            'success' => true, 
            'message' => 'Verification code sent to your email.'
        ]);
    } else {
        // If email fails, still return success but log the error
        error_log("Email sending failed: " . $emailResult['message']);
        echo json_encode([
            'success' => true, 
            'message' => 'Verification code generated. Email delivery issue.',
            'debug_otp' => $otp_code // For debugging only
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to generate OTP.']);
}
?>