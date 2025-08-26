<?php
// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    require_once 'core/config.php';

    $action = $_POST['action'];
    $response = [];

    try {
        switch ($action) {
            case 'sales_analysis':
                $response = processSalesData($conn, $_POST);
                break;
            case 'inventory_status':
                $response = processInventoryData($conn, $_POST);
                break;
            case 'order_analysis':
                $response = processOrderData($conn, $_POST);
                break;
            case 'custom_report':
                $response = processCustomReport($conn, $_POST);
                break;
            default:
                $response = ['error' => 'Invalid action'];
        }
    } catch (Exception $e) {
        $response = ['error' => $e->getMessage()];
    }

    echo json_encode($response);
    exit;
}

// Sales Data Processing
function processSalesData($conn, $params)
{
    $type = $params['type'] ?? 'daily';
    $limit = $params['limit'] ?? 10;

    switch ($type) {
        case 'daily':
            $sql = "SELECT 
                        DATE(o.created_at) as sale_date,
                        COUNT(o.order_id) as total_orders,
                        SUM(o.total_amount) as total_sales
                    FROM orders o 
                    WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY DATE(o.created_at) 
                    ORDER BY sale_date DESC";
            if ($limit != 'all') $sql .= " LIMIT $limit";
            break;

        case 'top_products':
            $sql = "SELECT 
                        p.product_name,
                        SUM(oi.quantity) as total_sold,
                        SUM(oi.price * oi.quantity) as total_revenue
                    FROM order_items oi
                    JOIN imported_product ip ON oi.product_id = ip.imported_product_id
                    JOIN products p ON ip.product_id = p.product_id
                    GROUP BY p.product_id, p.product_name
                    ORDER BY total_revenue DESC";
            if ($limit != 'all') $sql .= " LIMIT $limit";
            break;

        case 'category_sales':
            $sql = "SELECT 
                        pc.category_name,
                        COUNT(DISTINCT o.order_id) as total_orders,
                        SUM(o.total_amount) as total_sales
                    FROM orders o
                    JOIN order_items oi ON o.order_id = oi.order_id
                    JOIN imported_product ip ON oi.product_id = ip.imported_product_id
                    JOIN products p ON ip.product_id = p.product_id
                    JOIN product_categories pc ON p.product_category = pc.category_id
                    GROUP BY pc.category_id, pc.category_name
                    ORDER BY total_sales DESC";
            if ($limit != 'all') $sql .= " LIMIT $limit";
            break;

        default:
            $sql = "SELECT DATE(created_at) as date, COUNT(*) as orders FROM orders GROUP BY DATE(created_at) LIMIT 10";
    }

    $result = $conn->query($sql);
    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    $response = [
        'report_type' => 'sales_analysis',
        'analysis_type' => $type,
        'generated_at' => date('Y-m-d H:i:s'),
        'data' => $data,
        'summary' => calculateSalesSummary($data, $type)
    ];

    // Save to JSON file
    saveToFile($response, "sales_{$type}_" . date('Y-m-d_H-i-s') . ".json");

    return $response;
}

// Inventory Data Processing
function processInventoryData($conn, $params)
{
    $type = $params['type'] ?? 'low_stock';
    $threshold = intval($params['threshold'] ?? 10);

    switch ($type) {
        case 'low_stock':
            $sql = "SELECT 
                        p.product_name,
                        p.product_sku,
                        COALESCE(i.quantity, 0) as current_stock,
                        $threshold as threshold,
                        CASE 
                            WHEN COALESCE(i.quantity, 0) = 0 THEN 'out_of_stock'
                            WHEN COALESCE(i.quantity, 0) <= 5 THEN 'critical'
                            WHEN COALESCE(i.quantity, 0) <= $threshold THEN 'low'
                            ELSE 'normal'
                        END as status
                    FROM products p
                    LEFT JOIN inventory i ON p.product_id = i.product_id
                    WHERE COALESCE(i.quantity, 0) <= $threshold
                    ORDER BY current_stock ASC";
            break;

        case 'out_of_stock':
            $sql = "SELECT 
                        p.product_name,
                        p.product_sku,
                        COALESCE(i.quantity, 0) as current_stock,
                        p.updated_at as last_updated
                    FROM products p
                    LEFT JOIN inventory i ON p.product_id = i.product_id
                    WHERE COALESCE(i.quantity, 0) = 0";
            break;

        case 'all_inventory':
            $sql = "SELECT 
                        p.product_name,
                        p.product_sku,
                        COALESCE(i.quantity, 0) as current_stock,
                        pc.category_name,
                        p.status
                    FROM products p
                    LEFT JOIN inventory i ON p.product_id = i.product_id
                    LEFT JOIN product_categories pc ON p.product_category = pc.category_id
                    ORDER BY p.product_name";
            break;
    }

    $result = $conn->query($sql);
    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    $response = [
        'report_type' => 'inventory_analysis',
        'analysis_type' => $type,
        'generated_at' => date('Y-m-d H:i:s'),
        'parameters' => ['threshold' => $threshold],
        'data' => $data,
        'summary' => calculateInventorySummary($data, $type)
    ];

    // Save to JSON file
    saveToFile($response, "inventory_{$type}_" . date('Y-m-d_H-i-s') . ".json");

    return $response;
}

// Order Data Processing
function processOrderData($conn, $params)
{
    $type = $params['type'] ?? 'status_summary';
    $days = intval($params['days'] ?? 30);

    switch ($type) {
        case 'status_summary':
            $sql = "SELECT 
                        osh.status,
                        COUNT(DISTINCT o.order_id) as order_count,
                        SUM(o.total_amount) as total_amount,
                        ROUND(COUNT(DISTINCT o.order_id) * 100.0 / (
                            SELECT COUNT(*) FROM orders 
                            WHERE created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)
                        ), 2) as percentage
                    FROM orders o
                    JOIN order_status_history osh ON o.order_id = osh.order_id
                    WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)
                    AND osh.created_at = (
                        SELECT MAX(created_at) 
                        FROM order_status_history 
                        WHERE order_id = o.order_id
                    )
                    GROUP BY osh.status
                    ORDER BY order_count DESC";
            break;

        case 'recent_orders':
            $sql = "SELECT 
                        o.order_number,
                        CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                        o.total_amount,
                        o.created_at,
                        osh.status
                    FROM orders o
                    JOIN users u ON o.user_id = u.user_id
                    LEFT JOIN order_status_history osh ON o.order_id = osh.order_id
                    WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)
                    AND (osh.created_at = (
                        SELECT MAX(created_at) 
                        FROM order_status_history 
                        WHERE order_id = o.order_id
                    ) OR osh.created_at IS NULL)
                    ORDER BY o.created_at DESC
                    LIMIT 50";
            break;

        case 'customer_orders':
            $sql = "SELECT 
                        CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                        u.email,
                        COUNT(o.order_id) as total_orders,
                        SUM(o.total_amount) as total_spent,
                        MAX(o.created_at) as last_order_date
                    FROM orders o
                    JOIN users u ON o.user_id = u.user_id
                    WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)
                    GROUP BY o.user_id, u.first_name, u.last_name, u.email
                    ORDER BY total_spent DESC
                    LIMIT 25";
            break;
    }

    $result = $conn->query($sql);
    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    $response = [
        'report_type' => 'order_analysis',
        'analysis_type' => $type,
        'generated_at' => date('Y-m-d H:i:s'),
        'parameters' => ['days' => $days],
        'data' => $data,
        'summary' => calculateOrderSummary($data, $type)
    ];

    // Save to JSON file
    saveToFile($response, "orders_{$type}_" . date('Y-m-d_H-i-s') . ".json");

    return $response;
}

// Custom Report Processing
function processCustomReport($conn, $params)
{
    $reportType = $params['report_type'] ?? 'user_activity';

    switch ($reportType) {
        case 'user_activity':
            $sql = "SELECT 
                        u.user_id,
                        CONCAT(u.first_name, ' ', u.last_name) as full_name,
                        u.email,
                        COUNT(o.order_id) as total_orders,
                        COALESCE(SUM(o.total_amount), 0) as total_spent,
                        MAX(o.created_at) as last_order_date,
                        u.created_at as registration_date
                    FROM users u
                    LEFT JOIN orders o ON u.user_id = o.user_id
                    GROUP BY u.user_id
                    ORDER BY total_spent DESC";
            break;

        case 'store_performance':
            $sql = "SELECT 
                        sp.store_name,
                        sp.store_email,
                        COUNT(o.order_id) as total_orders,
                        COALESCE(SUM(o.total_amount), 0) as total_revenue,
                        COUNT(DISTINCT p.product_id) as total_products,
                        sp.status as store_status
                    FROM store_profile sp
                    LEFT JOIN orders o ON sp.store_id = o.store_id
                    LEFT JOIN imported_product ip ON sp.store_id = ip.store_id
                    LEFT JOIN products p ON ip.product_id = p.product_id
                    GROUP BY sp.store_id
                    ORDER BY total_revenue DESC";
            break;

        case 'feedback_analysis':
            $sql = "SELECT 
                        of.rating,
                        COUNT(*) as rating_count,
                        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM order_feedback), 2) as percentage
                    FROM order_feedback of
                    GROUP BY of.rating
                    ORDER BY of.rating DESC";
            break;
    }

    $result = $conn->query($sql);
    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    $response = [
        'report_type' => 'custom_report',
        'report_name' => $reportType,
        'generated_at' => date('Y-m-d H:i:s'),
        'data' => $data,
        'summary' => ['total_records' => count($data)]
    ];

    // Save to JSON file
    saveToFile($response, "custom_{$reportType}_" . date('Y-m-d_H-i-s') . ".json");

    return $response;
}

// Helper Functions
function calculateSalesSummary($data, $type)
{
    if (empty($data)) return ['total_records' => 0];

    $totalRevenue = 0;
    $totalOrders = 0;

    foreach ($data as $row) {
        $totalRevenue += floatval($row['total_sales'] ?? $row['total_revenue'] ?? 0);
        $totalOrders += intval($row['total_orders'] ?? $row['total_sold'] ?? 1);
    }

    return [
        'total_records' => count($data),
        'total_revenue' => number_format($totalRevenue, 2),
        'total_orders' => $totalOrders,
        'average_per_record' => count($data) ? number_format($totalRevenue / count($data), 2) : '0.00'
    ];
}

function calculateInventorySummary($data, $type)
{
    if (empty($data)) return ['total_records' => 0];

    $critical = 0;
    $low = 0;
    $outOfStock = 0;

    foreach ($data as $row) {
        $stock = intval($row['current_stock'] ?? 0);
        if ($stock == 0) $outOfStock++;
        elseif ($stock <= 5) $critical++;
        elseif ($stock <= 10) $low++;
    }

    return [
        'total_records' => count($data),
        'out_of_stock' => $outOfStock,
        'critical_stock' => $critical,
        'low_stock' => $low
    ];
}

function calculateOrderSummary($data, $type)
{
    if (empty($data)) return ['total_records' => 0];

    $summary = ['total_records' => count($data)];

    if ($type === 'customer_orders') {
        $totalSpent = array_sum(array_column($data, 'total_spent'));
        $totalOrders = array_sum(array_column($data, 'total_orders'));
        $summary['total_revenue'] = number_format($totalSpent, 2);
        $summary['total_orders'] = $totalOrders;
    }

    return $summary;
}

function saveToFile($data, $filename)
{
    $exportPath = __DIR__ . '/exports/';
    if (!file_exists($exportPath)) {
        mkdir($exportPath, 0777, true);
    }

    file_put_contents($exportPath . $filename, json_encode($data, JSON_PRETTY_PRINT));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Data Processing System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {

            color: black;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .content {
            padding: 30px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background: #fafafa;
        }

        .card h3 {
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        .btn:hover {
            background: #5a6fd8;
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .results {
            margin-top: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            display: none;
        }

        .results h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .json-output {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            margin-bottom: 15px;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
        }

        .summary-item {
            background: white;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
            border-left: 4px solid #667eea;
        }

        .summary-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }

        .summary-label {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .download-btn {
            background: #28a745;
            margin-left: 10px;
        }

        .download-btn:hover {
            background: #218838;
        }

        .alert {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Assessment Dropshipping</h1>
            <p>Process and export your database data to JSON format</p>
        </div>

        <div class="content">
            <div class="grid">
                <!-- Sales Analysis -->
                <div class="card">
                    <h3>📈 Sales Analysis</h3>
                    <form id="salesForm">
                        <div class="form-group">
                            <label>Analysis Type:</label>
                            <select name="type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="daily">Daily Sales</option>
                                <option value="top_products">Top Products</option>
                                <option value="category_sales">Sales by Category</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Limit Results:</label>
                            <select name="limit" class="form-control">
                                <option value="10">Top 10</option>
                                <option value="25">Top 25</option>
                                <option value="all">All Results</option>
                            </select>
                        </div>
                        <button type="submit" class="btn">Generate Report</button>
                    </form>
                </div>

                <!-- Inventory Status -->
                <div class="card">
                    <h3>📦 Inventory Status</h3>
                    <form id="inventoryForm">
                        <div class="form-group">
                            <label>Report Type:</label>
                            <select name="type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="low_stock">Low Stock Items</option>
                                <option value="out_of_stock">Out of Stock</option>
                                <option value="all_inventory">Complete Inventory</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Low Stock Threshold:</label>
                            <input type="number" name="threshold" class="form-control" value="10" min="1">
                        </div>
                        <button type="submit" class="btn">Check Inventory</button>
                    </form>
                </div>

                <!-- Order Analysis -->
                <div class="card">
                    <h3>🛒 Order Analysis</h3>
                    <form id="orderForm">
                        <div class="form-group">
                            <label>Analysis Type:</label>
                            <select name="type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="status_summary">Orders by Status</option>
                                <option value="recent_orders">Recent Orders</option>
                                <option value="customer_orders">Top Customers</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Time Period:</label>
                            <select name="days" class="form-control">
                                <option value="7">Last 7 Days</option>
                                <option value="30">Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                            </select>
                        </div>
                        <button type="submit" class="btn">Analyze Orders</button>
                    </form>
                </div>

                <!-- Custom Reports -->
                <div class="card">
                    <h3>📋 Custom Reports</h3>
                    <form id="customForm">
                        <div class="form-group">
                            <label>Report Type:</label>
                            <select name="report_type" class="form-control" required>
                                <option value="">Select Report</option>
                                <option value="user_activity">User Activity</option>
                                <option value="store_performance">Store Performance</option>
                                <option value="feedback_analysis">Customer Feedback</option>
                            </select>
                        </div>
                        <button type="submit" class="btn">Generate Report</button>
                    </form>
                </div>
            </div>

            <!-- Results Section -->
            <div id="results" class="results">
                <div class="loading" id="loading">
                    ⏳ Processing data...
                </div>
                <div id="output" style="display: none;">
                    <h3>Processing Results
                        <button id="downloadBtn" class="btn download-btn">💾 Download JSON</button>
                    </h3>
                    <div id="summary" class="summary"></div>
                    <pre id="jsonOutput" class="json-output"></pre>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentData = null;
        let currentFileName = '';

        // Form handlers
        document.getElementById('salesForm').addEventListener('submit', function(e) {
            e.preventDefault();
            processForm(this, 'sales_analysis');
        });

        document.getElementById('inventoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            processForm(this, 'inventory_status');
        });

        document.getElementById('orderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            processForm(this, 'order_analysis');
        });

        document.getElementById('customForm').addEventListener('submit', function(e) {
            e.preventDefault();
            processForm(this, 'custom_report');
        });

        function processForm(form, action) {
            const formData = new FormData(form);
            formData.append('action', action);

            showLoading();

            fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.error) {
                        showAlert(data.error, 'error');
                    } else {
                        displayResults(data);
                        showAlert('Report generated successfully!', 'success');
                    }
                })
                .catch(error => {
                    hideLoading();
                    showAlert('Error: ' + error.message, 'error');
                });
        }

        function displayResults(data) {
            currentData = data;
            currentFileName = `${data.report_type}_${data.analysis_type || data.report_name}_${new Date().toISOString().split('T')[0]}.json`;

            // Display summary
            const summaryDiv = document.getElementById('summary');
            summaryDiv.innerHTML = '';

            if (data.summary) {
                Object.entries(data.summary).forEach(([key, value]) => {
                    const summaryItem = document.createElement('div');
                    summaryItem.className = 'summary-item';
                    summaryItem.innerHTML = `
                        <div class="summary-number">${value}</div>
                        <div class="summary-label">${key.replace('_', ' ').toUpperCase()}</div>
                    `;
                    summaryDiv.appendChild(summaryItem);
                });
            }

            // Display JSON
            document.getElementById('jsonOutput').textContent = JSON.stringify(data, null, 2);

            // Show results
            document.getElementById('results').style.display = 'block';
            document.getElementById('output').style.display = 'block';

            // Scroll to results
            document.getElementById('results').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function showLoading() {
            document.getElementById('results').style.display = 'block';
            document.getElementById('loading').style.display = 'block';
            document.getElementById('output').style.display = 'none';
        }

        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;

            document.querySelector('.content').insertBefore(alertDiv, document.querySelector('.grid'));

            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Download functionality
        document.getElementById('downloadBtn').addEventListener('click', function() {
            if (currentData) {
                const jsonStr = JSON.stringify(currentData, null, 2);
                const blob = new Blob([jsonStr], {
                    type: 'application/json'
                });
                const url = URL.createObjectURL(blob);

                const a = document.createElement('a');
                a.href = url;
                a.download = currentFileName;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }
        });
    </script>
</body>

</html>