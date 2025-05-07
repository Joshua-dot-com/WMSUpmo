<?php
// search_users.php

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

// Get search query and limit if available
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

// Safety check: minimum 1, maximum 100
$limit = max(1, min($limit, 100));

// Prepare the SQL query
if (!empty($search)) {
    // If there is a search query
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, college, profile_picture 
        FROM users 
        WHERE account_state = 'Active' 
          AND is_admin = 0
          AND (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)
        ORDER BY first_name ASC
        LIMIT $limit
    ");
    $stmt->execute([':search' => "%$search%"]);
} else {
    // No search query, fetch all active non-admin users
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, college, profile_picture 
        FROM users 
        WHERE account_state = 'Active' 
          AND is_admin = 0
        ORDER BY first_name ASC
        LIMIT $limit
    ");
    $stmt->execute();
}

// Fetch results
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return as plain array
echo json_encode($users);
?>
