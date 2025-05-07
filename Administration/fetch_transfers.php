<?php
// fetch_transfers.php

$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database"; // Avoid spaces in database names

// Create PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["success" => false, "error" => "Database connection failed: " . $e->getMessage()]));
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$pageSize = 10; // 10 transfers per page
$offset = ($page - 1) * $pageSize;

// Base query
$sql = "
    SELECT 
        t.id,
        e.equipment_name,
        u1.first_name AS current_owner_first,
        u1.last_name AS current_owner_last,
        u2.first_name AS new_owner_first,
        u2.last_name AS new_owner_last,
        t.reason,
        t.status,
        t.request_date
    FROM transfers t
    INNER JOIN equipment e ON t.equipment_id = e.id
    INNER JOIN users u1 ON t.current_owner_id = u1.id
    INNER JOIN users u2 ON t.new_owner_id = u2.id
    WHERE 1
";

// Add search if needed (search equipment name, or current owner name)
$params = [];

if (!empty($search)) {
    $sql .= " AND (
        e.equipment_name LIKE :search 
        OR u1.first_name LIKE :search 
        OR u1.last_name LIKE :search
        OR u2.first_name LIKE :search 
        OR u2.last_name LIKE :search
    )";
    $params['search'] = "%$search%";
}

$sql .= " ORDER BY t.request_date DESC LIMIT :offset, :limit";

$stmt = $pdo->prepare($sql);

// Bind values properly
foreach ($params as $key => $value) {
    $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
}
$stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', (int) $pageSize, PDO::PARAM_INT);

try {
    $stmt->execute();
    $transfers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the output nicely
    $response = ['transfers' => []];

    foreach ($transfers as $transfer) {
        $response['transfers'][] = [
            'id' => $transfer['id'],
            'equipment_name' => $transfer['equipment_name'],
            'current_owner' => $transfer['current_owner_first'] . ' ' . $transfer['current_owner_last'],
            'new_owner' => $transfer['new_owner_first'] . ' ' . $transfer['new_owner_last'],
            'reason' => $transfer['reason'],
            'status' => $transfer['status'],
            'request_date' => date('Y-m-d', strtotime($transfer['request_date'])),
        ];
    }

    echo json_encode($response);
} catch (PDOException $e) {
    // If there's an error executing the query
    echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
}
?>
