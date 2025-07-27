<?php

// Include your database connection and helper functions
require_once('core/db/connection.php');

// Get the user ID from the session
$user_id = $_SESSION['user_id'] ?? null;

// Initialize an array for errors and success status
$errors = [];
$success = false;

// Initialize the $user array with default empty values or fetched values
// This array will hold the data to be displayed in the form
$user = [
    'national_id' => '',
    'name_ar' => '',
    'name_en' => '',
    'gender' => '',
    'country_code' => '+966', // Default for Saudi Arabia
    'mobile' => '',
    'email' => ''
];

// Check if the user is logged in
if (!$user_id) {
    echo '<div class="alert alert-danger">User not logged in.</div>';
} else {
    // --- Handle Form Submission (POST Request) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and trim all incoming POST data
        // Assign directly to $user array for consistent display after submission
        $user['national_id'] = trim($_POST['national-id'] ?? '');
        $user['name_ar'] = trim($_POST['full-name-ar'] ?? '');
        $user['name_en'] = trim($_POST['full-name-en'] ?? '');
        $user['country_code'] = trim($_POST['country-code'] ?? '+966');
        $user['mobile'] = trim($_POST['mobile'] ?? '');
        $user['email'] = trim($_POST['email'] ?? '');

        // Handle gender specifically: only 'male' or 'female', otherwise NULL
        $gender_input = trim($_POST['gender'] ?? '');
        if ($gender_input === 'male' || $gender_input === 'female') {
            $user['gender'] = $gender_input;
        } else {
            $user['gender'] = null; // Store as NULL in DB if invalid or not selected
        }

        // --- Server-side Validation ---
        // (Add more comprehensive validation as needed)
        if (empty($user['mobile'])) {
            $errors[] = "Mobile Number is required.";
        } elseif (!preg_match("/^\d{9}$/", $user['mobile'])) { // Assuming 9 digits after country code
            $errors[] = "Mobile Number must be 9 digits.";
        }

        if (empty($user['email'])) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }

        // Check if there are no validation errors
        if (empty($errors)) {
            // Prepare the SQL UPDATE statement
            // The national_id, name_ar, and name_en are typically not updated by the user
            // if they are linked to an identity system. I've included them here
            // based on your original code, but they are disabled in HTML.
            $sql = "UPDATE users SET national_id=?, name_ar=?, name_en=?, gender=?, country_code=?, mobile=?, email=? WHERE id=?";
            $stmt = prepare_statement($sql);

            // Determine the type for bind_param for gender
            // 's' for string, 'i' for integer, etc. MySQLi usually handles PHP null
            // as SQL NULL for string types, but it's good to be aware.
            $gender_param_for_db = $user['gender'];

            // Execute the prepared statement
            // Assuming your execute_statement function properly handles parameter binding
            // and types (s for string, i for integer).
            execute_statement(
                $stmt,
                "sssssssi", // String types for national_id, name_ar, name_en, gender, country_code, mobile, email; integer for user_id
                $user['national_id'],
                $user['name_ar'],
                $user['name_en'],
                $gender_param_for_db,
                $user['country_code'],
                $user['mobile'],
                $user['email'],
                $user_id
            );

            // Check if the update was successful (at least one row affected)
            if ($stmt->affected_rows > 0) {
                $success = true;
            } else {
                // If no rows were affected, it could mean no changes were made, or an error occurred.
                // You might want more granular error logging here.
                $errors[] = "No changes were made to your profile, or an error occurred during the update.";
            }

            // Close the statement
            $stmt->close();
        }
    }

    // --- Fetch User Data for Display ---
    // This section runs both on initial page load and after a form submission.
    // It ensures that the form always displays the most current data from the database.
    $sql = "SELECT national_id, name_ar, name_en, gender, country_code, mobile, email FROM users WHERE id=? LIMIT 1";
    $stmt = prepare_statement($sql);
    execute_statement($stmt, "i", $user_id); // 'i' for integer type for user_id

    $result = $stmt->get_result(); // Get the result set
    if ($result && $result->num_rows > 0) {
        $fetched_user_data = $result->fetch_assoc();
        // Overwrite the $user array with data fetched from the database
        $user = array_merge($user, $fetched_user_data);
    }
    $stmt->close();

    // Ensure all displayed fields are strings, even if NULL from DB, for htmlspecialchars
    foreach ($user as $key => $value) {
        if (is_null($value)) {
            $user[$key] = '';
        }
    }
}
?>

<section class="container align-content-center text-center h-100 w-100 m-auto">
    <div class="form-signin overlay-box py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4">
        <?php
        if ($success) {
            echo '<div class="alert alert-success">Profile updated successfully.</div>';
        }
        if (!empty($errors)) {
            echo '<div class="alert alert-danger">';
            foreach ($errors as $error) {
                echo '<p class="mb-0">' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
        }
        ?>
        <form id="trainee-form" action="?page=profile" method="post">
            <h1 class="mb-3 fw-normal text-white">Personal Information</h1>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="national-id" name="national-id" maxlength="10"
                    pattern="\d{10}" placeholder="National ID / Iqama"
                    value="<?php echo htmlspecialchars($user['national_id']); ?>" disabled>
                <label for="national-id">National ID / Iqama</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="full-name-ar" name="full-name-ar"
                    placeholder="Full Name (Arabic)" value="<?php echo htmlspecialchars($user['name_ar']); ?>" disabled>
                <label for="full-name-ar">Full Name (Arabic)</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="full-name-en" name="full-name-en"
                    placeholder="Full Name (English)" value="<?php echo htmlspecialchars($user['name_en']); ?>"
                    disabled>
                <label for="full-name-en">Full Name (English)</label>
            </div>

            <div class="form-floating mb-3">
                <select class="form-control" id="gender" name="gender" disabled>
                    <option value="" <?php if ($user['gender'] === '') echo 'selected'; ?> disabled>Select Gender
                    </option>
                    <option value="female" <?php if ($user['gender'] === 'female') echo 'selected'; ?>>Female</option>
                    <option value="male" <?php if ($user['gender'] === 'male') echo 'selected'; ?>>Male</option>
                </select>
                <label for="gender">Gender</label>
            </div>

            <div class="row mb-3">
                <div class="col-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="country-code" name="country-code"
                            value="<?php echo htmlspecialchars($user['country_code']); ?>" maxlength="4"
                            placeholder="Country Code" disabled>
                        <label for="country-code">Code</label>
                    </div>
                </div>
                <div class="col-8">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="mobile" name="mobile" pattern="\d{9}"
                            placeholder="Mobile Number" value="<?php echo htmlspecialchars($user['mobile']); ?>"
                            disabled>
                        <label for="mobile">Mobile Number</label>
                    </div>
                </div>
                <div class="error-message text-danger" id="mobile-error" style="display:none;">Invalid mobile number. It
                    must be 9 digits.</div>
            </div>

            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                    value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                <label for="email">Email</label>
                <div class="error-message text-danger" id="email-error" style="display:none;">Please enter a valid email
                    address.</div>
            </div>

            <button type="button" id="edit-btn" class="w-100 btn btn-lg btn-primary mb-3">Edit</button>
            <button type="submit" id="save-btn" class="w-100 btn btn-lg btn-success mb-3"
                style="display: none;">Save</button>
        </form>
    </div>
</section>