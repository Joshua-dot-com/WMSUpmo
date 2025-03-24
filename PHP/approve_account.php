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

    // Fetch user details
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
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
    $name = $user['first_name'] . ' ' . $user['last_name'];
    $college = $user['college']; 
    $admin_role = $user['admin_role'];

    // ✅ Check role and set is_admin accordingly
    $is_admin = ($role === "Administrative Officials") ? 1 : 0;

    // If user status is 'Rejected', remove the record first
    if (strtolower($user['status']) === 'rejected') {
        $deleteRejected = $conn->prepare("DELETE FROM users WHERE id = ?");
        $deleteRejected->bind_param("i", $id);
        $deleteRejected->execute();
        $deleteRejected->close();
    } else {
        // Update user status to 'Granted' and is_admin accordingly
        $stmt = $conn->prepare("UPDATE users SET status = 'Granted', is_admin = ? WHERE id = ?");
        $stmt->bind_param("ii", $is_admin, $id);

        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Failed to approve account']);
            exit;
        }
    }

    // ✅ Insert into overview_users with admin_role
    $insertStmt = $conn->prepare("
        INSERT INTO overview_users 
        (first_name, last_name, email, college, role, admin_role, status, last_login, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 'Active', ?, NOW())
    ");

    $insertStmt->bind_param(
        "sssssss",
        $user['first_name'],
        $user['last_name'],
        $user['email'],
        $college,
        $user['role'],
        $admin_role,
        $user['last_login']
    );

    if ($insertStmt->execute()) {
        // ✅ Send email notification
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wmsuequipment@gmail.com';
            $mail->Password = 'wrbtdkgykpesnjnn'; // Load from env ideally
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('wmsuequipment@gmail.com', 'WMSU Equipment Admin');
            $mail->addReplyTo('wmsuequipment@gmail.com', 'WMSU Equipment Admin');
            $mail->addAddress($email, $name);

            $mail->Subject = 'Your Account Has Been Approved ✅';

            $htmlBody = "
                <html>
                    <body>
                        <p>Dear <strong>$name</strong>,</p>
                        <p>We are pleased to inform you that your account has been <b>approved</b>.</p>
                        <p>You may now log in and access the system.</p>
                        <br>
                        <p>Best regards,<br>WMSU Equipment Admin</p>
                    </body>
                </html>";
            
            $plainText = "Dear $name,\n\nYour account has been approved.\n\nYou may now log in and access the system.\n\nBest regards,\nWMSU Equipment Admin";

            $mail->isHTML(true);
            $mail->Body = $htmlBody;
            $mail->AltBody = $plainText;
            $mail->SMTPDebug = 0;
            $mail->Priority = 3;

            $mail->send();
        } catch (Exception $e) {
            error_log("Failed to send email: " . $mail->ErrorInfo);
        }

        echo json_encode(['success' => true, 'message' => 'Account approved and added to overview successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to insert into overview_users']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
