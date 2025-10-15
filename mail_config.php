<?php
// mail_config.php - Gmail SMTP Configuration
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

class Mailer {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'christianjamesmabolochista@gmail.com'; // PALITAN ITO
        $this->mail->Password   = 'hvyx pzjt fero vxij';    // PALITAN ITO - Google App Password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;
        
        // Recipient settings
        $this->mail->setFrom('your.email@gmail.com', 'ATIERA Payroll System');
        $this->mail->isHTML(true);
    }
    
    public function sendOTP($toEmail, $toName, $otpCode) {
        try {
            $this->mail->addAddress($toEmail, $toName);
            
            $this->mail->Subject = 'Your ATIERA Login Verification Code';
            $this->mail->Body    = $this->getOTPEmailTemplate($otpCode);
            $this->mail->AltBody = "Your ATIERA verification code is: $otpCode. This code will expire in 10 minutes.";
            
            $this->mail->send();
            return ['success' => true, 'message' => 'OTP sent successfully.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => "Mailer Error: {$this->mail->ErrorInfo}"];
        }
    }
    
    private function getOTPEmailTemplate($otpCode) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { text-align: center; background: #1b2f73; color: white; padding: 20px; border-radius: 10px 10px 0 0; margin: -30px -30px 20px -30px; }
                .otp-code { font-size: 32px; font-weight: bold; text-align: center; color: #1b2f73; margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; letter-spacing: 5px; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 5px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>ATIERA Payroll System</h1>
                    <h2>Login Verification</h2>
                </div>
                
                <p>Hello,</p>
                <p>You are attempting to login to your ATIERA account. Use the verification code below:</p>
                
                <div class='otp-code'>$otpCode</div>
                
                <div class='warning'>
                    <strong>Important:</strong> This code will expire in 10 minutes. Do not share this code with anyone.
                </div>
                
                <p>If you didn't request this code, please ignore this email or contact support if you have concerns.</p>
                
                <div class='footer'>
                    <p>&copy; 2025 ATIERA BSIT 4101 CLUSTER 1. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
?>