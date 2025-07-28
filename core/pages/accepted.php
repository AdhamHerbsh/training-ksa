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
    $sql = "SELECT * FROM trainees WHERE status LIKE 'Accepted' ORDER BY created_at DESC"; // Order by most recent
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
    }
} catch (mysqli_sql_exception $e) {
    // Log the error and show a user-friendly message
    error_log("Database error fetching trainees: " . $e->getMessage());
    $_SESSION['error_message'] = "Could not retrieve training requests. Please try again later.";
}

$conn->close();

?>

<section class="py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="overlay-box p-3 p-sm-4 shadow rounded-4">
                <!-- Header with Search -->
                <div class="row align-items-center mb-4 gy-3">
                    <div class="col-12 col-sm-6">
                        <h2 class="mb-0 text-white" data-i18n="accepted.title">Confirmed Trainees</h2>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary">
                                <i class="bi bi-search text-white"></i>
                            </span>
                            <input type="text" class="form-control bg-dark text-white border-secondary"
                                id="searchTrainees" placeholder="Search..." data-i18n="accepted.search-placeholder"
                                aria-label="Search trainees">
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive" style="max-height: 70vh;">
                    <table class="table table-hover align-middle mb-0 text-white trainee-table">
                        <thead class="sticky-top">
                            <tr class="align-middle">
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="accepted.name">
                                    Name
                                    <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="name"></i>
                                </th>
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="accepted.student-id">
                                    University ID
                                </th>
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="accepted.major">
                                    Major
                                    <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="major"></i>
                                </th>
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="accepted.email">
                                    Email
                                </th>
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="accepted.confirm-date">
                                    Confirmation Date
                                    <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="date"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="confirmedTable">
                            <?php if (!empty($requests)): ?>
                                <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td class="border-bottom border-secondary">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-success rounded-circle p-2 d-flex align-items-center justify-content-center"
                                                    style="width: 35px; height: 35px;">
                                                    <span
                                                        class="text-white fw-bold"><?php echo htmlspecialchars(strtoupper(substr($request['en_name'], 0, 1))); ?></span>
                                                </div>
                                                <span><?php echo htmlspecialchars($request['en_name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="border-bottom border-secondary">
                                            <span
                                                class="badge bg-secondary"><?php echo htmlspecialchars($request['university']); ?></span>
                                        </td>
                                        <td class="border-bottom border-secondary">
                                            <span
                                                class="badge bg-info text-dark"><?php echo htmlspecialchars($request['major']); ?></span>
                                        </td>
                                        <td class="border-bottom border-secondary">
                                            <a href="mailto:amani.2443@gmail.com"
                                                class="text-decoration-none text-white d-flex align-items-center gap-2">
                                                <i class="bi bi-envelope-fill text-muted"></i>
                                                <?php echo htmlspecialchars($request['email']); ?>
                                            </a>
                                        </td>
                                        <td class="border-bottom border-secondary">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-calendar-check text-success"></i>
                                                <small><?php echo htmlspecialchars($request['updated_at']); ?></small>
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
                                                <h5 data-i18n="requests.no-data">No training requests found</h5>
                                                <p class="small" data-i18n="requests.no-data-hint">New requests will
                                                    appear here</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile View Helper -->
                <div class="d-block d-sm-none mt-3">
                    <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-info-circle"></i>
                        <span data-i18n="accepted.scroll-hint">Scroll horizontally to view all data</span>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="d-none text-center py-5">
                    <div class="text-muted">
                        <i class="bi bi-inbox-fill fs-1 mb-3 d-block"></i>
                        <h5 data-i18n="accepted.no-data">No confirmed trainees found</h5>
                        <p class="small" data-i18n="accepted.no-data-hint">Confirmed trainees will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>