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

$store_id = $request_body['store_id'] ?? '';
if (empty($store_id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Store ID is required.', 'http_code' => 400]);
    exit;
}
$store_status = $request_body['store_status'] ?? '';
if (empty($store_status)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Store status is required.', 'http_code' => 400]);
    exit;
}


$data = $storeProfileModel->getStoreById($store_id);
if (!$data) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Store not found.', 'http_code' => 200, 'store_name' => 'Unnamed Store']);
    exit;
}

if ($storeProfileModel->updateStoreStatus($store_id, $store_status)) {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Store status updated successfully.', 'http_code' => 200]);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to update store status.', 'http_code' => 500]);
    exit;
}
