<?php
require 'PHP/db_connect.php';
require 'vendor/autoload.php'; // Adjust if needed

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        // Check if user exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Check if there is already a valid reset request
            $checkReset = $conn->prepare("SELECT * FROM password_resets WHERE email = ? ORDER BY created_at DESC LIMIT 1");
            $checkReset->bind_param("s", $email);
            $checkReset->execute();
            $existing = $checkReset->get_result();

            if ($existing->num_rows > 0) {
                $existingReset = $existing->fetch_assoc();
                $expiresAt = strtotime($existingReset['expires_at']);
                $now = time();
                $minutesLeft = ceil(($expiresAt - $now) / 60);

                if ($expiresAt > $now) {
                    // User has already requested a reset and the token is still valid
                    header("Location: Login.php?already_requested=1&minutes_remaining=$minutesLeft");
                    exit;
                }
            }

            // Generate a new token for password reset
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $created = date('Y-m-d H:i:s');

            // Insert or update reset request
            $insert = $conn->prepare("
                INSERT INTO password_resets (email, token, expires_at, created_at) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                    token = VALUES(token),
                    expires_at = VALUES(expires_at),
                    created_at = VALUES(created_at)
            ");
            $insert->bind_param("ssss", $email, $token, $expires, $created);
            $insert->execute();

            // Send reset email
            $resetLink = "http://localhost/PMO/user-reset-password.php?token=$token";

            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'wmsuequipment@gmail.com';
                $mail->Password = 'wjgsuitdayyvyosu'; // Replace with env var in production
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('wmsuequipment@gmail.com', 'WMSU Equipment Admin');
                $mail->addAddress($email, $user['first_name'] . ' ' . $user['last_name']);
                $mail->Subject = 'Password Reset Request';
                $mail->isHTML(true);
                $mail->Body = "
                    <p>Hi <strong>{$user['first_name']}</strong>,</p>
                    <p>We received a request to reset your password. Click the link below to proceed:</p>
                    <p><a href='$resetLink'>$resetLink</a></p>
                    <p>If you didn’t request this, please ignore this email.</p>
                    <br>
                    <p>Regards,<br>WMSU Equipment Admin</p>
                ";
                $mail->AltBody = "Visit this link to reset your password: $resetLink";

                $mail->send();
                header("Location: Login.php?success=1");
                exit;

            } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
                header("Location: Login.php?error=" . urlencode("Failed to send reset email. Please try again."));
                exit;
            }
        } else {
            // Email not found
            header("Location: Login.php?error=" . urlencode("This email is not registered in the system."));
            exit;
        }
    }

    // Email was empty
    header("Location: Login.php?error=" . urlencode("Please enter a valid email address."));
    exit;
}
?>
