<?php
header('Content-Type: application/json');
session_start();

$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

$transferId = isset($_GET['transfer_id']) ? $_GET['transfer_id'] : null;

if (empty($transferId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Transfer ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT rejection_reason FROM equipment_transfers WHERE id = :transfer_id");
    $stmt->execute(['transfer_id' => $transferId]);
    $transfer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($transfer) {
        echo json_encode([
            'success' => true,
            'reason' => $transfer['rejection_reason']
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Transfer not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
