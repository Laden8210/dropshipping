<?php

require_once '../../core/config.php';
require_once '../../models/index.php';
require_once '../../function/UIDGenerator.php';
require_once '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Request method must be POST']);
    exit;
}

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Authorization token missing']);
    exit;
}
$jwt = $matches[1];

$secret_key = "dropshipping_8210";
try {
    $decoded = JWT::decode($jwt, new Key(trim($secret_key), 'HS256'));
    $user_id = $decoded->sub;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid token: ' . $e->getMessage()]);
    exit;
}


$cart = $cartModel->getCartItems($user_id);


$apiUrl = "https://open.er-api.com/v6/latest/USD";
$response = file_get_contents($apiUrl);

if (!$response) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch exchange rates', 'http_code' => 500]);
    exit;
}

$exchangeData = json_decode($response, true);
// Remove debug output of $exchangeData

$targetCurrency = 'PHP';
$rates = $exchangeData['rates'] ?? [];
$baseCode = $exchangeData['base_code'] ?? 'USD';

// Prepare conversion rates to PHP from all currencies
$conversionRates = [];
if (!empty($rates) && isset($rates[$targetCurrency])) {
    foreach ($rates as $currencyCode => $rate) {
        // Convert from $currencyCode to PHP
        if ($currencyCode === $targetCurrency) {
            $conversionRates[$currencyCode] = 1.0;
        } else {
            
            $conversionRates[$currencyCode] = $rates[$targetCurrency] / $rate;
        }
    }
}

foreach ($cart as &$item) {
    foreach ($item['items'] as &$product) {
        $currency = strtoupper($product['currency']);
        $basePrice = floatval($product['base_price']);

        if ($currency === $targetCurrency) {
            $product['converted_price'] = $basePrice;
            $product['converted_currency'] = $currency;
        } elseif (isset($conversionRates[$currency])) {
            $product['converted_price'] = round($basePrice * $conversionRates[$currency], 2);
            $product['converted_currency'] = $targetCurrency;
        } else {
            $product['converted_price'] = null;
            $product['converted_currency'] = null;
        }


        if ($product['converted_price'] !== null && $product['profit_margin'] !== null) {
            $product['converted_price'] = round($product['converted_price'], 2);
            // base price with profit margin
            $product['profit_with_base_price'] = round($product['base_price'] + ($product['base_price'] * ($product['profit_margin'] / 100)), 2);
            $product['selling_price'] = round($product['converted_price'] + ($product['converted_price'] * ($product['profit_margin'] / 100)), 2);
        } else {
            $product['selling_price'] = round($product['converted_price'], 2);
        }
    }
}

if ($cart) {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $cart]);
} else {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Cart not found']);
}
