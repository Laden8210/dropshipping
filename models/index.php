<?php 

require_once 'User.php';
require_once 'ProductModel.php';

$userModel = new User($conn);
$productModel = new ProductModel($conn);