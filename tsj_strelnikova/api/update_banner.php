<?php
session_start();
require_once '../core/db.php';
$data = json_decode(file_get_contents('php://input'), true);
if ($data['active']) {
    $stmt = $pdo->prepare("UPDATE banners SET active=0");
    $stmt->execute();
    $stmt = $pdo->prepare("INSERT INTO banners (title, message, photo, active) VALUES (?, ?, ?, 1) ON DUPLICATE KEY UPDATE title=?, message=?, photo=?, active=1");
    $stmt->execute([$data['title'], $data['message'], $data['photo'], $data['title'], $data['message'], $data['photo']]);
} else {
    $stmt = $pdo->prepare("UPDATE banners SET active=0");
    $stmt->execute();
}
echo json_encode(['success' => true]);
?>
