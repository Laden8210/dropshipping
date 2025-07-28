<?php 

$action = $_GET['action'] ?? 'index';


switch ($action) {

    case 'print-invoice':
        include 'print-invoice.php';
        break;

    default:
        http_response_code(404);
        include __DIR__ . '/public/view/error/404.php';
        exit;
}