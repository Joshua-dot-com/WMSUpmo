<?php
// Database connection
include 'db_connect.php'; // adjust the path as necessary

header('Content-Type: application/json');

// Check if required fields are set
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id'] ?? null;
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $college = $_POST['college'] ?? '';
    $role = $_POST['role'] ?? '';
    $status = $_POST['status'] ?? '';

    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'User ID is required.']);
        exit;
    }

    // Prepare SQL query to update user
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, college = ?, role = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $firstName, $lastName, $email, $college, $role, $status, $userId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
