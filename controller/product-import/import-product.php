<?php

use GuzzleHttp\Client;
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
ini_set('session.use_strict_mode', 1);

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to import product data.', 'http_code' => 405]);
    exit;
}

$request_body = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Request body is not valid JSON', 'http_code' => 400]);
    exit;
}
if (!isset($_SESSION['auth']['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please log in.', 'http_code' => 401]);
    exit;
}


$pid = isset($request_body['pid']) ? trim($request_body['pid']) : '';
if (empty($pid)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required.', 'http_code' => 400]);
    exit;
}



if (empty($pid)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required.', 'http_code' => 400]);
    exit;
}


if($productModel->is_product_imported($_SESSION['auth']['user_id'], $pid)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product already imported.', 'http_code' => 400]);
    exit;
}

$client = new Client();


try {

    $token = 'eyJhbGciOiJIUzI1NiJ9.eyJqdGkiOiIyNDY1NSIsInR5cGUiOiJBQ0NFU1NfVE9LRU4iLCJzdWIiOiJicUxvYnFRMGxtTm55UXB4UFdMWnlpUDdteHhQZnNNSnhCR1RhUlE0ZHdZZ2RSTTZIZGVUd25tdTZJc1Y5WnF6NU5YRUszWjA4KzFWanBoSGI5cUNud3R5RWpqN0hCV2VzMVZTL1VBV1U0V0EwbGsxc1Q5SGpiajVhRlZhR1psRVRSdmtjTWM1Vjhha29lb2tzN0pDTmNuNUpYdGxoY0R6NktmY3NJNkx5dHBXUzQrTklpc2w5Y3lVa2lkQ2xvbFoxWlA4TXk5V0M0ZUcvRWRoTE1RMlQrcG1yWml4UE84eTJnWnNMM1FZUTU1VWt2aXA2eklIOFR4YURoRUJScWt0OWtpOFFhMU40YlRUNm9NZmpOa3FGK05SNWhMWTdNV0lZNlh6eDd4VXBoaVRTZTVGYnFLSVhvK0JzMExQZStOSCIsImlhdCI6MTc0NzEzNDM3OX0.Pm07_Pq0j5PXBpy3qhQrtHlL57RuQB1yuQH8U_Jfvxo';


    $url = "https://developers.cjdropshipping.com/api2.0/v1/product/query?pid={$pid}";
    $response = $client->request('GET', $url, [
        'headers' => [
            'CJ-Access-Token' => $token,
            'Content-Type' => 'application/json',
        ],
    ]);
    $data = json_decode($response->getBody(), true);

    if (isset($data['msg']) && $data['msg'] !== 'success') {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'CJ API Error: ' . $data['msg'], 'http_code' => 500]);
        exit;
    }

    $cjProducts = $data['data'] ?? [];
    if (empty($cjProducts)) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'No product found for the given ID.', 'http_code' => 404]);
        exit;
    }

    $user_id = $_SESSION['auth']['user_id'];
    $productName = $cjProducts['productNameEn'] ?? '';
    $supplierId = $cjProducts['supplierId'] ?? '';
    $productSku = $cjProducts['productSku'] ?? '';
    $category = $cjProducts['categoryName'] ?? '';

    $product = $productModel->import_product($user_id, $pid, $productName, $supplierId, $productSku, $category);
    if (!$product) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to import product data.', 'http_code' => 500]);
        exit;
    }

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Product data retrieved successfullsy.',
        'http_code' => 200,
        'data' => $product
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve product data: ' . $e->getMessage(), 'http_code' => 500]);
    exit;
}
