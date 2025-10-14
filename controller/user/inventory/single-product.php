<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use GET to retrieve product data.', 'http_code' => 405]);
    exit;
}

$pid = isset($_GET['pid']) ? trim($_GET['pid']) : '';
if (empty($pid)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required.', 'http_code' => 400]);
    exit;
}

$data = $productModel->get_single_product_by_id($pid);
if (!$data) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Product not found.', 'http_code' => 404]);
    exit;
}

// Currency conversion for variations
$targetCurrency = 'PHP';
$uniqueCurrencies = [];

$profit_margin = $data['profit_margin'] ?? 0;

// Collect unique currencies from variations
if (isset($data['variations']) && is_array($data['variations'])) {
    foreach ($data['variations'] as $variation) {
        if (isset($variation['currency'])) {
            $curr = strtoupper($variation['currency']);
            if ($curr !== $targetCurrency && !in_array($curr, $uniqueCurrencies)) {
                $uniqueCurrencies[] = $curr;
            }
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

            // Calculate conversion rates for each unique currency
            foreach ($uniqueCurrencies as $currency) {
                if (isset($rates[$currency]) && isset($rates[$targetCurrency])) {
                    $conversionRates[$currency] = $rates[$targetCurrency] / $rates[$currency];
                }
            }

            // Apply conversion to each variation
            foreach ($data['variations'] as &$variation) {
                if (isset($variation['currency']) && isset($variation['price'])) {
                    $currency = strtoupper($variation['currency']);
                    
                    if ($currency === $targetCurrency) {
                        $variation['converted_price'] = $variation['price'];
                        $variation['converted_currency'] = $currency;
                        $variation['selling_price'] = $variation['price'] + ($variation['price'] * ($profit_margin / 100));
                        $variation['unconverted_selling_price'] = $variation['price'] + ($variation['price'] * ($profit_margin / 100));
                    } elseif (isset($conversionRates[$currency])) {
                        $variation['converted_price'] = round($variation['price'] * $conversionRates[$currency], 2);
                        $variation['converted_currency'] = $targetCurrency;
                        $variation['selling_price'] = $variation['converted_price'] + ($variation['converted_price'] * ($profit_margin / 100));
                        $variation['unconverted_selling_price'] = $variation['price'] + ($variation['price'] * ($profit_margin / 100));
                    } else {
                        $variation['converted_price'] = null;
                        $variation['converted_currency'] = null;
                    }
                }
            }
            unset($variation); // Unset reference
        }
    }
}

http_response_code(200);
echo json_encode(['status' => 'success', 'data' => $data, 'http_code' => 200]);
exit;