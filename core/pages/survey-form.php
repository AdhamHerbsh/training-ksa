<?php
// Database configuration
require('core/db/connection.php');

$user_id = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

// Check if user is authenticated before processing the form
if (isset($_SESSION['loggedin']) != true) {
    // Redirect to login page or show an error
    header("Location: ?auth=login"); // Assuming a login page
    exit();
}

// Check if user is a Trainee
if ($_SESSION['user_type'] != 'Trainee') {
    header("Location: ?page=home"); // Redirect if not a Supervisor
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
        $error_message = "Please answer all required questions.";
    } else {
        // Prepare and bind SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO intern_survey (user_id, q1, q2, q3, problems, suggestions) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $q1, $q2, $q3, $problems, $suggestions);

        // Execute the statement
        if ($stmt->execute()) {
            $success_message = "Survey submitted successfully!";
            // Optionally redirect to a thank you page
            // header("Location: thank_you.php");
            // exit();
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }
}

$conn->close();
?>



<section class="container align-content-center h-100 w-100 m-auto">
    <div class="form-signin overlay-box py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4">
        <form id="surveyForm" method="POST" action="?page=survey-form">
            <h1 class="mb-3 fw-normal text-white text-center">Intern Satisfaction Survey</h1>

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
                <label for="q1" class="text-white mb-1">Was the website interface clear and easy to use?</label>
                <select class="form-control" id="q1" name="q1" required>
                    <option value="" selected disabled>Select your answer</option>
                    <option value="Excellent">Excellent</option>
                    <option value="Good">Good</option>
                    <option value="Average">Average</option>
                    <option value="Poor">Poor</option>
                </select>
            </div>

            <div class="form-control bg-transparent mb-3">
                <label for="q2" class="text-white mb-1">Were the application steps clear and straightforward?</label>
                <select class="form-control" id="q2" name="q2" required>
                    <option value="" selected disabled>Select your answer</option>
                    <option value="Yes">Yes</option>
                    <option value="Somewhat">Somewhat</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="form-control bg-transparent mb-3">
                <label for="q3" class="text-white mb-1">How was the website performance during application?</label>
                <select class="form-control" id="q3" name="q3" required>
                    <option value="" selected disabled>Select your answer</option>
                    <option value="Very Fast">Very Fast</option>
                    <option value="Average">Average</option>
                    <option value="Slow">Slow</option>
                </select>
            </div>

            <div class="form-control bg-transparent mb-3">
                <label for="problems" class="text-white mb-1">Did you face any issues during the application
                    process?</label>
                <textarea class="form-control" id="problems" name="problems" style="height: 100px"
                    placeholder="Please describe any problems if any..."></textarea>
            </div>

            <div class="form-control bg-transparent mb-3">
                <label for="suggestions" class="text-white mb-1">Do you have any suggestions to improve the
                    website?</label>
                <textarea class="form-control" id="suggestions" name="suggestions" style="height: 100px"
                    placeholder="Write your suggestions here..."></textarea>
            </div>

            <button type="submit" class="w-100 btn btn-lg btn-primary mb-3">Submit Survey</button>
            <a href="?page=home" class="w-100 btn btn-lg btn-outline-secondary mb-3">Back</a>

        </form>
    </div>
</section>