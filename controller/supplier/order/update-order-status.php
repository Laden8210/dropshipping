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

$statusHistory = $orderModel->getOrderHistoryStatus($order_number);

if (!is_array($statusHistory)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch status history', 'http_code' => 500]);
    exit;
}

if (count($statusHistory) > 0) {
    $lastStatus = $statusHistory[0];
    if ($lastStatus['status'] === $status) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Order is already in the requested status', 'http_code' => 400]);
        exit;
    }
}

if (in_array($status, ['shipping', 'delivered']) && in_array($lastStatus['status'], ['cancelled', 'processing'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Cannot change status to ' . $status . ' from ' . $lastStatus['status'], 'http_code' => 400]);
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
if ($status === 'shipped') {
    $tracking_number = UIDGenerator::generateTrackingNumber();
    $result = $orderModel->addTrackingNumber($order_number, $tracking_number);
    if ($result['status'] === 'error') {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $result['message'], 'http_code' => 500]);
        exit;
    }
}


$userId = $data['data']['user_id'] ?? null;
if ($userId) {
    switch ($status) {
        case 'processing':
            $notificationMessage = "Your order with order number {$order_number} is now being processed.";
            break;
        case 'shipped':
            $notificationMessage = "Your order with order number {$order_number} has been shipped.";
            if (isset($tracking_number)) {
                $notificationMessage .= " Tracking Number: {$tracking_number}.";
            }
            break;
        case 'delivered':
            $notificationMessage = "Your order with order number {$order_number} has been delivered.";
            break;
        case 'cancelled':
            $notificationMessage = "Your order with order number {$order_number} has been cancelled.";
            break;
        default:
            $notificationMessage = "The status of your order with order number {$order_number} has been updated to '{$status}'.";
            break;
    }
    $notificationModel->create($userId, $notificationMessage);
}

$sql = "SELECT * FROM orders WHERE order_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $order_number);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();
$userId = $order['user_id'] ?? null;

$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$customerName = $user['first_name'] . ' ' . $user['last_name'];

if ($userId) {
    $notificationService = new NotificationService();
    if ($user['phone_number']) {
        $results['sms'] = $notificationService->sendSMS(
            $user['phone_number'],
            'Your order with order number ' . $order_number . ' has been updated to ' . $status . '.'
        );
    }

    if ($user['email']) {
        $results['email'] = $notificationService->sendEmail(
            $user['email'],
            'Order Status Update - ' . $order_number,
            "Hello,\n\nYour order with order number " . $order_number . " has been updated to " . $status . ".\n\nThank you for choosing our service!\n\nBest regards,\nDropshipping Support Team",
            $customerName
        );
    }
}




echo json_encode([
    'status' => 'success',
    'message' => 'Order status updated successfully.',
    'data' => [
        'order_number' => $order_number,
        'status' => $status,
        'products' => $items
    ],
    'http_code' => 200
]);
