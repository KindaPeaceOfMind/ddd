<?php
require_once '../core/db.php';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$role = $_GET['role'] ?? '';

$sql = "SELECT * FROM action_logs WHERE 1";
$params = [];

if ($search) {
    $sql .= " AND (action LIKE ? OR details LIKE ? OR user_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($role) {
    $sql .= " AND role = ?";
    $params[] = $role;
}

$sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

$countSql = "SELECT COUNT(*) FROM action_logs WHERE 1";
if ($search || $role) {
    $countSql .= " AND (" . ($search ? "action LIKE '%{$search}%' OR details LIKE '%{$search}%' OR user_name LIKE '%{$search}%'" : "") . ($search && $role ? " AND " : "") . ($role ? "role = '$role'" : "") . ")";
}
$total = $pdo->query($countSql)->fetchColumn();
$totalPages = ceil($total / $limit);

echo json_encode(['logs' => $logs, 'totalPages' => $totalPages]);
?>
