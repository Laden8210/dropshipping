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

$order_number = isset($request_body['order_id']) ? $request_body['order_id'] : null;
if (empty($order_number)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Order number is required', 'http_code' => 400]);
    exit;
}

$data = $orderModel->getOrderBy($order_number);

if ($data['status'] === 'error') {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => $data['message'], 'http_code' => 404]);
    exit;
}

$status = isset($request_body['status']) ? $request_body['status'] : null;
if (empty($status)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Status is required', 'http_code' => 400]);
    exit;
}


$updatedOrder = $orderModel->updateOrderStatus($order_number, $status);

$items = $data['data']['products'] ?? [];

if ($status === 'processing') {
    foreach ($items as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];


        $inventoryModel->addStockMovement($product_id, $quantity, 'out', 'Order ID: ' . $order_number);
    }
}



if ($updatedOrder['status'] === 'error') {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $updatedOrder['message'], 'http_code' => 500]);
    exit;
}
// echo json_encode([
//     'status' => 'success',
//     'message' => 'Order status updated successfully.',
//     'data' => [
//         'order_number' => $order_number,
//         'status' => $status,
//         'products' => $items
//     ],
//     'http_code' => 200
// ]);