<?php
session_start();
require_once '../core/db.php';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="logs_' . date('Y-m-d') . '.xls"');
$logs = $pdo->query("SELECT created_at, user_name, role, action, details FROM action_logs ORDER BY created_at DESC")->fetchAll();
echo "<table><tr><th>Дата</th><th>Пользователь</th><th>Роль</th><th>Действие</th><th>Детали</th></tr>";
foreach($logs as $l) {
    echo "<tr><td>{$l['created_at']}</td><td>{$l['user_name']}</td><td>{$l['role']}</td><td>{$l['action']}</td><td>{$l['details']}</td></tr>";
}
echo "</table>";
?>
