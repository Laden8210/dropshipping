<?php
header('Content-Type: application/json');
require_once '../../../core/config.php';
require_once '../../../services/NotificationService.php';

// Check user authentication
session_start();
if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'user') {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Forbidden: User access required.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method Not Allowed'
    ]);
    exit;
}

try {
    $order_id = $_POST['order_id'] ?? '';
    $new_status = $_POST['status'] ?? '';
    
    if (empty($order_id) || empty($new_status)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Order ID and status are required.'
        ]);
        exit;
    }

    // Get order and customer details
    $query = "
        SELECT 
            o.order_id,
            o.order_number,
            o.status,
            o.store_id,
            u.first_name,
            u.last_name,
            u.email,
            u.phone,
            sp.store_name
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        JOIN store_profile sp ON o.store_id = sp.store_id
        WHERE o.order_id = ? AND o.store_id = ?
    ";
    
    $stmt = $conn->prepare($query);
    $user_store_id = $_SESSION['auth']['store_id'];
    $stmt->bind_param("ii", $order_id, $user_store_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Order not found or access denied.'
        ]);
        exit;
    }
    
    $order = $result->fetch_assoc();
    $stmt->close();

    // Initialize notification service
    $notificationService = new NotificationService();
    
    // Send notifications
    $results = $notificationService->sendOrderStatusNotification(
        $order_id,
        $new_status,
        $order['phone'],
        $order['email'],
        $order['first_name'] . ' ' . $order['last_name']
    );

    // Log notification attempt
    error_log("Order status notification sent for order {$order_id}: " . json_encode($results));

    echo json_encode([
        'status' => 'success',
        'message' => 'Notification sent successfully',
        'data' => [
            'order_id' => $order_id,
            'order_number' => $order['order_number'],
            'customer_name' => $order['first_name'] . ' ' . $order['last_name'],
            'new_status' => $new_status,
            'notifications' => $results
        ]
    ]);

} catch (Exception $e) {
    error_log("Order notification error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>
