<?php 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use GET to retrieve categories.', 'http_code' => 405]);
    exit;
}

$data = $categoryModel->getByUser($_SESSION['auth']['user_id']);

if (empty($data)) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'No categories found', 'http_code' => 404]);
} else {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $data, 'http_code' => 200]);
}