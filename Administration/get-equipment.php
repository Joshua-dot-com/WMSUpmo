<?php
header("Content-Type: application/json");

// Database credentials
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

// Create database connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Prepare and execute query
$sql = "SELECT id, property_number, equipment_name AS name, category, status FROM equipment";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $equipment = [];

    while ($row = $result->fetch_assoc()) {
        $equipment[] = $row;
    }

    echo json_encode($equipment);
} else {
    echo json_encode([]); // Return empty array if no records
}

// Close connection
$conn->close();
?>
