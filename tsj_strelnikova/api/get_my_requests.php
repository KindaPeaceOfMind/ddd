<?php
session_start();
require_once '../core/db.php';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$userId = $_SESSION['user_id'] ?? 0;

$sql = "SELECT r.*, c.name as category_name 
        FROM requests r 
        JOIN categories c ON r.category_id = c.id 
        WHERE r.user_id = ? 
        ORDER BY r.created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$requests = $stmt->fetchAll();

$total = $pdo->prepare("SELECT COUNT(*) FROM requests WHERE user_id = ?");
$total->execute([$userId]);
$totalPages = ceil($total->fetchColumn() / $limit);

echo json_encode(['requests' => $requests, 'totalPages' => $totalPages]);
?>
