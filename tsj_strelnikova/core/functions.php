<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function addLog($userId, $userName, $role, $action, $details = '') {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO action_logs (user_id, user_name, role, action, details) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $userName, $role, $action, $details]);
}

function getUserById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getCategories() {
    global $pdo;
    return $pdo->query("SELECT * FROM categories")->fetchAll();
}

function getStatusBadge($status) {
    $badges = [
        'new' => '<span class="status-badge status-new">Новая</span>',
        'assigned' => '<span class="status-badge status-assigned">Назначена</span>',
        'in_progress' => '<span class="status-badge status-assigned">В работе</span>',
        'completed' => '<span class="status-badge status-completed">Выполнена</span>',
        'closed' => '<span class="status-badge status-closed">Закрыта</span>',
        'cancelled' => '<span class="status-badge status-cancelled">Отменена</span>'
    ];
    return $badges[$status] ?? $status;
}
?>
