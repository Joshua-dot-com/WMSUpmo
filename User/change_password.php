<?php
session_start();
header('Content-Type: application/json');

// --- SESSION CHECK ---
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// --- GET JSON INPUT ---
$raw_input = file_get_contents('php://input');
$data = json_decode($raw_input, true);

// --- Validate JSON input ---
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input format']);
    exit;
}

$current_password = trim($data['current_password'] ?? '');
$new_password = trim($data['new_password'] ?? '');
$confirm_password = trim($data['confirm_password'] ?? '');

// --- Validate Inputs ---
if ($current_password === '' || $new_password === '' || $confirm_password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
    exit;
}

if ($new_password !== $confirm_password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'New password and confirmation do not match']);
    exit;
}

// --- Password Policy ---
if (
    strlen($new_password) < 8 ||
    !preg_match('/[A-Z]/', $new_password) ||
    !preg_match('/[a-z]/', $new_password) ||
    !preg_match('/\d/', $new_password) ||
    !preg_match('/[\W_]/', $new_password)
) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters and include uppercase, lowercase, number, and special character']);
    exit;
}

// --- DB Connection ---
$host = 'localhost';
$dbname = 'equipment_database';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- Fetch User Password ---
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // --- Verify Current Password ---
    if (!password_verify($current_password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit;
    }

    // --- Update Password ---
    $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);
    $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->execute([$hashed_new_password, $user_id]);

    if ($update->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Password is the same as before or nothing changed']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error, please try again later']);
    // error_log($e->getMessage());
}
