<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../core/config.php';
require_once '../../services/NotificationService.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method Not Allowed. Use POST.',
        'data' => null
    ]);
    exit;
}

try {
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $order_id = $input['order_id'] ?? '';
    $new_status = $input['status'] ?? '';
    $customer_phone = $input['customer_phone'] ?? '';
    $customer_email = $input['customer_email'] ?? '';
    $customer_name = $input['customer_name'] ?? '';
    $order_number = $input['order_number'] ?? '';

    if (empty($order_id) || empty($new_status)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Order ID and status are required.',
            'data' => null
        ]);
        exit;
    }

    // If customer details not provided, fetch from database
    if (empty($customer_phone) || empty($customer_email)) {
        $query = "
            SELECT 
                o.user_id,
                u.first_name,
                u.last_name,
                u.email,
                u.phone
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            WHERE o.order_id = ?
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Order not found.',
                'data' => null
            ]);
            exit;
        }
        
        $customer = $result->fetch_assoc();
        $stmt->close();
        
        $customer_phone = $customer_phone ?: $customer['phone'];
        $customer_email = $customer_email ?: $customer['email'];
        $customer_name = $customer_name ?: ($customer['first_name'] . ' ' . $customer['last_name']);
    }

    // Initialize notification service
    $notificationService = new NotificationService();
    
    // Send notifications
    $results = $notificationService->sendOrderStatusNotification(
        $order_id,
        $new_status,
        $customer_phone,
        $customer_email,
        $customer_name
    );

    // Log notification attempt
    error_log("Order status notification sent for order {$order_id}: " . json_encode($results));

    echo json_encode([
        'status' => 'success',
        'message' => 'Notifications sent successfully',
        'data' => [
            'order_id' => $order_id,
            'order_number' => $order_number ?: "ORDER-" . str_pad($order_id, 6, '0', STR_PAD_LEFT),
            'customer_name' => $customer_name,
            'new_status' => $new_status,
            'sms_sent' => $customer_phone ? $results['sms']['success'] : false,
            'email_sent' => $customer_email ? $results['email']['success'] : false,
            'notifications' => $results
        ]
    ]);

} catch (Exception $e) {
    error_log("Order notification API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>
