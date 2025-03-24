<?php
header("Content-Type: application/json");
include 'db_connect.php';

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $first_name = trim($_POST['first-name'] ?? '');
    $last_name = trim($_POST['last-name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $admin_role = trim($_POST['adminRole'] ?? '');
    $other_services = trim($_POST['otherServices'] ?? '');
    $college = trim($_POST['college'] ?? ''); // ✅ Add this line to capture the college

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($role)) {
        $response["message"] = "All required fields must be filled!";
        echo json_encode($response);
        exit;
    }

    // Check if email already exists and fetch status
    $check_email = $conn->prepare("SELECT id, status FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (strtolower($user['status']) !== 'rejected') {
            $response["message"] = "Email is already registered!";
            echo json_encode($response);
            exit;
        } else {
            // Allow re-registration by deleting the rejected record
            $delete_rejected = $conn->prepare("DELETE FROM users WHERE id = ?");
            $delete_rejected->bind_param("i", $user['id']);
            $delete_rejected->execute();
            $delete_rejected->close();
        }
    }
    $check_email->close();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // ✅ Insert user with college
    $sql = "INSERT INTO users (first_name, last_name, email, password, role, admin_role, other_services, college)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $first_name, $last_name, $email, $hashed_password, $role, $admin_role, $other_services, $college);

    if ($stmt->execute()) {
        $response["success"] = true;
        $response["message"] = "Registration successful!";
    } else {
        $response["message"] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

echo json_encode($response);
?>
