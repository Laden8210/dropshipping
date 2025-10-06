<?php
// Get user profile data
require_once '../../../vendor/autoload.php';
require_once '../../../core/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');

session_start();

if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'user') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Forbidden: You do not have permission to access this resource.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

try {
    $userId = $_SESSION['auth']['user_id'];
    
    // Get user profile data
    $profileQuery = "SELECT 
                        user_id, first_name, last_name, email, phone_number, 
                        birth_date, gender, avatar_url, is_active, is_email_verified, 
                        created_at, updated_at
                    FROM users 
                    WHERE user_id = ? AND deleted_at IS NULL";
    
    $stmt = $conn->prepare($profileQuery);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'User profile not found']);
        exit;
    }
    
    $profile = $result->fetch_assoc();
    $stmt->close();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Profile retrieved successfully',
        'data' => $profile,
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve profile: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
?>
