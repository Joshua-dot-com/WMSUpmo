<?php
include 'db_connect.php'; // Adjust if needed

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);

    // Select only necessary fields to avoid exposing sensitive data
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, college, admin_role, role, status FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Return all required fields including admin_role
        echo json_encode(['success' => true, 'data' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
