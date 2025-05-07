<?php
// Set headers for JSON response
header('Content-Type: application/json');

// Database connection parameters
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

// Create database connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Get user ID from request
$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($userId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid user ID'
    ]);
    exit;
}

// Fetch user details
$userQuery = "SELECT 
                id, 
                first_name, 
                last_name, 
                email, 
                role, 
                college, 
                admin_role, 
                created_at, 
                status, 
                profile_picture, 
                last_login 
              FROM users 
              WHERE id = ?";

$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'User not found'
    ]);
    exit;
}

// Get user data
$userData = $userResult->fetch_assoc();

// Determine department based on role
$department = '';
if ($userData['role'] === 'User') {
    $department = $userData['college'];
} else if ($userData['role'] === 'Administrative Officials') {
    $department = $userData['admin_role'];
}

// Format user data
$user = [
    'id' => $userData['id'],
    'first_name' => $userData['first_name'],
    'last_name' => $userData['last_name'],
    'email' => $userData['email'],
    'role' => $userData['role'],
    'department' => $department,
    'college' => $userData['college'],
    'admin_role' => $userData['admin_role'],
    'created_at' => $userData['created_at'],
    'status' => $userData['status'],
    'profile_picture' => $userData['profile_picture'],
    'last_login' => $userData['last_login'],
    'position' => $userData['role'], // Using role as position since there's no specific position field
    'phone' => 'N/A', // Phone field not in provided schema
    'assigned_equipment' => []
];

// Fetch assigned equipment for this user
$equipmentQuery = "SELECT 
                    ue.id AS assignment_id,
                    ue.equipment_id,
                    ue.property_number,
                    ue.po_jo_no,
                    ue.assigned_at,
                    ue.returned_at,
                    ue.notes,
                    e.equipment_name,
                    e.category,
                    e.status,
                    e.description
                  FROM user_equipment ue
                  JOIN equipment e ON ue.equipment_id = e.id
                  WHERE ue.user_id = ? AND ue.returned_at IS NULL";

$stmt = $conn->prepare($equipmentQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$equipmentResult = $stmt->get_result();

// Add assigned equipment to user data
while ($equipment = $equipmentResult->fetch_assoc()) {
    $user['assigned_equipment'][] = [
        'assignment_id' => $equipment['assignment_id'],
        'equipment_id' => $equipment['equipment_id'],
        'equipment_name' => $equipment['equipment_name'],
        'category' => $equipment['category'],
        'property_number' => $equipment['property_number'],
        'po_jo_no' => $equipment['po_jo_no'],
        'assigned_at' => $equipment['assigned_at'],
        'status' => $equipment['status'],
        'description' => $equipment['description'],
        'notes' => $equipment['notes']
    ];
}

// Return user data with assigned equipment
echo json_encode([
    'success' => true,
    'data' => $user
]);

// Close connection
$stmt->close();
$conn->close();
?>
