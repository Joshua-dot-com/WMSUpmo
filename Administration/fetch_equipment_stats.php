<?php
// Include database connection
require_once 'db_connect.php';

header('Content-Type: application/json');

try {
    // Get total equipment count
    $totalStmt = $pdo->query("SELECT COUNT(*) as count FROM equipment");
    $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get available equipment count (not assigned)
    $availableStmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM equipment e
        WHERE NOT EXISTS (
            SELECT 1 FROM equipment_assignments ea 
            WHERE ea.equipment_id = e.id AND ea.returned_at IS NULL
        )
        AND e.status = 'Active'
    ");
    $available = $availableStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get assigned equipment count
    $assignedStmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM equipment_assignments
        WHERE returned_at IS NULL
    ");
    $assigned = $assignedStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get total history count
    $historyStmt = $pdo->query("SELECT COUNT(*) as count FROM equipment_assignments");
    $history = $historyStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total' => $total,
            'available' => $available,
            'assigned' => $assigned,
            'history' => $history
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>