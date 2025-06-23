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
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method Not Allowed. Use POST to retrieve product data.',
        'http_code' => 405
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$pid = isset($input['pid']) ? trim($input['pid']) : '';

if (empty($pid)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Product ID is required.',
        'http_code' => 400
    ]);
    exit;
}


$product = $productModel->get_single_product_by_id($pid);

if (!$product || !isset($product['product_id'])) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Product not found.',
        'http_code' => 404
    ]);
    exit;
}


if (empty($product['current_stock']) || $product['current_stock'] <= 0) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Product has no available stock.',
        'http_code' => 404
    ]);
    exit;
}


$targetCurrency = 'PHP';
$sourceCurrency = strtoupper($product['currency']);

if ($sourceCurrency !== $targetCurrency) {
    $apiUrl = "https://open.er-api.com/v6/latest/USD";
    $response = @file_get_contents($apiUrl);

    if (!$response) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch exchange rates.',
            'http_code' => 500
        ]);
        exit;
    }

    $exchangeData = json_decode($response, true);
    $rates = $exchangeData['rates'] ?? [];

    if (!isset($rates[$targetCurrency], $rates[$sourceCurrency])) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing exchange rate data.',
            'http_code' => 500
        ]);
        exit;
    }

    $conversionRate = $rates[$targetCurrency] / $rates[$sourceCurrency];
    $convertedPrice = round($product['price'] * $conversionRate, 2);
} else {
    $convertedPrice = $product['price'];
}


$product['converted_currency'] = $targetCurrency;
$product['converted_price'] = $convertedPrice;


$margin = floatval($product['profit_margin'] ?? 0);
if (is_numeric($convertedPrice)) {
    $product['selling_price'] = round($convertedPrice + ($convertedPrice * $margin / 100), 2);
} else {
    $product['selling_price'] = null;
}


echo json_encode([
    'status' => 'success',
    'message' => 'Product retrieved successfully.',
    'data' => $product,
    'http_code' => 200
]);
