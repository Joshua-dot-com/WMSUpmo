<?php
// get_equipment_stats.php
header('Content-Type: application/json');
session_start(); // assuming you store user_id in session after login
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

// You must ensure the user is logged in and user_id is available
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "User not authenticated"]);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Query for current assigned items (returned_at IS NULL)
    $stmt = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN returned_at IS NULL THEN 1 ELSE 0 END) AS currently_assigned,
            COUNT(*) AS total_assignments,
            SUM(CASE WHEN returned_at IS NOT NULL THEN 1 ELSE 0 END) AS returned_items
        FROM user_equipment
        WHERE user_id = :user_id
    ");
    $stmt->execute(['user_id' => $userId]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'stats' => [
            'currently_assigned' => (int)$stats['currently_assigned'],
            'total_assignments' => (int)$stats['total_assignments'],
            'returned_items' => (int)$stats['returned_items']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
