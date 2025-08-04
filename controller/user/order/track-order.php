<?php

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Request method must be GET']);
    exit;
}

$tracking_number = $_GET['tracking_number'] ?? null;
if (empty($tracking_number)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Tracking number is required', 'http_code' => 400]);
    exit;
}
$data = $orderShippingStatusModel->getByTrackingNumber($tracking_number);

if (!$data) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve shipping statuses']);
    exit;
}
echo json_encode(['status' => 'success', 'data' => $data, 'http_code' => 200]);
