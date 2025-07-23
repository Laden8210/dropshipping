<?php



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use GET to retrieve product data.', 'http_code' => 405]);
    exit;
}




$pid = isset($_GET['pid']) ? trim($_GET['pid']) : '';
if (empty($pid)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required.', 'http_code' => 400]);
    exit;
}

$data = $productModel->get_single_product_by_id($pid);
if (!$data) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Product not found.', 'http_code' => 404]);
    exit;
}

http_response_code(200);
echo json_encode(['status' => 'success', 'data' => $data, 'http_code' => 200]);
exit;