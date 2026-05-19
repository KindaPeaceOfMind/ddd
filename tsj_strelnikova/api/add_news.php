<?php
session_start();
require_once '../core/db.php';
$data = json_decode(file_get_contents('php://input'), true);
$stmt = $pdo->prepare("INSERT INTO news (title, content, published_at) VALUES (?, ?, NOW())");
$stmt->execute([$data['title'], $data['content']]);
echo json_encode(['success' => true]);
?>
