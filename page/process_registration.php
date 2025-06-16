<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm-password'] ?? '';
    $agree = isset($_POST['consent']) ? true : false;

    $errors = [];

    // Валидация
    if (empty($login)) {
        $errors[] = 'Логин обязателен';
    } elseif (strlen($login) < 3) {
        $errors[] = 'Логин должен быть не менее 3 символов';
    }

    if (empty($email)) {
        $errors[] = 'Email обязателен';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный email';
    }

    if (empty($password)) {
        $errors[] = 'Пароль обязателен';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Пароль должен быть не менее 6 символов';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Пароли не совпадают';
    }

    if (!$agree) {
        $errors[] = 'Вы должны согласиться на обработку персональных данных';
    }

    // Проверка уникальности
    if (empty($errors)) {
        try {
            // Проверяем логин
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE login = ?");
            $stmt->execute([$login]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'Пользователь с таким логином уже существует';
            }

            // Проверяем email
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'Пользователь с таким email уже существует';
            }
        } catch (PDOException $e) {
            $errors[] = 'Ошибка при проверке данных: ' . $e->getMessage();
        }
    }

    // Регистрация
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO Users (login, email, password, role) VALUES (?, ?, ?, 'user')");
            
            if ($stmt->execute([$login, $email, $hashed_password])) {
                // Успешная регистрация
                $_SESSION['registration_success'] = true;
                header('Location: vhod.php');
                exit();
            } else {
                $errors[] = 'Ошибка при регистрации. Попробуйте позже.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Ошибка базы данных: ' . $e->getMessage();
        }
    }

    // Если есть ошибки, сохраняем их в сессии и возвращаем на форму
    if (!empty($errors)) {
        $_SESSION['registration_errors'] = $errors;
        $_SESSION['registration_data'] = [
            'username' => $login,
            'email' => $email
        ];
        header('Location: reg.php');
        exit();
    }
} else {
    // Если это не POST запрос, перенаправляем на форму регистрации
    header('Location: reg.php');
    exit();
}
?> 