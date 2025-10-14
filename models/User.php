<?php
class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function sanitizeEmail($email)
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    public function login($email)
    {
        $email = $this->sanitizeEmail($email);


        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return ['status' => 'error', 'message' => 'Database query preparation failed'];
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null;
        }
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function loginWithGoogle($google_id)
    {
        $query = "SELECT * FROM users WHERE google_id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return ['status' => 'error', 'message' => 'Database query preparation failed'];
        }
        $stmt->bind_param("s", $google_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null;
        }
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function register($userData)
    {
        $user_id = UIDGenerator::generateUID();
        $role = $userData['role'] ?? 'user';
        $first_name = isset($userData['first_name']) ? trim($userData['first_name']) : '';
        $last_name = isset($userData['last_name']) ? trim($userData['last_name']) : '';
        $email = $this->sanitizeEmail($userData['email']);
        $phone_number = isset($userData['phone_number']) ? trim($userData['phone_number']) : '';
        $avatar_url = isset($userData['avatar_url']) ? trim($userData['avatar_url']) : '';
        $password = $userData['password'];
        $is_google_auth = false;
        $is_email_verified = false;

        $query = "INSERT INTO users (user_id, role, first_name, last_name, email, phone_number, avatar_url, password, is_google_auth, is_email_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return ['status' => 'error', 'message' => 'Database query preparation failed'];
        }
        $stmt->bind_param(
            "ssssssssii",
            $user_id,
            $role,
            $first_name,
            $last_name,
            $email,
            $phone_number,
            $avatar_url,
            $password,
            $is_google_auth,
            $is_email_verified
        );
        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }
        $stmt->close();
        return true;
    }

    public function google_register($userInfo)
    {
        $user_id = UIDGenerator::generateUID();
        $role = 'user';
        $first_name = isset($userInfo->givenName) ? trim($userInfo->givenName) : '';
        $last_name = isset($userInfo->familyName) ? trim($userInfo->familyName) : '';
        $email = isset($userInfo->email) ? $this->sanitizeEmail($userInfo->email) : '';
        $phone_number = '';
        $avatar_url = isset($userInfo->picture) ? trim($userInfo->picture) : '';
        $is_google_auth = true;
        $is_email_verified = true;

        $google_id = isset($userInfo->id) ? trim($userInfo->id) : '';

        $query = "INSERT INTO users (user_id, role, first_name, last_name, email, phone_number, avatar_url, is_google_auth, is_email_verified, google_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return ['status' => 'error', 'message' => 'Database query preparation failed'];
        }
        $stmt->bind_param(
            "sssssssiis",
            $user_id,
            $role,
            $first_name,
            $last_name,
            $email,
            $phone_number,
            $avatar_url,
            $is_google_auth,
            $is_email_verified,
            $google_id
        );

        if (!$stmt->execute()) {
            $stmt->close();
            return ['status' => 'error', 'message' => 'Error inserting user data: ' . $stmt->error];
        }
        $stmt->close();
        return ['status' => 'success', 'message' => 'User registered successfully', 'user_id' => $user_id];
    }


    public function getCurrentUser()
    {
        if (!isset($_SESSION['auth']) || !isset($_SESSION['auth']['user_id'])) {
            return null;
        }
        $user_id = $_SESSION['auth']['user_id'];
        $query = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return null;
        }
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null;
        }
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    // helper 

    public function isEmailRegistered($email)
    {
        $email = $this->sanitizeEmail($email);
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $isRegistered = $result->num_rows > 0;
        $stmt->close();
        return $isRegistered;
    }

    public function isPhoneNumberRegistered($phone_number)
    {
        $phone_number = trim($phone_number);
        $query = "SELECT * FROM users WHERE phone_number = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }
        $stmt->bind_param("s", $phone_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $isRegistered = $result->num_rows > 0;
        $stmt->close();
        return $isRegistered;
    }

    public function updateProfile($user_id, $data)
    {
        try {
            // Start building the SQL query
            $sql = "UPDATE users SET ";
            $params = [];
            $types = "";

            $updates = [];

            // Add each field to update
            if (isset($data['first_name'])) {
                $updates[] = "first_name = ?";
                $params[] = $data['first_name'];
                $types .= "s";
            }

            if (isset($data['last_name'])) {
                $updates[] = "last_name = ?";
                $params[] = $data['last_name'];
                $types .= "s";
            }

            if (isset($data['email'])) {
                $updates[] = "email = ?";
                $params[] = $data['email'];
                $types .= "s";
            }

            if (isset($data['phone_number'])) {
                $updates[] = "phone_number = ?";
                $params[] = $data['phone_number'];
                $types .= "s";
            }

            if (isset($data['gender'])) {
                $updates[] = "gender = ?";
                $params[] = $data['gender'];
                $types .= "s";
            }

            if (isset($data['birth_date'])) {
                $updates[] = "birth_date = ?";
                $params[] = $data['birth_date'];
                $types .= "s";
            }

            if (isset($data['password'])) {
                $updates[] = "password = ?";
                $params[] = $data['password'];
                $types .= "s";
            }

            // Add updated_at timestamp
            $updates[] = "updated_at = CURRENT_TIMESTAMP";

            // Complete the SQL query
            $sql .= implode(", ", $updates) . " WHERE user_id = ?";
            $params[] = $user_id;
            $types .= "s";

            // Prepare and execute the statement
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            error_log("Update profile error: " . $e->getMessage());
            return false;
        }
    }




    public function isGoogleIdRegistered($google_id)
    {
        $query = "SELECT * FROM users WHERE google_id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }
        $stmt->bind_param("s", $google_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $isRegistered = $result->num_rows > 0;
        $stmt->close();
        return $isRegistered;
    }

    /**
     * Verify user email
     */
    public function verifyEmail($userId)
    {
        $query = "UPDATE users SET is_email_verified = TRUE WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("s", $userId);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Check if user email is verified
     */
    public function isEmailVerified($email)
    {
        $email = $this->sanitizeEmail($email);
        $query = "SELECT is_email_verified FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return false;
        }

        $row = $result->fetch_assoc();
        $stmt->close();

        return (bool) $row['is_email_verified'];
    }

    /**
     * Update user password
     */
    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $query = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("ss", $hashedPassword, $userId);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email)
    {
        $email = $this->sanitizeEmail($email);
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return null;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return null;
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        return $user;
    }
}
