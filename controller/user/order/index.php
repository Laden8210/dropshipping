<?php


require_once '../../core/config.php';
require_once '../../models/index.php';
require_once '../../function/UIDGenerator.php';

require_once '../../vendor/autoload.php';

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
    
            default:
        }
        break;
    
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Invalid request']);
        break;
}
