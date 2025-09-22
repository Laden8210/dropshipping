<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
ini_set('session.use_strict_mode', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

session_start();

if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'courier') {
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

// Get courier stats
$courierId = $_SESSION['auth']['user_id'] ?? null;

if (!$courierId) {
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
    // Get total deliveries - Orders with tracking numbers (assigned to courier)
    $totalDeliveriesQuery = "SELECT COUNT(*) as total_deliveries FROM orders WHERE tracking_number IS NOT NULL";
    $stmt = $conn->prepare($totalDeliveriesQuery);
    $stmt->execute();
    $totalDeliveriesResult = $stmt->get_result();
    $totalDeliveries = $totalDeliveriesResult->fetch_assoc()['total_deliveries'] ?? 0;

    // Get completed deliveries - Orders with tracking numbers and delivered status
    $completedDeliveriesQuery = "SELECT COUNT(*) as completed_deliveries FROM orders o 
                                JOIN order_status_history osh ON o.order_id = osh.order_id 
                                WHERE o.tracking_number IS NOT NULL AND osh.status = 'delivered'";
    $stmt = $conn->prepare($completedDeliveriesQuery);
    $stmt->execute();
    $completedDeliveriesResult = $stmt->get_result();
    $completedDeliveries = $completedDeliveriesResult->fetch_assoc()['completed_deliveries'] ?? 0;

    // Get pending deliveries - Orders with tracking numbers but not delivered
    $pendingDeliveriesQuery = "SELECT COUNT(*) as pending_deliveries FROM orders o 
                               JOIN order_status_history osh ON o.order_id = osh.order_id 
                               WHERE o.tracking_number IS NOT NULL AND osh.status IN ('assigned', 'picked_up', 'in_transit')";
    $stmt = $conn->prepare($pendingDeliveriesQuery);
    $stmt->execute();
    $pendingDeliveriesResult = $stmt->get_result();
    $pendingDeliveries = $pendingDeliveriesResult->fetch_assoc()['pending_deliveries'] ?? 0;

    // Get today's deliveries - Orders with tracking numbers created today
    $todayDeliveriesQuery = "SELECT COUNT(*) as today_deliveries FROM orders o 
                             WHERE o.tracking_number IS NOT NULL AND DATE(o.created_at) = CURDATE()";
    $stmt = $conn->prepare($todayDeliveriesQuery);
    $stmt->execute();
    $todayDeliveriesResult = $stmt->get_result();
    $todayDeliveries = $todayDeliveriesResult->fetch_assoc()['today_deliveries'] ?? 0;

    // Get recent deliveries - Orders with tracking numbers
    $recentDeliveriesQuery = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name, 
                              a.address_line as address_line1, a.city, a.region as state, a.postal_code
                              FROM orders o 
                              JOIN users u ON o.user_id = u.user_id 
                              LEFT JOIN user_shipping_address a ON o.shipping_address_id = a.address_id
                              WHERE o.tracking_number IS NOT NULL 
                              ORDER BY o.created_at DESC LIMIT 5";
    $stmt = $conn->prepare($recentDeliveriesQuery);
    $stmt->execute();
    $recentDeliveriesResult = $stmt->get_result();
    $recentDeliveries = [];
    while ($row = $recentDeliveriesResult->fetch_assoc()) {
        $recentDeliveries[] = $row;
    }

    // Get delivery performance by status - Fixed GROUP BY clause
    $deliveryStatusQuery = "SELECT osh.status, COUNT(*) as count
                            FROM orders o
                            JOIN order_status_history osh ON o.order_id = osh.order_id
                            WHERE o.tracking_number IS NOT NULL
                            GROUP BY osh.status";
    $stmt = $conn->prepare($deliveryStatusQuery);
    $stmt->execute();
    $deliveryStatusResult = $stmt->get_result();
    $deliveryStatus = [];
    while ($row = $deliveryStatusResult->fetch_assoc()) {
        $deliveryStatus[] = $row;
    }

    // Get weekly delivery performance - Fixed GROUP BY clause
    $weeklyPerformanceQuery = "SELECT DATE(o.created_at) as delivery_date, COUNT(*) as deliveries
                              FROM orders o
                              WHERE o.tracking_number IS NOT NULL
                              AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                              GROUP BY DATE(o.created_at)
                              ORDER BY delivery_date";
    $stmt = $conn->prepare($weeklyPerformanceQuery);
    $stmt->execute();
    $weeklyPerformanceResult = $stmt->get_result();
    $weeklyPerformance = [];
    while ($row = $weeklyPerformanceResult->fetch_assoc()) {
        $weeklyPerformance[] = $row;
    }

    $dashboardData = [
        'stats' => [
            'total_deliveries' => $totalDeliveries,
            'completed_deliveries' => $completedDeliveries,
            'pending_deliveries' => $pendingDeliveries,
            'today_deliveries' => $todayDeliveries
        ],
        'recent_deliveries' => $recentDeliveries,
        'delivery_status' => $deliveryStatus,
        'weekly_performance' => $weeklyPerformance
    ];

    echo json_encode([
        'status' => 'success',
        'message' => 'Courier dashboard data retrieved successfully',
        'data' => $dashboardData,
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve courier dashboard data: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}