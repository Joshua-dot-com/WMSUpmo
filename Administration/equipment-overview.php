<?php
header('Content-Type: application/json');

// DB connection
$conn = new mysqli("localhost", "root", "", "equipment_database");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Helper function to get count
function getCount($conn, $query, $params = []) {
    $stmt = $conn->prepare($query);
    
    // Bind parameters if any
    if (!empty($params)) {
        $stmt->bind_param(...$params);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        return $row ? (int)$row['count'] : 0;
    } else {
        return 0; // Return 0 in case of failure
    }
}

// Date references
$today = date("Y-m-d");
$firstOfThisMonth = date("Y-m-01");
$firstOfLastMonth = date("Y-m-01", strtotime("-1 month"));
$sevenDaysAgo = date("Y-m-d", strtotime("-7 days"));

// Equipment stats
$total = getCount($conn, "SELECT COUNT(*) AS count FROM equipment");

// Available equipment (not assigned to any user)
$available = getCount($conn, "
    SELECT COUNT(*) AS count 
    FROM equipment 
    WHERE id NOT IN (SELECT equipment_id FROM user_equipment WHERE returned_at IS NULL)"
);

$assigned = getCount($conn, "SELECT COUNT(DISTINCT equipment_id) AS count FROM user_equipment WHERE returned_at IS NULL");
$underRepair = getCount($conn, "SELECT COUNT(*) AS count FROM equipment WHERE status = 'Under Repair'");

$thisMonthCount = getCount($conn, "SELECT COUNT(*) AS count FROM equipment WHERE created_at >= ?", ['s', $firstOfThisMonth]);
$lastMonthCount = getCount($conn, "SELECT COUNT(*) AS count FROM equipment WHERE created_at >= ? AND created_at < ?", ['ss', $firstOfLastMonth, $firstOfThisMonth]);

$growth = ($lastMonthCount > 0)
    ? round((($thisMonthCount - $lastMonthCount) / $lastMonthCount) * 100)
    : ($thisMonthCount > 0 ? 100 : 0);

// Transfers
$recentTransfers = getCount($conn, "
    SELECT COUNT(*) AS count 
    FROM equipment_transfers 
    WHERE status = 'approved' AND transfer_date >= ?",
    ['s', $sevenDaysAgo]
);

// Assignments
$currentAssigned = getCount($conn, "
    SELECT COUNT(DISTINCT equipment_id) AS count 
    FROM user_equipment 
    WHERE returned_at IS NULL"
);

// Requests
$pendingRequests = getCount($conn, "
    SELECT COUNT(*) AS count 
    FROM equipment_transfers 
    WHERE status = 'pending'"
);

$approvedToday = getCount($conn, "
    SELECT COUNT(*) AS count 
    FROM equipment_transfers 
    WHERE status = 'approved' AND DATE(approved_at) = ?",
    ['s', $today]
);

$rejectedToday = getCount($conn, "
    SELECT COUNT(*) AS count 
    FROM equipment_transfers 
    WHERE status = 'rejected' AND DATE(rejected_at) = ?",
    ['s', $today]
);

// Final output
$response = [
    "total" => $total,
    "assigned" => $assigned,
    "available" => $available,
    "under_repair" => $underRepair,
    "growth_percentage" => $growth,
    "recent_transfers" => $recentTransfers,
    "current_assigned" => $currentAssigned,
    "pending_requests" => $pendingRequests,
    "approved_requests_today" => $approvedToday,
    "rejected_requests_today" => $rejectedToday
];

echo json_encode($response);
$conn->close();
?>
