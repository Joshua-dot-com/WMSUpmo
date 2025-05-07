<?php
require 'PHP/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        exit("Passwords do not match.");
    }

    // Get reset record
    $stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        exit("Invalid or expired reset link.");
    }

    $row = $result->fetch_assoc();
    if (strtotime($row['expires_at']) < time()) {
        exit("Reset link has expired.");
    }

    $email = $row['email'];
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Update password in admin table
    $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed, $email);
    $stmt->execute();

    // Delete reset token
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    echo "Password has been reset successfully. <a href='Login.php'>Login now</a>";
}
?>
