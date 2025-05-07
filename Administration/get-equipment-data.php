<?php
header('Content-Type: application/json');

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get filters and sort option from query string
$equipmentType = $_GET['equipment_type'] ?? '';
$status = $_GET['status'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$searchTerm = $_GET['search_term'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$sort = $_GET['sort'] ?? 'latest_first';  // Default to 'latest_first' sorting
$limit = 10;
$offset = ($page - 1) * $limit;

$params = [];
$conditions = [];

// Apply filters
if (!empty($equipmentType)) {
    $conditions[] = "e.category = :equipment_type";
    $params[':equipment_type'] = $equipmentType;
}
if (!empty($status)) {
    $conditions[] = "ue.returned_at IS " . ($status === 'Assigned' ? "NULL" : "NOT NULL");
}
if (!empty($dateFrom)) {
    $conditions[] = "ue.assigned_at >= :date_from";
    $params[':date_from'] = $dateFrom;
}
if (!empty($searchTerm)) {
    $conditions[] = "(u.first_name LIKE :term OR u.last_name LIKE :term OR e.equipment_name LIKE :term)";
    $params[':term'] = '%' . $searchTerm . '%';
}

$whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

// Determine the ORDER BY clause based on the sort parameter
$orderBy = 'ue.assigned_at DESC';  // Default to 'latest_first' (descending)
if ($sort === 'oldest_first') {
    $orderBy = 'ue.assigned_at ASC';  // If sorting by oldest first (ascending)
}

// Get total count
$totalStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM user_equipment ue
    JOIN equipment e ON ue.equipment_id = e.id
    JOIN users u ON ue.user_id = u.id
    $whereClause
");
$totalStmt->execute($params);
$totalRecords = $totalStmt->fetchColumn();

// Get paginated and sorted records
$dataStmt = $pdo->prepare("
    SELECT
        ue.id AS assignment_id,
        CONCAT(u.first_name, ' ', u.last_name) AS user_name,
        u.college AS department,  -- Changed from 'u.department' to 'u.college'
        e.equipment_name AS equipment,
        e.category AS type,
        ue.assigned_at AS assigned_date,
        CASE
            WHEN ue.returned_at IS NULL THEN 'Assigned'
            ELSE 'Returned'
        END AS status,
        ue.return_notes AS return_notes  -- Added the return_notes column
    FROM user_equipment ue
    JOIN equipment e ON ue.equipment_id = e.id
    JOIN users u ON ue.user_id = u.id
    $whereClause
    ORDER BY $orderBy
    LIMIT :limit OFFSET :offset
");
$dataStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
foreach ($params as $key => $val) {
    $dataStmt->bindValue($key, $val);
}
$dataStmt->execute();
$records = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

// Return results as JSON
echo json_encode([
    'records' => $records,
    'totalRecords' => (int)$totalRecords
]);
