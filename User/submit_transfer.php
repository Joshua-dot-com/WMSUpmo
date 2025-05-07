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

if (
    isset($_POST['equipment_id']) &&
    isset($_POST['recipient_id']) &&
    isset($_POST['transfer_date']) &&
    isset($_POST['reason'])
) {
    $equipment_id = intval($_POST['equipment_id']);
    $new_owner_id = intval($_POST['recipient_id']);
    $transfer_date = $_POST['transfer_date'];
    $reason = trim($_POST['reason']);

    // Prevent past dates
    $today = date('Y-m-d');
    if ($transfer_date < $today) {
        $response['message'] = 'Transfer date cannot be in the past.';
        echo json_encode($response);
        exit;
    }

    try {
        // STEP 1: Confirm equipment exists
        $stmt = $pdo->prepare("SELECT id FROM equipment WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $equipment_id]);
        $equipmentRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$equipmentRow) {
            $response['message'] = 'Invalid equipment ID.';
            echo json_encode($response);
            exit;
        }

        // STEP 2: Confirm equipment is currently assigned
        $stmt = $pdo->prepare("
            SELECT user_id AS current_owner_id 
            FROM user_equipment 
            WHERE equipment_id = :equipment_id 
              AND returned_at IS NULL
            ORDER BY assigned_at DESC 
            LIMIT 1
        ");
        $stmt->execute(['equipment_id' => $equipment_id]);
        $assignmentRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$assignmentRow) {
            $response['message'] = 'This equipment is not currently assigned or has been returned.';
            echo json_encode($response);
            exit;
        }

        $current_owner_id = $assignmentRow['current_owner_id'];

        // STEP 3: Check for duplicate pending transfer
        $stmt = $pdo->prepare("
            SELECT id FROM equipment_transfers 
            WHERE equipment_id = :equipment_id
              AND current_owner_id = :current_owner_id
              AND new_owner_id = :new_owner_id
              AND transfer_date = :transfer_date
              AND status = 'Pending'
        ");
        $stmt->execute([
            'equipment_id' => $equipment_id,
            'current_owner_id' => $current_owner_id,
            'new_owner_id' => $new_owner_id,
            'transfer_date' => $transfer_date
        ]);

        if ($stmt->rowCount() > 0) {
            $response['message'] = 'This transfer request already exists.';
        } else {
            // STEP 4: Insert new transfer request and update is_pending_transfer
            $pdo->beginTransaction();

            // Insert into equipment_transfers
            $stmt = $pdo->prepare("
                INSERT INTO equipment_transfers 
                    (equipment_id, current_owner_id, new_owner_id, reason, transfer_date, status)
                VALUES 
                    (:equipment_id, :current_owner_id, :new_owner_id, :reason, :transfer_date, 'Pending')
            ");
            $stmt->execute([
                'equipment_id' => $equipment_id,
                'current_owner_id' => $current_owner_id,
                'new_owner_id' => $new_owner_id,
                'reason' => $reason,
                'transfer_date' => $transfer_date
            ]);

            // Update user_equipment to mark as pending transfer
            $updatePending = $pdo->prepare("
                UPDATE user_equipment
                SET is_pending_transfer = 1, reason = :reason
                WHERE equipment_id = :equipment_id 
                  AND user_id = :current_owner_id 
                  AND returned_at IS NULL
            ");
            $updatePending->execute([
                'equipment_id' => $equipment_id,
                'current_owner_id' => $current_owner_id,
                'reason' => $reason
            ]);

            $pdo->commit();
            $response['success'] = true;
            $response['message'] = 'Transfer request submitted and equipment marked as pending.';
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Missing required fields.';
}

echo json_encode($response);
