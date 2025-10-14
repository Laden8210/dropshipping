<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class EmailService
{
    private $mail;
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $from_email;
    private $from_name;
    private $app_url;

    public function __construct()
    {
        // Load configuration from environment or set defaults
        $this->smtp_host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $this->smtp_port = getenv('SMTP_PORT') ?: 587;
        $this->smtp_username = getenv('SMTP_USERNAME') ?: '';
        $this->smtp_password = getenv('SMTP_PASSWORD') ?: '';
        $this->from_email = getenv('SMTP_FROM') ?: 'noreply@luzvimidrop.com';
        $this->from_name = getenv('FROM_NAME') ?: 'LuzViMinDrop';
        $this->app_url = getenv('APP_URL') ?: 'http://localhost/dropshipping';

        $this->mail = new PHPMailer(true);
        $this->configureMailer();
    }

    private function configureMailer()
    {
        try {
            // Server settings
            $this->mail->isSMTP();
            $this->mail->Host = $this->smtp_host;
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $this->smtp_username;
            $this->mail->Password = $this->smtp_password;
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = $this->smtp_port;

            // Recipients
            $this->mail->setFrom($this->from_email, $this->from_name);
            $this->mail->isHTML(true);
        } catch (Exception $e) {
            error_log("Email configuration error: " . $e->getMessage());
        }
    }

    public function sendEmailVerification($userEmail, $userName, $verificationToken)
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($userEmail, $userName);

            $this->mail->Subject = 'Verify Your Email - LuzViMinDrop';
            
            $verificationUrl = $this->app_url . '/verify-email?token=' . $verificationToken;
            
            $this->mail->Body = $this->getEmailVerificationTemplate($userName, $verificationUrl);
            $this->mail->AltBody = "Hello $userName,\n\nPlease verify your email by clicking the following link:\n$verificationUrl\n\nIf you didn't create an account, please ignore this email.\n\nBest regards,\nLuzViMinDrop Team";

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email verification send error: " . $e->getMessage());
            return false;
        }
    }

    public function sendPasswordResetEmail($userEmail, $userName, $resetToken)
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($userEmail, $userName);

            $this->mail->Subject = 'Reset Your Password - LuzViMinDrop';
            
            $resetUrl = $this->app_url . '/reset-password?token=' . $resetToken;
            
            $this->mail->Body = $this->getPasswordResetTemplate($userName, $resetUrl);
            $this->mail->AltBody = "Hello $userName,\n\nYou requested to reset your password. Click the following link to reset it:\n$resetUrl\n\nThis link will expire in 1 hour.\n\nIf you didn't request this, please ignore this email.\n\nBest regards,\nLuzViMinDrop Team";

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Password reset email send error: " . $e->getMessage());
            return false;
        }
    }

    private function getEmailVerificationTemplate($userName, $verificationUrl)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Verify Your Email</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Welcome to LuzViMinDrop!</h1>
                    <p>Your AI-powered dropshipping partner</p>
                </div>
                <div class='content'>
                    <h2>Hello $userName,</h2>
                    <p>Thank you for registering with LuzViMinDrop! To complete your registration and start using our platform, please verify your email address by clicking the button below:</p>
                    
                    <div style='text-align: center;'>
                        <a href='$verificationUrl' class='button'>Verify Email Address</a>
                    </div>
                    
                    <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
                    <p style='word-break: break-all; background: #e9e9e9; padding: 10px; border-radius: 5px;'>$verificationUrl</p>
                    
                    <p><strong>Important:</strong> This verification link will expire in 24 hours for security reasons.</p>
                    
                    <p>If you didn't create an account with us, please ignore this email.</p>
                </div>
                <div class='footer'>
                    <p>Best regards,<br>The LuzViMinDrop Team</p>
                    <p>This is an automated message, please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    private function getPasswordResetTemplate($userName, $resetUrl)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Reset Your Password</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: #e74c3c; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Password Reset Request</h1>
                    <p>LuzViMinDrop Security</p>
                </div>
                <div class='content'>
                    <h2>Hello $userName,</h2>
                    <p>We received a request to reset your password for your LuzViMinDrop account. If you made this request, click the button below to reset your password:</p>
                    
                    <div style='text-align: center;'>
                        <a href='$resetUrl' class='button'>Reset Password</a>
                    </div>
                    
                    <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
                    <p style='word-break: break-all; background: #e9e9e9; padding: 10px; border-radius: 5px;'>$resetUrl</p>
                    
                    <div class='warning'>
                        <strong>Security Notice:</strong>
                        <ul>
                            <li>This link will expire in 1 hour for security reasons</li>
                            <li>If you didn't request this password reset, please ignore this email</li>
                            <li>Your password will remain unchanged until you create a new one</li>
                        </ul>
                    </div>
                </div>
                <div class='footer'>
                    <p>Best regards,<br>The LuzViMinDrop Security Team</p>
                    <p>This is an automated message, please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";
    }
}
