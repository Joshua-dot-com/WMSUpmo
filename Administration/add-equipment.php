<?php
header('Content-Type: application/json');

// Enable strict error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]));
}

// Get JSON payload
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit;
}

// Prepare and sanitize inputs
$equipment_name = $conn->real_escape_string($data["equipment_name"]);  // Handle equipment_name
$category = $conn->real_escape_string($data["category"]);  // Handle category
$po_jo_no = $conn->real_escape_string($data["po_jo_no"]);
$property_number = $conn->real_escape_string($data["property_number"]);
$account_code = $conn->real_escape_string($data["account_code"]);
$purchase_date = $conn->real_escape_string($data["purchase_date"]);
$ris_no = $conn->real_escape_string($data["ris_no"]);
$oblig_no = $conn->real_escape_string($data["oblig_no"]);
$units = intval($data["units"]);
$description = $conn->real_escape_string($data["description"]);

// Insert into database
$sql = "INSERT INTO equipment (
    equipment_name, category, po_jo_no, property_number, account_code, purchase_date,
    ris_no, oblig_no, units, description
) VALUES (
    '$equipment_name', '$category', '$po_jo_no', '$property_number', '$account_code', '$purchase_date',
    '$ris_no', '$oblig_no', $units, '$description'
)";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $conn->error
    ]);
}

$conn->close();
?>
