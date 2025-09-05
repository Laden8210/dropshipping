<!DOCTYPE html>
<html lang="en">

<head>
    <?php

    include 'public/view/components/head.php';
    ?>
</head>

<body class="index-page">

    <?php


    $currentRoute = $_GET['request'] ?? 'home';


    $hideTopBarRoutes = ['login', 'register', 'forgot-password', 'confirm-otp', 'reset-password', 'home', '', 'shop', 'about'];
    if (!in_array($currentRoute, $hideTopBarRoutes)) {
        include 'public/view/components/header.php';
        include 'public/view/components/aside.php';
    }



    include $content;

    ?>


    <?php
    include 'public/view/components/script.php';
    include 'public/view/components/footer.php';
    ?>

</body>

</html>