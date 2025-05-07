<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

// Connect to database
$conn = new mysqli($host, $user, $password, $database);
header('Content-Type: application/json');

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Get posted data
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
$assignment_id = $data['assignment_id'] ?? null;
if (!$assignment_id) {
    echo json_encode(["success" => false, "message" => "Missing assignment_id."]);
    exit;
}

// Prepare return operation
$return_date = date('Y-m-d H:i:s'); // current timestamp

$stmt = $conn->prepare("
    UPDATE user_equipment
    SET returned_at = ?
    WHERE id = ? AND returned_at IS NULL
");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("si", $return_date, $assignment_id);
$stmt->execute();

// Check if any record was updated
if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Equipment marked as returned."]);
} else {
    echo json_encode(["success" => false, "message" => "No active assignment found or already returned."]);
}

$stmt->close();
$conn->close();
?>
