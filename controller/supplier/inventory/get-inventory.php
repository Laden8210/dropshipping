<?php

$data = $supplierProductModel->get_inventory($_SESSION['auth']['user_id']);
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
if (!$data) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'No inventory found', 'http_code' => 404]);
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


if ($keyword) {
    $data = array_filter($data, function ($product) use ($keyword) {
        return stripos($product['product'], $keyword) !== false || stripos($product['category'], $keyword) !== false || stripos($product['sku'], $keyword) !== false;
    });
}
if ($status) {
    $data = array_filter($data, function ($product) use ($status) {
        return $product['status'] === $status;
    });
}


echo json_encode([
    'status' => 'success',
    'data' => $data,
    'http_code' => 200
]);
