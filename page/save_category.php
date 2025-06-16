<?php
session_start();
require_once 'config.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    error_log("Unauthorized access attempt");
    http_response_code(401);
    echo json_encode(['error' => 'Необходима авторизация']);
    exit;
}

// Получаем данные из POST-запроса
$raw_data = file_get_contents('php://input');
error_log("Raw POST data: " . $raw_data);

$data = json_decode($raw_data, true);
error_log("Decoded data: " . print_r($data, true));

if (!$data || !isset($data['category_name']) || !isset($data['questions'])) {
    error_log("Invalid data received: " . print_r($data, true));
    http_response_code(400);
    echo json_encode(['error' => 'Неверные данные']);
    exit;
}

try {
    $pdo->beginTransaction();
    error_log("Starting transaction for category: " . $data['category_name']);

    // Проверяем, существует ли категория
    $stmt = $pdo->prepare("SELECT id FROM Categories WHERE name = ?");
    $stmt->execute([$data['category_name']]);
    if ($stmt->fetch()) {
        error_log("Category already exists: " . $data['category_name']);
        throw new Exception('Категория с таким названием уже существует');
    }

    // Создаем новую категорию
    $stmt = $pdo->prepare("INSERT INTO Categories (name) VALUES (?)");
    $stmt->execute([$data['category_name']]);
    $category_id = $pdo->lastInsertId();
    error_log("Created new category with ID: " . $category_id);

    // Добавляем вопросы
    $stmt = $pdo->prepare("
        INSERT INTO Quiz (user_id, category_id, quest, first_wrong_answer, second_wrong_answer, third_wrong_answer, correct_answer)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $question_count = 0;
    foreach ($data['questions'] as $question) {
        try {
            $stmt->execute([
                $_SESSION['user_id'],
                $category_id,
                $question['quest'],
                $question['wrong_answers'][0],
                $question['wrong_answers'][1],
                $question['wrong_answers'][2],
                $question['correct_answer']
            ]);
            $question_count++;
            error_log("Added question {$question_count} to category {$category_id}: " . $question['quest']);
        } catch (PDOException $e) {
            error_log("Error adding question: " . $e->getMessage());
            throw new Exception('Ошибка при добавлении вопроса: ' . $e->getMessage());
        }
    }

    // Проверяем, что все вопросы были добавлены
    $verify_stmt = $pdo->prepare("SELECT COUNT(*) FROM Quiz WHERE category_id = ?");
    $verify_stmt->execute([$category_id]);
    $actual_count = $verify_stmt->fetchColumn();
    
    if ($actual_count !== $question_count) {
        throw new Exception("Ошибка: не все вопросы были добавлены");
    }

    $pdo->commit();
    error_log("Successfully saved category {$category_id} with {$question_count} questions");
    
    // Получаем информацию о созданной категории
    $category_info = $pdo->prepare("
        SELECT c.*, COUNT(q.id) as question_count 
        FROM Categories c 
        LEFT JOIN Quiz q ON c.id = q.category_id 
        WHERE c.id = ?
        GROUP BY c.id
    ");
    $category_info->execute([$category_id]);
    $category_data = $category_info->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Категория "' . htmlspecialchars($data['category_name']) . '" успешно создана!',
        'category_id' => $category_id,
        'category_name' => $data['category_name'],
        'questions_count' => $question_count,
        'category_data' => $category_data
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error saving category: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 