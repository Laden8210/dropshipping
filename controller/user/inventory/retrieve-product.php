<?php

if (empty($_SESSION['auth']['store_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Store ID is not set', 'http_code' => 400]);
    exit;
}

$data = $productModel->get_imported_products(
    isset($_SESSION['auth']['user_id']) ? $_SESSION['auth']['user_id'] : null,
    isset($_SESSION['auth']['store_id']) ? $_SESSION['auth']['store_id'] : null
);

if (!$data) {

    echo json_encode(['status' => 'error', 'message' => 'No products found in your inventory. Please set the store first.', 'http_code' => 200]);
    exit;
}



echo json_encode([
    'status' => 'success',
    'data' => $data,
    'http_code' => 200
]);
