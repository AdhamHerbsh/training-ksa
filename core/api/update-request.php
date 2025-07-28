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

// Check if user is a Supervisor
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Supervisor') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Only supervisors can update requests.'
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
    $ids = isset($_POST['ids']) ? json_decode($_POST['ids'], true) : [];
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    // Validate IDs
    if (empty($ids) || !is_array($ids)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid or missing trainee IDs.'
        ]);
        exit();
    }

    // Validate status
    $allowedStatuses = ['Pending', 'Reviewed', 'Accepted', 'Rejected'];
    if (!in_array($status, $allowedStatuses)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid status. Allowed values: ' . implode(', ', $allowedStatuses)
        ]);
        exit();
    }

    // Sanitize IDs (ensure they are integers)
    $sanitizedIds = [];
    foreach ($ids as $id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id !== false && $id > 0) {
            $sanitizedIds[] = $id;
        }
    }

    if (empty($sanitizedIds)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'No valid trainee IDs provided.'
        ]);
        exit();
    }

    // Start transaction
    $conn->autocommit(false);

    // Create placeholders for prepared statement
    $placeholders = str_repeat('?,', count($sanitizedIds) - 1) . '?';

    // Prepare the SQL statement
    $sql = "UPDATE trainees SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    // Create the types string for bind_param (s for status, i for each id)
    $types = 's' . str_repeat('i', count($sanitizedIds));

    // Create the parameters array
    $params = array_merge([$status], $sanitizedIds);

    // Bind parameters dynamically
    $stmt->bind_param($types, ...$params);

    // Execute the statement
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute statement: " . $stmt->error);
    }

    $affectedRows = $stmt->affected_rows;
    $stmt->close();

    // Commit the transaction
    $conn->commit();
    $conn->autocommit(true);

    // Prepare success response
    $response = [
        'success' => true,
        'message' => "Successfully updated {$affectedRows} trainee request(s) to '{$status}' status.",
        'affected_rows' => $affectedRows,
        'updated_ids' => $sanitizedIds,
        'new_status' => $status
    ];

    // Log the action for audit purposes
    error_log("Supervisor ID {$_SESSION['user_id']} updated trainee requests: " .
        "IDs [" . implode(', ', $sanitizedIds) . "] to status '{$status}'");

    echo json_encode($response);
} catch (mysqli_sql_exception $e) {
    // Rollback transaction on database error
    $conn->rollback();
    $conn->autocommit(true);

    // Log the error
    error_log("Database error in update-request.php: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred while updating requests. Please try again.'
    ]);
} catch (Exception $e) {
    // Rollback transaction on any other error
    if (isset($conn)) {
        $conn->rollback();
        $conn->autocommit(true);
    }

    // Log the error
    error_log("Error in update-request.php: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your request. Please try again.'
    ]);
} finally {
    // Close database connection
    if (isset($conn)) {
        $conn->close();
    }
}
