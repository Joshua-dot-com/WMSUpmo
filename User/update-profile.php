<?php
// Assuming you have session and database connection established
session_start();
include 'db_connect.php';  // Make sure to include the database connection file

// Fetch user inputs and sanitize them
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$college = isset($_POST['college']) ? trim($_POST['college']) : '';
$role = isset($_POST['role']) ? trim($_POST['role']) : ''; // Default to empty if not provided
$admin_role = isset($_POST['admin_role']) ? trim($_POST['admin_role']) : ''; // Default to empty if not provided
$other_services = isset($_POST['other_services']) ? trim($_POST['other_services']) : ''; // Default to empty if not provided

// Initialize the variable for profile image path
$imagePath = null;

// Handle profile image upload if exists
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $profileImage = $_FILES['profile_image'];
    $imageName = time() . '_' . basename($profileImage['name']);
    $imagePath = 'uploads/profile_images/' . $imageName;

    // Validate image type (optional)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($profileImage['type'], $allowedTypes)) {
        // Check if the upload directory exists, create if not
        $targetDir = "uploads/profile_images/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);  // Create the directory if it doesn't exist
        }

        if (move_uploaded_file($profileImage['tmp_name'], $imagePath)) {
            // Image successfully uploaded
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to upload image']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid image type']);
        exit;
    }
} else {
    // If no image uploaded, do not change the image path
    $imagePath = null;
}

// Update user details in the database, including the profile picture if it exists
$query = "UPDATE users 
          SET first_name = ?, last_name = ?, email = ?, college = ?, role = ?, profile_picture = COALESCE(?, profile_picture), 
              admin_role = ?, other_services = ?
          WHERE id = ?";
$stmt = $db->prepare($query);

// Bind parameters. If no image uploaded, use NULL for imagePath
$stmt->bind_param('sssssssssi', $first_name, $last_name, $email, $college, $role, $imagePath, $admin_role, $other_services, $_SESSION['user_id']);

// Execute the query
if ($stmt->execute()) {
    // Respond with success
    echo json_encode(['success' => true, 'new_profile_image' => $imagePath ?: '']);
} else {
    // Respond with error if the query fails
    echo json_encode(['success' => false, 'error' => 'Failed to update profile']);
}

// Close the statement
$stmt->close();
?>
