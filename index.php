<?php

require 'core/config.php';
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/models/index.php';

try {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
    ini_set('session.use_strict_mode', 1);
    session_start();

    $isLocal = $_SERVER['HTTP_HOST'] === 'localhost';
    $baseUrl = $isLocal ? 'http://localhost/dropshipping' : '';
    $request = trim(preg_replace('/[^a-zA-Z0-9_-]/', '', str_replace('/dropshipping/', '', explode('?', $_SERVER['REQUEST_URI'])[0])), '/');




    $routes = [
        '' => ['file' => 'auth/login.php', 'title' => 'Login', 'auth_required' => false],
        'login' => ['file' => 'auth/login.php', 'title' => 'Login', 'auth_required' => false],
        'register' => ['file' => 'auth/register.php', 'title' => 'Register', 'auth_required' => false],
        'forgot-password' => ['file' => 'auth/forgot-password.php', 'title' => 'Forgot Password', 'auth_required' => false],
        'redirect' => ['file' => 'google/redirect.php', 'title' => 'Redirect', 'auth_required' => false],
        'dashboard' => ['file' => 'dashboard/index.php', 'title' => 'Dashboard', 'auth_required' => true],
     
        'product-import' => ['file' => 'product-import/index.php', 'title' => 'Product Import', 'auth_required' => true],
        'inventory' => ['file' => 'inventory/index.php', 'title' => 'Inventory Management', 'auth_required' => true],
        'orders' => ['file' => 'orders/index.php', 'title' => 'Order Management', 'auth_required' => true],
        'reports' => ['file' => 'reports/index.php', 'title' => 'Reports', 'auth_required' => true],
        'forex-conversion' => ['file' => 'forex-conversion/index.php', 'title' => 'Forex Conversion', 'auth_required' => true],
        'support' => ['file' => 'support/index.php', 'title' => 'Customer Support', 'auth_required' => true],
        'feedback' => ['file' => 'feedback/index.php', 'title' => 'Ratings & Feedback', 'auth_required' => true],
        'profile' => ['file' => 'profile/index.php', 'title' => 'Profile', 'auth_required' => true],

        'settings' => ['file' => 'settings/index.php', 'title' => 'Settings', 'auth_required' => true],
    ];



    $layout = 'app.php';

    if ($request === 'logout') {
        session_unset();
        session_destroy();
        header('Location: login');
        exit;
    }

    if (!isset($routes[$request])) {
        http_response_code(404);
        include 'public/view/error/404.php';
        exit;
    }

    $route = $routes[$request];

    if ($route['auth_required']) {
        if (!isset($_SESSION['auth'])) {
            header('Location: login');
            exit;
        }

        if ($_SESSION['auth']['ip_address'] !== $_SERVER['REMOTE_ADDR'] || $_SESSION['auth']['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            session_unset();
            session_destroy();
            header('Location: login?error=session_tampered');
            exit;
        }

        $user = $userModel->getCurrentUser();
        $name = $user['first_name'] . ' ' . $user['last_name'];
        $role = $user['role'];
    }

    if (isset($_SESSION['auth']) && in_array($request, ['login', 'register', 'forgot-password', ''])) {
        header('Location: dashboard');
        exit;
    }

    $title = $route['title'];
    $content = __DIR__ . '/public/view/' . $route['file'];
    require_once __DIR__ . '/public/view/layouts/' . ($route['layout'] ?? $layout);
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    include 'public/view/error/500.php';
    exit;
}
