<?php
require '../../vendor/autoload.php';
require_once '../../core/config.php';
require_once '../../models/index.php';
require_once '../../function/UIDGenerator.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "dropshipping_8210";

// Set headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get Authorization header
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Access token missing or malformed']);
    exit;
}

$jwt = $matches[1];

try {
    $decoded = JWT::decode($jwt, new Key(trim($secret_key), 'HS256'));

    // Use whatever fields you encoded into the JWT payload
    $user_id = $decoded->sub ?? null;
    $role = $decoded->role ?? null;

    if (!$user_id) {
        throw new Exception("Invalid token payload");
    }

    // Get and validate request body
    $request_body = json_decode(file_get_contents('php://input'), true);
    
    if (!$request_body) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
        exit;
    }

    // Validate required fields
    $required_fields = ['first_name', 'last_name', 'email'];
    foreach ($required_fields as $field) {
        if (!isset($request_body[$field]) || empty(trim($request_body[$field]))) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => "Field '$field' is required"]);
            exit;
        }
    }

    // Validate email format
    if (!filter_var($request_body['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit;
    }

    // Check if email already exists for other users
    $email_check_sql = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
    $stmt = $conn->prepare($email_check_sql);
    $stmt->bind_param("ss", $request_body['email'], $user_id);
    $stmt->execute();
    $email_result = $stmt->get_result();
    
    if ($email_result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
        exit;
    }

    // Handle password change if provided
    if (isset($request_body['current_password']) && !empty($request_body['current_password'])) {
        // Verify current password
        $password_check_sql = "SELECT password FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($password_check_sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $password_result = $stmt->get_result();
        $user_data = $password_result->fetch_assoc();
        
        if (!$user_data || !password_verify($request_body['current_password'], $user_data['password'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
            exit;
        }

        // Validate new password
        if (!isset($request_body['new_password']) || empty($request_body['new_password'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'New password is required']);
            exit;
        }

        if (strlen($request_body['new_password']) < 6) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'New password must be at least 6 characters long']);
            exit;
        }

        if ($request_body['new_password'] !== $request_body['confirm_password']) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'New password and confirmation do not match']);
            exit;
        }

        // Hash new password
        $hashed_password = password_hash($request_body['new_password'], PASSWORD_DEFAULT);
    }

    // Prepare update data
    $update_data = [
        'first_name' => trim($request_body['first_name']),
        'last_name' => trim($request_body['last_name']),
        'email' => trim($request_body['email']),
        'phone_number' => isset($request_body['phone_number']) ? trim($request_body['phone_number']) : null,
        'gender' => isset($request_body['gender']) ? trim($request_body['gender']) : null,
        'birth_date' => isset($request_body['birth_date']) ? trim($request_body['birth_date']) : null
    ];

    // Add password to update if changing
    if (isset($hashed_password)) {
        $update_data['password'] = $hashed_password;
    }

    // Update profile using UserModel
    if ($userModel->updateProfile($user_id, $update_data)) {
        // Get updated user data
        $user_sql = "SELECT user_id, first_name, last_name, email, phone_number, birth_date, gender, avatar_url, is_google_auth FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($user_sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $user_result = $stmt->get_result();
        $updated_user = $user_result->fetch_assoc();

        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => $updated_user
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update profile'
        ]);
    }

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Access denied',
        'error' => $e->getMessage()
    ]);
}
?>