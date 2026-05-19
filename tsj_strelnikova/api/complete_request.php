<?php
session_start();
require_once '../core/db.php';
$data = json_decode(file_get_contents('php://input'), true);
$stmt = $pdo->prepare("UPDATE requests SET status='completed', completion_report=?, work_photos=?, completed_at=NOW() WHERE id=?");
$stmt->execute([$data['comment'], $data['photos'], $data['request_id']]);
echo json_encode(['success' => true]);
?>
