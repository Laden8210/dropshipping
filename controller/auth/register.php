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

// format phone number to philippine standard
$phone = preg_replace('/[^0-9]/', '', $request_body['phone_number']);
if (strlen($phone) == 10 && preg_match('/^9[0-9]{9}$/', $phone)) {
    $phone = '0' . $phone;
} elseif (strlen($phone) == 11 && preg_match('/^09[0-9]{9}$/', $phone)) {
    // already in correct format
} elseif (strlen($phone) == 12 && preg_match('/^639[0-9]{9}$/', $phone)) {
    $phone = '0' . substr($phone, 2);
} elseif (strlen($phone) == 13 && preg_match('/^\+639[0-9]{9}$/', $phone)) {
    $phone = '0' . substr($phone, 3);
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Phone number is not valid. Must be a valid Philippine phone number.', 'http_code' => 400]);
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

if (!isset($request_body['role']) || !in_array($request_body['role'], ['user', 'supplier', 'courier'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid user type. Must be "user" or "supplier".', 'http_code' => 400]);
    exit;
}


// Hash the password before storing
$request_body['password'] = password_hash($request_body['password'], PASSWORD_DEFAULT);

$user = $userModel->register($request_body);

if ($user == false) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error registering user', 'http_code' => 500]);
    exit;
}

// Get the user ID for the newly registered user
$registeredUser = $userModel->getUserByEmail($request_body['email']);
if (!$registeredUser) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error retrieving user data', 'http_code' => 500]);
    exit;
}

// Generate email verification token
$verificationToken = $tokenService->createEmailVerificationToken($registeredUser['user_id']);

if (!$verificationToken) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error generating verification token', 'http_code' => 500]);
    exit;
}

// Send verification email
$userName = $request_body['first_name'] . ' ' . $request_body['last_name'];
$emailSent = $emailService->sendEmailVerification(
    $request_body['email'], 
    $userName, 
    $verificationToken
);

if (!$emailSent) {
    // Log the error but don't fail registration
    error_log("Failed to send verification email to: " . $request_body['email']);
}

// Save to CSV file (keeping existing functionality)
$csvFile = __DIR__ . '/../../data/users.csv';
if (!file_exists(dirname($csvFile))) {
    mkdir(dirname($csvFile), 0777, true);
}
if (!file_exists($csvFile)) {
    touch($csvFile);
}
$csvData = [
    $request_body['first_name'],
    $request_body['last_name'],
    $request_body['email'],
    $request_body['phone_number'],
    $request_body['password'], // Already hashed
    $request_body['role'],
    date('Y-m-d H:i:s')
];

$fileExists = file_exists($csvFile);
$fp = fopen($csvFile, 'a');
if (!$fileExists) {
    fputcsv($fp, ['first_name', 'last_name', 'email', 'phone_number', 'password_hash', 'role', 'created_at']);
}
fputcsv($fp, $csvData);
fclose($fp);

http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'User registered successfully. Please check your email to verify your account.',
    'email_sent' => $emailSent,
    'http_code' => 200
]);
