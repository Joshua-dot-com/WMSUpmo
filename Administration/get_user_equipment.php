<?php
// get_user_equipment.php

// Database connection parameters
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database"; // Make sure this is correct

// Create PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

// Get the user ID from the request
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if (!$user_id) {
    echo json_encode(['equipment' => []]);
    exit;
}

// Prepare and execute the query
try {
    $stmt = $pdo->prepare("
        SELECT 
            e.id AS equipment_id,
            e.equipment_name,
            e.description,
            e.status,
            ue.assigned_at,
            ue.po_jo_no,            -- Added po_jo_no
            ue.property_number      -- Added property_number
        FROM user_equipment ue
        INNER JOIN equipment e ON ue.equipment_id = e.id
        WHERE ue.user_id = :user_id
          AND ue.returned_at IS NULL
    ");
    
    $stmt->execute(['user_id' => $user_id]);
    $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['equipment' => $equipment]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database query failed: " . $e->getMessage()]);
}
?>
