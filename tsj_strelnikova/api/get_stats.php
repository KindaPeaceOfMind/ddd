<?php
require_once '../core/db.php';
$stats = $pdo->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='new' THEN 1 ELSE 0 END) as new,
    SUM(CASE WHEN status='assigned' THEN 1 ELSE 0 END) as assigned,
    SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status='closed' THEN 1 ELSE 0 END) as closed,
    AVG(rating) as avgRating
FROM requests")->fetch();
echo json_encode($stats);
?>
