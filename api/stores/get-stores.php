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
$input = json_decode(file_get_contents('php://input'), true);


$storeList = $storeProfileModel->getAllStores();
if ($storeList !== false) {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $storeList]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve stores']);
}