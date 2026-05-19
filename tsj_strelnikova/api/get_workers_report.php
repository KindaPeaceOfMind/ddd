<?php
require_once '../core/db.php';
$report = $pdo->query("SELECT u.full_name, COUNT(r.id) as completed, AVG(r.rating) as avg_rating 
    FROM users u 
    LEFT JOIN requests r ON u.id = r.worker_id AND r.status IN ('completed','closed') 
    WHERE u.role='worker' 
    GROUP BY u.id")->fetchAll();
$text = "Отчёт по рабочим:\n";
foreach($report as $w) {
    $text .= "- {$w['full_name']}: выполнено {$w['completed']}, ср.рейтинг " . round($w['avg_rating'], 2) . "\n";
}
echo json_encode(['text' => $text]);
?>
