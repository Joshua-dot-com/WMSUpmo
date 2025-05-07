<?php
// get_recent_assignments.php

// Database connection parameters
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

// Create PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

// Get limit parameter
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

// Query to get recent assignments
$query = "
    SELECT 
        e.property_number AS equipment_id,
        e.equipment_name AS equipment_type,
        CONCAT(u.first_name, ' ', u.last_name) AS assigned_to,
        ue.assigned_at AS assignment_date,
        CASE WHEN ue.returned_at IS NULL THEN 'Active' ELSE 'Returned' END AS status
    FROM 
        user_equipment ue
    JOIN 
        equipment e ON ue.equipment_id = e.id
    JOIN 
        users u ON ue.user_id = u.id
    ORDER BY 
        ue.assigned_at DESC
    LIMIT :limit
";

try {
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'assignments' => $assignments
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch assignments: ' . $e->getMessage()]);
}
?>