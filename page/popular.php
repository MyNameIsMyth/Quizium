<?php
require_once 'config.php';

// Получаем случайные вопросы
try {
    $stmt = $pdo->query("
        SELECT 
            q.*,
            c.name as category_name,
            u.login as author_name
        FROM Quiz q
        JOIN Categories c ON q.category_id = c.id
        JOIN Users u ON q.user_id = u.id
        ORDER BY RAND()
        LIMIT 5
    ");
    $random_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $random_questions = [];
    error_log("Ошибка при получении случайных вопросов: " . $e->getMessage());
}

// Получаем случайные категории
try {
    $stmt = $pdo->query("
        SELECT 
            c.*,
            COUNT(q.id) as question_count
        FROM Categories c
        LEFT JOIN Quiz q ON c.id = q.category_id
        GROUP BY c.id
        ORDER BY RAND()
        LIMIT 4
    ");
    $random_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $random_categories = [];
    error_log("Ошибка при получении случайных категорий: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/index.css">
    <style>
        .popular-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .popular-section {
            margin-bottom: 3rem;
        }
        .popular-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 2rem;
            text-align: center;
        }
        .questions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        .question-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .question-text {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 1rem;
        }
        .question-meta {
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 0.9rem;
        }
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
        }
        .category-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .category-name {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 1rem;
        }
        .category-count {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .view-category-btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.2s;
        }
        .view-category-btn:hover {
            background: #0056b3;
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
        <button class="nav__btn" onclick="window.location.href='kategorii.php'">Категории</button>
        <button class="nav__btn active">Популярное</button>
        <button class="nav__btn" onclick="window.location.href='create.php'">Создать</button>
    </nav>

    <main class="popular-container">
        <section class="popular-section">
            <h2 class="popular-title">Популярные вопросы</h2>
            <div class="questions-grid">
                <?php foreach ($random_questions as $question): ?>
                    <div class="question-card">
                        <div class="question-text"><?php echo htmlspecialchars($question['quest']); ?></div>
                        <div class="question-meta">
                            <span>Категория: <?php echo htmlspecialchars($question['category_name']); ?></span>
                            <span>Автор: <?php echo htmlspecialchars($question['author_name']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="popular-section">
            <h2 class="popular-title">Популярные категории</h2>
            <div class="categories-grid">
                <?php foreach ($random_categories as $category): ?>
                    <div class="category-card">
                        <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
                        <div class="category-count">Вопросов: <?php echo $category['question_count']; ?></div>
                        <a href="category_questions.php?id=<?php echo $category['id']; ?>" class="view-category-btn">Смотреть вопросы</a>
                    </div>
                <?php endforeach; ?>
            </div>
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
