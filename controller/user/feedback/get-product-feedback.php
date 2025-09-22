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
$productId = $_GET['product_id'] ?? null;

if (!$userId || !$productId) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access or missing product ID',
        'data' => null,
        'http_code' => 403
    ]);
    exit;
}

try {
    // Get feedback for the specific product (only approved feedback)
    $feedbackQuery = "SELECT feedback_id, feedback_type, rating, subject, message, 
                             is_anonymous, status, admin_response, created_at
                      FROM user_feedback 
                      WHERE product_id = ? AND status = 'approved'
                      ORDER BY created_at DESC";

    $stmt = $conn->prepare($feedbackQuery);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $feedback = [];
    while ($row = $result->fetch_assoc()) {
        $feedback[] = $row;
    }

    // Get feedback statistics
    $statsQuery = "SELECT 
                        COUNT(*) as total_feedback,
                        AVG(rating) as average_rating,
                        SUM(CASE WHEN feedback_type = 'review' THEN 1 ELSE 0 END) as reviews,
                        SUM(CASE WHEN feedback_type = 'question' THEN 1 ELSE 0 END) as questions,
                        SUM(CASE WHEN feedback_type = 'complaint' THEN 1 ELSE 0 END) as complaints,
                        SUM(CASE WHEN feedback_type = 'suggestion' THEN 1 ELSE 0 END) as suggestions
                   FROM user_feedback 
                   WHERE product_id = ? AND status = 'approved'";

    $stmt = $conn->prepare($statsQuery);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $statsResult = $stmt->get_result();
    $stats = $statsResult->fetch_assoc();

    // Get rating distribution
    $ratingQuery = "SELECT rating, COUNT(*) as count
                    FROM user_feedback 
                    WHERE product_id = ? AND status = 'approved' AND rating IS NOT NULL
                    GROUP BY rating";

    $stmt = $conn->prepare($ratingQuery);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $ratingResult = $stmt->get_result();
    
    $ratingDistribution = ['total' => 0];
    while ($row = $ratingResult->fetch_assoc()) {
        $ratingDistribution[$row['rating']] = $row['count'];
        $ratingDistribution['total'] += $row['count'];
    }

    $stats['rating_distribution'] = $ratingDistribution;
    $stats['average_rating'] = $stats['average_rating'] ? round($stats['average_rating'], 1) : null;

    echo json_encode([
        'status' => 'success',
        'message' => 'Product feedback retrieved successfully',
        'data' => [
            'feedback' => $feedback,
            'stats' => $stats
        ],
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve product feedback: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
