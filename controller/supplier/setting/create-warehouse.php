<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to add a category.', 'http_code' => 405]);
    exit;
}



$request_body = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Request body is not valid JSON', 'http_code' => 400]);
    exit;
}

$warehouse_name = isset($request_body['warehouse_name']) ? trim($request_body['warehouse_name']) : '';
$warehouse_address = isset($request_body['warehouse_address']) ? trim($request_body['warehouse_address']) : '';

if (empty($warehouse_name) || empty($warehouse_address)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Warehouse name and address are required.']);
    exit;
}


$user_id = $_SESSION['auth']['user_id'] ?? null;

if (!$user_id) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Forbidden: You do not have permission to access this resource.']);
    exit;
}

if($warehouseModel->createWarehouse($user_id, $warehouse_name, $warehouse_address)) {

    echo json_encode(['status' => 'success', 'message' => 'Warehouse created successfully.', 'http_code' => 200]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to create warehouse. Please try again later.', 'http_code' => 500]);
}

