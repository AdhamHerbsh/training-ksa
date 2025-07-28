<?php
session_start();

// Set content type to JSON
header('Content-Type: application/json');

// Database configuration
require('../../core/db/connection.php');

// --- Security and Authentication Checks ---
// Check if user is authenticated
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Please login first.'
    ]);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Only POST requests are accepted.'
    ]);
    exit();
}

try {
    // Get and validate input data
    $trainee_id = isset($_POST['trainee_id']) ? (int)$_POST['trainee_id'] : 0;
    $response = isset($_POST['response']) ? trim($_POST['response']) : '';
    $user_id = $_SESSION['user_id'];

    // Validate trainee ID
    if ($trainee_id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid trainee ID provided.'
        ]);
        exit();
    }

    // Validate response
    $allowedResponses = ['Accepted', 'Rejected'];
    if (!in_array($response, $allowedResponses)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid response. Allowed values: ' . implode(', ', $allowedResponses)
        ]);
        exit();
    }

    // Start transaction
    $conn->autocommit(false);

    // First, verify that the trainee record belongs to the logged-in user and is in 'Accepted' status
    $checkSql = "SELECT id, status, trainee_response, en_name FROM trainees WHERE id = ? AND user_id = ?";
    $checkStmt = $conn->prepare($checkSql);

    if (!$checkStmt) {
        throw new Exception("Failed to prepare check statement: " . $conn->error);
    }

    $checkStmt->bind_param("ii", $trainee_id, $user_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        $checkStmt->close();
        $conn->rollback();
        $conn->autocommit(true);

        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Training application not found or you do not have permission to access it.'
        ]);
        exit();
    }

    $traineeData = $checkResult->fetch_assoc();
    $checkStmt->close();

    // Check if the application is in 'Accepted' status
    if ($traineeData['status'] !== 'Accepted') {
        $conn->rollback();
        $conn->autocommit(true);

        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'You can only respond to accepted training applications.'
        ]);
        exit();
    }

    // Check if trainee has already responded
    if (!empty($traineeData['trainee_response'])) {
        $conn->rollback();
        $conn->autocommit(true);

        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'You have already responded to this training application.'
        ]);
        exit();
    }

    // Update the trainee response
    $updateSql = "UPDATE trainees SET trainee_response = ?, response_date = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?";
    $updateStmt = $conn->prepare($updateSql);

    if (!$updateStmt) {
        throw new Exception("Failed to prepare update statement: " . $conn->error);
    }

    $updateStmt->bind_param("sii", $response, $trainee_id, $user_id);

    if (!$updateStmt->execute()) {
        throw new Exception("Failed to execute update statement: " . $updateStmt->error);
    }

    $affectedRows = $updateStmt->affected_rows;
    $updateStmt->close();

    if ($affectedRows === 0) {
        throw new Exception("No rows were updated. The record may have been modified by another process.");
    }

    // Commit the transaction
    $conn->commit();
    $conn->autocommit(true);

    // Prepare success response
    $responseMessage = $response === 'Accepted' ?
        "Thank you! You have successfully accepted the training opportunity." :
        "Your response has been recorded. You have declined the training opportunity.";

    $response_data = [
        'success' => true,
        'message' => $responseMessage,
        'trainee_id' => $trainee_id,
        'response' => $response,
        'trainee_name' => $traineeData['en_name'],
        'response_date' => date('Y-m-d H:i:s')
    ];

    // Log the action for audit purposes
    error_log("Trainee response recorded: User ID {$user_id}, Trainee ID {$trainee_id}, Response: {$response}");

    // Send email notification to supervisor (optional - you can implement this)
    // sendSupervisorNotification($traineeData, $response);

    echo json_encode($response_data);
} catch (mysqli_sql_exception $e) {
    // Rollback transaction on database error
    if (isset($conn)) {
        $conn->rollback();
        $conn->autocommit(true);
    }

    // Log the error
    error_log("Database error in trainee-response.php: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred while processing your response. Please try again.'
    ]);
} catch (Exception $e) {
    // Rollback transaction on any other error
    if (isset($conn)) {
        $conn->rollback();
        $conn->autocommit(true);
    }

    // Log the error
    error_log("Error in trainee-response.php: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your response. Please try again.'
    ]);
} finally {
    // Close database connection
    if (isset($conn)) {
        $conn->close();
    }
}

// Optional function to send email notification to supervisor
function sendSupervisorNotification($traineeData, $response)
{
    // Implementation depends on your email system
    // This is a placeholder for email notification functionality

    /*
    $to = $traineeData['supervisor_email'];
    $subject = "Trainee Response: " . $traineeData['en_name'];
    $message = "The trainee {$traineeData['en_name']} has {$response} the training opportunity.";
    $headers = "From: noreply@yourcompany.com";
    
    mail($to, $subject, $message, $headers);
    */
}
