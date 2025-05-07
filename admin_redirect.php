<?php
// Check if the current path is '/PMO/Admin' (no session check)
if ($_SERVER['REQUEST_URI'] == '/PMO/Admin') {
    // Redirect to admin_login.php
    header("Location: /PMO/admin_Login.php");
    exit();
}

// If the user is already on the login page, no redirect needed
echo "Welcome to the Admin Page or Login Page!";
?>
