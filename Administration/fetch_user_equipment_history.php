<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

// Connect to database
$conn = new mysqli($host, $user, $password, $database);
header('Content-Type: application/json');

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Get optional user_id filter
$user_id = null;
if (isset($_GET['user_id']) && $_GET['user_id'] !== '') {
    $user_id = intval($_GET['user_id']);
}

// Base SQL
$sql = "
    SELECT 
        ue.id AS assignment_id,
        ue.assigned_at,
        ue.returned_at,
        ue.notes,

        u.id AS user_id,
        u.first_name,
        u.last_name,
        u.email,
        u.college,

        e.id AS equipment_id,
        e.equipment_name,
        e.property_number,
        e.category,
        e.status AS equipment_status

    FROM user_equipment ue
    JOIN users u ON ue.user_id = u.id
    JOIN equipment e ON ue.equipment_id = e.id
";

// Add WHERE clause if filtering by user
if ($user_id !== null) {
    $sql .= " WHERE ue.user_id = ?";
}

$sql .= " ORDER BY ue.returned_at DESC, ue.assigned_at DESC";

// Prepare and execute
$stmt = $conn->prepare($sql);
if ($user_id !== null) {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

echo json_encode(["success" => true, "data" => $history]);

$stmt->close();
$conn->close();
?>
