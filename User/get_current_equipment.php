<?php
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

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "User not authenticated"]);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            ue.id AS user_equipment_id,
            ue.user_id,
            ue.equipment_id,
            e.po_jo_no,
            e.property_number,
            e.category,
            e.equipment_name,
            e.status AS equipment_status,
            ue.assigned_at,
            ue.returned_at,
            ue.notes
        FROM 
            user_equipment ue
        JOIN 
            equipment e ON ue.equipment_id = e.id
        WHERE 
            ue.user_id = :user_id
            AND ue.returned_at IS NULL
            AND ue.is_pending_transfer = 0
            AND e.is_pending_transfer = 0  -- Ensure that equipment is not pending transfer as well
        ORDER BY 
            ue.assigned_at DESC
    ");
    $stmt->execute(['user_id' => $userId]);
    $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'equipment' => $equipment
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
