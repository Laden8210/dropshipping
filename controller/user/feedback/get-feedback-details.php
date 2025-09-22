<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
ini_set('session.use_strict_mode', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

session_start();

if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'user') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Forbidden: You do not have permission to access this resource.']);
    exit;
}

require_once '../../../core/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Request method must be GET',
        'data' => null,
        'http_code' => 405
    ]);
    exit;
}

// Get user info
$userId = $_SESSION['auth']['user_id'] ?? null;
$feedbackId = $_GET['feedback_id'] ?? null;

if (!$userId || !$feedbackId) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access or missing feedback ID',
        'data' => null,
        'http_code' => 403
    ]);
    exit;
}

try {
    // Get feedback details
    $query = "SELECT feedback_id, feedback_type, priority, subject, message, 
                     contact_email, is_anonymous, status, admin_response, 
                     created_at, updated_at
              FROM user_feedback 
              WHERE feedback_id = ? AND user_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $feedbackId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $feedback = $result->fetch_assoc();
    
    if (!$feedback) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Feedback not found',
            'data' => null,
            'http_code' => 404
        ]);
        exit;
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Feedback details retrieved successfully',
        'data' => $feedback,
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve feedback details: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
