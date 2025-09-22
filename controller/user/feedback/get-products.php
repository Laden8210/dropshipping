<?php
// Get Products for Feedback API

require_once '../../../core/config.php';
require_once '../../../core/request.php';

// Set JSON header
header('Content-Type: application/json');

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
    
    // Get products that the user can provide feedback for
    // This could be products they've ordered, or all products in the system
    $query = "
        SELECT DISTINCT p.product_id, p.product_name, p.description, c.category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.status = 'active'
        ORDER BY p.product_name ASC
    ";
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Database query failed: ' . $conn->error);
    }
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'description' => $row['description'],
            'category_name' => $row['category_name']
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Products retrieved successfully',
        'data' => $products
    ]);
    
} catch (Exception $e) {
    error_log("Get products error: " . $e->getMessage());
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