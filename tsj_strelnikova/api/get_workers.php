<?php
require_once '../core/db.php';
$stmt = $pdo->query("SELECT id, full_name, rating, rating_count FROM users WHERE role='worker' ORDER BY rating DESC");
echo json_encode($stmt->fetchAll());
?>
