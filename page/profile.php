<?php
require_once 'config.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: vhod.php');
    exit;
}

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT * FROM Users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Получаем статистику пользователя (количество созданных вопросов с существующей категорией)
$questions_stmt = $pdo->prepare("SELECT COUNT(*) as question_count FROM Quiz q INNER JOIN Categories c ON q.category_id = c.id WHERE q.user_id = ?");
$questions_stmt->execute([$_SESSION['user_id']]);
$questions_count = $questions_stmt->fetch()['question_count'];

// Получаем все вопросы пользователя, у которых категория существует
$all_questions_stmt = $pdo->prepare("SELECT q.* FROM Quiz q INNER JOIN Categories c ON q.category_id = c.id WHERE q.user_id = ? ORDER BY q.id DESC");
$all_questions_stmt->execute([$_SESSION['user_id']]);
$all_questions = $all_questions_stmt->fetchAll();

// Получаем категории пользователя
$stmt = $pdo->prepare("
    SELECT DISTINCT c.id, c.name, COUNT(q.id) as question_count
    FROM Categories c
    JOIN Quiz q ON c.id = q.category_id
    WHERE q.user_id = ?
    GROUP BY c.id, c.name
");
$stmt->execute([$user['id']]);
$user_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем сумму очков пользователя по всем прохождениям (по существующим категориям)
$points_stmt = $pdo->prepare("SELECT COALESCE(SUM(qr.score), 0) as total_points FROM QuizResults qr INNER JOIN Categories c ON qr.category_id = c.id WHERE qr.user_id = ?");
$points_stmt->execute([$_SESSION['user_id']]);
$total_points = $points_stmt->fetch()['total_points'];

// Получаем список пройденных пользователем категорий (только существующие)
$passed_categories_stmt = $pdo->prepare("
    SELECT DISTINCT c.id, c.name
    FROM QuizResults qr
    INNER JOIN Categories c ON qr.category_id = c.id
    WHERE qr.user_id = ?
    ORDER BY c.name
");
$passed_categories_stmt->execute([$_SESSION['user_id']]);
$passed_categories = $passed_categories_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль - <?= htmlspecialchars($user['login']) ?></title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/profile.css">
    <style>
        .collapsible-btn {
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            font-size: 1rem;
        }
        .collapsible-btn:focus {
            outline: none;
        }
        .questions-list.collapsed {
            display: none;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header__center">
            <img src="../img/logo.png" alt="Логотип" class="header__logo">
        </div>
        <button class="header__login-btn" onclick="window.location.href='../index.php'">
            <img src="../img/vhod.png" alt="Выход" class="header__login-icon">
        </button>
    </header>
    
    <nav class="nav">
        <button class="nav__btn" onclick="window.location.href='../index.php'">Главная</button>
        <button class="nav__btn" onclick="window.location.href='kategorii.php'">Категории</button>
        <button class="nav__btn" onclick="window.location.href='popular.php'">Популярное</button>
        <button class="nav__btn" onclick="window.location.href='create.php'">Создать</button>
    </nav>

    <main class="profile-main">
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <img src="../img/default-avatar.png" alt="Аватар пользователя" class="avatar-image">
                </div>
                <div class="profile-info">
                    <h1 class="profile-name"><?= htmlspecialchars($user['login']) ?></h1>
                    <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
                    <p class="profile-role">Роль: <?= htmlspecialchars($user['role']) ?></p>
                    <button class="logout-btn" onclick="window.location.href='logout.php'">Выйти из аккаунта</button>
                </div>
            </div>
            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-value"><?= $questions_count ?></span>
                    <span class="stat-label">Вопросов</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= $total_points ?></span>
                    <span class="stat-label">Очков</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= date('d.m.Y', strtotime($user['created_at'])) ?></span>
                    <span class="stat-label">Дата регистрации</span>
                </div>
            </div>
            <div class="profile-content">
                <h2 class="profile-section-title">Мои вопросы</h2>
                <button class="collapsible-btn" id="toggleQuestionsBtn">Свернуть</button>
                <div class="questions-list" id="questionsList">
                    <?php if (count($all_questions) > 0): ?>
                        <?php foreach ($all_questions as $question): ?>
                            <div class="question-card">
                                <h3><?= htmlspecialchars($question['quest']) ?></h3>
                                <p>Категория: 
                                    <?php 
                                        $cat_stmt = $pdo->prepare("SELECT name FROM Categories WHERE id = ?");
                                        $cat_stmt->execute([$question['category_id']]);
                                        $category = $cat_stmt->fetch();
                                        echo htmlspecialchars($category['name'] ?? 'Без категории');
                                    ?>
                                </p>
                                <p>Очки: <?= $question['points'] ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="question-card placeholder">У вас пока нет вопросов</div>
                    <?php endif; ?>
                </div>
                <h2 class="profile-section-title">Пройденные категории</h2>
                <div class="categories-list">
                    <?php if (count($passed_categories) > 0): ?>
                        <ul>
                            <?php foreach ($passed_categories as $cat): ?>
                                <li><?= htmlspecialchars($cat['name']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="question-card placeholder">Вы ещё не проходили ни одной категории</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <hr class="footer-line">
        <div class="footer-content">
            <div class="footer-logo-block">
                <img src="../img/logo.png" alt="Логотип" class="footer-logo">
            </div>
            <div class="footer-links-block">
                <div class="footer-title">Страницы</div>
                <nav class="footer-nav">
                    <a href="../index.php" class="footer-link">Главная</a>
                    <a href="kategorii.php" class="footer-link">Категории</a>
                    <a href="popular.php" class="footer-link">Популярное</a>
                    <a href="create.php" class="footer-link">Создать</a>
                </nav>
            </div>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggleQuestionsBtn');
        const questionsList = document.getElementById('questionsList');
        let collapsed = false;
        toggleBtn.addEventListener('click', function() {
            collapsed = !collapsed;
            if (collapsed) {
                questionsList.classList.add('collapsed');
                toggleBtn.textContent = 'Развернуть';
            } else {
                questionsList.classList.remove('collapsed');
                toggleBtn.textContent = 'Свернуть';
            }
        });
    });
    </script>
</body>
</html>