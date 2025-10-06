<?php
// Test database connection for admin pages
require_once '../vendor/autoload.php';
require_once '../core/config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Test database connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Test a simple query
    $testQuery = "SELECT COUNT(*) as count FROM users WHERE deleted_at IS NULL";
    $stmt = $conn->prepare($testQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    $userCount = $result->fetch_assoc()['count'] ?? 0;
    $stmt->close();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Database connection successful',
        'data' => [
            'user_count' => $userCount,
            'mysql_version' => $conn->server_info,
            'connection_status' => 'Connected'
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage(),
        'data' => null
    ]);
}
?>
