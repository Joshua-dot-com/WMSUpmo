<?php
require 'db_connect.php';

header('Content-Type: application/json');

try {
    // Get the request data
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    $id = $data['id'];

    // Fetch user role
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    $user = $result->fetch_assoc();
    $role = $user['role'];

    // Set is_admin based on role
    $is_admin = ($role === "Administrative") ? 1 : 0;

    // Update user status and is_admin
    $stmt = $conn->prepare("UPDATE users SET status = 'Granted', is_admin = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_admin, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Account approved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to approve account']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
