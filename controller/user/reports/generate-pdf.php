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
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$dateRange = $_GET['date_range'] ?? 'this_month'; // Added missing date_range variable

// Validate dates
if (!strtotime($startDate) || !strtotime($endDate)) {
    http_response_code(400);
    echo 'Invalid date format';
    exit;
}

// Get store information
$storeQuery = "SELECT store_name, store_description FROM store_profile WHERE store_id = ?";
$stmt = $conn->prepare($storeQuery);
$stmt->bind_param("i", $storeId);
$stmt->execute();
$storeResult = $stmt->get_result();
$storeInfo = $storeResult->fetch_assoc();

if (!$storeInfo) {
    http_response_code(404);
    echo 'Store not found';
    exit;
}

// Get report data based on type
$reportData = [];
$reportTitle = '';

try {
    switch ($reportType) {
        case 'sales':
            $reportTitle = 'Sales Report';
            $salesQuery = "SELECT DATE_FORMAT(o.created_at, '%Y-%m-%d') as date, 
                          COUNT(*) as orders, SUM(o.total_amount) as revenue
                          FROM orders o 
                          WHERE o.store_id = ? AND o.created_at BETWEEN ? AND ?
                          GROUP BY DATE_FORMAT(o.created_at, '%Y-%m-%d')
                          ORDER BY date DESC";
            $stmt = $conn->prepare($salesQuery);
            $stmt->bind_param("iss", $storeId, $startDate, $endDate);
            $stmt->execute();
            $salesResult = $stmt->get_result();
            $reportData = [];
            while ($row = $salesResult->fetch_assoc()) {
                $reportData[] = $row;
            }
            break;

        case 'revenue':
            $reportTitle = 'Revenue Report';
            
            // Get daily revenue data
            $dailyRevenueQuery = "SELECT DATE_FORMAT(o.created_at, '%Y-%m-%d') as date, 
                                 COUNT(*) as orders, 
                                 SUM(o.total_amount) as revenue,
                                 SUM(o.subtotal) as subtotal,
                                 SUM(o.shipping_fee) as shipping_fee,
                                 SUM(o.tax) as tax
                                 FROM orders o 
                                 WHERE o.store_id = ? AND o.created_at BETWEEN ? AND ?
                                 GROUP BY DATE_FORMAT(o.created_at, '%Y-%m-%d')
                                 ORDER BY date DESC";
            $stmt = $conn->prepare($dailyRevenueQuery);
            $stmt->bind_param("iss", $storeId, $startDate, $endDate);
            $stmt->execute();
            $dailyResult = $stmt->get_result();
            $dailyRevenue = [];
            while ($row = $dailyResult->fetch_assoc()) {
                $dailyRevenue[] = $row;
            }
            
            // Get monthly revenue data
            $monthlyRevenueQuery = "SELECT DATE_FORMAT(o.created_at, '%Y-%m') as month, 
                                   COUNT(*) as orders, 
                                   SUM(o.total_amount) as revenue,
                                   SUM(o.subtotal) as subtotal,
                                   SUM(o.shipping_fee) as shipping_fee,
                                   SUM(o.tax) as tax,
                                   AVG(o.total_amount) as avg_order_value
                                   FROM orders o 
                                   WHERE o.store_id = ? AND o.created_at BETWEEN ? AND ?
                                   GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
                                   ORDER BY month DESC";
            $stmt = $conn->prepare($monthlyRevenueQuery);
            $stmt->bind_param("iss", $storeId, $startDate, $endDate);
            $stmt->execute();
            $monthlyResult = $stmt->get_result();
            $monthlyRevenue = [];
            while ($row = $monthlyResult->fetch_assoc()) {
                $monthlyRevenue[] = $row;
            }
            
            // Get revenue by payment method
            $paymentRevenueQuery = "SELECT op.payment_method, 
                                   COUNT(*) as orders, 
                                   SUM(op.amount) as revenue
                                   FROM order_payments op
                                   JOIN orders o ON op.order_id = o.order_id
                                   WHERE o.store_id = ? AND o.created_at BETWEEN ? AND ?
                                   GROUP BY op.payment_method
                                   ORDER BY revenue DESC";
            $stmt = $conn->prepare($paymentRevenueQuery);
            $stmt->bind_param("iss", $storeId, $startDate, $endDate);
            $stmt->execute();
            $paymentResult = $stmt->get_result();
            $paymentRevenue = [];
            while ($row = $paymentResult->fetch_assoc()) {
                $paymentRevenue[] = $row;
            }
            
            // Get total revenue summary
            $totalRevenueQuery = "SELECT 
                                 COUNT(*) as total_orders,
                                 SUM(o.total_amount) as total_revenue,
                                 SUM(o.subtotal) as total_subtotal,
                                 SUM(o.shipping_fee) as total_shipping,
                                 SUM(o.tax) as total_tax,
                                 AVG(o.total_amount) as avg_order_value,
                                 MIN(o.total_amount) as min_order,
                                 MAX(o.total_amount) as max_order
                                 FROM orders o 
                                 WHERE o.store_id = ? AND o.created_at BETWEEN ? AND ?";
            $stmt = $conn->prepare($totalRevenueQuery);
            $stmt->bind_param("iss", $storeId, $startDate, $endDate);
            $stmt->execute();
            $totalResult = $stmt->get_result();
            $totalSummary = $totalResult->fetch_assoc();
            
            $reportData = [
                'daily_revenue' => $dailyRevenue,
                'monthly_revenue' => $monthlyRevenue,
                'payment_revenue' => $paymentRevenue,
                'summary' => $totalSummary
            ];
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
                             WHERE o.store_id = ? AND o.created_at BETWEEN ? AND ?
                             GROUP BY p.product_id, p.product_name, pc.category_name
                             ORDER BY total_revenue DESC";
            $stmt = $conn->prepare($productsQuery);
            $stmt->bind_param("iss", $storeId, $startDate, $endDate);
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
                            WHERE o.store_id = ? AND o.created_at BETWEEN ? AND ?
                            GROUP BY o.order_id
                            ORDER BY o.created_at DESC";
            $stmt = $conn->prepare($ordersQuery);
            $stmt->bind_param("iss", $storeId, $startDate, $endDate);
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

            $revenueQuery = "SELECT SUM(total_amount) as total_revenue FROM orders o WHERE o.store_id = ? AND o.created_at BETWEEN ? AND ?";
            $stmt = $conn->prepare($revenueQuery);
            $stmt->bind_param("iss", $storeId, $startDate, $endDate);
            $stmt->execute();
            $revenueResult = $stmt->get_result();
            $totalRevenue = $revenueResult->fetch_assoc()['total_revenue'] ?? 0;

            $ordersQuery = "SELECT COUNT(*) as total_orders FROM orders o WHERE o.store_id = ? AND o.created_at BETWEEN ? AND ?";
            $stmt = $conn->prepare($ordersQuery);
            $stmt->bind_param("iss", $storeId, $startDate, $endDate);
            $stmt->execute();
            $ordersResult = $stmt->get_result();
            $totalOrders = $ordersResult->fetch_assoc()['total_orders'] ?? 0;

            // Get additional complete report data
            $monthlySalesQuery = "SELECT DATE_FORMAT(o.created_at, '%Y-%m') as month, 
                                 COUNT(*) as orders, 
                                 SUM(o.total_amount) as revenue
                                 FROM orders o 
                                 WHERE o.store_id = ? AND o.created_at BETWEEN ? AND ?
                                 GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
                                 ORDER BY month DESC
                                 LIMIT 6";
            $stmt = $conn->prepare($monthlySalesQuery);
            $stmt->bind_param("iss", $storeId, $startDate, $endDate);
            $stmt->execute();
            $monthlySalesResult = $stmt->get_result();
            $monthlySales = [];
            while ($row = $monthlySalesResult->fetch_assoc()) {
                $monthlySales[] = $row;
            }

            $topProductsQuery = "SELECT p.product_name, pc.category_name, 
                                SUM(oi.quantity) as total_sales, 
                                SUM(oi.price * oi.quantity) as total_revenue,
                                (SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id) as stock
                                FROM order_items oi
                                JOIN products p ON oi.product_id = p.product_id
                                JOIN product_categories pc ON p.product_category = pc.category_id
                                JOIN orders o ON oi.order_id = o.order_id
                                WHERE o.store_id = ? AND o.created_at BETWEEN ? AND ?
                                GROUP BY p.product_id, p.product_name, pc.category_name
                                ORDER BY total_revenue DESC
                                LIMIT 10";
            $stmt = $conn->prepare($topProductsQuery);
            $stmt->bind_param("iss", $storeId, $startDate, $endDate);
            $stmt->execute();
            $topProductsResult = $stmt->get_result();
            $topProducts = [];
            while ($row = $topProductsResult->fetch_assoc()) {
                $topProducts[] = $row;
            }

            $reportData = [
                'summary' => [
                    'total_revenue' => $totalRevenue,
                    'total_orders' => $totalOrders,
                    'total_products' => $totalProducts,
                    'conversion_rate' => $totalProducts > 0 ? round(($totalOrders / $totalProducts) * 100, 1) : 0
                ],
                'monthly_sales' => $monthlySales,
                'top_products' => $topProducts
            ];
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error generating report: ' . $e->getMessage();
    exit;
}

// Generate PDF using dompdf
$dompdf = new \Dompdf\Dompdf();
$dompdf->setPaper('A4', 'portrait');

// Generate HTML content
$html = generateReportHTML($reportTitle, $storeInfo, $reportData, $startDate, $endDate, $reportType);

$dompdf->loadHtml($html);
$dompdf->render();

// Output PDF
$filename = strtolower(str_replace(' ', '_', $reportTitle)) . '_' . date('Y-m-d') . '.pdf';
$dompdf->stream($filename, ['Attachment' => 1]);

function generateReportHTML($title, $storeInfo, $reportData, $startDate, $endDate, $reportType) {
    $dateRangeText = date('M j, Y', strtotime($startDate)) . ' to ' . date('M j, Y', strtotime($endDate));
    $currentDate = date('F j, Y');
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>' . $title . '</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
            .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px; }
            .header h1 { color: #333; margin: 0; font-size: 24px; }
            .header h2 { color: #666; margin: 5px 0; font-size: 16px; }
            .report-info { margin-bottom: 20px; }
            .report-info table { width: 100%; border-collapse: collapse; }
            .report-info td { padding: 4px; border: none; }
            .report-info td:first-child { font-weight: bold; width: 120px; }
            .summary { margin-bottom: 20px; }
            .summary h3 { color: #333; border-bottom: 1px solid #ccc; padding-bottom: 4px; font-size: 14px; }
            .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 15px; }
            .summary-card { background: #f8f9fa; padding: 12px; border-radius: 4px; text-align: center; }
            .summary-card h4 { margin: 0 0 8px 0; color: #666; font-size: 11px; }
            .summary-card .value { font-size: 18px; font-weight: bold; color: #333; }
            .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10px; }
            .data-table th, .data-table td { border: 1px solid #ddd; padding: 6px; text-align: left; }
            .data-table th { background-color: #f2f2f2; font-weight: bold; }
            .data-table tr:nth-child(even) { background-color: #f9f9f9; }
            .footer { margin-top: 30px; text-align: center; color: #666; font-size: 10px; }
            .section-title { background: #e9ecef; padding: 8px; margin: 15px 0 10px 0; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>' . $title . '</h1>
            <h2>' . htmlspecialchars($storeInfo['store_name'] ?? 'Store') . '</h2>
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
        case 'revenue':
            $html .= generateRevenueReport($reportData);
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

function generateRevenueReport($data) {
    $html = '';
    
    // Revenue Summary
    if (isset($data['summary'])) {
        $summary = $data['summary'];
        $html .= '<h3>Revenue Summary</h3>
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
                <h4>Average Order Value</h4>
                <div class="value">₱' . number_format($summary['avg_order_value'], 2) . '</div>
            </div>
            <div class="summary-card">
                <h4>Highest Order</h4>
                <div class="value">₱' . number_format($summary['max_order'], 2) . '</div>
            </div>
        </div>';
    }
    
    // Daily Revenue Table
    if (isset($data['daily_revenue']) && !empty($data['daily_revenue'])) {
        $html .= '<h3>Daily Revenue Breakdown</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Orders</th>
                    <th>Subtotal</th>
                    <th>Shipping</th>
                    <th>Tax</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($data['daily_revenue'] as $row) {
            $html .= '<tr>
                <td>' . date('M j, Y', strtotime($row['date'])) . '</td>
                <td>' . $row['orders'] . '</td>
                <td>₱' . number_format($row['subtotal'], 2) . '</td>
                <td>₱' . number_format($row['shipping_fee'], 2) . '</td>
                <td>₱' . number_format($row['tax'], 2) . '</td>
                <td>₱' . number_format($row['revenue'], 2) . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table>';
    }
    
    // Monthly Revenue Table
    if (isset($data['monthly_revenue']) && !empty($data['monthly_revenue'])) {
        $html .= '<h3>Monthly Revenue Summary</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Orders</th>
                    <th>Subtotal</th>
                    <th>Shipping</th>
                    <th>Tax</th>
                    <th>Total Revenue</th>
                    <th>Avg Order Value</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($data['monthly_revenue'] as $row) {
            $html .= '<tr>
                <td>' . date('F Y', strtotime($row['month'] . '-01')) . '</td>
                <td>' . $row['orders'] . '</td>
                <td>₱' . number_format($row['subtotal'], 2) . '</td>
                <td>₱' . number_format($row['shipping_fee'], 2) . '</td>
                <td>₱' . number_format($row['tax'], 2) . '</td>
                <td>₱' . number_format($row['revenue'], 2) . '</td>
                <td>₱' . number_format($row['avg_order_value'], 2) . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table>';
    }
    
    // Payment Method Revenue Table
    if (isset($data['payment_revenue']) && !empty($data['payment_revenue'])) {
        $html .= '<h3>Revenue by Payment Method</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>';
        
        $totalRevenue = 0;
        foreach ($data['payment_revenue'] as $row) {
            $totalRevenue += $row['revenue'];
        }
        
        foreach ($data['payment_revenue'] as $row) {
            $percentage = $totalRevenue > 0 ? round(($row['revenue'] / $totalRevenue) * 100, 1) : 0;
            $html .= '<tr>
                <td>' . ucfirst($row['payment_method']) . '</td>
                <td>' . $row['orders'] . '</td>
                <td>₱' . number_format($row['revenue'], 2) . '</td>
                <td>' . $percentage . '%</td>
            </tr>';
        }
        
        $html .= '</tbody></table>';
    }
    
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
?>