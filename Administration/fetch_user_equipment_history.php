<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

// Connect to database
$conn = new mysqli($host, $user, $password, $database);
header('Content-Type: application/json');

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Get optional user_id filter
$user_id = null;
if (isset($_GET['user_id']) && $_GET['user_id'] !== '') {
    $user_id = intval($_GET['user_id']);
}

// Base SQL - Get all equipment that was either returned or transferred away from the user
$sql = "
    SELECT 
        ue.id AS assignment_id,
        ue.assigned_at,
        ue.returned_at,
        ue.notes,
        ue.po_jo_no,
        ue.property_number,
        ue.return_reason,
        ue.return_condition,
        ue.return_notes,

        u.id AS user_id,
        u.first_name,
        u.last_name,
        u.email,
        u.college,

        e.id AS equipment_id,
        e.equipment_name,
        e.category,
        e.status AS equipment_status,
        
        /* Determine the equipment status */
        CASE
            WHEN ue.returned_at IS NOT NULL THEN 'RETURNED'
            ELSE (
                SELECT 
                    CASE 
                        WHEN EXISTS (
                            SELECT 1 FROM equipment_history eh 
                            WHERE eh.equipment_id = e.id 
                            AND eh.action_type = 'transfer' 
                            AND eh.from_user_id = ue.user_id
                        ) THEN 'TRANSFERRED'
                        ELSE 'ACTIVE'
                    END
            )
        END AS equipment_disposition,
        
        /* Get the date when equipment was no longer with the user */
        COALESCE(
            ue.returned_at,
            (SELECT MAX(eh.action_date) 
             FROM equipment_history eh 
             WHERE eh.equipment_id = e.id 
             AND eh.action_type = 'transfer' 
             AND eh.from_user_id = ue.user_id)
        ) AS disposition_date,
        
        /* Get transfer details if applicable */
        (SELECT JSON_OBJECT(
            'to_user_id', eh.to_user_id,
            'action_date', eh.action_date,
            'notes', eh.notes
         )
         FROM equipment_history eh 
         WHERE eh.equipment_id = e.id 
         AND eh.action_type = 'transfer' 
         AND eh.from_user_id = ue.user_id
         ORDER BY eh.action_date DESC
         LIMIT 1
        ) AS transfer_details,
        
        /* Get all history for this equipment */
        (SELECT GROUP_CONCAT(
            JSON_OBJECT(
                'id', eh.id,
                'action_type', eh.action_type,
                'action_date', eh.action_date,
                'from_user_id', eh.from_user_id,
                'to_user_id', eh.to_user_id,
                'condition_status', eh.condition_status,
                'notes', eh.notes
            )
            ORDER BY eh.action_date DESC
            SEPARATOR '||'
        ) FROM equipment_history eh 
        WHERE eh.equipment_id = e.id
        ) AS history_json
    FROM user_equipment ue
    JOIN users u ON ue.user_id = u.id
    JOIN equipment e ON ue.equipment_id = e.id
    WHERE 1=1
";

// Add user filter if provided
if ($user_id !== null) {
    $sql .= " AND ue.user_id = ?";
}

// Only include past equipment (either returned or transferred)
$sql .= " AND (
    ue.returned_at IS NOT NULL 
    OR 
    EXISTS (
        SELECT 1 FROM equipment_history eh 
        WHERE eh.equipment_id = ue.equipment_id 
        AND eh.action_type = 'transfer' 
        AND eh.from_user_id = ue.user_id
    )
)";

$sql .= " ORDER BY COALESCE(ue.returned_at, 
    (SELECT MAX(eh.action_date) FROM equipment_history eh 
     WHERE eh.equipment_id = ue.equipment_id 
     AND eh.action_type = 'transfer' 
     AND eh.from_user_id = ue.user_id)
) DESC";

// Prepare and execute
$stmt = $conn->prepare($sql);
if ($user_id !== null) {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    // Process history JSON
    if (!empty($row['history_json'])) {
        $historyItems = [];
        $historyJsonItems = explode('||', $row['history_json']);
        
        foreach ($historyJsonItems as $jsonItem) {
            $historyItems[] = json_decode($jsonItem, true);
        }
        
        $row['history'] = $historyItems;
        unset($row['history_json']); // Remove the raw JSON string
    } else {
        $row['history'] = [];
    }
    
    // Process transfer details if applicable
    if (!empty($row['transfer_details'])) {
        $row['transfer_details'] = json_decode($row['transfer_details'], true);
        
        // Get the name of the user it was transferred to
        if (!empty($row['transfer_details']['to_user_id'])) {
            $toUserId = $row['transfer_details']['to_user_id'];
            $userSql = "SELECT first_name, last_name FROM users WHERE id = ?";
            $userStmt = $conn->prepare($userSql);
            $userStmt->bind_param("i", $toUserId);
            $userStmt->execute();
            $userResult = $userStmt->get_result();
            
            if ($userRow = $userResult->fetch_assoc()) {
                $row['transfer_details']['to_user_name'] = $userRow['first_name'] . ' ' . $userRow['last_name'];
            }
            
            $userStmt->close();
        }
    }
    
    // Calculate duration of assignment
    if (!empty($row['assigned_at']) && !empty($row['disposition_date'])) {
        $assigned = new DateTime($row['assigned_at']);
        $ended = new DateTime($row['disposition_date']);
        $interval = $assigned->diff($ended);
        
        $row['assignment_duration'] = [
            'days' => $interval->days,
            'formatted' => $interval->format('%a days, %h hours')
        ];
    }
    
    // Add a human-readable status with details
    switch ($row['equipment_disposition']) {
        case 'RETURNED':
            $row['status_description'] = 'Returned on ' . date('M d, Y', strtotime($row['returned_at']));
            if (!empty($row['return_reason'])) {
                $row['status_description'] .= ' (Reason: ' . $row['return_reason'] . ')';
            }
            break;
            
        case 'TRANSFERRED':
            $transferDate = date('M d, Y', strtotime($row['disposition_date']));
            $transferTo = !empty($row['transfer_details']['to_user_name']) ? 
                $row['transfer_details']['to_user_name'] : 'another user';
            
            $row['status_description'] = "Transferred to {$transferTo} on {$transferDate}";
            break;
            
        default:
            $row['status_description'] = 'Unknown status';
    }
    
    $history[] = $row;
}

echo json_encode([
    "success" => true, 
    "data" => $history,
    "count" => count($history),
    "timestamp" => date('Y-m-d H:i:s')
]);

$stmt->close();
$conn->close();
?>