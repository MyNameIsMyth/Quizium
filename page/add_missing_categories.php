<?php
require_once 'config.php';

$categories = [
    'История',
    'География',
    'Наука',
    'Искусство',
    'Спорт',
    'Кино',
    'Игры',
    'Мифология'
];

try {
    $stmt = $pdo->prepare("INSERT IGNORE INTO Categories (name) VALUES (?)");
    
    echo "<h2>Добавление категорий:</h2>";
    echo "<ul>";
    
    foreach ($categories as $category) {
        $stmt->execute([$category]);
        if ($stmt->rowCount() > 0) {
            echo "<li>Добавлена категория: " . htmlspecialchars($category) . "</li>";
        } else {
            echo "<li>Категория уже существует: " . htmlspecialchars($category) . "</li>";
        }
    }
    
    echo "</ul>";
    
    // Проверяем итоговое количество категорий
    $stmt = $pdo->query("SELECT COUNT(*) FROM Categories");
    $count = $stmt->fetchColumn();
    echo "<p>Всего категорий в базе данных: " . $count . "</p>";
    
} catch (PDOException $e) {
    die("Ошибка: " . $e->getMessage());
}
?> 