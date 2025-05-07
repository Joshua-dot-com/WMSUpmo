<?php
session_start();
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to login page with a message
header("Location: /PMO/Login.php?error=You have been logged out.");
exit();
?>
