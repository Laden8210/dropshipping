<?php
require '../../vendor/autoload.php';
require_once '../../core/config.php';
require_once '../../models/index.php';
require_once '../../function/UIDGenerator.php';
require_once '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "dropshipping_8210";

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

    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // remove password from user
    unset($user['password']);


    echo json_encode([
        'status' => 'success',
        'message' => 'Access granted',
        'data' => [
            'user' => $user
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
