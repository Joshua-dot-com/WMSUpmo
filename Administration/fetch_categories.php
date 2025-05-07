<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database"; // Avoid spaces in database names

// Create PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["success" => false, "error" => "Database connection failed: " . $e->getMessage()]));
}

header('Content-Type: application/json');

try {
    // Query to fetch distinct categories
    $query = "SELECT DISTINCT category FROM equipment WHERE category IS NOT NULL AND category != '' ORDER BY category";
    
    $stmt = $pdo->prepare($query); // Use $pdo here, not $conn
    $stmt->execute();
    
    $categories = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categories[] = $row['category'];
    }
    
    // Return the categories as JSON
    echo json_encode([
        'success' => true,
        'data' => $categories,
        'count' => count($categories)
    ]);
    
} catch (PDOException $e) {
    // Log the error (in a production environment)
    error_log('Database error in fetch_categories.php: ' . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred',
        'error' => $e->getMessage()
    ]);
}
?>
