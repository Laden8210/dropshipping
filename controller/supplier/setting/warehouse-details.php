<?php 

$user_id = $_SESSION['auth']['user_id'];

$warehouses = $warehouseModel->getWarehousesByUser($user_id);
if ($warehouses) {
    echo json_encode(['status' => 'success', 'data' => $warehouses, 'message' => 'Warehouses retrieved successfully.', 'http_code' => 200]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No warehouses found for this user.']);
}