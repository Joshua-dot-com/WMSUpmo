<?php
// create_transfer.php

require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
    $transferredEquipments = [];

    foreach ($equipmentIds as $equipmentId) {
        $fetchStmt = $pdo->prepare("SELECT equipment_name, po_jo_no, property_number FROM equipment WHERE id = :equipment_id");
        $fetchStmt->execute(['equipment_id' => $equipmentId]);
        $equipment = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        if ($equipment) {
            $equipmentName = $equipment['equipment_name'];
            $poJoNo = $equipment['po_jo_no'];
            $propertyNumber = $equipment['property_number'];

            $updateStmt = $pdo->prepare("UPDATE user_equipment SET user_id = :new_owner_id, is_pending_transfer = 1 WHERE equipment_id = :equipment_id AND user_id = :current_owner_id AND returned_at IS NULL");
            $updateStmt->execute([
                'new_owner_id' => $newOwnerId,
                'equipment_id' => $equipmentId,
                'current_owner_id' => $currentOwnerId
            ]);

            if ($updateStmt->rowCount() > 0) {
                $historyStmt = $pdo->prepare("INSERT INTO equipment_history (equipment_id, po_jo_no, from_user_id, to_user_id, action_type, notes) VALUES (:equipment_id, :po_jo_no, :from_user_id, :to_user_id, 'transfer', :notes)");
                $historyStmt->execute([
                    'equipment_id' => $equipmentId,
                    'po_jo_no' => $poJoNo,
                    'from_user_id' => $currentOwnerId,
                    'to_user_id' => $newOwnerId,
                    'notes' => "Transferred: " . $reason
                ]);

                $transferredEquipments[] = [
                    'equipment_name' => $equipmentName,
                    'po_jo_no' => $poJoNo,
                    'property_number' => $propertyNumber
                ];

                $successCount++;
            } else {
                $failedItems[] = $equipmentId;
            }
        } else {
            $failedItems[] = $equipmentId;
        }
    }

    $pdo->commit();

    // Fetch new owner's email and name
    $userStmt = $pdo->prepare("SELECT email, first_name, last_name FROM users WHERE id = :id");
    $userStmt->execute(['id' => $newOwnerId]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $newOwnerEmail = $user['email'];
        $newOwnerName = $user['first_name'] . ' ' . $user['last_name'];

        // Send email
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'wmsuequipment@gmail.com';
        $mail->Password = 'wjgsuitdayyvyosu';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('wmsuequipment@gmail.com', 'WMSU Equipment Admin');
        $mail->addAddress($newOwnerEmail, $newOwnerName);
        $mail->isHTML(true);
        $mail->Subject = 'Equipment Transferred to You';

        $equipmentListHtml = "<ul>";
        foreach ($transferredEquipments as $item) {
            $equipmentListHtml .= "<li><strong>{$item['equipment_name']}</strong><br>PO/JO No: {$item['po_jo_no']}<br>Property #: {$item['property_number']}</li>";
        }
        $equipmentListHtml .= "</ul>";

        $mail->Body = "
            <p>Dear <strong>$newOwnerName</strong>,</p>
            <p>The following equipment items have been transferred to you for the reason: <strong>$reason</strong>.</p>
            $equipmentListHtml
            <p>Please check your account for more details.</p>
            <br><p>Best regards,<br>WMSU Equipment Admin</p>
        ";

        $mail->AltBody = "Dear $newOwnerName,\n\nThe following equipment items have been transferred to you:\n" .
            implode("\n", array_map(fn($item) =>
                "{$item['equipment_name']} - PO/JO No: {$item['po_jo_no']}, Property #: {$item['property_number']}",
                $transferredEquipments)) .
            "\n\nPlease check your account.\n\nWMSU Equipment Admin";

        $mail->send();
    }

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
