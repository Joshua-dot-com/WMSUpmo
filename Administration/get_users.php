<?php
header('Content-Type: application/json');

// Database connection parameters
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}

// Pagination settings
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
$offset = ($page - 1) * $limit;

// Search term
$search = $_GET['search'] ?? '';
$searchTerm = '%' . $search . '%';

try {
    // Count total users
    if (!empty($search)) {
        $countStmt = $pdo->prepare("
            SELECT COUNT(*) FROM users 
            WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR college LIKE ?
        ");
        $countStmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    } else {
        $countStmt = $pdo->query("SELECT COUNT(*) FROM users");
    }
    $total_users = (int) $countStmt->fetchColumn();

    // Main query with optional search and equipment count
    $query = "
        SELECT 
            u.id, u.first_name, u.last_name, u.email, u.role, u.college, u.admin_role, u.other_services,
            u.created_at, u.status, u.account_state, u.is_admin, u.reviewed_by, u.updated_at,
            u.profile_picture, u.login_attempts, u.last_login, u.is_pending_deletion,
            u.is_blocked, u.block_reason, u.block_until,
            COUNT(ue.id) AS equipment_count
        FROM users u
        LEFT JOIN user_equipment ue ON u.id = ue.user_id AND ue.returned_at IS NULL
    ";

    $conditions = [];
    $params = [];

    if (!empty($search)) {
        $conditions[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR u.college LIKE ?)";
        array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    $query .= " GROUP BY u.id ORDER BY u.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $users,
        'pagination' => [
            'current_page' => $page,
            'items_per_page' => $limit,
            'total_items' => $total_users,
            'total_pages' => ceil($total_users / $limit)
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Query error: ' . $e->getMessage()
    ]);
}
