<?php


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to add a category.', 'http_code' => 405]);
    exit;
}


// json 

$request_body = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Request body is not valid JSON', 'http_code' => 400]);
    exit;
}



$movement_type = isset($request_body['movement_type']) ? trim($request_body['movement_type']) : '';
if (empty($movement_type) || !in_array($movement_type, ['in', 'out'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Valid movement type is required (in or out)', 'http_code' => 400]);
    exit;
}
$product_id = isset($request_body['product_id']) ? (int)$request_body['product_id'] : 0;
if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Valid product ID is required', 'http_code' => 400]);
    exit;
}
$quantity = isset($request_body['quantity']) ? (int)$request_body['quantity'] : 0;
if ($quantity <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Valid quantity is required', 'http_code' => 400]);
    exit;
}
$reason = isset($request_body['reason']) ? trim($request_body['reason']) : '';
if (empty($reason)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Reason is required', 'http_code' => 400]);
    exit;
}

if($inventoryModel->addStockMovement($product_id, $quantity, $movement_type, $reason)) {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Stock movement added successfully', 'http_code' => 200]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to add stock movement', 'http_code' => 500]);
}
