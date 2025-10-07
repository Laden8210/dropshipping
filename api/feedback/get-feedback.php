<?php

require_once '../../core/config.php';
require_once '../../models/index.php';
require_once '../../function/UIDGenerator.php';
require_once '../../vendor/autoload.php';

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

$request_body = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    exit;
}

$product_id = $request_body['product_id'] ?? '';

$sql = "SELECT * FROM user_feedback
JOIN users ON user_feedback.user_id = users.user_id
WHERE user_feedback.product_id = ?";



$smtp = $conn->prepare($sql);
$smtp->bind_param("s", $product_id);

if ($smtp === false) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database query preparation failed']);
    exit;
}
$smtp->execute();
$result = $smtp->get_result();  
$data = $result->fetch_all(MYSQLI_ASSOC);
$smtp->close();


if (!$data) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve user feedback']);
    exit;
}

echo json_encode(['status' => 'success', 'data' => $data, 'message' => 'User feedback retrieved successfully']);
