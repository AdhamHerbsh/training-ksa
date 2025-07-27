<?php

// Include the database connection file.
require('core/db/connection.php');


// If the user is already logged in, redirect them to the welcome page.
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ?page=home");
    exit;
}

// Initialize variables and error messages.
$email = $password = "";
$errors = [];

// Process form data when the form is submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Validate Email ---
    if (empty(trim($_POST["email"]))) {
        $errors[] = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // --- Validate Password ---
    if (empty(trim($_POST["password"]))) {
        $errors[] = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // --- Authenticate Credentials ---
    if (empty($errors)) {
        // Prepare SQL statement
        $sql = "SELECT id, name_en, name_ar, email, password, user_type FROM users WHERE email = ? LIMIT 1";
        $stmt = prepare_statement($sql);
        execute_statement($stmt, "s", $email);

        // Store result
        $stmt->store_result();

        // Check if email exists, if yes then verify password
        if ($stmt->num_rows == 1) {
            // Bind result variables
            $stmt->bind_result($id, $name_en, $name_ar, $db_email, $hashed_password, $user_type);
            if ($stmt->fetch()) {
                if (password_verify($password, $hashed_password)) {
                    // Password is correct, so start a new session
                    session_start();
                    $_SESSION["loggedin"] = true;
                    $_SESSION["user_id"] = $id;
                    $_SESSION["name_en"] = $name_en;
                    $_SESSION["name_ar"] = $name_ar;
                    $_SESSION["email"] = $db_email;
                    $_SESSION["user_type"] = $user_type;
                    // Redirect user to home page
                    header("location: ?page=home");
                    exit;
                } else {
                    $errors[] = "Invalid email or password.";
                }
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
        $stmt->close();
    }
    // Close connection.
    $conn->close();
}
?>

<div class="vh-100">
    <main class="container align-content-center text-center h-100 w-100 m-auto">
        <div class="form-signin overlay-box py-3 px-4 m-auto shadow border border-1 border-secondary rounded-4">
            <form action="?auth=login" method="post">
                <h1 class="mb-3 fw-normal text-white">Please Login</h1>

                <?php
                // Display errors if any exist.
                if (!empty($errors)) {
                    echo '<div class="alert alert-danger">';
                    foreach ($errors as $error) {
                        echo '<p class="mb-0">' . $error . '</p>';
                    }
                    echo '</div>';
                }
                ?>

                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="floatingInput"
                        placeholder="name@example.com" required>
                    <label for="floatingInput">Email address</label>
                </div>
                <div class="form-floating position-relative">

                    <input type="password" class="form-control" id="floatingPassword" name="password"
                        placeholder="Password" required>
                    <label for="floatingPassword">Password</label>
                    <button type="button" id="togglePassword"
                        class="btn position-absolute top-50 translate-middle-y pe-3"
                        style="background: none; border: none; cursor: pointer;">
                        <i class="bi bi-eye-slash text-primary fs-5"></i>
                    </button>
                </div>


                <div class="checkbox mb-3">
                    <label class="text-white">
                        <input type="checkbox" value="remember-me"> Remember me
                    </label>
                </div>

                <button class="w-100 btn btn-lg btn-primary" type="submit">Login</button>
                <hr>
                <a href="?auth=register" class="w-100 btn btn-lg btn-outline-secondary">Register</a>
                <hr>
                <div class="mb-3">
                    <label>
                        <a href="?auth=reset-password">Forget Password</a>
                    </label>
                </div>
                <p class="mt-5 mb-3 text-muted">© 1983 - 2025</p>
            </form>
        </div>
    </main>
</div>