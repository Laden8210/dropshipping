<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
ini_set('session.use_strict_mode', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

session_start();

if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'supplier') {
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

// Get supplier stats
$userId = $_SESSION['auth']['user_id'] ?? null;

if (!$userId) {
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
    // Get supplier products count - Direct SQL query
    $supplierProductsQuery = "SELECT COUNT(*) as total_products FROM products WHERE user_id = ?";
    $stmt = $conn->prepare($supplierProductsQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $supplierProductsResult = $stmt->get_result();
    $totalProducts = $supplierProductsResult->fetch_assoc()['total_products'] ?? 0;

    // Get total revenue - Orders containing supplier's products
    $revenueQuery = "SELECT SUM(o.total_amount) as total_revenue 
                     FROM orders o
                     JOIN order_items oi ON o.order_id = oi.order_id
                     JOIN products p ON oi.product_id = p.product_id
                     WHERE p.user_id = ?";
    $stmt = $conn->prepare($revenueQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $revenueResult = $stmt->get_result();
    $totalRevenue = $revenueResult->fetch_assoc()['total_revenue'] ?? 0;

    // Get total orders - Orders containing supplier's products
    $ordersQuery = "SELECT COUNT(DISTINCT o.order_id) as total_orders 
                    FROM orders o
                    JOIN order_items oi ON o.order_id = oi.order_id
                    JOIN products p ON oi.product_id = p.product_id
                    WHERE p.user_id = ?";
    $stmt = $conn->prepare($ordersQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $ordersResult = $stmt->get_result();
    $totalOrders = $ordersResult->fetch_assoc()['total_orders'] ?? 0;

    // Get low stock products - Direct SQL query
    $lowStockQuery = "SELECT COUNT(*) as low_stock FROM inventory i 
                      JOIN products p ON i.product_id = p.product_id 
                      WHERE p.user_id = ? AND i.quantity < 10";
    $stmt = $conn->prepare($lowStockQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $lowStockResult = $stmt->get_result();
    $lowStockCount = $lowStockResult->fetch_assoc()['low_stock'] ?? 0;

    // Get recent orders - Orders containing supplier's products
    $recentOrdersQuery = "SELECT DISTINCT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name 
                          FROM orders o
                          JOIN order_items oi ON o.order_id = oi.order_id
                          JOIN products p ON oi.product_id = p.product_id
                          JOIN users u ON o.user_id = u.user_id 
                          WHERE p.user_id = ? 
                          ORDER BY o.created_at DESC LIMIT 5";
    $stmt = $conn->prepare($recentOrdersQuery);
    $stmt->bind_param("s", $userId);
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
                         WHERE p.user_id = ?
                         GROUP BY p.product_id, p.product_name, pc.category_name
                         ORDER BY total_sales DESC LIMIT 5";
    $stmt = $conn->prepare($topProductsQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $topProductsResult = $stmt->get_result();
    $topProducts = [];
    while ($row = $topProductsResult->fetch_assoc()) {
        $topProducts[] = $row;
    }

    // Get monthly sales data for chart - Orders containing supplier's products
    $monthlySalesQuery = "SELECT DATE_FORMAT(o.created_at, '%Y-%m') as month, 
                          COUNT(DISTINCT o.order_id) as orders, SUM(o.total_amount) as revenue
                          FROM orders o
                          JOIN order_items oi ON o.order_id = oi.order_id
                          JOIN products p ON oi.product_id = p.product_id
                          WHERE p.user_id = ?
                          AND o.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                          GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
                          ORDER BY month";
    $stmt = $conn->prepare($monthlySalesQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $monthlySalesResult = $stmt->get_result();
    $monthlySales = [];
    while ($row = $monthlySalesResult->fetch_assoc()) {
        $monthlySales[] = $row;
    }

    // Get category revenue data - Fixed GROUP BY clause
    $categoryRevenueQuery = "SELECT pc.category_name, SUM(oi.price * oi.quantity) as revenue
                             FROM order_items oi
                             JOIN products p ON oi.product_id = p.product_id
                             JOIN product_categories pc ON p.product_category = pc.category_id
                             JOIN orders o ON oi.order_id = o.order_id
                             WHERE p.user_id = ?
                             GROUP BY pc.category_id, pc.category_name
                             ORDER BY revenue DESC";
    $stmt = $conn->prepare($categoryRevenueQuery);
    $stmt->bind_param("s", $userId);
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
            'low_stock_count' => $lowStockCount
        ],
        'recent_orders' => $recentOrders,
        'top_products' => $topProducts,
        'monthly_sales' => $monthlySales,
        'category_revenue' => $categoryRevenue
    ];

    echo json_encode([
        'status' => 'success',
        'message' => 'Supplier dashboard data retrieved successfully',
        'data' => $dashboardData,
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve supplier dashboard data: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}