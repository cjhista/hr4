<?php
session_start();

// Generate new CAPTCHA code
$captcha_code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
$_SESSION['captcha_code'] = $captcha_code;

echo json_encode(['success' => true, 'captcha_code' => $captcha_code]);
?>