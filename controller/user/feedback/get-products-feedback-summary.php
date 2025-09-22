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
    // Get products with feedback summary
    $query = "SELECT 
                p.product_id,
                p.product_name,
                p.description,
                pc.category_name,
                COALESCE(AVG(uf.rating), 0) as average_rating,
                COUNT(uf.feedback_id) as total_feedback,
                SUM(CASE WHEN uf.feedback_type = 'review' THEN 1 ELSE 0 END) as reviews,
                SUM(CASE WHEN uf.feedback_type = 'question' THEN 1 ELSE 0 END) as questions,
                SUM(CASE WHEN uf.feedback_type = 'complaint' THEN 1 ELSE 0 END) as complaints,
                SUM(CASE WHEN uf.feedback_type = 'suggestion' THEN 1 ELSE 0 END) as suggestions
              FROM products p
              LEFT JOIN product_categories pc ON p.product_category = pc.category_id
              LEFT JOIN user_feedback uf ON p.product_id = uf.product_id AND uf.status = 'approved'
              GROUP BY p.product_id, p.product_name, p.description, pc.category_name
              HAVING total_feedback > 0
              ORDER BY average_rating DESC, total_feedback DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'description' => $row['description'],
            'category_name' => $row['category_name'],
            'average_rating' => floatval($row['average_rating']),
            'total_feedback' => intval($row['total_feedback']),
            'reviews' => intval($row['reviews']),
            'questions' => intval($row['questions']),
            'complaints' => intval($row['complaints']),
            'suggestions' => intval($row['suggestions'])
        ];
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Products feedback summary retrieved successfully',
        'data' => $products,
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve products feedback summary: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
