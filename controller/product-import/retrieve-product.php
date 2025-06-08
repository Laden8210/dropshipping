<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
ini_set('session.use_strict_mode', 1);

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use GET to retrieve product data.', 'http_code' => 405]);
    exit;
}


use GuzzleHttp\Client;

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$page_size = isset($_GET['totalProduct']) ? (int)$_GET['totalProduct'] : 10;

$client = new Client();

try {

    $token = 'eyJhbGciOiJIUzI1NiJ9.eyJqdGkiOiIyNDY1NSIsInR5cGUiOiJBQ0NFU1NfVE9LRU4iLCJzdWIiOiJicUxvYnFRMGxtTm55UXB4UFdMWnlpUDdteHhQZnNNSnhCR1RhUlE0ZHdZZ2RSTTZIZGVUd25tdTZJc1Y5WnF6NU5YRUszWjA4KzFWanBoSGI5cUNud3R5RWpqN0hCV2VzMVZTL1VBV1U0V0EwbGsxc1Q5SGpiajVhRlZhR1psRVRSdmtjTWM1Vjhha29lb2tzN0pDTmNuNUpYdGxoY0R6NktmY3NJNkx5dHBXUzQrTklpc2w5Y3lVa2lkQ2xvbFoxWlA4TXk5V0M0ZUcvRWRoTE1RMlQrcG1yWml4UE84eTJnWnNMM1FZUTU1VWt2aXA2eklIOFR4YURoRUJScWt0OWtpOFFhMU40YlRUNm9NZmpOa3FGK05SNWhMWTdNV0lZNlh6eDd4VXBoaVRTZTVGYnFLSVhvK0JzMExQZStOSCIsImlhdCI6MTc0NzEzNDM3OX0.Pm07_Pq0j5PXBpy3qhQrtHlL57RuQB1yuQH8U_Jfvxo';

    $url = "https://developers.cjdropshipping.com/api2.0/v1/product/list?productName={$keyword}&pageNum=1&pageSize={$page_size}";
    $response = $client->request('GET', $url, [
        'headers' => [
            'CJ-Access-Token' => $token,
            'Content-Type' => 'application/json',
        ],
    ]);

    $data = json_decode($response->getBody(), true);

    if (isset($data['msg']) && $data['msg'] !== 'success') {

        echo json_encode(['status' => 'error', 'message' => 'CJ API Error: ' . $data['msg'], 'http_code' => 500]);
        exit;
    }

    $cjProducts = $data['data']['list'] ?? [];
    if (empty($cjProducts)) {

        echo json_encode(['status' => 'error', 'message' => 'No products found for the given keyword.', 'http_code' => 404]);
        exit;
    }

    $user_id = $_SESSION['auth']['user_id'] ?? null;
    $filteredProducts = [];
    foreach ($cjProducts as $product) {
        $pid = $product['pid'] ?? null;
        if ($pid && !$productModel->is_product_imported($user_id, $pid)) {
            $filteredProducts[] = $product;
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Products retrieved successfully.',
        'data' => $filteredProducts,
        'http_code' => 200
    ]);
} catch (Exception $e) {

    echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve product data: ' . $e->getMessage(), 'http_code' => 500]);
    exit;
}
