<?php
header('Content-Type: application/json');

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Get filter parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Items per page
$itemsPerPage = 10;
$offset = ($page - 1) * $itemsPerPage;

// Build the base query
$baseQuery = "FROM equipment e
              LEFT JOIN user_equipment ue ON e.id = ue.equipment_id AND ue.returned_at IS NULL
              LEFT JOIN users u ON ue.user_id = u.id
              WHERE 1=1";

// Add search filter
$params = [];
if (!empty($search)) {
    $searchTerm = "%$search%";
    $baseQuery .= " AND (e.equipment_name LIKE ? OR e.po_jo_no LIKE ? OR e.property_number LIKE ? OR e.description LIKE ?)";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// Add category filter
if (!empty($category)) {
    $baseQuery .= " AND e.category = ?";
    $params[] = $category;
}

// Add status filter
if (!empty($status)) {
    $baseQuery .= " AND e.status = ?";
    $params[] = $status;
}

// Count total items for pagination
$countQuery = "SELECT COUNT(DISTINCT e.id) as total " . $baseQuery;
$countStmt = $conn->prepare($countQuery);

// Bind parameters for count query
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $countStmt->bind_param($types, ...$params);
}

$countStmt->execute();
$countResult = $countStmt->get_result();
$totalItems = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

// Main query with pagination
$mainQuery = "SELECT 
                e.id,
                e.po_jo_no, 
                e.equipment_name, 
                e.category, 
                e.property_number,
                e.status,
                (SELECT COUNT(*) 
                 FROM user_equipment ue2 
                 WHERE ue2.equipment_id = e.id AND ue2.returned_at IS NULL) AS current_assignments,
                GROUP_CONCAT(CONCAT(u.first_name, ' ', u.last_name) SEPARATOR ', ') AS assigned_users
              " . $baseQuery . "
              GROUP BY e.id
              ORDER BY e.id DESC
              LIMIT ? OFFSET ?";

$mainStmt = $conn->prepare($mainQuery);

// Add pagination parameters
$params[] = $itemsPerPage;
$params[] = $offset;

// Bind parameters for main query
if (!empty($params)) {
    $types = str_repeat('s', count($params) - 2) . 'ii'; // string params + 2 integers for LIMIT/OFFSET
    $mainStmt->bind_param($types, ...$params);
}

$mainStmt->execute();
$result = $mainStmt->get_result();

// Prepare the data array
$equipment = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $equipment[] = $row;
    }
} else {
    echo json_encode(["success" => false, "message" => "Failed to fetch equipment data"]);
    $conn->close();
    exit;
}

$conn->close();

// Return JSON response with pagination info
echo json_encode([
    'success' => true,
    'data' => $equipment,
    'pagination' => [
        'total' => $totalItems,
        'per_page' => $itemsPerPage,
        'current_page' => $page,
        'total_pages' => $totalPages,
        'showing' => count($equipment)
    ]
]);
?>
