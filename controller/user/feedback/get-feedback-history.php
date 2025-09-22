<?php
// Get User Feedback History API

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
    
    // Get user's feedback history
    $query = "
        SELECT 
            uf.feedback_id,
            uf.product_id,
            p.product_name,
            c.category_name,
            uf.rating,
            uf.message as review,
            uf.status,
            uf.created_at
        FROM user_feedback uf
        LEFT JOIN products p ON uf.product_id = p.product_id
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE uf.user_id = ?
        ORDER BY uf.created_at DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $feedback_history = [];
    while ($row = $result->fetch_assoc()) {
        $feedback_history[] = [
            'feedback_id' => $row['feedback_id'],
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'category_name' => $row['category_name'],
            'rating' => intval($row['rating']),
            'review' => $row['review'],
            'status' => $row['status'],
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Feedback history retrieved successfully',
        'data' => $feedback_history
    ]);
    
} catch (Exception $e) {
    error_log("Get feedback history error: " . $e->getMessage());
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