<?php
require_once 'config.php';
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Получаем данные из POST-запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

try {
    // Сохраняем результаты в базу данных
    $stmt = $pdo->prepare("
        INSERT INTO QuizResults (user_id, category_id, score, correct_answers, total_questions, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $_SESSION['user_id'],
        $data['category_id'],
        $data['score'],
        $data['correct_answers'],
        $data['total_questions']
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log("Ошибка при сохранении результатов: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?> 