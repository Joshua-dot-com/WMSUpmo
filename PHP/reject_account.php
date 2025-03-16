<?php
require 'db_connect.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

try {
    // Get the request data
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    $id = $data['id'];

    // Fetch user role, email, and full name
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

    // For rejected accounts, set is_admin to 0
    $is_admin = 0;

    // Update user status to "Rejected"
    $stmt = $conn->prepare("UPDATE users SET status = 'Rejected', is_admin = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_admin, $id);

    if ($stmt->execute()) {
        // Send email notification about account rejection
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wmsuequipment@gmail.com'; // Your Gmail address
            $mail->Password = 'wrbtdkgykpesnjnn';         // Your App Password (use env variables in production)
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('wmsuequipment@gmail.com', 'Admin');
            $mail->addAddress($email, $name);
            $mail->Subject = 'Account Rejected';
            $mail->Body = "Dear $name,\n\nWe regret to inform you that your account has been rejected. Please contact support for further details.\n\nBest regards,\nAdmin";

            // Disable debug output for production
            $mail->SMTPDebug = 0;
            $mail->send();
        } catch (Exception $e) {
            error_log("Failed to send email to $email: " . $e->getMessage());
        }

        echo json_encode(['success' => true, 'message' => 'Account rejected successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to reject account']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
