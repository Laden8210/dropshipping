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

$orders = $orderProductModel->getAll();
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