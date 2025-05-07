<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

$conn = new mysqli($host, $user, $password, $database);
header('Content-Type: application/json');

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Modified query to join with user_equipment table to check assignment status
$query = "
    SELECT e.*, 
           CASE WHEN ue.id IS NOT NULL THEN 'Assigned' ELSE 'Available' END as assignment_status,
           CASE WHEN ue.id IS NOT NULL THEN ue.user_id ELSE NULL END as assigned_to_user_id,
           CONCAT(u.first_name, ' ', u.last_name) as assigned_to_user_name
    FROM equipment e
    LEFT JOIN user_equipment ue ON e.id = ue.equipment_id AND ue.returned_at IS NULL
    LEFT JOIN users u ON ue.user_id = u.id
    WHERE e.status = 'Active'
    ORDER BY e.id DESC
";

$result = $conn->query($query);

if (!$result) {
    echo json_encode(["success" => false, "message" => "Query failed: " . $conn->error]);
    exit;
}

$equipment = [];
while ($row = $result->fetch_assoc()) {
    $equipment[] = $row;
}

echo json_encode(["success" => true, "data" => $equipment]);

$conn->close();
?>
