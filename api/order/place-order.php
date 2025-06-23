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

// Validate JWT token
$secret_key = "your_jwt_secret_key";
try {
    $decoded = JWT::decode($jwt, new Key(trim($secret_key), 'HS256'));
    $user_id = $decoded->sub;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid token: ' . $e->getMessage()]);
    exit;
}




if (
    !isset($request_body['payment_method']) ||
    !in_array(strtolower($request_body['payment_method']), ['credit_card', 'paypal', 'bank_transfer', 'cod'])
) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid payment method']);
    exit;
}


if (!isset($request_body['subtotal']) || !is_numeric($request_body['subtotal'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid subtotal']);
    exit;
}
if (!isset($request_body['shipping']) || !is_numeric($request_body['shipping'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid shipping cost']);
    exit;
}
if (!isset($request_body['tax']) || !is_numeric($request_body['tax'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid tax amount']);
    exit;
}
if (!isset($request_body['total']) || !is_numeric($request_body['total'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid total amount']);
    exit;
}
if (!isset($request_body['products']) || !is_array($request_body['products']) || empty($request_body['products'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Products array is required']);
    exit;
}

$order_number = UIDGenerator::generateOrderNumber();

$data = $orderModel->createOrder($user_id, $order_number, $request_body);
if ($data['status'] === 'success') {
    http_response_code(201);
    echo json_encode([
        'status' => 'success',
        'message' => 'Order placed successfully',
        'order_id' => $data['order_id'],
        'order_number' => $data['order_number']
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $data['message']]);
}
