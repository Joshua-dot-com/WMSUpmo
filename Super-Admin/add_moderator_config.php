<?php
require 'db_connect.php'; // Include database connection

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST["firstName"] ?? "");
    $lastName = trim($_POST["lastName"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $role = trim($_POST["role"] ?? "");

    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($role)) {
        echo json_encode(["success" => false, "error" => "All fields are required."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "error" => "Invalid email format."]);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Only using is_owner now
    if ($role === "admin") {
        $isOwner = 0;
    } elseif ($role === "moderator") {
        $isOwner = 1;
    } else {
        echo json_encode(["success" => false, "error" => "Invalid role. Use 'admin' or 'moderator'."]);
        exit;
    }

    try {
        // Check if email already exists
        $checkStmt = $conn->prepare("SELECT id FROM admin WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            echo json_encode(["success" => false, "error" => "Duplicate entry: This email is already registered."]);
            $checkStmt->close();
            exit;
        }
        $checkStmt->close();

        // Insert new user (without is_admin)
        $stmt = $conn->prepare("INSERT INTO admin (email, password, is_owner, first_name, last_name, role, created_at) 
                                VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssisss", $email, $hashedPassword, $isOwner, $firstName, $lastName, $role);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "$role user added successfully"]);
        } else {
            echo json_encode(["success" => false, "error" => "Database error: " . $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } catch (mysqli_sql_exception $e) {
        echo json_encode(["success" => false, "error" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method."]);
}
?>
