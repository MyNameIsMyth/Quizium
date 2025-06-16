<?php
$host = 'localhost';
$dbname = 'quiz_app';
$db_username = 'root';
$db_password = 'Ivasik2006';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Проверка базы данных:</h2>";
    
    // Проверяем существование таблицы Users
    $stmt = $pdo->query("SHOW TABLES LIKE 'Users'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>Таблица Users не существует!</p>";
        echo "<p>Нужно создать таблицу заново.</p>";
    } else {
        echo "<p style='color: green;'>Таблица Users существует.</p>";
        
        // Проверяем структуру таблицы
        $stmt = $pdo->query("DESCRIBE Users");
        echo "<h3>Структура таблицы Users:</h3>";
        echo "<pre>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
        echo "</pre>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Ошибка подключения к базе данных: " . $e->getMessage() . "</p>";
}
?> 