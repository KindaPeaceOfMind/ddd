<?php
require_once '../core/db.php';
$data = json_decode(file_get_contents('php://input'), true);
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$data['email']]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Email уже существует']);
    exit;
}
$stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, apartment, role) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$data['full_name'], $data['email'], $data['password'], $data['apartment'], $data['role']]);
echo json_encode(['success' => true]);
?>
