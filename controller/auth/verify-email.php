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
    echo json_encode(['status' => 'error', 'message' => 'Verification token is required', 'http_code' => 400]);
    exit;
}

$token = trim($request_body['token']);

// Validate verification token
$userId = $tokenService->validateEmailVerificationToken($token);
if (!$userId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired verification token', 'http_code' => 400]);
    exit;
}

// Verify user email
$success = $userModel->verifyEmail($userId);

if (!$success) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error verifying email', 'http_code' => 500]);
    exit;
}

http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Email verified successfully. You can now log in to your account.',
    'http_code' => 200
]);
