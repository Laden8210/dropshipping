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
    case 'POST':
        switch ($action) {
            case 'import-product':
                
                require_once 'import-product.php';
      
                break;

            default:
                http_response_code(404);
                echo json_encode(['error' => 'Invalid action']);
        }

        break;


    case 'GET':



        switch ($action) {
            case 'search-product':
                require_once 'retrieve-product.php';
                break;
            case 'single-product':
                require_once 'single-product.php';
                break;
            default:
                
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Invalid request']);
        break;
}
