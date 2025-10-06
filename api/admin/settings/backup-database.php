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
    // Generate backup filename
    $backupFilename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $backupPath = __DIR__ . '/../../backups/' . $backupFilename;
    
    // Create backups directory if it doesn't exist
    if (!file_exists(dirname($backupPath))) {
        mkdir(dirname($backupPath), 0755, true);
    }
    
    // Get database credentials
    $host = getenv('DB_HOST');
    $username = getenv('DB_USER');
    $password = getenv('DB_PASS');
    $database = getenv('DB_NAME');
    
    // Create mysqldump command
    $command = "mysqldump --host={$host} --user={$username} --password={$password} {$database} > {$backupPath}";
    
    // Execute backup command
    $output = [];
    $returnCode = 0;
    exec($command, $output, $returnCode);
    
    if ($returnCode === 0 && file_exists($backupPath)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Database backup completed successfully',
            'data' => [
                'filename' => $backupFilename,
                'path' => $backupPath,
                'size' => filesize($backupPath)
            ],
            'http_code' => 200
        ]);
    } else {
        throw new Exception('Backup command failed');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to backup database: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
