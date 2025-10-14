<?php

require_once '../../../core/config.php';
require_once '../../../models/index.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to toggle unlisted status.', 'http_code' => 405]);
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

$isUnlisted = isset($_POST['is_unlisted']) ? (bool)$_POST['is_unlisted'] : false;

// Verify product ownership
$product = $supplierProductModel->get_product_by_id($productId);
if (!$product || $product['user_id'] !== $user_id) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Product not found or access denied', 'http_code' => 403]);
    exit;
}

$success = $supplierProductModel->updateProductUnlistedStatus($productId, $isUnlisted);

if (!$success) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to update product status', 'http_code' => 500]);
    exit;
}

$statusMessage = $isUnlisted ? 'Product unlisted successfully' : 'Product listed successfully';

http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => $statusMessage,
    'is_unlisted' => $isUnlisted,
    'http_code' => 200
]);
