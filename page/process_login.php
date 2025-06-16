<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $errors = [];

    if (empty($username)) {
        $errors[] = 'Введите логин';
    }
    if (empty($password)) {
        $errors[] = 'Введите пароль';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('SELECT * FROM Users WHERE login = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['login'];
                $_SESSION['role'] = $user['role'];
                
                // Обновляем время последнего входа
                $stmt = $pdo->prepare("UPDATE Users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                header('Location: profile.php');
                exit();
            } else {
                $errors[] = 'Неверный логин или пароль';
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $errors[] = 'Ошибка при входе в систему. Пожалуйста, попробуйте позже.';
        }
    }

    if (!empty($errors)) {
        $_SESSION['login_errors'] = $errors;
        header('Location: vhod.php');
        exit();
    }
} else {
    header('Location: vhod.php');
    exit();
}
?> 