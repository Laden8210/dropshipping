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

if (!isset($request_body['token']) || empty(trim($request_body['token']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Reset token is required', 'http_code' => 400]);
    exit;
}

if (!isset($request_body['password']) || empty(trim($request_body['password']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'New password is required', 'http_code' => 400]);
    exit;
}

if (!isset($request_body['confirm_password']) || empty(trim($request_body['confirm_password']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password confirmation is required', 'http_code' => 400]);
    exit;
}

if ($request_body['password'] !== $request_body['confirm_password']) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match', 'http_code' => 400]);
    exit;
}

// Validate password strength
$password = $request_body['password'];
if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters long', 'http_code' => 400]);
    exit;
}

$token = trim($request_body['token']);

// Validate reset token
$userId = $tokenService->validatePasswordResetToken($token);
if (!$userId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired reset token', 'http_code' => 400]);
    exit;
}

// Update password
$success = $userModel->updatePassword($userId, $password);

if (!$success) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error updating password', 'http_code' => 500]);
    exit;
}

// Mark token as used
$tokenService->markPasswordResetTokenAsUsed($token);

http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Password updated successfully. You can now log in with your new password.',
    'http_code' => 200
]);
