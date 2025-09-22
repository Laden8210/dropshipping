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

// Get export parameters
$dataType = $_GET['data_type'] ?? 'dashboard';
$format = $_GET['format'] ?? 'json';

try {
    $exportData = [];
    $filename = '';
    $mimeType = '';

    switch ($dataType) {
        case 'dashboard':
            // Export complete dashboard data
            $importedProductsQuery = "SELECT COUNT(*) as total_products FROM imported_product WHERE user_id = ? AND store_id = ?";
            $stmt = $conn->prepare($importedProductsQuery);
            $stmt->bind_param("si", $userId, $storeId);
            $stmt->execute();
            $importedProductsResult = $stmt->get_result();
            $totalProducts = $importedProductsResult->fetch_assoc()['total_products'] ?? 0;

            $revenueQuery = "SELECT SUM(total_amount) as total_revenue FROM orders WHERE store_id = ?";
            $stmt = $conn->prepare($revenueQuery);
            $stmt->bind_param("i", $storeId);
            $stmt->execute();
            $revenueResult = $stmt->get_result();
            $totalRevenue = $revenueResult->fetch_assoc()['total_revenue'] ?? 0;

            $ordersQuery = "SELECT COUNT(*) as total_orders FROM orders WHERE store_id = ?";
            $stmt = $conn->prepare($ordersQuery);
            $stmt->bind_param("i", $storeId);
            $stmt->execute();
            $ordersResult = $stmt->get_result();
            $totalOrders = $ordersResult->fetch_assoc()['total_orders'] ?? 0;

            $exportData = [
                'export_info' => [
                    'type' => 'dashboard',
                    'exported_at' => date('Y-m-d H:i:s'),
                    'store_id' => $storeId
                ],
                'stats' => [
                    'total_revenue' => $totalRevenue,
                    'total_orders' => $totalOrders,
                    'total_products' => $totalProducts,
                    'conversion_rate' => $totalProducts > 0 ? round(($totalOrders / $totalProducts) * 100, 1) : 0
                ]
            ];

            // Add recent orders
            $recentOrdersQuery = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name 
                                  FROM orders o 
                                  JOIN users u ON o.user_id = u.user_id 
                                  WHERE o.store_id = ? 
                                  ORDER BY o.created_at DESC LIMIT 10";
            $stmt = $conn->prepare($recentOrdersQuery);
            $stmt->bind_param("i", $storeId);
            $stmt->execute();
            $recentOrdersResult = $stmt->get_result();
            $exportData['recent_orders'] = [];
            while ($row = $recentOrdersResult->fetch_assoc()) {
                $exportData['recent_orders'][] = $row;
            }

            // Add top products
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
                               ORDER BY total_sales DESC LIMIT 10";
            $stmt = $conn->prepare($topProductsQuery);
            $stmt->bind_param("i", $storeId);
            $stmt->execute();
            $topProductsResult = $stmt->get_result();
            $exportData['top_products'] = [];
            while ($row = $topProductsResult->fetch_assoc()) {
                $exportData['top_products'][] = $row;
            }

            $filename = 'dashboard_data_' . date('Y-m-d') . '.' . $format;
            break;

        case 'orders':
            // Export orders data
            $ordersQuery = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                            COUNT(oi.order_item_id) as item_count
                            FROM orders o 
                            JOIN users u ON o.user_id = u.user_id
                            LEFT JOIN order_items oi ON o.order_id = oi.order_id
                            WHERE o.store_id = ?
                            GROUP BY o.order_id
                            ORDER BY o.created_at DESC";
            $stmt = $conn->prepare($ordersQuery);
            $stmt->bind_param("i", $storeId);
            $stmt->execute();
            $ordersResult = $stmt->get_result();
            $exportData = [];
            while ($row = $ordersResult->fetch_assoc()) {
                $exportData[] = $row;
            }
            $filename = 'orders_data_' . date('Y-m-d') . '.' . $format;
            break;

        case 'products':
            // Export products data
            $productsQuery = "SELECT p.*, pc.category_name, COALESCE(i.quantity, 0) as stock
                              FROM products p
                              JOIN product_categories pc ON p.product_category = pc.category_id
                              LEFT JOIN inventory i ON p.product_id = i.product_id
                              WHERE p.user_id = ?
                              ORDER BY p.created_at DESC";
            $stmt = $conn->prepare($productsQuery);
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $productsResult = $stmt->get_result();
            $exportData = [];
            while ($row = $productsResult->fetch_assoc()) {
                $exportData[] = $row;
            }
            $filename = 'products_data_' . date('Y-m-d') . '.' . $format;
            break;

        case 'sales':
            // Export sales data
            $salesQuery = "SELECT DATE_FORMAT(o.created_at, '%Y-%m-%d') as date, 
                          COUNT(*) as orders, SUM(o.total_amount) as revenue
                          FROM orders o 
                          WHERE o.store_id = ?
                          GROUP BY DATE_FORMAT(o.created_at, '%Y-%m-%d')
                          ORDER BY date DESC";
            $stmt = $conn->prepare($salesQuery);
            $stmt->bind_param("i", $storeId);
            $stmt->execute();
            $salesResult = $stmt->get_result();
            $exportData = [];
            while ($row = $salesResult->fetch_assoc()) {
                $exportData[] = $row;
            }
            $filename = 'sales_data_' . date('Y-m-d') . '.' . $format;
            break;
    }

    // Format data based on export format
    $content = '';
    switch ($format) {
        case 'json':
            $content = json_encode($exportData, JSON_PRETTY_PRINT);
            $mimeType = 'application/json';
            break;
        case 'csv':
            $content = convertToCSV($exportData);
            $mimeType = 'text/csv';
            break;
        case 'excel':
            $content = convertToExcel($exportData);
            $mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            break;
        default:
            $content = json_encode($exportData, JSON_PRETTY_PRINT);
            $mimeType = 'application/json';
            break;
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Data exported successfully',
        'content' => $content,
        'filename' => $filename,
        'mime_type' => $mimeType,
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to export data: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}

function convertToCSV($data) {
    if (empty($data)) return '';
    
    $csv = '';
    
    // Handle different data structures
    if (isset($data[0]) && is_array($data[0])) {
        // Array of objects
        $headers = array_keys($data[0]);
        $csv .= implode(',', $headers) . "\n";
        
        foreach ($data as $row) {
            $csvRow = [];
            foreach ($headers as $header) {
                $csvRow[] = '"' . str_replace('"', '""', $row[$header] ?? '') . '"';
            }
            $csv .= implode(',', $csvRow) . "\n";
        }
    } else {
        // Single object or nested structure
        $csv = json_encode($data, JSON_PRETTY_PRINT);
    }
    
    return $csv;
}

function convertToExcel($data) {
    // For simplicity, return JSON format
    // In a real implementation, you would use a library like PhpSpreadsheet
    return json_encode($data, JSON_PRETTY_PRINT);
}
