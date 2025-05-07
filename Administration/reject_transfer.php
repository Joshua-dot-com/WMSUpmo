<?php
// reject_transfer.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

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

$data = json_decode(file_get_contents('php://input'), true);
$transferId = isset($data['transfer_id']) ? $data['transfer_id'] : null;
$reason = isset($data['reason']) ? trim($data['reason']) : '';

if (empty($transferId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Transfer ID is required']);
    exit;
}

if (empty($reason)) {
    http_response_code(400);
    echo json_encode(['error' => 'Rejection reason is required']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Fetch transfer and equipment details
    $getTransferStmt = $pdo->prepare("
        SELECT et.*, e.property_number, e.po_jo_no 
        FROM equipment_transfers et
        JOIN equipment e ON et.equipment_id = e.id
        WHERE et.id = :transfer_id AND et.status = 'pending'
    ");
    $getTransferStmt->execute(['transfer_id' => $transferId]);
    $transfer = $getTransferStmt->fetch(PDO::FETCH_ASSOC);

    if (!$transfer) {
        throw new Exception('Transfer not found or already processed');
    }

    // Get user info
    $userStmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, role 
        FROM overview_users 
        WHERE id IN (:current_id, :new_id)
    ");
    $userStmt->execute([
        'current_id' => $transfer['current_owner_id'],
        'new_id'     => $transfer['new_owner_id']
    ]);
    $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

    $currentOwner = $newOwner = null;
    foreach ($users as $u) {
        if ($u['id'] == $transfer['current_owner_id']) {
            $currentOwner = $u;
        } elseif ($u['id'] == $transfer['new_owner_id']) {
            $newOwner = $u;
        }
    }

    $currentName = $currentOwner 
        ? "{$currentOwner['first_name']} {$currentOwner['last_name']} ({$currentOwner['role']})" 
        : "User ID {$transfer['current_owner_id']}";

    $newName = $newOwner 
        ? "{$newOwner['first_name']} {$newOwner['last_name']} ({$newOwner['role']})" 
        : "User ID {$transfer['new_owner_id']}";

    // Update transfer status to rejected
    $updateStmt = $pdo->prepare("
        UPDATE equipment_transfers 
        SET status = 'rejected', 
            rejected_at = NOW(),
            rejection_reason = :reason
        WHERE id = :transfer_id AND status = 'pending'
    ");
    $updateStmt->execute([
        'transfer_id' => $transferId,
        'reason' => $reason
    ]);

    // Set is_pending_transfer = 0 in both equipment and user_equipment tables
    $updateEquipmentStmt = $pdo->prepare("
        UPDATE equipment 
        SET is_pending_transfer = 0 
        WHERE id = :equipment_id
    ");
    $updateEquipmentStmt->execute([
        'equipment_id' => $transfer['equipment_id']
    ]);

    // Update user_equipment table to reflect the change in pending transfer status
    $updateUserEquipmentStmt = $pdo->prepare("
        UPDATE user_equipment 
        SET is_pending_transfer = 0 
        WHERE equipment_id = :equipment_id
    ");
    $updateUserEquipmentStmt->execute([
        'equipment_id' => $transfer['equipment_id']
    ]);

    // Insert into equipment_history
    $historyStmt = $pdo->prepare("
        INSERT INTO equipment_history 
        (equipment_id, po_jo_no, action_type, from_user_id, to_user_id, notes) 
        VALUES 
        (:equipment_id, :po_jo_no, 'reject', :from_user_id, :to_user_id, :notes)
    ");
    $historyStmt->execute([
        'equipment_id' => $transfer['equipment_id'],
        'po_jo_no' => $transfer['po_jo_no'],
        'from_user_id' => $transfer['current_owner_id'],
        'to_user_id' => $transfer['new_owner_id'],
        'notes' => 'Transfer rejected: ' . $reason
    ]);

    // Log to activity_log
    $logMessage = "Transfer rejected: Equipment [{$transfer['property_number']}] from {$currentName} to {$newName}. Reason: {$reason}";
    $logStmt = $pdo->prepare("
        INSERT INTO activity_log (message, timestamp) 
        VALUES (:message, NOW())
    ");
    $logStmt->execute(['message' => $logMessage]);

    // Send email to the requester (newOwner)
    if ($newOwner && filter_var($newOwner['email'], FILTER_VALIDATE_EMAIL)) {
        $mail = new PHPMailer(true);

        try {
            // SMTP config
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wmsuequipment@gmail.com';
            $mail->Password = 'wjgsuitdayyvyosu'; // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('wmsuequipment@gmail.com', 'WMSU Equipment System');
            $mail->addAddress($newOwner['email'], "{$newOwner['first_name']} {$newOwner['last_name']}");

            $mail->isHTML(true);
            $mail->Subject = 'Equipment Transfer Request Rejected';
            $mail->Body = "
                <p>Dear {$newOwner['first_name']},</p>
                <p>Your equipment transfer request for property number <strong>{$transfer['property_number']}</strong> has been <strong>rejected</strong>.</p>
                <p><strong>Reason:</strong> {$reason}</p>
                <p>Regards,<br>WMSU Equipment Management System</p>
            ";

            $mail->send();
        } catch (Exception $ex) {
            error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Transfer rejected and email sent'
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
    