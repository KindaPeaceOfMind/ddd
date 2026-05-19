<?php
session_start();
require_once '../core/db.php';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$workerId = $_SESSION['user_id'] ?? 0;

$sql = "SELECT r.*, c.name as category_name 
        FROM requests r 
        JOIN categories c ON r.category_id = c.id 
        WHERE r.worker_id = ? AND r.status IN ('assigned','in_progress') 
        ORDER BY r.created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute([$workerId]);
$tasks = $stmt->fetchAll();

$total = $pdo->prepare("SELECT COUNT(*) FROM requests WHERE worker_id = ? AND status IN ('assigned','in_progress')");
$total->execute([$workerId]);
$totalPages = ceil($total->fetchColumn() / $limit);

echo json_encode(['tasks' => $tasks, 'totalPages' => $totalPages]);
?>
