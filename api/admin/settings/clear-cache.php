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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

try {
    // Clear various caches
    $clearedItems = [];
    
    // Clear PHP opcache if available
    if (function_exists('opcache_reset')) {
        opcache_reset();
        $clearedItems[] = 'PHP OpCache';
    }
    
    // Clear session files (optional - be careful with this)
    // session_destroy();
    // $clearedItems[] = 'Session files';
    
    // Clear temporary files
    $tempDir = sys_get_temp_dir();
    if (is_dir($tempDir)) {
        $files = glob($tempDir . '/*');
        $clearedCount = 0;
        foreach ($files as $file) {
            if (is_file($file) && (time() - filemtime($file)) > 3600) { // Older than 1 hour
                unlink($file);
                $clearedCount++;
            }
        }
        $clearedItems[] = "Temporary files ({$clearedCount} files)";
    }
    
    // Clear application-specific cache (if you have one)
    $cacheDir = __DIR__ . '/../../cache/';
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . '*');
        $clearedCount = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $clearedCount++;
            }
        }
        $clearedItems[] = "Application cache ({$clearedCount} files)";
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Cache cleared successfully',
        'data' => [
            'cleared_items' => $clearedItems,
            'timestamp' => date('Y-m-d H:i:s')
        ],
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to clear cache: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
