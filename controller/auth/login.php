<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
ini_set('session.use_strict_mode', 1);

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Request method must be POST', 'http_code' => 405]);
    exit;
}

$request_body = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Request body is not valid JSON', 'http_code' => 400]);
    exit;
}

if (!isset($request_body['email']) || !isset($request_body['password'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required', 'http_code' => 400]);
    exit;
}

$email = trim($request_body['email']);
$password = $request_body['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format', 'http_code' => 400]);
    exit;
}

$user = $userModel->login($email);

if (!$user) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'User not found', 'http_code' => 404]);
    exit;
}

if ($user['password'] !==  $password) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid password', 'http_code' => 401]);
    exit;
}

$_SESSION['auth'] = [
    'user_id' => $user['user_id'],
    'role' => $user['role'],
    'ip_address' => $_SERVER['REMOTE_ADDR'],
    'user_agent' => $_SERVER['HTTP_USER_AGENT']
];

http_response_code(200);

echo json_encode([
    'status' => 'success',
    'message' => 'Login successful',
    'http_code' => 200
]);
