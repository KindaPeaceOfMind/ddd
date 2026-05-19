<?php
session_start();
header('Content-Type: application/json');
require_once '../core/db.php';
require_once '../core/functions.php';

// Функция для отправки JSON ответа
function jsonResponse($success, $message = '', $data = [], $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Проверка авторизации
if (!isLoggedIn()) {
    jsonResponse(false, 'Пользователь не авторизован', [], 401);
}

try {
    // Чтение и декодирование JSON
    $input = file_get_contents('php://input');
    if (empty($input)) {
        throw new Exception('Нет данных запроса');
    }
    
    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Неверный формат JSON');
    }

    // Валидация обязательных полей
    if (empty($data['category_id'])) {
        throw new Exception('Не выбрана категория заявки');
    }
    if (empty($data['description']) || strlen(trim($data['description'])) < 5) {
        throw new Exception('Описание проблемы слишком короткое (минимум 5 символов)');
    }
    if (empty($data['priority'])) {
        $data['priority'] = 'medium'; // Значение по умолчанию
    }

    // Подготовка данных
    $userId = $_SESSION['user_id'];
    $categoryId = (int)$data['category_id'];
    $description = trim($data['description']);
    $priority = in_array($data['priority'], ['low', 'medium', 'high']) ? $data['priority'] : 'medium';
    $photos = isset($data['photos']) ? $data['photos'] : '[]';

    // Вставка в базу данных
    $stmt = $pdo->prepare("
        INSERT INTO requests (tenant_id, category_id, description, priority, photos, status, created_at) 
        VALUES (?, ?, ?, ?, ?, 'new', NOW())
    ");
    
    if (!$stmt->execute([$userId, $categoryId, $description, $priority, $photos])) {
        throw new Exception('Ошибка выполнения SQL-запроса');
    }

    $newId = $pdo->lastInsertId();

    // Логирование действия
    addLog($userId, $_SESSION['user_name'], $_SESSION['role'], 'Создал заявку #' . $newId, mb_substr($description, 0, 50));

    jsonResponse(true, 'Заявка успешно создана!', ['id' => $newId]);

} catch (Exception $e) {
    // Логирование ошибки сервера (можно писать в файл логов)
    error_log("Ошибка создания заявки: " . $e->getMessage());
    
    // Отправляем понятное сообщение клиенту
    jsonResponse(false, $e->getMessage(), [], 400);
}
?>
