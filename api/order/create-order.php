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

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Request method must be POST']);
    exit;
}

// Get and validate JSON input
$request_body = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    exit;
}

// Extract JWT token from header
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

// Required fields validation
$requiredFields = [
    'total_amount', 'shipping_zip', 'shipping_country', 'shipping_country_code',
    'shipping_province', 'shipping_city', 'shipping_phone', 'shipping_customer_name',
    'shipping_address', 'from_country_code', 'items'
];
$missing = [];
foreach ($requiredFields as $field) {
    if (empty($request_body[$field])) {
        $missing[] = $field;
    }
}
if ($missing) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing fields: ' . implode(', ', $missing)]);
    exit;
}

// Validate order items
if (!is_array($request_body['items']) || count($request_body['items']) === 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'At least one order item is required']);
    exit;
}

foreach ($request_body['items'] as $item) {
    if (empty($item['product_id']) || empty($item['quantity']) || !isset($item['price'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Each item requires product_id, quantity, and price']);
        exit;
    }
}


if (empty($request_body['order_number'])) {
    $request_body['order_number'] = UIDGenerator::generateOrderNumber();
}

// Prepare order data
$orderData = [
    'user_id' => $user_id,
    'total_amount' => $request_body['total_amount'],
    'status' => $request_body['status'] ?? 'pending',
    'order_number' => $request_body['order_number'],
    'shipping_zip' => $request_body['shipping_zip'],
    'shipping_country' => $request_body['shipping_country'],
    'shipping_country_code' => $request_body['shipping_country_code'],
    'shipping_province' => $request_body['shipping_province'],
    'shipping_city' => $request_body['shipping_city'],
    'shipping_county' => $request_body['shipping_county'] ?? null,
    'shipping_phone' => $request_body['shipping_phone'],
    'shipping_customer_name' => $request_body['shipping_customer_name'],
    'shipping_address' => $request_body['shipping_address'],
    'shipping_address2' => $request_body['shipping_address2'] ?? null,
    'tax_id' => $request_body['tax_id'] ?? null,
    'remark' => $request_body['remark'] ?? null,
    'email' => $request_body['email'] ?? null,
    'consignee_id' => $request_body['consignee_id'] ?? null,
    'pay_type' => $request_body['pay_type'] ?? null,
    'shop_amount' => $request_body['shop_amount'] ?? null,
    'logistic_name' => $request_body['logistic_name'] ?? null,
    'from_country_code' => $request_body['from_country_code'],
    'house_number' => $request_body['house_number'] ?? null
];

$order_id = $orderProductModel->create($orderData, $request_body['items']);

if ($order_id === -1) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Order creation failed']);
    exit;
}

// Fetch created order details
$createdOrder = $orderProductModel->get($order_id);
$orderItems = $orderProductModel->getItems($order_id);

// Success response
http_response_code(201);
echo json_encode([
    'status' => 'success',
    'message' => 'Order created successfully',
    'order' => $createdOrder,
    'items' => $orderItems
]);