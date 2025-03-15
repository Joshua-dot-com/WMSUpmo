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

    // Update user status to "Rejected"
    $stmt = $conn->prepare("UPDATE users SET status = 'Rejected' WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Account rejected successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to reject account']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
