<?php
session_start();
require 'PHP/db_connect.php'; // Adjust path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                if ($user['status'] === 'Granted') {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['college'] = $user['college'];
                    $_SESSION['admin_role'] = $user['admin_role'];
                    $_SESSION['other_services'] = $user['other_services'];
                    $_SESSION['is_admin'] = $user['is_admin'];
                    $_SESSION['is_owner'] = $user['is_owner'];

                    if ($user['is_admin'] == 1 || $user['is_owner'] == 1) {
                        header('Location: index.php');
                    } else {
                        header('Location: User-Dashboard.php');
                    }
                    exit;
                } else {
                    $error = "Your account is not granted access.";
                }
            } else {
                $error = "Incorrect email or password.";
            }
        } else {
            $error = "No account found with that email.";
        }
        $stmt->close();
    } else {
        $error = "Please enter both email and password.";
    }

    // Redirect back to login with error
    header("Location: Login.php?error=" . urlencode($error));
    exit;
}
?>
