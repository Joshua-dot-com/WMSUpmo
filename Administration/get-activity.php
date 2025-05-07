<?php
header('Content-Type: application/json');

// Database credentials
$host = "localhost";
$dbname = "equipment_database";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Optional filters
$equipment_id = isset($_GET['equipment_id']) ? intval($_GET['equipment_id']) : null;
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

// Base SQL query
$sql = "
    SELECT 
        ue.id,
        ue.equipment_id,
        ue.user_id,
        u.first_name,
        u.last_name,
        u.email,
        ue.assigned_at,
        ue.returned_at,
        ue.return_reason,
        ue.return_condition,
        ue.return_notes,
        ue.notes,
        ue.po_jo_no,
        ue.property_number,
        ue.is_pending_transfer,
        ue.updated_at
    FROM user_equipment ue
    JOIN users u ON ue.user_id = u.id
    WHERE 1
";

// Add optional filters
if ($equipment_id) {
    $sql .= " AND ue.equipment_id = $equipment_id";
}
if ($user_id) {
    $sql .= " AND ue.user_id = $user_id";
}

$sql .= " ORDER BY ue.assigned_at DESC";

// Execute query
$result = $conn->query($sql);

$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $conn->error]);
}

$conn->close();
?>
