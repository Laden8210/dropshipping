<?php


if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use DELETE to remove a category.', 'http_code' => 405]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input', 'http_code' => 400]);
    exit;
}

$categoryId = isset($data['category_id']) ? intval($data['category_id']) : 0;
if ($categoryId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid category ID', 'http_code' => 400]);
    exit;
}
if (!$categoryModel->existsById($categoryId, $_SESSION['auth']['user_id'])) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Category not found', 'http_code' => 404]);
    exit;
}
if ($categoryModel->delete($categoryId, $_SESSION['auth']['user_id'])) {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Category deleted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete category', 'http_code' => 500]);
}
