<?php



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use GET to retrieve product data.', 'http_code' => 405]);
    exit;
}

if (!isset($_SESSION['auth']['store_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please select a store before importing products.', 'http_code' => 400]);
    exit;
}

$data = $supplierProductModel->get_available_products(
    $_SESSION['auth']['user_id'],
    $_SESSION['auth']['store_id']
);
if ($data) {
    echo json_encode(['status' => 'success', 'data' => $data, 'http_code' => 200]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No products found', 'http_code' => 404]);
}