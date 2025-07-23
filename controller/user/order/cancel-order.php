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


$orderNumber = $_GET['order_number'] ?? null;

$orderExists = $orderProductModel->is_order_exist($orderNumber);

if (!$orderExists) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Order not found',
        'http_code' => 404
    ]);
    exit;
}


$isCancelled = $orderProductModel->is_order_cancelled($orderNumber);
if ($isCancelled) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Order has already been cancelled',
        'data' => null,
        'http_code' => 400
    ]);
    exit;
}

$result = $orderProductModel->cancel_order($orderNumber);

if ($result !== true) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to cancel order',
        'error' => $result, 
        'http_code' => 500
    ]);
    exit;
}

http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Order cancelled successfully',
    'order_number' => $orderNumber,
    'http_code' => 200
]);