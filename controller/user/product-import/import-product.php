<?php

use GuzzleHttp\Client;
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
ini_set('session.use_strict_mode', 1);

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to import product data.', 'http_code' => 405]);
    exit;
}

$request_body = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Request body is not valid JSON', 'http_code' => 400]);
    exit;
}
if (!isset($_SESSION['auth']['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please log in.', 'http_code' => 401]);
    exit;
}


$pid = isset($request_body['pid']) ? trim($request_body['pid']) : '';
if (empty($pid)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required.', 'http_code' => 400]);
    exit;
}



if (empty($pid)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required.', 'http_code' => 400]);
    exit;
}


if($productModel->is_product_imported($_SESSION['auth']['user_id'], $pid)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product already imported.', 'http_code' => 400]);
    exit;
}
