<?php
require_once 'config.php';

try {
    // Проверяем структуру таблицы
    echo "<h3>Структура таблицы Categories:</h3>";
    $stmt = $pdo->query("DESCRIBE Categories");
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";

    // Проверяем содержимое таблицы
    echo "<h3>Содержимое таблицы Categories:</h3>";
    $stmt = $pdo->query("SELECT * FROM Categories ORDER BY id");
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Название</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Проверяем количество записей
    $stmt = $pdo->query("SELECT COUNT(*) FROM Categories");
    $count = $stmt->fetchColumn();
    echo "<p>Всего категорий в базе данных: " . $count . "</p>";

} catch (PDOException $e) {
    die("Ошибка: " . $e->getMessage());
}
?> 