<?php


// Set headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use GET to retrieve product data.', 'http_code' => 405]);
    exit;
}

// Check if user is authenticated and has store selected
if (!isset($_SESSION['auth']['store_id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Please select a store before importing products.', 'http_code' => 400]);
    exit;
}

// Get all products
$data = $supplierProductModel->get_available_products(
    $_SESSION['auth']['user_id'],
    $_SESSION['auth']['store_id']
);

// Get filter parameters
$search = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';        

// Apply search filter
if ($search !== '') {
    $data = array_filter($data, function ($product) use ($search) {
        return stripos($product['product_name'], $search) !== false;
    });
}

// Apply category filter
if ($category !== '') {
    $data = array_filter($data, function ($product) use ($category) {
        return $product['category_name'] == $category;
    });
}

// Reindex the array to ensure sequential numeric keys
$data = array_values($data);

if (!empty($data)) {
    echo json_encode([
        'status' => 'success', 
        'data' => $data, 
        'http_code' => 200,
        'count' => count($data)
    ]);
} else {
    http_response_code(404);
    echo json_encode([
        'status' => 'error', 
        'message' => 'No products found', 
        'http_code' => 404,
        'data' => [] 
    ]);
}
?>