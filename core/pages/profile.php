<?php
// Database configuration
require('core/db/connection.php');

// Initialize variables
$user = [];
$errors = [];
$success = false;
$user_id = $_SESSION['user_id'] ?? null; // Assuming user ID is stored in session

// Handle form submission (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    // Safely get POST values and avoid undefined key warnings
    $national_id = isset($_POST['national_id']) ? $_POST['national_id'] : '';
    $name_ar     = isset($_POST['full_name_ar']) ? $_POST['full_name_ar'] : '';
    $name_en     = isset($_POST['full_name_en']) ? $_POST['full_name_en'] : '';
    $gender      = isset($_POST['gender']) ? $_POST['gender'] : '';
    $country_code = isset($_POST['country_code']) ? $_POST['country_code'] : '';
    $mobile      = isset($_POST['mobile']) ? $_POST['mobile'] : '';
    $email       = isset($_POST['email']) ? $_POST['email'] : '';

    // Validation
    if (!preg_match('/^\d{9}$/', $mobile)) {
        $errors[] = "Mobile number must be exactly 9 digits.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if ($gender !== 'Male' && $gender !== 'Female') {
        $errors[] = "Please select a valid gender.";
    }

    // Convert gender to proper case
    $gender = ucfirst(strtolower($gender));

    // Check if email already exists for other users
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "This email is already registered by another user.";
    }
    $stmt->close();

    // If no errors, update the database
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET national_id = ?, name_ar = ?, name_en = ?, gender = ?, country_code = ?, mobile = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sssssssi", $national_id, $name_ar, $name_en, $gender, $country_code, $mobile, $email, $user_id);
        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch user data from database (SELECT)
$stmt = $conn->prepare("SELECT national_id, name_ar, name_en, gender, country_code, mobile, email FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($national_id, $name_ar, $name_en, $gender, $country_code, $mobile, $email);

if ($stmt->fetch()) {
    $user = [
        'national_id' => $national_id ?: '',
        'name_ar'     => $name_ar ?: '',
        'name_en'     => $name_en ?: '',
        'gender'      => $gender ?: '',
        'country_code' => $country_code ?: '',
        'mobile'      => $mobile ?: '',
        'email'       => $email ?: ''
    ];
} else {
    header("Location: ?auth=login"); // Redirect if user not found
    exit();
}
$stmt->close();
?>

<section class="container align-content-center text-center h-100 w-100 m-auto">
    <div class="form-signin overlay-box py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4">
        <?php
        if ($success) {
            echo '<div class="alert alert-success" data-i18n="profile.update-success">Profile updated successfully.</div>';
        }
        if (!empty($errors)) {
            echo '<div class="alert alert-danger">';
            foreach ($errors as $error) {
                echo '<p class="mb-0">' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
        }
        ?>
        <form id="trainee-form" action="?page=profile" method="POST" enctype="multipart/form-data">
            <h1 class="mb-3 fw-normal text-white" data-i18n="profile.title">Personal Information</h1>

            <div class="form-floating mb-3">
                <input type="text" class="form-control editable" id="national_id" name="national_id" maxlength="10"
                    pattern="\d{10}" placeholder="National ID / Iqama" value="<?= $user['national_id']; ?>" readonly>
                <label for="national_id" data-i18n="profile.national-id">National ID / Iqama</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control editable" id="full_name_ar" name="full_name_ar"
                    placeholder="Full Name (Arabic)" value="<?= $user['name_ar']; ?>" readonly>
                <label for="full_name_ar" data-i18n="profile.name-ar">Full Name (Arabic)</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control editable" id="full_name_en" name="full_name_en"
                    placeholder="Full Name (English)" value="<?= $user['name_en']; ?>" readonly>
                <label for="full_name_en" data-i18n="profile.name-en">Full Name (English)</label>
            </div>
            <div class="form-floating mb-3">
                <select class="form-control editable" id="gender" name="gender">
                    <option value="" <?php if ($user['gender'] === '') echo 'selected'; ?> disabled data-i18n="auth.register.select-gender">Select Gender
                    </option>
                    <option value="Female" <?php if ($user['gender'] === 'Female') echo 'selected'; ?> data-i18n="profile.female">Female</option>
                    <option value="Male" <?php if ($user['gender'] === 'Male') echo 'selected'; ?> data-i18n="profile.male">Male</option>
                </select>
                <label for="gender" data-i18n="profile.gender">Gender</label>
            </div>

            <div class="row mb-3">
                <div class="col-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="country_code" name="country_code"
                            value="<?= $user['country_code']; ?>" maxlength="4" placeholder="Country Code" readonly>
                        <label for="country_code" data-i18n="profile.country-code">Code</label>
                    </div>
                </div>
                <div class="col-8">
                    <div class="form-floating">
                        <input type="text" class="form-control editable" id="mobile" name="mobile" pattern="\d{9}"
                            placeholder="Mobile Number" value="<?= $user['mobile']; ?>" readonly>
                        <label for="mobile" data-i18n="profile.mobile">Mobile Number</label>
                    </div>
                </div>
                <div class="error-message text-danger" id="mobile-error" style="display:none;" data-i18n="training-form.errors.invalid-mobile">Invalid mobile number. It must be 9 digits.</div>
            </div>

            <div class="form-floating mb-3">
                <input type="email" class="form-control editable" id="email" name="email" placeholder="Email"
                    value="<?= $user['email']; ?>" readonly>
                <label for="email" data-i18n="profile.email">Email</label>
                <div class="error-message text-danger" id="email-error" style="display:none;" data-i18n="training-form.errors.invalid-email">Please enter a valid email address.</div>
            </div>

            <button type="button" id="edit-btn" class="w-100 btn btn-lg btn-primary mb-3" data-i18n="common.edit">Edit</button>
            <button type="submit" id="save-btn" name="save_profile"
                class="w-100 btn btn-lg btn-success mb-3" data-i18n="common.save">Save</button>
            <button type="button" id="cancel-btn" class="w-100 btn btn-lg btn-outline-secondary mb-3" data-i18n="common.cancel">Cancel</button>
        </form>
    </div>
</section>