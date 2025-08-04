<?php

require 'core/config.php';
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/models/index.php';

try {

    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', $_SERVER['HTTP_HOST'] !== 'localhost');
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Lax');
    session_start();

    $isLocal = $_SERVER['HTTP_HOST'] === 'localhost';
    $baseUrl = $isLocal ? 'http://localhost/dropshipping' : '';
    $request = trim(preg_replace('/[^a-zA-Z0-9_-]/', '', str_replace('/dropshipping/', '', explode('?', $_SERVER['REQUEST_URI'])[0])), '/');

    if ($request === 'redirect') {
        include_once __DIR__ . '/public/view/google/redirect.php';

        exit;
    }

    if ($request === 'print') {
        require_once __DIR__ . '/public/view/report/index.php';
        exit;
    }

    $routes = [

        '' => [
            'auth_required' => false,
            'file' => 'auth/login.php',
            'title' => 'Login'
        ],
        'login' => [
            'auth_required' => false,
            'file' => 'auth/login.php',
            'title' => 'Login'
        ],
        'register' => [
            'auth_required' => false,
            'file' => 'auth/register.php',
            'title' => 'Register'
        ],
        'forgot-password' => [
            'auth_required' => false,
            'file' => 'auth/forgot-password.php',
            'title' => 'Forgot Password'
        ],
        'redirect' => [
            'auth_required' => false,
            'file' => 'google/redirect.php',
            'title' => 'Redirect'
        ],


        'dashboard' => [
            'auth_required' => true,
            'user' => [
                'file' => 'user/dashboard/index.php',
                'title' => 'Dashboard'
            ],
            'supplier' => [
                'file' => 'supplier/dashboard/index.php',
                'title' => 'Supplier Dashboard'
            ],
            'courier' => [
                'file' => 'courier/dashboard/index.php',
                'title' => 'Courier Dashboard'
            ]
        ],
        'inventory' => [
            'auth_required' => true,
            'user' => [
                'file' => 'user/inventory/index.php',
                'title' => 'Inventory Management'
            ],
            'supplier' => [
                'file' => 'supplier/inventory/index.php',
                'title' => 'Supplier Inventory'
            ]
        ],
        'orders' => [
            'auth_required' => true,
            'user' => [
                'file' => 'user/orders/index.php',
                'title' => 'Order Management'
            ],
            'supplier' => [
            'file' => 'supplier/orders/index.php',
                'title' => 'Supplier Orders'
            ]
        ],
        'reports' => [
            'auth_required' => true,
            'user' => [
                'file' => 'user/reports/index.php',
                'title' => 'Reports'
            ],
            'supplier' => [
                'file' => 'supplier/reports/index.php',
                'title' => 'Supplier Reports'
            ]
        ],
        'track' => [
            'auth_required' => true,
            'user' => [
                'file' => 'user/track/index.php',
                'title' => 'Track Orders'
            ],
            'supplier' => [
                'file' => 'supplier/track/index.php',
                'title' => 'Supplier Track'
            ],
            'courier' => [
                'file' => 'courier/track/index.php',
                'title' => 'Courier Track'
            ]
        ],
        'settings' => [
            'auth_required' => true,
            'user' => [
                'file' => 'user/settings/index.php',
                'title' => 'Settings'
            ],
            'supplier' => [
                'file' => 'supplier/settings/index.php',
                'title' => 'Supplier Settings'
            ],
            'courier' => [
                'file' => 'courier/settings/index.php',
                'title' => 'Courier Settings'
            ]

        ],

        // User-only routes
        'product-import' => [
            'auth_required' => true,
            'user' => [
                'file' => 'user/product-import/index.php',
                'title' => 'Product Import'
            ]
        ],
        'forex-conversion' => [
            'auth_required' => true,
            'user' => [
                'file' => 'user/forex-conversion/index.php',
                'title' => 'Forex Conversion'
            ]
        ],
        'support' => [
            'auth_required' => true,
            'user' => [
                'file' => 'user/support/index.php',
                'title' => 'Customer Support'
            ]
        ],
        'feedback' => [
            'auth_required' => true,
            'user' => [
                'file' => 'user/feedback/index.php',
                'title' => 'Ratings & Feedback'
            ]
        ],
        'profile' => [
            'auth_required' => true,
            'user' => [
                'file' => 'user/profile/index.php',
                'title' => 'Profile'
            ]
        ],

        'create-store'
        => [
            'auth_required' => true,
            'user' => [
                'file' => 'user/store/create.php',
                'title' => 'Create Store'
            ]
        ],

        // Supplier-only routes
        'product' => [
            'auth_required' => true,
            'supplier' => [
                'file' => 'supplier/product/index.php',
                'title' => 'Supplier Products'
            ]
        ],
        'category' => [
            'auth_required' => true,
            'supplier' => [
                'file' => 'supplier/category/index.php',
                'title' => 'Categories Management'
            ]
        ],

        // Courier-only routes

        'deliveries' => [
            'auth_required' => true,
            'courier' => [
                'file' => 'courier/deliveries/index.php',
                'title' => 'Deliveries Management'
            ]
        ]
    ];


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

        if (
            $_SESSION['auth']['ip_address'] !== $_SERVER['REMOTE_ADDR'] ||
            $_SESSION['auth']['user_agent'] !== $_SERVER['HTTP_USER_AGENT']
        ) {
            session_unset();
            session_destroy();
            header('Location: login?error=session_tampered');
            exit;
        }


        $user = $userModel->getCurrentUser();
        $role = $user['role'];
        $name = $user['first_name'] . ' ' . $user['last_name'];

        // Role-based view selection
        if (!isset($route[$role])) {
            http_response_code(403);
            include 'public/view/error/403.php';
            exit;
        }

        $viewConfig = $route[$role];
        $title = $viewConfig['title'];
        $content = __DIR__ . '/public/view/' . $viewConfig['file'];
    } else {

        if (isset($_SESSION['auth'])) {

            $role = $_SESSION['auth']['role'];
            header('Location: dashboard');
            exit;
        }

        $title = $route['title'];
        $content = __DIR__ . '/public/view/' . $route['file'];
    }

    // Render the view
    $layout = 'app.php';
    require_once __DIR__ . '/public/view/layouts/' . $layout;
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    include 'public/view/error/500.php';
    exit;
}
