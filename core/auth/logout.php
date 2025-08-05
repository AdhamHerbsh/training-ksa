<?php

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

?>

<main class="container text-center vh-100 align-content-center">
    <div class="overlay-box col-12 col-md-6 py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4">
        <h1 class="display-5 text-white" data-i18n="auth.logout.title">Logged Out Successfully</h1>
        <p class="lead" data-i18n="auth.logout.p">You will be redirected to the homepage in 5 seconds...</p>
        <a href="?page=home" class="btn btn-info text-dark-50" data-i18n="auth.logout.btn">Return to Homepage Now</a>
    </div>
</main>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            window.location.href = '?page=home';
        }, 5000); // 5000 milliseconds = 5 seconds
    });
</script>