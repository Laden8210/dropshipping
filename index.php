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
        '' => ['file' => 'auth/login.php', 'title' => 'Login', 'auth_required' => false, 'user_type' => ''],
        'login' => ['file' => 'auth/login.php', 'title' => 'Login', 'auth_required' => false, 'user_type' => 'user'],
        'register' => ['file' => 'auth/register.php', 'title' => 'Register', 'auth_required' => false, 'user_type' => 'user'],
        'forgot-password' => ['file' => 'auth/forgot-password.php', 'title' => 'Forgot Password', 'auth_required' => false, 'user_type' => ''],
        'redirect' => ['file' => 'user/google/redirect.php', 'title' => 'Redirect', 'auth_required' => false, 'user_type' => ''],
        'dashboard' => ['file' => 'user/dashboard/index.php', 'title' => 'Dashboard', 'auth_required' => true, 'user_type' => 'user'],
        'product-import' => ['file' => 'user/product-import/index.php', 'title' => 'Product Import', 'auth_required' => true, 'user_type' => 'user'],
        'inventory' => ['file' => 'user/inventory/index.php', 'title' => 'Inventory Management', 'auth_required' => true, 'user_type' => 'user'],
        'orders' => ['file' => 'user/orders/index.php', 'title' => 'Order Management', 'auth_required' => true, 'user_type' => 'user'],
        'reports' => ['file' => 'user/reports/index.php', 'title' => 'Reports', 'auth_required' => true, 'user_type' => 'user'],
        'forex-conversion' => ['file' => 'user/forex-conversion/index.php', 'title' => 'Forex Conversion', 'auth_required' => true, 'user_type' => 'user'],
        'support' => ['file' => 'user/support/index.php', 'title' => 'Customer Support', 'auth_required' => true, 'user_type' => 'user'],
        'feedback' => ['file' => 'user/feedback/index.php', 'title' => 'Ratings & Feedback', 'auth_required' => true, 'user_type' => 'user'],
        'profile' => ['file' => 'user/profile/index.php', 'title' => 'Profile', 'auth_required' => true, 'user_type' => 'user'],
        'store' => ['file' => 'user/store/index.php', 'title' => 'Store Profile', 'auth_required' => true, 'user_type' => 'user'],
        'settings' => ['file' => 'user/settings/index.php', 'title' => 'Settings', 'auth_required' => true, 'user_type' => 'user'],

        // supplier routes
        'dashboard' => ['file' => 'supplier/dashboard/index.php', 'title' => 'Supplier Dashboard', 'auth_required' => true, 'user_type' => 'supplier'],
        
        'inventory' => ['file' => 'supplier/inventory/index.php', 'title' => 'Supplier Inventory', 'auth_required' => true, 'user_type' => 'supplier'],
        'orders' => ['file' => 'supplier/orders/index.php', 'title' => 'Supplier Orders', 'auth_required' => true, 'user_type' => 'supplier'],
        'category' => ['file' => 'supplier/category/index.php', 'title' => 'Categories Management', 'auth_required' => true, 'user_type' => 'supplier'],
        'reports' => ['file' => 'supplier/reports/index.php', 'title' => 'Supplier Reports', 'auth_required' => true, 'user_type' => 'supplier'],

        
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
