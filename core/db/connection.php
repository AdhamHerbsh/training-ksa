<?php
// Database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "training";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
if (!$conn->set_charset("utf8mb4")) {
    die("Error loading character set utf8mb4: " . $conn->error);
}

// Helper function to create prepared statements
function prepare_statement($sql)
{
    global $conn;
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    return $stmt;
}

// Helper function to execute prepared statements with parameters
function execute_statement($stmt, $types, ...$params)
{
    if ($params) {
        if (!$stmt->bind_param($types, ...$params)) {
            die("Error binding parameters: " . $stmt->error);
        }
    }

    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }

    return $stmt;
}
