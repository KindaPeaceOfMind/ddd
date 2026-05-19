<?php
session_start();
require_once 'core/db.php';
require_once 'core/config.php';
require_once 'core/functions.php';

// Определяем запрашиваемую страницу
$page = $_GET['page'] ?? 'dashboard';
$allowed_pages = ['dashboard', 'tenant', 'operator', 'worker', 'admin', 'auth', 'logout', 'coming_soon'];

// Проверка авторизации
if (!isLoggedIn() && $page !== 'auth') {
    $page = 'auth';
}

// Обработка выхода
if ($page === 'logout') {
    session_destroy();
    redirect('index.php?page=auth');
}

// Подключение модуля
$module_path = "modules/$page.php";
if (file_exists($module_path)) {
    include $module_path;
} else {
    include 'modules/dashboard.php';
}
?>
