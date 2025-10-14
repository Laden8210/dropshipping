<?php
date_default_timezone_set('Asia/Manila');
require_once '../../core/config.php';
require_once '../../models/index.php';
require_once '../../function/UIDGenerator.php';
require_once '../../services/EmailService.php';
require_once '../../services/TokenService.php';
require_once '../../core/config.php';

$request = $_SERVER['REQUEST_METHOD'];
$request = trim($request, '/');
$emailService = new EmailService();

$tokenService = new TokenService($conn);


$request = preg_replace('/[^a-zA-Z0-9_-]/', '', $request);
$request = $request ?: '';


switch ($request) {
    case 'POST':
        if (isset($_GET['action']) && $_GET['action'] === 'login') {
            include_once 'login.php';
        } elseif (isset($_GET['action']) && $_GET['action'] === 'register') {
            include_once 'register.php';
        } elseif (isset($_GET['action']) && $_GET['action'] === 'forgot-password') {
            include_once 'forgot-password.php';
        } elseif (isset($_GET['action']) && $_GET['action'] === 'reset-password') {
            include_once 'reset-password.php';
        } elseif (isset($_GET['action']) && $_GET['action'] === 'verify-email') {
            include_once 'verify-email.php';
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
