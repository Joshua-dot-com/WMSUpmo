<?php
session_start();
header('Content-Type: application/json');

// --- SESSION CHECK ---
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if college is set in session
if (!isset($_SESSION['college'])) {
    echo json_encode(['status' => 'error', 'message' => 'College not found in session']);
    exit;
}

$college = $_SESSION['college'];

// Get the JSON payload from the request body
$inputData = json_decode(file_get_contents('php://input'), true);
$reason = isset($inputData['reason']) ? trim($inputData['reason']) : null;

// --- DATABASE CONNECTION ---
$host = 'localhost';
$dbname = 'equipment_database';
$username = 'root';
$password = ''; // replace with your actual DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check for existing unprocessed request
    $check = $pdo->prepare("SELECT * FROM deletion_requests WHERE user_id = ? AND processed = 0");
    $check->execute([$user_id]);
    if ($check->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'You have already submitted a deletion request.']);
        exit;
    }

    // Retrieve first_name, last_name, and email from users table
    $userQuery = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
    $userQuery->execute([$user_id]);
    $user = $userQuery->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        exit;
    }

    $first_name = $user['first_name'];
    $last_name = $user['last_name'];
    $email = $user['email'];

    // Insert new deletion request with first_name, last_name, email, college, and reason
    $insert = $pdo->prepare("INSERT INTO deletion_requests (user_id, first_name, last_name, email, college, reason) VALUES (?, ?, ?, ?, ?, ?)");
    $insert->execute([$user_id, $first_name, $last_name, $email, $college, $reason]);

    // Flag user as pending deletion
    $update = $pdo->prepare("UPDATE users SET is_pending_deletion = 1 WHERE id = ?");
    $update->execute([$user_id]);

    echo json_encode(['status' => 'success', 'message' => 'Account deletion request submitted successfully.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>