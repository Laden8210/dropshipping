<?php


require_once '../../core/config.php';
require_once '../../models/index.php';
require_once '../../function/UIDGenerator.php';
require_once '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;



header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed. Use POST.',
        'data' => null
    ]);
    exit;
}

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Authorization token missing']);
    exit;
}
$jwt = $matches[1];

$secret_key = "dropshipping_8210";
try {
    $decoded = JWT::decode($jwt, new Key(trim($secret_key), 'HS256'));
    $user_id = $decoded->sub;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid token: ' . $e->getMessage()]);
    exit;
}

try {
    $request_body = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
        exit;
    }

    $product_id = $request_body['product_id'] ?? '';
    $rating = $request_body['rating'] ?? '';
    $review = $request_body['review'] ?? '';
    $order_id = $request_body['order_id'] ?? '';

    if (!isset($product_id) || $product_id === '') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Product ID is required']);
        exit;
    }
    if (!isset($rating) || $rating === '') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Rating is required']);
        exit;
    }
    if (!isset($order_id) || $order_id === '') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Order ID is required']);
        exit;
    }
    
    if (!isset($review) || $review === '') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Review is required']);
        exit;
    }
    $sql = "SELECT store_id FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $store_id = $row['store_id'];



    $rating = intval($rating);
    if ($rating < 1 || $rating > 5) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Rating must be between 1 and 5',
            'data' => null
        ]);
        exit;
    }

    // Validate product exists
    $product_check = $conn->prepare("SELECT product_id FROM products WHERE product_id = ?");
    $product_check->bind_param("i", $product_id);
    $product_check->execute();
    $product_result = $product_check->get_result();

    if ($product_result->num_rows === 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Product not found',
            'data' => null
        ]);
        exit;
    }

    // Generate unique feedback ID  20 characters long
    $feedback_id = UIDGenerator::generateUid();


    // Insert feedback
    $insert_query = "INSERT INTO user_feedback (
        feedback_id, 
        user_id, 
        store_id,
        product_id, 
        rating, 
        review,
        order_id,
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssiiisi", $feedback_id, $user_id, $store_id, $product_id, $rating, $review, $order_id);

    if ($stmt->execute()) {
        // Get product name for response
        $product_name_query = $conn->prepare("
            SELECT p.product_name, c.category_name 
            FROM products p 
            LEFT JOIN product_categories c ON p.product_category = c.category_id 
            WHERE p.product_id = ?
        ");
        $product_name_query->bind_param("i", $product_id);
        $product_name_query->execute();
        $product_info = $product_name_query->get_result()->fetch_assoc();

        echo json_encode([
            'status' => 'success',
            'message' => 'Feedback submitted successfully',
            'data' => [
                'feedback_id' => $feedback_id,
                'product_id' => $product_id,
                'store_id' => $store_id,
                'product_name' => $product_info['product_name'],
                'category_name' => $product_info['category_name'],
                'rating' => $rating,
                'review' => $review,
                'order_id' => $order_id,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        throw new Exception('Failed to insert feedback: ' . $stmt->error);
    }
} catch (Exception $e) {
    error_log("Mobile feedback submission error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
