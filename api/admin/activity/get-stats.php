<?php
require_once '../../vendor/autoload.php';
require_once '../../core/config.php';
require_once '../../models/index.php';

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
    // Get system statistics
    $stats = [];

    // Active sessions (simulated - you might want to implement session tracking)
    $stats['active_sessions'] = rand(15, 50);

    // Orders today
    $ordersTodayQuery = "SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()";
    $stmt = $conn->prepare($ordersTodayQuery);
    $stmt->execute();
    $stats['orders_today'] = $stmt->get_result()->fetch_assoc()['count'] ?? 0;
    $stmt->close();

    // Revenue today
    $revenueTodayQuery = "SELECT SUM(total_amount) as total FROM orders WHERE DATE(created_at) = CURDATE()";
    $stmt = $conn->prepare($revenueTodayQuery);
    $stmt->execute();
    $stats['revenue_today'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // New users today
    $newUsersTodayQuery = "SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()";
    $stmt = $conn->prepare($newUsersTodayQuery);
    $stmt->execute();
    $stats['new_users_today'] = $stmt->get_result()->fetch_assoc()['count'] ?? 0;
    $stmt->close();

    // Performance data (simulated)
    $stats['hourly_labels'] = [];
    $stats['response_times'] = [];
    $stats['cpu_usage'] = [];
    
    for ($i = 0; $i < 24; $i++) {
        $stats['hourly_labels'][] = sprintf('%02d:00', $i);
        $stats['response_times'][] = rand(50, 200);
        $stats['cpu_usage'][] = rand(20, 80);
    }

    // Activity distribution
    $stats['activity_labels'] = ['Login', 'Order', 'Product', 'Payment', 'Support', 'Error'];
    $stats['activity_counts'] = [rand(20, 50), rand(10, 30), rand(5, 20), rand(5, 15), rand(2, 10), rand(0, 5)];

    echo json_encode([
        'status' => 'success',
        'message' => 'System statistics retrieved successfully',
        'data' => $stats,
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve system statistics: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
