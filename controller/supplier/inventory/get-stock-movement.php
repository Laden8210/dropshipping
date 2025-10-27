<?php

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Valid product ID is required', 'http_code' => 400]);
    exit;
}

$variation_id = isset($_GET['variation_id']) ? intval($_GET['variation_id']) : null;

if ($variation_id !== null && $variation_id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid variation ID', 'http_code' => 400]);
    exit;
}

$data = $inventoryModel->getStockMovementsVariant($product_id, $variation_id);
if (!$data) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'No stock movements found for this product', 'http_code' => 404]);
    exit;
}


http_response_code(200);
echo json_encode(['status' => 'success', 'data' => $data, 'http_code' => 200]);