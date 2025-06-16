<?php
require_once 'page/config.php';

// Получаем популярные вопросы
try {
    $stmt = $pdo->query("
        SELECT q.*, u.login as author_name, c.name as category_name 
        FROM Quiz q 
        LEFT JOIN Users u ON q.user_id = u.id 
        LEFT JOIN Categories c ON q.category_id = c.id 
        ORDER BY q.points DESC 
        LIMIT 6
    ");
    $popular_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $popular_questions = [];
    error_log("Ошибка при получении вопросов: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">

</head>
<body>
    <header class="header">
        <div class="header__center">
            <img src="img/logo.png" alt="Логотип" class="header__logo">
        </div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="page/profile.php" style="text-decoration: none;">
                <div class="header__login-btn">
                    <img src="img/vhod.png" alt="Профиль" class="header__login-icon">
                </div>
            </a>
        <?php else: ?>
            <a href="page/vhod.php" style="text-decoration: none;">
                <div class="header__login-btn">
                    <img src="img/vhod.png" alt="Вход" class="header__login-icon">
                </div>
            </a>
        <?php endif; ?>
    </header>
    
    <nav class="nav">
        <button class="nav__btn active">Главная</button>
        <button class="nav__btn" onclick="window.location.href='page/kategorii.php'">Категории</button>
        <button class="nav__btn" onclick="window.location.href='page/popular.php'">Популярное</button>
        <button class="nav__btn" onclick="window.location.href='page/create.php'">Создать</button>
    </nav>
    <main>
        <img src="img/main.png" alt="Главная картинка" class="main-image">
        <h2 class="popular-title">Популярные вопросы</h2>
        <section class="popular-questions">
            <?php if (!empty($popular_questions)): ?>
                <?php foreach ($popular_questions as $question): ?>
                    <div class="question-card">
                        <div class="question-header">
                            <span class="category"><?php echo htmlspecialchars($question['category_name']); ?></span>
                            <span class="points"><?php echo $question['points']; ?> очков</span>
                        </div>
                        <h3 class="question-title"><?php echo htmlspecialchars($question['quest']); ?></h3>
                        <div class="question-footer">
                            <span class="author">Автор: <?php echo htmlspecialchars($question['author_name']); ?></span>
                            <a href="page/category_questions.php?id=<?php echo $question['category_id']; ?>" class="view-btn">Смотреть</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-questions">Пока нет вопросов</div>
            <?php endif; ?>
        </section>
    </main>
    <footer class="footer">
        <hr class="footer-line">
        <div class="footer-content">
            <div class="footer-logo-block">
                <img src="img/logo.png" alt="Логотип" class="footer-logo">
            </div>
            <div class="footer-links-block">
                <div class="footer-title">Страницы</div>
                <nav class="footer-nav">
                    <a href="index.php" class="footer-link">Главная</a>
                    <a href="page/kategorii.php" class="footer-link">Категории</a>
                    <a href="page/popular.php" class="footer-link">Популярное</a>
                    <a href="page/create.php" class="footer-link">Создать</a>
                </nav>
            </div>
        </div>
    </footer>

</body>
</html>