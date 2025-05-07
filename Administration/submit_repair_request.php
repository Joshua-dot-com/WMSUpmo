<?php
// Database connection
require_once 'db_connect.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Decode JSON body if needed
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if (strpos($contentType, 'application/json') !== false) {
    $content = trim(file_get_contents("php://input"));
    $decoded = json_decode($content, true);
    if (is_array($decoded)) {
        $_POST = $decoded;
    }
}

// Validate equipment_id
if (!isset($_POST['equipment_id']) || empty($_POST['equipment_id']) || $_POST['equipment_id'] === 'undefined') {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid equipment ID']);
    exit;
}

// Validate required fields
$required_fields = ['issue_type', 'priority', 'issue_description', 'reported_by'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

try {
    // Start transaction
    $conn->begin_transaction();

    // 1. Insert into maintenance_requests
    $stmt = $conn->prepare("
        INSERT INTO maintenance_requests (
            equipment_id,
            issue_type,
            priority,
            issue_description,
            reported_by,
            expected_completion_date,
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())
    ");

    $equipment_id       = intval($_POST['equipment_id']);
    $issue_type         = trim($_POST['issue_type']);
    $priority           = trim($_POST['priority']);
    $issue_description  = trim($_POST['issue_description']);
    $reported_by        = trim($_POST['reported_by']);
    $expected_completion = (!empty($_POST['expected_completion']))
                          ? trim($_POST['expected_completion'])
                          : null;

    $stmt->bind_param(
        "isssss",
        $equipment_id,
        $issue_type,
        $priority,
        $issue_description,
        $reported_by,
        $expected_completion
    );
    $stmt->execute();
    $request_id = $conn->insert_id;

    // 2. Update equipment status to "Under Repair"
    $update_stmt = $conn->prepare("
        UPDATE equipment
        SET status = 'Under Repair',
            updated_at = NOW()
        WHERE id = ?
    ");
    $update_stmt->bind_param("i", $equipment_id);
    $update_stmt->execute();

    // If no rows were updated, rollback
    if ($update_stmt->affected_rows === 0) {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'Equipment with ID ' . $equipment_id . ' not found'
        ]);
        exit;
    }

    // Commit transaction
    $conn->commit();

    // Return success
    echo json_encode([
        'success'      => true,
        'message'      => 'Maintenance request submitted and equipment set to Under Repair',
        'request_id'   => $request_id,
        'equipment_id'=> $equipment_id
    ]);

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    error_log("Error in submit_repair_request.php: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Error submitting maintenance request: ' . $e->getMessage()
    ]);
}

// Clean up
if (isset($stmt))        $stmt->close();
if (isset($update_stmt)) $update_stmt->close();
$conn->close();
?>
