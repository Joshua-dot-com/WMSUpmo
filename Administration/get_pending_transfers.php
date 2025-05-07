<?php
    // Database connection
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "equipment_database";

    $conn = new mysqli($host, $user, $password, $database);
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    header('Content-Type: application/json');

    // Input handling
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    $statusFilter = $_GET['status'] ?? 'Pending';

    // Main query with JOINs for owner names and equipment details
    $sql = "
        SELECT et.id, e.po_jo_no AS equipment_id, e.equipment_name AS equipment_type, 
            et.reason, et.transfer_date, et.status,
            et.approved_at, et.rejected_at, et.rejection_reason,
            CONCAT(u1.first_name, ' ', u1.last_name) AS from_user,
            CONCAT(u2.first_name, ' ', u2.last_name) AS to_user
        FROM equipment_transfers et
        JOIN users u1 ON et.current_owner_id = u1.id
        JOIN users u2 ON et.new_owner_id = u2.id
        JOIN equipment e ON et.equipment_id = e.id
        WHERE et.status = ?
        ORDER BY et.transfer_date DESC
        LIMIT ? OFFSET ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $statusFilter, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $transfers = [];
    while ($row = $result->fetch_assoc()) {
        $transfers[] = $row;
    }

    // Pagination logic
    $countSql = "SELECT COUNT(*) as total FROM equipment_transfers WHERE status = ?";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param("s", $statusFilter);
    $countStmt->execute();
    $countResult = $countStmt->get_result()->fetch_assoc();

    $totalRecords = $countResult['total'];
    $totalPages = ceil($totalRecords / $limit);

    // Return JSON response
    echo json_encode([
        'success' => true,
        'transfers' => $transfers,
        'pagination' => [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'limit' => $limit
        ]
    ]);

    $conn->close();
    ?>
