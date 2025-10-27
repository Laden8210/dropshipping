<?php
require '../../vendor/autoload.php';
require_once '../../core/config.php';
require_once '../../models/index.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "dropshipping_8210";

// Set headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}


$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Access token missing or malformed']);
    exit;
}

$jwt = $matches[1];

try {
    $decoded = JWT::decode($jwt, new Key(trim($secret_key), 'HS256'));


    $user_id = $decoded->sub ?? null;
    $role = $decoded->role ?? null;

    if (!$user_id) {
        throw new Exception("Invalid token payload");
    }


    if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error', 
            'message' => 'No file uploaded or upload error',
            'upload_error' => $_FILES['profile_picture']['error'] ?? 'No file'
        ]);
        exit;
    }

    $uploadedFile = $_FILES['profile_picture'];


    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($uploadedFile['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.'
        ]);
        exit;
    }


    $maxFileSize = 5 * 1024 * 1024; 
    if ($uploadedFile['size'] > $maxFileSize) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error', 
            'message' => 'File too large. Maximum size is 5MB.'
        ]);
        exit;
    }


    $current_avatar_sql = "SELECT avatar_url FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($current_avatar_sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $current_avatar_url = $user_data['avatar_url'] ?? '';


    $uploadDir = '../../public/images/users/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }


    $fileExtension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
    $fileName = 'profile_' . $user_id . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;


    if (!move_uploaded_file($uploadedFile['tmp_name'], $filePath)) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Failed to save uploaded file'
        ]);
        exit;
    }


    $relativePath = $fileName;


    $update_sql = "UPDATE users SET avatar_url = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ss", $relativePath, $user_id);

    if ($stmt->execute()) {

        if (!empty($current_avatar_url) && 
            file_exists($uploadDir . basename($current_avatar_url))) {
            @unlink($uploadDir . basename($current_avatar_url));
        }

        // Get updated user data
        $user_sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($user_sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $user_result = $stmt->get_result();
        $updated_user = $user_result->fetch_assoc();

        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Profile picture uploaded successfully',
            'data' => [
                'avatar_url' => $relativePath,
                'user' => $updated_user
            ]
        ]);
    } else {
        @unlink($filePath);
        
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update profile picture in database'
        ]);
    }

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Access denied',
        'error' => $e->getMessage()
    ]);
}
?>