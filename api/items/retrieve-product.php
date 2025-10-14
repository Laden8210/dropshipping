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

// FIXED: Check if it's POST request, not GET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $storeId = isset($input['store_id']) ? htmlspecialchars(strip_tags($input['store_id'])) : null;


    if ($storeId === null) {
        $data = $productModel->get_all_products();
    } else {
        $data = $productModel->get_products_by_store($storeId);

    }
    
    // Filter out products with no stock
    $data = array_filter($data, function($product) {
        return !empty($product['total_stock']) && $product['total_stock'] > 0;
    });
    $data = array_values($data); 

    // Currency conversion
    $targetCurrency = 'PHP';
    $uniqueCurrencies = [];

    foreach ($data as $item) {
        if (isset($item['currency'])) {
            $curr = strtoupper($item['currency']);
            if ($curr !== $targetCurrency && !in_array($curr, $uniqueCurrencies)) {
                $uniqueCurrencies[] = $curr;
            }
        }
    }

    // Fetch exchange rates only if we have currencies to convert
    if (!empty($uniqueCurrencies)) {
        $apiUrl = "https://open.er-api.com/v6/latest/USD";
        $response = file_get_contents($apiUrl);

        if ($response) {
            $exchangeData = json_decode($response, true);

            if (isset($exchangeData['rates'][$targetCurrency])) {
                $rates = $exchangeData['rates'];
                $conversionRates = [];

                foreach ($uniqueCurrencies as $currency) {
                    if (isset($rates[$currency]) && isset($rates[$targetCurrency])) {
                        $conversionRates[$currency] = $rates[$targetCurrency] / $rates[$currency];
                    }
                }

                foreach ($data as &$product) {
                    if (isset($product['currency']) && isset($product['min_price'])) {
                        $currency = strtoupper($product['currency']);
                        
                        if ($currency === $targetCurrency) {
                            $product['min_converted_price'] = $product['min_price'];
                            $product['min_converted_currency'] = $currency;
                            $product['min_selling_price'] = $product['min_price'] + ($product['min_price'] * ($product['profit_margin'] / 100));
                            $product['max_converted_price'] = $product['max_price'];
                            $product['max_converted_currency'] = $currency;
                            $product['max_selling_price'] = $product['max_price'] + ($product['max_price'] * ($product['profit_margin'] / 100));
                        } elseif (isset($conversionRates[$currency])) {
                            $product['min_converted_price'] = round($product['min_price'] * $conversionRates[$currency], 2);
                            $product['min_converted_currency'] = $targetCurrency;
                            $product['min_selling_price'] = $product['min_converted_price'] + ($product['min_converted_price'] * ($product['profit_margin'] / 100));
                            $product['max_converted_price'] = round($product['max_price'] * $conversionRates[$currency], 2);
                            $product['max_converted_currency'] = $targetCurrency;
                            $product['max_selling_price'] = $product['max_converted_price'] + ($product['max_converted_price'] * ($product['profit_margin'] / 100));
                        } else {
                            $product['min_converted_price'] = null;
                            $product['converted_currency'] = null;
                        }

             
                    }
                }
                unset($product); // Unset reference
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Products retrieved successfully.',
        'data' => $data,
        'http_code' => 200
    ]);
} else {
    // Return 405 for non-POST requests
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to retrieve product data.', 'http_code' => 405]);
    exit;
}