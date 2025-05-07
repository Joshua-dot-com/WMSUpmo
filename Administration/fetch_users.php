<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database"; // Make sure this is correct

// MySQLi connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]));
}

header('Content-Type: application/json');

// Prepare and execute query
$sql = "
    SELECT 
        id,
        first_name,
        last_name,
        email,
        role,
        college,
        admin_role,
        other_services,
        created_at,
        status,
        account_state,
        is_admin,
        reviewed_by,
        updated_at,
        profile_picture
    FROM users
    WHERE account_state = 'active'
      AND reviewed_by IS NOT NULL
      AND is_admin != 1
";


$result = $conn->query($sql);

if ($result) {
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $users]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
