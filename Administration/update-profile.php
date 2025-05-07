<?php
// Assuming you have session and database connection established
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

// Fetch user inputs and sanitize them
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$college = isset($_POST['college']) ? trim($_POST['college']) : '';

// Initialize variables for other fields (optional, since you’re not updating them in this case)
$role = isset($_POST['role']) ? trim($_POST['role']) : ''; 
$admin_role = isset($_POST['admin_role']) ? trim($_POST['admin_role']) : ''; 
$other_services = isset($_POST['other_services']) ? trim($_POST['other_services']) : ''; 
$imagePath = null;  // We will not update profile image in this example

// Update user details in the database, including only the sent fields
$query = "UPDATE users 
          SET first_name = ?, last_name = ?, email = ?, college = ?
          WHERE id = ?";
$stmt = $conn->prepare($query);  // Use $conn instead of $db

// Bind parameters. Adjust the bind type string to match the parameters sent (4 strings + 1 integer)
$stmt->bind_param('ssssi', $first_name, $last_name, $email, $college, $_SESSION['user_id']);

// Execute the query
if ($stmt->execute()) {
    // Respond with success
    echo json_encode(['success' => true]);
} else {
    // Respond with error if the query fails
    echo json_encode(['success' => false, 'error' => 'Failed to update profile']);
}

// Close the statement
$stmt->close();

// Close the connection
$conn->close();
?>
