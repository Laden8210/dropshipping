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

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$page_size = isset($_GET['totalProduct']) ? (int)$_GET['totalProduct'] : 10;

$client = new Client();

try {

    $token = 'eyJhbGciOiJIUzI1NiJ9.eyJqdGkiOiIyNDY1NSIsInR5cGUiOiJBQ0NFU1NfVE9LRU4iLCJzdWIiOiJicUxvYnFRMGxtTm55UXB4UFdMWnlpUDdteHhQZnNNSnhCR1RhUlE0ZHdZZ2RSTTZIZGVUd25tdTZJc1Y5WnF6NU5YRUszWjA4KzFWanBoSGI5cUNud3R5RWpqN0hCV2VzMVZTL1VBV1U0V0EwbGsxc1Q5SGpiajVhRlZhR1psRVRSdmtjTWM1Vjhha29lb2tzN0pDTmNuNUpYdGxoY0R6NktmY3NJNkx5dHBXUzQrTklpc2w5Y3lVa2lkQ2xvbFoxWlA4TXk5V0M0ZUcvRWRoTE1RMlQrcG1yWml4UE84eTJnWnNMM1FZUTU1VWt2aXA2eklIOFR4YURoRUJScWt0OWtpOFFhMU40YlRUNm9NZmpOa3FGK05SNWhMWTdNV0lZNlh6eDd4VXBoaVRTZTVGYnFLSVhvK0JzMExQZStOSCIsImlhdCI6MTc0NzEzNDM3OX0.Pm07_Pq0j5PXBpy3qhQrtHlL57RuQB1yuQH8U_Jfvxo';


    if (isset($data['msg']) && $data['msg'] !== 'success') {
 
        echo json_encode(['status' => 'error', 'message' => 'CJ API Error: ' . $data['msg'], 'http_code' => 500]);
        exit;
    }

    $productList = $productModel->get_all_products();

    $detailsList = [];

    foreach ($productList as $product) {
        $pid = $product['pid'] ?? null;
        $sku = $product['product_sku'] ?? null;
        $status = $product['status'] ?? null;
        if (!$pid || !$sku || $status !== 'active') {
            continue;
        }
        if (!$pid) {
            continue;
        }

        // Get product details
        $url = "https://developers.cjdropshipping.com/api2.0/v1/product/query?pid={$pid}";
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'CJ-Access-Token' => $token,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            if (isset($data['code']) && $data['code'] == 200 && isset($data['data'])) {
                // Merge API details into your product array if needed
                $product = array_merge($product, $data['data']);
            }
        } catch (Exception $e) {
            // Handle error or skip product
            continue;
        }

        // Get stock info with fixed URL and handling
        $stockUrl = "https://developers.cjdropshipping.com/api2.0/v1/product/stock/queryBySku?sku={$sku}";
        try {
            $stockResponse = $client->request('GET', $stockUrl, [
                'headers' => [
                    'CJ-Access-Token' => $token,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $stockData = json_decode($stockResponse->getBody(), true);
            if (isset($stockData['code']) && $stockData['code'] == 200 && !empty($stockData['data'])) {
                $product['stock'] = $stockData['data'];

                $totalInventory = 0;
                foreach ($stockData['data'] as $warehouse) {
                    $totalInventory += $warehouse['totalInventoryNum'];
                }
                $product['totalInventory'] = $totalInventory;
            } else {

                $product['totalInventory'] = 0;
            }
        } catch (Exception $e) {

            $product['totalInventory'] = 0;
        }

        $accessKey = 'd942c0471b1447b986035d6c0c40c452';
        $currency = 'PHP';
        $apiUrl = "https://api.currencyfreaks.com/latest?apikey={$accessKey}&symbols={$currency}";

        // sellPrice
        $sellPrice = $product['suggestSellPrice'] ?? 0;
        if ($sellPrice > 0) {
            try {
                $response = $client->request('GET', $apiUrl);
                $data = json_decode($response->getBody(), true);
                if (isset($data['rates'][$currency])) {
                    $exchangeRate = $data['rates'][$currency];
                    $product['exchangeRate'] = round($sellPrice * $exchangeRate, 2);
                } else {
                    $product['exchangeRate'] = round($sellPrice, 2);
                }
            } catch (Exception $e) {
              
            }
        } else {
            $product['sellPrice'] = 0;
        }


        $product['status_db'] = $status;
        $detailsList[] = $product;
    }



    echo json_encode([
        'status' => 'success',
        'message' => 'Products retrieved successfully.',
        'data' => $detailsList,
        'http_code' => 200
    ]);
} catch (Exception $e) {

    echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve product data: ' . $e->getMessage(), 'http_code' => 500]);
    exit;
}
