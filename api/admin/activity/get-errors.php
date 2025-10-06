<?php
require_once '../../vendor/autoload.php';
require_once '../../core/config.php';
require_once '../../models/index.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

session_start();

// Check if user is admin
if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Forbidden: Admin access required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get error logs (simulated data since we don't have an error_logs table)
    $errors = [
        [
            'id' => 1,
            'timestamp' => date('Y-m-d H:i:s'),
            'error_type' => 'DatabaseError',
            'message' => 'Connection timeout to database',
            'user_name' => 'System',
            'severity' => 'high'
        ],
        [
            'id' => 2,
            'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'error_type' => 'ValidationError',
            'message' => 'Invalid email format provided',
            'user_name' => 'John Doe',
            'severity' => 'medium'
        ],
        [
            'id' => 3,
            'timestamp' => date('Y-m-d H:i:s', strtotime('-4 hours')),
            'error_type' => 'FileNotFound',
            'message' => 'Product image not found',
            'user_name' => 'Jane Smith',
            'severity' => 'low'
        ],
        [
            'id' => 4,
            'timestamp' => date('Y-m-d H:i:s', strtotime('-6 hours')),
            'error_type' => 'PaymentError',
            'message' => 'Payment gateway timeout',
            'user_name' => 'System',
            'severity' => 'critical'
        ]
    ];

    echo json_encode([
        'status' => 'success',
        'message' => 'Error logs retrieved successfully',
        'data' => $errors,
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve error logs: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
