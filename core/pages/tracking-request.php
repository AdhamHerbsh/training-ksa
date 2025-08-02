<?php
// Database configuration
require('core/db/connection.php');

// --- Security and Authentication Checks ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ?auth=login");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];
$trainee_data = null;
$application_found = false;

// Fetch trainee data from database
try {
    $sql = "SELECT * FROM trainees WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $trainee_data = $result->fetch_assoc();
        $application_found = true;
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log("Database error fetching trainee data: " . $e->getMessage());
    $_SESSION['error_message'] = $lang['tracking']['errors']['database-error'] ?? "Could not retrieve application data. Please try again later.";
}

$conn->close();

// Determine current step based on status
$current_step = 1;
$step_status = ['received' => true, 'review' => false, 'decision' => false];

if ($application_found) {
    $status = $trainee_data['status'];
    switch ($status) {
        case 'Pending':
            $current_step = 1;
            $step_status = ['received' => true, 'review' => false, 'decision' => false];
            break;
        case 'Reviewed':
            $current_step = 2;
            $step_status = ['received' => true, 'review' => true, 'decision' => false];
            break;
        case 'Accepted':
        case 'Rejected':
            $current_step = 3;
            $step_status = ['received' => true, 'review' => true, 'decision' => true];
            break;
    }
}
?>

<section class="py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="overlay-box py-4 px-4 shadow border border-1 border-secondary rounded-4">
                    <h1 class="h3 mb-4 fw-normal text-white text-center" data-i18n="tracking.title">
                        <?php echo $lang['tracking']['title'] ?? 'Application Tracking'; ?></h1>

                    <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger text-center mb-4" role="alert">
                        <?php echo $_SESSION['error_message'];
                            unset($_SESSION['error_message']); ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!$application_found): ?>
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-search fs-1 mb-3 d-block text-white-50"></i>
                            <h4 class="text-white-50" data-i18n="tracking.no-application">
                                <?php echo $lang['tracking']['no-application'] ?? 'No Application Found'; ?></h4>
                            <p class="text-white-50" data-i18n="tracking.no-application-hint">
                                <?php echo $lang['tracking']['no-application-hint'] ?? 'You haven\'t submitted any training application yet.'; ?>
                            </p>
                            <a href="?page=training-form" class="btn btn-primary mt-3">
                                <i class="bi bi-plus-circle me-2"></i>
                                <span
                                    data-i18n="tracking.apply-now"><?php echo $lang['tracking']['apply-now'] ?? 'Apply Now'; ?></span>
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Success/Error Messages for trainee response -->
                    <div id="responseAlert" class="alert text-center mb-4" style="display: none;" role="alert">
                        <div id="responseMessage"></div>
                    </div>

                    <!-- Progress Tracker -->
                    <div class="mb-5 position-relative" style="height: 80px;">
                        <div class="position-absolute top-50 start-0 w-100 bg-secondary"
                            style="height: 4px; transform: translateY(-50%); z-index: 1;"></div>
                        <div id="progress-fill-line" class="position-absolute top-50 start-0 bg-primary"
                            style="height: 4px; width: <?php echo ($current_step - 1) * 50; ?>%; transform: translateY(-50%); z-index: 1; transition: width 0.5s ease-in-out;">
                        </div>

                        <div
                            class="d-flex justify-content-between w-100 position-absolute top-0 start-0 h-100 align-items-center">
                            <!-- Step 1: Received -->
                            <div class="text-center position-relative z-2 cursor-pointer flex-grow-1"
                                onclick="showStep(1)">
                                <div id="step1"
                                    class="step-circle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 text-white <?php echo $step_status['received'] ? 'bg-primary border-primary' : 'bg-secondary border-secondary'; ?>"
                                    style="width: 40px; height: 40px; font-weight: bold; border-width: 3px !important;">
                                    <?php if ($step_status['received']): ?>
                                    <i class="bi bi-check-lg"></i>
                                    <?php else: ?>
                                    1
                                    <?php endif; ?>
                                </div>
                                <div id="label1" class="step-label text-white fw-bold" style="font-size: 0.85rem;"
                                    data-i18n="tracking.step1"><?php echo $lang['tracking']['step1'] ?? 'Received'; ?>
                                </div>
                                <div class="text-white-50" style="font-size: 0.7rem;">
                                    <?php echo date('M d, Y', strtotime($trainee_data['created_at'])); ?>
                                </div>
                            </div>

                            <!-- Step 2: Under Review -->
                            <div class="text-center position-relative z-2 cursor-pointer flex-grow-1"
                                onclick="showStep(2)">
                                <div id="step2"
                                    class="step-circle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 text-white <?php echo $step_status['review'] ? 'bg-primary border-primary' : 'bg-secondary border-secondary'; ?>"
                                    style="width: 40px; height: 40px; font-weight: bold; border-width: 3px !important;">
                                    <?php if ($step_status['review']): ?>
                                    <i class="bi bi-check-lg"></i>
                                    <?php else: ?>
                                    <i class="bi bi-hourglass-split"></i>
                                    <?php endif; ?>
                                </div>
                                <div id="label2" class="step-label text-white fw-bold" style="font-size: 0.85rem;"
                                    data-i18n="tracking.step2">
                                    <?php echo $lang['tracking']['step2'] ?? 'Under Review'; ?></div>
                                <div class="text-white-50" style="font-size: 0.7rem;">
                                    <?php if ($step_status['review']): ?>
                                    <?php echo date('M d, Y', strtotime($trainee_data['updated_at'])); ?>
                                    <?php else: ?>
                                    <?php echo $lang['tracking']['pending'] ?? 'Pending'; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Step 3: Decision -->
                            <div class="text-center position-relative z-2 cursor-pointer flex-grow-1"
                                onclick="showStep(3)">
                                <div id="step3"
                                    class="step-circle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 text-white <?php echo $step_status['decision'] ? ($trainee_data['status'] == 'Accepted' ? 'bg-success border-success' : 'bg-danger border-danger') : 'bg-secondary border-secondary'; ?>"
                                    style="width: 40px; height: 40px; font-weight: bold; border-width: 3px !important;">
                                    <?php if ($step_status['decision']): ?>
                                    <?php if ($trainee_data['status'] == 'Accepted'): ?>
                                    <i class="bi bi-check-lg"></i>
                                    <?php else: ?>
                                    <i class="bi bi-x-lg"></i>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <i class="bi bi-clock"></i>
                                    <?php endif; ?>
                                </div>
                                <div id="label3" class="step-label text-white fw-bold" style="font-size: 0.85rem;"
                                    data-i18n="tracking.step3"><?php echo $lang['tracking']['step3'] ?? 'Decision'; ?>
                                </div>
                                <div class="text-white-50" style="font-size: 0.7rem;">
                                    <?php if ($step_status['decision']): ?>
                                    <?php echo date('M d, Y', strtotime($trainee_data['updated_at'])); ?>
                                    <?php else: ?>
                                    <?php echo $lang['tracking']['pending'] ?? 'Pending'; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details Sections -->
                    <div class="details-section-container">
                        <!-- Step 1: Application Details -->
                        <div id="details-step1"
                            class="details-section p-4 rounded text-white <?php echo $current_step == 1 ? 'active' : ''; ?>"
                            style="background: rgba(0, 0, 0, 0.5); border: 1px solid #444;">
                            <h5 class="text-white-50 text-start mb-3">
                                <i class="bi bi-person-circle me-2"></i>
                                <span
                                    data-i18n="tracking.application-details"><?php echo $lang['tracking']['application-details'] ?? 'Application Details'; ?></span>
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="<?php echo htmlspecialchars($trainee_data['en_name']); ?>" readonly>
                                        <label
                                            data-i18n="tracking.name"><?php echo $lang['tracking']['name'] ?? 'Name (English)'; ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="<?php echo htmlspecialchars($trainee_data['ar_name']); ?>" readonly>
                                        <label
                                            data-i18n="tracking.name-ar"><?php echo $lang['tracking']['name-ar'] ?? 'Name (Arabic)'; ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="<?php echo htmlspecialchars($trainee_data['uni_id']); ?>" readonly>
                                        <label
                                            data-i18n="tracking.student-id"><?php echo $lang['tracking']['student-id'] ?? 'University ID'; ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="<?php echo htmlspecialchars($trainee_data['national_id']); ?>"
                                            readonly>
                                        <label
                                            data-i18n="tracking.national-id"><?php echo $lang['tracking']['national-id'] ?? 'National ID'; ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="<?php echo htmlspecialchars($trainee_data['major']); ?>" readonly>
                                        <label
                                            data-i18n="tracking.major"><?php echo $lang['tracking']['major'] ?? 'Major'; ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="<?php echo htmlspecialchars($trainee_data['university']); ?>"
                                            readonly>
                                        <label
                                            data-i18n="tracking.university"><?php echo $lang['tracking']['university'] ?? 'University'; ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control"
                                            value="<?php echo htmlspecialchars($trainee_data['country_code'] . $trainee_data['mobile_number']); ?>"
                                            readonly>
                                        <label
                                            data-i18n="tracking.mobile"><?php echo $lang['tracking']['mobile'] ?? 'Mobile Number'; ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control"
                                            value="<?php echo htmlspecialchars($trainee_data['email']); ?>" readonly>
                                        <label
                                            data-i18n="tracking.email"><?php echo $lang['tracking']['email'] ?? 'Email'; ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="<?php echo date('F d, Y', strtotime($trainee_data['created_at'])); ?>"
                                            readonly>
                                        <label
                                            data-i18n="tracking.application-date"><?php echo $lang['tracking']['application-date'] ?? 'Application Date'; ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="<?php echo htmlspecialchars($trainee_data['center']); ?>" readonly>
                                        <label
                                            data-i18n="tracking.training-center"><?php echo $lang['tracking']['training-center'] ?? 'Training Center'; ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Review Status -->
                        <div id="details-step2"
                            class="details-section p-4 rounded text-white <?php echo $current_step == 2 ? 'active' : ''; ?>"
                            style="background: rgba(0, 0, 0, 0.5); border: 1px solid #444;">
                            <h5 class="text-white-50 text-start mb-3">
                                <i class="bi bi-search me-2"></i>
                                <span
                                    data-i18n="tracking.review-status"><?php echo $lang['tracking']['review-status'] ?? 'Review Status'; ?></span>
                            </h5>

                            <?php if ($trainee_data['status'] == 'Pending'): ?>
                            <div class="alert alert-warning">
                                <div class="d-flex align-items-center">
                                    <div class="spinner-border spinner-border-sm text-warning me-3" role="status"></div>
                                    <div>
                                        <h6 class="mb-1" data-i18n="tracking.pending-title">
                                            <?php echo $lang['tracking']['pending-title'] ?? 'Application Under Initial Review'; ?>
                                        </h6>
                                        <p class="mb-0 small" data-i18n="tracking.pending-message">
                                            <?php echo $lang['tracking']['pending-message'] ?? 'Your application has been received and is waiting for supervisor review.'; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php elseif ($trainee_data['status'] == 'Reviewed'): ?>
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-hourglass-split fs-4 text-info me-3"></i>
                                    <div>
                                        <h6 class="mb-1" data-i18n="tracking.reviewed-title">
                                            <?php echo $lang['tracking']['reviewed-title'] ?? 'Application Under Final Review'; ?>
                                        </h6>
                                        <p class="mb-0 small" data-i18n="tracking.reviewed-message">
                                            <?php echo $lang['tracking']['reviewed-message'] ?? 'Your application is being evaluated by the training committee. You will be notified once the decision is made.'; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="row g-3 mt-3">
                                <div class="col-md-6">
                                    <div class="card bg-dark border-secondary">
                                        <div class="card-body text-center">
                                            <h6 class="card-title text-white-50" data-i18n="tracking.training-period">
                                                <?php echo $lang['tracking']['training-period'] ?? 'Training Period'; ?>
                                            </h6>
                                            <p class="card-text text-white">
                                                <?php echo date('M d', strtotime($trainee_data['start_date'])) . ' - ' . date('M d, Y', strtotime($trainee_data['end_date'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-dark border-secondary">
                                        <div class="card-body text-center">
                                            <h6 class="card-title text-white-50" data-i18n="tracking.supervisor">
                                                <?php echo $lang['tracking']['supervisor'] ?? 'Supervisor'; ?></h6>
                                            <p class="card-text text-white">
                                                <?php echo htmlspecialchars($trainee_data['supervisor_name']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Decision -->
                        <div id="details-step3"
                            class="details-section p-4 rounded text-white <?php echo $current_step == 3 ? 'active' : ''; ?>"
                            style="background: rgba(0, 0, 0, 0.5); border: 1px solid #444;">
                            <h5 class="text-white-50 text-start mb-3">
                                <i class="bi bi-clipboard-check me-2"></i>
                                <span
                                    data-i18n="tracking.application-result"><?php echo $lang['tracking']['application-result'] ?? 'Application Result'; ?></span>
                            </h5>

                            <?php if ($trainee_data['status'] == 'Accepted'): ?>
                            <div class="text-center mb-4">
                                <div style="font-size: 3rem; color: #28a745;">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <h3 class="text-success mt-2" data-i18n="tracking.accepted-title">
                                    <?php echo $lang['tracking']['accepted-title'] ?? 'Congratulations!'; ?></h3>
                                <p class="text-white-50" data-i18n="tracking.accepted-message">
                                    <?php echo $lang['tracking']['accepted-message'] ?? 'Your training application has been accepted. Please confirm your participation below.'; ?>
                                </p>
                            </div>

                            <?php if (empty($trainee_data['trainee_response'])): ?>
                            <!-- Trainee Response Buttons -->
                            <div class="alert alert-info">
                                <h6 class="mb-2" data-i18n="tracking.response-required">
                                    <?php echo $lang['tracking']['response-required'] ?? 'Response Required'; ?></h6>
                                <p class="mb-0 small" data-i18n="tracking.response-message">
                                    <?php echo $lang['tracking']['response-message'] ?? 'Please confirm whether you accept or reject this training opportunity.'; ?>
                                </p>
                            </div>

                            <div class="row g-3 mt-3">
                                <div class="col-md-6">
                                    <button class="w-100 btn btn-lg btn-success" id="acceptTraining"
                                        data-id="<?php echo $trainee_data['id']; ?>">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <span
                                            data-i18n="tracking.accept-training"><?php echo $lang['tracking']['accept-training'] ?? 'Accept Training'; ?></span>
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button class="w-100 btn btn-lg btn-outline-danger" id="rejectTraining"
                                        data-id="<?php echo $trainee_data['id']; ?>">
                                        <i class="bi bi-x-circle me-2"></i>
                                        <span
                                            data-i18n="tracking.reject-training"><?php echo $lang['tracking']['reject-training'] ?? 'Reject Training'; ?></span>
                                    </button>
                                </div>
                            </div>
                            <?php else: ?>
                            <!-- Show trainee's response -->
                            <div
                                class="alert <?php echo $trainee_data['trainee_response'] == 'Accepted' ? 'alert-success' : 'alert-danger'; ?>">
                                <div class="d-flex align-items-center">
                                    <i
                                        class="bi bi-<?php echo $trainee_data['trainee_response'] == 'Accepted' ? 'check-circle-fill' : 'x-circle-fill'; ?> fs-4 me-3"></i>
                                    <div>
                                        <h6 class="mb-1"
                                            data-i18n="tracking.trainee-response.<?php echo strtolower($trainee_data['trainee_response']); ?>">
                                            <?php echo $lang['tracking']['trainee-response'][strtolower($trainee_data['trainee_response'])] ?? ($trainee_data['trainee_response'] == 'Accepted' ? 'Training Accepted' : 'Training Rejected'); ?>
                                        </h6>
                                        <p class="mb-0 small"
                                            data-i18n="tracking.response-details.<?php echo strtolower($trainee_data['trainee_response']); ?>">
                                            <?php echo $lang['tracking']['response-details'][strtolower($trainee_data['trainee_response'])] ?? 'You have ' . strtolower($trainee_data['trainee_response']) . ' this training opportunity.'; ?>
                                            <?php if (!empty($trainee_data['response_date'])): ?>
                                            <br><?php echo $lang['tracking']['response-date'] ?? 'Response Date'; ?>:
                                            <?php echo date('F d, Y g:i A', strtotime($trainee_data['response_date'])); ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php elseif ($trainee_data['status'] == 'Rejected'): ?>
                            <div class="text-center mb-4">
                                <div style="font-size: 3rem; color: #dc3545;">
                                    <i class="bi bi-x-circle-fill"></i>
                                </div>
                                <h3 class="text-danger mt-2" data-i18n="tracking.rejected-title">
                                    <?php echo $lang['tracking']['rejected-title'] ?? 'Application Not Accepted'; ?>
                                </h3>
                                <p class="text-white-50" data-i18n="tracking.rejected-message">
                                    <?php echo $lang['tracking']['rejected-message'] ?? 'Unfortunately, your training application was not accepted at this time.'; ?>
                                </p>
                            </div>

                            <div class="alert alert-info">
                                <h6 class="mb-2" data-i18n="tracking.reapply-title">
                                    <?php echo $lang['tracking']['reapply-title'] ?? 'You can apply again'; ?></h6>
                                <p class="mb-0 small" data-i18n="tracking.reapply-message">
                                    <?php echo $lang['tracking']['reapply-message'] ?? 'Don\'t give up! You can submit a new application for future training periods.'; ?>
                                </p>
                            </div>

                            <div class="text-center mt-3">
                                <a href="?page=apply" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    <span
                                        data-i18n="tracking.apply-again"><?php echo $lang['tracking']['apply-again'] ?? 'Apply Again'; ?></span>
                                </a>
                            </div>
                            <?php endif; ?>

                            <!-- Application Summary -->
                            <div class="row g-3 mt-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="TR-<?php echo str_pad($trainee_data['id'], 6, '0', STR_PAD_LEFT); ?>"
                                            readonly>
                                        <label
                                            data-i18n="tracking.application-number"><?php echo $lang['tracking']['application-number'] ?? 'Application Number'; ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="<?php echo date('F d, Y', strtotime($trainee_data['created_at'])); ?>"
                                            readonly>
                                        <label
                                            data-i18n="tracking.application-date"><?php echo $lang['tracking']['application-date'] ?? 'Application Date'; ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="<?php echo date('F d, Y', strtotime($trainee_data['updated_at'])); ?>"
                                            readonly>
                                        <label
                                            data-i18n="tracking.last-updated"><?php echo $lang['tracking']['last-updated'] ?? 'Last Updated'; ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="<?php echo htmlspecialchars($trainee_data['status']); ?>" readonly>
                                        <label
                                            data-i18n="tracking.current-status"><?php echo $lang['tracking']['current-status'] ?? 'Current Status'; ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show alert function
    function showAlert(message, type = 'success') {
        const alertContainer = document.getElementById('responseAlert');
        const alertMessage = document.getElementById('responseMessage');

        alertContainer.className = `alert text-center mb-4 alert-${type}`;
        alertMessage.textContent = message;
        alertContainer.style.display = 'block';

        // Auto-hide after 5 seconds
        setTimeout(() => {
            alertContainer.style.display = 'none';
        }, 5000);
    }

    // Function to show specific step details
    window.showStep = function(stepNumber) {
        // Hide all details sections
        document.querySelectorAll('.details-section').forEach(section => {
            section.classList.remove('active');
        });

        // Show selected section
        const targetSection = document.getElementById(`details-step${stepNumber}`);
        if (targetSection) {
            targetSection.classList.add('active');
        }
    };

    // Handle trainee response (Accept/Reject training)
    const acceptBtn = document.getElementById('acceptTraining');
    const rejectBtn = document.getElementById('rejectTraining');

    if (acceptBtn) {
        acceptBtn.addEventListener('click', function() {
            const traineeId = this.dataset.id;
            if (confirm(
                    '<?php echo $lang['tracking']['confirm-accept'] ?? 'Are you sure you want to accept this training opportunity?'; ?>'
                    )) {
                sendTraineeResponse(traineeId, 'Accepted');
            }
        });
    }

    if (rejectBtn) {
        rejectBtn.addEventListener('click', function() {
            const traineeId = this.dataset.id;
            if (confirm(
                    '<?php echo $lang['tracking']['confirm-reject'] ?? 'Are you sure you want to reject this training opportunity? This action cannot be undone.'; ?>'
                    )) {
                sendTraineeResponse(traineeId, 'Rejected');
            }
        });
    }

    // Function to send trainee response via AJAX
    function sendTraineeResponse(traineeId, response) {
        // Disable buttons to prevent double submission
        if (acceptBtn) acceptBtn.disabled = true;
        if (rejectBtn) rejectBtn.disabled = true;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'core/api/trainee-response.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const result = JSON.parse(xhr.responseText);
                    if (result.success) {
                        showAlert(result.message, 'success');
                        // Reload page after 2 seconds to reflect changes
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        showAlert(result.message, 'danger');
                        // Re-enable buttons on error
                        if (acceptBtn) acceptBtn.disabled = false;
                        if (rejectBtn) rejectBtn.disabled = false;
                    }
                } catch (e) {
                    showAlert(
                        '<?php echo $lang['tracking']['errors']['invalid-response'] ?? 'Invalid response from server'; ?>',
                        'danger');
                    if (acceptBtn) acceptBtn.disabled = false;
                    if (rejectBtn) rejectBtn.disabled = false;
                }
            } else {
                showAlert(
                    `<?php echo $lang['tracking']['errors']['server-error'] ?? 'Server error: '; ?>${xhr.status}`,
                    'danger');
                if (acceptBtn) acceptBtn.disabled = false;
                if (rejectBtn) rejectBtn.disabled = false;
            }
        };

        xhr.onerror = function() {
            showAlert(
                '<?php echo $lang['tracking']['errors']['network-error'] ?? 'Network error. Could not connect to server.'; ?>',
                'danger');
            if (acceptBtn) acceptBtn.disabled = false;
            if (rejectBtn) rejectBtn.disabled = false;
        };

        xhr.send(`trainee_id=${encodeURIComponent(traineeId)}&response=${encodeURIComponent(response)}`);
    }

    // Auto-refresh every 30 seconds if application is still under review
    <?php if ($application_found && in_array($trainee_data['status'], ['Pending', 'Reviewed'])): ?>
    setInterval(function() {
        // Only refresh if user is not interacting with the page
        if (document.visibilityState === 'visible') {
            location.reload();
        }
    }, 30000); // 30 seconds
    <?php endif; ?>
});
</script>

<style>
.details-section {
    display: none;
    animation: fadeIn 0.3s ease-in-out;
}

.details-section.active {
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.step-circle {
    transition: all 0.3s ease;
    cursor: pointer;
}

.step-circle:hover {
    transform: scale(1.05);
}

.cursor-pointer {
    cursor: pointer;
}

.cursor-pointer:hover .step-label {
    text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
}

.alert {
    border: none;
    border-radius: 0.5rem;
}

.form-control:read-only {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
    color: #fff;
}

.form-floating>label {
    color: rgba(255, 255, 255, 0.7);
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .step-circle {
        width: 35px !important;
        height: 35px !important;
    }

    .step-label {
        font-size: 0.75rem !important;
    }

    .btn-lg {
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }
}

/* Print styles */
@media print {

    .btn,
    .alert,
    #responseAlert {
        display: none !important;
    }

    .overlay-box {
        background: white !important;
        color: black !important;
        border: 1px solid #000 !important;
    }

    .text-white,
    .text-white-50 {
        color: black !important;
    }

    .form-control {
        background: white !important;
        color: black !important;
        border: 1px solid #000 !important;
    }
}

/* Loading animation for buttons */
.btn.loading::after {
    content: "";
    display: inline-block;
    width: 1rem;
    height: 1rem;
    margin-left: 0.5rem;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

/* Status-specific animations */
.alert-success {
    animation: slideInSuccess 0.5s ease-out;
}

.alert-danger {
    animation: slideInError 0.5s ease-out;
}

@keyframes slideInSuccess {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }

    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideInError {
    from {
        transform: translateX(100%);
        opacity: 0;
    }

    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Progress line animation */
#progress-fill-line {
    background: linear-gradient(90deg, #007bff 0%, #28a745 100%);
}

/* Hover effects for interactive elements */
.details-section-container {
    min-height: 400px;
}

.overlay-box {
    backdrop-filter: blur(10px);
    background: rgba(0, 0, 0, 0.8) !important;
}

/* Enhanced visual feedback */
.step-circle.bg-primary {
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
}

.step-circle.bg-success {
    box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
}

.step-circle.bg-danger {
    box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
}
</style>