<?php
require 'db_connect.php';

header('Content-Type: application/json');

try {
    // Prepare query to fetch only pending users
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role, college, created_at FROM users WHERE status = 'Pending'");
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch data as an associative array
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    // Return JSON response
    echo json_encode(['success' => true, 'data' => $users]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
