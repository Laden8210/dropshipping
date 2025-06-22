<?php



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use GET to retrieve product data.', 'http_code' => 405]);
    exit;
}


use GuzzleHttp\Client;

$pid = isset($_GET['pid']) ? trim($_GET['pid']) : '';
if (empty($pid)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required.', 'http_code' => 400]);
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


    echo json_encode([
        'status' => 'success',
        'message' => 'Products retrieved successfully.',
        'data' => $cjProducts,
        'http_code' => 200
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve product data: ' . $e->getMessage(), 'http_code' => 500]);
    exit;
}
