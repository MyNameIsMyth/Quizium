<?php
$host = 'localhost';
$dbname = 'quiz_app';
$db_username = 'root';
$db_password = 'Ivasik2006';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Исправление базы данных:</h2>";
    
    // Удаляем существующую таблицу Users если она существует
    $pdo->exec("DROP TABLE IF EXISTS Users");
    echo "<p style='color: green;'>Старая таблица Users удалена (если существовала)</p>";
    
    // Создаем таблицу Users заново
    $pdo->exec("CREATE TABLE Users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        login VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        points INT DEFAULT 0,
        role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p style='color: green;'>Таблица Users создана заново</p>";
    
    // Добавляем тестовых пользователей
    $pdo->exec("INSERT INTO Users (login, email, password, role) VALUES
        ('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
        ('user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user')");
    echo "<p style='color: green;'>Тестовые пользователи добавлены</p>";
    
    // Проверяем структуру таблицы
    $stmt = $pdo->query("DESCRIBE Users");
    echo "<h3>Структура таблицы Users:</h3>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";
    
    echo "<p style='color: green;'>База данных успешно исправлена! Теперь вы можете <a href='reg.php'>вернуться к регистрации</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Ошибка: " . $e->getMessage() . "</p>";
}
?> 