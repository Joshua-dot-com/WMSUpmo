<?php
$host = "localhost";
$user = "root"; // Default XAMPP MySQL user
$password = ""; // Default XAMPP MySQL password (empty)
$database = "equipment database"; // Database name

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
