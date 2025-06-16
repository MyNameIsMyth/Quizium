<?php
require_once 'config.php';

try {
    // Проверяем существование таблицы Categories
    $stmt = $pdo->query("SHOW TABLES LIKE 'Categories'");
    if ($stmt->rowCount() == 0) {
        die("Таблица Categories не существует!");
    }

    // Получаем все категории
    $stmt = $pdo->query("SELECT * FROM Categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Категории в базе данных:</h2>";
    echo "<ul>";
    foreach ($categories as $category) {
        echo "<li>" . htmlspecialchars($category['name']) . " (ID: " . $category['id'] . ")</li>";
    }
    echo "</ul>";

    // Проверяем количество категорий
    echo "<p>Всего категорий: " . count($categories) . "</p>";

} catch (PDOException $e) {
    die("Ошибка: " . $e->getMessage());
}
?> 