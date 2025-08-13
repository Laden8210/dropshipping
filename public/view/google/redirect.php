  <?php

    require 'vendor/autoload.php';
    require_once 'core/config.php';
    require_once 'function/UIDGenerator.php';

    require_once 'models/User.php';

    $user = new User($conn);


    $client = new Google\Client();
    $client->setClientId('408096805493-cfatjhsa5q0aubs53d6862d2ccdjs76u.apps.googleusercontent.com');
    $client->setClientSecret('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');
    $client->setRedirectUri('http://localhost/dropshipping/redirect');

    if (!isset($_GET['code'])) {
        exit('No authorization code provided.');
    }

    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        exit('Error fetching access token: ' . $token['error']);
    }
    $client->setAccessToken($token['access_token']);
    $oauth2 = new Google\Service\Oauth2($client);
    $userInfo = $oauth2->userinfo->get();
    if (!$userInfo) {
        exit('Failed to retrieve user information.');
    }

    $action =  $_GET['state'];


    if ($action === '') {
        exit('Invalid action specified.');
    }

    if ($action !== 'register' && $action !== 'login') {
        exit('Invalid action specified. Only "register" and "login" are allowed.');
    }


    if ($action === 'login') {

        echo 'Logging in with Google...<br>';
        if (!isset($userInfo->id) || !isset($userInfo->email)) {
            exit('Google ID or email not found in user information.');
        }
        echo 'Google ID: ' . $userInfo->id . '<br>';

        print_r($userInfo);

        $userInfo = $user->loginWithGoogle($userInfo->id);



        if ($userInfo) {

            $_SESSION['auth'] = [
                'user_id' => $userInfo['user_id'],
                'email' => $userInfo['email'],
                'role' => $userInfo['role'],
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT']
            ];
            header('Location: dashboard');
        } else {
            echo 'User not found. Please register first.';
        }

        exit;
    }


    if ($action === 'register') {



        $email = isset($userInfo->email) ? $userInfo->email : '';
        if ($user->isEmailRegistered($email)) {
            echo 'Email is already registered. Please log in instead.';
            exit;
        }

        if ($user->isGoogleIdRegistered($userInfo->id)) {
            echo 'Google ID is already registered. Please log in instead.';
            exit;
        }

        $response = $user->google_register($userInfo);

        if ($response['status'] === 'success') {
            header('Location: dashboard');
        } else {
            echo 'Error: ' . $response['message'];
        }
    }


    ?>
