<?php
require 'db_connect.php'; // Include database connection

header('Content-Type: application/json');

// Ensure the request is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST["firstName"] ?? "");
    $lastName = trim($_POST["lastName"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    // If any of the fields are empty, return an error message
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        echo json_encode(["success" => false, "error" => "All fields are required."]);
        exit;
    }

    // Hash the password before saving to database
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Predetermined role and is_admin value
    $role = 'admin'; // Always set the role to 'admin'
    $isAdmin = 1;    // is_admin is always 1 for Admins
    $isOwner = 0;    // Default owner value (can be adjusted if needed)

    // Insert user data into the database with the predefined role and is_admin value
    $stmt = $conn->prepare("INSERT INTO admin (email, password, is_admin, is_owner, first_name, last_name, role, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssiiiss", $email, $hashedPassword, $isAdmin, $isOwner, $firstName, $lastName, $role);

    // Execute the query and return success or error response
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Admin user added successfully"]);
    } else {
        echo json_encode(["success" => false, "error" => "Database error: " . $stmt->error]);
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method."]);
}
?>
