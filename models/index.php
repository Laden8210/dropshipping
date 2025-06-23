<?php 

require_once 'User.php';
require_once 'ProductModel.php';
require_once 'OrderProduct.php';
require_once 'SupplierProduct.php';
require_once 'Category.php';
require_once 'Inventory.php';
require_once 'Warehouse.php';
require_once 'StoreProfile.php';
require_once 'Order.php';

$userModel = new User($conn);
$productModel = new ProductModel($conn);
$orderProductModel = new OrderProduct($conn);
$supplierProductModel = new SupplierProduct($conn);
$categoryModel = new Category($conn);
$inventoryModel = new Inventory($conn);
$warehouseModel = new Warehouse($conn);
$storeProfileModel = new StoreProfile($conn);
$orderModel = new Order($conn);
