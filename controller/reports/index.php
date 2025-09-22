<?php

require_once '../../vendor/autoload.php';
require_once '../../core/config.php';


session_start();

// Get report parameters
$reportType = $_GET['type'] ?? 'dashboard';
$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo = $_GET['date_to'] ?? date('Y-m-d');
$userRole = $_SESSION['auth']['role'] ?? 'user';
$userId = $_SESSION['auth']['user_id'] ?? null;

if (!$userId) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access',
        'data' => null,
        'http_code' => 401
    ]);
    exit;
}
if (!$userId) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access',
        'data' => null,
        'http_code' => 401
    ]);
    exit;
}

try {
    // Generate report data based on type
    switch ($reportType) {
        case 'dashboard':
            $reportData = generateDashboardData($conn, $userRole, $userId, $dateFrom, $dateTo);
            break;
        case 'sales':
            $reportData = generateSalesData($conn, $userRole, $userId, $dateFrom, $dateTo);
            break;
        case 'inventory':
            $reportData = generateInventoryData($conn, $userRole, $userId, $dateFrom, $dateTo);
            break;
        case 'orders':
            $reportData = generateOrdersData($conn, $userRole, $userId, $dateFrom, $dateTo);
            break;
        case 'performance':
            $reportData = generatePerformanceData($conn, $userRole, $userId, $dateFrom, $dateTo);
            break;
        default:
            $reportData = generateDashboardData($conn, $userRole, $userId, $dateFrom, $dateTo);
    }

    // Return data based on format
    if ($format === 'json') {
        echo json_encode([
            'status' => 'success',
            'message' => 'Report data generated successfully',
            'data' => $reportData,
            'http_code' => 200
        ]);
    } else {
        // Redirect to PDF generation
        $params = http_build_query([
            'type' => $reportType,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'user_role' => $userRole,
            'user_id' => $userId
        ]);
        header("Location: generate-pdf.php?$params");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to generate report: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}

function generateDashboardData($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $data = [
        'title' => ucfirst($userRole) . ' Dashboard Report',
        'period' => "$dateFrom to $dateTo",
        'generated_at' => date('Y-m-d H:i:s'),
        'stats' => [],
        'charts' => [],
        'tables' => []
    ];
    
    // Get basic stats
    $data['stats'] = getBasicStats($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    // Get chart data
    $data['charts'] = getChartData($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    // Get table data
    $data['tables'] = getTableData($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    return $data;
}

function generateSalesData($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $data = [
        'title' => ucfirst($userRole) . ' Sales Report',
        'period' => "$dateFrom to $dateTo",
        'generated_at' => date('Y-m-d H:i:s'),
        'summary' => [],
        'top_products' => [],
        'monthly_trends' => []
    ];
    
    // Get sales summary
    $data['summary'] = getSalesSummary($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    // Get top products
    $data['top_products'] = getTopProducts($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    // Get monthly trends
    $data['monthly_trends'] = getMonthlyTrends($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    return $data;
}

function generateInventoryData($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $data = [
        'title' => ucfirst($userRole) . ' Inventory Report',
        'period' => "$dateFrom to $dateTo",
        'generated_at' => date('Y-m-d H:i:s'),
        'summary' => [],
        'products' => [],
        'movements' => []
    ];
    
    // Get inventory summary
    $data['summary'] = getInventorySummary($conn, $userRole, $userId);
    
    // Get product details
    $data['products'] = getProductInventory($conn, $userRole, $userId);
    
    // Get stock movements
    $data['movements'] = getStockMovements($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    return $data;
}

function generateOrdersData($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $data = [
        'title' => ucfirst($userRole) . ' Orders Report',
        'period' => "$dateFrom to $dateTo",
        'generated_at' => date('Y-m-d H:i:s'),
        'summary' => [],
        'recent_orders' => [],
        'status_breakdown' => []
    ];
    
    // Get orders summary
    $data['summary'] = getOrdersSummary($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    // Get recent orders
    $data['recent_orders'] = getRecentOrders($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    // Get status breakdown
    $data['status_breakdown'] = getOrderStatusBreakdown($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    return $data;
}

function generatePerformanceData($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $data = [
        'title' => ucfirst($userRole) . ' Performance Report',
        'period' => "$dateFrom to $dateTo",
        'generated_at' => date('Y-m-d H:i:s'),
        'metrics' => [],
        'trends' => [],
        'comparisons' => []
    ];
    
    // Get performance metrics
    $data['metrics'] = getPerformanceMetrics($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    // Get trends
    $data['trends'] = getPerformanceTrends($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    // Get comparisons
    $data['comparisons'] = getPerformanceComparisons($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    return $data;
}

function getBasicStats($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $stats = ['revenue' => 0, 'orders' => 0, 'products' => 0, 'customers' => 0];
    
    switch ($userRole) {
        case 'admin':
            $stats['revenue'] = $conn->query("SELECT SUM(total_amount) FROM orders WHERE created_at BETWEEN '$dateFrom' AND '$dateTo'")->fetch_row()[0] ?? 0;
            $stats['orders'] = $conn->query("SELECT COUNT(*) FROM orders WHERE created_at BETWEEN '$dateFrom' AND '$dateTo'")->fetch_row()[0] ?? 0;
            $stats['products'] = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0] ?? 0;
            $stats['customers'] = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetch_row()[0] ?? 0;
            break;
        case 'supplier':
            $stmt = $conn->prepare("SELECT SUM(total_amount) FROM orders WHERE store_id IN (SELECT store_id FROM store_profile WHERE user_id = ?) AND created_at BETWEEN ? AND ?");
            $stmt->bind_param("sss", $userId, $dateFrom, $dateTo);
            $stmt->execute();
            $stats['revenue'] = $stmt->get_result()->fetch_row()[0] ?? 0;
            
            $stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE store_id IN (SELECT store_id FROM store_profile WHERE user_id = ?) AND created_at BETWEEN ? AND ?");
            $stmt->bind_param("sss", $userId, $dateFrom, $dateTo);
            $stmt->execute();
            $stats['orders'] = $stmt->get_result()->fetch_row()[0] ?? 0;
            
            $stats['products'] = $conn->query("SELECT COUNT(*) FROM products WHERE user_id = '$userId'")->fetch_row()[0] ?? 0;
            $stats['customers'] = $conn->query("SELECT COUNT(DISTINCT user_id) FROM orders")->fetch_row()[0] ?? 0;
            break;
        case 'courier':
            $stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE courier_id = ? AND created_at BETWEEN ? AND ?");
            $stmt->bind_param("sss", $userId, $dateFrom, $dateTo);
            $stmt->execute();
            $stats['orders'] = $stmt->get_result()->fetch_row()[0] ?? 0;
            
            $stats['revenue'] = 0;
            $stats['products'] = 0;
            $stats['customers'] = $conn->query("SELECT COUNT(DISTINCT user_id) FROM orders")->fetch_row()[0] ?? 0;
            break;
        case 'user':
            $storeId = $_SESSION['auth']['store_id'] ?? null;
            if ($storeId) {
                $stmt = $conn->prepare("SELECT SUM(total_amount) FROM orders WHERE store_id = ? AND created_at BETWEEN ? AND ?");
                $stmt->bind_param("iss", $storeId, $dateFrom, $dateTo);
                $stmt->execute();
                $stats['revenue'] = $stmt->get_result()->fetch_row()[0] ?? 0;
                
                $stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE store_id = ? AND created_at BETWEEN ? AND ?");
                $stmt->bind_param("iss", $storeId, $dateFrom, $dateTo);
                $stmt->execute();
                $stats['orders'] = $stmt->get_result()->fetch_row()[0] ?? 0;
                
                $stmt = $conn->prepare("SELECT COUNT(*) FROM imported_product WHERE user_id = ? AND store_id = ?");
                $stmt->bind_param("si", $userId, $storeId);
                $stmt->execute();
                $stats['products'] = $stmt->get_result()->fetch_row()[0] ?? 0;
            }
            $stats['customers'] = $conn->query("SELECT COUNT(DISTINCT user_id) FROM orders")->fetch_row()[0] ?? 0;
            break;
    }
    
    return $stats;
}

function getChartData($conn, $userRole, $userId, $dateFrom, $dateTo) {
    // Get monthly sales data
    $monthlyData = [];
    $stmt = $conn->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as orders, SUM(total_amount) as revenue FROM orders WHERE created_at BETWEEN ? AND ? GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month");
    $stmt->bind_param("ss", $dateFrom, $dateTo);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $monthlyData[] = $row;
    }
    
    return ['monthly_sales' => $monthlyData];
}

function getTableData($conn, $userRole, $userId, $dateFrom, $dateTo) {
    // Get recent orders
    $recentOrders = [];
    $stmt = $conn->prepare("SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name FROM orders o JOIN users u ON o.user_id = u.user_id WHERE o.created_at BETWEEN ? AND ? ORDER BY o.created_at DESC LIMIT 10");
    $stmt->bind_param("ss", $dateFrom, $dateTo);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recentOrders[] = $row;
    }
    
    return ['recent_orders' => $recentOrders];
}

function getSalesSummary($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $stats = getBasicStats($conn, $userRole, $userId, $dateFrom, $dateTo);
    return [
        'total_revenue' => $stats['revenue'],
        'total_orders' => $stats['orders'],
        'avg_order_value' => $stats['orders'] > 0 ? $stats['revenue'] / $stats['orders'] : 0
    ];
}

function getTopProducts($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $topProducts = [];
    $stmt = $conn->prepare("SELECT p.product_name, pc.category_name, SUM(oi.quantity) as total_sales, SUM(oi.price * oi.quantity) as total_revenue FROM order_items oi JOIN products p ON oi.product_id = p.product_id JOIN product_categories pc ON p.product_category = pc.category_id JOIN orders o ON oi.order_id = o.order_id WHERE o.created_at BETWEEN ? AND ? GROUP BY p.product_id ORDER BY total_sales DESC LIMIT 10");
    $stmt->bind_param("ss", $dateFrom, $dateTo);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $topProducts[] = $row;
    }
    
    return $topProducts;
}

function getMonthlyTrends($conn, $userRole, $userId, $dateFrom, $dateTo) {
    return getChartData($conn, $userRole, $userId, $dateFrom, $dateTo)['monthly_sales'];
}

function getInventorySummary($conn, $userRole, $userId) {
    $summary = ['total_products' => 0, 'in_stock' => 0, 'low_stock' => 0, 'out_of_stock' => 0];
    
    switch ($userRole) {
        case 'admin':
            $summary['total_products'] = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0] ?? 0;
            $summary['in_stock'] = $conn->query("SELECT COUNT(*) FROM inventory WHERE quantity > 10")->fetch_row()[0] ?? 0;
            $summary['low_stock'] = $conn->query("SELECT COUNT(*) FROM inventory WHERE quantity BETWEEN 1 AND 10")->fetch_row()[0] ?? 0;
            $summary['out_of_stock'] = $conn->query("SELECT COUNT(*) FROM inventory WHERE quantity = 0")->fetch_row()[0] ?? 0;
            break;
        case 'supplier':
            $summary['total_products'] = $conn->query("SELECT COUNT(*) FROM products WHERE user_id = '$userId'")->fetch_row()[0] ?? 0;
            $summary['in_stock'] = $conn->query("SELECT COUNT(*) FROM inventory i JOIN products p ON i.product_id = p.product_id WHERE p.user_id = '$userId' AND i.quantity > 10")->fetch_row()[0] ?? 0;
            $summary['low_stock'] = $conn->query("SELECT COUNT(*) FROM inventory i JOIN products p ON i.product_id = p.product_id WHERE p.user_id = '$userId' AND i.quantity BETWEEN 1 AND 10")->fetch_row()[0] ?? 0;
            $summary['out_of_stock'] = $conn->query("SELECT COUNT(*) FROM inventory i JOIN products p ON i.product_id = p.product_id WHERE p.user_id = '$userId' AND i.quantity = 0")->fetch_row()[0] ?? 0;
            break;
    }
    
    return $summary;
}

function getProductInventory($conn, $userRole, $userId) {
    $products = [];
    $query = "SELECT p.product_name, p.product_sku, i.quantity, i.updated_at FROM products p LEFT JOIN inventory i ON p.product_id = i.product_id";
    
    if ($userRole === 'supplier') {
        $query .= " WHERE p.user_id = '$userId'";
    }
    
    $query .= " ORDER BY p.product_name";
    
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    return $products;
}

function getStockMovements($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $movements = [];
    $query = "SELECT sm.*, p.product_name FROM stock_movements sm JOIN products p ON sm.product_id = p.product_id WHERE sm.created_at BETWEEN '$dateFrom' AND '$dateTo'";
    
    if ($userRole === 'supplier') {
        $query .= " AND p.user_id = '$userId'";
    }
    
    $query .= " ORDER BY sm.created_at DESC LIMIT 50";
    
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $movements[] = $row;
    }
    
    return $movements;
}

function getOrdersSummary($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $stats = getBasicStats($conn, $userRole, $userId, $dateFrom, $dateTo);
    return [
        'total_orders' => $stats['orders'],
        'total_revenue' => $stats['revenue'],
        'completed_orders' => 0, // Would need to query order status
        'pending_orders' => 0   // Would need to query order status
    ];
}

function getRecentOrders($conn, $userRole, $userId, $dateFrom, $dateTo) {
    return getTableData($conn, $userRole, $userId, $dateFrom, $dateTo)['recent_orders'];
}

function getOrderStatusBreakdown($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $breakdown = [];
    $stmt = $conn->prepare("SELECT osh.status, COUNT(*) as count FROM order_status_history osh JOIN orders o ON osh.order_id = o.order_id WHERE o.created_at BETWEEN ? AND ? GROUP BY osh.status");
    $stmt->bind_param("ss", $dateFrom, $dateTo);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $breakdown[] = $row;
    }
    
    return $breakdown;
}

function getPerformanceMetrics($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $stats = getBasicStats($conn, $userRole, $userId, $dateFrom, $dateTo);
    return [
        'revenue_growth' => 0, // Would calculate from previous period
        'order_growth' => 0,   // Would calculate from previous period
        'conversion_rate' => 0 // Would calculate based on visitors/orders
    ];
}

function getPerformanceTrends($conn, $userRole, $userId, $dateFrom, $dateTo) {
    return getChartData($conn, $userRole, $userId, $dateFrom, $dateTo);
}

function getPerformanceComparisons($conn, $userRole, $userId, $dateFrom, $dateTo) {
    return []; // Would compare with previous periods or benchmarks
}
