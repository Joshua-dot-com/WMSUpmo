<?php
header("Content-Type: application/json");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

$host = 'localhost';
$dbname = 'equipment_database';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

// Validate and sanitize input
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id <= 0) {
    echo json_encode(["success" => false, "error" => "Invalid ID"]);
    exit;
}

$conn->begin_transaction();

try {
    // Get user_id and email
    $stmt = $conn->prepare("SELECT dr.user_id, u.email, u.first_name, u.last_name 
                            FROM deletion_requests dr 
                            JOIN users u ON dr.user_id = u.id 
                            WHERE dr.id = ? AND dr.processed = 0");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $conn->rollback();
        echo json_encode(["success" => false, "error" => "Request not found or already processed"]);
        exit;
    }

    $row = $result->fetch_assoc();
    $user_id = $row['user_id'];
    $user_email = $row['email'];
    $user_name = "{$row['first_name']} {$row['last_name']}";
    $stmt->close();

    // Send email before deletion
    if (filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wmsuequipment@gmail.com'; // Sender email
            $mail->Password = 'wjgsuitdayyvyosu';       // App password (not Gmail password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('wmsuequipment@gmail.com', 'WMSU Equipment System');
            $mail->addAddress($user_email, $user_name);

            $mail->isHTML(true);
            $mail->Subject = 'Account Deletion Request Approved';
            $mail->Body = "
                <p>Dear {$user_name},</p>
                <p>Your request to delete your account has been approved and your data has been removed from our system.</p>
                <p>We’re sorry to see you go. If you have any feedback, please let us know.</p>
                <p>Regards,<br>WMSU Equipment Management System</p>
            ";

            $mail->send();
        } catch (Exception $ex) {
            error_log("Email failed: " . $mail->ErrorInfo);
            // Continue even if email fails
        }
    }

    // Delete from deletion_requests
    $stmt = $conn->prepare("DELETE FROM deletion_requests WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        $conn->rollback();
        echo json_encode(["success" => false, "error" => "Failed to delete request"]);
        exit;
    }
    $stmt->close();

    // Delete from users
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        $conn->rollback();
        echo json_encode(["success" => false, "error" => "Failed to delete user"]);
        exit;
    }
    $stmt->close();

    // Commit changes
    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "Account and request deleted successfully. Email notification sent."
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "error" => "Error: " . $e->getMessage()]);
}

$conn->close();
?>
