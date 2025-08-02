<?php
// Database configuration
require('core/db/connection.php');

$user_id = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

// Check if user is authenticated before processing the form
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page or show an error
    header("Location: ?auth=login"); // Assuming a login page
    exit();
}

// Check if user is a Trainee
if ($_SESSION['user_type'] !== 'Trainee') {
    header("Location: ?page=home"); // Redirect if not a Trainee
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $q1 = filter_input(INPUT_POST, 'q1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $q2 = filter_input(INPUT_POST, 'q2', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $q3 = filter_input(INPUT_POST, 'q3', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $problems = filter_input(INPUT_POST, 'problems', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $suggestions = filter_input(INPUT_POST, 'suggestions', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Basic validation for required fields
    if (empty($q1) || empty($q2) || empty($q3)) {
        $error_message = $lang['survey-form']['errors']['required-fields'] ?? "Please answer all required questions.";
    } else {
        // Prepare and bind SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO intern_survey (user_id, q1, q2, q3, problems, suggestions) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $q1, $q2, $q3, $problems, $suggestions);

        // Execute the statement
        if ($stmt->execute()) {
            $success = $lang['survey-form']['success'] ?? "Survey form submitted successfully!";
            // Optional: Clear form fields after successful submission
            header("Location: ?page=survey-form&success=1"); // Redirect to prevent re-submission
            exit();
        } else {
            $error_message = $lang['survey-form']['errors']['database-error'] ?? "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }
}

$conn->close();
?>

<?php if (isset($_GET['success']) && $_GET['success'] == 1) : ?>
<section class="container text-center align-content-center">
    <div class="overlay-box col-12 col-md-6 py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4">
        <h1 class="display-5 text-white" data-i18n="survey-form.success">
            <?php echo $lang['survey-form']['success'] ?? 'Thank you, your form has been submitted successfully.'; ?>
        </h1>
    </div>
</section>
<?php else: ?>

<section class="container align-content-center h-100 w-100 m-auto">
    <div class="form-signin overlay-box py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4">
        <form id="surveyForm" method="POST" action="?page=survey-form">
            <h1 class="mb-3 fw-normal text-white text-center" data-i18n="survey-form.title">
                <?php echo $lang['survey-form']['title'] ?? 'Intern Satisfaction Survey'; ?></h1>

            <?php if (isset($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>

            <div class="form-control bg-transparent mb-3">
                <label for="q1" class="text-white mb-1"
                    data-i18n="survey-form.q1"><?php echo $lang['survey-form']['q1'] ?? 'Was the website interface clear and easy to use?'; ?></label>
                <select class="form-control" id="q1" name="q1" required>
                    <option value="" selected disabled data-i18n="survey-form.select-answer">
                        <?php echo $lang['survey-form']['select-answer'] ?? 'Select your answer'; ?></option>
                    <option value="Excellent" data-i18n="survey-form.excellent">
                        <?php echo $lang['survey-form']['excellent'] ?? 'Excellent'; ?></option>
                    <option value="Good" data-i18n="survey-form.good">
                        <?php echo $lang['survey-form']['good'] ?? 'Good'; ?></option>
                    <option value="Average" data-i18n="survey-form.average">
                        <?php echo $lang['survey-form']['average'] ?? 'Average'; ?></option>
                    <option value="Poor" data-i18n="survey-form.poor">
                        <?php echo $lang['survey-form']['poor'] ?? 'Poor'; ?></option>
                </select>
            </div>

            <div class="form-control bg-transparent mb-3">
                <label for="q2" class="text-white mb-1"
                    data-i18n="survey-form.q2"><?php echo $lang['survey-form']['q2'] ?? 'Were the application steps clear and straightforward?'; ?></label>
                <select class="form-control" id="q2" name="q2" required>
                    <option value="" selected disabled data-i18n="survey-form.select-answer">
                        <?php echo $lang['survey-form']['select-answer'] ?? 'Select your answer'; ?></option>
                    <option value="Yes" data-i18n="survey-form.yes"><?php echo $lang['survey-form']['yes'] ?? 'Yes'; ?>
                    </option>
                    <option value="Somewhat" data-i18n="survey-form.somewhat">
                        <?php echo $lang['survey-form']['somewhat'] ?? 'Somewhat'; ?></option>
                    <option value="No" data-i18n="survey-form.no"><?php echo $lang['survey-form']['no'] ?? 'No'; ?>
                    </option>
                </select>
            </div>

            <div class="form-control bg-transparent mb-3">
                <label for="q3" class="text-white mb-1"
                    data-i18n="survey-form.q3"><?php echo $lang['survey-form']['q3'] ?? 'How was the website performance during application?'; ?></label>
                <select class="form-control" id="q3" name="q3" required>
                    <option value="" selected disabled data-i18n="survey-form.select-answer">
                        <?php echo $lang['survey-form']['select-answer'] ?? 'Select your answer'; ?></option>
                    <option value="Very Fast" data-i18n="survey-form.very-fast">
                        <?php echo $lang['survey-form']['very-fast'] ?? 'Very Fast'; ?></option>
                    <option value="Average" data-i18n="survey-form.average">
                        <?php echo $lang['survey-form']['average'] ?? 'Average'; ?></option>
                    <option value="Slow" data-i18n="survey-form.slow">
                        <?php echo $lang['survey-form']['slow'] ?? 'Slow'; ?></option>
                </select>
            </div>

            <div class="form-control bg-transparent mb-3">
                <label for="problems" class="text-white mb-1"
                    data-i18n="survey-form.problems"><?php echo $lang['survey-form']['problems'] ?? 'Did you face any issues during the application process?'; ?></label>
                <textarea class="form-control" id="problems" name="problems" style="height: 100px"
                    data-i18n="[placeholder]survey-form.problems-placeholder"></textarea>
            </div>

            <div class="form-control bg-transparent mb-3">
                <label for="suggestions" class="text-white mb-1"
                    data-i18n="survey-form.suggestions"><?php echo $lang['survey-form']['suggestions'] ?? 'Do you have any suggestions to improve the website?'; ?></label>
                <textarea class="form-control" id="suggestions" name="suggestions" style="height: 100px"
                    data-i18n="[placeholder]survey-form.suggestions-placeholder"></textarea>
            </div>

            <button type="submit" class="w-100 btn btn-lg btn-primary mb-3"
                data-i18n="survey-form.submit-button"><?php echo $lang['survey-form']['submit-button'] ?? 'Submit Survey'; ?></button>
            <a href="?page=home" class="w-100 btn btn-lg btn-outline-secondary mb-3"
                data-i18n="survey-form.back"><?php echo $lang['survey-form']['back'] ?? 'Back'; ?></a>
        </form>
    </div>
</section>
<?php endif; ?>