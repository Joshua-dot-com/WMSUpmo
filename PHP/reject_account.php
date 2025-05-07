<?php
session_start();

require 'db_connect.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Ensure an admin is logged in
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin not logged in']);
    exit;
}

// Fetch admin info
$admin = $_SESSION['admin'];
$adminId = $admin['id'];

$adminQuery = $conn->prepare("SELECT first_name, last_name FROM admin WHERE id = ?");
$adminQuery->bind_param("i", $adminId);
$adminQuery->execute();
$adminResult = $adminQuery->get_result();

if ($adminResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Reviewer not found']);
    exit;
}

$admin = $adminResult->fetch_assoc();
$reviewedBy = $admin['first_name'] . ' ' . $admin['last_name'];

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    $id = intval($data['id']);

    // Fetch user details
    $stmt = $conn->prepare("SELECT role, email, CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) AS name FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    $user = $result->fetch_assoc();
    $email = $user['email'];
    $name = $user['name'];

    // Set status to Rejected and is_admin to 0
    $updateStmt = $conn->prepare("UPDATE users SET status = 'Rejected', is_admin = 0, reviewed_by = ? WHERE id = ?");
    $updateStmt->bind_param("si", $reviewedBy, $id);

    if ($updateStmt->execute()) {
        // Send rejection email
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            // Set your Gmail and app password here
            $mail->Username = 'wmsuequipment@gmail.com';
            $mail->Password = 'wjgsuitdayyvyosu'; // Use Gmail App Password

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('wmsuequipment@gmail.com', 'WMSU Equipment Admin');
            $mail->addAddress($email, $name);

            $mail->Subject = 'Your Account Has Been Rejected ❌';
            $mail->isHTML(true);

            $mail->Body = "
                <p>Dear <strong>$name</strong>,</p>
                <p>We regret to inform you that your account has been <strong>rejected</strong> by <strong>$reviewedBy</strong>.</p>
                <p>If you believe this was a mistake or you have any questions, please contact support.</p>
                <br><p>Best regards,<br>WMSU Equipment Admin</p>
            ";
            $mail->AltBody = "Dear $name,\n\nYour account has been rejected by Admin. \nIf you believe this is a mistake, please contact support.\n\nBest regards,\nWMSU Equipment Admin";

            $mail->send();

            // Delete the user from the users table
            $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $deleteStmt->bind_param("i", $id);
            $deleteStmt->execute();

            echo json_encode(['success' => true, 'message' => 'Account rejected and user deleted successfully']);
        } catch (Exception $e) {
            error_log("Email failed: " . $mail->ErrorInfo);
            echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to reject account']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
