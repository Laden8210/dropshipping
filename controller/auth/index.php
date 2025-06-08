<?php

require_once '../../core/config.php';
require_once '../../models/index.php';
require_once '../../function/UIDGenerator.php';

$request = $_SERVER['REQUEST_METHOD'];
$request = trim($request, '/');

$request = preg_replace('/[^a-zA-Z0-9_-]/', '', $request);
$request = $request ?: '';


switch ($request) {
    case 'POST':
        if (isset($_GET['action']) && $_GET['action'] === 'login') {
            include_once 'login.php';
        } elseif (isset($_GET['action']) && $_GET['action'] === 'register') {

            include_once 'register.php';
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action',]);
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Invalid request']);
        break;
}
