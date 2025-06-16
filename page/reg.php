<?php
require_once 'config.php';

// Проверяем, авторизован ли пользователь
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Получаем данные из сессии, если они есть
$errors = $_SESSION['registration_errors'] ?? [];
$formData = $_SESSION['registration_data'] ?? [];

// Очищаем данные сессии
unset($_SESSION['registration_errors']);
unset($_SESSION['registration_data']);

// Проверяем существование таблицы и колонок
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM Users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('email', $columns)) {
        die("Ошибка: В таблице Users отсутствует колонка email. Пожалуйста, выполните SQL-скрипт из файла Baza.sql");
    }
} catch (PDOException $e) {
    die("Ошибка: Таблица Users не существует. Пожалуйста, выполните SQL-скрипт из файла Baza.sql");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm-password'] ?? '';
    $agree = isset($_POST['consent']) ? true : false;

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
                // Устанавливаем флаг успешной регистрации
                $success = true;
                
                // Очищаем все буферы вывода
                if (ob_get_level()) {
                    ob_end_clean();
                }
                
                // Выполняем редирект
                header('Location: vhod.php?registered=true');
                exit();
            } else {
                $errors[] = 'Ошибка при регистрации. Попробуйте позже.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Ошибка базы данных: ' . $e->getMessage();
        }
    }
}

// Если это не POST запрос или есть ошибки, показываем форму
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="../css/reg.css">
</head>
<body>
    <div class="container">
        <div class="form-wrapper">
            <div class="image-side">
                <img src="../img/фото.jpg" alt="Изображение">
            </div>
            <div class="form">
                <h2 class="title">Регистрация</h2>
                <?php if (!empty($errors)): ?>
                    <div class="error-message">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form action="process_registration.php" method="POST">
                    <input type="text" id="username" name="username" placeholder="Логин" value="<?php echo htmlspecialchars($formData['username'] ?? ''); ?>" required>
                    <input type="email" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
                    <input type="password" id="password" name="password" placeholder="Пароль" required>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Подтвердите пароль" required>
                    <div class="checkbox-container">
                        <input type="checkbox" id="consent" name="consent" required>
                        <label for="consent" class="checkbox-label">Я согласен на обработку персональных данных</label>
                    </div>
                    <button type="submit">Зарегистрироваться</button>
                </form>
                <p class="form-footer">
                    Уже есть аккаунт? <a href="vhod.php">Войти</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>