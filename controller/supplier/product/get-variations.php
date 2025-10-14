<?php

require_once '../../../core/config.php';
require_once '../../../models/index.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use GET to retrieve variations.', 'http_code' => 405]);
    exit;
}

$user_id = isset($_SESSION['auth']['user_id']) ? $_SESSION['auth']['user_id'] : null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please log in.', 'http_code' => 401]);
    exit;
}

$productId = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
if (!$productId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required', 'http_code' => 400]);
    exit;
}

// Verify product ownership
$product = $supplierProductModel->get_product_by_id($productId);
if (!$product || $product['user_id'] !== $user_id) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Product not found or access denied', 'http_code' => 403]);
    exit;
}

// Get product with variations and attributes
$productData = $supplierProductModel->getProductWithVariations($productId);

if (!$productData) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Product not found', 'http_code' => 404]);
    exit;
}

http_response_code(200);
echo json_encode([
    'status' => 'success',
    'data' => $productData,
    'http_code' => 200
]);
