<?php
// Include your database connection
include 'db_connect.php';

$response = ['success' => false];

// Check if the user is logged in by verifying the session
session_start();
if (isset($_SESSION['user_id'])) {
    // Retrieve the logged-in user's ID from session
    $userId = $_SESSION['user_id'];

    // Example query to fetch user profile data including college, role, profile picture, and status
    $query = "SELECT id, first_name, last_name, email, role, college, profile_picture, created_at, status, is_admin, login_attempts, last_login
              FROM users WHERE id = ?";

    // Prepare and execute the query
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user data is found
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Ensure the profile picture has a default value if not set
            $user['profile_picture'] = $user['profile_picture'] ? $user['profile_picture'] : 'default-profile-image.jpg';  // Fallback image

            // Check if the file exists in the correct directory
            $profilePicturePath = $_SERVER['DOCUMENT_ROOT'] . '/PMO/' . $user['profile_picture'];

            if (file_exists($profilePicturePath)) {
                // Construct the full URL for the profile picture
                $user['profile_picture_url'] = 'http://localhost/PMO/' . $user['profile_picture'];
            } else {
                // If the image does not exist, use a default image
                $user['profile_picture_url'] = 'http://localhost/PMO/default-profile-image.jpg';
            }

            // Return the success response with user data
            $response['success'] = true;
            $response['data'] = $user;
        } else {
            // If no user is found, return an error message
            $response['error'] = 'User not found';
        }

        // Close the statement
        $stmt->close();
    } else {
        // Query preparation failed
        $response['error'] = 'Database query failed';
    }
} else {
    $response['error'] = 'User is not logged in';
}

// Output the response as JSON
echo json_encode($response);
?>
