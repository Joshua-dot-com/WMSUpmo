<?php
// Enable strict error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require 'db_connect.php'; // Include the database connection file

header('Content-Type: application/json');

try {
    // Check if the database connection was successful
    if (!$conn) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    // Current month stats query
    $result = $conn->query("SELECT COUNT(*) AS count FROM users");
    if (!$result) {
        throw new Exception('Error executing query for total users: ' . $conn->error);
    }
    $row = $result->fetch_assoc();
    $total_users = $row['count'];

    $result = $conn->query("SELECT COUNT(*) AS count FROM users WHERE status = 'Granted'");
    if (!$result) {
        throw new Exception('Error executing query for granted users: ' . $conn->error);
    }
    $row = $result->fetch_assoc();
    $granted = $row['count'];

    $result = $conn->query("SELECT COUNT(*) AS count FROM users WHERE status = 'Pending'");
    if (!$result) {
        throw new Exception('Error executing query for pending users: ' . $conn->error);
    }
    $row = $result->fetch_assoc();
    $pending = $row['count'];

    $result = $conn->query("SELECT COUNT(*) AS count FROM users WHERE status = 'Rejected'");
    if (!$result) {
        throw new Exception('Error executing query for rejected users: ' . $conn->error);
    }
    $row = $result->fetch_assoc();
    $rejected = $row['count'];

    // Previous month stats query (for percentage calculation)
    $lastMonthResult = $conn->query("SELECT COUNT(*) AS count FROM users WHERE MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)");
    $lastMonthRow = $lastMonthResult->fetch_assoc();
    $lastMonthTotalUsers = $lastMonthRow['count'];

    $lastMonthGrantedResult = $conn->query("SELECT COUNT(*) AS count FROM users WHERE status = 'Granted' AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)");
    $lastMonthGrantedRow = $lastMonthGrantedResult->fetch_assoc();
    $lastMonthGranted = $lastMonthGrantedRow['count'];

    $lastMonthPendingResult = $conn->query("SELECT COUNT(*) AS count FROM users WHERE status = 'Pending' AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)");
    $lastMonthPendingRow = $lastMonthPendingResult->fetch_assoc();
    $lastMonthPending = $lastMonthPendingRow['count'];

    $lastMonthRejectedResult = $conn->query("SELECT COUNT(*) AS count FROM users WHERE status = 'Rejected' AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)");
    $lastMonthRejectedRow = $lastMonthRejectedResult->fetch_assoc();
    $lastMonthRejected = $lastMonthRejectedRow['count'];

    // Send back the current and last month's data as a JSON response
    echo json_encode([
        'current' => [
            'total_users' => $total_users,
            'granted' => $granted,
            'pending' => $pending,
            'rejected' => $rejected,
        ],
        'last_month' => [
            'total_users' => $lastMonthTotalUsers,
            'granted' => $lastMonthGranted,
            'pending' => $lastMonthPending,
            'rejected' => $lastMonthRejected,
        ]
    ]);

} catch (Exception $e) {
    // Return a 500 Internal Server Error with an error message in JSON format
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
