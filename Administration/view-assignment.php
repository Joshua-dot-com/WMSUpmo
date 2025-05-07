<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed.");
}

if (!isset($_GET['id'])) {
    echo "<p class='text-red-500'>Invalid assignment ID.</p>";
    exit;
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare("
    SELECT ue.*, 
           CONCAT(u.first_name, ' ', u.last_name) AS user_name, 
           u.college AS department,  -- Changed 'department' to 'college'
           e.equipment_name, 
           e.category, 
           e.property_number, 
           ue.return_notes  -- Added the return_notes column
    FROM user_equipment ue
    JOIN users u ON ue.user_id = u.id
    JOIN equipment e ON ue.equipment_id = e.id
    WHERE ue.id = ?
");
$stmt->execute([$id]);
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assignment) {
    echo "<p class='text-red-500'>Assignment not found.</p>";
    exit;
}

echo "<div class='space-y-2'>
        <p><strong>User:</strong> {$assignment['user_name']}</p>
        <p><strong>Department:</strong> {$assignment['department']}</p>
        <p><strong>Equipment:</strong> {$assignment['equipment_name']} ({$assignment['property_number']})</p>
        <p><strong>Category:</strong> {$assignment['category']}</p>
        <p><strong>Assigned At:</strong> {$assignment['assigned_at']}</p>
        <p><strong>Returned At:</strong> " . ($assignment['returned_at'] ?? '<em>Not yet returned</em>') . "</p>
        <p><strong>Return Notes:</strong> " . ($assignment['return_notes'] ?? '—') . "</p>  <!-- Added Return Notes field -->
      </div>";
?>
