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

// Prepare the SQL query to fetch all equipment details
$query = "SELECT id, po_jo_no, equipment_name, property_number, account_code, purchase_date, ris_no, oblig_no, units, category, description, status, created_at, updated_at FROM equipment";

// Execute the query
$result = $conn->query($query);

// Check if we got results
if ($result->num_rows > 0) {
    $equipment = [];

    // Loop through each row and fetch data
    while ($row = $result->fetch_assoc()) {
        // Decode HTML entities to get the original description
        $description = html_entity_decode($row['description']); // Decode HTML entities

        // Optionally, limit the description length for the snippet
        $descriptionSnippet = strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;

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
    // No equipment found
    echo json_encode([
        'success' => false,
        'message' => 'No equipment found in the database'
    ]);
}

// Close the database connection
$conn->close();
?>
