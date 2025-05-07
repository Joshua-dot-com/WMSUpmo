<?php
header("Content-Type: application/json");

// Database connection (update as needed)
$host = 'localhost';
$dbname = 'equipment_database';
$username = 'root';
$password = ''; // replace with your actual DB password

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

// Handle search query
$query = isset($_POST['query']) ? trim($_POST['query']) : "";

// Prepare SQL with optional filtering
$sql = "SELECT id, user_id, first_name, last_name, email, college, reason, requested_at FROM deletion_requests WHERE processed = 0";
$params = [];
$types = "";

if (!empty($query)) {
    $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR college LIKE ?)";
    $likeQuery = "%" . $query . "%";
    $params = [$likeQuery, $likeQuery, $likeQuery, $likeQuery];
    $types = "ssss";
}

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$requests = [];

while ($row = $result->fetch_assoc()) {
    $requests[] = [
        "id" => $row["id"],
        "name" => $row["first_name"] . " " . $row["last_name"],
        "email" => $row["email"] ?: "—", // Fallback if email is NULL
        "college" => $row["college"] ?: "—", // Fallback if college is NULL
        "reason" => $row["reason"], // Currently not shown in table, used for modal
        "request_date" => date("M d, Y h:i A", strtotime($row["requested_at"])),
    ];
}

echo json_encode($requests);

$stmt->close();
$conn->close();
?>