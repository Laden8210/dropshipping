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

// mysqli connection

$sql = "SELECT category_id, category_name FROM product_categories where is_deleted = 1";
$result = $conn->query($sql);
$categories = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Categories retrieved successfully', 'data' => $categories]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve categories']);
}