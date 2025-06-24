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
}



echo json_encode([
    'status' => 'success',
    'data' => $data,
    'http_code' => 200
]);
