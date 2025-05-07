<?php
session_start();
require 'PHP/db_connect.php';
header('Content-Type: application/json');

$response = ['success' => false, 'error' => 'Unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        // Try logging in as admin
        $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $adminResult = $stmt->get_result();

        if ($adminResult->num_rows === 1) {
            $admin = $adminResult->fetch_assoc();

            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin'] = [
                    'id' => $admin['id'],
                    'first_name' => $admin['first_name'],
                    'last_name' => $admin['last_name'],
                    'email' => $admin['email'],
                    'role' => $admin['role'],
                    'is_owner' => (int)$admin['is_owner'],
                    'created_at' => $admin['created_at'],
                    'updated_at' => $admin['updated_at']
                ];

                $response['success'] = true;
                $response['redirect'] = ($admin['is_owner'] == 1)
                    ? '/PMO/Super-Admin/Dashboard.php'
                    : '/PMO/Administration/Admin-Dashboard.php';

                echo json_encode($response);
                exit;
            } else {
                $response['error'] = "Incorrect email or password.";
                echo json_encode($response);
                exit;
            }
        }

        // Try logging in as regular user
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $userResult = $stmt->get_result();

        if ($userResult->num_rows === 1) {
            $user = $userResult->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                if ($user['status'] === 'Granted') {
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'first_name' => $user['first_name'],
                        'last_name' => $user['last_name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'college' => $user['college'],
                        'admin_role' => $user['admin_role'],
                        'other_services' => $user['other_services'],
                        'status' => $user['status'],
                        'is_admin' => $user['is_admin'],
                        'is_owner' => isset($user['is_owner']) ? (int)$user['is_owner'] : 0,
                        'profile_picture' => $user['profile_picture'],
                        'created_at' => $user['created_at'],
                        'updated_at' => $user['updated_at'],
                        'login_attempts' => $user['login_attempts'],
                        'last_login' => $user['last_login']
                    ];

                    $response['success'] = true;
                    $response['redirect'] = ($user['is_owner'] == 1)
                        ? '/PMO/Super-Admin/Dashboard.php'
                        : (($user['is_admin'] == 1)
                            ? '/PMO/Administration/Admin-Dashboard.php'
                            : '/PMO/User/user-profile.php');
                } else {
                    $response['error'] = "Your account is not granted access.";
                }
            } else {
                $response['error'] = "Incorrect email or password.";
            }
        } else {
            $response['error'] = "No account found with that email.";
        }

        $stmt->close();
    } else {
        $response['error'] = "Please enter both email and password.";
    }
}

echo json_encode($response);
exit;
