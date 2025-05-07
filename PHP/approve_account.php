<?php
session_start(); // Start session to access logged-in admin

require 'db_connect.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// 1) Ensure an admin is logged in
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin not logged in']);
    exit;
}

// 2) Fetch reviewer name
$adminData = $_SESSION['admin'];
$adminId   = $adminData['id']; 
$adminQ    = $conn->prepare("SELECT first_name, last_name FROM admin WHERE id = ?");
$adminQ->bind_param("i", $adminId);
$adminQ->execute();
$res = $adminQ->get_result();
if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Reviewer not found']);
    exit;
}
$adminRow   = $res->fetch_assoc();
$reviewedBy = $adminRow['first_name'] . ' ' . $adminRow['last_name'];

try {
    // 3) Decode incoming JSON
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }
    $id = intval($data['id']);

    // 4) Read desired accessType: "admin" or "user"
    $accessType = isset($data['accessType']) && $data['accessType'] === 'admin'
                ? 'admin'
                : 'user';
    $is_admin = ($accessType === 'admin') ? 1 : 0;

    // 5) Fetch the user
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $userRes = $stmt->get_result();
    if ($userRes->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    $user = $userRes->fetch_assoc();
    $email    = $user['email'];
    $name     = $user['first_name'] . ' ' . $user['last_name'];
    $college  = $user['college'];
    $admin_role = $user['admin_role'];
    $password   = $user['password'];  // already hashed if you used password_hash()

    // 6) Prevent duplicate in overview_users
    $emailCheck = $conn->prepare("SELECT id FROM overview_users WHERE email = ?");
    $emailCheck->bind_param("s", $email);
    $emailCheck->execute();
    if ($emailCheck->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'The email is already in use.']);
        exit;
    }

    // 7) If user was previously "rejected", delete them so we can re-create
    if (strtolower($user['status']) === 'rejected') {
        $del = $conn->prepare("DELETE FROM users WHERE id = ?");
        $del->bind_param("i", $id);
        $del->execute();
        $del->close();
    } else {
        // 8) Otherwise, update status to "Granted", set is_admin & reviewed_by
        $upd = $conn->prepare("
            UPDATE users
            SET status     = 'Granted',
                is_admin   = ?,
                reviewed_by= ?
            WHERE id = ?
        ");
        $upd->bind_param("isi", $is_admin, $reviewedBy, $id);
        if (!$upd->execute()) {
            echo json_encode(['success' => false, 'message' => 'Failed to approve account']);
            exit;
        }
        $upd->close();
    }

    // 9) Send approval email
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'wmsuequipment@gmail.com';
        $mail->Password   = 'wjgsuitdayyvyosu';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('wmsuequipment@gmail.com', 'WMSU Equipment Admin');
        $mail->addAddress($email, $name);
        $mail->Subject    = 'Your Account Has Been Approved ✅';
        $mail->isHTML(true);

        $mail->Body = "
          <p>Dear <strong>$name</strong>,</p>
          <p>Your account has been <strong>approved</strong> by <strong>$reviewedBy</strong>.</p>
          <p>Your access level is: <strong>" . ucfirst($accessType) . " Access</strong>.</p>
          <p>You may now log in and access the system.</p>
          <br><p>Best regards,<br>WMSU Equipment Admin</p>
        ";
        $mail->AltBody = "Dear $name,\n\n"
                       . "Your account has been approved by $reviewedBy.\n"
                       . "Your access level is: " . ucfirst($accessType) . " Level Access.\n\n"
                       . "You may now log in and access the system.\n\n"
                       . "Best regards,\nWMSU Equipment Admin";

        $mail->send();
    } catch (Exception $e) {
        error_log("Email send failed: " . $mail->ErrorInfo);
    }

    echo json_encode(['success' => true, 'message' => 'Account approved successfully']);
    exit;

} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        echo json_encode(['success' => false, 'message' => 'The email is already in use.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
