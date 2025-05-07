<?php
// Include the database connection file
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database"; // Avoid spaces in database names

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]));
}

session_start();

// Check if the image is uploaded and there is no error
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    // Get the uploaded file
    $profileImage = $_FILES['profile_image'];
    $imageName = time() . '_' . basename($profileImage['name']);

    // Set the path for the image in the uploads directory (absolute path)
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/PMO/uploads/profile_images/"; // Absolute path
    $imagePath = "uploads/profile_images/" . $imageName; // Store in relative path for the database

    // Validate image type (optional)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($profileImage['type'], $allowedTypes)) {
        // Check if the upload directory exists, create if not
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);  // Create the directory with proper permissions if it doesn't exist
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($profileImage['tmp_name'], $targetDir . $imageName)) {
            // Get user ID from session
            $userId = $_SESSION['user_id'];

            // Update the profile_picture in the database with the relative path to the image
            $query = "UPDATE users SET profile_picture = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $imagePath, $userId);

            if ($stmt->execute()) {
                // Respond with the new image path to update the frontend
                echo json_encode(['success' => true, 'new_profile_image' => $imagePath]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update profile picture in the database']);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid image type']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No image uploaded or an error occurred']);
}
?>
