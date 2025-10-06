<?php
require_once '../../vendor/autoload.php';
require_once '../../core/config.php';
require_once '../../models/index.php';

use Firebase\JWT\JWT;

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$request_body = json_decode(file_get_contents('php://input'), true);

if (!$request_body) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    exit;
}

if (!isset($request_body['email']) || !isset($request_body['password'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
    exit;
}

$email = trim($request_body['email']);
$password = trim($request_body['password']);

try {
    // Check if user exists and is admin
    $sql = "SELECT * FROM users WHERE email = ? AND role = 'admin' AND deleted_at IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Invalid credentials or not an admin']);
        exit;
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();

    // Verify password
    if (!password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
        exit;
    }

    // Generate JWT token
    $JWT_SECRET_KEY = "dropshipping_8210";
    $issuedAt = time();
    $expiration = null; // Token never expires

    $payload = [
        'iat' => $issuedAt,
        'exp' => $expiration,
        'iss' => 'localhost',
        'aud' => 'localhost',
        'sub' => $user['user_id'], 
        'role' => $user['role'],
        'jti' => bin2hex(random_bytes(16)) 
    ];

    try {
        $jwt = JWT::encode($payload, $JWT_SECRET_KEY, 'HS256');
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Token generation failed']);
        exit;
    }

    // Set session
    session_start();
    $_SESSION['auth'] = [
        'user_id' => $user['user_id'],
        'role' => $user['role'],
        'email' => $user['email'],
        'name' => $user['first_name'] . ' ' . $user['last_name'],
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT']
    ];

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Admin login successful',
        'token' => $jwt,
        'expires' => $expiration,
        'user' => [
            'user_id' => $user['user_id'],
            'role' => $user['role'],
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'email' => $user['email']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Login failed: ' . $e->getMessage()
    ]);
}
