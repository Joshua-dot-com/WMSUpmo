<?php
// get-equipment-stats.php

$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Total equipment (count from equipment table)
$total_equipment_query = "SELECT COUNT(*) AS total FROM equipment";
$total_equipment_result = $conn->query($total_equipment_query);
$total_equipment = $total_equipment_result->fetch_assoc()['total'];

// Currently assigned equipment (latest assignment without returned_at)
$assigned_query = "
    SELECT COUNT(DISTINCT equipment_id) AS assigned
    FROM user_equipment
    WHERE returned_at IS NULL
";
$assigned_result = $conn->query($assigned_query);
$current_assigned = $assigned_result->fetch_assoc()['assigned'];

// Transfers in last 30 days (returned + reassigned)
$transfer_query = "
    SELECT COUNT(*) AS transfers
    FROM user_equipment
    WHERE assigned_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
";
$transfer_result = $conn->query($transfer_query);
$transfers = $transfer_result->fetch_assoc()['transfers'];

echo json_encode([
    'total_equipment' => $total_equipment,
    'currently_assigned' => $current_assigned,
    'transfers_30_days' => $transfers
]);
$conn->close();
?>
