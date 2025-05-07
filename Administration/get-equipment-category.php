<?php
header('Content-Type: application/json');

// Database credentials
$host = "localhost";
$dbname = "equipment_database";
$username = "root";
$password = "";

try {
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch selected equipment fields
    $stmt = $conn->prepare("SELECT 
        id,
        property_number,
        equipment_name AS name,
        category,
        status
    FROM equipment");

    $stmt->execute();
    $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON
    echo json_encode($equipment);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
