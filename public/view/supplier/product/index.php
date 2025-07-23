<?php 

$action = $_GET['action'] ?? 'index';

echo $action;

switch ($action) {
    case 'index':
        include 'list-view.php';
        break;
    case 'add':
        include 'add.php';
        break;
    case 'edit':
        $title = 'Edit Inventory Item';
        include 'edit.php';
        break;
    default:
        http_response_code(404);
        include __DIR__ . '/public/view/error/404.php';
        exit;
}