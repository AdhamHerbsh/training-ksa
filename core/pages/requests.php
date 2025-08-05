<?php

// Database configuration
require('core/db/connection.php');

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

// Fetch data from the database
$requests = [];
try {
    $sql = "SELECT * FROM trainees WHERE status LIKE 'Pending' ORDER BY created_at DESC"; // Order by most recent
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
    }
} catch (mysqli_sql_exception $e) {
    // Log the error and show a user-friendly message
    error_log("Database error fetching trainees: " . $e->getMessage());
    $_SESSION['error_message'] = $lang['requests']['errors']['database-error'] ?? "Could not retrieve training requests. Please try again later.";
}

$conn->close();

?>

<section class="py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="overlay-box p-3 p-sm-4 shadow rounded-4">
                    <div class="row">
                        <h2 class="h3 mb-0 text-white text-center mb-3" data-i18n="requests.title">
                            <?php echo $lang['requests']['title'] ?? 'Co-op Training Requests'; ?></h2>
                    </div>

                    <div id="alertContainer" class="mb-3" style="display: none;">
                        <div id="alertMessage" class="alert text-center" role="alert"></div>
                    </div>

                    <div class="row align-items-center mb-4 gy-3 justify-content-center">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary">
                                    <i class="bi bi-search text-white"></i>
                                </span>
                                <input type="text" class="form-control bg-dark text-white border-secondary"
                                    id="searchRequests"
                                    placeholder="<?php echo $lang['requests']['search-placeholder'] ?? 'Search requests...'; ?>"
                                    data-i18n="[placeholder]requests.search-placeholder" aria-label="Search requests">
                            </div>
                        </div>
                        <div class="col-12 col-md-5">
                            <div class="d-flex gap-2 justify-content-end flex-wrap">
                                <button class="btn btn-success d-inline-flex align-items-center gap-2" id="bulkAccept"
                                    disabled>
                                    <i class="bi bi-check-circle"></i>
                                    <span
                                        data-i18n="requests.bulk-accept"><?php echo $lang['requests']['bulk-accept'] ?? 'Accept Selected'; ?></span>
                                    <span id="selectedCount" class="badge bg-light text-dark ms-1"
                                        style="display: none;">0</span>
                                </button>
                                <button class="btn btn-danger d-inline-flex align-items-center gap-2" id="bulkReject"
                                    disabled>
                                    <i class="bi bi-x-circle"></i>
                                    <span
                                        data-i18n="requests.bulk-reject"><?php echo $lang['requests']['bulk-reject'] ?? 'Reject Selected'; ?></span>
                                </button>
                                <button class="btn btn-light d-inline-flex align-items-center gap-2"
                                    onclick="window.print()">
                                    <i class="bi bi-printer"></i>
                                    <span
                                        data-i18n="requests.print"><?php echo $lang['requests']['print'] ?? 'Print'; ?></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <?php echo $_SESSION['error_message'];
                            unset($_SESSION['error_message']); ?>
                        </div>
                    <?php endif; ?>

                    <div id="loadingOverlay" class="position-relative" style="display: none;">
                        <div
                            class="position-absolute top-50 start-50 translate-middle bg-dark bg-opacity-75 rounded p-3">
                            <div class="d-flex align-items-center gap-2 text-white">
                                <div class="spinner-border spinner-border-sm" role="status"></div>
                                <span
                                    data-i18n="requests.processing"><?php echo $lang['requests']['processing'] ?? 'Processing...'; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 70vh;">
                        <table class="table table-hover align-middle mb-0 text-white requests-table">
                            <thead class="sticky-top">
                                <tr class="align-middle">
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.name-ar">
                                        <?php echo $lang['requests']['name-ar'] ?? 'Name (Arabic)'; ?>
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.name-en">
                                        <?php echo $lang['requests']['name-en'] ?? 'Name (English)'; ?>
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.id"><?php echo $lang['requests']['id'] ?? 'National ID'; ?>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.age"><?php echo $lang['requests']['age'] ?? 'Age'; ?>
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="number"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.mobile">
                                        <?php echo $lang['requests']['mobile'] ?? 'Mobile'; ?></th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.email"><?php echo $lang['requests']['email'] ?? 'Email'; ?>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.country">
                                        <?php echo $lang['requests']['country'] ?? 'Country'; ?></th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.gender">
                                        <?php echo $lang['requests']['gender'] ?? 'Gender'; ?></th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.university">
                                        <?php echo $lang['requests']['university'] ?? 'University'; ?>
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.student-no">
                                        <?php echo $lang['requests']['student-no'] ?? 'University ID'; ?></th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.major"><?php echo $lang['requests']['major'] ?? 'Major'; ?>
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.degree">
                                        <?php echo $lang['requests']['degree'] ?? 'Degree'; ?></th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.start-date">
                                        <?php echo $lang['requests']['start-date'] ?? 'Start Date'; ?>
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="date"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.end-date">
                                        <?php echo $lang['requests']['end-date'] ?? 'End Date'; ?></th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.supervisor">
                                        <?php echo $lang['requests']['supervisor'] ?? 'Supervisor'; ?></th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.supervisor-email">
                                        <?php echo $lang['requests']['supervisor-email'] ?? 'Supervisor Email'; ?></th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.center">
                                        <?php echo $lang['requests']['center'] ?? 'Center'; ?></th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.letter">
                                        <?php echo $lang['requests']['letter'] ?? 'Training Letter'; ?></th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.cv"><?php echo $lang['requests']['cv'] ?? 'CV'; ?></th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.status">
                                        <?php echo $lang['requests']['status'] ?? 'Status'; ?></th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.actions">
                                        <?php echo $lang['requests']['actions'] ?? 'Actions'; ?></th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                                <?php if (!empty($requests)): ?>
                                    <?php foreach ($requests as $request): ?>
                                        <tr data-id="<?php echo htmlspecialchars($request['id']); ?>">
                                            <td class="border-bottom border-secondary">
                                                <div class="form-check">
                                                    <input class="form-check-input studentCheckbox" type="checkbox"
                                                        value="<?php echo htmlspecialchars($request['id']); ?>">
                                                </div>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <?php echo htmlspecialchars($request['ar_name']); ?></td>
                                            <td class="border-bottom border-secondary">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center"
                                                        style="width: 35px; height: 35px;">
                                                        <span
                                                            class="text-white fw-bold"><?php echo htmlspecialchars(strtoupper(substr($request['en_name'], 0, 1))); ?></span>
                                                    </div>
                                                    <span><?php echo htmlspecialchars($request['en_name']); ?></span>
                                                </div>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <span
                                                    class="badge bg-secondary"><?php echo htmlspecialchars($request['national_id']); ?></span>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <?php echo htmlspecialchars($request['age']); ?></td>
                                            <td class="border-bottom border-secondary">
                                                <a href="tel:<?php echo htmlspecialchars($request['country_code'] . $request['mobile_number']); ?>"
                                                    class="text-decoration-none text-white">
                                                    <i class="bi bi-telephone-fill text-muted me-1"></i>
                                                    <?php echo htmlspecialchars($request['country_code'] . $request['mobile_number']); ?>
                                                </a>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <a href="mailto:<?php echo htmlspecialchars($request['email']); ?>"
                                                    class="text-decoration-none text-white">
                                                    <i class="bi bi-envelope-fill text-muted me-1"></i>
                                                    <?php echo htmlspecialchars($request['email']); ?>
                                                </a>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <?php echo htmlspecialchars($request['country']); ?></td>
                                            <td class="border-bottom border-secondary">
                                                <?php if ($request['gender'] == 'Female'): ?>
                                                    <i class="bi bi-gender-female text-pink me-1"></i>
                                                    <span
                                                        data-i18n="requests.gender-female"><?php echo $lang['requests']['gender-female'] ?? 'أنثى'; ?></span>
                                                <?php else: ?>
                                                    <i class="bi bi-gender-male text-info me-1"></i>
                                                    <span
                                                        data-i18n="requests.gender-male"><?php echo $lang['requests']['gender-male'] ?? 'ذكر'; ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <?php echo htmlspecialchars($request['university']); ?></td>
                                            <td class="border-bottom border-secondary">
                                                <span
                                                    class="badge bg-secondary"><?php echo htmlspecialchars($request['uni_id']); ?></span>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <span
                                                    class="badge bg-info text-dark"><?php echo htmlspecialchars($request['major']); ?></span>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <span
                                                    class="badge bg-primary"><?php echo htmlspecialchars($request['degree']); ?></span>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-calendar text-info"></i>
                                                    <small><?php echo htmlspecialchars($request['start_date']); ?></small>
                                                </div>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-calendar text-warning"></i>
                                                    <small><?php echo htmlspecialchars($request['end_date']); ?></small>
                                                </div>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <?php echo htmlspecialchars($request['supervisor_name']); ?></td>
                                            <td class="border-bottom border-secondary">
                                                <a href="mailto:<?php echo htmlspecialchars($request['supervisor_email']); ?>"
                                                    class="text-decoration-none text-white">
                                                    <i class="bi bi-envelope-fill text-muted me-1"></i>
                                                    <?php echo htmlspecialchars($request['supervisor_email']); ?>
                                                </a>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <?php echo htmlspecialchars($request['center']); ?></td>
                                            <td class="border-bottom border-secondary">
                                                <?php if (!empty($request['training_letter_path'])): ?>
                                                    <a href="<?php echo htmlspecialchars($request['training_letter_path']); ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-file-text me-1"></i>
                                                        <span
                                                            data-i18n="requests.view"><?php echo $lang['requests']['view'] ?? 'View'; ?></span>
                                                    </a>
                                                <?php else: ?>
                                                    <span
                                                        data-i18n="requests.na"><?php echo $lang['requests']['na'] ?? 'N/A'; ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <?php if (!empty($request['cv_path'])): ?>
                                                    <a href="<?php echo htmlspecialchars($request['cv_path']); ?>" target="_blank"
                                                        class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-file-person me-1"></i>
                                                        <span
                                                            data-i18n="requests.view"><?php echo $lang['requests']['view'] ?? 'View'; ?></span>
                                                    </a>
                                                <?php else: ?>
                                                    <span
                                                        data-i18n="requests.na"><?php echo $lang['requests']['na'] ?? 'N/A'; ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <?php
                                                $status_class = '';
                                                switch ($request['status']) {
                                                    case 'Accepted':
                                                        $status_class = 'bg-success';
                                                        break;
                                                    case 'Rejected':
                                                        $status_class = 'bg-danger';
                                                        break;
                                                    case 'Reviewed':
                                                        $status_class = 'bg-primary';
                                                        break;
                                                    default:
                                                        $status_class = 'bg-warning text-dark';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge status-badge <?php echo $status_class; ?>"
                                                    data-i18n="requests.status-<?php echo strtolower($request['status']); ?>"><?php echo $lang['requests']['status-' . strtolower($request['status'])] ?? htmlspecialchars($request['status']); ?></span>
                                            </td>
                                            <td class="border-bottom border-secondary">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-warning"
                                                        title="<?php echo $lang['requests']['schedule-interview'] ?? 'Schedule Interview'; ?>"
                                                        data-i18n="[title]requests.schedule-interview"
                                                        onclick="showScheduleModal(<?php echo htmlspecialchars($request['id']); ?>, '<?php echo htmlspecialchars($request['email']); ?>')">
                                                        <i class="bi bi-camera-video"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-success"
                                                        title="<?php echo $lang['requests']['accept'] ?? 'Accept'; ?>"
                                                        data-i18n="[title]requests.accept"
                                                        onclick="handleStatusUpdate(<?php echo htmlspecialchars($request['id']); ?>, '<?php echo htmlspecialchars($request['en_name']); ?>', 'Accepted')">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger"
                                                        title="<?php echo $lang['requests']['reject'] ?? 'Reject'; ?>"
                                                        data-i18n="[title]requests.reject"
                                                        onclick="handleStatusUpdate(<?php echo htmlspecialchars($request['id']); ?>, '<?php echo htmlspecialchars($request['en_name']); ?>', 'Rejected')">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="22">
                                            <div id="emptyRequestsState" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="bi bi-inbox-fill fs-1 mb-3 d-block"></i>
                                                    <h5 data-i18n="requests.no-data">
                                                        <?php echo $lang['requests']['no-data'] ?? 'No training requests found'; ?>
                                                    </h5>
                                                    <p class="small" data-i18n="requests.no-data-hint">
                                                        <?php echo $lang['requests']['no-data-hint'] ?? 'New requests will appear here'; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-block d-sm-none mt-3">
                        <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                            <i class="bi bi-info-circle"></i>
                            <span
                                data-i18n="requests.scroll-hint"><?php echo $lang['requests']['scroll-hint'] ?? 'Scroll horizontally to view all data'; ?></span>
                        </div>
                    </div>

                    <?php if (empty($requests)): ?>
                        <div id="emptyRequestsState" class="text-center py-5 d-none">
                            <div class="text-muted">
                                <i class="bi bi-inbox-fill fs-1 mb-3 d-block"></i>
                                <h5 data-i18n="requests.no-data">
                                    <?php echo $lang['requests']['no-data'] ?? 'No training requests found'; ?></h5>
                                <p class="small" data-i18n="requests.no-data-hint">
                                    <?php echo $lang['requests']['no-data-hint'] ?? 'New requests will appear here'; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php require 'core/pages/schedule-interview-modal.php'; ?>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchRequests = document.getElementById('searchRequests');
        const studentsTableBody = document.getElementById('studentsTableBody');
        const emptyRequestsState = document.getElementById('emptyRequestsState');
        const selectAllCheckbox = document.getElementById('selectAll');
        const bulkAcceptBtn = document.getElementById('bulkAccept');
        const bulkRejectBtn = document.getElementById('bulkReject');
        const selectedCount = document.getElementById('selectedCount');
        const alertContainer = document.getElementById('alertContainer');
        const alertMessage = document.getElementById('alertMessage');
        const loadingOverlay = document.getElementById('loadingOverlay');

        // Function to show alert messages
        function showAlert(message, type = 'success') {
            alertMessage.className = `alert text-center alert-${type}`;
            alertMessage.textContent = message;
            alertContainer.style.display = 'block';

            // Auto-hide after 5 seconds
            setTimeout(() => {
                alertContainer.style.display = 'none';
            }, 5000);
        }

        // Function to show/hide loading overlay
        function toggleLoading(show) {
            loadingOverlay.style.display = show ? 'block' : 'none';
        }

        // Function to filter table rows based on search input
        function filterTable() {
            const searchTerm = searchRequests.value.toLowerCase();
            let visibleRows = 0;

            Array.from(studentsTableBody.rows).forEach(row => {
                if (row.querySelector('td[colspan="22"]')) return; // Skip empty state row

                const textContent = row.textContent.toLowerCase();
                if (textContent.includes(searchTerm)) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (visibleRows === 0 && studentsTableBody.querySelector('td[colspan="22"]') === null) {
                emptyRequestsState.classList.remove('d-none');
            } else {
                emptyRequestsState.classList.add('d-none');
            }
        }

        // Function to update selected count and button states
        function updateBulkButtonsState() {
            const checkboxes = document.querySelectorAll('.studentCheckbox');
            const checkedBoxes = document.querySelectorAll('.studentCheckbox:checked');
            const count = checkedBoxes.length;

            if (count > 0) {
                bulkAcceptBtn.disabled = false;
                bulkRejectBtn.disabled = false;
                selectedCount.textContent = count;
                selectedCount.style.display = 'inline';
            } else {
                bulkAcceptBtn.disabled = true;
                bulkRejectBtn.disabled = true;
                selectedCount.style.display = 'none';
            }

            // Update select all checkbox state
            selectAllCheckbox.checked = checkboxes.length > 0 && count === checkboxes.length;
            selectAllCheckbox.indeterminate = count > 0 && count < checkboxes.length;
        }

        // Function to update status badge in the table
        function updateStatusBadge(row, newStatus) {
            const statusBadge = row.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.textContent =
                    `<?php echo $lang['requests']['status-' . strtolower($newStatus)] ?? $newStatus; ?>`;
                statusBadge.className = 'badge status-badge';
                statusBadge.setAttribute('data-i18n', `requests.status-${newStatus.toLowerCase()}`);

                switch (newStatus) {
                    case 'Accepted':
                        statusBadge.classList.add('bg-success');
                        break;
                    case 'Rejected':
                        statusBadge.classList.add('bg-danger');
                        break;
                    case 'Reviewed':
                        statusBadge.classList.add('bg-primary');
                        break;
                    default:
                        statusBadge.classList.add('bg-warning', 'text-dark');
                        break;
                }
            }
        }

        // Function to send AJAX request to update status
        function sendStatusUpdate(ids, status, callback) {
            toggleLoading(true);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'core/api/update-request.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                toggleLoading(false);

                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            showAlert(response.message, 'success');

                            // Update the UI for each affected row
                            response.updated_ids.forEach(id => {
                                const row = document.querySelector(`tr[data-id="${id}"]`);
                                if (row) {
                                    updateStatusBadge(row, response.new_status);
                                    // Uncheck the checkbox
                                    const checkbox = row.querySelector('.studentCheckbox');
                                    if (checkbox) checkbox.checked = false;
                                }
                            });

                            // Update bulk button states
                            updateBulkButtonsState();

                            if (callback) callback(true, response);
                        } else {
                            showAlert(response.message, 'danger');
                            if (callback) callback(false, response);
                        }
                    } catch (e) {
                        showAlert(
                            `<?php echo $lang['requests']['errors']['invalid-response'] ?? 'Invalid response from server'; ?>`,
                            'danger');
                        if (callback) callback(false, null);
                    }
                } else {
                    showAlert(
                        `<?php echo $lang['requests']['errors']['server-error'] ?? 'Server error: '; ?>${xhr.status}`,
                        'danger');
                    if (callback) callback(false, null);
                }
            };

            xhr.onerror = function() {
                toggleLoading(false);
                showAlert(
                    `<?php echo $lang['requests']['errors']['network-error'] ?? 'Network error. Could not connect to server.'; ?>`,
                    'danger');
                if (callback) callback(false, null);
            };

            xhr.send('ids=' + JSON.stringify(ids) + '&status=' + encodeURIComponent(status));
        }

        // Individual Accept/Reject button handler function
        window.handleStatusUpdate = function(id, name, status) {
            let confirmationMessage = '';
            if (status === 'Accepted') {
                confirmationMessage =
                    `<?php echo $lang['requests']['confirm-accept'] ?? 'Are you sure you want to accept the training request for "${name}"?'; ?>`;
            } else if (status === 'Rejected') {
                confirmationMessage =
                    `<?php echo $lang['requests']['confirm-reject'] ?? 'Are you sure you want to reject the training request for "${name}"?'; ?>`;
            }
            if (confirm(confirmationMessage)) {
                sendStatusUpdate([id], status);
            }
        };

        // Search functionality
        searchRequests.addEventListener('input', filterTable);

        // Select All Checkbox functionality
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.studentCheckbox');
            checkboxes.forEach(checkbox => {
                if (checkbox.closest('tr').style.display !== 'none') {
                    checkbox.checked = this.checked;
                }
            });
            updateBulkButtonsState();
        });

        // Individual checkbox change event
        studentsTableBody.addEventListener('change', function(event) {
            if (event.target.classList.contains('studentCheckbox')) {
                updateBulkButtonsState();
            }
        });

        // Bulk Accept button
        bulkAcceptBtn.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.studentCheckbox:checked'))
                .map(cb => parseInt(cb.value))
                .filter(id => !isNaN(id));

            if (selectedIds.length > 0) {
                const message = selectedIds.length === 1 ?
                    '<?php echo $lang['requests']['confirm-bulk-accept-single'] ?? 'Are you sure you want to accept the selected request?'; ?>' :
                    `<?php echo $lang['requests']['confirm-bulk-accept'] ?? 'Are you sure you want to accept ${selectedIds.length} selected requests?'; ?>`;

                if (confirm(message)) {
                    sendStatusUpdate(selectedIds, 'Accepted');
                }
            }
        });

        // Bulk Reject button
        bulkRejectBtn.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.studentCheckbox:checked'))
                .map(cb => parseInt(cb.value))
                .filter(id => !isNaN(id));

            if (selectedIds.length > 0) {
                const message = selectedIds.length === 1 ?
                    '<?php echo $lang['requests']['confirm-bulk-reject-single'] ?? 'Are you sure you want to reject the selected request?'; ?>' :
                    `<?php echo $lang['requests']['confirm-bulk-reject'] ?? 'Are you sure you want to reject ${selectedIds.length} selected requests?'; ?>`;

                if (confirm(message)) {
                    sendStatusUpdate(selectedIds, 'Rejected');
                }
            }
        });

        // Sorting functionality
        document.querySelectorAll('th[data-sort]').forEach(header => {
            header.addEventListener('click', function() {
                const table = this.closest('table');
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr')).filter(row =>
                    !row.querySelector('td[colspan="22"]') // Exclude empty state row
                );
                const column = this.cellIndex;
                const sortType = this.dataset.sort;
                const isAsc = this.classList.contains('asc');

                rows.sort((a, b) => {
                    let aText = a.children[column].textContent.trim();
                    let bText = b.children[column].textContent.trim();

                    if (sortType === 'number') {
                        aText = parseFloat(aText) || 0;
                        bText = parseFloat(bText) || 0;
                    } else if (sortType === 'date') {
                        aText = new Date(aText);
                        bText = new Date(bText);
                    }

                    if (aText < bText) return isAsc ? 1 : -1;
                    if (aText > bText) return isAsc ? -1 : 1;
                    return 0;
                });

                // Clear previous sort indicators
                document.querySelectorAll('th[data-sort]').forEach(th => {
                    th.classList.remove('asc', 'desc');
                });

                // Add new sort indicator
                if (isAsc) {
                    this.classList.add('desc');
                } else {
                    this.classList.add('asc');
                }

                // Re-append sorted rows to tbody
                rows.forEach(row => tbody.appendChild(row));
            });
        });

        // Initialize bulk button states
        updateBulkButtonsState();

        // Initial filter call in case search input has pre-filled value
        filterTable();

        // Close alert when clicked
        alertContainer.addEventListener('click', function() {
            this.style.display = 'none';
        });
    });

    // Ensure Bootstrap modal is properly initialized
    var scheduleModal;
    document.addEventListener('DOMContentLoaded', function() {
        scheduleModal = new bootstrap.Modal(document.getElementById('scheduleInterviewModal'));
    });
</script>

<style>
    /* Custom styles for better UX */
    .requests-table th[data-sort] {
        cursor: pointer;
        user-select: none;
    }

    .requests-table th[data-sort]:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .requests-table th.asc .bi-arrow-down-up::before {
        content: "\f145";
        /* bi-arrow-up */
        color: #28a745;
    }

    .requests-table th.desc .bi-arrow-down-up::before {
        content: "\f149";
        /* bi-arrow-down */
        color: #ffc107;
    }

    .form-check-input:indeterminate {
        background-color: #6c757d;
        border-color: #6c757d;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10h8'/%3e%3c/svg%3e");
    }

    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .alert {
        cursor: pointer;
        transition: opacity 0.3s ease;
    }

    .alert:hover {
        opacity: 0.8;
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    /* Animation for status updates */
    @keyframes statusUpdate {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .status-badge.updating {
        animation: statusUpdate 0.5s ease-in-out;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .table-responsive {
            font-size: 0.875rem;
        }

        .badge {
            font-size: 0.7rem;
        }
    }

    /* Print styles */
    @media print {

        .btn,
        .form-check,
        .alert,
        #loadingOverlay {
            display: none !important;
        }

        .table {
            color: black !important;
            background: white !important;
        }

        .table th,
        .table td {
            border: 1px solid #dee2e6 !important;
            background: white !important;
            color: black !important;
        }
    }
</style>