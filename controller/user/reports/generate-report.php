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

// Get report parameters
$reportType = $_GET['type'] ?? 'complete';
$dateRange = $_GET['date_range'] ?? 'all';
$format = $_GET['format'] ?? 'json';

try {
    // Calculate date range
    $dateCondition = '';
    switch ($dateRange) {
        case 'last7days':
            $dateCondition = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case 'last30days':
            $dateCondition = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
        case 'last3months':
            $dateCondition = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)";
            break;
        case 'last6months':
            $dateCondition = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
            break;
        case 'lastyear':
            $dateCondition = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
            break;
        case 'all':
        default:
            $dateCondition = "";
            break;
    }

    $reportData = [];

    switch ($reportType) {
        case 'sales':
            // Sales Report
            $salesQuery = "SELECT DATE_FORMAT(o.created_at, '%Y-%m-%d') as date, 
                          COUNT(*) as orders, SUM(o.total_amount) as revenue
                          FROM orders o 
                          WHERE o.store_id = ? $dateCondition
                          GROUP BY DATE_FORMAT(o.created_at, '%Y-%m-%d')
                          ORDER BY date DESC";
            $stmt = $conn->prepare($salesQuery);
            $stmt->bind_param("i", $storeId);
            $stmt->execute();
            $salesResult = $stmt->get_result();
            $reportData['sales_data'] = [];
            while ($row = $salesResult->fetch_assoc()) {
                $reportData['sales_data'][] = $row;
            }
            break;

        case 'products':
            // Product Performance Report
            $productsQuery = "SELECT p.product_name, pc.category_name, 
                             SUM(oi.quantity) as total_sales, 
                             SUM(oi.price * oi.quantity) as total_revenue,
                             COUNT(DISTINCT o.order_id) as order_count
                             FROM order_items oi
                             JOIN products p ON oi.product_id = p.product_id
                             JOIN product_categories pc ON p.product_category = pc.category_id
                             JOIN orders o ON oi.order_id = o.order_id
                             WHERE o.store_id = ? $dateCondition
                             GROUP BY p.product_id, p.product_name, pc.category_name
                             ORDER BY total_revenue DESC";
            $stmt = $conn->prepare($productsQuery);
            $stmt->bind_param("i", $storeId);
            $stmt->execute();
            $productsResult = $stmt->get_result();
            $reportData['products_data'] = [];
            while ($row = $productsResult->fetch_assoc()) {
                $reportData['products_data'][] = $row;
            }
            break;

        case 'orders':
            // Order Summary Report
            $ordersQuery = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                            COUNT(oi.order_item_id) as item_count
                            FROM orders o 
                            JOIN users u ON o.user_id = u.user_id
                            LEFT JOIN order_items oi ON o.order_id = oi.order_id
                            WHERE o.store_id = ? $dateCondition
                            GROUP BY o.order_id
                            ORDER BY o.created_at DESC";
            $stmt = $conn->prepare($ordersQuery);
            $stmt->bind_param("i", $storeId);
            $stmt->execute();
            $ordersResult = $stmt->get_result();
            $reportData['orders_data'] = [];
            while ($row = $ordersResult->fetch_assoc()) {
                $reportData['orders_data'][] = $row;
            }
            break;

        case 'complete':
        default:
            // Complete Dashboard Report
            // Get all dashboard data
            $importedProductsQuery = "SELECT COUNT(*) as total_products FROM imported_product WHERE user_id = ? AND store_id = ?";
            $stmt = $conn->prepare($importedProductsQuery);
            $stmt->bind_param("si", $userId, $storeId);
            $stmt->execute();
            $importedProductsResult = $stmt->get_result();
            $totalProducts = $importedProductsResult->fetch_assoc()['total_products'] ?? 0;

            $revenueQuery = "SELECT SUM(total_amount) as total_revenue FROM orders WHERE store_id = ? $dateCondition";
            $stmt = $conn->prepare($revenueQuery);
            $stmt->bind_param("i", $storeId);
            $stmt->execute();
            $revenueResult = $stmt->get_result();
            $totalRevenue = $revenueResult->fetch_assoc()['total_revenue'] ?? 0;

            $ordersQuery = "SELECT COUNT(*) as total_orders FROM orders WHERE store_id = ? $dateCondition";
            $stmt = $conn->prepare($ordersQuery);
            $stmt->bind_param("i", $storeId);
            $stmt->execute();
            $ordersResult = $stmt->get_result();
            $totalOrders = $ordersResult->fetch_assoc()['total_orders'] ?? 0;

            $reportData = [
                'report_info' => [
                    'type' => $reportType,
                    'date_range' => $dateRange,
                    'generated_at' => date('Y-m-d H:i:s'),
                    'store_id' => $storeId
                ],
                'summary' => [
                    'total_revenue' => $totalRevenue,
                    'total_orders' => $totalOrders,
                    'total_products' => $totalProducts,
                    'conversion_rate' => $totalProducts > 0 ? round(($totalOrders / $totalProducts) * 100, 1) : 0
                ]
            ];

            // Add detailed data
            $salesQuery = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                          COUNT(*) as orders, SUM(total_amount) as revenue
                          FROM orders 
                          WHERE store_id = ? $dateCondition
                          GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                          ORDER BY month";
            $stmt = $conn->prepare($salesQuery);
            $stmt->bind_param("i", $storeId);
            $stmt->execute();
            $salesResult = $stmt->get_result();
            $reportData['monthly_sales'] = [];
            while ($row = $salesResult->fetch_assoc()) {
                $reportData['monthly_sales'][] = $row;
            }

            $topProductsQuery = "SELECT p.product_name, pc.category_name, SUM(oi.quantity) as total_sales, 
                               SUM(oi.price * oi.quantity) as total_revenue, 
                               COALESCE(AVG(i.quantity), 0) as stock
                               FROM order_items oi
                               JOIN products p ON oi.product_id = p.product_id
                               JOIN product_categories pc ON p.product_category = pc.category_id
                               LEFT JOIN inventory i ON p.product_id = i.product_id
                               JOIN orders o ON oi.order_id = o.order_id
                               WHERE o.store_id = ? $dateCondition
                               GROUP BY p.product_id, p.product_name, pc.category_name
                               ORDER BY total_sales DESC LIMIT 10";
            $stmt = $conn->prepare($topProductsQuery);
            $stmt->bind_param("i", $storeId);
            $stmt->execute();
            $topProductsResult = $stmt->get_result();
            $reportData['top_products'] = [];
            while ($row = $topProductsResult->fetch_assoc()) {
                $reportData['top_products'][] = $row;
            }
            break;
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Report generated successfully',
        'data' => $reportData,
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to generate report: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
