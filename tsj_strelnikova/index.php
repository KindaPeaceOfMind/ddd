<?php
session_start();
require_once __DIR__ . '/core/db.php';
require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/core/functions.php';

// Определяем базовый URL проекта
$base_url = dirname($_SERVER['SCRIPT_NAME']) === '/' ? '' : dirname($_SERVER['SCRIPT_NAME']);

// Делаем $base_url доступной внутри подключаемых файлов через глобальную переменную
$GLOBALS['base_url'] = $base_url;

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
    header('Location: ' . $base_url . '/index.php?page=auth');
    exit;
}

// Подключение модуля
$module_path = __DIR__ . "/modules/$page.php";
if (file_exists($module_path)) {
    include $module_path;
} else {
    include __DIR__ . '/modules/dashboard.php';
}
?>
