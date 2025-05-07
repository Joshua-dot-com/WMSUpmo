<?php
session_start();
header('Content-Type: application/json');

// --- SESSION CHECK ---
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// --- GET JSON INPUT ---
$data = json_decode(file_get_contents('php://input'), true);
$current_password = isset($data['current_password']) ? trim($data['current_password']) : null;
$new_password = isset($data['new_password']) ? trim($data['new_password']) : null;
$confirm_password = isset($data['confirm_password']) ? trim($data['confirm_password']) : null;

// --- Validate Inputs ---
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields']);
    exit;
}

if ($new_password !== $confirm_password) {
    echo json_encode(['status' => 'error', 'message' => 'New password and confirmation do not match']);
    exit;
}

// --- PASSWORD REQUIREMENTS CHECK ---
if (strlen($new_password) < 8) {
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters long']);
    exit;
}

if (!preg_match('/[A-Z]/', $new_password)) {
    echo json_encode(['status' => 'error', 'message' => 'Password must contain at least one uppercase letter']);
    exit;
}

if (!preg_match('/[a-z]/', $new_password)) {
    echo json_encode(['status' => 'error', 'message' => 'Password must contain at least one lowercase letter']);
    exit;
}

if (!preg_match('/\d/', $new_password)) {
    echo json_encode(['status' => 'error', 'message' => 'Password must contain at least one number']);
    exit;
}

if (!preg_match('/[\W_]/', $new_password)) {
    echo json_encode(['status' => 'error', 'message' => 'Password must contain at least one special character']);
    exit;
}

// --- DATABASE CONNECTION ---
$host = 'localhost';
$dbname = 'equipment_database';
$username = 'root';
$password = ''; // replace with your actual DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- FETCH USER DATA ---
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }

    // --- VERIFY CURRENT PASSWORD ---
    if (!password_verify($current_password, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
        exit;
    }

    // --- HASH NEW PASSWORD ---
    $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);

    // --- UPDATE PASSWORD ---
    $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->execute([$hashed_new_password, $user_id]);

    echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
