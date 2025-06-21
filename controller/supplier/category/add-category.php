<?php


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to add a category.', 'http_code' => 405]);
    exit;
}

// json 

$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input', 'http_code' => 400]);
    exit;
}

$categoryName = isset($data['category_name']) ? trim($data['category_name']) : '';
if (empty($categoryName)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Category name is required', 'http_code' => 400]);
    exit;
}


if ($categoryModel->exists($categoryName, $_SESSION['auth']['user_id'])) {
    http_response_code(409);
    echo json_encode(['status' => 'error', 'message' => 'Category already exists', 'http_code' => 409]);
    exit;
}


$categoryId = $categoryModel->create($categoryName, $_SESSION['auth']['user_id']);
if ($categoryId > 0) {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Category added successfully', 'category_id' => $categoryId, 'http_code' => 200]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to add category', 'http_code' => 500]);
}
