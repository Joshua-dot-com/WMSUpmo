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

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'] ?? null;
$equipment_id = $data['equipment_id'] ?? null;
$assignment_date = $data['assignment_date'] ?? date('Y-m-d');
$po_jo_no = $data['po_jo_no'] ?? null;
$property_number = $data['property_number'] ?? null;

if (!$user_id || !$equipment_id || !$po_jo_no || !$property_number) {
    echo json_encode(["success" => false, "message" => "Missing required fields (user_id, equipment_id, po_jo_no, or property_number)."]);
    error_log("Missing required fields: " . json_encode($data));
    exit;
}

// ✅ Check if the equipment is already assigned and not returned
$check_stmt = $conn->prepare("
    SELECT ue.*, u.first_name, u.last_name 
    FROM user_equipment ue
    JOIN users u ON ue.user_id = u.id
    WHERE ue.equipment_id = ? AND ue.returned_at IS NULL
");
$check_stmt->bind_param("i", $equipment_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($row = $check_result->fetch_assoc()) {
    // Equipment is already assigned
    $assigned_user = $row['first_name'] . ' ' . $row['last_name'];
    $assigned_date = $row['assigned_at'];

    echo json_encode([
        "success" => false,
        "message" => "This equipment is already assigned to {$assigned_user} on {$assigned_date} and has not been returned yet."
    ]);
    $check_stmt->close();
    $conn->close();
    exit;
}
$check_stmt->close();

// ✅ Proceed with assignment
$stmt = $conn->prepare("
    INSERT INTO user_equipment (user_id, equipment_id, assigned_at, po_jo_no, property_number) 
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("iisss", $user_id, $equipment_id, $assignment_date, $po_jo_no, $property_number);

// Execute user_equipment insert
if ($stmt->execute()) {
    // ✅ If assignment was successful, insert into history
    $insert_history_stmt = $conn->prepare("
        INSERT INTO equipment_history (equipment_id, po_jo_no, action_type, from_user_id, to_user_id, action_date, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $action_type = 'assigned';
    $from_user_id = null; // Setting null properly
    $to_user_id = $user_id;
    $action_date = $assignment_date;
    $notes = 'Initial assignment.';

    // Manually handle NULL from_user_id
    $insert_history_stmt->bind_param(
        "isssiss", 
        $equipment_id, 
        $po_jo_no, 
        $action_type, 
        $from_user_id, // will be NULL
        $to_user_id, 
        $action_date, 
        $notes
    );

    if ($insert_history_stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Equipment assigned and history recorded successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Equipment assigned but failed to record history."]);
    }

    $insert_history_stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Failed to assign equipment."]);
}

$stmt->close();
$conn->close();
?>
