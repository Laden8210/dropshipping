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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Request method must be POST',
        'data' => null,
        'http_code' => 405
    ]);
    exit;
}

// Get user info
$userId = $_SESSION['auth']['user_id'] ?? null;

if (!$userId) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access',
        'data' => null,
        'http_code' => 403
    ]);
    exit;
}

try {
    // Get form data
    $productId = $_POST['product_id'] ?? null;
    $feedbackType = $_POST['feedback_type'] ?? '';
    $rating = $_POST['rating'] ?? null;
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $anonymous = isset($_POST['anonymous']) && $_POST['anonymous'] === 'on';

    // Validate required fields
    if (empty($productId) || empty($feedbackType) || empty($subject) || empty($message)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please fill in all required fields',
            'data' => null,
            'http_code' => 400
        ]);
        exit;
    }

    // Validate rating for rating type feedback
    if ($feedbackType === 'rating' && (empty($rating) || $rating < 1 || $rating > 5)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please provide a valid rating (1-5 stars)',
            'data' => null,
            'http_code' => 400
        ]);
        exit;
    }

    // Generate feedback ID
    $feedbackId = 'FB' . date('Ymd') . strtoupper(substr(uniqid(), -6));

    // Insert feedback into database
    $insertQuery = "INSERT INTO user_feedback (
        feedback_id, user_id, product_id, feedback_type, rating, subject, message, 
        is_anonymous, status, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("siissssi", 
        $feedbackId, 
        $userId, 
        $productId, 
        $feedbackType, 
        $rating, 
        $subject, 
        $message, 
        $anonymous ? 1 : 0
    );

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Product feedback submitted successfully! It will be reviewed before being published.',
            'data' => [
                'feedback_id' => $feedbackId,
                'submitted_at' => date('Y-m-d H:i:s')
            ],
            'http_code' => 200
        ]);
    } else {
        throw new Exception('Failed to insert feedback into database');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to submit product feedback: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
