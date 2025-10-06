<?php
// Change user password
require_once '../../../vendor/autoload.php';
require_once '../../../core/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

session_start();

if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'user') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Forbidden: You do not have permission to access this resource.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

try {
    $userId = $_SESSION['auth']['user_id'];

    $request_body = json_decode(file_get_contents('php://input'), true);
    
    // Get form data
    $current_password = $request_body['current_password'] ?? '';
    $new_password = $request_body['new_password'] ?? '';
    $confirm_password = $request_body['confirm_password'] ?? '';
    
    // Validation
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'All password fields are required']);
        exit;
    }
    
    if ($new_password !== $confirm_password) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'New password and confirm password do not match']);
        exit;
    }
    
    if (strlen($new_password) < 6) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'New password must be at least 6 characters long']);
        exit;
    }
    
    // Get current user data
    $userQuery = "SELECT password FROM users WHERE user_id = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Verify current password
    if ($current_password !== $user['password']) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
        exit;
    }
    
    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update password
    $updateQuery = "UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ss", $hashed_password, $userId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Password changed successfully',
                'data' => null,
                'http_code' => 200
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
        }
    } else {
        throw new Exception("Failed to update password: " . $stmt->error);
    }
    
    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to change password: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
?>
