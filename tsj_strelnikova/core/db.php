<?php
$host = 'localhost';
$dbname = 'tsj_strelnikova';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Логирование ошибки в файл вместо вывода на экран (безопасность)
    error_log("DB Connection Error: " . $e->getMessage());
    if (DEBUG_MODE) {
        die("Ошибка подключения к БД. Проверьте логи.");
    } else {
        die("Сервис временно недоступен. Попробуйте позже.");
    }
}
?>
