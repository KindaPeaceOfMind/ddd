<?php
session_start();
require_once '../core/db.php';
$data = json_decode(file_get_contents('php://input'), true);
$stmt = $pdo->prepare("UPDATE requests SET rating=? WHERE id=?");
$stmt->execute([$data['rating'], $data['request_id']]);
$stmt = $pdo->prepare("UPDATE users SET rating = (rating * rating_count + ?) / (rating_count + 1), rating_count = rating_count + 1 WHERE id = (SELECT worker_id FROM requests WHERE id=?)");
$stmt->execute([$data['rating'], $data['request_id']]);
echo json_encode(['success' => true]);
?>
