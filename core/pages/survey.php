<?php

// Database configuration
require('core/db/connection.php');

// Check if user is authenticated before processing the form
if (isset($_SESSION['loggedin']) != true) {
    // Redirect to login page or show an error
    header("Location: ?auth=login"); // Assuming a login page
    exit();
}

// Check if user is a Supervisor
if ($_SESSION['user_type'] != 'Supervisor') {
    header("Location: ?page=home"); // Redirect if not a Supervisor
    exit();
}


// Fetch data from the intern_satisfaction_survey table
$sql = "SELECT q1, q2, q3, problems, suggestions, created_date FROM intern_survey ORDER BY created_date DESC";
$result = $conn->query($sql);

$survey_data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $survey_data[] = $row;
    }
}

$conn->close();
?>


<section class="py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="overlay-box p-3 p-sm-4 shadow rounded-4">
                <div class="row align-items-center mb-4 gy-3">
                    <div class="col-12 col-sm">
                        <h1 class="h2 mb-0 text-white" data-i18n="survey.title">Trainee Satisfaction Survey</h1>
                    </div>
                    <div class="col-12 col-sm-auto">
                        <button onclick="window.print()" class="btn btn-light d-inline-flex align-items-center gap-2"
                            id="print-btn">
                            <i class="bi bi-printer"></i>
                            <span data-i18n="survey.print">Print</span>
                        </button>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 70vh;">
                    <table class="table table-hover align-middle mb-0 text-white">
                        <thead class="sticky-top">
                            <tr class="align-middle">
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="survey.interface">Website Interface</th>
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="survey.steps">Application Steps</th>
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="survey.speed">Speed</th>
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="survey.problems">Problems</th>
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="survey.suggestions">Suggestions</th>
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="survey.time">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($survey_data)): ?>
                            <tr>
                                <td colspan="6" class="text-center border-bottom border-secondary"
                                    data-i18n="survey.no-data-available">No survey data
                                    available.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($survey_data as $row): ?>
                            <tr>
                                <td class="border-bottom border-secondary">
                                    <div class="d-flex align-items-center gap-2">
                                        <span><?php echo htmlspecialchars($row['q1']); ?></span>
                                    </div>
                                </td>
                                <td class="border-bottom border-secondary"><?php echo htmlspecialchars($row['q2']); ?>
                                </td>
                                <td class="border-bottom border-secondary">
                                    <?php
                                            $badge_class = '';
                                            switch ($row['q3']) {
                                                case 'Very Fast':
                                                    $badge_class = 'bg-success';
                                                    break;
                                                case 'Average':
                                                    $badge_class = 'bg-warning text-dark';
                                                    break;
                                                case 'Slow':
                                                    $badge_class = 'bg-danger';
                                                    break;
                                                default:
                                                    $badge_class = 'bg-secondary';
                                                    break;
                                            }
                                            ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo htmlspecialchars($row['q3']); ?>
                                    </span>
                                </td>
                                <td class="border-bottom border-secondary">
                                    <?php echo !empty($row['problems']) ? '<span class="text-danger">' . htmlspecialchars($row['problems']) . '</span>' : '-'; ?>
                                </td>
                                <td class="border-bottom border-secondary">
                                    <?php echo !empty($row['suggestions']) ? htmlspecialchars($row['suggestions']) : 'No suggestions'; ?>
                                </td>
                                <td class="border-bottom border-secondary">
                                    <small><?php echo date('Y-m-d h:i a', strtotime($row['created_date'])); ?></small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-block d-sm-none mt-3">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        <span data-i18n="survey.scroll-hint">Scroll horizontally to view more data</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>