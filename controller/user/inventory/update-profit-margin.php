<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to import product data.', 'http_code' => 405]);
    exit;
}

$request_body = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Request body is not valid JSON', 'http_code' => 400]);
    exit;
}


$product_id = isset($request_body['product_id']) ? trim($request_body['product_id']) : '';
if (empty($product_id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required.', 'http_code' => 400]);
    exit;
}

$profit_margin = isset($request_body['profit_margin']) ? trim($request_body['profit_margin']) : '';
if (empty($profit_margin)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Profit margin is required.', 'http_code' => 400]);
    exit;
}

if (!is_numeric($profit_margin) || $profit_margin < 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Profit margin must be a valid number greater than or equal to 0.', 'http_code' => 400]);
    exit;
}

if($productModel->update_profit_margin($_SESSION['auth']['user_id'], $product_id, $profit_margin)) {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Profit margin updated successfully.', 'http_code' => 200]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to update profit margin.', 'http_code' => 500]);
}