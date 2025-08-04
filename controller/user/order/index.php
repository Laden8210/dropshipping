<?php


ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
ini_set('session.use_strict_mode', 1);


header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

session_start();

if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'user') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Forbidden: You do not have permission to access this resource.']);
    exit;
}


require_once '../../../core/config.php';
require_once '../../../models/index.php';
require_once '../../../function/UIDGenerator.php';

require_once '../../../vendor/autoload.php';

$request = $_SERVER['REQUEST_METHOD'];
$request = trim($request, '/');

$request = preg_replace('/[^a-zA-Z0-9_-]/', '', $request);
$request = $request ?: '';
$action = isset($_GET['action']) ? $_GET['action'] : '';


switch ($request) {


    case 'GET':
        switch ($action) {
            case 'get-orders':
                require_once 'get-orders.php';
                break;
            case 'get-order-details':
                require_once 'get-order-details.php';
                break;
            case 'cancel-order':
                require_once 'cancel-order.php';
                break;
            case 'track-order':
                require_once 'track-order.php';
                break;

            default:
        }
        break;
    case 'POST':
        switch ($action) {
            case 'create-order':
                require_once 'create-order.php';
                break;
            case 'update-order':
                require_once 'update-order.php';
                break;

            case 'print-invoice':
                require_once 'print-invoice.php';
                break;

            default:
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Invalid request']);
        break;
}
