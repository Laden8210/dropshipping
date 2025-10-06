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
    // Get all tables
    $tablesQuery = "SHOW TABLES";
    $stmt = $conn->prepare($tablesQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $optimizedTables = [];
    while ($row = $result->fetch_array()) {
        $tableName = $row[0];
        
        // Optimize each table
        $optimizeQuery = "OPTIMIZE TABLE `{$tableName}`";
        $optimizeStmt = $conn->prepare($optimizeQuery);
        $optimizeStmt->execute();
        $optimizeResult = $optimizeStmt->get_result();
        
        $optimizedTables[] = [
            'table' => $tableName,
            'status' => 'optimized'
        ];
        
        $optimizeStmt->close();
    }
    $stmt->close();

    echo json_encode([
        'status' => 'success',
        'message' => 'Database optimization completed successfully',
        'data' => [
            'optimized_tables' => $optimizedTables,
            'total_tables' => count($optimizedTables)
        ],
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to optimize database: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
