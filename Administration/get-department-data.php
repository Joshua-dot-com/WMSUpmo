<?php
// Database connection (adjust as needed)
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

try {
    // Corrected connection string
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

// Query to get the count of equipment assigned to each department
$query = "
    SELECT u.college, COUNT(ue.equipment_id) AS equipment_count
    FROM users u
    LEFT JOIN user_equipment ue ON u.id = ue.user_id
    LEFT JOIN equipment e ON ue.equipment_id = e.id
    WHERE u.college IS NOT NULL
    GROUP BY u.college
    ORDER BY equipment_count DESC
";

// Prepare and execute the query
$stmt = $pdo->prepare($query);
$stmt->execute();

// Fetch results
$departmentData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for Chart.js (labels and datasets)
$labels = [];
$datasets = [
    'data' => [],
    'backgroundColor' => []
];

// If there is no data, return an empty response
if (empty($departmentData)) {
    echo json_encode([
        'labels' => $labels,
        'datasets' => $datasets
    ]);
    exit;
}

foreach ($departmentData as $row) {
    $labels[] = $row['college'];
    $datasets['data'][] = (int) $row['equipment_count'];
    $datasets['backgroundColor'][] = getRandomColor(); // Random color for each department
}

// Return data as JSON
echo json_encode([
    'labels' => $labels,
    'datasets' => $datasets
]);

// Helper function to generate random colors
function getRandomColor() {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}
?>
