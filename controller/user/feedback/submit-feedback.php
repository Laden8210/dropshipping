<?php
// Submit Product Feedback API
// Based on mobile code sample: product_id, rating, review

require_once '../../../core/config.php';
require_once '../../../core/request.php';

// Set JSON header
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed. Use POST.',
        'data' => null
    ]);
    exit;
}

try {
    // Get user ID from session (assuming user is logged in)
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'message' => 'User not authenticated',
            'data' => null
        ]);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Get POST data
    $product_id = $_POST['product_id'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $review = $_POST['review'] ?? '';
    
    // Validate required fields
    if (empty($product_id) || empty($rating) || empty($review)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required fields: product_id, rating, review',
            'data' => null
        ]);
        exit;
    }
    
    // Validate rating (1-5)
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
    
    // Check if user already submitted feedback for this product
    $existing_check = $conn->prepare("SELECT feedback_id FROM user_feedback WHERE user_id = ? AND product_id = ?");
    $existing_check->bind_param("si", $user_id, $product_id);
    $existing_check->execute();
    $existing_result = $existing_check->get_result();
    
    if ($existing_result->num_rows > 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'You have already submitted feedback for this product',
            'data' => null
        ]);
        exit;
    }
    
    // Generate unique feedback ID
    require_once '../../../function/UIDGenerator.php';
    $feedback_id = 'FB-' . date('Ymd-His') . '-' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
    
    // Insert feedback
    $insert_query = "INSERT INTO user_feedback (
        feedback_id, 
        user_id, 
        product_id, 
        feedback_type, 
        rating, 
        subject, 
        message, 
        is_anonymous, 
        status, 
        created_at
    ) VALUES (?, ?, ?, 'review', ?, 'Product Review', ?, FALSE, 'pending', NOW())";
    
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssiss", $feedback_id, $user_id, $product_id, $rating, $review);
    
    if ($stmt->execute()) {
        // Get product name for response
        $product_name_query = $conn->prepare("
            SELECT p.product_name, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
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
                'product_name' => $product_info['product_name'],
                'category_name' => $product_info['category_name'],
                'rating' => $rating,
                'review' => $review,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        throw new Exception('Failed to insert feedback: ' . $stmt->error);
    }
    
} catch (Exception $e) {
    error_log("Feedback submission error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error: ' . $e->getMessage(),
        'data' => null
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>