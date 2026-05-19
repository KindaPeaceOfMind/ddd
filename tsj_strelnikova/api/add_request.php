<?php
session_start();
require_once '../core/db.php';
$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'] ?? 0;
$stmt = $pdo->prepare("INSERT INTO requests (user_id, category_id, description, priority, photos) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$userId, $data['category_id'], $data['description'], $data['priority'], $data['photos']]);
echo json_encode(['success' => true]);
?>
