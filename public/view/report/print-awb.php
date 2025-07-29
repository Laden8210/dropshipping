<?php
require_once 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;
use Picqer\Barcode\BarcodeGeneratorPNG;

// Database configuration
$host = 'localhost';
$dbname = 'your_database_name';
$username = 'your_username';
$password = 'your_password';

// Get tracking number from URL parameter
$tracking_number = $_GET['tracking_number'] ?? '';
if (!$tracking_number) {
    http_response_code(404);
    include __DIR__ . '/public/view/error/404.php';
    exit;
}

try {

    $order = $orderProductModel->printAWB($tracking_number);

    $generator = new BarcodeGeneratorPNG();
    $barcode = $generator->getBarcode($order['tracking_number'], $generator::TYPE_CODE_128, 1, 40);
    $barcodeBase64 = base64_encode($barcode);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>AWB <?= htmlspecialchars($order['tracking_number']) ?></title>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-size: 9pt;
            line-height: 1.2;
        }
        
        body { 
            font-family: Arial, sans-serif; 
            color: #333; 
        }
        
        .awb-container {
            width: 150mm;
            min-height: 100mm;
            padding: 5mm;
            border: 1px solid #000;
            position: relative;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3mm;
            padding-bottom: 2mm;
            border-bottom: 1px solid #000;
        }
        
        .header h1 {
            font-size: 12pt;
            color: #d40000;
        }
        
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3mm;
            margin-bottom: 3mm;
        }
        
        .address-box {
            border: 1px solid #000;
            padding: 2mm;
            min-height: 20mm;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 8pt;
            margin-bottom: 1mm;
            background: #f0f0f0;
            padding: 1mm;
        }
        
        .barcode-container {
            text-align: center;
            margin: 2mm 0;
        }
        
        .barcode-number {
            font-size: 10pt;
            letter-spacing: 1px;
            margin-top: 1mm;
            font-weight: bold;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 2mm;
            margin-top: 2mm;
        }
        
        .detail-box {
            border: 1px solid #000;
            padding: 2mm;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
            font-size: 8pt;
        }
        
        .items-table th, 
        .items-table td {
            border: 1px solid #000;
            padding: 1mm;
        }
        
        .items-table th {
            background-color: #f0f0f0;
        }
        
        .special-handling {
            margin-top: 2mm;
            padding-top: 2mm;
            border-top: 1px dashed #000;
            font-size: 8pt;
        }
        
        .footer {
            margin-top: 3mm;
            text-align: center;
            font-size: 7pt;
            padding-top: 2mm;
            border-top: 1px dashed #000;
        }
    </style>
</head>
<body>
    <div class="awb-container">
        <div class="header">
            <h1>SHIPPING LABEL</h1>
            <div>
                <strong>Service:</strong> <?= htmlspecialchars($order['service_type']) ?><br>
                <strong>Ref:</strong> <?= htmlspecialchars($order['order_number']) ?>
            </div>
        </div>
        
        <div class="info-section">
            <div>
                <div class="section-title">SHIPPER</div>
                <div class="address-box">
                    <strong><?= htmlspecialchars($order['store_profile']['store_name']) ?></strong><br>
                    <?= htmlspecialchars($order['store_profile']['store_address']) ?><br>
                    <?= htmlspecialchars($order['store_profile']['store_phone']) ?>
                </div>
            </div>
            
            <div>
                <div class="section-title">CONSIGNEE</div>
                <div class="address-box">
                    <strong><?= htmlspecialchars($order['shipping_address']['first_name']) ?> <?= htmlspecialchars($order['shipping_address']['last_name']) ?></strong><br>
                    <?= htmlspecialchars($order['shipping_address']['address_line']) ?><br>
                    <?= htmlspecialchars($order['shipping_address']['brgy']) ?>, <?= htmlspecialchars($order['shipping_address']['city']) ?><br>
                    <?= htmlspecialchars($order['shipping_address']['region']) ?> <?= htmlspecialchars($order['shipping_address']['postal_code']) ?>
                </div>
            </div>
        </div>
        
        <div class="barcode-container">
            <img src="data:image/png;base64,<?= $barcodeBase64 ?>" alt="Barcode">
            <div class="barcode-number"><?= htmlspecialchars($order['tracking_number']) ?></div>
        </div>
        
        <div class="details-grid">
            <div class="detail-box">
                <div class="section-title">CONTENTS</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="detail-box">
                <div class="section-title">SHIPPING INFO</div>
                <p><strong>Date:</strong> <?= date('m/d/Y') ?></p>
                <p><strong>Pieces:</strong> <?= count($order['items']) ?></p>
            </div>
            
            <div class="detail-box">
                <div class="section-title">HANDLING</div>
                <p>□ Fragile</p>
                <p>□ Keep Dry</p>
                <p>□ This End Up</p>
            </div>
        </div>
        
        <div class="special-handling">
            <strong>CARRIER USE ONLY</strong>
            <p>Date: ________ Time: ________ Signed: ________________</p>
        </div>
        
        <div class="footer">

        </div>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("awb_" . $order['tracking_number'] . ".pdf", [
    'Attachment' => false
]);