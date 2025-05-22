<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database"; // Avoid spaces in database names

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]));
}

// Set headers to allow cross-origin requests (optional, remove if not needed)
header('Content-Type: application/json');

// Get the equipment ID from the URL parameter
if (isset($_GET['id'])) {
    $equipmentId = $_GET['id'];
    
    // Sanitize the input to prevent SQL injection
    $equipmentId = $conn->real_escape_string($equipmentId);
    
    // Prepare the SQL query to fetch specific equipment details
    $query = "SELECT id, po_jo_no, equipment_name, property_number, account_code, purchase_date, ris_no, oblig_no, units, category, description, status, created_at, updated_at FROM equipment WHERE id = '$equipmentId'";
} else {
    // If no ID is provided, return an error
    echo json_encode([
        'success' => false,
        'message' => 'No equipment ID provided'
    ]);
    $conn->close();
    exit;
}

// Execute the query
$result = $conn->query($query);

// Check if we got results
if ($result->num_rows > 0) {
    $equipment = [];

    // Loop through each row and fetch data
    while ($row = $result->fetch_assoc()) {
        // Decode HTML entities to get the original description
        $description = html_entity_decode($row['description']); // Decode HTML entities

        $equipment[] = [
            'id' => $row['id'],
            'po_jo_no' => $row['po_jo_no'],
            'equipment_name' => $row['equipment_name'],
            'property_number' => $row['property_number'],
            'account_code' => $row['account_code'],
            'purchase_date' => $row['purchase_date'],
            'ris_no' => $row['ris_no'],
            'oblig_no' => $row['oblig_no'],
            'units' => $row['units'],
            'category' => $row['category'],
            'description' => $description, // Full description returned
            'status' => $row['status'], // Include status column
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ];
    }

    // Return JSON response with equipment data
    echo json_encode([
        'success' => true,
        'data' => $equipment
    ]);
} else {
    // No equipment found with the given ID
    echo json_encode([
        'success' => false,
        'message' => "Equipment with ID $equipmentId not found"
    ]);
}

// Close the database connection
$conn->close();
?>