<?php
require 'db_connect.php';
header('Content-Type: application/json');

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['filter']) ? trim($_GET['filter']) : ''; // Can be 'Active' or 'Inactive'

try {
    // Base query with account_state included
    $query = "SELECT id, first_name, last_name, email, college, role, status, account_state, last_login, created_at, admin_role 
              FROM users";

    $conditions = [];
    $params = [];
    $types = '';

    // Apply search query
    if (!empty($searchQuery)) {
        $conditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR college LIKE ? OR role LIKE ?)";
        $searchParam = "%$searchQuery%";
        $params = array_merge($params, array_fill(0, 5, $searchParam));
        $types .= str_repeat('s', 5);
    }

    // Apply status filter using account_state
    if ($statusFilter === 'Active' || $statusFilter === 'Inactive') {
        $conditions[] = "account_state = ?";
        $params[] = $statusFilter;
        $types .= 's';
    }

    // Combine conditions
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($query);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $users
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
