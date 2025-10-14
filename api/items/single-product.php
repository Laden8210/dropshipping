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
$store_id = isset($input['store_id']) ? trim($input['store_id']) : '';

if (empty($pid)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Product ID is required.',
        'http_code' => 400
    ]);
    exit;
}

if (empty($store_id)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Store ID is required.',
        'http_code' => 400
    ]);
    exit;
}

$product = $productModel->get_single_product_by_id_by_store($pid, $store_id);

if (!$product || !isset($product['product_id'])) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Product not found.',
        'http_code' => 404
    ]);
    exit;
}

// Get variations for the product with inventory data
$variations = $productModel->get_product_variations_with_inventory($pid);

// Get all images for the product
$images = $productModel->get_product_images($pid);

// Prepare the response data
$responseData = [
    'product_id' => $product['product_id'],
    'user_id' => $product['user_id'],
    'product_name' => $product['product_name'],
    'product_sku' => $product['product_sku'],
    'product_category' => $product['category_id'],
    'description' => $product['description'],
    'created_at' => $product['created_at'],
    'status' => $product['status'],
    'updated_at' => $product['updated_at'],
    'is_unlisted' => $product['is_unlisted'],
    'category_name' => $product['category_name'],
    'profit_margin' => $product['profit_margin'],
    'image_urls' => implode(', ', $images),
    'primary_image_url' => $product['primary_image'],
    'variations' => [],
    'images' => $images,
    'primary_image' => $product['primary_image']
];

// Process variations with currency conversion
$targetCurrency = 'PHP';
$margin = floatval($product['profit_margin'] ?? 0);

foreach ($variations as $variation) {
    $sourceCurrency = strtoupper($variation['currency']);
    
    if ($sourceCurrency !== $targetCurrency) {
        $apiUrl = "https://open.er-api.com/v6/latest/USD";
        $response = @file_get_contents($apiUrl);
        
        if ($response) {
            $exchangeData = json_decode($response, true);
            $rates = $exchangeData['rates'] ?? [];
            
            if (isset($rates[$targetCurrency], $rates[$sourceCurrency])) {
                $conversionRate = $rates[$targetCurrency] / $rates[$sourceCurrency];
                $convertedPrice = round($variation['price'] * $conversionRate, 2);
            } else {
                $convertedPrice = $variation['price'];
            }
        } else {
            $convertedPrice = $variation['price'];
        }
    } else {
        $convertedPrice = $variation['price'];
    }
    
    $sellingPrice = round($convertedPrice + ($convertedPrice * $margin / 100), 2);
    $unconvertedSellingPrice = round($variation['price'] + ($variation['price'] * $margin / 100), 2);
    
    $responseData['variations'][] = [
        'variation_id' => $variation['variation_id'],
        'product_id' => $variation['product_id'],
        'size' => $variation['size'],
        'color' => $variation['color'],
        'weight' => $variation['weight'],
        'length' => $variation['length'],
        'width' => $variation['width'],
        'height' => $variation['height'],
        'price' => $variation['price'],
        'currency' => $variation['currency'],
        'sku_suffix' => $variation['sku_suffix'],
        'stock_quantity' => $variation['quantity'], // From inventory table
        'is_active' => $variation['is_active'],
        'created_at' => $variation['created_at'],
        'updated_at' => $variation['updated_at'],
        'change_date' => $variation['change_date'],
        'converted_price' => $convertedPrice,
        'converted_currency' => $targetCurrency,
        'selling_price' => $sellingPrice,
        'unconverted_selling_price' => $unconvertedSellingPrice
    ];
}

// Check if any variation has stock
$hasStock = false;
foreach ($responseData['variations'] as $variation) {
    if ($variation['stock_quantity'] > 0) {
        $hasStock = true;
        break;
    }
}

if (!$hasStock) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Product has no available stock.',
        'http_code' => 404
    ]);
    exit;
}

echo json_encode([
    'status' => 'success',
    'data' => $responseData,
    'http_code' => 200
]);