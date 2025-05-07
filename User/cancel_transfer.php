<?php
header('Content-Type: application/json');
session_start();

$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

$response = ['success' => false];

if (isset($_POST['request_id'])) {
    $transfer_id = intval($_POST['request_id']);

    try {
        // Step 1: Check if transfer exists and is pending
        $stmt = $pdo->prepare("SELECT * FROM equipment_transfers WHERE id = :id AND status = 'Pending'");
        $stmt->execute(['id' => $transfer_id]);

        if ($stmt->rowCount() === 0) {
            $response['message'] = 'Transfer request not found or is no longer pending.';
        } else {
            $transfer = $stmt->fetch(PDO::FETCH_ASSOC);
            $equipment_id = $transfer['equipment_id'];
            $current_owner_id = $transfer['current_owner_id'];

            $pdo->beginTransaction();

            // Step 2: Delete the transfer record
            $deleteTransferStmt = $pdo->prepare("DELETE FROM equipment_transfers WHERE id = :id");
            $deleteTransferStmt->execute(['id' => $transfer_id]);

            // Step 3: Mark all current assignments of this equipment as returned
            $returnOthersStmt = $pdo->prepare("
                UPDATE user_equipment 
                SET returned_at = NOW(), is_pending_transfer = 0
                WHERE equipment_id = :equipment_id AND returned_at IS NULL
            ");
            $returnOthersStmt->execute(['equipment_id' => $equipment_id]);

            // Step 4: Reactivate the original owner's assignment
            $reactivateStmt = $pdo->prepare("
                UPDATE user_equipment 
                SET returned_at = NULL, is_pending_transfer = 0
                WHERE equipment_id = :equipment_id AND user_id = :user_id
                ORDER BY assigned_at DESC
                LIMIT 1
            ");
            $reactivateStmt->execute([
                'equipment_id' => $equipment_id,
                'user_id' => $current_owner_id
            ]);

            $pdo->commit();

            $response['success'] = true;
            $response['message'] = 'Transfer cancelled and ownership restored to the original user.';
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Missing transfer ID.';
}

echo json_encode($response);
