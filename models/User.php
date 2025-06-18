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


    public function getCurrentUser(){
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

}
