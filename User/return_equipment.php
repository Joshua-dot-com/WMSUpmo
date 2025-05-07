<?php
// return_equipment.php
header('Content-Type: application/json');
session_start();
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

// Validate session
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "User not authenticated"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$equipmentId = $data['equipment_id'];
$returnReason = $data['reason'];  // Capture the reason sent from the frontend

if (empty($equipmentId) || empty($returnReason)) {
    echo json_encode(["error" => "Invalid equipment ID or missing return reason"]);
    exit;
}

try {
    // Update the user_equipment table with the return date and reason
    $stmt = $pdo->prepare("
        UPDATE user_equipment 
        SET returned_at = NOW(), return_notes = :return_reason 
        WHERE equipment_id = :equipment_id AND user_id = :user_id AND returned_at IS NULL
    ");
    $stmt->execute([
        'equipment_id' => $equipmentId,
        'user_id' => $_SESSION['user_id'],
        'return_reason' => $returnReason
    ]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
