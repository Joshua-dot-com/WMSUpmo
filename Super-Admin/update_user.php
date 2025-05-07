<?php
header('Content-Type: application/json');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli("localhost", "root", "", "equipment_database");

    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid user ID."]);
        exit;
    }

    // Determine if user is being blocked
    $is_blocked = isset($_POST['is_blocked']) && $_POST['is_blocked'] == '1' ? 1 : 0;

    $account_state = $is_blocked ? 'Blocked' : 'Active';
    $status = $account_state;

    $block_reason = trim($_POST['block_reason'] ?? '');
    $block_until = trim($_POST['block_until'] ?? '');

    // Normalize to null if empty
    $block_reason = $block_reason === '' ? null : $block_reason;
    $block_until = $block_until === '' ? null : $block_until;

    // Helper function to bind and execute queries with nullable params
    function bindAndExecuteWithNulls($conn, $query, $params) {
        $stmt = $conn->prepare($query);

        $types = '';
        $bindParams = [];

        foreach ($params as $key => $param) {
            if (is_null($param)) {
                $types .= 's'; // still bind as string
                $param = null;
            } elseif (is_int($param)) {
                $types .= 'i';
            } else {
                $types .= 's';
            }
            $bindParams[] = &$params[$key];
        }

        array_unshift($bindParams, $types);
        call_user_func_array([$stmt, 'bind_param'], $bindParams);

        $stmt->execute();
        $stmt->close();
    }

    // Update users table
    bindAndExecuteWithNulls($conn,
        "UPDATE users SET account_state = ?, is_blocked = ?, block_reason = ?, block_until = ? WHERE id = ?",
        [$account_state, $is_blocked, $block_reason, $block_until, $id]
    );

    // Update overview_users table
    bindAndExecuteWithNulls($conn,
        "UPDATE overview_users SET status = ?, is_blocked = ?, block_reason = ?, block_until = ? WHERE id = ?",
        [$status, $is_blocked, $block_reason, $block_until, $id]
    );

    $message = $is_blocked ? "User has been blocked successfully." : "User has been unblocked successfully.";
    echo json_encode(["success" => true, "message" => $message]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}
?>
