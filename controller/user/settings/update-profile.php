<?php
// Update user profile
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

try {
    $userId = $_SESSION['auth']['user_id'];

    $request_body = json_decode(file_get_contents('php://input'), true);
    
    // Get form data
    $first_name = trim($request_body['first_name'] ?? '');
    $last_name = trim($request_body['last_name'] ?? '');
    $email = trim($request_body['email'] ?? '');
    $phone_number = trim($request_body['phone_number'] ?? '');
    $birth_date = $request_body['birth_date'] ?? null;
    $gender = $request_body['gender'] ?? 'male';
    $avatar_url = trim($request_body['avatar_url'] ?? '');
    
    // Validation
    if (empty($first_name) || empty($last_name) || empty($email)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'First name, last name, and email are required']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit;
    }
    
    // Check if email is already taken by another user
    $emailCheckQuery = "SELECT user_id FROM users WHERE email = ? AND user_id != ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($emailCheckQuery);
    $stmt->bind_param("ss", $email, $userId);
    $stmt->execute();
    $emailResult = $stmt->get_result();
    
    if ($emailResult->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Email is already taken by another user']);
        exit;
    }
    $stmt->close();
    
    // Update user profile
    $updateQuery = "UPDATE users SET 
                        first_name = ?, 
                        last_name = ?, 
                        email = ?, 
                        phone_number = ?, 
                        birth_date = ?, 
                        gender = ?, 
                        avatar_url = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE user_id = ? AND deleted_at IS NULL";
    
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssssss", $first_name, $last_name, $email, $phone_number, $birth_date, $gender, $avatar_url, $userId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Update session data
            $_SESSION['auth']['name'] = $first_name . ' ' . $last_name;
            $_SESSION['auth']['email'] = $email;
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone_number' => $phone_number,
                    'birth_date' => $birth_date,
                    'gender' => $gender,
                    'avatar_url' => $avatar_url
                ],
                'http_code' => 200
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'No changes made to profile']);
        }
    } else {
        throw new Exception("Failed to update profile: " . $stmt->error);
    }
    
    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update profile: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
}
?>
