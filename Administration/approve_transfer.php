<?php
// approve_transfer.php

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database credentials
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

// Set content type
header('Content-Type: application/json');

// Database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

// Read and validate input
$data = json_decode(file_get_contents('php://input'), true);
$transferId = isset($data['transfer_id']) ? intval($data['transfer_id']) : null;

if (!$transferId) {
    http_response_code(400);
    echo json_encode(['error' => 'Transfer ID is required']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Fetch transfer and equipment details
    $stmt = $pdo->prepare("
        SELECT et.*, e.property_number, e.po_jo_no, e.id AS equipment_id
        FROM equipment_transfers et
        JOIN equipment e ON et.equipment_id = e.id
        WHERE et.id = :transfer_id AND et.status = 'pending'
    ");
    $stmt->execute(['transfer_id' => $transferId]);
    $transfer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transfer) {
        throw new Exception('Transfer not found or already processed');
    }

    // Fetch both users (current and new owner)
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
    foreach ($users as $user) {
        if ($user['id'] == $transfer['current_owner_id']) {
            $currentOwner = $user;
        } elseif ($user['id'] == $transfer['new_owner_id']) {
            $newOwner = $user;
        }
    }

    $currentName = $currentOwner 
        ? "{$currentOwner['first_name']} {$currentOwner['last_name']} ({$currentOwner['role']})"
        : "User ID {$transfer['current_owner_id']}";

    $newName = $newOwner 
        ? "{$newOwner['first_name']} {$newOwner['last_name']} ({$newOwner['role']})"
        : "User ID {$transfer['new_owner_id']}";

    // Update transfer status
    $pdo->prepare("
        UPDATE equipment_transfers 
        SET status = 'transferred', approved_at = NOW()
        WHERE id = :transfer_id
    ")->execute(['transfer_id' => $transferId]);

    // Mark old assignment returned
    $pdo->prepare("
        UPDATE user_equipment 
        SET returned_at = NOW()
        WHERE equipment_id = :equipment_id AND returned_at IS NULL
    ")->execute(['equipment_id' => $transfer['equipment_id']]);

    // Assign new user
    $pdo->prepare("
        INSERT INTO user_equipment 
        (user_id, equipment_id, property_number, po_jo_no, assigned_at) 
        VALUES 
        (:user_id, :equipment_id, :property_number, :po_jo_no, NOW())
    ")->execute([
        'user_id' => $transfer['new_owner_id'],
        'equipment_id' => $transfer['equipment_id'],
        'property_number' => $transfer['property_number'],
        'po_jo_no' => $transfer['po_jo_no']
    ]);

    // Insert into equipment history
    $pdo->prepare("
        INSERT INTO equipment_history 
        (equipment_id, po_jo_no, action_type, from_user_id, to_user_id, notes)
        VALUES 
        (:equipment_id, :po_jo_no, 'transfer', :from_user_id, :to_user_id, 'Transfer completed')
    ")->execute([
        'equipment_id' => $transfer['equipment_id'],
        'po_jo_no' => $transfer['po_jo_no'],
        'from_user_id' => $transfer['current_owner_id'],
        'to_user_id' => $transfer['new_owner_id']
    ]);

    // Log activity
    $logMessage = "Transfer completed: Equipment [{$transfer['property_number']}] transferred from {$currentName} to {$newName}.";
    $pdo->prepare("
        INSERT INTO activity_log (message, timestamp)
        VALUES (:message, NOW())
    ")->execute(['message' => $logMessage]);

    $pdo->commit();

    // Send email to new owner
    if ($newOwner && !empty($newOwner['email'])) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wmsuequipment@gmail.com';
            $mail->Password = 'wjgsuitdayyvyosu'; // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('wmsuequipment@gmail.com', 'WMSU Equipment Admin');
            $mail->addAddress($newOwner['email'], "{$newOwner['first_name']} {$newOwner['last_name']}");
            $mail->Subject = 'Equipment Transfer Completed';
            $mail->isHTML(true);
            $mail->Body = "
                <p>Dear <strong>{$newOwner['first_name']} {$newOwner['last_name']}</strong>,</p>
                <p>Your equipment transfer has been <strong>completed</strong>.</p>
                <p><strong>Equipment:</strong> {$transfer['property_number']}</p>
                <p><strong>PO/JO Number:</strong> {$transfer['po_jo_no']}</p>
                <p><strong>Previous Owner:</strong> {$currentName}</p>
                <p><strong>Date:</strong> " . date('F j, Y, g:i A') . "</p>
                <br><p>Regards,<br>WMSU Equipment Admin</p>
            ";
            $mail->AltBody = "Dear {$newOwner['first_name']} {$newOwner['last_name']},\n\n"
                           . "Your equipment transfer (Property No. {$transfer['property_number']}) has been completed.\n\n"
                           . "Previous Owner: {$currentName}\n"
                           . "Date: " . date('F j, Y, g:i A') . "\n\n"
                           . "Regards,\nWMSU Equipment Admin";

            $mail->send();
        } catch (Exception $e) {
            error_log("Email failed to send: " . $mail->ErrorInfo);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Transfer approved and email sent.']);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Transfer failed: ' . $e->getMessage()]);
}
?>
