<?php
// get_equipment_history.php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "Database connection failed", 
        "message" => $e->getMessage()
    ]);
    exit;
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? min(50, max(1, (int)$_GET['limit'])) : 10;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$actionType = isset($_GET['actionType']) ? trim($_GET['actionType']) : 'all';
$dateRange = isset($_GET['dateRange']) ? trim($_GET['dateRange']) : 'all';
$equipmentType = isset($_GET['equipmentType']) ? trim($_GET['equipmentType']) : 'all';

$offset = ($page - 1) * $limit;

$query = "
    SELECT 
        h.id,
        h.po_jo_no,
        e.equipment_name,
        h.action_type,
        h.action_date,
        h.notes,
        CONCAT(u1.first_name, ' ', u1.last_name) as from_user,
        CONCAT(u2.first_name, ' ', u2.last_name) as to_user
    FROM 
        equipment_history h
    LEFT JOIN 
        equipment e ON h.equipment_id = e.id
    LEFT JOIN 
        users u1 ON h.from_user_id = u1.id
    LEFT JOIN 
        users u2 ON h.to_user_id = u2.id
    WHERE h.po_jo_no IS NOT NULL AND h.po_jo_no != ''
";

$countQuery = "
    SELECT COUNT(*) 
    FROM equipment_history h
    LEFT JOIN equipment e ON h.equipment_id = e.id
    LEFT JOIN users u1 ON h.from_user_id = u1.id
    LEFT JOIN users u2 ON h.to_user_id = u2.id
    WHERE h.po_jo_no IS NOT NULL AND h.po_jo_no != ''
";

$params = [];

// Search condition
if (!empty($search)) {
    $searchCondition = "
        AND (
            e.equipment_name LIKE :search OR
            e.property_number LIKE :search OR
            CONCAT(u1.first_name, ' ', u1.last_name) LIKE :search OR
            CONCAT(u2.first_name, ' ', u2.last_name) LIKE :search OR
            h.notes LIKE :search
        )
    ";
    $query .= $searchCondition;
    $countQuery .= $searchCondition;
    $params[':search'] = "%$search%";
}

if ($actionType !== 'all') {
    $query .= " AND h.action_type = :actionType";
    $countQuery .= " AND h.action_type = :actionType";
    $params[':actionType'] = $actionType;
}

if ($dateRange !== 'all') {
    switch ($dateRange) {
        case 'today':
            $query .= " AND DATE(h.action_date) = CURDATE()";
            $countQuery .= " AND DATE(h.action_date) = CURDATE()";
            break;
        case 'yesterday':
            $query .= " AND DATE(h.action_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            $countQuery .= " AND DATE(h.action_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $query .= " AND h.action_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            $countQuery .= " AND h.action_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $query .= " AND h.action_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            $countQuery .= " AND h.action_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
    }
}

if ($equipmentType !== 'all') {
    $query .= " AND e.equipment_type = :equipmentType";
    $countQuery .= " AND e.equipment_type = :equipmentType";
    $params[':equipmentType'] = $equipmentType;
}

$query .= " ORDER BY h.action_date DESC LIMIT $offset, $limit";

try {
    $countStmt = $pdo->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalRecords = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);

    $stmt = $pdo->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'history' => $history,
        'pagination' => [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'limit' => $limit
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database query failed',
        'message' => $e->getMessage()
    ]);
}
?>
