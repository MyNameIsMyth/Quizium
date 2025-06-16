<?php
$host = 'localhost';
$db_username = 'root';
$db_password = 'Ivasik2006';

try {
    // Подключаемся к MySQL без выбора базы данных
    $pdo = new PDO("mysql:host=$host", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Настройка базы данных:</h2>";
    
    // Создаем базу данных, если она не существует
    $pdo->exec("CREATE DATABASE IF NOT EXISTS quiz_app");
    echo "<p style='color: green;'>База данных quiz_app создана или уже существует</p>";
    
    // Выбираем базу данных
    $pdo->exec("USE quiz_app");
    
    // Отключаем проверку внешних ключей
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Удаляем все существующие таблицы
    $pdo->exec("DROP TABLE IF EXISTS collect_quiz");
    $pdo->exec("DROP TABLE IF EXISTS Quiz");
    $pdo->exec("DROP TABLE IF EXISTS Users");
    $pdo->exec("DROP TABLE IF EXISTS Categories");
    echo "<p style='color: green;'>Старые таблицы удалены (если существовали)</p>";
    
    // Включаем проверку внешних ключей
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // Создаем таблицу Categories
    $pdo->exec("CREATE TABLE Categories (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL UNIQUE
    )");
    echo "<p style='color: green;'>Таблица Categories создана</p>";
    
    // Создаем таблицу Users
    $pdo->exec("CREATE TABLE Users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        login VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        points INT DEFAULT 0,
        role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL
    )");
    echo "<p style='color: green;'>Таблица Users создана</p>";
    
    // Создаем таблицу Quiz
    $pdo->exec("CREATE TABLE Quiz (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        category_id INT,
        quest TEXT NOT NULL,
        first_wrong_answer VARCHAR(255) NOT NULL,
        second_wrong_answer VARCHAR(255) NOT NULL,
        third_wrong_answer VARCHAR(255) NOT NULL,
        correct_answer VARCHAR(255) NOT NULL,
        points INT NOT NULL DEFAULT 10,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES Users(id),
        FOREIGN KEY (category_id) REFERENCES Categories(id)
    )");
    echo "<p style='color: green;'>Таблица Quiz создана</p>";
    
    // Добавляем начальные данные
    $pdo->exec("INSERT INTO Categories (name) VALUES
        ('История'),
        ('География'),
        ('Наука'),
        ('Искусство'),
        ('Спорт')");
    echo "<p style='color: green;'>Категории добавлены</p>";
    
    $pdo->exec("INSERT INTO Users (login, email, password, role) VALUES
        ('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
        ('user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user')");
    echo "<p style='color: green;'>Тестовые пользователи добавлены</p>";
    
    $pdo->exec("INSERT INTO Quiz (user_id, category_id, quest, first_wrong_answer, second_wrong_answer, third_wrong_answer, correct_answer) VALUES
        (1, 1, 'В каком году началась Вторая мировая война?', '1935', '1941', '1914', '1939'),
        (1, 2, 'Какая самая длинная река в мире?', 'Нил', 'Янцзы', 'Миссисипи', 'Амазонка'),
        (2, 3, 'Кто открыл закон всемирного тяготения?', 'Эйнштейн', 'Галилей', 'Тесла', 'Ньютон')");
    echo "<p style='color: green;'>Тестовые вопросы добавлены</p>";
    
    // Проверяем структуру таблицы Users
    $stmt = $pdo->query("DESCRIBE Users");
    echo "<h3>Структура таблицы Users:</h3>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";
    
    echo "<p style='color: green; font-size: 18px;'>База данных успешно настроена! Теперь вы можете <a href='reg.php'>вернуться к регистрации</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Ошибка: " . $e->getMessage() . "</p>";
}
?> 