<?php
// Database configuration
// Assuming 'core/db/connection.php' contains your database connection logic
// and sets a $conn variable for the database connection.
require_once('core/db/connection.php'); // Use require_once to prevent multiple inclusions

// --- Security and Authentication Checks ---
// Check if user is authenticated
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ?auth=login"); // Redirect to login page
    exit();
}

// Check if user is a Supervisor
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Supervisor') {
    header("Location: ?page=home"); // Redirect if not a Supervisor
    exit();
}
// Fetch data from the clearance_forms table
$sql = "SELECT id, user_id, name_ar, name_en, id_iqama_number, nationality, country_code, mobile_number, email, department, job_title, submission_date, work_card_expiry_date, has_housing, id_photo_path, work_card_path, administration_name, created_at FROM clearance_forms ORDER BY created_at DESC";
$result = $conn->query($sql);

$clearance_data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $clearance_data[] = $row;
    }
}

$conn->close();
?>

<section class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="overlay-box p-3 p-sm-4 shadow rounded-4">
                    <div class="row align-items-center mb-4 gy-3">
                        <div class="col-12 col-sm-6 col-md-4">
                            <h2 class="h3 mb-0 text-white" data-i18n="clearance.title">Clearance Form Responses</h2>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary">
                                    <i class="bi bi-search text-white"></i>
                                </span>
                                <input type="text" class="form-control bg-dark text-white border-secondary"
                                    id="searchClearance" placeholder="Search..."
                                    data-i18n-placeholder="clearance.search-placeholder" aria-label="Search responses">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="d-flex gap-2">
                                <select id="adminFilter"
                                    class="form-select bg-dark text-white border-secondary flex-grow-1">
                                    <option value="all" data-i18n="clearance.all-departments">كل الإدارات</option>
                                    <option value="Nursing Education Administration"
                                        data-i18n="clearance.nursing-education">Nursing Education Administration
                                    </option>
                                    <option value="Health Training Administration"
                                        data-i18n="clearance.health-training">Health Training Administration</option>
                                    <option value="Medical Training Administration"
                                        data-i18n="clearance.medical-training">Medical Training Administration</option>
                                    <option value="Administrative Training Administration"
                                        data-i18n="clearance.administrative-training">Administrative Training
                                        Administration</option>
                                    <option value="Volunteering Administration" data-i18n="clearance.volunteering">
                                        Volunteering Administration</option>
                                    <option value="Contracting / Operation Administration"
                                        data-i18n="clearance.contracting-operation">Contracting / Operation
                                        Administration</option>
                                    <option value="Family Medicine Academy" data-i18n="clearance.family-medicine">Family
                                        Medicine Academy</option>
                                    <option value="Healthcare Security Administration"
                                        data-i18n="clearance.healthcare-security">Healthcare Security Administration
                                    </option>
                                    <option value="Radiology Administration AAML" data-i18n="clearance.radiology">
                                        Radiology Administration AAML</option>
                                </select>
                                <button onclick="window.print()"
                                    class="btn btn-light d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-printer"></i>
                                    <span data-i18n="clearance.print">Print</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 70vh;">
                        <table id="responsesTable"
                            class="table table-hover align-middle mb-0 text-white clearance-table">
                            <thead class="sticky-top">
                                <tr class="align-middle">
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="clearance.date">
                                        Date
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="date"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="clearance.form-date">
                                        Form Date
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="date"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="clearance.housing">Housing</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="clearance.nationality">
                                        Nationality
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="clearance.arabic-name">
                                        Arabic Name
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="clearance.english-name">
                                        English Name
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="clearance.id">ID/Iqama</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="clearance.mobile">Mobile</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="clearance.email">Email</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="clearance.department">
                                        Department
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="clearance.title">Job Title</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="clearance.card-expiry">
                                        Card Expiry
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="date"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="clearance.section">
                                        Administration
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="clearance.documents">Documents</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($clearance_data)): ?>
                                    <tr>
                                        <td colspan="14" class="text-center border-bottom border-secondary"
                                            data-i18n="clearance.no-data">No clearance
                                            responses found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($clearance_data as $row): ?>
                                        <tr>
                                            <td class="border-bottom border-secondary">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-calendar text-info"></i>
                                                    <small><?php echo date('Y-m-d h:i a', strtotime($row['created_at'])); ?></small>
                                                </div>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <small><?php echo htmlspecialchars($row['submission_date']); ?></small>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <?php
                                                $badge_class = ($row['has_housing'] == 'Yes') ? 'bg-success' : 'bg-danger';
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <?php echo htmlspecialchars($row['has_housing']); ?>
                                                </span>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <?php echo htmlspecialchars($row['nationality']); ?></td>
                                            <td class="border-bottom border-secondary">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center"
                                                        style="width: 35px; height: 35px;">
                                                        <span
                                                            class="text-white fw-bold"><?php echo htmlspecialchars(mb_substr($row['name_ar'], 0, 1)); ?></span>
                                                    </div>
                                                    <span><?php echo htmlspecialchars($row['name_ar']); ?></span>
                                                </div>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <?php echo htmlspecialchars($row['name_en']); ?></td>
                                            <td class="border-bottom border-secondary">
                                                <span
                                                    class="badge bg-secondary"><?php echo htmlspecialchars($row['id_iqama_number']); ?></span>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <a href="tel:<?php echo htmlspecialchars($row['country_code'] . $row['mobile_number']); ?>"
                                                    class="text-decoration-none text-white">
                                                    <i class="bi bi-telephone-fill text-muted me-1"></i>
                                                    <?php echo htmlspecialchars($row['country_code'] . $row['mobile_number']); ?>
                                                </a>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"
                                                    class="text-decoration-none text-white">
                                                    <i class="bi bi-envelope-fill text-muted me-1"></i>
                                                    <?php echo htmlspecialchars($row['email']); ?>
                                                </a>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <?php echo htmlspecialchars($row['department']); ?></td>
                                            <td class="border-bottom border-secondary">
                                                <span
                                                    class="badge bg-info text-dark"><?php echo htmlspecialchars($row['job_title']); ?></span>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-calendar-check text-success"></i>
                                                    <small><?php echo htmlspecialchars($row['work_card_expiry_date']); ?></small>
                                                </div>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <?php echo htmlspecialchars($row['administration_name']); ?></td>
                                            <td class="border-bottom border-secondary text-nowrap">
                                                <?php if (!empty($row['id_photo_path'])): ?>
                                                    <a href="<?php echo htmlspecialchars($row['id_photo_path']); ?>" target="_blank"
                                                        class="btn btn-sm btn-outline-primary mb-1 me-1">
                                                        <i class="bi bi-file-earmark-image"></i> <span
                                                            data-i18n="clearance.id-photo">ID Photo</span>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($row['work_card_path'])): ?>
                                                    <a href="<?php echo htmlspecialchars($row['work_card_path']); ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                                        <i class="bi bi-file-earmark-medical"></i> <span
                                                            data-i18n="clearance.work-card">Work Card</span>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (empty($row['id_photo_path']) && empty($row['work_card_path'])): ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-block d-sm-none mt-3">
                        <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                            <i class="bi bi-info-circle"></i>
                            <span data-i18n="clearance.scroll-hint">Scroll horizontally to view all data</span>
                        </div>
                    </div>

                    <div id="emptyClearanceState"
                        class="<?php echo empty($clearance_data) ? 'd-block' : 'd-none'; ?> text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-inbox-fill fs-1 mb-3 d-block"></i>
                            <h5 data-i18n="clearance.no-data">No clearance responses found</h5>
                            <p class="small" data-i18n="clearance.no-data-hint">Responses will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>