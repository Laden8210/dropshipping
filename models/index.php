<?php 

require_once 'User.php';
require_once 'ProductModel.php';
require_once 'OrderProduct.php';
require_once 'SupplierProduct.php';
require_once 'Category.php';

$userModel = new User($conn);
$productModel = new ProductModel($conn);
$orderProductModel = new OrderProduct($conn);
$supplierProductModel = new SupplierProduct($conn);
$categoryModel = new Category($conn);