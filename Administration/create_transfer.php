<?php
// create_transfer.php

// Database connection parameters
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$currentOwnerId = $data['current_owner_id'] ?? null;
$newOwnerId = $data['new_owner_id'] ?? null;
$reason = trim($data['reason'] ?? '');
$equipmentIds = $data['equipment_ids'] ?? [];

if (isset($data['equipment_id']) && !empty($data['equipment_id'])) {
    $equipmentIds = [$data['equipment_id']];
}

$errors = [];
if (empty($currentOwnerId)) $errors[] = "Current owner ID is required";
if (empty($newOwnerId)) $errors[] = "New owner ID is required";
if (empty($equipmentIds)) $errors[] = "At least one equipment item must be selected";
if (empty($reason) || strlen($reason) < 3) $errors[] = "Transfer reason must be at least 3 characters";

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Validation failed',
        'messages' => $errors
    ]);
    exit;
}

try {
    $successCount = 0;
    $failedItems = [];

    foreach ($equipmentIds as $equipmentId) {
        // Fetch equipment details
        $fetchStmt = $pdo->prepare("
            SELECT po_jo_no
            FROM equipment
            WHERE id = :equipment_id
        ");
        $fetchStmt->execute(['equipment_id' => $equipmentId]);
        $equipment = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        if ($equipment) {
            $poJoNo = $equipment['po_jo_no'];

            // Update the user_equipment ownership and set is_pending_transfer = 1
            $updateStmt = $pdo->prepare("
                UPDATE user_equipment
                SET user_id = :new_owner_id,
                    is_pending_transfer = 1
                WHERE equipment_id = :equipment_id
                  AND user_id = :current_owner_id
                  AND returned_at IS NULL
            ");
            $updateStmt->execute([
                'new_owner_id' => $newOwnerId,
                'equipment_id' => $equipmentId,
                'current_owner_id' => $currentOwnerId
            ]);

            if ($updateStmt->rowCount() > 0) {
                // Insert into equipment_history
                $historyStmt = $pdo->prepare("
                    INSERT INTO equipment_history 
                    (equipment_id, po_jo_no, from_user_id, to_user_id, action_type, notes)
                    VALUES
                    (:equipment_id, :po_jo_no, :from_user_id, :to_user_id, 'transfer', :notes)
                ");
                $historyStmt->execute([
                    'equipment_id' => $equipmentId,
                    'po_jo_no' => $poJoNo,
                    'from_user_id' => $currentOwnerId,
                    'to_user_id' => $newOwnerId,
                    'notes' => "Transferred: " . $reason
                ]);

                $successCount++;
            } else {
                $failedItems[] = $equipmentId;
            }
        } else {
            $failedItems[] = $equipmentId;
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Transfer completed successfully',
        'success_count' => $successCount,
        'failed_items' => $failedItems
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
