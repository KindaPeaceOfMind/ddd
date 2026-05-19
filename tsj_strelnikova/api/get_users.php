<?php
require_once '../core/db.php';
$stmt = $pdo->query("SELECT id, full_name, email, apartment, role FROM users ORDER BY role, full_name");
echo json_encode($stmt->fetchAll());
?>
