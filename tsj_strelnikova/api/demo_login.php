<?php
session_start();
require_once '../core/db.php';
$data = json_decode(file_get_contents('php://input'), true);
$demo_users = [
    'tenant' => ['email' => 'tenant@example.com', 'password' => '123', 'full_name' => 'Тестовый жилец', 'role' => 'tenant'],
    'operator' => ['email' => 'operator@example.com', 'password' => '123', 'full_name' => 'Тестовый оператор', 'role' => 'operator'],
    'worker' => ['email' => 'worker@example.com', 'password' => '123', 'full_name' => 'Тестовый рабочий', 'role' => 'worker'],
    'admin' => ['email' => 'admin@example.com', 'password' => '123', 'full_name' => 'Администратор', 'role' => 'admin']
];
$user = $demo_users[$data['role']] ?? null;
if ($user) {
    $_SESSION['user_id'] = 999;
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['role'] = $user['role'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Неверная роль']);
}
?>
