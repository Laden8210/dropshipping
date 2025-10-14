<?php

require_once '../../../core/config.php';
require_once '../../../models/index.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to add variations.', 'http_code' => 405]);
    exit;
}

$user_id = isset($_SESSION['auth']['user_id']) ? $_SESSION['auth']['user_id'] : null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please log in.', 'http_code' => 401]);
    exit;
}

$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
if (!$productId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required', 'http_code' => 400]);
    exit;
}

$variationName = isset($_POST['variation_name']) ? trim($_POST['variation_name']) : '';
if (empty($variationName)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Variation name is required', 'http_code' => 400]);
    exit;
}

$variationValue = isset($_POST['variation_value']) ? trim($_POST['variation_value']) : '';
if (empty($variationValue)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Variation value is required', 'http_code' => 400]);
    exit;
}

$price = isset($_POST['price']) ? floatval($_POST['price']) : null;
$currency = isset($_POST['currency']) ? trim($_POST['currency']) : 'USD';
$stockQuantity = isset($_POST['stock_quantity']) ? intval($_POST['stock_quantity']) : 0;
$skuSuffix = isset($_POST['sku_suffix']) ? trim($_POST['sku_suffix']) : null;

// Verify product ownership
$product = $supplierProductModel->get_product_by_id($productId);
if (!$product || $product['user_id'] !== $user_id) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Product not found or access denied', 'http_code' => 403]);
    exit;
}

$variationId = $supplierProductModel->createVariation(
    $productId,
    $variationName,
    $variationValue,
    $skuSuffix,
    $price,
    $currency,
    $stockQuantity
);

if (!$variationId) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to create variation', 'http_code' => 500]);
    exit;
}

http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Variation created successfully',
    'variation_id' => $variationId,
    'http_code' => 200
]);
