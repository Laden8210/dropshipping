<?php


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to add a category.', 'http_code' => 405]);
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
$productImage = isset($_FILES['product_image']) ? $_FILES['product_image'] : null;
if (!$productImage || $productImage['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product image is required', 'http_code' => 400]);
    exit;
}
$productImages = isset($_FILES['product_images']) ? $_FILES['product_images'] : null;

if ($productImages && is_array($productImages['error'])) {
    foreach ($productImages['error'] as $index => $error) {
        if ($error !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => "Error uploading image at index $index",
                'http_code' => 400
            ]);
            exit;
        }
    }
}


$user_id = isset($_SESSION['auth']['user_id']) ? $_SESSION['auth']['user_id'] : null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please log in.', 'http_code' => 401]);
    exit;
}

$productSKU = UIDGenerator::generateProductSKU();

$data = $supplierProductModel->create_product(
    $user_id,
    $productName,
    $productSKU,
    $category,
    $description,
    $status,
    $product_weight
);

if (!$data) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to create product', 'http_code' => 500]);
    exit;
}

// Handle product image upload
$uploadDir = '../../../public/images/products/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$extension = pathinfo($productImage['name'], PATHINFO_EXTENSION);
$newFileName = uniqid('product_', true) . '.' . $extension;
$productImagePath = $uploadDir . $newFileName;
if (!move_uploaded_file($productImage['tmp_name'], $productImagePath)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to upload product image', 'http_code' => 500]);
    exit;
}


if($supplierProductModel->add_product_image($data['pid'], $newFileName, true)) {
    $data['product_image'] = $newFileName;
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to save product image in database', 'http_code' => 500]);
    exit;
}

// Handle additional product images
if ($productImages && is_array($productImages['name'])) {
    foreach ($productImages['name'] as $index => $imageName) {
        if ($productImages['error'][$index] === UPLOAD_ERR_OK) {
            $imageTmpPath = $productImages['tmp_name'][$index];

            $extension = pathinfo($imageName, PATHINFO_EXTENSION);
            $newFileName = uniqid('product_', true) . '.' . $extension;
            $imagePath = $uploadDir . $newFileName;

            if (move_uploaded_file($imageTmpPath, $imagePath)) {
                if (!$supplierProductModel->add_product_image($data['pid'], $newFileName, false)) {
                    http_response_code(500);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Failed to save additional product image in database',
                        'http_code' => 500
                    ]);
                    exit;
                }
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => "Failed to upload additional image at index $index",
                    'http_code' => 500
                ]);
                exit;
            }
        }
    }
}


// price 
if (!$supplierProductModel->add_price_history($data['pid'], $price, $currency)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to add price history', 'http_code' => 500]);
    exit;
}



http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Product added successfully',
    'data' => $data,
    'http_code' => 200
]);