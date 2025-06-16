<?php
require_once 'config.php';

// Получаем все категории с количеством вопросов
try {
    $stmt = $pdo->query("
        SELECT 
            c.id,
            c.name,
            COUNT(q.id) as question_count
        FROM Categories c
        LEFT JOIN Quiz q ON c.id = q.category_id
        GROUP BY c.id, c.name
        ORDER BY c.name
    ");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Отладочная информация
    error_log("Найдено категорий: " . count($categories));
    foreach ($categories as $category) {
        error_log("Категория: " . $category['name'] . " (ID: " . $category['id'] . ", вопросов: " . $category['question_count'] . ")");
    }
} catch (PDOException $e) {
    $categories = [];
    error_log("Ошибка при получении категорий: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/index.css">
    <style>
        .kategorii-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .kategorii-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .kategorii-card:hover {
            transform: translateY(-5px);
        }
        .kategorii-card h3 {
            margin: 0 0 1rem 0;
            color: #333;
            font-size: 1.5rem;
        }
        .category-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .question-count {
            color: #666;
            font-size: 0.9rem;
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 5px;
            text-align: center;
        }
        .view-category-btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            transition: background 0.2s;
            font-weight: bold;
        }
        .view-category-btn:hover {
            background: #0056b3;
        }
        .kategorii-title {
            text-align: center;
            margin: 2rem 0;
            color: #333;
            font-size: 2rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header__center">
            <img src="../img/logo.png" alt="Логотип" class="header__logo">
        </div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php" style="text-decoration: none;">
                <div class="header__login-btn">
                    <img src="../img/vhod.png" alt="Профиль" class="header__login-icon">
                </div>
            </a>
        <?php else: ?>
            <a href="vhod.php" style="text-decoration: none;">
                <div class="header__login-btn">
                    <img src="../img/vhod.png" alt="Вход" class="header__login-icon">
                </div>
            </a>
        <?php endif; ?>
    </header>
    <nav class="nav">
        <button class="nav__btn" onclick="window.location.href='../index.php'">Главная</button>
        <button class="nav__btn active">Категории</button>
        <button class="nav__btn" onclick="window.location.href='popular.php'">Популярное</button>
        <button class="nav__btn" onclick="window.location.href='create.php'">Создать</button>
    </nav>
    <!-- Контент страницы Категории -->
    <main>
        <h1 class="kategorii-title">Категории</h1>
        <section class="kategorii-list">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <div class="kategorii-card">
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        <div class="category-info">
                            <span class="question-count">Вопросов: <?php echo $category['question_count']; ?></span>
                            <a href="category_questions.php?id=<?php echo $category['id']; ?>" class="view-category-btn">Смотреть вопросы</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-categories">Категории пока не добавлены</div>
            <?php endif; ?>
        </section>
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
</body>
</html>
