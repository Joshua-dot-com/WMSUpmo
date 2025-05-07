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
    die("Connection failed: " . $conn->connect_error);
}

// Set the response content type to JSON
header('Content-Type: application/json');

// Query to get user equipment data and equipment counts per category
$sql = "SELECT ue.id, ue.user_id, ue.equipment_id, ue.property_number, ue.po_jo_no, ue.assigned_at, ue.returned_at, ue.notes, 
               e.equipment_name, e.category, e.status, e.description, e.units
        FROM user_equipment ue
        JOIN equipment e ON ue.equipment_id = e.id";

$result = $conn->query($sql);

// Check if there are any records
if ($result->num_rows > 0) {
    // Create an array to store the data
    $equipmentData = [];
    
    // Fetch all the records
    while($row = $result->fetch_assoc()) {
        $equipmentData[] = [
            'id'              => $row['id'],
            'user_id'         => $row['user_id'],
            'equipment_id'    => $row['equipment_id'],
            'property_number' => $row['property_number'],
            'po_jo_no'        => $row['po_jo_no'],
            'assigned_at'     => $row['assigned_at'],
            'returned_at'     => $row['returned_at'],
            'notes'           => $row['notes'],
            'equipment_name'  => $row['equipment_name'],
            'category'        => $row['category'],
            'status'          => $row['status'],
            'description'     => $row['description'],
            'units'           => $row['units'],
        ];
    }

    // Query to get the count of equipment per category
    $categoryQuery = "SELECT category, COUNT(*) AS equipment_count FROM equipment GROUP BY category";
    $categoryResult = $conn->query($categoryQuery);

    // Prepare category data for the chart
    $categoryData = [
        'labels' => [],
        'datasets' => [
            [
                'label' => 'Number of Equipments per Category',
                'data' => [],
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',  // Customize color if needed
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'borderWidth' => 1
            ]
        ]
    ];

    // Check if category query has results
    if ($categoryResult->num_rows > 0) {
        // Fetch the category counts
        while ($categoryRow = $categoryResult->fetch_assoc()) {
            $categoryData['labels'][] = $categoryRow['category'];
            $categoryData['datasets'][0]['data'][] = (int)$categoryRow['equipment_count'];
        }
    } else {
        // Log if the category query fails
        error_log("No data found for category counts.");
    }

    // Return both equipment data and category data as JSON
    echo json_encode([
        'equipmentData' => $equipmentData,
        'categoryData'  => $categoryData
    ]);
} else {
    // Log if the equipment data query fails
    error_log("No data found for equipment.");

    // Return empty arrays if no data found
    echo json_encode(['equipmentData' => [], 'categoryData' => []]);  
}

// Close the connection
$conn->close();
?>
