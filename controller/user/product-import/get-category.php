<?php

$sql = "SELECT category_id, category_name FROM product_categories where is_deleted = 1";
$result = $conn->query($sql);
$categories = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Categories retrieved successfully', 'data' => $categories, 'http_code' => 200]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve categories']);
}