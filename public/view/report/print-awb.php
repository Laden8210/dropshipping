<?php
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

// Get tracking number from URL parameter
$tracking_number = $_GET['tracking_number'] ?? '';
if (!$tracking_number) {
    http_response_code(404);
    include __DIR__ . '/public/view/error/404.php';
    exit;
}

try {
    $order = $orderProductModel->printAWB($tracking_number);

    // Generate QR Code
    $builder = new Builder(
        writer: new PngWriter(),
        writerOptions: [],
        validateResult: false,
        data: $tracking_number,
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::High,
        size: 200,
        margin: 5,
        roundBlockSizeMode: RoundBlockSizeMode::Margin,
        logoResizeToWidth: 40,
        logoPunchoutBackground: true,
        labelText: $tracking_number,
        labelFont: new OpenSans(8),
        labelAlignment: LabelAlignment::Center
    );

    $qrCode = $builder->build();
    $qrBase64 = $qrCode->getDataUri();

    // Format dates
    $orderDate = date('M j, Y', strtotime($order['order_date']));
    $currentDate = date('M j, Y H:i');
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
            font-size: 8pt;
            line-height: 1.2;
            font-family: 'Helvetica', Arial, sans-serif;
        }

        body {
            color: #333;
            background: #fff;
        }

        .awb-container {
            width: 150mm;
            min-height: 100mm;
            padding: 4mm;
            border: 1px solid #000;
            position: relative;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2mm;
            padding-bottom: 1mm;
            border-bottom: 1px solid #000;
        }

        .header h1 {
            font-size: 10pt;
            color: #d40000;
            font-weight: bold;
        }

        .header-info {
            text-align: right;
        }

        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2mm;
            margin-bottom: 2mm;
        }

        .address-box {
            border: 1px solid #000;
            padding: 1mm;
            min-height: 18mm;
            font-size: 7pt;
        }

        .section-title {
            font-weight: bold;
            font-size: 7pt;
            margin-bottom: 0.5mm;
            background: #f0f0f0;
            padding: 0.5mm 1mm;
            border: 1px solid #000;
            border-bottom: none;
        }

        .qr-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin: 2mm 0;
            padding: 1mm;
            border: 1px solid #000;
        }

        .tracking-info {
            flex: 1;
        }

        .tracking-info p {
            margin: 0.5mm 0;
            font-size: 7pt;
        }

        .qrcode-container {
            text-align: center;
        }

        .qrcode-number {
            font-size: 7pt;
            margin-top: 0.5mm;
            font-weight: bold;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 1mm;
            margin-top: 1mm;
        }

        .detail-box {
            border: 1px solid #000;
            padding: 1mm;
            font-size: 7pt;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1mm;
            font-size: 6pt;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 0.5mm;
            text-align: left;
        }

        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .package-info {
            margin-top: 1mm;
        }

        .package-info p {
            margin: 0.3mm 0;
        }

        .special-handling {
            margin-top: 2mm;
            padding-top: 1mm;
            border-top: 1px dashed #000;
            font-size: 7pt;
        }

        .footer {
            margin-top: 2mm;
            text-align: center;
            font-size: 6pt;
            padding-top: 1mm;
            border-top: 1px dashed #000;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5mm 1mm;
            background: #333;
            color: white;
            border-radius: 2px;
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .dimensions {
            font-family: 'Courier New', monospace;
        }
    </style>
</head>

<body>
    <div class="awb-container">
        <div class="header">
            <h1>SHIPPING LABEL</h1>
            <div class="header-info">
                <strong>Service:</strong> <?= htmlspecialchars($order['service_type']) ?><br>
                <strong>Ref No:</strong> <?= htmlspecialchars($order['order_number']) ?><br>
                <strong>Order Date:</strong> <?= $orderDate ?><br>
            </div>
        </div>

        <div class="info-section">
            <div>
                <div class="section-title">SHIPPER</div>
                <div class="address-box">
                    <strong><?= htmlspecialchars($order['store_profile']['store_name']) ?></strong><br>
                    <?= htmlspecialchars($order['store_profile']['store_address']) ?><br>
                    Tel: <?= htmlspecialchars($order['store_profile']['store_phone']) ?><br>
                    Email: <?= htmlspecialchars($order['store_profile']['store_email']) ?>
                </div>
            </div>

            <div>
                <div class="section-title">CONSIGNEE</div>
                <div class="address-box">
                    <strong><?= htmlspecialchars($order['shipping_address']['first_name']) ?> <?= htmlspecialchars($order['shipping_address']['last_name']) ?></strong><br>
                    Tel: <?= htmlspecialchars($order['shipping_address']['phone_number']) ?><br>
                    <?= htmlspecialchars($order['shipping_address']['full_address']) ?>
                </div>
            </div>
        </div>

        <!-- QR Code Section -->
        <div class="qr-section">
            <div class="tracking-info">
                <div class="section-title">SHIPPING DETAILS</div>
                <p><strong>Tracking No:</strong> <?= htmlspecialchars($order['tracking_number']) ?></p>
                <p><strong>Package:</strong> <?= $order['package_info']['package_count'] ?> of <?= $order['package_info']['package_count'] ?></p>
                <p><strong>Items:</strong> <?= $order['package_info']['total_items'] ?> pcs</p>
                <?php if ($order['package_info']['total_weight']): ?>
                    <p><strong>Weight:</strong> <?= $order['package_info']['total_weight'] ?> kg</p>
                <?php endif; ?>
                <?php if ($order['package_info']['dimensions']): ?>
                    <p><strong>Dimensions:</strong>
                        <span class="dimensions">
                            <?= $order['package_info']['dimensions']['length'] ?>x<?= $order['package_info']['dimensions']['width'] ?>x<?= $order['package_info']['dimensions']['height'] ?> <?= $order['package_info']['dimensions']['unit'] ?>
                        </span>
                    </p>
                <?php endif; ?>
            </div>
            <div class="qrcode-container">
                <img src="<?= $qrBase64 ?>" alt="QR Code" style="width: 45mm; height: 45mm;">
                <div class="qrcode-number">SCAN TO TRACK</div>
            </div>
        </div>

        <div class="details-grid">
            <div class="detail-box">
                <div class="section-title">ORDER CONTENTS</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Variation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name'] ) ?></td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td>
                                    <?php
                                    $variation = [];
                                    if (!empty($item['size'])) $variation[] = $item['size'];
                                    if (!empty($item['color'])) $variation[] = $item['color'];
                                    echo htmlspecialchars(implode('/', $variation) ?: 'Standard');
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="detail-box">
                <div class="section-title">SHIPPING INFO</div>
                <div class="package-info">
                    <p><strong>Service:</strong> <?= htmlspecialchars($order['service_type']) ?></p>
                    <p><strong>Payment:</strong> <?= htmlspecialchars($order['payment_info']['method'] ?? 'N/A') ?></p>
                    <p><strong>Amount:</strong> PHP<?= number_format($order['financial_info']['total_amount'], 2) ?></p>
                </div>
            </div>

            <div class="detail-box">
                <div class="section-title">HANDLING</div>
                <p>• Fragile</p>
                <p>• Keep Dry</p>
                <p>• This End Up</p>
                <p>• Do Not Stack</p>
            </div>
        </div>

        <div class="special-handling">
            <strong>CARRIER USE ONLY</strong>
            <p>Date: ________ Time: ________ Signed: ________________</p>
            <p>Remarks: _________________________________________</p>
        </div>

        <div class="footer">
            Generated on <?= $currentDate ?> | <?= htmlspecialchars($order['store_profile']['store_name']) ?> | Page 1 of 1
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
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->render();

$dompdf->stream("awb_" . $order['tracking_number'] . ".pdf", [
    'Attachment' => false
]);
