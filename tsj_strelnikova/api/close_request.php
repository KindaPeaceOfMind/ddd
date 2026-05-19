<?php
session_start();
require_once '../core/db.php';
$data = json_decode(file_get_contents('php://input'), true);
$stmt = $pdo->prepare("UPDATE requests SET status='closed', closed_at=NOW() WHERE id=?");
$stmt->execute([$data['id']]);
echo json_encode(['success' => true]);
?>
