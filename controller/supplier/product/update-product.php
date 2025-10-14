<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST to update a product.', 'http_code' => 405]);
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
    $status
);

if (!$updated) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to update product', 'http_code' => 500]);
    exit;
}

// Handle product variations (size, color, weight, price, dimensions)
$variations = isset($_POST['variations']) ? json_decode($_POST['variations'], true) : null;
if ($variations && is_array($variations)) {
    // First, deactivate existing variations
    $supplierProductModel->deleteAllVariationsForProduct($productId);
    
    // Then add new variations
    foreach ($variations as $variation) {
        $size = isset($variation['size']) ? trim($variation['size']) : null;
        $color = isset($variation['color']) ? trim($variation['color']) : null;
        $weight = isset($variation['weight']) ? floatval($variation['weight']) : null;
        $length = isset($variation['length']) ? floatval($variation['length']) : null;
        $width = isset($variation['width']) ? floatval($variation['width']) : null;
        $height = isset($variation['height']) ? floatval($variation['height']) : null;
        $variationPrice = isset($variation['price']) ? floatval($variation['price']) : 0;
        $variationId = isset($variation['variation_id']) ? intval($variation['variation_id']) : null;
        
        // Only create variation if size or color is provided
        if ($size || $color) {
            // Generate SKU suffix based on size and color
            $skuSuffix = '';
            if ($size) $skuSuffix .= '-' . strtoupper(substr($size, 0, 1));
            if ($color) $skuSuffix .= '-' . strtoupper(substr($color, 0, 3));
            
            $variationId = $supplierProductModel->updateSimpleVariation(
                $productId,
                $variationId,
                $size,
                $color,
                $weight,
                $length,
                $width,
                $height,
                $variationPrice,
                $currency,
                $skuSuffix,
                0
            );

            if (!$supplierProductModel->add_price_history($productId, $variationId, $variationPrice, $currency)) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to add price history', 'http_code' => 500]);
                exit;
            }
            
            if (!$variationId) {
                error_log("Failed to create simple variation: " . json_encode($variation));
            }
        }
    }
}

// Handle image uploads
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

// Add price history


http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Product updated successfully',
    'http_code' => 200
]);
