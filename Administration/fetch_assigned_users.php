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

// Get equipment_id from query string
$equipment_id = isset($_GET['equipment_id']) ? (int)$_GET['equipment_id'] : 0;

if ($equipment_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid equipment ID"]);
    $conn->close();
    exit;
}

// Query for assigned users
$sql = "
    SELECT u.first_name, u.last_name, u.email
    FROM user_equipment ue
    JOIN users u ON ue.user_id = u.id
    WHERE ue.equipment_id = ? AND ue.returned_at IS NULL
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $equipment_id);
$stmt->execute();
$result = $stmt->get_result();

$assignments = [];
while ($row = $result->fetch_assoc()) {
    $assignments[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode(["success" => true, "data" => $assignments]);
?>
