<?php

require_once __DIR__ . '/../core/config.php';
date_default_timezone_set('Asia/Manila');

class TokenService
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Generate a secure random token
     */
    public function generateToken($length = 64)
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Create email verification token
     */
    public function createEmailVerificationToken($userId)
    {
        $token = $this->generateToken();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Clean up any existing tokens for this user
        $this->cleanupEmailVerificationTokens($userId);

        $query = "INSERT INTO email_verification_tokens (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("sss", $userId, $token, $expiresAt);
        $result = $stmt->execute();
        $stmt->close();

        return $result ? $token : false;
    }

    /**
     * Create password reset token
     */
    public function createPasswordResetToken($userId)
    {
        $token = $this->generateToken();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Clean up any existing tokens for this user
        $this->cleanupPasswordResetTokens($userId);

        $query = "INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("sss", $userId, $token, $expiresAt);
        $result = $stmt->execute();
        $stmt->close();

        return $result ? $token : false;
    }

    /**
     * Validate email verification token
     */
    public function validateEmailVerificationToken($token)
    {
        $query = "SELECT user_id, expires_at FROM email_verification_tokens 
                  WHERE token = ? AND is_used = FALSE";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("s", $token);

        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return false;
        }

        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            error_log("No valid token found for: " . $token);
            $stmt->close();
            return false;
        }

        $row = $result->fetch_assoc();
        $userId = $row['user_id'];
        $expiresAt = $row['expires_at'];

        // Debug logging
        error_log("Token found - User ID: " . $userId . ", Expires: " . $expiresAt);
        error_log("Current time: " . date('Y-m-d H:i:s'));

        // Check if token is expired
        // if (strtotime($expiresAt) < time()) {
        //     error_log("Token expired - Expires: " . $expiresAt . ", Current: " . date('Y-m-d H:i:s'));
        //     $stmt->close();
        //     return false;
        // }

        $stmt->close();

        // Mark token as used
        if (!$this->markEmailVerificationTokenAsUsed($token)) {
            error_log("Failed to mark token as used: " . $token);
            return false;
        }

        return $userId;
    }
    /**
     * Validate password reset token
     */
    public function validatePasswordResetToken($token)
    {
        $query = "SELECT user_id FROM password_reset_tokens 
                  WHERE token = ? AND expires_at > NOW() AND is_used = FALSE";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return false;
        }

        $row = $result->fetch_assoc();
        $userId = $row['user_id'];
        $stmt->close();

        return $userId;
    }

    /**
     * Mark email verification token as used
     */
    private function markEmailVerificationTokenAsUsed($token)
    {
        $query = "UPDATE email_verification_tokens SET is_used = TRUE WHERE token = ?";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("s", $token);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Mark password reset token as used
     */
    public function markPasswordResetTokenAsUsed($token)
    {
        $query = "UPDATE password_reset_tokens SET is_used = TRUE WHERE token = ?";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("s", $token);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Clean up expired email verification tokens
     */
    private function cleanupEmailVerificationTokens($userId = null)
    {
        if ($userId) {
            $query = "DELETE FROM email_verification_tokens WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $userId);
        } else {
            $query = "DELETE FROM email_verification_tokens WHERE expires_at < NOW()";
            $stmt = $this->conn->prepare($query);
        }

        if ($stmt === false) {
            return false;
        }

        $stmt->execute();
        $stmt->close();

        return true;
    }

    /**
     * Clean up expired password reset tokens
     */
    private function cleanupPasswordResetTokens($userId = null)
    {
        if ($userId) {
            $query = "DELETE FROM password_reset_tokens WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $userId);
        } else {
            $query = "DELETE FROM password_reset_tokens WHERE expires_at < NOW()";
            $stmt = $this->conn->prepare($query);
        }

        if ($stmt === false) {
            return false;
        }

        $stmt->execute();
        $stmt->close();

        return true;
    }

    /**
     * Clean up all expired tokens (can be called periodically)
     */
    public function cleanupExpiredTokens()
    {
        $this->cleanupEmailVerificationTokens();
        $this->cleanupPasswordResetTokens();
    }
}
