<?php
header('Content-Type: application/json; charset=utf-8');

// Разрешаем CORS для локальной разработки
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Обработка preflight запроса
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Обрабатываем только POST запросы
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Получаем JSON из тела запроса
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Проверяем наличие необходимых данных
if (!isset($data['locationId']) || !isset($data['selectedOption']) || !isset($data['correctAnswer'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Missing required data',
        'correct' => false
    ]);
    exit();
}

$locationId = (int)$data['locationId'];
$selectedOption = (int)$data['selectedOption'];
$correctAnswer = (int)$data['correctAnswer'];

// Имитация задержки для реалистичности (можно убрать в продакшене)
usleep(300000); // 300ms

// Проверяем правильность ответа
$isCorrect = ($selectedOption === $correctAnswer);

// Формируем ответ
$response = [
    'success' => true,
    'correct' => $isCorrect,
    'locationId' => $locationId,
    'selectedOption' => $selectedOption,
    'correctAnswer' => $correctAnswer,
    'timestamp' => date('Y-m-d H:i:s')
];

// Если ответ правильный, можно добавить дополнительную логику
if ($isCorrect) {
    $response['message'] = 'Правильный ответ! Следующее место разблокировано.';
    
    // Здесь можно добавить сохранение в базу данных или сессию
    // Например: $_SESSION['completed_locations'][] = $locationId;
} else {
    $response['message'] = 'Неверный ответ. Попробуйте ещё раз.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
