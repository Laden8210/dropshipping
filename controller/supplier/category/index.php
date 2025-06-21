<?php


ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
ini_set('session.use_strict_mode', 1);


header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

session_start();

require_once '../../../core/config.php';
require_once '../../../models/index.php';
require_once '../../../function/UIDGenerator.php';

require_once '../../../vendor/autoload.php';



if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'supplier') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Forbidden: You do not have permission to access this resource.']);
    exit;
}

$request = $_SERVER['REQUEST_METHOD'];
$request = trim($request, '/');

$request = preg_replace('/[^a-zA-Z0-9_-]/', '', $request);
$request = $request ?: '';
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($request) {


    case 'POST':
        switch ($action) {
            case 'add-category':
                require_once 'add-category.php';
                break;
        }
        break;

    case 'GET':
        switch ($action) {
            case 'get-categories':
                require_once 'get-categories.php';
                break;
            case 'get-category':
                require_once 'get-category.php';
                break;
        }
        break;

    case 'DELETE':
        switch ($action) {
            case 'delete-category':
                require_once 'delete-category.php';
                break;
        }
        break;
    
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Invalid request']);
        break;
}
