<?php

require_once '../../core/config.php';
require_once '../../models/index.php';
require_once '../../function/UIDGenerator.php';
require_once '../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Request method must be POST']);
    exit;
}

// json decode
$request_body = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    exit;
}

$tracking_number = $request_body['tracking_number'] ?? null;
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
