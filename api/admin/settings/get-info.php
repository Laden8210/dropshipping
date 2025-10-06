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
    // Get MySQL version
    $mysqlVersion = $conn->server_info;
    
    // Get disk usage (simulated)
    $diskUsage = 'Unknown';
    if (function_exists('disk_free_space') && function_exists('disk_total_space')) {
        $freeBytes = disk_free_space('.');
        $totalBytes = disk_total_space('.');
        if ($freeBytes !== false && $totalBytes !== false) {
            $usedBytes = $totalBytes - $freeBytes;
            $diskUsage = round(($usedBytes / $totalBytes) * 100, 2) . '%';
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'System information retrieved successfully',
        'data' => [
            'mysql_version' => $mysqlVersion,
            'disk_usage' => $diskUsage,
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
        ],
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve system information: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
