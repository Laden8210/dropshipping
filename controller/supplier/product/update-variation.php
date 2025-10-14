<?php

require_once '../../../core/config.php';
require_once '../../../models/index.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to update variation.', 'http_code' => 405]);
    exit;
}

$user_id = isset($_SESSION['auth']['user_id']) ? $_SESSION['auth']['user_id'] : null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please log in.', 'http_code' => 401]);
    exit;
}

$variationId = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
if (!$variationId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Variation ID is required', 'http_code' => 400]);
    exit;
}

// Get variation and verify ownership
$variation = $supplierProductModel->getVariationById($variationId);
if (!$variation) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Variation not found', 'http_code' => 404]);
    exit;
}

$product = $supplierProductModel->get_product_by_id($variation['product_id']);
if (!$product || $product['user_id'] !== $user_id) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Access denied', 'http_code' => 403]);
    exit;
}

$success = true;
$message = 'Variation updated successfully';

// Update price if provided
if (isset($_POST['price']) && is_numeric($_POST['price'])) {
    $price = floatval($_POST['price']);
    $currency = isset($_POST['currency']) ? trim($_POST['currency']) : 'USD';
    
    if (!$supplierProductModel->updateVariationPrice($variationId, $price, $currency)) {
        $success = false;
        $message = 'Failed to update variation price';
    }
}

// Update stock if provided
if (isset($_POST['stock_quantity']) && is_numeric($_POST['stock_quantity'])) {
    $stockQuantity = intval($_POST['stock_quantity']);
    
    if (!$supplierProductModel->updateVariationStock($variationId, $stockQuantity)) {
        $success = false;
        $message = 'Failed to update variation stock';
    }
}

if (!$success) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $message, 'http_code' => 500]);
    exit;
}

http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => $message,
    'http_code' => 200
]);
