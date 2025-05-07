<?php
require 'db_connect.php'; // Include database connection
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get the values from the form
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';

    // Check for empty fields
    if (!$firstName || !$lastName || !$email || !$role || !$id) {
        echo json_encode(["success" => false, "error" => "All fields are required."]);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "error" => "Invalid email format."]);
        exit;
    }

    // Prepare the update SQL query
    $stmt = $conn->prepare("UPDATE admin SET first_name = ?, last_name = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $firstName, $lastName, $email, $role, $id);

    // Execute and check if the update was successful
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User updated successfully."]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to update user: " . $stmt->error]);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method."]);
}
?>
