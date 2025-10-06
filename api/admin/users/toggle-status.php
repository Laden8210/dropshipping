<?php
require_once '../../vendor/autoload.php';
require_once '../../core/config.php';
require_once '../../models/index.php';

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

if (!$request_body || !isset($request_body['user_id']) || !isset($request_body['is_active'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'User ID and status are required']);
    exit;
}

try {
    $user_id = $request_body['user_id'];
    $is_active = intval($request_body['is_active']);
    
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

    // Update user status
    $updateQuery = "UPDATE users SET is_active = ?, updated_at = NOW() WHERE user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("is", $is_active, $user_id);

    if ($stmt->execute()) {
        $status_text = $is_active ? 'activated' : 'deactivated';
        echo json_encode([
            'status' => 'success',
            'message' => "User $status_text successfully",
            'data' => ['is_active' => $is_active],
            'http_code' => 200
        ]);
    } else {
        throw new Exception('Failed to update user status');
    }

    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update user status: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
