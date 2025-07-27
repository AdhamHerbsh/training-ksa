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
    <div class="overlay-box py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4">
        <h1 class="display-5 text-white">Thanks 🙋‍♂️ Come Back Again</h1>
        <a href="?page=home" class="btn btn-info fs-4 fw-bold">Back ⬅️</a>
    </div>
</main>