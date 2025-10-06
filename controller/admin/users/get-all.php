<?php
require_once '../../../vendor/autoload.php';
require_once '../../../core/config.php';
require_once '../../../models/index.php';

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
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 10;
    $offset = ($page - 1) * $limit;

    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM users WHERE deleted_at IS NULL";
    $stmt = $conn->prepare($countQuery);
    $stmt->execute();
    $totalCount = $stmt->get_result()->fetch_assoc()['total'];

    // Get users with pagination using existing database structure
    $usersQuery = "SELECT 
                        user_id, first_name, last_name, email, phone_number, 
                        role, is_active, created_at, updated_at, birth_date, gender,
                        is_google_auth, google_id, is_email_verified, avatar_url
                    FROM users 
                    WHERE deleted_at IS NULL 
                    ORDER BY created_at DESC 
                    LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($usersQuery);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        // Get last login from order creation (as a proxy for activity)
        $lastActivityQuery = "SELECT MAX(created_at) as last_activity FROM orders WHERE user_id = ?";
        $activityStmt = $conn->prepare($lastActivityQuery);
        $activityStmt->bind_param("s", $row['user_id']);
        $activityStmt->execute();
        $lastActivityResult = $activityStmt->get_result();
        $row['last_login'] = $lastActivityResult->fetch_assoc()['last_activity'] ?? null;
        $activityStmt->close();
        
        // Get user's store count if they're a user role
        if ($row['role'] === 'user') {
            $storeCountQuery = "SELECT COUNT(*) as store_count FROM store_profile WHERE user_id = ?";
            $storeStmt = $conn->prepare($storeCountQuery);
            $storeStmt->bind_param("s", $row['user_id']);
            $storeStmt->execute();
            $storeCountResult = $storeStmt->get_result();
            $row['store_count'] = $storeCountResult->fetch_assoc()['store_count'] ?? 0;
            $storeStmt->close();
        }
        
        $users[] = $row;
    }
    $stmt->close();

    echo json_encode([
        'status' => 'success',
        'message' => 'Users retrieved successfully',
        'data' => $users,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $totalCount,
            'total_pages' => ceil($totalCount / $limit)
        ],
        'http_code' => 200
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve users: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
