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

if($storeProfileModel->exists($_SESSION['auth']['user_id'], $store_id) === false) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Store not found.', 'http_code' => 404]);
    exit;
}

$_SESSION['auth']['store_id'] = $store_id;
http_response_code(200);
echo json_encode(['status' => 'success', 'message' => 'Current store updated successfully.', 'http_code' => 200]);
exit;