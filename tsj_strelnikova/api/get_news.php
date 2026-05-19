<?php
require_once '../core/db.php';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['admin']) && $_GET['admin'] === 'true' ? 10 : 5;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("SELECT * FROM news ORDER BY published_at DESC LIMIT $limit OFFSET $offset");
$stmt->execute();
$news = $stmt->fetchAll();

$total = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$totalPages = ceil($total / $limit);

echo json_encode(['news' => $news, 'totalPages' => $totalPages]);
?>
