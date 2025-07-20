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
    echo json_encode(['status' => 'error', 'message' => 'Request method must be POST']);
    exit;
}

$request_body = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
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


// addressJson.put("address_line", address.getAddressLine());
// addressJson.put("region", address.getRegion());
// addressJson.put("city", address.getCity());
// addressJson.put("brgy", address.getBrgy());
// addressJson.put("postal_code", address.getPostalCode());

// validate required fields

if (

    !isset($request_body['address_line'])

) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Address line is required']);
    exit;
}
if (
    !isset($request_body['region'])
) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Region is required']);
    exit;
}
if (!isset($request_body['city'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'City is required']);
    exit;
}
if (!isset($request_body['brgy'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Barangay is required']);
    exit;
}
if (!isset($request_body['postal_code'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Postal code is required']);
    exit;
}

if ($addressModel->saveAddress($user_id, $request_body)) {
    http_response_code(201);
    echo json_encode(['status' => 'success', 'message' => 'Address added successfully']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to add address']);
}
