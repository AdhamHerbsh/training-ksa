<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/logo/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/logo/favicon/favicon-16x16.png">
    <link rel="shortcut icon" href="assets/img/logo/favicon/favicon.ico">

    <link rel="stylesheet" href="lib/aos/aos.css">
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap-icons.css">

    <link rel="stylesheet" href="assets/css/style.css">

    <title data-i18n="page.title">إدارة التدريب - مدينة الملك فهد الطبية</title>

</head>

<body>


    <div id="preloader">
        <div class="spinner spinner-border text-primary" role="status">
        </div>
    </div>

    <?php

    // Routing logic
    $page = isset($_GET['page']) ? $_GET['page'] : null;
    $auth = isset($_GET['auth']) ? $_GET['auth'] : null;

    if ($page === null && $auth === null) {
        // User on index, show home page
        include('core/layout/navmenu.php');
        require('core/layout/header.php');
        echo '<main class="container">';
        require('core/pages/home.php');
        echo '</main>';
        require('core/layout/footer.php');
    } elseif ($auth === 'login') {
        // User on login, show login page
        require('core/auth/login.php');
    } elseif ($page !== null) {
        // User requested a page
        $file = "core/pages/$page.php";
        if (file_exists($file)) {
            include('core/layout/navmenu.php');
            require('core/layout/header.php');
            echo '<main class="container">';
            require($file);
            echo '</main>';
            require('core/layout/footer.php');
        } else {
            require('core/layout/404-not-found.php');
        }
    } elseif ($auth !== null) {
        // User requested an auth page
        $file = "core/auth/$auth.php";
        if (file_exists($file)) {
            require($file);
        } else {
            require('core/layout/404-not-found.php');
        }
    } else {
        // Unknown keyword, show 404
        require('core/layout/404-not-found.php');
    }

    ?>

    <script src="assets/js/main.js"></script>
    <script src="lib/aos/aos.js"></script>
    <script src="lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>