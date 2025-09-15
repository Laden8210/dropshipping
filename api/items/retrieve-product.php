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

$input = json_decode(file_get_contents('php://input'), true);

$storeId = isset($input['store_id']) ? htmlspecialchars(strip_tags($input['store_id'])) : null;

if ($storeId === null) {

$data = $productModel->get_all_products();
} else {
    $data = $productModel->get_products_by_store($storeId);
}

foreach ($data as $key => $product) {
    if (empty($product['current_stock']) || $product['current_stock'] <= 0) {
        unset($data[$key]);
    }
}



$targetCurrency = 'PHP';
$uniqueCurrencies = [];

foreach ($data as $item) {
    $curr = strtoupper($item['currency']);
    if ($curr !== $targetCurrency && !in_array($curr, $uniqueCurrencies)) {
        $uniqueCurrencies[] = $curr;
    }
}


$apiUrl = "https://open.er-api.com/v6/latest/USD";
$response = file_get_contents($apiUrl);

if (!$response) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch exchange rates', 'http_code' => 500]);
    exit;
}

$exchangeData = json_decode($response, true);

if (!isset($exchangeData['rates'][$targetCurrency])) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'PHP rate missing in API response', 'http_code' => 500]);
    exit;
}

$rates = $exchangeData['rates'];
$conversionRates = [];

foreach ($uniqueCurrencies as $currency) {
    if (isset($rates[$currency]) && isset($rates[$targetCurrency])) {
        $conversionRates[$currency] = $rates[$targetCurrency] / $rates[$currency];
    }
}

foreach ($data as &$product) {
    $currency = strtoupper($product['currency']);
    if ($currency === $targetCurrency) {
        $product['converted_price'] = $product['price'];
        $product['converted_currency'] = $currency;
    } elseif (isset($conversionRates[$currency])) {
        $product['converted_price'] = round($product['price'] * $conversionRates[$currency], 2);
        $product['converted_currency'] = $targetCurrency;
    } else {
        $product['converted_price'] = null;
        $product['converted_currency'] = null;
    }

    $product['selling_price'] = $product['converted_price'] + ($product['converted_price'] * ($product['profit_margin'] / 100));

}



$data = array_values($data);

echo json_encode([
    'status' => 'success',
    'message' => 'Products retrieved successfully.',
    'data' => $data,
    'http_code' => 200
]);
