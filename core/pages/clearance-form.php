    <?php

    // Initialize variables for form submission
    $errors = [];
    $success = '';
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and validate inputs
        $submission_date = $_POST['submission_date'] ?? '';
        $has_housing = $_POST['has_housing'] ?? '';
        $nationality = $_POST['nationality'] ?? '';
        $name_ar = trim($_POST['name_ar'] ?? '');
        $name_en = trim($_POST['name_en'] ?? '');
        $id_iqama_number = trim($_POST['id_iqama_number'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $department = trim($_POST['department'] ?? '');
        $job_title = trim($_POST['job_title'] ?? '');
        $work_card_expiry_date = $_POST['work_card_expiry_date'] ?? '';
        $administration_name = $_POST['administration_name'] ?? '';

        // Validate required fields
        if (empty($submission_date)) $errors[] = 'Date is required';
        if (!in_array($has_housing, ['Yes', 'No'])) $errors[] = 'Housing status is required';
        if (!in_array($nationality, ['Saudi', 'Non-Saudi'])) $errors[] = 'Nationality is required';
        if (empty($name_ar)) $errors[] = 'Name in Arabic is required';
        if (empty($name_en)) $errors[] = 'Name in English is required';
        if (!preg_match('/^\d{10}$/', $id_iqama_number)) $errors[] = 'ID/Iqama number must be 10 digits';
        if (!preg_match('/^\+966\d{9}$/', $mobile)) $errors[] = 'Mobile number must start with +966 and be 9 digits after the prefix';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address';
        if (empty($department)) $errors[] = 'Department/Administration is required';
        if (empty($job_title)) $errors[] = 'Job title is required';
        if (empty($work_card_expiry_date)) $errors[] = 'Work card expiry date is required';
        if (empty($administration_name)) $errors[] = 'Administration name is required';

        // Handle file uploads
        $id_photo = null;
        $work_card = null;
        if (isset($_FILES['id_photo']) && $_FILES['id_photo']['error'] === UPLOAD_ERR_OK) {
            $file_ext = pathinfo($_FILES['id_photo']['name'], PATHINFO_EXTENSION);
            if (!in_array(strtolower($file_ext), ['pdf', 'jpg', 'jpeg', 'png'])) {
                $errors[] = 'ID photo must be PDF, JPG, or PNG';
            } else {
                $id_photo = 'id_photo_' . time() . '.' . $file_ext;
                move_uploaded_file($_FILES['id_photo']['tmp_name'], $upload_dir . $id_photo);
            }
        }
        if (isset($_FILES['work_card']) && $_FILES['work_card']['error'] === UPLOAD_ERR_OK) {
            $file_ext = pathinfo($_FILES['work_card']['name'], PATHINFO_EXTENSION);
            if (!in_array(strtolower($file_ext), ['pdf', 'jpg', 'jpeg', 'png'])) {
                $errors[] = 'Work card must be PDF, JPG, or PNG';
            } else {
                $work_card = 'work_card_' . time() . '.' . $file_ext;
                move_uploaded_file($_FILES['work_card']['tmp_name'], $upload_dir . $work_card);
            }
        }

        // If no errors, insert into database
        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("INSERT INTO clearance_forms (
                submission_date, has_housing, nationality, name_ar, name_en, id_iqama_number, mobile, email,
                department, job_title, work_card_expiry_date, id_photo, work_card, administration_name
            ) VALUES (:submission_date, :has_housing, :nationality, :name_ar, :name_en, :id_iqama_number, :mobile, :email,
                :department, :job_title, :work_card_expiry_date, :id_photo, :work_card, :administration_name)");

                $stmt->execute([
                    ':submission_date' => $submission_date,
                    ':has_housing' => $has_housing,
                    ':nationality' => $nationality,
                    ':name_ar' => $name_ar,
                    ':name_en' => $name_en,
                    ':id_iqama_number' => $id_iqama_number,
                    ':mobile' => $mobile,
                    ':email' => $email,
                    ':department' => $department,
                    ':job_title' => $job_title,
                    ':work_card_expiry_date' => $work_card_expiry_date,
                    ':id_photo' => $id_photo,
                    ':work_card' => $work_card,
                    ':administration_name' => $administration_name
                ]);
                $success = 'Clearance form submitted successfully!';
            } catch (PDOException $e) {
                $errors[] = 'Form submission failed: ' . $e->getMessage();
            }
        }
    }
    ?>
    <section class="container align-content-center h-100 w-100 m-auto">
        <div class="overlay-box py-4 px-4 m-auto shadow border border-1 border-secondary rounded-4">
            <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm(event)">
                <h1 class="h3 mb-4 fw-normal text-white text-center">Clearance Form</h1>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p class="error mb-0"><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <p class="success mb-0"><?php echo htmlspecialchars($success); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Personal Information Section -->
                <div class="mb-4">
                    <h4 class="text-white mb-3">Personal Information</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name_ar" name="name_ar"
                                    placeholder="Name in Arabic" required>
                                <label for="name_ar">Name in Arabic</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name_en" name="name_en"
                                    placeholder="Name in English" required>
                                <label for="name_en">Name in English</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="id_iqama_number" name="id_iqama_number"
                                    placeholder="ID/Iqama Number" maxlength="10"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                                <label for="id_iqama_number">ID/Iqama Number</label>
                                <span class="note">* Must be 10 digits</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-control" id="nationality" name="nationality" required>
                                    <option value="">Select Nationality</option>
                                    <option value="Saudi">Saudi</option>
                                    <option value="Non-Saudi">Non-Saudi</option>
                                </select>
                                <label for="nationality">Nationality</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="mb-4">
                    <h4 class="text-white mb-3">Contact Information</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="mobile" name="mobile"
                                    placeholder="Mobile Number" maxlength="13" oninput="formatMobile(this)" required>
                                <label for="mobile">Mobile Number</label>
                                <span class="note">* Starts with +966 and must be 9 digits after the prefix</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Email Address" required>
                                <label for="email">Email Address</label>
                                <span class="note">* Please enter a valid email address</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Information Section -->
                <div class="mb-4">
                    <h4 class="text-white mb-3">Professional Information</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="department" name="department"
                                    placeholder="Department/Administration" required>
                                <label for="department">Department/Administration</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="job_title" name="job_title"
                                    placeholder="Job Title" required>
                                <label for="job_title">Job Title</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="submission_date" name="submission_date"
                                    required>
                                <label for="submission_date">Date</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="work_card_expiry_date"
                                    name="work_card_expiry_date" required>
                                <label for="work_card_expiry_date">Work Card Expiry Date</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-control" id="has_housing" name="has_housing" required>
                                    <option value="">Housing Available?</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                                <label for="has_housing">Housing Available?</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="mb-4">
                    <h4 class="text-white mb-3">Required Documents</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="id_photo" class="form-label text-white small">ID Photo (PDF, JPG, or
                                PNG)</label>
                            <input type="file" class="form-control" id="id_photo" name="id_photo"
                                accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="col-md-6">
                            <label for="work_card" class="form-label text-white small">Work Card (PDF, JPG, or
                                PNG)</label>
                            <input type="file" class="form-control" id="work_card" name="work_card"
                                accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>

                <!-- Administration Section -->
                <div class="mb-4">
                    <h4 class="text-white mb-3">Administration</h4>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="form-floating">
                                <select class="form-control" id="administration_name" name="administration_name"
                                    required>
                                    <option value="">Select Administration</option>
                                    <option value="Nursing Education Administration">Nursing Education Administration
                                    </option>
                                    <option value="Health Training Administration">Health Training Administration
                                    </option>
                                    <option value="Medical Training Administration">Medical Training Administration
                                    </option>
                                    <option value="Administrative Training Administration">Administrative Training
                                        Administration</option>
                                    <option value="Volunteering Administration">Volunteering Administration</option>
                                    <option value="Contracting / Operation Administration">Contracting / Operation
                                        Administration</option>
                                    <option value="Family Medicine Academy">Family Medicine Academy</option>
                                    <option value="Healthcare Security Administration">Healthcare Security
                                        Administration</option>
                                    <option value="Radiology Administration AAML">Radiology Administration AAML</option>
                                </select>
                                <label for="administration_name">Administration Name</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row g-3 mt-4">
                    <div class="col-md-6">
                        <button class="w-100 btn btn-lg btn-primary" type="submit">Submit Form</button>
                    </div>
                    <div class="col-md-6">
                        <a href="?page=home" class="w-100 btn btn-lg btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <?php
    // Close connection
    $conn = null;
    ?>