<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
ini_set('session.use_strict_mode', 1);

session_start();

if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'user') {
    http_response_code(403);
    echo 'Forbidden: You do not have permission to access this resource.';
    exit;
}

require_once '../../../core/config.php';
require_once '../../../vendor/autoload.php';

// Get user stats
$userId = $_SESSION['auth']['user_id'] ?? null;
$storeId = $_SESSION['auth']['store_id'] ?? null;

if (!$userId || !$storeId) {
    http_response_code(403);
    echo 'Unauthorized access';
    exit;
}

// Get report parameters
$reportType = $_GET['type'] ?? 'complete';
$dateRange = $_GET['date_range'] ?? 'all';

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

    // Get store information
    $storeQuery = "SELECT store_name, store_description FROM store_profile WHERE store_id = ?";
    $stmt = $conn->prepare($storeQuery);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $storeResult = $stmt->get_result();
    $storeInfo = $storeResult->fetch_assoc();

    // Get report data based on type
    $reportData = [];
    $reportTitle = '';

    switch ($reportType) {
        case 'sales':
            $reportTitle = 'Sales Report';
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
            $reportData = [];
            while ($row = $salesResult->fetch_assoc()) {
                $reportData[] = $row;
            }
            break;

        case 'products':
            $reportTitle = 'Product Performance Report';
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
            $reportData = [];
            while ($row = $productsResult->fetch_assoc()) {
                $reportData[] = $row;
            }
            break;

        case 'orders':
            $reportTitle = 'Order Summary Report';
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
            $reportData = [];
            while ($row = $ordersResult->fetch_assoc()) {
                $reportData[] = $row;
            }
            break;

        case 'complete':
        default:
            $reportTitle = 'Complete Dashboard Report';
            // Get summary stats
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
                'summary' => [
                    'total_revenue' => $totalRevenue,
                    'total_orders' => $totalOrders,
                    'total_products' => $totalProducts,
                    'conversion_rate' => $totalProducts > 0 ? round(($totalOrders / $totalProducts) * 100, 1) : 0
                ]
            ];
            break;
    }

    // Generate PDF using dompdf
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->setPaper('A4', 'portrait');

    // Generate HTML content
    $html = generateReportHTML($reportTitle, $storeInfo, $reportData, $dateRange, $reportType);
    
    $dompdf->loadHtml($html);
    $dompdf->render();

    // Output PDF
    $filename = strtolower($reportType) . '_report_' . date('Y-m-d') . '.pdf';
    $dompdf->stream($filename, ['Attachment' => 1]);

} catch (Exception $e) {
    http_response_code(500);
    echo 'Failed to generate PDF report: ' . $e->getMessage();
}

function generateReportHTML($title, $storeInfo, $reportData, $dateRange, $reportType) {
    $dateRangeText = ucfirst(str_replace('_', ' ', $dateRange));
    $currentDate = date('F j, Y');
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>' . $title . '</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
            .header h1 { color: #333; margin: 0; }
            .header h2 { color: #666; margin: 5px 0; }
            .report-info { margin-bottom: 30px; }
            .report-info table { width: 100%; border-collapse: collapse; }
            .report-info td { padding: 5px; border: none; }
            .report-info td:first-child { font-weight: bold; width: 150px; }
            .summary { margin-bottom: 30px; }
            .summary h3 { color: #333; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
            .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 20px; }
            .summary-card { background: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; }
            .summary-card h4 { margin: 0 0 10px 0; color: #666; font-size: 14px; }
            .summary-card .value { font-size: 24px; font-weight: bold; color: #333; }
            .data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
            .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            .data-table th { background-color: #f2f2f2; font-weight: bold; }
            .data-table tr:nth-child(even) { background-color: #f9f9f9; }
            .footer { margin-top: 50px; text-align: center; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>' . $title . '</h1>
            <h2>' . ($storeInfo['store_name'] ?? 'Store') . '</h2>
        </div>
        
        <div class="report-info">
            <table>
                <tr><td>Generated On:</td><td>' . $currentDate . '</td></tr>
                <tr><td>Date Range:</td><td>' . $dateRangeText . '</td></tr>
                <tr><td>Report Type:</td><td>' . ucfirst($reportType) . '</td></tr>
            </table>
        </div>';

    // Add summary section for complete report
    if ($reportType === 'complete' && isset($reportData['summary'])) {
        $summary = $reportData['summary'];
        $html .= '
        <div class="summary">
            <h3>Summary Statistics</h3>
            <div class="summary-grid">
                <div class="summary-card">
                    <h4>Total Revenue</h4>
                    <div class="value">₱' . number_format($summary['total_revenue'], 2) . '</div>
                </div>
                <div class="summary-card">
                    <h4>Total Orders</h4>
                    <div class="value">' . number_format($summary['total_orders']) . '</div>
                </div>
                <div class="summary-card">
                    <h4>Total Products</h4>
                    <div class="value">' . number_format($summary['total_products']) . '</div>
                </div>
                <div class="summary-card">
                    <h4>Conversion Rate</h4>
                    <div class="value">' . $summary['conversion_rate'] . '%</div>
                </div>
            </div>
        </div>';
    }

    // Add data tables based on report type
    switch ($reportType) {
        case 'sales':
            $html .= generateSalesTable($reportData);
            break;
        case 'products':
            $html .= generateProductsTable($reportData);
            break;
        case 'orders':
            $html .= generateOrdersTable($reportData);
            break;
        case 'complete':
            $html .= generateCompleteReport($reportData);
            break;
    }

    $html .= '
        <div class="footer">
            <p>Generated by Dropshipping System - ' . $currentDate . '</p>
        </div>
    </body>
    </html>';

    return $html;
}

function generateSalesTable($data) {
    $html = '<h3>Sales Data</h3><table class="data-table">
        <thead><tr><th>Date</th><th>Orders</th><th>Revenue</th></tr></thead>
        <tbody>';
    
    foreach ($data as $row) {
        $html .= '<tr>
            <td>' . $row['date'] . '</td>
            <td>' . $row['orders'] . '</td>
            <td>₱' . number_format($row['revenue'], 2) . '</td>
        </tr>';
    }
    
    $html .= '</tbody></table>';
    return $html;
}

function generateProductsTable($data) {
    $html = '<h3>Product Performance</h3><table class="data-table">
        <thead><tr><th>Product</th><th>Category</th><th>Sales</th><th>Revenue</th><th>Orders</th></tr></thead>
        <tbody>';
    
    foreach ($data as $row) {
        $html .= '<tr>
            <td>' . $row['product_name'] . '</td>
            <td>' . $row['category_name'] . '</td>
            <td>' . $row['total_sales'] . '</td>
            <td>₱' . number_format($row['total_revenue'], 2) . '</td>
            <td>' . $row['order_count'] . '</td>
        </tr>';
    }
    
    $html .= '</tbody></table>';
    return $html;
}

function generateOrdersTable($data) {
    $html = '<h3>Order Summary</h3><table class="data-table">
        <thead><tr><th>Order Number</th><th>Customer</th><th>Date</th><th>Amount</th><th>Items</th></tr></thead>
        <tbody>';
    
    foreach ($data as $row) {
        $html .= '<tr>
            <td>' . $row['order_number'] . '</td>
            <td>' . $row['customer_name'] . '</td>
            <td>' . date('M j, Y', strtotime($row['created_at'])) . '</td>
            <td>₱' . number_format($row['total_amount'], 2) . '</td>
            <td>' . $row['item_count'] . '</td>
        </tr>';
    }
    
    $html .= '</tbody></table>';
    return $html;
}

function generateCompleteReport($data) {
    $html = '';
    
    if (isset($data['monthly_sales'])) {
        $html .= '<h3>Monthly Sales</h3><table class="data-table">
            <thead><tr><th>Month</th><th>Orders</th><th>Revenue</th></tr></thead>
            <tbody>';
        
        foreach ($data['monthly_sales'] as $row) {
            $html .= '<tr>
                <td>' . $row['month'] . '</td>
                <td>' . $row['orders'] . '</td>
                <td>₱' . number_format($row['revenue'], 2) . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table>';
    }
    
    if (isset($data['top_products'])) {
        $html .= '<h3>Top Products</h3><table class="data-table">
            <thead><tr><th>Product</th><th>Category</th><th>Sales</th><th>Revenue</th><th>Stock</th></tr></thead>
            <tbody>';
        
        foreach ($data['top_products'] as $row) {
            $html .= '<tr>
                <td>' . $row['product_name'] . '</td>
                <td>' . $row['category_name'] . '</td>
                <td>' . $row['total_sales'] . '</td>
                <td>₱' . number_format($row['total_revenue'], 2) . '</td>
                <td>' . $row['stock'] . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table>';
    }
    
    return $html;
}
