<?php
require 'db_connect.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    $id = $data['id'];

    // Fetch user role, email, and name
    $stmt = $conn->prepare("SELECT role, email, CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) AS name FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    $user = $result->fetch_assoc();
    $role = $user['role'];
    $email = $user['email'];
    $name = $user['name'];

    $is_admin = ($role === "Administrative") ? 1 : 0;

    // Update user status
    $stmt = $conn->prepare("UPDATE users SET status = 'Granted', is_admin = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_admin, $id);

    if ($stmt->execute()) {
        // Send email
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wmsuequipment@gmail.com';
            $mail->Password = 'wrbtdkgykpesnjnn';  // Use environment variables in production!
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('wmsuequipment@gmail.com', 'Admin');
            $mail->addAddress($email, $name);
            $mail->Subject = 'Account Approved';
            $mail->Body = "Dear $name,\n\nYour account has been approved. You can now log in.\n\nBest regards,\nAdmin";

            // Remove debugging output
            $mail->SMTPDebug = 0;

            $mail->send();
        } catch (Exception $e) {
            error_log("Failed to send email: " . $e->getMessage());
        }

        echo json_encode(['success' => true, 'message' => 'Account approved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to approve account']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>