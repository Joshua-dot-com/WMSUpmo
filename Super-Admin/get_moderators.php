<?php
require 'db_connect.php'; // Include database connection

header('Content-Type: application/json');

try {
    $response = [
        'success' => true,
        'admins' => [],
        'moderators' => []
    ];

    // Prepare and execute query for both roles using one statement to avoid code duplication
    $roles = ['admin', 'moderator'];
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role FROM admin WHERE role = ?");

    foreach ($roles as $role) {
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            if ($role === 'admin') {
                $response['admins'][] = $row;
            } elseif ($role === 'moderator') {
                $response['moderators'][] = $row;
            }
        }
    }

    // Send the response back as JSON
    echo json_encode($response);

    // Close the statement and connection after response
    $stmt->close();
    $conn->close();
} catch (mysqli_sql_exception $e) {
    // Handle errors and output a response
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
