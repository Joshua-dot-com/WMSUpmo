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

    // Fetch all unique equipment categories
    $stmt = $conn->prepare("SELECT DISTINCT category FROM equipment WHERE category IS NOT NULL AND category != ''");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN); // fetch only the category values as a simple array

    echo json_encode($categories);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
