<?php

// Database configuration
require('core/db/connection.php');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Security and Authentication Checks ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ?auth=login");
    exit();
}

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Trainee') {
    header("Location: ?page=home");
    exit();
}

$user_id = $_SESSION['user_id'] ?? null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $user_id) {
    $errors = [];

    // Sanitize and validate inputs
    $ar_name = isset($_POST['ar_name']) ? trim(htmlspecialchars($_POST['ar_name'])) : '';
    $en_name = isset($_POST['en_name']) ? trim(htmlspecialchars($_POST['en_name'])) : '';
    $national_id = isset($_POST['national_id']) ? trim(htmlspecialchars($_POST['national_id'])) : '';
    $age = isset($_POST['age']) ? filter_var($_POST['age'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 15, "max_range" => 100]]) : false;
    $country_code = isset($_POST['country_code']) ? trim(htmlspecialchars($_POST['country_code'])) : '';
    $mobile_number = isset($_POST['mobile_number']) ? trim(htmlspecialchars($_POST['mobile_number'])) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) : false;
    $country = isset($_POST['country']) ? trim(htmlspecialchars($_POST['country'])) : '';
    $gender = isset($_POST['gender']) ? trim(htmlspecialchars($_POST['gender'])) : '';
    $university = isset($_POST['university']) ? trim(htmlspecialchars($_POST['university'])) : '';
    $uni_id = isset($_POST['uni_id']) ? trim(htmlspecialchars($_POST['uni_id'])) : '';
    $major = isset($_POST['major']) ? trim(htmlspecialchars($_POST['major'])) : '';
    $degree = isset($_POST['degree']) ? trim(htmlspecialchars($_POST['degree'])) : '';
    $start_date = isset($_POST['start_date']) ? trim(htmlspecialchars($_POST['start_date'])) : '';
    $end_date = isset($_POST['end_date']) ? trim(htmlspecialchars($_POST['end_date'])) : '';
    $supervisor_name = isset($_POST['supervisor_name']) ? trim(htmlspecialchars($_POST['supervisor_name'])) : '';
    $supervisor_email = isset($_POST['supervisor_email']) ? filter_var($_POST['supervisor_email'], FILTER_VALIDATE_EMAIL) : false;
    $center = isset($_POST['center']) ? trim(htmlspecialchars($_POST['center'])) : '';

    // Validation
    if (!$ar_name) $errors[] = $lang['training-form']['errors']['name-ar-required'] ?? "Name in Arabic is required.";
    if (!$en_name) $errors[] = $lang['training-form']['errors']['name-en-required'] ?? "Name in English is required.";
    if (!$national_id) $errors[] = $lang['training-form']['errors']['national-id-required'] ?? "National ID is required.";
    if ($age === false) $errors[] = $lang['training-form']['errors']['invalid-age'] ?? "Invalid age. Must be between 15 and 100.";
    if (!$mobile_number || !preg_match("/^\d{9}$/", $mobile_number)) $errors[] = $lang['training-form']['errors']['invalid-mobile'] ?? "Mobile number must be 9 digits.";
    if (!$email) $errors[] = $lang['training-form']['errors']['invalid-email'] ?? "Invalid email address.";
    if (!$country) $errors[] = $lang['training-form']['errors']['country-required'] ?? "Country is required.";
    if (!in_array($gender, ['Male', 'Female'])) $errors[] = $lang['training-form']['errors']['invalid-gender'] ?? "Invalid gender selection.";
    if (!$university) $errors[] = $lang['training-form']['errors']['university-required'] ?? "University name is required.";
    if (!$uni_id) $errors[] = $lang['training-form']['errors']['university-id-required'] ?? "University ID is required.";
    if (!$major) $errors[] = $lang['training-form']['errors']['major-required'] ?? "Major is required.";
    if (!$degree) $errors[] = $lang['training-form']['errors']['degree-required'] ?? "Degree is required.";
    if (!$start_date) $errors[] = $lang['training-form']['errors']['start-date-required'] ?? "Training start date is required.";
    if (!$end_date) $errors[] = $lang['training-form']['errors']['end-date-required'] ?? "Training end date is required.";
    if (strtotime($start_date) > strtotime($end_date)) $errors[] = $lang['training-form']['errors']['invalid-date-range'] ?? "End date cannot be before start date.";
    if (!$supervisor_name) $errors[] = $lang['training-form']['errors']['supervisor-name-required'] ?? "Academic Supervisor Name is required.";
    if (!$supervisor_email) $errors[] = $lang['training-form']['errors']['invalid-supervisor-email'] ?? "Invalid Academic Supervisor Email address.";
    if (!$center) $errors[] = $lang['training-form']['errors']['center-required'] ?? "Center selection is required.";

    // File uploads
    $training_letter_path = null;
    $cv_path = null;
    $target_dir = "Uploads/";

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (isset($_FILES['training_letter']) && $_FILES['training_letter']['error'] == UPLOAD_ERR_OK) {
        $file_name = uniqid('letter_') . '_' . basename($_FILES['training_letter']['name']);
        $training_letter_path = $target_dir . $file_name;
        $file_type = mime_content_type($_FILES['training_letter']['tmp_name']);
        if ($file_type == 'application/pdf') {
            if (!move_uploaded_file($_FILES['training_letter']['tmp_name'], $training_letter_path)) {
                $errors[] = $lang['training-form']['errors']['training-letter-upload-failed'] ?? "Failed to upload training letter.";
                $training_letter_path = null;
            }
        } else {
            $errors[] = $lang['training-form']['errors']['training-letter-pdf-only'] ?? "Training letter must be a PDF.";
            $training_letter_path = null;
        }
    } else {
        $errors[] = $lang['training-form']['errors']['training-letter-required'] ?? "Training letter is required.";
    }

    if (isset($_FILES['cv']) && $_FILES['cv']['error'] == UPLOAD_ERR_OK) {
        $file_name = uniqid('cv_') . '_' . basename($_FILES['cv']['name']);
        $cv_path = $target_dir . $file_name;
        $file_type = mime_content_type($_FILES['cv']['tmp_name']);
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (in_array($file_type, $allowed_types)) {
            if (!move_uploaded_file($_FILES['cv']['tmp_name'], $cv_path)) {
                $errors[] = $lang['training-form']['errors']['cv-upload-failed'] ?? "Failed to upload CV.";
                $cv_path = null;
            }
        } else {
            $errors[] = $lang['training-form']['errors']['cv-format'] ?? "CV must be a PDF, DOC, or DOCX.";
            $cv_path = null;
        }
    } else {
        $errors[] = $lang['training-form']['errors']['cv-required'] ?? "Curriculum Vitae (CV) is required.";
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO trainees (user_id, ar_name, en_name, national_id, age, country_code, mobile_number, email, country, gender, university, uni_id, major, degree, start_date, end_date, supervisor_name, supervisor_email, center, training_letter_path, cv_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
            $stmt->bind_param("isssissssssssssssssss", $user_id, $ar_name, $en_name, $national_id, $age, $country_code, $mobile_number, $email, $country, $gender, $university, $uni_id, $major, $degree, $start_date, $end_date, $supervisor_name, $supervisor_email, $center, $training_letter_path, $cv_path);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = $lang['training-form']['success-message'] ?? "Registration submitted successfully!";
                header("Location: ?page=home");
                exit();
            } else {
                $_SESSION['error_message'] = $lang['training-form']['errors']['database-error'] ?? "Error: " . $stmt->error;
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $_SESSION['error_message'] = $lang['training-form']['errors']['duplicate-national-id'] ?? "Registration failed: A trainee with this National ID already exists.";
            } else {
                $_SESSION['error_message'] = $lang['training-form']['errors']['database-error'] ?? "Database error: " . $e->getMessage();
            }
        }
    } else {
        $_SESSION['error_message'] = implode("<br>", $errors);
    }
    $_SESSION['form_data'] = $_POST;
    header("Location: ?page=training-form");
    exit();
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<section class="container align-content-center h-100 w-100 m-auto">
    <div class="overlay-box py-4 px-4 m-auto shadow border border-1 border-secondary rounded-4">
        <h1 class="h3 mb-4 fw-normal text-white text-center" data-i18n="training-form.form-title">
            <?php echo $lang['training-form']['form-title'] ?? 'Trainee Registration Form'; ?></h1>

        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success text-center" role="alert" data-i18n="training-form.success-message">
            <?php echo $_SESSION['success_message'];
                unset($_SESSION['success_message']); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger text-center" role="alert">
            <?php echo $_SESSION['error_message'];
                unset($_SESSION['error_message']); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-4">
                <h5 class="text-white mb-3" data-i18n="training-form.personal-info-heading">
                    <?php echo $lang['training-form']['personal-info-heading'] ?? 'Personal Information'; ?></h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="en-name" name="en_name"
                                placeholder="<?php echo $lang['training-form']['full-name-en-placeholder'] ?? 'Name in English'; ?>"
                                required value="<?php echo htmlspecialchars($form_data['en_name'] ?? ''); ?>">
                            <label for="en-name"
                                data-i18n="training-form.full-name-en"><?php echo $lang['training-form']['full-name-en'] ?? 'Name in English'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="ar-name" name="ar_name"
                                placeholder="<?php echo $lang['training-form']['full-name-ar-placeholder'] ?? 'Name in Arabic'; ?>"
                                required value="<?php echo htmlspecialchars($form_data['ar_name'] ?? ''); ?>">
                            <label for="ar-name"
                                data-i18n="training-form.full-name-ar"><?php echo $lang['training-form']['full-name-ar'] ?? 'Name in Arabic'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="national-id" name="national_id"
                                placeholder="<?php echo $lang['training-form']['national-id-placeholder'] ?? 'National ID'; ?>"
                                required value="<?php echo htmlspecialchars($form_data['national_id'] ?? ''); ?>">
                            <label for="national-id"
                                data-i18n="training-form.national-id"><?php echo $lang['training-form']['national-id'] ?? 'National ID'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="age" name="age"
                                placeholder="<?php echo $lang['training-form']['age-placeholder'] ?? 'Age'; ?>" min="15"
                                max="100" required value="<?php echo htmlspecialchars($form_data['age'] ?? ''); ?>">
                            <label for="age"
                                data-i18n="training-form.age"><?php echo $lang['training-form']['age'] ?? 'Age'; ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="text-white mb-3" data-i18n="training-form.contact-info-heading">
                    <?php echo $lang['training-form']['contact-info-heading'] ?? 'Contact Information'; ?></h5>
                <div class="row g-3">
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="country_code" name="country_code" value="+966"
                                maxlength="5"
                                placeholder="<?php echo $lang['training-form']['country-code-placeholder'] ?? 'Country Code'; ?>"
                                required readonly>
                            <label for="country_code"
                                data-i18n="training-form.country-code"><?php echo $lang['training-form']['country-code'] ?? 'Country Code'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="mobile_number" name="mobile_number"
                                placeholder="<?php echo $lang['training-form']['mobile-number-placeholder'] ?? 'Mobile Number'; ?>"
                                maxlength="9" pattern="\d{9}" oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                required value="<?php echo htmlspecialchars($form_data['mobile_number'] ?? ''); ?>">
                            <label for="mobile_number"
                                data-i18n="training-form.mobile-number"><?php echo $lang['training-form']['mobile-number'] ?? 'Mobile Number'; ?></label>
                            <span class="note"
                                data-i18n="training-form.mobile-number-note"><?php echo $lang['training-form']['mobile-number-note'] ?? '* Must be 9 digits after the country code'; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="<?php echo $lang['training-form']['email-placeholder'] ?? 'Email Address'; ?>"
                                required value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
                            <label for="email"
                                data-i18n="training-form.email"><?php echo $lang['training-form']['email'] ?? 'Email Address'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="country" name="country"
                                placeholder="<?php echo $lang['training-form']['country-placeholder'] ?? 'Country'; ?>"
                                required value="<?php echo htmlspecialchars($form_data['country'] ?? ''); ?>">
                            <label for="country"
                                data-i18n="training-form.country"><?php echo $lang['training-form']['country'] ?? 'Country'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="gender-select" name="gender" required>
                                <option value="" data-i18n="training-form.select-gender">
                                    <?php echo $lang['training-form']['select-gender'] ?? '-- Select Gender --'; ?>
                                </option>
                                <option value="Male" data-i18n="training-form.gender.male"
                                    <?php echo (isset($form_data['gender']) && $form_data['gender'] == 'Male') ? 'selected' : ''; ?>>
                                    <?php echo $lang['training-form']['gender']['male'] ?? 'Male'; ?></option>
                                <option value="Female" data-i18n="training-form.gender.female"
                                    <?php echo (isset($form_data['gender']) && $form_data['gender'] == 'Female') ? 'selected' : ''; ?>>
                                    <?php echo $lang['training-form']['gender']['female'] ?? 'Female'; ?></option>
                            </select>
                            <label for="gender-select"
                                data-i18n="training-form.gender"><?php echo $lang['training-form']['gender'] ?? 'Gender'; ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="text-white mb-3" data-i18n="training-form.academic-info-heading">
                    <?php echo $lang['training-form']['academic-info-heading'] ?? 'Academic Information'; ?></h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="university" name="university"
                                placeholder="<?php echo $lang['training-form']['university-placeholder'] ?? 'University Name'; ?>"
                                required value="<?php echo htmlspecialchars($form_data['university'] ?? ''); ?>">
                            <label for="university"
                                data-i18n="training-form.university"><?php echo $lang['training-form']['university'] ?? 'University Name'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="uni-id" name="uni_id"
                                placeholder="<?php echo $lang['training-form']['university-id-placeholder'] ?? 'University ID Number'; ?>"
                                required value="<?php echo htmlspecialchars($form_data['uni_id'] ?? ''); ?>">
                            <label for="uni-id"
                                data-i18n="training-form.university-id"><?php echo $lang['training-form']['university-id'] ?? 'University ID Number'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="major" name="major"
                                placeholder="<?php echo $lang['training-form']['major-placeholder'] ?? 'Major'; ?>"
                                required value="<?php echo htmlspecialchars($form_data['major'] ?? ''); ?>">
                            <label for="major"
                                data-i18n="training-form.major"><?php echo $lang['training-form']['major'] ?? 'Major'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="degree" name="degree"
                                placeholder="<?php echo $lang['training-form']['degree-placeholder'] ?? 'Degree'; ?>"
                                required value="<?php echo htmlspecialchars($form_data['degree'] ?? ''); ?>">
                            <label for="degree"
                                data-i18n="training-form.degree"><?php echo $lang['training-form']['degree'] ?? 'Degree'; ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="text-white mb-3" data-i18n="training-form.training-info-heading">
                    <?php echo $lang['training-form']['training-info-heading'] ?? 'Training Information'; ?></h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="start-date" name="start_date" required
                                value="<?php echo htmlspecialchars($form_data['start_date'] ?? ''); ?>">
                            <label for="start-date"
                                data-i18n="training-form.start-date"><?php echo $lang['training-form']['start-date'] ?? 'Training Start Date'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="end-date" name="end_date" required
                                value="<?php echo htmlspecialchars($form_data['end_date'] ?? ''); ?>">
                            <label for="end-date"
                                data-i18n="training-form.end-date"><?php echo $lang['training-form']['end-date'] ?? 'Training End Date'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="supervisor-name" name="supervisor_name"
                                placeholder="<?php echo $lang['training-form']['supervisor-name-placeholder'] ?? 'Academic Supervisor Name'; ?>"
                                required value="<?php echo htmlspecialchars($form_data['supervisor_name'] ?? ''); ?>">
                            <label for="supervisor-name"
                                data-i18n="training-form.supervisor-name"><?php echo $lang['training-form']['supervisor-name'] ?? 'Academic Supervisor Name'; ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="supervisor-email" name="supervisor_email"
                                placeholder="<?php echo $lang['training-form']['supervisor-email-placeholder'] ?? 'Academic Supervisor Email'; ?>"
                                required value="<?php echo htmlspecialchars($form_data['supervisor_email'] ?? ''); ?>">
                            <label for="supervisor-email"
                                data-i18n="training-form.supervisor-email"><?php echo $lang['training-form']['supervisor-email'] ?? 'Academic Supervisor Email'; ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="text-white mb-3" data-i18n="training-form.center-selection-heading">
                    <?php echo $lang['training-form']['center-selection-heading'] ?? 'Center Selection'; ?></h5>
                <div class="form-floating">
                    <input type="text" class="form-control" id="center-search"
                        placeholder="<?php echo $lang['training-form']['center-search-placeholder'] ?? 'Search for health centers and hospitals...'; ?>"
                        data-i18n-placeholder="training-form.center-search-placeholder">
                    <label for="center-search"
                        data-i18n="training-form.available-facilities"><?php echo $lang['training-form']['available-facilities'] ?? 'Available Facilities'; ?></label>
                </div>
                <div class="mt-2">
                    <select class="form-select" id="center-select" name="center" required>
                        <option value="" data-i18n="training-form.select-center">
                            <?php echo $lang['training-form']['select-center'] ?? '-- Select Center --'; ?></option>
                        <option value="Al-Ghadeer"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Ghadeer') ? 'selected' : ''; ?>>
                            Al-Ghadeer</option>
                        <option value="Al-Narjis"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Narjis') ? 'selected' : ''; ?>>
                            Al-Narjis</option>
                        <option value="Al-Rabie"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Rabie') ? 'selected' : ''; ?>>
                            Al-Rabie</option>
                        <option value="Al-Sahafa"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Sahafa') ? 'selected' : ''; ?>>
                            Al-Sahafa</option>
                        <option value="Al-Falah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Falah') ? 'selected' : ''; ?>>
                            Al-Falah</option>
                        <option value="Al-Yasmeen"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Yasmeen') ? 'selected' : ''; ?>>
                            Al-Yasmeen</option>
                        <option value="Al-Wadi"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Wadi') ? 'selected' : ''; ?>>
                            Al-Wadi</option>
                        <option value="Al-Ezdihar"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Ezdihar') ? 'selected' : ''; ?>>
                            Al-Ezdihar</option>
                        <option value="Salah Aldeen"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Salah Aldeen') ? 'selected' : ''; ?>>
                            Salah Aldeen</option>
                        <option value="Al-Maseef"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Maseef') ? 'selected' : ''; ?>>
                            Al-Maseef</option>
                        <option value="Al-Mursalat"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Mursalat') ? 'selected' : ''; ?>>
                            Al-Mursalat</option>
                        <option value="King Fahad District"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'King Fahad District') ? 'selected' : ''; ?>>
                            King Fahad District</option>
                        <option value="Al-Murooj"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Murooj') ? 'selected' : ''; ?>>
                            Al-Murooj</option>
                        <option value="Al-Sulaimania"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Sulaimania') ? 'selected' : ''; ?>>
                            Al-Sulaimania</option>
                        <option value="Al-Nuzha"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Nuzha') ? 'selected' : ''; ?>>
                            Al-Nuzha</option>
                        <option value="Al-Wurood"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Wurood') ? 'selected' : ''; ?>>
                            Al-Wurood</option>
                        <option value="Ishbiliya"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Ishbiliya') ? 'selected' : ''; ?>>
                            Ishbiliya</option>
                        <option value="Khaleej 2"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Khaleej 2') ? 'selected' : ''; ?>>
                            Khaleej 2</option>
                        <option value="Al-Hamra"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Hamra') ? 'selected' : ''; ?>>
                            Al-Hamra</option>
                        <option value="Khaleej 1"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Khaleej 1') ? 'selected' : ''; ?>>
                            Khaleej 1</option>
                        <option value="Qurtoba"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Qurtoba') ? 'selected' : ''; ?>>
                            Qurtoba</option>
                        <option value="Al-Yarmouk (West)"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Yarmouk (West)') ? 'selected' : ''; ?>>
                            Al-Yarmouk (West)</option>
                        <option value="King Faisal District"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'King Faisal District') ? 'selected' : ''; ?>>
                            King Faisal District</option>
                        <option value="Al-Munsiyah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Munsiyah') ? 'selected' : ''; ?>>
                            Al-Munsiyah</option>
                        <option value="Ghornata"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Ghornata') ? 'selected' : ''; ?>>
                            Ghornata</option>
                        <option value="Al-Rawdah 1"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Rawdah 1') ? 'selected' : ''; ?>>
                            Al-Rawdah 1</option>
                        <option value="Al-Rawdah 2"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Rawdah 2') ? 'selected' : ''; ?>>
                            Al-Rawdah 2</option>
                        <option value="Al-Nahda (West)"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Nahda (West)') ? 'selected' : ''; ?>>
                            Al-Nahda (West)</option>
                        <option value="Janadriyah (West)"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Janadriyah (West)') ? 'selected' : ''; ?>>
                            Janadriyah (West)</option>
                        <option value="Janadriyah (East)"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Janadriyah (East)') ? 'selected' : ''; ?>>
                            Janadriyah (East)</option>
                        <option value="North Nadheem"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'North Nadheem') ? 'selected' : ''; ?>>
                            North Nadheem</option>
                        <option value="South Nadheem"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'South Nadheem') ? 'selected' : ''; ?>>
                            South Nadheem</option>
                        <option value="Al-Nadwa"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Nadwa') ? 'selected' : ''; ?>>
                            Al-Nadwa</option>
                        <option value="Hijrat Saad"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Hijrat Saad') ? 'selected' : ''; ?>>
                            Hijrat Saad</option>
                        <option value="Airport Health Control Center"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Airport Health Control Center') ? 'selected' : ''; ?>>
                            Airport Health Control Center</option>
                        <option value="Royal Protocol Clinic"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Royal Protocol Clinic') ? 'selected' : ''; ?>>
                            Royal Protocol Clinic</option>
                        <option value="Royal Diwan Clinic"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Royal Diwan Clinic') ? 'selected' : ''; ?>>
                            Royal Diwan Clinic</option>
                        <option value="Women’s Health Clinic (Hayat Mall)"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Women’s Health Clinic (Hayat Mall)') ? 'selected' : ''; ?>>
                            Women’s Health Clinic (Hayat Mall)</option>
                        <option value="Al-Manar"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Manar') ? 'selected' : ''; ?>>
                            Al-Manar</option>
                        <option value="Al-Salam"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Salam') ? 'selected' : ''; ?>>
                            Al-Salam</option>
                        <option value="Middle Naseem"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Middle Naseem') ? 'selected' : ''; ?>>
                            Middle Naseem</option>
                        <option value="South Naseem"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'South Naseem') ? 'selected' : ''; ?>>
                            South Naseem</option>
                        <option value="East Naseem"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'East Naseem') ? 'selected' : ''; ?>>
                            East Naseem</option>
                        <option value="West Naseem"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'West Naseem') ? 'selected' : ''; ?>>
                            West Naseem</option>
                        <option value="Al-Saada"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Saada') ? 'selected' : ''; ?>>
                            Al-Saada</option>
                        <option value="Al-Jazeera"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Jazeera') ? 'selected' : ''; ?>>
                            Al-Jazeera</option>
                        <option value="Artawiyah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Artawiyah') ? 'selected' : ''; ?>>
                            Artawiyah</option>
                        <option value="Umm Al-Jamajim"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Umm Al-Jamajim') ? 'selected' : ''; ?>>
                            Umm Al-Jamajim</option>
                        <option value="Mushrifah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Mushrifah') ? 'selected' : ''; ?>>
                            Mushrifah</option>
                        <option value="Jurab"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Jurab') ? 'selected' : ''; ?>>
                            Jurab</option>
                        <option value="Al-Barzah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Barzah') ? 'selected' : ''; ?>>
                            Al-Barzah</option>
                        <option value="Mishdhuba"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Mishdhuba') ? 'selected' : ''; ?>>
                            Mishdhuba</option>
                        <option value="Masadat Sudair"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Masadat Sudair') ? 'selected' : ''; ?>>
                            Masadat Sudair</option>
                        <option value="Qaa’yat Sudair"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Qaa’yat Sudair') ? 'selected' : ''; ?>>
                            Qaa’yat Sudair</option>
                        <option value="Mishlah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Mishlah') ? 'selected' : ''; ?>>
                            Mishlah</option>
                        <option value="Mishash Awadh"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Mishash Awadh') ? 'selected' : ''; ?>>
                            Mishash Awadh</option>
                        <option value="Howaimidah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Howaimidah') ? 'selected' : ''; ?>>
                            Howaimidah</option>
                        <option value="Umm Sudairah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Umm Sudairah') ? 'selected' : ''; ?>>
                            Umm Sudairah</option>
                        <option value="Barzan"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Barzan') ? 'selected' : ''; ?>>
                            Barzan</option>
                        <option value="Al-Quds"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Quds') ? 'selected' : ''; ?>>
                            Al-Quds</option>
                        <option value="Al-Khalidiyah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Khalidiyah') ? 'selected' : ''; ?>>
                            Al-Khalidiyah</option>
                        <option value="Al-Siddiq"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Siddiq') ? 'selected' : ''; ?>>
                            Al-Siddiq</option>
                        <option value="Al-Farouq"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Farouq') ? 'selected' : ''; ?>>
                            Al-Farouq</option>
                        <option value="Al-Yamamah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Yamamah') ? 'selected' : ''; ?>>
                            Al-Yamamah</option>
                        <option value="Ulaqqah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Ulaqqah') ? 'selected' : ''; ?>>
                            Ulaqqah</option>
                        <option value="Al-Thuwair"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Thuwair') ? 'selected' : ''; ?>>
                            Al-Thuwair</option>
                        <option value="Ghat PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Ghat PHC') ? 'selected' : ''; ?>>
                            Ghat PHC</option>
                        <option value="Meleeh PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Meleeh PHC') ? 'selected' : ''; ?>>
                            Meleeh PHC</option>
                        <option value="Al-Abdaliyah PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Abdaliyah PHC') ? 'selected' : ''; ?>>
                            Al-Abdaliyah PHC</option>
                        <option value="Airport PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Airport PHC') ? 'selected' : ''; ?>>
                            Airport PHC</option>
                        <option value="Al-Fayhaa PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Fayhaa PHC') ? 'selected' : ''; ?>>
                            Al-Fayhaa PHC</option>
                        <option value="Al-Yarmouk PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Yarmouk PHC') ? 'selected' : ''; ?>>
                            Al-Yarmouk PHC</option>
                        <option value="Al-Baseerah PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Baseerah PHC') ? 'selected' : ''; ?>>
                            Al-Baseerah PHC</option>
                        <option value="Abdulaziz Al-Shuwai'er PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Abdulaziz Al-Shuwai\'er PHC') ? 'selected' : ''; ?>>
                            Abdulaziz Al-Shuwai'er PHC</option>
                        <option value="Harmah PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Harmah PHC') ? 'selected' : ''; ?>>
                            Harmah PHC</option>
                        <option value="Al-Faisaliah PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Faisaliah PHC') ? 'selected' : ''; ?>>
                            Al-Faisaliah PHC</option>
                        <option value="Majmaah PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Majmaah PHC') ? 'selected' : ''; ?>>
                            Majmaah PHC</option>
                        <option value="Tumair PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Tumair PHC') ? 'selected' : ''; ?>>
                            Tumair PHC</option>
                        <option value="Umm Rujoum Center"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Umm Rujoum Center') ? 'selected' : ''; ?>>
                            Umm Rujoum Center</option>
                        <option value="Hotat Sudair Center"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Hotat Sudair Center') ? 'selected' : ''; ?>>
                            Hotat Sudair Center</option>
                        <option value="Al-Nahda PHC (Sudair)"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Nahda PHC (Sudair)') ? 'selected' : ''; ?>>
                            Al-Nahda PHC (Sudair)</option>
                        <option value="Al-Shifa PHC (Hotat Sudair)"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Shifa PHC (Hotat Sudair)') ? 'selected' : ''; ?>>
                            Al-Shifa PHC (Hotat Sudair)</option>
                        <option value="Tuwaiem Center"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Tuwaiem Center') ? 'selected' : ''; ?>>
                            Tuwaiem Center</option>
                        <option value="Rawdat Sudair Center"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Rawdat Sudair Center') ? 'selected' : ''; ?>>
                            Rawdat Sudair Center</option>
                        <option value="Al-Atar PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Atar PHC') ? 'selected' : ''; ?>>
                            Al-Atar PHC</option>
                        <option value="Al-Khatamah PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Khatamah PHC') ? 'selected' : ''; ?>>
                            Al-Khatamah PHC</option>
                        <option value="Awdat Sudair PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Awdat Sudair PHC') ? 'selected' : ''; ?>>
                            Awdat Sudair PHC</option>
                        <option value="Ashirat Sudair PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Ashirat Sudair PHC') ? 'selected' : ''; ?>>
                            Ashirat Sudair PHC</option>
                        <option value="Mubayidh PHC"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Mubayidh PHC') ? 'selected' : ''; ?>>
                            Mubayidh PHC</option>
                        <option value="Hafr Al-Atash"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Hafr Al-Atash') ? 'selected' : ''; ?>>
                            Hafr Al-Atash</option>
                        <option value="Rumhiya"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Rumhiya') ? 'selected' : ''; ?>>
                            Rumhiya</option>
                        <option value="Aytliyah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Aytliyah') ? 'selected' : ''; ?>>
                            Aytliyah</option>
                        <option value="Ghaylanah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Ghaylanah') ? 'selected' : ''; ?>>
                            Ghaylanah</option>
                        <option value="Al-Muzayri’"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Al-Muzayri’') ? 'selected' : ''; ?>>
                            Al-Muzayri’</option>
                        <option value="Hafnat Al-Tairi"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Hafnat Al-Tairi') ? 'selected' : ''; ?>>
                            Hafnat Al-Tairi</option>
                        <option value="Rumah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Rumah') ? 'selected' : ''; ?>>
                            Rumah</option>
                        <option value="Shuweyah"
                            <?php echo (isset($form_data['center']) && $form_data['center'] == 'Shuweyah') ? 'selected' : ''; ?>>
                            Shuweyah</option>
                    </select>
                    <div id="center-no-results" class="text-danger small mt-2" style="display:none;"
                        data-i18n="training-form.no-results">
                        <?php echo $lang['training-form']['no-results'] ?? 'No results found.'; ?></div>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="text-white mb-3" data-i18n="training-form.required-documents-heading">
                    <?php echo $lang['training-form']['required-documents-heading'] ?? 'Required Documents'; ?></h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="training-letter" class="form-label text-white small"
                            data-i18n="training-form.training-letter"><?php echo $lang['training-form']['training-letter'] ?? 'Training Letter (PDF)'; ?></label>
                        <input type="file" class="form-control" id="training-letter" name="training_letter"
                            accept=".pdf" required>
                        <div id="letter-preview" class="file-preview"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="cv" class="form-label text-white small"
                            data-i18n="training-form.cv"><?php echo $lang['training-form']['cv'] ?? 'Curriculum Vitae (CV)'; ?></label>
                        <input type="file" class="form-control" id="cv" name="cv" accept=".pdf,.doc,.docx" required>
                        <div id="cv-preview" class="file-preview"></div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-4">
                <div class="col-12 col-md-6">
                    <button type="submit" id="submit-btn" class="w-100 btn btn-lg btn-primary"
                        data-i18n="training-form.submit"><?php echo $lang['training-form']['submit'] ?? 'Submit'; ?></button>
                </div>
                <div class="col-12 col-md-6">
                    <a href="?page=home" class="w-100 btn btn-lg btn-outline-secondary"
                        data-i18n="training-form.cancel"><?php echo $lang['training-form']['cancel'] ?? 'Cancel'; ?></a>
                </div>
            </div>
        </form>
    </div>
</section>