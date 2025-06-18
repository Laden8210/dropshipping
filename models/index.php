<?php 

require_once 'User.php';
require_once 'ProductModel.php';
require_once 'OrderProduct.php';

$userModel = new User($conn);
$productModel = new ProductModel($conn);
$orderProductModel = new OrderProduct($conn);