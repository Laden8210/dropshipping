<?php

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Request method must be GET',
        'data' => null,
        'http_code' => 405
    ]);
    exit;
}

$order_number = isset($_GET['order_number']) ? trim($_GET['order_number']) : '';
$customer_name = isset($_GET['customer_name']) ? trim($_GET['customer_name']) : '';
$order_status = isset($_GET['order_status']) ? trim($_GET['order_status']) : '';
$date_range = isset($_GET['date_range']) ? trim($_GET['date_range']) : '';

$orders = $orderProductModel->getAll();
if ($orders === false) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve orders',
        'data' => null,
        'http_code' => 500
    ]);
    exit;
}

$orders = array_filter($orders, function ($order) use ($order_number, $customer_name, $order_status, $date_range) {

    if ($order_number && stripos($order['order_number'], $order_number) === false) {
        return false;
    }
  
    if ($customer_name) {
        $customer_name_parts = preg_split('/\s+/', trim($customer_name));
        $order_name_parts = array_filter([
            $order['user']['first_name'] ?? '',
            $order['user']['middle_name'] ?? '',
            $order['user']['last_name'] ?? ''
        ]);
        $order_name_string = implode(' ', $order_name_parts);


        foreach ($customer_name_parts as $part) {
            if (stripos($order_name_string, $part) === false) {
                return false;
            }
        }
    }

    if ($order_status && stripos($order['status'], $order_status) === false) {
        return false;
    }
 
    if ($date_range) {
        $orderDate = strtotime($order['created_at']);
        $today = strtotime(date('Y-m-d'));
        switch ($date_range) {
            case 'today':
                if (date('Y-m-d', $orderDate) !== date('Y-m-d')) return false;
                break;
            case 'last_7_days':
                if ($orderDate < strtotime('-6 days', $today)) return false;
                break;
            case 'last_30_days':
                if ($orderDate < strtotime('-29 days', $today)) return false;
                break;
            case 'this_month':
                if (date('Y-m', $orderDate) !== date('Y-m')) return false;
                break;
        }
    }
    return true;
});

echo json_encode([
    'status' => 'success',
    'message' => 'Orders retrieved successfully.',
    'data' => array_values($orders),
    'http_code' => 200
]);