<?php


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to add a category.', 'http_code' => 405]);
    exit;
}

$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
if (!$productId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required for update', 'http_code' => 400]);
    exit;
}


$productName = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
if (empty($productName)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product name is required', 'http_code' => 400]);
    exit;
}
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
if (empty($category)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Category is required', 'http_code' => 400]);
    exit;
}

$price = isset($_POST['price']) ? trim($_POST['price']) : '';
if (empty($price) || !is_numeric($price)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Valid price is required', 'http_code' => 400]);
    exit;
}
$currency = isset($_POST['currency']) ? trim($_POST['currency']) : '';
if (empty($currency)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Currency is required', 'http_code' => 400]);
    exit;
}

$status = isset($_POST['status']) ? trim($_POST['status']) : '';
if (empty($status)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Status is required', 'http_code' => 400]);
    exit;
}

$product_weight = isset($_POST['product_weight']) ? trim($_POST['product_weight']) : '';
if (empty($product_weight) || !is_numeric($product_weight)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Valid product weight is required', 'http_code' => 400]);
    exit;
}

$description = isset($_POST['description']) ? trim($_POST['description']) : '';
if (empty($description)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Description is required', 'http_code' => 400]);
    exit;
}



$user_id = isset($_SESSION['auth']['user_id']) ? $_SESSION['auth']['user_id'] : null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please log in.', 'http_code' => 401]);
    exit;
}

$updated = $supplierProductModel->update_product(
    $productId,
    $user_id,
    $productName,
    $category,
    $description,
    $status,
    $product_weight
);

if (!$updated) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to update product', 'http_code' => 500]);
    exit;
}


$uploadDir = '../../../public/images/products/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$productImage = $_FILES['product_image'] ?? null;
if ($productImage && $productImage['error'] === UPLOAD_ERR_OK) {
    $extension = pathinfo($productImage['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid('product_', true) . '.' . $extension;
    $productImagePath = $uploadDir . $newFileName;

    if (move_uploaded_file($productImage['tmp_name'], $productImagePath)) {
        if (!$supplierProductModel->add_product_image($productId, $newFileName, true)) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to save new primary image', 'http_code' => 500]);
            exit;
        }
    }
}


$productImages = $_FILES['product_images'] ?? null;
if ($productImages && is_array($productImages['name'])) {
    foreach ($productImages['name'] as $index => $imageName) {
        if ($productImages['error'][$index] === UPLOAD_ERR_OK) {
            $imageTmpPath = $productImages['tmp_name'][$index];
            $extension = pathinfo($imageName, PATHINFO_EXTENSION);
            $newFileName = uniqid('product_', true) . '.' . $extension;
            $imagePath = $uploadDir . $newFileName;

            if (move_uploaded_file($imageTmpPath, $imagePath)) {
                if (!$supplierProductModel->add_product_image($productId, $newFileName, false)) {
                    http_response_code(500);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Failed to save additional image',
                        'http_code' => 500
                    ]);
                    exit;
                }
            }
        }
    }
}


if (!$supplierProductModel->add_price_history($productId, $price, $currency)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to add price history', 'http_code' => 500]);
    exit;
}


http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Product updated successfully',
    'http_code' => 200
]);
