<?php
session_start();
require_once '../core/db.php';
$userId = $_SESSION['user_id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM invoices WHERE user_id = ? ORDER BY year DESC, month DESC");
$stmt->execute([$userId]);
echo json_encode($stmt->fetchAll());
?>
