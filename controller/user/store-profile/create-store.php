<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to add a category.', 'http_code' => 405]);
    exit;
}



$store_name = $_POST['store_name'] ?? '';

if (empty($store_name)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Store name is required.', 'http_code' => 400]);
    exit;
}

$store_email = $_POST['store_email'] ?? '';

if (empty($store_email)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Store email is required.', 'http_code' => 400]);
    exit;
}

$store_phone = $_POST['store_phone'] ?? '';

if (empty($store_phone)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Store phone is required.', 'http_code' => 400]);
    exit;
}

$store_address = $_POST['store_address'] ?? '';

if (empty($store_address)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Store address is required.', 'http_code' => 400]);
    exit;
}

$store_description = $_POST['store_description'] ?? '';

if (empty($store_description)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Store description is required.', 'http_code' => 400]);
    exit;
}

$store_logo_url = $_FILES['store_logo_url'] ?? null;

if ($store_logo_url && $store_logo_url['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($store_logo_url['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid image type. Only JPEG, PNG, and GIF are allowed.', 'http_code' => 400]);
        exit;
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Store logo is required.', 'http_code' => 400]);
    exit;
}

if($storeProfileModel->isStoreNameExists($_SESSION['auth']['user_id'], $store_name)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Store name already exists.', 'http_code' => 400]);
    exit;
}

$uploadDir = '../../../public/images/store/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$logoFileName = uniqid('store_logo_', true) . '.' . pathinfo($store_logo_url['name'], PATHINFO_EXTENSION);


if (!move_uploaded_file($store_logo_url['tmp_name'], $uploadDir . $logoFileName)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to upload store logo.', 'http_code' => 500]);
    exit;
}

if($storeProfileModel->create(
    $_SESSION['auth']['user_id'],
    $store_name,
    $store_description,
    $logoFileName,
    $store_address,
    $store_phone,
    $store_email
)) {
    http_response_code(201);
    echo json_encode(['status' => 'success', 'message' => 'Store profile created successfully.', 'http_code' => 201]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to create store profile.', 'http_code' => 500]);
}

