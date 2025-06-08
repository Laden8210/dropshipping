<?php
require '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "your_secret_key_here";

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['error' => 'Access token missing']);
    exit;
}

$jwt = $matches[1];

try {
    $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
    $user = $decoded->data->username;
    echo json_encode(['message' => 'Access granted', 'user' => $user]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Access denied', 'message' => $e->getMessage()]);
}
