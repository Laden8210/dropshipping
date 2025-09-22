<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
ini_set('session.use_strict_mode', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

session_start();

if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'user') {
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

// Get user stats
$userId = $_SESSION['auth']['user_id'] ?? null;
$storeId = $_SESSION['auth']['store_id'] ?? null;

if (!$userId || !$storeId) {
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
    // Get imported products count - Direct SQL query
    $importedProductsQuery = "SELECT COUNT(*) as total_products FROM imported_product WHERE user_id = ? AND store_id = ?";
    $stmt = $conn->prepare($importedProductsQuery);
    $stmt->bind_param("si", $userId, $storeId);
    $stmt->execute();
    $importedProductsResult = $stmt->get_result();
    $totalProducts = $importedProductsResult->fetch_assoc()['total_products'] ?? 0;

    // Get total revenue for this store
    $revenueQuery = "SELECT SUM(total_amount) as total_revenue FROM orders WHERE store_id = ?";
    $stmt = $conn->prepare($revenueQuery);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $revenueResult = $stmt->get_result();
    $totalRevenue = $revenueResult->fetch_assoc()['total_revenue'] ?? 0;

    // Get total orders for this store
    $ordersQuery = "SELECT COUNT(*) as total_orders FROM orders WHERE store_id = ?";
    $stmt = $conn->prepare($ordersQuery);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $ordersResult = $stmt->get_result();
    $totalOrders = $ordersResult->fetch_assoc()['total_orders'] ?? 0;

    // Get recent orders
    $recentOrdersQuery = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name 
                          FROM orders o 
                          JOIN users u ON o.user_id = u.user_id 
                          WHERE o.store_id = ? 
                          ORDER BY o.created_at DESC LIMIT 5";
    $stmt = $conn->prepare($recentOrdersQuery);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $recentOrdersResult = $stmt->get_result();
    $recentOrders = [];
    while ($row = $recentOrdersResult->fetch_assoc()) {
        $recentOrders[] = $row;
    }

    // Get top products by sales - Fixed GROUP BY clause
    $topProductsQuery = "SELECT p.product_name, pc.category_name, SUM(oi.quantity) as total_sales, 
                         SUM(oi.price * oi.quantity) as total_revenue, 
                         COALESCE(AVG(i.quantity), 0) as stock
                         FROM order_items oi
                         JOIN products p ON oi.product_id = p.product_id
                         JOIN product_categories pc ON p.product_category = pc.category_id
                         LEFT JOIN inventory i ON p.product_id = i.product_id
                         JOIN orders o ON oi.order_id = o.order_id
                         WHERE o.store_id = ?
                         GROUP BY p.product_id, p.product_name, pc.category_name
                         ORDER BY total_sales DESC LIMIT 5";
    $stmt = $conn->prepare($topProductsQuery);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $topProductsResult = $stmt->get_result();
    $topProducts = [];
    while ($row = $topProductsResult->fetch_assoc()) {
        $topProducts[] = $row;
    }

    // Get monthly sales data for chart
    $monthlySalesQuery = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                          COUNT(*) as orders, SUM(total_amount) as revenue
                          FROM orders 
                          WHERE store_id = ?
                          AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                          GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                          ORDER BY month";
    $stmt = $conn->prepare($monthlySalesQuery);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $monthlySalesResult = $stmt->get_result();
    $monthlySales = [];
    while ($row = $monthlySalesResult->fetch_assoc()) {
        $monthlySales[] = $row;
    }

    // Get category revenue data
    $categoryRevenueQuery = "SELECT pc.category_name, SUM(oi.price * oi.quantity) as revenue
                             FROM order_items oi
                             JOIN products p ON oi.product_id = p.product_id
                             JOIN product_categories pc ON p.product_category = pc.category_id
                             JOIN orders o ON oi.order_id = o.order_id
                             WHERE o.store_id = ?
                             GROUP BY pc.category_id, pc.category_name
                             ORDER BY revenue DESC";
    $stmt = $conn->prepare($categoryRevenueQuery);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $categoryRevenueResult = $stmt->get_result();
    $categoryRevenue = [];
    while ($row = $categoryRevenueResult->fetch_assoc()) {
        $categoryRevenue[] = $row;
    }

    $dashboardData = [
        'stats' => [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'total_products' => $totalProducts,
            'conversion_rate' => $totalProducts > 0 ? round(($totalOrders / $totalProducts) * 100, 1) : 0
        ],
        'recent_orders' => $recentOrders,
        'top_products' => $topProducts,
        'monthly_sales' => $monthlySales,
        'category_revenue' => $categoryRevenue
    ];

    echo json_encode([
        'status' => 'success',
        'message' => 'Dashboard data retrieved successfully',
        'data' => $dashboardData,
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve dashboard data: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
