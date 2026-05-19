<?php
session_start();
require_once '../core/db.php';
$data = json_decode(file_get_contents('php://input'), true);
$stmt = $pdo->prepare("UPDATE requests SET status='assigned', worker_id=? WHERE id=?");
$stmt->execute([$data['worker_id'], $data['request_id']]);
echo json_encode(['success' => true]);
?>
