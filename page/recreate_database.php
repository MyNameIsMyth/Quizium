<?php
$host = 'localhost';
$db_username = 'root';
$db_password = 'Ivasik2006';

try {
    // Создаем подключение без выбора базы данных
    $pdo = new PDO("mysql:host=$host", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Удаляем базу данных, если она существует
    $pdo->exec("DROP DATABASE IF EXISTS quiz_app");
    echo "База данных удалена<br>";

    // Создаем базу данных заново
    $pdo->exec("CREATE DATABASE quiz_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "База данных создана<br>";

    // Выбираем базу данных
    $pdo->exec("USE quiz_app");

    // Создаем таблицу категорий
    $pdo->exec("CREATE TABLE Categories (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL UNIQUE
    )");
    echo "Таблица Categories создана<br>";

    // Создаем таблицу пользователей
    $pdo->exec("CREATE TABLE Users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        login VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        points INT DEFAULT 0,
        role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Таблица Users создана<br>";

    // Создаем таблицу вопросов
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
    echo "Таблица Quiz создана<br>";

    // Добавляем категории
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

    $stmt = $pdo->prepare("INSERT INTO Categories (name) VALUES (?)");
    foreach ($categories as $category) {
        $stmt->execute([$category]);
        echo "Добавлена категория: " . htmlspecialchars($category) . "<br>";
    }

    // Добавляем тестовых пользователей
    $pdo->exec("INSERT INTO Users (login, email, password, role) VALUES
        ('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
        ('user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user')");
    echo "Добавлены тестовые пользователи<br>";

    // Добавляем примеры вопросов
    $pdo->exec("INSERT INTO Quiz (user_id, category_id, quest, first_wrong_answer, second_wrong_answer, third_wrong_answer, correct_answer) VALUES
        (1, 1, 'В каком году началась Вторая мировая война?', '1935', '1941', '1914', '1939'),
        (1, 2, 'Какая самая длинная река в мире?', 'Нил', 'Янцзы', 'Миссисипи', 'Амазонка'),
        (2, 3, 'Кто открыл закон всемирного тяготения?', 'Эйнштейн', 'Галилей', 'Тесла', 'Ньютон')");
    echo "Добавлены примеры вопросов<br>";

    echo "<br>База данных успешно пересоздана!";

} catch (PDOException $e) {
    die("Ошибка: " . $e->getMessage());
}
?> 