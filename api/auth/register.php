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



if (!isset($request_body['first_name']) || empty(trim($request_body['first_name']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'First name is required', 'http_code' => 400]);
    exit;
}
if (!isset($request_body['last_name']) || empty(trim($request_body['last_name']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Last name is required', 'http_code' => 400]);
    exit;
}

if (!isset($request_body['email']) || empty(trim($request_body['email']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Email is required', 'http_code' => 400]);
    exit;
}

if (!isset($request_body['phone_number']) || empty(trim($request_body['phone_number']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Phone number is required', 'http_code' => 400]);
    exit;
}

if (!isset($request_body['password']) || empty(trim($request_body['password']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password is required', 'http_code' => 400]);
    exit;
}

if (!isset($request_body['confirm_password']) || empty(trim($request_body['confirm_password']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Confirm password is required', 'http_code' => 400]);
    exit;
}

if ($request_body['password'] !== $request_body['confirm_password']) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match', 'http_code' => 400]);
    exit;
}



if ($userModel->isEmailRegistered($request_body['email'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Email is already registered. Please log in instead.', 'http_code' => 400]);
    exit;
}

if ($userModel->isPhoneNumberRegistered($request_body['phone_number'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Phone number is already registered. Please log in instead.', 'http_code' => 400]);
    exit;
}


$user =  $userModel->register($request_body);


if ($user == false) {

    echo json_encode(['status' => 'error', 'message' => 'Error registering user', 'http_code' => 500]);
    exit;
}

echo json_encode([
    'status' => 'success',
    'message' => 'User registered successfully',

    'http_code' => 200
]);
