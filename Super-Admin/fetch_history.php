<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Get filter parameters from the request
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Adjust number of records per page
$offset = ($page - 1) * $limit;

try {
    // Start building the base query
    $query = "SELECT id, first_name, last_name, email, role AS department, created_at AS request_date, created_at AS decision_date, status, reviewed_by 
              FROM users WHERE 1";

    // Apply status filter
    if ($status !== 'all') {
        $query .= " AND status = ?";
    }

    // Apply search filter
    if (!empty($search)) {
        $query .= " AND (CONCAT(first_name, ' ', last_name) LIKE ? OR email LIKE ? OR role LIKE ?)";
    }

    // Apply date range filter
    if (!empty($startDate) && !empty($endDate)) {
        $query .= " AND created_at BETWEEN ? AND ?";
    }

    // Limit and offset for pagination
    $query .= " LIMIT ? OFFSET ?";

    // Prepare the query
    $stmt = $conn->prepare($query);

    // Bind parameters dynamically
    $paramTypes = '';
    $params = [];

    if ($status !== 'all') {
        $paramTypes .= 's';
        $params[] = $status;
    }
    
    if (!empty($search)) {
        $paramTypes .= 'ssss';
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if (!empty($startDate) && !empty($endDate)) {
        $paramTypes .= 'ss';
        $params[] = $startDate;
        $params[] = $endDate;
    }

    // Always bind limit and offset for pagination
    $paramTypes .= 'ii';
    $params[] = $limit;
    $params[] = $offset;

    // Bind the parameters
    $stmt->bind_param($paramTypes, ...$params);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare the data for response
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['name'] = $row['first_name'] . ' ' . $row['last_name'];
        $data[] = $row;
    }

    // Total count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM users WHERE 1";
    if ($status !== 'all') {
        $countQuery .= " AND status = ?";
    }
    if (!empty($search)) {
        $countQuery .= " AND (CONCAT(first_name, ' ', last_name) LIKE ? OR email LIKE ? OR role LIKE ?)";
    }
    if (!empty($startDate) && !empty($endDate)) {
        $countQuery .= " AND created_at BETWEEN ? AND ?";
    }

    $countStmt = $conn->prepare($countQuery);
    $countParams = [];

    if ($status !== 'all') {
        $countParams[] = $status;
    }

    if (!empty($search)) {
        $countParams[] = "%$search%";
        $countParams[] = "%$search%";
        $countParams[] = "%$search%";
    }

    if (!empty($startDate) && !empty($endDate)) {
        $countParams[] = $startDate;
        $countParams[] = $endDate;
    }

    // Bind parameters for total count
    if (!empty($countParams)) {
        $countStmt->bind_param(str_repeat('s', count($countParams)), ...$countParams);
    }

    // Execute the total count query
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $countRow = $countResult->fetch_assoc();
    $totalRecords = $countRow['total'];
    $totalPages = ceil($totalRecords / $limit);

    // Return the data with pagination information
    echo json_encode([
        'success' => true,
        'data' => $data,
        'totalPages' => $totalPages,
        'totalRecords' => $totalRecords,
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
