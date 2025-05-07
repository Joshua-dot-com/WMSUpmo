<?php
require 'PHP/db_connect.php'; // Adjust as needed

// Set accounts to 'Inactive' if last_login was over 24 hours ago
$sql = "UPDATE users 
        SET account_state = 'Inactive' 
        WHERE last_login IS NOT NULL 
        AND last_login < NOW() - INTERVAL 1 DAY 
        AND account_state != 'Inactive'";

if ($conn->query($sql) === TRUE) {
    echo "Inactive accounts updated successfully.";
} else {
    echo "Error updating accounts: " . $conn->error;
}

$conn->close();
?>
