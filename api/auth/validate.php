<?php
require '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "your_jwt_secret_key";

// Set headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // For testing; restrict in production
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Get Authorization header
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['error' => 'Access token missing or malformed']);
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

    echo json_encode([
        'status' => 'success',
        'message' => 'Access granted',
        'user' => [
            'user_id' => $user_id,
            'role' => $role
        ]
    ]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Access denied',
        'error' => $e->getMessage()
    ]);
}
