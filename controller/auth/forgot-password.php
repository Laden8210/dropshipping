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

if (!isset($request_body['email']) || empty(trim($request_body['email']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Email is required', 'http_code' => 400]);
    exit;
}

$email = trim($request_body['email']);

// Check if email exists
$user = $userModel->getUserByEmail($email);
if (!$user) {
    // For security, don't reveal if email exists or not
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'If the email exists, a password reset link has been sent.',
        'http_code' => 200
    ]);
    exit;
}

// Generate password reset token
$resetToken = $tokenService->createPasswordResetToken($user['user_id']);

if (!$resetToken) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error generating reset token', 'http_code' => 500]);
    exit;
}

// Send password reset email
$userName = $user['first_name'] . ' ' . $user['last_name'];
$emailSent = $emailService->sendPasswordResetEmail($email, $userName, $resetToken);

if (!$emailSent) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error sending reset email', 'http_code' => 500]);
    exit;
}

http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Password reset email sent successfully. Please check your email.',
    'http_code' => 200
]);
