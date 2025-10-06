<?php
require_once '../../../vendor/autoload.php';
require_once '../../../core/config.php';
require_once '../../../models/index.php';
require_once '../../../function/UIDGenerator.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

session_start();

// Check if user is admin
if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Forbidden: Admin access required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$request_body = json_decode(file_get_contents('php://input'), true);

if (!$request_body) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    exit;
}

// Validate required fields
$required_fields = ['first_name', 'last_name', 'email', 'role', 'password'];
foreach ($required_fields as $field) {
    if (!isset($request_body[$field]) || empty(trim($request_body[$field]))) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
        exit;
    }
}

// Validate password confirmation
if (isset($request_body['confirm_password']) && $request_body['password'] !== $request_body['confirm_password']) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
    exit;
}

// Validate role
$allowed_roles = ['user', 'supplier', 'courier', 'admin'];
if (!in_array($request_body['role'], $allowed_roles)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid role']);
    exit;
}

try {
    // Check if email already exists
    $emailCheckQuery = "SELECT user_id FROM users WHERE email = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($emailCheckQuery);
    $stmt->bind_param("s", $request_body['email']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
        exit;
    }
    $stmt->close();

    // Generate user ID
    $user_id = generateUID();

    // Hash password
    $hashed_password = password_hash($request_body['password'], PASSWORD_DEFAULT);

    // Insert user
    $insertQuery = "INSERT INTO users (
        user_id, role, first_name, last_name, email, phone_number, 
        birth_date, gender, password, is_active, created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sssssssssi", 
        $user_id,
        $request_body['role'],
        $request_body['first_name'],
        $request_body['last_name'],
        $request_body['email'],
        $request_body['phone_number'] ?? null,
        $request_body['birth_date'] ?? null,
        $request_body['gender'] ?? 'male',
        $hashed_password,
        $request_body['is_active'] ?? 1
    );

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => ['user_id' => $user_id],
            'http_code' => 201
        ]);
    } else {
        throw new Exception('Failed to create user');
    }

    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to create user: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
