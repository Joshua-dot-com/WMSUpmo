<?php
require 'db_connect.php';

header('Content-Type: application/json');

try {
    // Fetch all users (Pending, Granted, Rejected)
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role, college, created_at, status FROM users");
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $users]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
