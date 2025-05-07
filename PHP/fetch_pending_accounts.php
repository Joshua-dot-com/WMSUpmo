<?php
require 'db_connect.php';

header('Content-Type: application/json');

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';  // Default to latest to oldest

try {
    // Base query
    $query = "SELECT id, first_name, last_name, email, role, college, created_at, status FROM users WHERE status NOT IN ('Granted', 'Rejected')";

    // Add search condition if there's a search query
    if (!empty($search)) {
        $query .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
    }

    // Add sorting based on the selected option
    if ($sort === 'latest') {
        $query .= " ORDER BY created_at DESC";
    } else {
        $query .= " ORDER BY created_at ASC";
    }

    // Prepare and execute query
    $stmt = $conn->prepare($query);

    // Bind search parameters if necessary
    if (!empty($search)) {
        $searchTerm = "%$search%";
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    }

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
