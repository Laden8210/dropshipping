<?php 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use GET to retrieve the current store.', 'http_code' => 405]);
    exit;
}

$store_id = $_SESSION['auth']['store_id'] ?? null;

if (empty($store_id)) {
  
    echo json_encode(['status' => 'error', 'message' => 'Store ID is not set in session.', 'http_code' => 200, 'store_name' => 'No Store']);
    exit;
}

$data = $storeProfileModel->getStoreById($store_id);
if (!$data) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Store not found.', 'http_code' => 200, 'store_name' => 'Unnamed Store']);
    exit;
}
http_response_code(200);
echo json_encode(['status' => 'success',  'http_code' => 200, 'store_name' => $data['store_name'] ?? 'Unnamed Store']);
exit;