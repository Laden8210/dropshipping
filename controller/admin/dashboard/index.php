<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
ini_set('session.use_strict_mode', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

session_start();

if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'admin') {
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

// Check if user is admin
$userRole = $_SESSION['auth']['role'] ?? null;

if ($userRole !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access - Admin only',
        'data' => null,
        'http_code' => 403
    ]);
    exit;
}

try {
    // Get system-wide stats - Direct SQL queries
    // Total users
    $totalUsersQuery = "SELECT COUNT(*) as total_users FROM users";
    $stmt = $conn->prepare($totalUsersQuery);
    $stmt->execute();
    $totalUsersResult = $stmt->get_result();
    $totalUsers = $totalUsersResult->fetch_assoc()['total_users'] ?? 0;

    // Total suppliers
    $totalSuppliersQuery = "SELECT COUNT(*) as total_suppliers FROM users WHERE role = 'supplier'";
    $stmt = $conn->prepare($totalSuppliersQuery);
    $stmt->execute();
    $totalSuppliersResult = $stmt->get_result();
    $totalSuppliers = $totalSuppliersResult->fetch_assoc()['total_suppliers'] ?? 0;

    // Total couriers
    $totalCouriersQuery = "SELECT COUNT(*) as total_couriers FROM users WHERE role = 'courier'";
    $stmt = $conn->prepare($totalCouriersQuery);
    $stmt->execute();
    $totalCouriersResult = $stmt->get_result();
    $totalCouriers = $totalCouriersResult->fetch_assoc()['total_couriers'] ?? 0;

    // Total revenue
    $totalRevenueQuery = "SELECT SUM(total_amount) as total_revenue FROM orders";
    $stmt = $conn->prepare($totalRevenueQuery);
    $stmt->execute();
    $totalRevenueResult = $stmt->get_result();
    $totalRevenue = $totalRevenueResult->fetch_assoc()['total_revenue'] ?? 0;

    // Total orders
    $totalOrdersQuery = "SELECT COUNT(*) as total_orders FROM orders";
    $stmt = $conn->prepare($totalOrdersQuery);
    $stmt->execute();
    $totalOrdersResult = $stmt->get_result();
    $totalOrders = $totalOrdersResult->fetch_assoc()['total_orders'] ?? 0;

    // Total products
    $totalProductsQuery = "SELECT COUNT(*) as total_products FROM products";
    $stmt = $conn->prepare($totalProductsQuery);
    $stmt->execute();
    $totalProductsResult = $stmt->get_result();
    $totalProducts = $totalProductsResult->fetch_assoc()['total_products'] ?? 0;

    // Active stores
    $activeStoresQuery = "SELECT COUNT(*) as active_stores FROM store_profile";
    $stmt = $conn->prepare($activeStoresQuery);
    $stmt->execute();
    $activeStoresResult = $stmt->get_result();
    $activeStores = $activeStoresResult->fetch_assoc()['active_stores'] ?? 0;

    // Recent orders
    $recentOrdersQuery = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name, sp.store_name
                          FROM orders o 
                          JOIN users u ON o.user_id = u.user_id 
                          LEFT JOIN store_profile sp ON o.store_id = sp.store_id
                          ORDER BY o.created_at DESC LIMIT 5";
    $stmt = $conn->prepare($recentOrdersQuery);
    $stmt->execute();
    $recentOrdersResult = $stmt->get_result();
    $recentOrders = [];
    while ($row = $recentOrdersResult->fetch_assoc()) {
        $recentOrders[] = $row;
    }

    // Top performing stores - Fixed GROUP BY clause
    $topStoresQuery = "SELECT sp.store_name, COUNT(o.order_id) as total_orders, SUM(o.total_amount) as total_revenue
                       FROM store_profile sp
                       LEFT JOIN orders o ON sp.store_id = o.store_id
                       GROUP BY sp.store_id, sp.store_name
                       ORDER BY total_revenue DESC LIMIT 5";
    $stmt = $conn->prepare($topStoresQuery);
    $stmt->execute();
    $topStoresResult = $stmt->get_result();
    $topStores = [];
    while ($row = $topStoresResult->fetch_assoc()) {
        $topStores[] = $row;
    }

    // Monthly revenue data
    $monthlyRevenueQuery = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                            COUNT(*) as orders, SUM(total_amount) as revenue
                            FROM orders 
                            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                            ORDER BY month";
    $stmt = $conn->prepare($monthlyRevenueQuery);
    $stmt->execute();
    $monthlyRevenueResult = $stmt->get_result();
    $monthlyRevenue = [];
    while ($row = $monthlyRevenueResult->fetch_assoc()) {
        $monthlyRevenue[] = $row;
    }

    // User registration trends
    $userTrendsQuery = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                        COUNT(*) as new_users
                        FROM users 
                        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                        ORDER BY month";
    $stmt = $conn->prepare($userTrendsQuery);
    $stmt->execute();
    $userTrendsResult = $stmt->get_result();
    $userTrends = [];
    while ($row = $userTrendsResult->fetch_assoc()) {
        $userTrends[] = $row;
    }

    // Order status distribution - Fixed GROUP BY clause
    $orderStatusQuery = "SELECT osh.status, COUNT(*) as count
                         FROM order_status_history osh
                         JOIN (
                             SELECT order_id, MAX(created_at) as latest_status
                             FROM order_status_history
                             GROUP BY order_id
                         ) latest ON osh.order_id = latest.order_id AND osh.created_at = latest.latest_status
                         GROUP BY osh.status";
    $stmt = $conn->prepare($orderStatusQuery);
    $stmt->execute();
    $orderStatusResult = $stmt->get_result();
    $orderStatus = [];
    while ($row = $orderStatusResult->fetch_assoc()) {
        $orderStatus[] = $row;
    }

    $dashboardData = [
        'stats' => [
            'total_revenue' => $totalRevenue,
            'total_users' => $totalUsers,
            'total_orders' => $totalOrders,
            'active_stores' => $activeStores,
            'total_suppliers' => $totalSuppliers,
            'total_couriers' => $totalCouriers,
            'total_products' => $totalProducts,
            'conversion_rate' => $totalUsers > 0 ? round(($totalOrders / $totalUsers) * 100, 1) : 0
        ],
        'recent_orders' => $recentOrders,
        'top_stores' => $topStores,
        'monthly_revenue' => $monthlyRevenue,
        'user_trends' => $userTrends,
        'order_status' => $orderStatus
    ];

    echo json_encode([
        'status' => 'success',
        'message' => 'Admin dashboard data retrieved successfully',
        'data' => $dashboardData,
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve admin dashboard data: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}