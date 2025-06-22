<?php

$data = $storeProfileModel->getStoresByUser($_SESSION['auth']['user_id']);
if (!$data) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'No stores found', 'http_code' => 404]);
    exit;
}
http_response_code(200);
echo json_encode(['status' => 'success', 'data' => $data, 'http_code' => 200]);
exit;