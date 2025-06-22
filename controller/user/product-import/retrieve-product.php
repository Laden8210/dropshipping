<?php



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use GET to retrieve product data.', 'http_code' => 405]);
    exit;
}


$data = $supplierProductModel->get_all_products();
if ($data) {
    echo json_encode(['status' => 'success', 'data' => $data, 'http_code' => 200]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No products found', 'http_code' => 404]);
}