<?php
require 'db_connect.php'; // Include database connection
header('Content-Type: application/json');

// Check if 'id' is provided in the request
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Prepare SQL query to fetch user data by ID
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role FROM admin WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Fetch the user data
            $user = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'user' => $user
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'User not found.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Database error: ' . $stmt->error
        ]);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid user ID.'
    ]);
}
?>
