<?php
require_once 'vendor/autoload.php'; 
use Dompdf\Dompdf;
use Dompdf\Options;

$order_id = $_GET['order_id'] ?? '';
if (!$order_id) {
    http_response_code(404);
    include __DIR__ . '/public/view/error/404.php';
    exit;
}

$order = $orderProductModel->getByOrderNumber($order_id);

function formatCurrency($value) {
    return '₱' . number_format((float)$value, 2, '.', ',');
}

ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice <?= $order['order_number'] ?></title>
    <style>
        /* Modern CSS Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, 'DejaVu Sans';
            color: #333;

            padding: 30px;
            line-height: 1.6;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 30px;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .company-details {
            color: #7f8c8d;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .invoice-meta {
            text-align: right;
        }
        
        .invoice-title {
            font-size: 32px;
            font-weight: 700;
            color: #3498db;
            margin-bottom: 10px;
        }
        
        .invoice-number {
            font-size: 18px;
            color: #7f8c8d;
        }
        
        .invoice-date {
            font-size: 16px;
            color: #7f8c8d;
        }
        
        .address-section {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .billing-address, .shipping-address {
            flex: 1;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        
        .address-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .items-table th {
            background: #3498db;
            color: white;
            text-align: left;
            padding: 15px;
            font-weight: 600;
        }
        
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        .text-right {
            text-align: right;
        }
        
        .summary-section {
            background: #f1f9ff;
            border-radius: 8px;
            padding: 25px;
            margin-top: 30px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        
        .summary-label {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .summary-value {
            font-weight: 600;
            color: #3498db;
        }
        
        .total-row {
            border-top: 2px solid #3498db;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 18px;
        }
        
        .payment-info {
            display: flex;
            gap: 30px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .payment-method, .order-status {
            flex: 1;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        
        .info-title {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .info-value {
            font-size: 18px;
            font-weight: 700;
            color: #3498db;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }
        
        .status-pending {
            background: #f39c12;
            color: white;
        }
        
        .status-completed {
            background: #2ecc71;
            color: white;
        }
        
        .status-cancelled {
            background: #e74c3c;
            color: white;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            font-weight: 800;
            color: rgba(52, 152, 219, 0.08);
            pointer-events: none;
            z-index: -1;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="watermark"><?= strtoupper($order['status']) ?></div>
        
        <div class="invoice-header">
            <div class="company-info">
                <!-- <div class="company-name">ShopEase</div>
                <div class="company-details">
                    123 Business Avenue, Makati City<br>
                    Metro Manila, Philippines<br>
                    Phone: (02) 1234-5678 | Email: info@shopease.com
                </div> -->
            </div>
            
            <div class="invoice-meta">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">Order #: <?= $order['order_number'] ?></div>
                <div class="invoice-date">Date: <?= date('M d, Y', strtotime($order['created_at'])) ?></div>
            </div>
        </div>
        
        <div class="address-section">
            <div class="billing-address">
                <div class="address-title">Bill To:</div>
                <p><strong><?= $order['first_name'] ?> <?= $order['last_name'] ?></strong></p>
                <p><?= $order['user_email'] ?></p>
            </div>
            
            <div class="shipping-address">
                <div class="address-title">Ship To:</div>
                <p><?= $order['shipping_address']['address_line'] ?></p>
                <p><?= $order['shipping_address']['brgy'] ?>, <?= $order['shipping_address']['city'] ?></p>
                <p><?= $order['shipping_address']['region'] ?>, <?= $order['shipping_address']['postal_code'] ?></p>
            </div>
        </div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Unit Price</th>
                    <th>Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><?= $item['product_name'] ?></td>
                        <td><?= $item['product_sku'] ?></td>
                        <td><?= formatCurrency($item['price']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td class="text-right"><?= formatCurrency($item['price'] * $item['quantity']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="summary-section">
            <div class="summary-row">
                <div class="summary-label">Subtotal:</div>
                <div class="summary-value"><?= formatCurrency($order['subtotal']) ?></div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Shipping Fee:</div>
                <div class="summary-value"><?= formatCurrency($order['shipping_fee']) ?></div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Tax (12%):</div>
                <div class="summary-value"><?= formatCurrency($order['tax']) ?></div>
            </div>
            <div class="summary-row total-row">
                <div class="summary-label">Total Amount:</div>
                <div class="summary-value"><?= formatCurrency($order['total_amount']) ?></div>
            </div>
        </div>
        
        <div class="payment-info">
            <div class="payment-method">
                <div class="info-title">Payment Method</div>
                <div class="info-value"><?= strtoupper($order['payment']['payment_method']) ?></div>
            </div>
            <div class="tracking-info">
                <div class="info-title">Tracking Number</div>
                <div class="info-value"><?= $order['tracking_number'] ?></div>
            </div>
            <div class="order-status">
                <div class="info-title">Order Status</div>
                <div class="info-value">
                    <span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Thank you for your business! We appreciate your trust in our services.</p>
            <p>Invoice was generated on <?= date('M d, Y \a\t h:i A') ?></p>
            <p>For any inquiries, contact us at support@shopease.com or call (02) 1234-5678</p>
        </div>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("invoice_{$order['order_number']}.pdf", [
    'Attachment' => false
]);
?>