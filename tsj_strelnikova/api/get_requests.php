<?php
require_once '../core/db.php';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT r.*, u.full_name as tenant_name, c.name as category_name 
        FROM requests r 
        JOIN users u ON r.user_id = u.id 
        JOIN categories c ON r.category_id = c.id 
        WHERE 1";

$params = [];
if ($status) {
    $sql .= " AND r.status = ?";
    $params[] = $status;
}
if ($search) {
    $sql .= " AND (r.description LIKE ? OR u.full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY r.created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();

$total = $pdo->query("SELECT COUNT(*) FROM requests")->fetchColumn();
$totalPages = ceil($total / $limit);

echo json_encode(['requests' => $requests, 'totalPages' => $totalPages]);
?>
