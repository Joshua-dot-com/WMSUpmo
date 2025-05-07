<?php
session_start();
require 'PHP/db_connect.php'; // Adjust path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT *, is_blocked, block_reason, block_until FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Check if user is blocked
                if ($user['is_blocked'] == 1) {
                    $blockExpired = false;
                    if ($user['block_until'] !== null) {
                        $blockUntil = new DateTime($user['block_until']);
                        $today = new DateTime();

                        if ($today > $blockUntil) {
                            $unblockStmt = $conn->prepare("UPDATE users SET is_blocked = 0, block_reason = NULL, block_until = NULL WHERE id = ?");
                            $unblockStmt->bind_param("i", $user['id']);
                            $unblockStmt->execute();
                            $unblockStmt->close();
                            $blockExpired = true;
                        } else {
                            $formattedDate = $blockUntil->format('F j, Y');
                            $error = "Your account has been blocked. Reason: " . $user['block_reason'] . ". Your account will be unblocked on " . $formattedDate . ".";
                        }
                    } else {
                        $error = "Your account has been blocked. Reason: " . $user['block_reason'] . ". Please contact an administrator for assistance.";
                    }

                    if (!$blockExpired) {
                        header("Location: Login.php?error=" . urlencode($error) . "&blocked=1");
                        exit;
                    }
                }

                if (strtolower($user['status']) === 'granted') {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['college'] = $user['college'];
                    $_SESSION['admin_role'] = $user['admin_role'];
                    $_SESSION['other_services'] = $user['other_services'];
                    $_SESSION['is_admin'] = $user['is_admin'];
                    $_SESSION['profile_picture'] = $user['profile_picture']; // ✅ ADD THIS LINE

                    // ✅ Update last login and account_state
                    $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW(), account_state = 'Active' WHERE id = ?");
                    $updateStmt->bind_param("i", $user['id']);
                    $updateStmt->execute();
                    $updateStmt->close();

                    // Redirect based on role
                    if ($user['is_admin'] == 1) {
                        header('Location: http://localhost/PMO/Administration/Admin-Dashboard.php');
                    } else {
                        header('Location: http://localhost/PMO/User/user-profile.php');
                    }
                    exit;
                } else {
                    $error = "Your account has not been granted access please be back in a minute.";
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

    header("Location: Login.php?error=" . urlencode($error));
    exit;
}
?>
