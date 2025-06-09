<?php


require_once '../../core/config.php';
require_once '../../models/index.php';
require_once '../../function/UIDGenerator.php';

require_once '../../vendor/autoload.php';

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
ini_set('session.use_strict_mode', 1);

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to retrieve product data.', 'http_code' => 405]);
    exit;
}

use GuzzleHttp\Client;

// Expecting JSON input
$input = json_decode(file_get_contents('php://input'), true);
$pid = isset($input['pid']) ? trim($input['pid']) : '';
if (empty($pid)) {

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

        echo json_encode(['status' => 'error', 'message' => 'CJ API Error: ' . $data['msg'], 'http_code' => 500]);
        exit;
    }

    $cjProducts = $data['data'] ?? [];
    if (empty($cjProducts)) {

        echo json_encode(['status' => 'error', 'message' => 'No product found for the given ID.', 'http_code' => 404]);
        exit;
    }

    $accessKey = 'd942c0471b1447b986035d6c0c40c452';
    $currency = 'PHP';
    $apiUrl = "https://api.currencyfreaks.com/latest?apikey={$accessKey}&symbols={$currency}";

    // sellPrice
    $sellPrice = $cjProducts['suggestSellPrice'] ?? 0;
    if ($sellPrice > 0) {
        try {
            $response = $client->request('GET', $apiUrl);
            $data = json_decode($response->getBody(), true);
            if (isset($data['rates'][$currency])) {
                $exchangeRate = $data['rates'][$currency];
                $cjProducts['exchangeRate'] = round($sellPrice * $exchangeRate, 2);
            } else {
               $cjProducts['exchangeRate'] = round($sellPrice, 2);
            }
        } catch (Exception $e) {
        }
    } else {
       $cjProducts['exchangeRate'] = 0;
    }



    echo json_encode([
        'status' => 'success',
        'message' => 'Products retrieved successfully.',
        'data' => $cjProducts,
        'http_code' => 200
    ]);
} catch (Exception $e) {

    echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve product data: ' . $e->getMessage(), 'http_code' => 500]);
    exit;
}
