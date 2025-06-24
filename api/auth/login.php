<?php
require '../../vendor/autoload.php';
require_once '../../core/config.php';
require_once '../../models/index.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$JWT_SECRET_KEY = "your_jwt_secret_key"; 
if (!$JWT_SECRET_KEY) {

    echo json_encode(['status' => 'error', 'message' => 'JWT secret key not set', 'http_code' => 500]);
    exit;
}

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Force HTTPS cookies
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed', 'http_code' => 405]);
    exit;
}

$request_body = file_get_contents('php://input');
if (empty($request_body)) {

    echo json_encode(['status' => 'error', 'message' => 'Missing request body', 'http_code' => 400]);
    exit;
}

$data = json_decode($request_body, true);
if (json_last_error() !== JSON_ERROR_NONE) {
   
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON', 'http_code' => 400]);
    exit;
}

if (!isset($data['email']) || !isset($data['password'])) {

    echo json_encode(['status' => 'error', 'message' => 'Email and password required', 'http_code' => 400]);
    exit;
}

$email = trim($data['email']);
$password = $data['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
 
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format', 'http_code' => 400]);
    exit;
}



$user = $userModel->login($email);
if (!$user) {

    echo json_encode(['status' => 'error', 'message' => 'Invalid credentials', 'http_code' => 401]);
    exit;
}



// Verify password (assuming passwords are stored hashed)
if ($password !== $user['password']) {

    echo json_encode(['status' => 'error', 'message' => 'Invalid credentials', 'http_code' => 401]);
    exit;
}

if ($user['role'] !== 'client') {

    echo json_encode(['status' => 'error', 'message' => 'Account is inactive', 'http_code' => 403]);
    exit;
}


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
    echo json_encode(['status' => 'error', 'message' => 'Token generation failed', 'http_code' => 500]);
    exit;
}

http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Login successful',
    'token' => $jwt,
    'expires' => $expiration,
    'user' => [
        'user_id' => $user['user_id'],
        'role' => $user['role']
    ]
]);