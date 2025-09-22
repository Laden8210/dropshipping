<?php

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


require_once '../../vendor/autoload.php';
require_once '../../core/config.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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

// Set up dompdf options
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);

try {
    // Generate report based on type and user role
    switch ($reportType) {
        case 'dashboard':
            generateDashboardReport($conn, $userRole, $userId, $dompdf, $dateFrom, $dateTo);
            break;
        case 'sales':
            generateSalesReport($conn, $userRole, $userId, $dompdf, $dateFrom, $dateTo);
            break;
        case 'inventory':
            generateInventoryReport($conn, $userRole, $userId, $dompdf, $dateFrom, $dateTo);
            break;
        case 'orders':
            generateOrdersReport($conn, $userRole, $userId, $dompdf, $dateFrom, $dateTo);
            break;
        default:
            generateDashboardReport($conn, $userRole, $userId, $dompdf, $dateFrom, $dateTo);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to generate PDF report: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}

function generateDashboardReport($conn, $userRole, $userId, $dompdf, $dateFrom, $dateTo) {
    $html = generateDashboardHTML($conn, $userRole, $userId, $dateFrom, $dateTo);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    $filename = strtolower($userRole) . '_dashboard_report_' . date('Y-m-d_H-i-s') . '.pdf';
    $dompdf->stream($filename, ['Attachment' => true]);
}

function generateSalesReport($conn, $userRole, $userId, $dompdf, $dateFrom, $dateTo) {
    $html = generateSalesHTML($conn, $userRole, $userId, $dateFrom, $dateTo);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    $filename = strtolower($userRole) . '_sales_report_' . date('Y-m-d_H-i-s') . '.pdf';
    $dompdf->stream($filename, ['Attachment' => true]);
}

function generateInventoryReport($conn, $userRole, $userId, $dompdf, $dateFrom, $dateTo) {
    $html = generateInventoryHTML($conn, $userRole, $userId, $dateFrom, $dateTo);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    $filename = strtolower($userRole) . '_inventory_report_' . date('Y-m-d_H-i-s') . '.pdf';
    $dompdf->stream($filename, ['Attachment' => true]);
}

function generateOrdersReport($conn, $userRole, $userId, $dompdf, $dateFrom, $dateTo) {
    $html = generateOrdersHTML($conn, $userRole, $userId, $dateFrom, $dateTo);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    $filename = strtolower($userRole) . '_orders_report_' . date('Y-m-d_H-i-s') . '.pdf';
    $dompdf->stream($filename, ['Attachment' => true]);
}

function generateDashboardHTML($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $title = ucfirst($userRole) . ' Dashboard Report';
    $date = date('F d, Y');
    
    // Get basic stats based on role
    $stats = getDashboardStats($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>$title</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; }
            .header h1 { color: #333; margin: 0; }
            .header p { color: #666; margin: 5px 0; }
            .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
            .stat-card { border: 1px solid #ddd; padding: 20px; border-radius: 8px; text-align: center; }
            .stat-value { font-size: 24px; font-weight: bold; color: #4361ee; margin: 10px 0; }
            .stat-label { color: #666; font-size: 14px; }
            .section { margin-bottom: 30px; }
            .section h2 { color: #333; border-bottom: 2px solid #4361ee; padding-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
            th { background-color: #f8f9fa; font-weight: bold; }
            .footer { margin-top: 50px; text-align: center; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>$title</h1>
            <p>Generated on $date</p>
            <p>Period: $dateFrom to $dateTo</p>
        </div>
        
        <div class='stats-grid'>
            <div class='stat-card'>
                <div class='stat-value'>PHP" . number_format($stats['revenue'], 2) . "</div>
                <div class='stat-label'>Total Revenue</div>
            </div>
            <div class='stat-card'>
                <div class='stat-value'>" . number_format($stats['orders']) . "</div>
                <div class='stat-label'>Total Orders</div>
            </div>
            <div class='stat-card'>
                <div class='stat-value'>" . number_format($stats['products']) . "</div>
                <div class='stat-label'>Products</div>
            </div>
            <div class='stat-card'>
                <div class='stat-value'>" . number_format($stats['customers']) . "</div>
                <div class='stat-label'>Customers</div>
            </div>
        </div>
        
        <div class='section'>
            <h2>Recent Activity</h2>
            <p>This report contains comprehensive analytics and performance metrics for your " . strtolower($userRole) . " account.</p>
        </div>
        
        <div class='footer'>
            <p>Report generated by Dropshipping Management System</p>
        </div>
    </body>
    </html>";
    
    return $html;
}

function generateSalesHTML($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $title = ucfirst($userRole) . ' Sales Report';
    $date = date('F d, Y');
    
    // Get sales data based on role
    $salesData = getSalesData($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>$title</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; }
            .header h1 { color: #333; margin: 0; }
            .header p { color: #666; margin: 5px 0; }
            .summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
            .summary-card { border: 1px solid #ddd; padding: 20px; border-radius: 8px; text-align: center; }
            .summary-value { font-size: 20px; font-weight: bold; color: #4361ee; margin: 10px 0; }
            .summary-label { color: #666; font-size: 14px; }
            .section { margin-bottom: 30px; }
            .section h2 { color: #333; border-bottom: 2px solid #4361ee; padding-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
            th { background-color: #f8f9fa; font-weight: bold; }
            .footer { margin-top: 50px; text-align: center; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>$title</h1>
            <p>Generated on $date</p>
            <p>Period: $dateFrom to $dateTo</p>
        </div>
        
        <div class='summary'>
            <div class='summary-card'>
                <div class='summary-value'>PHP" . number_format($salesData['total_revenue'], 2) . "</div>
                <div class='summary-label'>Total Revenue</div>
            </div>
            <div class='summary-card'>
                <div class='summary-value'>" . number_format($salesData['total_orders']) . "</div>
                <div class='summary-label'>Total Orders</div>
            </div>
            <div class='summary-card'>
                <div class='summary-value'>PHP" . number_format($salesData['avg_order_value'], 2) . "</div>
                <div class='summary-label'>Average Order Value</div>
            </div>
        </div>
        
        <div class='section'>
            <h2>Top Selling Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Quantity Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>";
    
    foreach ($salesData['top_products'] as $product) {
        $html .= "
                    <tr>
                        <td>" . htmlspecialchars($product['product_name']) . "</td>
                        <td>" . htmlspecialchars($product['category_name']) . "</td>
                        <td>" . number_format($product['total_sales']) . "</td>
                        <td>PHP" . number_format($product['total_revenue'], 2) . "</td>
                    </tr>";
    }
    
    $html .= "
                </tbody>
            </table>
        </div>
        
        <div class='footer'>
            <p>Report generated by Dropshipping Management System</p>
        </div>
    </body>
    </html>";
    
    return $html;
}

function generateInventoryHTML($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $title = ucfirst($userRole) . ' Inventory Report';
    $date = date('F d, Y');
    
    // Get inventory data based on role
    $inventoryData = getInventoryData($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>$title</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; }
            .header h1 { color: #333; margin: 0; }
            .header p { color: #666; margin: 5px 0; }
            .summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
            .summary-card { border: 1px solid #ddd; padding: 20px; border-radius: 8px; text-align: center; }
            .summary-value { font-size: 20px; font-weight: bold; color: #4361ee; margin: 10px 0; }
            .summary-label { color: #666; font-size: 14px; }
            .section { margin-bottom: 30px; }
            .section h2 { color: #333; border-bottom: 2px solid #4361ee; padding-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
            th { background-color: #f8f9fa; font-weight: bold; }
            .low-stock { background-color: #fff3cd; }
            .out-of-stock { background-color: #f8d7da; }
            .footer { margin-top: 50px; text-align: center; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>$title</h1>
            <p>Generated on $date</p>
            <p>Period: $dateFrom to $dateTo</p>
        </div>
        
        <div class='summary'>
            <div class='summary-card'>
                <div class='summary-value'>" . number_format($inventoryData['total_products']) . "</div>
                <div class='summary-label'>Total Products</div>
            </div>
            <div class='summary-card'>
                <div class='summary-value'>" . number_format($inventoryData['in_stock']) . "</div>
                <div class='summary-label'>In Stock</div>
            </div>
            <div class='summary-card'>
                <div class='summary-value'>" . number_format($inventoryData['low_stock']) . "</div>
                <div class='summary-label'>Low Stock</div>
            </div>
            <div class='summary-card'>
                <div class='summary-value'>" . number_format($inventoryData['out_of_stock']) . "</div>
                <div class='summary-label'>Out of Stock</div>
            </div>
        </div>
        
        <div class='section'>
            <h2>Inventory Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>SKU</th>
                        <th>Current Stock</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>";
    
    foreach ($inventoryData['products'] as $product) {
        $rowClass = '';
        if ($product['quantity'] == 0) {
            $rowClass = 'out-of-stock';
        } elseif ($product['quantity'] < 10) {
            $rowClass = 'low-stock';
        }
        
        $html .= "
                    <tr class='$rowClass'>
                        <td>" . htmlspecialchars($product['product_name']) . "</td>
                        <td>" . htmlspecialchars($product['product_sku']) . "</td>
                        <td>" . number_format($product['quantity']) . "</td>
                        <td>" . ($product['quantity'] == 0 ? 'Out of Stock' : ($product['quantity'] < 10 ? 'Low Stock' : 'In Stock')) . "</td>
                        <td>" . date('M d, Y', strtotime($product['updated_at'])) . "</td>
                    </tr>";
    }
    
    $html .= "
                </tbody>
            </table>
        </div>
        
        <div class='footer'>
            <p>Report generated by Dropshipping Management System</p>
        </div>
    </body>
    </html>";
    
    return $html;
}

function generateOrdersHTML($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $title = ucfirst($userRole) . ' Orders Report';
    $date = date('F d, Y');
    
    // Get orders data based on role
    $ordersData = getOrdersData($conn, $userRole, $userId, $dateFrom, $dateTo);
    
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>$title</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; }
            .header h1 { color: #333; margin: 0; }
            .header p { color: #666; margin: 5px 0; }
            .summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
            .summary-card { border: 1px solid #ddd; padding: 20px; border-radius: 8px; text-align: center; }
            .summary-value { font-size: 20px; font-weight: bold; color: #4361ee; margin: 10px 0; }
            .summary-label { color: #666; font-size: 14px; }
            .section { margin-bottom: 30px; }
            .section h2 { color: #333; border-bottom: 2px solid #4361ee; padding-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
            th { background-color: #f8f9fa; font-weight: bold; }
            .status-completed { color: #28a745; font-weight: bold; }
            .status-pending { color: #ffc107; font-weight: bold; }
            .status-cancelled { color: #dc3545; font-weight: bold; }
            .footer { margin-top: 50px; text-align: center; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>$title</h1>
            <p>Generated on $date</p>
            <p>Period: $dateFrom to $dateTo</p>
        </div>
        
        <div class='summary'>
            <div class='summary-card'>
                <div class='summary-value'>" . number_format($ordersData['total_orders']) . "</div>
                <div class='summary-label'>Total Orders</div>
            </div>
            <div class='summary-card'>
                <div class='summary-value'>PHP" . number_format($ordersData['total_revenue'], 2) . "</div>
                <div class='summary-label'>Total Revenue</div>
            </div>
            <div class='summary-card'>
                <div class='summary-value'>" . number_format($ordersData['completed_orders']) . "</div>
                <div class='summary-label'>Completed</div>
            </div>
            <div class='summary-card'>
                <div class='summary-value'>" . number_format($ordersData['pending_orders']) . "</div>
                <div class='summary-label'>Pending</div>
            </div>
        </div>
        
        <div class='section'>
            <h2>Recent Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>";
    
    foreach ($ordersData['recent_orders'] as $order) {
        $statusClass = 'status-' . strtolower($order['status']);
        $html .= "
                    <tr>
                        <td>" . htmlspecialchars($order['order_number']) . "</td>
                        <td>" . htmlspecialchars($order['customer_name']) . "</td>
                        <td>" . date('M d, Y', strtotime($order['created_at'])) . "</td>
                            <td>PHP" . number_format($order['total_amount'], 2) . "</td>
                        <td class='$statusClass'>" . ucfirst($order['status']) . "</td>
                    </tr>";
    }
    
    $html .= "
                </tbody>
            </table>
        </div>
        
        <div class='footer'>
            <p>Report generated by Dropshipping Management System</p>
        </div>
    </body>
    </html>";
    
    return $html;
}

function getDashboardStats($conn, $userRole, $userId, $dateFrom, $dateTo) {
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

function getSalesData($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $data = ['total_revenue' => 0, 'total_orders' => 0, 'avg_order_value' => 0, 'top_products' => []];
    
    // Implementation would be similar to getDashboardStats but focused on sales metrics
    $stats = getDashboardStats($conn, $userRole, $userId, $dateFrom, $dateTo);
    $data['total_revenue'] = $stats['revenue'];
    $data['total_orders'] = $stats['orders'];
    $data['avg_order_value'] = $stats['orders'] > 0 ? $stats['revenue'] / $stats['orders'] : 0;
    
    return $data;
}

function getInventoryData($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $data = ['total_products' => 0, 'in_stock' => 0, 'low_stock' => 0, 'out_of_stock' => 0, 'products' => []];
    
    // Implementation would query inventory tables based on user role
    $stats = getDashboardStats($conn, $userRole, $userId, $dateFrom, $dateTo);
    $data['total_products'] = $stats['products'];
    
    return $data;
}

function getOrdersData($conn, $userRole, $userId, $dateFrom, $dateTo) {
    $data = ['total_orders' => 0, 'total_revenue' => 0, 'completed_orders' => 0, 'pending_orders' => 0, 'recent_orders' => []];
    
    // Implementation would query orders tables based on user role
    $stats = getDashboardStats($conn, $userRole, $userId, $dateFrom, $dateTo);
    $data['total_orders'] = $stats['orders'];
    $data['total_revenue'] = $stats['revenue'];
    
    return $data;
}
