<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Get parameters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = ($page - 1) * $limit;

$equipment_type = isset($_GET['equipment_type']) ? $_GET['equipment_type'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$search_term = isset($_GET['search_term']) ? $_GET['search_term'] : '';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'desc';

// Build query
$where_clauses = [];
$params = [];
$types = '';

if (!empty($equipment_type)) {
    $where_clauses[] = "e.category = ?";
    $params[] = $equipment_type;
    $types .= 's';
}

// Define the status condition separately - we'll use it both for filtering and in the SELECT
$status_condition = "
    CASE
        WHEN ue.returned_at IS NOT NULL THEN 'RETURNED'
        WHEN EXISTS (
            SELECT 1 FROM equipment_history eh 
            WHERE eh.equipment_id = e.id 
            AND eh.action_type = 'transfer' 
            AND eh.from_user_id = ue.user_id
        ) THEN 'TRANSFERRED'
        ELSE 'ACTIVE'
    END
";

if (!empty($status)) {
    // Handle different status types - IMPORTANT: This is where we filter by status
    switch(strtoupper($status)) {
        case 'ACTIVE':
            $where_clauses[] = "(ue.returned_at IS NULL AND NOT EXISTS (
                SELECT 1 FROM equipment_history eh 
                WHERE eh.equipment_id = e.id 
                AND eh.action_type = 'transfer' 
                AND eh.from_user_id = ue.user_id
            ))";
            break;
        case 'RETURNED':
            $where_clauses[] = "ue.returned_at IS NOT NULL";
            break;
        case 'TRANSFERRED':
            $where_clauses[] = "ue.returned_at IS NULL AND EXISTS (
                SELECT 1 FROM equipment_history eh 
                WHERE eh.equipment_id = e.id 
                AND eh.action_type = 'transfer' 
                AND eh.from_user_id = ue.user_id
            )";
            break;
        case 'LOST':
            $where_clauses[] = "ue.return_condition = 'lost'";
            break;
        case 'DAMAGED':
            $where_clauses[] = "ue.return_condition = 'damaged'";
            break;
        default:
            // If status is something else, use it directly
            $where_clauses[] = "ue.return_condition = ?";
            $params[] = $status;
            $types .= 's';
    }
}

if (!empty($date_from)) {
    $where_clauses[] = "ue.assigned_at >= ?";
    $params[] = $date_from;
    $types .= 's';
}

if (!empty($search_term)) {
    $where_clauses[] = "(e.equipment_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.college LIKE ?)";
    $search_param = "%$search_term%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ssss';
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Count total records
$count_sql = "
    SELECT COUNT(*) as total
    FROM user_equipment ue
    JOIN users u ON ue.user_id = u.id
    JOIN equipment e ON ue.equipment_id = e.id
    $where_sql
";

$stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_records = $row['total'];

// Get records with pagination
$sql = "
    SELECT 
        ue.id as assignment_id,
        CONCAT(u.first_name, ' ', u.last_name) as user_name,
        u.college as department,
        e.equipment_name as equipment,
        e.category as type,
        ue.assigned_at as assigned_date,
        $status_condition as status
    FROM user_equipment ue
    JOIN users u ON ue.user_id = u.id
    JOIN equipment e ON ue.equipment_id = e.id
    $where_sql
    ORDER BY ue.assigned_at $sort_order
    LIMIT $offset, $limit
";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$records = [];
while ($row = $result->fetch_assoc()) {
    // Format dates for display
    if (!empty($row['assigned_date'])) {
        $date = new DateTime($row['assigned_date']);
        $row['assigned_date'] = $date->format('M d, Y');
    }
    
    // Double-check status to ensure it matches the filter
    // This is a safety check in case our SQL logic has any issues
    if (!empty($status) && strtoupper($status) !== strtoupper($row['status'])) {
        continue; // Skip records that don't match the requested status
    }
    
    $records[] = $row;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'records' => $records,
    'totalRecords' => count($records), // Update total records to match filtered count
    'page' => $page,
    'limit' => $limit,
    'totalPages' => ceil(count($records) / $limit)
]);

$stmt->close();
$conn->close();
?>