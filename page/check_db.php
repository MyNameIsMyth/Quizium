<?php
require_once 'config.php';

try {
    echo "<h2>Проверка базы данных:</h2>";
    
    // Проверяем структуру таблицы Users
    $stmt = $pdo->query("DESCRIBE Users");
    echo "<h3>Структура таблицы Users:</h3>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";
    
    // Проверяем существующих пользователей
    $stmt = $pdo->query("SELECT id, login, email, role FROM Users");
    echo "<h3>Существующие пользователи:</h3>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";
    
    // Пробуем найти пользователя admin
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE login = ?");
    $stmt->execute(['admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h3>Тестовый вход для admin:</h3>";
    if ($user) {
        echo "Пользователь найден:<br>";
        echo "ID: " . $user['id'] . "<br>";
        echo "Login: " . $user['login'] . "<br>";
        echo "Email: " . $user['email'] . "<br>";
        echo "Role: " . $user['role'] . "<br>";
        
        // Проверяем пароль
        $test_password = 'password123';
        if (password_verify($test_password, $user['password'])) {
            echo "<p style='color: green;'>Пароль верный!</p>";
        } else {
            echo "<p style='color: red;'>Пароль неверный!</p>";
        }
    } else {
        echo "<p style='color: red;'>Пользователь admin не найден!</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Ошибка: " . $e->getMessage() . "</p>";
}
?> 