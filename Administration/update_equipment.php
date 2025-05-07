<?php
// update_equipment.php
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
error_log("Received data for update_equipment: " . print_r($_POST, true));

// Check if equipment_id is provided and valid
if (!isset($_POST['id']) || empty($_POST['id']) || $_POST['id'] === 'undefined') {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid equipment ID']);
    exit;
}

// Check if equipment_name is provided
if (!isset($_POST['equipment_name']) || empty($_POST['equipment_name'])) {
    echo json_encode(['success' => false, 'message' => 'Equipment name is required']);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    $equipment_id = intval($_POST['id']);
    $equipment_name = $_POST['equipment_name'];
    $category = $_POST['category'] ?? '';
    $po_jo_no = $_POST['po_jo_no'] ?? '';
    $property_number = $_POST['property_number'] ?? '';
    $account_code = $_POST['account_code'] ?? '';
    $purchase_date = $_POST['purchase_date'] ?? null;
    $ris_no = $_POST['ris_no'] ?? '';
    $oblig_no = $_POST['oblig_no'] ?? '';
    $units = isset($_POST['units']) ? intval($_POST['units']) : 1;
    $status = $_POST['status'] ?? 'Active';
    $description = $_POST['description'] ?? '';
    
    // Check if status is changing from Maintenance to Active
    $status_changing_to_active = false;
    
    // Get current equipment status
    $check_stmt = $conn->prepare("SELECT status FROM equipment WHERE id = ?");
    $check_stmt->bind_param("i", $equipment_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $current_status = $row['status'];
        if ($current_status === 'Maintenance' && $status === 'Active') {
            $status_changing_to_active = true;
        }
    }
    
    $check_stmt->close();
    
    // Update equipment data
    $update_stmt = $conn->prepare("
        UPDATE equipment 
        SET 
            equipment_name = ?,
            category = ?,
            po_jo_no = ?,
            property_number = ?,
            account_code = ?,
            purchase_date = ?,
            ris_no = ?,
            oblig_no = ?,
            units = ?,
            status = ?,
            description = ?,
            updated_at = NOW()
        WHERE id = ?
    ");
    
    $update_stmt->bind_param(
        "ssssssssissi", 
        $equipment_name,
        $category,
        $po_jo_no,
        $property_number,
        $account_code,
        $purchase_date,
        $ris_no,
        $oblig_no,
        $units,
        $status,
        $description,
        $equipment_id
    );
    
    $update_result = $update_stmt->execute();
    
    // Check if equipment was found and updated
    if ($update_stmt->affected_rows === 0) {
        // Equipment not found, rollback and return error
        $conn->rollback();
        echo json_encode([
            'success' => false, 
            'message' => 'Equipment with ID ' . $equipment_id . ' not found or no changes were made'
        ]);
        exit;
    }
    
    if ($status_changing_to_active) {
        $complete_stmt = $conn->prepare("
            UPDATE maintenance_requests 
            SET 
                status = 'Completed', 
                resolution_notes = 'Marked as active via equipment edit', 
                updated_at = NOW()
            WHERE equipment_id = ? AND status = 'Pending'
        ");
        
        $complete_stmt->bind_param("i", $equipment_id);
        $complete_stmt->execute();
        $complete_stmt->close();
    }
    
    
    // Log the update in equipment_history table (if you have one)
    if (tableExists($conn, 'equipment_history')) {
        $action_details = "Equipment details updated";
        if ($status_changing_to_active) {
            $action_details .= " and status changed to Active";
        }
        
        $history_stmt = $conn->prepare("
            INSERT INTO equipment_history (
                equipment_id,
                action_type,
                action_details,
                performed_by,
                created_at
            ) VALUES (?, 'Update', ?, 'System', NOW())
        ");
        
        $history_stmt->bind_param("is", $equipment_id, $action_details);
        $history_stmt->execute();
        $history_stmt->close();
    }
    
    // Commit transaction
    $conn->commit();
    
    // Return success response
    echo json_encode([
        'success' => true, 
        'message' => 'Equipment updated successfully',
        'equipment_id' => $equipment_id
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    // Log the error
    error_log("Error in update_equipment.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'message' => 'Error updating equipment: ' . $e->getMessage()
    ]);
}

// Close the connection
if (isset($update_stmt)) $update_stmt->close();
$conn->close();

// Helper function to check if a table exists
function tableExists($connection, $tableName) {
    $result = $connection->query("SHOW TABLES LIKE '$tableName'");
    return $result->num_rows > 0;
}
?>
