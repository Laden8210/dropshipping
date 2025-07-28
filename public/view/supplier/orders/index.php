<?php 

$action = $_GET['action'] ?? 'index';

echo $action;

switch ($action) {
    case 'index':
        include 'list-order.php';
        break;
    case 'print-invoice':
        include 'print-invoice.php';
        break;

    default:
        http_response_code(404);
        include __DIR__ . '/public/view/error/404.php';
        exit;
}