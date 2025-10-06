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
    // Get system activity logs from existing database tables
    $activities = [];
    
    // Get recent orders as activity
    $ordersQuery = "SELECT 
                        o.order_id as id,
                        o.user_id,
                        CONCAT(u.first_name, ' ', u.last_name) as user_name,
                        u.role as user_role,
                        'order' as activity_type,
                        'New Order Placed' as title,
                        CONCAT('Order #', o.order_number, ' for $', o.total_amount) as description,
                        o.created_at
                    FROM orders o
                    JOIN users u ON o.user_id = u.user_id
                    ORDER BY o.created_at DESC
                    LIMIT 20";
    
    $stmt = $conn->prepare($ordersQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $activities[] = [
            'id' => $row['id'],
            'user_id' => $row['user_id'],
            'user_name' => $row['user_name'],
            'user_role' => $row['user_role'],
            'type' => $row['activity_type'],
            'title' => $row['title'],
            'description' => $row['description'],
            'created_at' => $row['created_at']
        ];
    }
    $stmt->close();
    
    // Get new user registrations as activity
    $usersQuery = "SELECT 
                        user_id as id,
                        user_id,
                        CONCAT(first_name, ' ', last_name) as user_name,
                        role as user_role,
                        'login' as activity_type,
                        'New User Registration' as title,
                        CONCAT('User registered as ', role) as description,
                        created_at
                    FROM users
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                    ORDER BY created_at DESC
                    LIMIT 10";
    
    $stmt = $conn->prepare($usersQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $activities[] = [
            'id' => $row['id'],
            'user_id' => $row['user_id'],
            'user_name' => $row['user_name'],
            'user_role' => $row['user_role'],
            'type' => $row['activity_type'],
            'title' => $row['title'],
            'description' => $row['description'],
            'created_at' => $row['created_at']
        ];
    }
    $stmt->close();
    
    // Get new products as activity
    $productsQuery = "SELECT 
                        p.product_id as id,
                        p.user_id,
                        CONCAT(u.first_name, ' ', u.last_name) as user_name,
                        u.role as user_role,
                        'product' as activity_type,
                        'New Product Added' as title,
                        CONCAT('Product: ', p.product_name) as description,
                        p.created_at
                    FROM products p
                    JOIN users u ON p.user_id = u.user_id
                    WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                    ORDER BY p.created_at DESC
                    LIMIT 10";
    
    $stmt = $conn->prepare($productsQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $activities[] = [
            'id' => $row['id'],
            'user_id' => $row['user_id'],
            'user_name' => $row['user_name'],
            'user_role' => $row['user_role'],
            'type' => $row['activity_type'],
            'title' => $row['title'],
            'description' => $row['description'],
            'created_at' => $row['created_at']
        ];
    }
    $stmt->close();
    
    // Sort activities by date
    usort($activities, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    // Limit to 50 most recent activities
    $activities = array_slice($activities, 0, 50);

    echo json_encode([
        'status' => 'success',
        'message' => 'Activity logs retrieved successfully',
        'data' => $activities,
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve activity logs: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
