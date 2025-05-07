<?php
// mark_equipment_active.php
// Database connection
require_once 'db_connect.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON data if content type is application/json
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if (strpos($contentType, 'application/json') !== false) {
    $content = trim(file_get_contents("php://input"));
    $decoded = json_decode($content, true);
    
    if (is_array($decoded)) {
        $_POST = $decoded;
    }
}

// Debug: Log the received data
error_log("Received data for mark_equipment_active: " . print_r($_POST, true));

// Check if equipment_id is provided and valid
if (!isset($_POST['equipment_id']) || empty($_POST['equipment_id']) || $_POST['equipment_id'] === 'undefined') {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid equipment ID']);
    exit;
}

// Check if resolved_by is provided
if (!isset($_POST['resolved_by']) || empty($_POST['resolved_by'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required field: resolved_by']);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    $equipment_id = intval($_POST['equipment_id']);
    $resolution_notes = isset($_POST['resolution_notes']) ? $_POST['resolution_notes'] : '';
    $resolved_by = $_POST['resolved_by'];
    
    // 1. Update equipment status to "Active"
    $update_stmt = $conn->prepare("
        UPDATE equipment 
        SET status = 'Active', updated_at = NOW() 
        WHERE id = ?
    ");
    
    $update_stmt->bind_param("i", $equipment_id);
    $update_result = $update_stmt->execute();
    
    // Check if equipment was found and updated
    if ($update_stmt->affected_rows === 0) {
        // Equipment not found, rollback and return error
        $conn->rollback();
        echo json_encode([
            'success' => false, 
            'message' => 'Equipment with ID ' . $equipment_id . ' not found'
        ]);
        exit;
    }
    
    // 2. Update any pending maintenance requests for this equipment to "Completed"
    $complete_stmt = $conn->prepare("
        UPDATE maintenance_requests 
        SET 
            status = 'Completed', 
            resolution_notes = ?, 
            resolved_by = ?, 
            resolved_at = NOW(),
            updated_at = NOW()
        WHERE equipment_id = ? AND status = 'Pending'
    ");
    
    $complete_stmt->bind_param("ssi", $resolution_notes, $resolved_by, $equipment_id);
    $complete_stmt->execute();
    
    // 3. Log the status change in equipment_history table (if you have one)
    if (tableExists($conn, 'equipment_history')) {
        $history_stmt = $conn->prepare("
            INSERT INTO equipment_history (
                equipment_id,
                action_type,
                action_details,
                performed_by,
                created_at
            ) VALUES (?, 'Status Change', 'Status changed to Active', ?, NOW())
        ");
        
        $history_stmt->bind_param("is", $equipment_id, $resolved_by);
        $history_stmt->execute();
        $history_stmt->close();
    }
    
    // Commit transaction
    $conn->commit();
    
    // Return success response
    echo json_encode([
        'success' => true, 
        'message' => 'Equipment marked as active successfully',
        'equipment_id' => $equipment_id
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    // Log the error
    error_log("Error in mark_equipment_active.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'message' => 'Error marking equipment as active: ' . $e->getMessage()
    ]);
}

// Close the connection
if (isset($update_stmt)) $update_stmt->close();
if (isset($complete_stmt)) $complete_stmt->close();
$conn->close();

// Helper function to check if a table exists
function tableExists($connection, $tableName) {
    $result = $connection->query("SHOW TABLES LIKE '$tableName'");
    return $result->num_rows > 0;
}
?>