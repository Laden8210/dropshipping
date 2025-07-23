<?php

$product_id = isset($_GET['productId']) ? intval($_GET['productId']) : 0;
if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Valid product ID is required', 'http_code' => 400]);
    exit;
}

$data = $productModel->get_price_product_history($product_id);
if (!$data) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'No price history found for this product', 'http_code' => 404]);
    exit;
}


http_response_code(200);
echo json_encode(['status' => 'success', 'data' => $data, 'http_code' => 200]);