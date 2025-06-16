<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Отладочная информация
echo "<!-- Страница входа загружена -->";

// Проверяем, авторизован ли пользователь
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit();
}

$errors = [];
$success = false;

// Проверяем успешную регистрацию
if (isset($_SESSION['registration_success'])) {
    $success = true;
    unset($_SESSION['registration_success']);
}

// Проверяем ошибки входа
if (isset($_SESSION['login_errors'])) {
    $errors = $_SESSION['login_errors'];
    unset($_SESSION['login_errors']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $errors[] = 'Пожалуйста, заполните все поля';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE login = ?");
            $stmt->execute([$login]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Успешная авторизация
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_login'] = $user['login'];
                $_SESSION['user_role'] = $user['role'];

                // Обновляем время последнего входа
                $stmt = $pdo->prepare("UPDATE Users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$user['id']]);

                header('Location: ../index.php');
                exit();
            } else {
                $errors[] = 'Неверный логин или пароль';
            }
        } catch (PDOException $e) {
            $errors[] = 'Ошибка при входе в систему. Пожалуйста, попробуйте позже.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="../css/vhod.css">
</head>
<body>
    <div class="container">
        <div class="form-wrapper">
            <div class="image-side">
                <img src="../img/фото.jpg" alt="Изображение">
            </div>
            <div class="form">
                <h2 class="title">Вход</h2>
                <?php if ($success): ?>
                    <div class="success-message">
                        Регистрация успешно завершена! Теперь вы можете войти.
                    </div>
                <?php endif; ?>
                <?php if (!empty($errors)): ?>
                    <div class="error-message">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form action="process_login.php" method="POST">
                    <input type="text" id="username" name="username" placeholder="Логин" required>
                    <input type="password" id="password" name="password" placeholder="Пароль" required>
                    <button type="submit">Войти</button>
                </form>
                <p class="form-footer">
                    Нет аккаунта? <a href="reg.php">Зарегистрироваться</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>