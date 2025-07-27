<?php
// Connect to the database.
// Make sure this path is correct for your project structure.
require('core/db/connection.php');

// Initialize an array to hold error messages.
$errors = [];

// Check if the form was submitted using the POST method.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Sanitize and Validate Inputs ---
    $name_en = trim($_POST['name_en']);
    $name_ar = trim($_POST['name_ar']);
    $email = trim($_POST['email']);
    $national_id = trim($_POST['national_id']);
    $gender = $_POST['gender'] ?? '';
    $country_code = trim($_POST['country_code']);
    $mobile = trim($_POST['mobile']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $user_type = $_POST['user_type'] ?? '';

    // --- Form Validation ---
    if (empty($name_en)) {
        $errors[] = "Name in English is required.";
    }
    if (empty($name_ar)) {
        $errors[] = "Name in Arabic is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($national_id)) {
        $errors[] = "National ID / Iqama is required.";
    } elseif (!preg_match('/^\d{10}$/', $national_id)) {
        $errors[] = "National ID must be 10 digits.";
    }
    if (empty($gender)) {
        $errors[] = "Gender is required.";
    }
    if (empty($country_code)) {
        $errors[] = "Country code is required.";
    }
    if (empty($mobile)) {
        $errors[] = "Mobile number is required.";
    } elseif (!preg_match('/^\d{9}$/', $mobile)) {
        $errors[] = "Mobile number must be 9 digits.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if ($password !== $password_confirm) {
        $errors[] = "Passwords do not match.";
    }
    if (empty($user_type) || !in_array($user_type, ['Supervisor', 'Trainee'])) {
        $errors[] = "A valid user type must be selected.";
    }

    // --- Check for Duplicates (Email and National ID) ---
    if (empty($errors)) {
        $sql_check = "SELECT id FROM users WHERE email = ? OR national_id = ?";
        if ($stmt_check = $conn->prepare($sql_check)) {
            $stmt_check->bind_param("ss", $email, $national_id);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $errors[] = "An account with this email or National ID already exists.";
            }
            $stmt_check->close();
        }
    }

    // --- Insert User into Database ---
    if (empty($errors)) {
        // Hash the password for security.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql_insert = "INSERT INTO users (name_en, name_ar, email, national_id, gender, country_code, mobile, password, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt_insert = $conn->prepare($sql_insert)) {
            // Bind variables to the prepared statement as parameters.
            $stmt_insert->bind_param("sssssssss", $name_en, $name_ar, $email, $national_id, $gender, $country_code, $mobile, $hashed_password, $user_type);

            // Attempt to execute the prepared statement.
            if ($stmt_insert->execute()) {
                // Get the ID of the newly created user.
                $user_id = $stmt_insert->insert_id;

                // --- Start Session and Store User Data ---
                // Ensure session is started before using $_SESSION
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['name_en'] = $name_en;
                $_SESSION['email'] = $email;
                $_SESSION['user_type'] = $user_type;

                // Redirect to a welcome/dashboard page.
                header("location: ?page=home");
                exit; // Ensure no further code is executed after redirect.
            } else {
                $errors[] = "Something went wrong. Please try again later.";
            }
            // Close statement.
            $stmt_insert->close();
        }
    }
    // Close connection.
    $conn->close();
}
?>

<div class="vh-100">
    <main class="container align-content-center text-center h-100 w-100 m-auto">
        <div class="form-signin overlay-box py-3 px-4 m-auto shadow border border-1 border-secondary rounded-4">
            <form action="?auth=register" method="post">
                <h1 class="mb-3 fw-normal text-white">Create New Account</h1>

                <?php
                // Display errors if any exist.
                if (!empty($errors)) {
                    echo '<div class="alert alert-danger">';
                    foreach ($errors as $error) {
                        echo '<p class="mb-0">' . htmlspecialchars($error) . '</p>';
                    }
                    echo '</div>';
                }
                ?>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="name_en" name="name_en"
                                placeholder="Name in English" required>
                            <label for="name_en">Name in English</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="name_ar" name="name_ar"
                                placeholder="Name in Arabic" required>
                            <label for="name_ar">Name in Arabic</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="national_id" name="national_id" maxlength="10"
                        pattern="\d{10}" placeholder="National ID / Iqama" required>
                    <label for="national_id">National ID / Iqama</label>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="" selected disabled>Select Gender</option>
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                    </select>
                    <label for="gender">Gender</label>
                </div>

                <div class="row mb-3">
                    <div class="col-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="country_code" name="country_code" value="+966"
                                maxlength="4" placeholder="Code" required>
                            <label for="country_code">Code</label>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="form-floating">
                            <input type="tel" class="form-control" id="mobile" name="mobile" pattern="\d{9}"
                                placeholder="Mobile Number" required>
                            <label for="mobile">Mobile (9 digits)</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com"
                        required>
                    <label for="email">Email address</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password"
                        required>
                    <label for="password">Password</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                        placeholder="Confirm Password" required>
                    <label for="password_confirm">Confirm Password</label>
                </div>

                <input type="hidden" id="user_type" name="user_type" value="Trainee" />

                <button class="w-100 btn btn-lg btn-primary" type="submit">Register</button>
                <hr>
                <a href="?auth=login" class="w-100 btn btn-lg btn-outline-secondary">Already have an account? Login</a>
            </form>
        </div>
    </main>
</div>