<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database"; // Avoid spaces in database names

// Create connection using MySQLi
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]));
}

header('Content-Type: application/json');

try {
    $query = "
        SELECT 
            u.id AS user_id,
            u.first_name,
            u.last_name,
            u.college,
            eq.id AS equipment_id,
            eq.equipment_name,
            eq.property_number,
            ue.assigned_at,
            ue.notes
        FROM users u
        LEFT JOIN user_equipment ue ON u.id = ue.user_id AND ue.returned_at IS NULL
        LEFT JOIN equipment eq ON ue.equipment_id = eq.id
        ORDER BY u.last_name, u.first_name
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $overview = [];

    foreach ($results as $row) {
        $userId = $row['user_id'];
        if (!isset($overview[$userId])) {
            $overview[$userId] = [
                'user_id' => $userId,
                'full_name' => $row['first_name'] . ' ' . $row['last_name'],
                'college' => $row['college'],
                'equipment' => []
            ];
        }

        if ($row['equipment_id']) {
            $overview[$userId]['equipment'][] = [
                'equipment_id' => $row['equipment_id'],
                'equipment_name' => $row['equipment_name'],
                'property_number' => $row['property_number'],
                'assigned_at' => $row['assigned_at'],
                'notes' => $row['notes']
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'data' => array_values($overview)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
