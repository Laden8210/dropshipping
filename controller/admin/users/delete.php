<?php
require_once '../../../vendor/autoload.php';
require_once '../../../core/config.php';
require_once '../../../models/index.php';

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

if (!$request_body || !isset($request_body['user_id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
    exit;
}

try {
    $user_id = $request_body['user_id'];
    
    // Check if user exists
    $checkQuery = "SELECT user_id FROM users WHERE user_id = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }
    $stmt->close();

    // Soft delete user
    $deleteQuery = "UPDATE users SET deleted_at = NOW() WHERE user_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("s", $user_id);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'User deleted successfully',
            'data' => null,
            'http_code' => 200
        ]);
    } else {
        throw new Exception('Failed to delete user');
    }

    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to delete user: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
