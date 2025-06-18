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
    // Проверяем, проходил ли пользователь уже эту категорию
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM QuizResults WHERE user_id = ? AND category_id = ?");
    $check_stmt->execute([$_SESSION['user_id'], $data['category_id']]);
    $already_passed = $check_stmt->fetchColumn() > 0;

    // Если уже проходил, очки не начисляем
    $score = $already_passed ? 0 : $data['score'];
    $correct_answers = $already_passed ? 0 : $data['correct_answers'];
    $total_questions = $already_passed ? 0 : $data['total_questions'];

    // Сохраняем результаты в базу данных
    $stmt = $pdo->prepare("
        INSERT INTO QuizResults (user_id, category_id, score, correct_answers, total_questions, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $data['category_id'],
        $score,
        $correct_answers,
        $total_questions
    ]);

    echo json_encode(['success' => true, 'already_passed' => $already_passed]);
} catch (PDOException $e) {
    error_log("Ошибка при сохранении результатов: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?> 