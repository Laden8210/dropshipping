<?php

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Request method must be GET',
        'data' => null,
        'http_code' => 405
    ]);
    exit;
}

$order_number = isset($_GET['order_number']) ? $_GET['order_number'] : null;
if (empty($order_number)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Order number is required',
        'data' => null,
        'http_code' => 400
    ]);
    exit;
}

$orders = $orderProductModel->getByOrderNumber($order_number);
if ($orders === false) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve orders',
        'data' => null,
        'http_code' => 500
    ]);
    exit;
}

echo json_encode([
    'status' => 'success',
    'message' => 'Orders retrieved successfully.',
    'data' => $orders,
    'http_code' => 200
]);