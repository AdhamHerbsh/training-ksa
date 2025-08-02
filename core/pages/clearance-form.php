<?php

// Database configuration
require('core/db/connection.php');

// --- Security and Authentication Checks ---
// Check if user is authenticated
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ?auth=login"); // Redirect to login page
    exit();
}

// Check if user is a Trainee
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Trainee') {
    header("Location: ?page=home"); // Redirect if not a Trainee
    exit();
}

$user_id = $_SESSION['user_id']; // Get user_id from session

$errors = [];
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and Validate Input Data
    $name_ar = trim(filter_input(INPUT_POST, 'name_ar', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $name_en = trim(filter_input(INPUT_POST, 'name_en', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $id_iqama_number = trim(filter_input(INPUT_POST, 'id_iqama_number', FILTER_SANITIZE_NUMBER_INT)); // Only numbers
    $nationality = trim(filter_input(INPUT_POST, 'nationality', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $country_code = trim(filter_input(INPUT_POST, 'country_code', FILTER_SANITIZE_FULL_SPECIAL_CHARS)); // Should be '+966'
    $mobile_number = trim(filter_input(INPUT_POST, 'mobile_number', FILTER_SANITIZE_NUMBER_INT)); // Only numbers
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $department = trim(filter_input(INPUT_POST, 'department', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $job_title = trim(filter_input(INPUT_POST, 'job_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $submission_date = trim(filter_input(INPUT_POST, 'submission_date', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $work_card_expiry_date = trim(filter_input(INPUT_POST, 'work_card_expiry_date', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $has_housing = trim(filter_input(INPUT_POST, 'has_housing', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $administration_name = trim(filter_input(INPUT_POST, 'administration_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    // Basic Validation
    if (empty($name_ar)) {
        $errors[] = $lang['clearance-form']['errors']['name-ar-required'] ?? "Name in Arabic is required.";
    }
    if (empty($name_en)) {
        $errors[] = $lang['clearance-form']['errors']['name-en-required'] ?? "Name in English is required.";
    }
    if (empty($id_iqama_number) || strlen($id_iqama_number) !== 10) {
        $errors[] = $lang['clearance-form']['errors']['id-iqama-number-invalid'] ?? "ID/Iqama Number must be 10 digits.";
    }
    if (empty($nationality)) {
        $errors[] = $lang['clearance-form']['errors']['nationality-required'] ?? "Nationality is required.";
    }
    if (empty($mobile_number) || strlen($mobile_number) !== 9) {
        $errors[] = $lang['clearance-form']['errors']['mobile-number-invalid'] ?? "Mobile Number must be 9 digits.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = $lang['clearance-form']['errors']['email-invalid'] ?? "A valid Email Address is required.";
    }
    if (empty($department)) {
        $errors[] = $lang['clearance-form']['errors']['department-required'] ?? "Department/Administration is required.";
    }
    if (empty($job_title)) {
        $errors[] = $lang['clearance-form']['errors']['job-title-required'] ?? "Job Title is required.";
    }
    if (empty($submission_date)) {
        $errors[] = $lang['clearance-form']['errors']['submission-date-required'] ?? "Submission Date is required.";
    }
    if (empty($work_card_expiry_date)) {
        $errors[] = $lang['clearance-form']['errors']['work-card-expiry-date-required'] ?? "Work Card Expiry Date is required.";
    }
    if (empty($has_housing)) {
        $errors[] = $lang['clearance-form']['errors']['has-housing-required'] ?? "Housing availability is required.";
    }
    if (empty($administration_name)) {
        $errors[] = $lang['clearance-form']['errors']['administration-name-required'] ?? "Administration Name is required.";
    }

    // 2. Handle File Uploads
    $upload_dir = 'Uploads/clearance_documents/'; // Define your upload directory
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true); // Create directory if it doesn't exist
    }

    $id_photo_path = null;
    if (isset($_FILES['id_photo']) && $_FILES['id_photo']['error'] == UPLOAD_ERR_OK) {
        $file_name = uniqid('id_photo_') . '_' . basename($_FILES['id_photo']['name']);
        $target_file = $upload_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type and size
        $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
        if (!in_array($imageFileType, $allowed_types)) {
            $errors[] = $lang['clearance-form']['errors']['id-photo-format'] ?? "Only PDF, JPG, JPEG, PNG files are allowed for ID Photo.";
        } elseif ($_FILES['id_photo']['size'] > 5 * 1024 * 1024) { // Max 5MB
            $errors[] = $lang['clearance-form']['errors']['id-photo-size'] ?? "ID Photo file is too large. Max 5MB allowed.";
        } else {
            if (move_uploaded_file($_FILES['id_photo']['tmp_name'], $target_file)) {
                $id_photo_path = $target_file;
            } else {
                $errors[] = $lang['clearance-form']['errors']['id-photo-upload-failed'] ?? "Failed to upload ID Photo.";
            }
        }
    }

    $work_card_path = null;
    if (isset($_FILES['work_card']) && $_FILES['work_card']['error'] == UPLOAD_ERR_OK) {
        $file_name = uniqid('work_card_') . '_' . basename($_FILES['work_card']['name']);
        $target_file = $upload_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type and size
        $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
        if (!in_array($imageFileType, $allowed_types)) {
            $errors[] = $lang['clearance-form']['errors']['work-card-format'] ?? "Only PDF, JPG, JPEG, PNG files are allowed for Work Card.";
        } elseif ($_FILES['work_card']['size'] > 5 * 1024 * 1024) { // Max 5MB
            $errors[] = $lang['clearance-form']['errors']['work-card-size'] ?? "Work Card file is too large. Max 5MB allowed.";
        } else {
            if (move_uploaded_file($_FILES['work_card']['tmp_name'], $target_file)) {
                $work_card_path = $target_file;
            } else {
                $errors[] = $lang['clearance-form']['errors']['work-card-upload-failed'] ?? "Failed to upload Work Card.";
            }
        }
    }

    // 3. Insert into Database if no errors
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO clearance_forms (user_id, name_ar, name_en, id_iqama_number, nationality, country_code, mobile_number, email, department, job_title, submission_date, work_card_expiry_date, has_housing, id_photo_path, work_card_path, administration_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("isssssssssssssss", $user_id, $name_ar, $name_en, $id_iqama_number, $nationality, $country_code, $mobile_number, $email, $department, $job_title, $submission_date, $work_card_expiry_date, $has_housing, $id_photo_path, $work_card_path, $administration_name);

            if ($stmt->execute()) {
                $success = $lang['clearance-form']['success-message'] ?? "Clearance form submitted successfully!";
                // Optional: Clear form fields after successful submission
                header("Location: ?page=clearance-form&success=1"); // Redirect to prevent re-submission
                exit();
            } else {
                $errors[] = $lang['clearance-form']['errors']['database-error'] ?? "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors[] = $lang['clearance-form']['errors']['database-prepare-error'] ?? "Failed to prepare the database statement: " . $conn->error;
        }
    }
}

// Ensure database connection is closed at the end of the script
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}

?>

<?php if (isset($_GET['success']) && $_GET['success'] == 1) : ?>
<section class="container text-center align-content-center">
    <div class="overlay-box col-12 col-md-6 py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4">
        <h1 class="display-5 text-white" data-i18n="clearance-form.success-message">
            <?php echo $lang['clearance-form']['success-message'] ?? 'Form Submitted Successfully'; ?></h1>
    </div>
</section>
<?php else: ?>

<section class="container align-content-center h-100 w-100 m-auto">
    <div class="overlay-box py-4 px-4 m-auto shadow border border-1 border-secondary rounded-4">
        <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm(event)">
            <h1 class="h3 mb-4 fw-normal text-white text-center" data-i18n="clearance-form.form-title">
                <?php echo $lang['clearance-form']['form-title'] ?? 'Clearance Form'; ?></h1>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                <p class="error mb-0"><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="mb-4">
                <h4 class="text-white mb-3" data-i18n="clearance-form.personal-info-heading">
                    <?php echo $lang['clearance-form']['personal-info-heading'] ?? 'Personal Information'; ?></h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="name_en" name="name_en"
                                data-i18n="[placeholder]clearance-form.name-en-placeholder" required
                                value="<?php echo htmlspecialchars($_POST['name_en'] ?? ''); ?>">
                            <label for="name_en"
                                data-i18n="clearance-form.name-en"><?php echo $lang['clearance-form']['name-en'] ?? 'Name in English'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="name_ar" name="name_ar"
                                data-i18n="[placeholder]clearance-form.name-ar-placeholder" required
                                value="<?php echo htmlspecialchars($_POST['name_ar'] ?? ''); ?>">
                            <label for="name_ar"
                                data-i18n="clearance-form.name-ar"><?php echo $lang['clearance-form']['name-ar'] ?? 'Name in Arabic'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="id_iqama_number" name="id_iqama_number"
                                data-i18n="[placeholder]clearance-form.id-iqama-number-placeholder" maxlength="10"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')" required
                                value="<?php echo htmlspecialchars($_POST['id_iqama_number'] ?? ''); ?>">
                            <label for="id_iqama_number"
                                data-i18n="clearance-form.id-iqama-number"><?php echo $lang['clearance-form']['id-iqama-number'] ?? 'ID/Iqama Number'; ?></label>
                            <span class="note"
                                data-i18n="clearance-form.id-iqama-number-note"><?php echo $lang['clearance-form']['id-iqama-number-note'] ?? '* Must be 10 digits'; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-control" id="nationality" name="nationality" required>
                                <option value="" data-i18n="clearance-form.select-nationality">
                                    <?php echo $lang['clearance-form']['select-nationality'] ?? '-- Select Nationality --'; ?>
                                </option>
                                <option value="Saudi"
                                    <?php echo (($_POST['nationality'] ?? '') == 'Saudi') ? 'selected' : ''; ?>
                                    data-i18n="clearance-form.saudi">
                                    <?php echo $lang['clearance-form']['saudi'] ?? 'Saudi'; ?></option>
                                <option value="Non-Saudi"
                                    <?php echo (($_POST['nationality'] ?? '') == 'Non-Saudi') ? 'selected' : ''; ?>
                                    data-i18n="clearance-form.non-saudi">
                                    <?php echo $lang['clearance-form']['non-saudi'] ?? 'Non-Saudi'; ?></option>
                            </select>
                            <label for="nationality"
                                data-i18n="clearance-form.nationality"><?php echo $lang['clearance-form']['nationality'] ?? 'Nationality'; ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="text-white mb-3" data-i18n="clearance-form.contact-info-heading">
                    <?php echo $lang['clearance-form']['contact-info-heading'] ?? 'Contact Information'; ?></h4>
                <div class="row g-3">
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="country_code" name="country_code" value="+966"
                                maxlength="5" data-i18n="[placeholder]clearance-form.country-code-placeholder" required
                                readonly>
                            <label for="country_code"
                                data-i18n="clearance-form.country-code"><?php echo $lang['clearance-form']['country-code'] ?? 'Country Code'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="mobile_number" name="mobile_number"
                                data-i18n="[placeholder]clearance-form.mobile-number-placeholder" maxlength="9"
                                pattern="\d{9}" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required
                                value="<?php echo htmlspecialchars($_POST['mobile_number'] ?? ''); ?>">
                            <label for="mobile_number"
                                data-i18n="clearance-form.mobile-number"><?php echo $lang['clearance-form']['mobile-number'] ?? 'Mobile Number'; ?></label>
                            <span class="note"
                                data-i18n="clearance-form.mobile-number-note"><?php echo $lang['clearance-form']['mobile-number-note'] ?? '* Must be 9 digits after the country code'; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email"
                                data-i18n="[placeholder]clearance-form.email-placeholder" required
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            <label for="email"
                                data-i18n="clearance-form.email"><?php echo $lang['clearance-form']['email'] ?? 'Email Address'; ?></label>
                            <span class="note"
                                data-i18n="clearance-form.email-note"><?php echo $lang['clearance-form']['email-note'] ?? '* Please enter a valid email address'; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="text-white mb-3" data-i18n="clearance-form.professional-info-heading">
                    <?php echo $lang['clearance-form']['professional-info-heading'] ?? 'Professional Information'; ?>
                </h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="department" name="department"
                                data-i18n="[placeholder]clearance-form.department-placeholder" required
                                value="<?php echo htmlspecialchars($_POST['department'] ?? ''); ?>">
                            <label for="department"
                                data-i18n="clearance-form.department"><?php echo $lang['clearance-form']['department'] ?? 'Department/Administration'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="job_title" name="job_title"
                                data-i18n="[placeholder]clearance-form.job-title-placeholder" required
                                value="<?php echo htmlspecialchars($_POST['job_title'] ?? ''); ?>">
                            <label for="job_title"
                                data-i18n="clearance-form.job-title"><?php echo $lang['clearance-form']['job-title'] ?? 'Job Title'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="submission_date" name="submission_date" required
                                value="<?php echo htmlspecialchars($_POST['submission_date'] ?? date('Y-m-d')); ?>">
                            <label for="submission_date"
                                data-i18n="clearance-form.submission-date"><?php echo $lang['clearance-form']['submission-date'] ?? 'Submission Date'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="work_card_expiry_date"
                                name="work_card_expiry_date" required
                                value="<?php echo htmlspecialchars($_POST['work_card_expiry_date'] ?? ''); ?>">
                            <label for="work_card_expiry_date"
                                data-i18n="clearance-form.work-card-expiry-date"><?php echo $lang['clearance-form']['work-card-expiry-date'] ?? 'Work Card Expiry Date'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-control" id="has_housing" name="has_housing" required>
                                <option value="" data-i18n="clearance-form.select-option">
                                    <?php echo $lang['clearance-form']['select-option'] ?? '-- Select Option --'; ?>
                                </option>
                                <option value="Yes"
                                    <?php echo (($_POST['has_housing'] ?? '') == 'Yes') ? 'selected' : ''; ?>
                                    data-i18n="clearance-form.yes">
                                    <?php echo $lang['clearance-form']['yes'] ?? 'Yes'; ?></option>
                                <option value="No"
                                    <?php echo (($_POST['has_housing'] ?? '') == 'No') ? 'selected' : ''; ?>
                                    data-i18n="clearance-form.no"><?php echo $lang['clearance-form']['no'] ?? 'No'; ?>
                                </option>
                            </select>
                            <label for="has_housing"
                                data-i18n="clearance-form.has-housing"><?php echo $lang['clearance-form']['has-housing'] ?? 'Housing Provided'; ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="text-white mb-3" data-i18n="clearance-form.documents-upload-heading">
                    <?php echo $lang['clearance-form']['documents-upload-heading'] ?? 'Required Documents'; ?></h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="id_photo" class="form-label"
                            data-i18n="clearance-form.upload-id-photo"><?php echo $lang['clearance-form']['upload-id-photo'] ?? 'ID Photo (PDF, JPG, JPEG, PNG)'; ?></label>
                        <input type="file" class="form-control" id="id_photo" name="id_photo"
                            accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div class="col-md-6">
                        <label for="work_card" class="form-label"
                            data-i18n="clearance-form.upload-work-card"><?php echo $lang['clearance-form']['upload-work-card'] ?? 'Work Card (PDF, JPG, JPEG, PNG)'; ?></label>
                        <input type="file" class="form-control" id="work_card" name="work_card"
                            accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="text-white mb-3" data-i18n="clearance-form.admin-info-heading">
                    <?php echo $lang['clearance-form']['admin-info-heading'] ?? 'Administration Information'; ?></h4>
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-floating">
                            <select class="form-control" id="administration_name" name="administration_name" required>
                                <option value="" data-i18n="clearance-form.select-administration">
                                    <?php echo $lang['clearance-form']['select-administration'] ?? 'Select Administration'; ?>
                                </option>
                                <option value="Nursing Education Administration"
                                    <?php echo (($_POST['administration_name'] ?? '') == 'Nursing Education Administration') ? 'selected' : ''; ?>
                                    data-i18n="clearance-form.administrations.nursing-education">
                                    <?php echo $lang['clearance-form']['administrations']['nursing-education'] ?? 'Nursing Education Administration'; ?>
                                </option>
                                <option value="Health Training Administration"
                                    <?php echo (($_POST['administration_name'] ?? '') == 'Health Training Administration') ? 'selected' : ''; ?>
                                    data-i18n="clearance-form.administrations.health-training">
                                    <?php echo $lang['clearance-form']['administrations']['health-training'] ?? 'Health Training Administration'; ?>
                                </option>
                                <option value="Medical Training Administration"
                                    <?php echo (($_POST['administration_name'] ?? '') == 'Medical Training Administration') ? 'selected' : ''; ?>
                                    data-i18n="clearance-form.administrations.medical-training">
                                    <?php echo $lang['clearance-form']['administrations']['medical-training'] ?? 'Medical Training Administration'; ?>
                                </option>
                                <option value="Administrative Training Administration"
                                    <?php echo (($_POST['administration_name'] ?? '') == 'Administrative Training Administration') ? 'selected' : ''; ?>
                                    data-i18n="clearance-form.administrations.administrative-training">
                                    <?php echo $lang['clearance-form']['administrations']['administrative-training'] ?? 'Administrative Training Administration'; ?>
                                </option>
                                <option value="Volunteering Administration"
                                    <?php echo (($_POST['administration_name'] ?? '') == 'Volunteering Administration') ? 'selected' : ''; ?>
                                    data-i18n="clearance-form.administrations.volunteering">
                                    <?php echo $lang['clearance-form']['administrations']['volunteering'] ?? 'Volunteering Administration'; ?>
                                </option>
                                <option value="Contracting / Operation Administration"
                                    <?php echo (($_POST['administration_name'] ?? '') == 'Contracting / Operation Administration') ? 'selected' : ''; ?>
                                    data-i18n="clearance-form.administrations.contracting-operation">
                                    <?php echo $lang['clearance-form']['administrations']['contracting-operation'] ?? 'Contracting / Operation Administration'; ?>
                                </option>
                                <option value="Family Medicine Academy"
                                    <?php echo (($_POST['administration_name'] ?? '') == 'Family Medicine Academy') ? 'selected' : ''; ?>
                                    data-i18n="clearance-form.administrations.family-medicine">
                                    <?php echo $lang['clearance-form']['administrations']['family-medicine'] ?? 'Family Medicine Academy'; ?>
                                </option>
                                <option value="Healthcare Security Administration"
                                    <?php echo (($_POST['administration_name'] ?? '') == 'Healthcare Security Administration') ? 'selected' : ''; ?>
                                    data-i18n="clearance-form.administrations.healthcare-security">
                                    <?php echo $lang['clearance-form']['administrations']['healthcare-security'] ?? 'Healthcare Security Administration'; ?>
                                </option>
                                <option value="Radiology Administration AAML"
                                    <?php echo (($_POST['administration_name'] ?? '') == 'Radiology Administration AAML') ? 'selected' : ''; ?>
                                    data-i18n="clearance-form.administrations.radiology">
                                    <?php echo $lang['clearance-form']['administrations']['radiology'] ?? 'Radiology Administration AAML'; ?>
                                </option>
                            </select>
                            <label for="administration_name"
                                data-i18n="clearance-form.admin-name"><?php echo $lang['clearance-form']['admin-name'] ?? 'Administration Name'; ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-4">
                <div class="col-md-6">
                    <button class="w-100 btn btn-lg btn-primary" type="submit"
                        data-i18n="clearance-form.submit"><?php echo $lang['clearance-form']['submit'] ?? 'Submit Form'; ?></button>
                </div>
                <div class="col-md-6">
                    <a href="?page=home" class="w-100 btn btn-lg btn-outline-secondary"
                        data-i18n="clearance-form.cancel"><?php echo $lang['clearance-form']['cancel'] ?? 'Cancel'; ?></a>
                </div>
            </div>
        </form>
    </div>
</section>
<?php endif; ?>