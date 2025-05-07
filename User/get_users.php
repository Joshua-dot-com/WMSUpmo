<?php
header('Content-Type: application/json');

// Database connection
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

// Ensure that the user is logged in and that their user ID is stored in the session
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

// Get logged-in user's ID
$loggedInUserId = $_SESSION['user_id'];

// Pagination and search parameters
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
$offset = ($page - 1) * $limit;
$search = trim($_GET['search'] ?? '');
$searchTerm = '%' . $search . '%';

// Build base query parts
$conditions = [];
$params = [];

// Apply search filters
if (!empty($search)) {
    $conditions[] = "(u.first_name LIKE :search OR u.last_name LIKE :search OR u.email LIKE :search OR u.college LIKE :search)";
    $params[':search'] = $searchTerm;
}

// Add condition to exclude the logged-in user's account
$conditions[] = "u.id != :loggedInUserId";
$params[':loggedInUserId'] = $loggedInUserId;

// Add condition to fetch only users with is_admin = 0
$conditions[] = "u.is_admin = 0";  // This line filters out users with is_admin != 0

// Count matching users
$countSql = "SELECT COUNT(*) FROM users u";
if ($conditions) {
    $countSql .= " WHERE " . implode(' AND ', $conditions);
}
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total_users = (int) $countStmt->fetchColumn();

// Main data query
$dataSql = "
    SELECT 
        u.id, u.first_name, u.last_name, u.email, u.role, u.college, u.admin_role, u.other_services,
        u.created_at, u.status, u.account_state, u.is_admin, u.reviewed_by, u.updated_at,
        u.profile_picture, u.login_attempts, u.last_login, u.is_pending_deletion,
        u.is_blocked, u.block_reason, u.block_until,
        COUNT(ue.id) AS equipment_count
    FROM users u
    LEFT JOIN user_equipment ue ON u.id = ue.user_id AND ue.returned_at IS NULL
";

if ($conditions) {
    $dataSql .= " WHERE " . implode(' AND ', $conditions);
}

$dataSql .= " GROUP BY u.id ORDER BY u.created_at DESC LIMIT :limit OFFSET :offset";

$dataStmt = $pdo->prepare($dataSql);

// Bind parameters
foreach ($params as $key => $value) {
    $dataStmt->bindValue($key, $value, PDO::PARAM_STR);
}
$dataStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$dataStmt->execute();
$users = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

// Return result
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
?>
