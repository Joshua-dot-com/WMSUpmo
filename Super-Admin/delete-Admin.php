<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "equipment_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the POST data
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'User ID not received.']);
    exit;
}

// Delete by ID instead of email
$stmt = $conn->prepare("DELETE FROM admin WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error deleting admin']);
}

$stmt->close();
$conn->close();
?>
