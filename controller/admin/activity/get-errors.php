<?php
require_once '../../../vendor/autoload.php';
require_once '../../../core/config.php';
require_once '../../../models/index.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

session_start();

// Check if user is admin
if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Forbidden: Admin access required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get error logs from existing database tables
    $errors = [];
    
    // Get failed payments as errors
    $failedPaymentsQuery = "SELECT 
                                op.payment_id as id,
                                op.created_at as timestamp,
                                'PaymentError' as error_type,
                                CONCAT('Payment failed for order #', o.order_number) as message,
                                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                                'high' as severity
                            FROM order_payments op
                            JOIN orders o ON op.order_id = o.order_id
                            JOIN users u ON o.user_id = u.user_id
                            WHERE op.status = 'failed'
                            ORDER BY op.created_at DESC
                            LIMIT 10";
    
    $stmt = $conn->prepare($failedPaymentsQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $errors[] = [
            'id' => $row['id'],
            'timestamp' => $row['timestamp'],
            'error_type' => $row['error_type'],
            'message' => $row['message'],
            'user_name' => $row['user_name'],
            'severity' => $row['severity']
        ];
    }
    $stmt->close();
    
    // Get cancelled orders as errors
    $cancelledOrdersQuery = "SELECT 
                                o.order_id as id,
                                o.created_at as timestamp,
                                'OrderError' as error_type,
                                CONCAT('Order #', o.order_number, ' was cancelled') as message,
                                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                                'medium' as severity
                            FROM orders o
                            JOIN users u ON o.user_id = u.user_id
                            JOIN order_status_history osh ON o.order_id = osh.order_id
                            WHERE osh.status = 'cancelled'
                            ORDER BY o.created_at DESC
                            LIMIT 5";
    
    $stmt = $conn->prepare($cancelledOrdersQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $errors[] = [
            'id' => $row['id'],
            'timestamp' => $row['timestamp'],
            'error_type' => $row['error_type'],
            'message' => $row['message'],
            'user_name' => $row['user_name'],
            'severity' => $row['severity']
        ];
    }
    $stmt->close();
    
    // Get inactive users as potential issues
    $inactiveUsersQuery = "SELECT 
                                user_id as id,
                                updated_at as timestamp,
                                'UserInactive' as error_type,
                                CONCAT('User ', first_name, ' ', last_name, ' has been inactive') as message,
                                CONCAT(first_name, ' ', last_name) as user_name,
                                'low' as severity
                            FROM users
                            WHERE is_active = 0 AND deleted_at IS NULL
                            ORDER BY updated_at DESC
                            LIMIT 5";
    
    $stmt = $conn->prepare($inactiveUsersQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $errors[] = [
            'id' => $row['id'],
            'timestamp' => $row['timestamp'],
            'error_type' => $row['error_type'],
            'message' => $row['message'],
            'user_name' => $row['user_name'],
            'severity' => $row['severity']
        ];
    }
    $stmt->close();
    
    // Sort by timestamp
    usort($errors, function($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });

    echo json_encode([
        'status' => 'success',
        'message' => 'Error logs retrieved successfully',
        'data' => $errors,
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve error logs: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
