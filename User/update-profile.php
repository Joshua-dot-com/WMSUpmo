<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

$db = new mysqli($host, $user, $password, $database);
if ($db->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed: ' . $db->connect_error]));
}

// Fetch and sanitize user inputs
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$college = isset($_POST['college']) ? trim($_POST['college']) : '';
$role = isset($_POST['role']) ? trim($_POST['role']) : '';
$admin_role = isset($_POST['admin_role']) ? trim($_POST['admin_role']) : '';
$other_services = isset($_POST['other_services']) ? trim($_POST['other_services']) : '';
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit;
}

// Step 1: Check for duplicates in `first_name`, `last_name`, and `email`
// Ensure no other user has the same `first_name`, `last_name`, and `email`
$checkQuery = "SELECT id FROM users WHERE first_name = ? AND last_name = ? AND email = ? AND id != ?";
$checkStmt = $db->prepare($checkQuery);
$checkStmt->bind_param('sssi', $first_name, $last_name, $email, $user_id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'This name and email already exist.']);
    $checkStmt->close();
    $db->close();
    exit;
}
$checkStmt->close();

// Step 2: Initialize the variable for profile image path
$imagePath = null;

// Handle profile image upload if exists
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $profileImage = $_FILES['profile_image'];
    $imageName = time() . '_' . basename($profileImage['name']);
    $imagePath = 'uploads/profile_images/' . $imageName;

    // Validate image type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($profileImage['type'], $allowedTypes)) {
        $targetDir = "uploads/profile_images/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (!move_uploaded_file($profileImage['tmp_name'], $imagePath)) {
            echo json_encode(['success' => false, 'error' => 'Failed to upload image.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid image type.']);
        exit;
    }
}

// Step 3: Update user details in the database
$query = "UPDATE users 
          SET first_name = ?, last_name = ?, email = ?, college = ?, role = ?, profile_picture = COALESCE(?, profile_picture), 
              admin_role = ?, other_services = ?
          WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('ssssssssi', $first_name, $last_name, $email, $college, $role, $imagePath, $admin_role, $other_services, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'new_profile_image' => $imagePath ?: '']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update profile.']);
}

$stmt->close();
$db->close();
?>
