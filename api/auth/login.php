<?php
require '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Your secret key (keep it safe and secret)
$secret_key = "your_secret_key_here";

// Fake user database for demo
$users = [
    'user1' => 'password123',
    'user2' => 'mypassword'
];

// Simulated login input
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Username and password required']);
    exit;
}

// Check if user exists and password matches (use hashing in real apps!)
if (!isset($users[$username]) || $users[$username] !== $password) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

// User is authenticated, create JWT payload
$issuedAt   = time();
$expiration = $issuedAt + 3600; // 1 hour token validity

$payload = [
    'iat' => $issuedAt,
    'exp' => $expiration,
    'iss' => 'yourdomain.com',
    'aud' => 'yourdomain.com',
    'data' => [
        'username' => $username,
    ]
];

// Encode the token
$jwt = JWT::encode($payload, $secret_key, 'HS256');

// Return the token
header('Content-Type: application/json');
echo json_encode([
    'message' => 'Login successful',
    'token' => $jwt
]);
