<?php
// Database connection details
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]));
}

header('Content-Type: application/json');

try {
    // SQL query to get all pending transfer requests
    $query = "SELECT 
                et.id AS request_id, 
                et.equipment_id, 
                e.equipment_name, 
                u1.first_name AS current_owner_first_name, 
                u1.last_name AS current_owner_last_name, 
                u2.first_name AS new_owner_first_name, 
                u2.last_name AS new_owner_last_name, 
                et.reason, 
                et.transfer_date, 
                et.status
              FROM 
               equipment_transfers et
              JOIN 
                users u1 ON et.current_owner_id = u1.id
              JOIN 
                users u2 ON et.new_owner_id = u2.id
              JOIN 
                equipment e ON et.equipment_id = e.id
              WHERE 
                et.status = 'Pending'";

    // Execute the query
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $requests = [];

        while ($row = $result->fetch_assoc()) {
            $requests[] = [
                'id' => $row['request_id'], // renamed to match JS expectation
                'equipment_name' => $row['equipment_name'], // renamed
                'recipient_name' => $row['new_owner_first_name'] . ' ' . $row['new_owner_last_name'], // renamed
                'transfer_date' => $row['transfer_date'], // renamed
                'status' => $row['status']
            ];
        }

        echo json_encode(['success' => true, 'requests' => $requests]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No pending transfer requests found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred while fetching transfer requests', 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
