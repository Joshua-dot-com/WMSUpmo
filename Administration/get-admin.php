<?php
// get-admin.php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'first_name'      => $_SESSION['first_name'],
        'last_name'       => $_SESSION['last_name'],
        'profile_picture' => $_SESSION['profile_picture'] ?? null
    ]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
}
