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

// Required fields
$required = ['tracking_number', 'status', 'latitude', 'longitude'];
foreach ($required as $field) {
    if (!isset($request_body[$field]) || empty($request_body[$field])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => "Field '$field' is required"]);
        exit;
    }
}

// Extract and sanitize data
$trackingNumber = $request_body['tracking_number'];
$status = $request_body['status'];
$scanTime = $request_body['scan_time'];
$latitude = (float)$request_body['latitude'];
$longitude = (float)$request_body['longitude'];

// Get current location from lat/lng
function getLocationFromCoordinates($latitude, $longitude)
{
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&zoom=18&addressdetails=1";
    $opts = ["http" => ["header" => "User-Agent: PHP"]];
    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        return "Unknown Location";
    }

    $data = json_decode($response, true);
    return $data['display_name'] ?? 'Unknown Location';
}

$remarks = '';
$locationText = getLocationFromCoordinates($latitude, $longitude);
$timeText = $scanTime ? date('Y-m-d H:i:s', strtotime($scanTime)) : date('Y-m-d H:i:s');
switch ($status) {
    case "Received":
        $remarks = "Package received at the warehouse. Location: {$locationText}. Time: {$timeText}.";
        break;
    case "In Transit":
        $remarks = "Package is in transit. Location: {$locationText}. Time: {$timeText}.";
        break;
    case "Out for Delivery":
        $remarks = "Package is out for delivery. Location: {$locationText}. Time: {$timeText}.";
        break;
    case "Delivered":
        $remarks = "Package has been delivered. Location: {$locationText}. Time: {$timeText}.";
        break;
    case "Returned":
        $remarks = "Package has been returned. Location: {$locationText}. Time: {$timeText}.";
        break;
}

try {
    $currentLocation = getLocationFromCoordinates($latitude, $longitude);


    $result = $orderShippingStatusModel->createShippingStatus(
        $remarks,
        $trackingNumber,
        $currentLocation,
        $latitude,
        $longitude
    );


    if ($result) {

        // Notify user about status update
        $order = $orderModel->getByTrackingNumber($trackingNumber);
        if ($order) {
            $userId = $order['user_id'];
            $notificationMessage = "Your order with tracking number {$trackingNumber} status updated to '{$status}'.";
            $notificationModel->create($userId, $notificationMessage);
        }

        http_response_code(201);
        echo json_encode(['status' => 'success', 'message' => 'Shipping status saved']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to save shipping status']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
