<?php
header("Content-Type: application/json");

// Database connection (adjust credentials as needed)
$host = 'localhost';
$dbname = 'equipment_database';
$username = 'root';
$password = ''; // replace with your actual DB password

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

// Validate and sanitize input
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id <= 0) {
    echo json_encode(["success" => false, "error" => "Invalid ID"]);
    exit;
}

// Query the deletion_requests table
$stmt = $conn->prepare("SELECT first_name, last_name, email, college, reason, requested_at FROM deletion_requests WHERE id = ? AND processed = 0");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "error" => "Request not found or already processed"]);
} else {
    $row = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "name" => $row["first_name"] . " " . $row["last_name"],
        "email" => $row["email"] ?: "—",
        "college" => $row["college"] ?: "—",
        "reason" => $row["reason"] ?: "No reason provided",
        "requested_at" => date("M d, Y h:i A", strtotime($row["requested_at"]))
    ]);
}

$stmt->close();
$conn->close();
?>