<?php
header('Content-Type: application/json');

// Database config
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

// Validate equipment ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing equipment ID']);
    exit;
}

$equipmentId = intval($_GET['id']);

// Connect using PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Fetch equipment details
try {
    $stmt = $pdo->prepare("
        SELECT 
            ue.id AS equipment_assignment_id,
            ue.user_id,
            ue.equipment_id,
            ue.assigned_at,
            ue.returned_at,
            ue.return_reason,
            ue.return_condition,
            ue.return_notes,
            ue.notes,
            ue.po_jo_no,
            ue.property_number,
            ue.is_pending_transfer,
            ue.updated_at,
            u.first_name,
            u.last_name,
            u.email AS user_email,
            u.profile_picture
        FROM user_equipment ue
        LEFT JOIN users u ON ue.user_id = u.id
        WHERE ue.id = :equipment_id
        LIMIT 1
    ");
    $stmt->execute([
        'equipment_id' => $equipmentId
    ]);
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $row['name'] = $row['first_name'] . ' ' . $row['last_name'];
        $row['status'] = $row['returned_at'] ? 'Returned' : 'Assigned';
        $row['profile_picture'] = $row['profile_picture'] ?: 'uploads/profile_images/default-profile-image.png';
        echo json_encode($row);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'No equipment found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query error: ' . $e->getMessage()]);
}
?>
