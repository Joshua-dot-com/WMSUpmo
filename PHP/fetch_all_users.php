<?php
require 'db_connect.php';
header('Content-Type: application/json');

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['filter']) ? trim($_GET['filter']) : '';

try {
    // Base query
    $query = "SELECT id, first_name, last_name, email, college, role, status, last_login, created_at, admin_role 
              FROM overview_users";

    $conditions = [];
    $params = [];
    $types = '';

    // Apply search query if present
    if (!empty($searchQuery)) {
        $conditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR college LIKE ? OR role LIKE ?)";
        $searchParam = "%$searchQuery%";
        // Repeat search param for each field
        $params = array_merge($params, array_fill(0, 5, $searchParam));
        $types .= str_repeat('s', 5);
    }

    // Apply status filter if it's exactly 'Active' or 'Inactive'
    if ($statusFilter === 'Active' || $statusFilter === 'Inactive') {
        $conditions[] = "status = ?";
        $params[] = $statusFilter;
        $types .= 's';
    }

    // Append conditions if any
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($query);

    // Bind parameters dynamically if needed
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
