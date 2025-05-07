<?php
// Check if the current path is '/PMO/Admin' (no session check)
if ($_SERVER['REQUEST_URI'] == '/PMO/Login') {
    // Redirect to admin_login.php
    header("Location: /PMO/Login.php");
    exit();
}

// If the user is already on the login page, no redirect needed
echo "Welcome to the Login Page!";
?>
