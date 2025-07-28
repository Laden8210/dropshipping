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
    echo json_encode(['status' => 'error', 'message' => 'Request method must be GET']);
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

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['product_id']) || empty($data['store_id']) || empty($data['quantity'])) {
    http_response_code(400);
    echo json_encode(['status' => "error", 'message' => 'Invalid request data']);
    exit;
}
$productId = (int)$data['product_id'];
$storeId = (int)$data['store_id'];
$quantity = (int)$data['quantity'];

if ($quantity <= 0) {
    http_response_code(400);
    echo json_encode(['status' => "error", 'message' => 'Quantity must be at least 1']);
    exit;
}

if ($cartModel->addToCart($user_id, $productId, $quantity, $storeId)) {
    http_response_code(201);
    echo json_encode(['status' => "success", 'message' => 'Item added to cart successfully']);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['status' => "error", 'message' => 'Failed to add item to cart']);
}

