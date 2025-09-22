<?php
require_once '../../../core/config.php';
require_once '../../../core/request.php';

header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'user') {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Forbidden: You do not have permission to access this resource.'
    ]);
    exit;
}

try {
    $store_id = $_SESSION['auth']['store_id'];

    $query = "
        SELECT 
            uf.feedback_id,
            uf.product_id,
            uf.store_id,
            p.product_name,
            c.category_name,
            uf.rating,
            uf.review,
            uf.user_id,
            uf.created_at
        FROM user_feedback uf
        LEFT JOIN products p ON uf.product_id = p.product_id
        LEFT JOIN product_categories c ON p.product_category = c.category_id
        
        WHERE uf.store_id = ?
        ORDER BY uf.created_at DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $store_id);
    $stmt->execute();

    $result = $stmt->get_result();  // ✅ only once

    $feedback_data = [];
    while ($row = $result->fetch_assoc()) {
        $feedback_data[] = [
            'feedback_id'   => $row['feedback_id'],
            'product_id'    => $row['product_id'],
            'store_id'      => $row['store_id'],
            'product_name'  => $row['product_name'],
            'category_name' => $row['category_name'],
            'rating'        => (int) $row['rating'],
            'review'        => $row['review'],
            'user_id'       => $row['user_id'],
            'created_at'    => $row['created_at']
        ];
    }

    echo json_encode([
        'status'    => 'success',
        'message'   => 'Feedback data retrieved successfully',
        'data'      => $feedback_data,
        'http_code' => 200
    ]);

} catch (Exception $e) {
    error_log("Get all feedback error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status'    => 'error',
        'message'   => 'Internal server error: ' . $e->getMessage(),
        'data'      => null,
        'http_code' => 500
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>
