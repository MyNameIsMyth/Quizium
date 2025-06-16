<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <header class="header">
        <div class="header__center">
            <img src="../img/logo.png" alt="Логотип" class="header__logo">
        </div>
        <button class="header__login-btn" onclick="window.location.href='vhod.php'">
            <img src="../img/vhod.png" alt="Вход" class="header__login-icon">
        </button>
    </header>
    
    <nav class="nav">
        <button class="nav__btn" onclick="window.location.href='../index.php'">Главная</button>
        <button class="nav__btn" onclick="window.location.href='kategorii.php'">Категории</button>
        <button class="nav__btn" onclick="window.location.href='popular.php'">Популярное</button>
        <button class="nav__btn" onclick="window.location.href='create.php'">Создать</button>
        <button class="nav__btn active">Админ</button>
    </nav>

    <div class="admin-layout">
        <aside class="admin-sidebar">
            <nav class="admin-nav">
                <a href="#users" class="admin-nav-item active">
                    <span class="admin-nav-icon">👥</span>
                    Управление пользователями
                </a>
                <a href="#create-quiz" class="admin-nav-item">
                    <span class="admin-nav-icon">➕</span>
                    Создание квиза
                </a>
                <a href="#edit-quiz" class="admin-nav-item">
                    <span class="admin-nav-icon">✏️</span>
                    Редактирование квизов
                </a>
            </nav>
        </aside>

        <main class="admin-main">
            <div id="users" class="admin-container">
                <h1 class="admin-title">Управление пользователями</h1>
                <div class="users-table-container">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Имя</th>
                                <th>Почта</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Здесь будут данные из базы данных -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="create-quiz" class="admin-container quiz-section">
                <h1 class="admin-title">Создание квиза</h1>
                <div class="create-bg">
                    <form class="create-form" id="quiz-create-form">
                        <label>
                            Категория:
                            <input type="text" class="create-category" id="create-category" placeholder="Например: Математика" required>
                        </label>
                        <label>
                            Вопрос:
                            <textarea class="create-question" placeholder="Напишите вопрос"></textarea>
                        </label>
                        <div class="create-answers">
                            <label>Варианты ответа (отметьте правильный):</label>
                            <div class="create-answer-row">
                                <input type="radio" name="correct-answer" class="create-answer-radio" value="0">
                                <input type="text" class="create-answer" placeholder="Вариант 1">
                            </div>
                            <div class="create-answer-row">
                                <input type="radio" name="correct-answer" class="create-answer-radio" value="1">
                                <input type="text" class="create-answer" placeholder="Вариант 2">
                            </div>
                            <div class="create-answer-row">
                                <input type="radio" name="correct-answer" class="create-answer-radio" value="2">
                                <input type="text" class="create-answer" placeholder="Вариант 3">
                            </div>
                            <div class="create-answer-row">
                                <input type="radio" name="correct-answer" class="create-answer-radio" value="3">
                                <input type="text" class="create-answer" placeholder="Вариант 4">
                            </div>
                        </div>
                        <button type="submit" class="create-submit-btn">Создать</button>
                    </form>
                </div>
            </div>

            <div id="edit-quiz" class="admin-container quiz-edit-section">
                <h1 class="admin-title">Редактирование квизов</h1>
                <div class="quiz-edit-container">
                    <div class="quiz-table-container">
                        <table class="quiz-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Категория</th>
                                    <th>Вопрос</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Здесь будут данные из базы данных -->
                            </tbody>
                        </table>
                    </div>
                    <div class="quiz-edit-panel">
                        <form class="edit-form" id="quiz-edit-form">
                            <label>
                                Категория:
                                <input type="text" class="edit-category" id="edit-category" placeholder="Например: Математика" required>
                            </label>
                            <label>
                                Вопрос:
                                <textarea class="edit-question" placeholder="Напишите вопрос"></textarea>
                            </label>
                            <div class="edit-answers">
                                <label>Варианты ответа (отметьте правильный):</label>
                                <div class="edit-answer-row">
                                    <input type="radio" name="edit-correct-answer" class="edit-answer-radio" value="0">
                                    <input type="text" class="edit-answer" placeholder="Вариант 1">
                                </div>
                                <div class="edit-answer-row">
                                    <input type="radio" name="edit-correct-answer" class="edit-answer-radio" value="1">
                                    <input type="text" class="edit-answer" placeholder="Вариант 2">
                                </div>
                                <div class="edit-answer-row">
                                    <input type="radio" name="edit-correct-answer" class="edit-answer-radio" value="2">
                                    <input type="text" class="edit-answer" placeholder="Вариант 3">
                                </div>
                                <div class="edit-answer-row">
                                    <input type="radio" name="edit-correct-answer" class="edit-answer-radio" value="3">
                                    <input type="text" class="edit-answer" placeholder="Вариант 4">
                                </div>
                            </div>
                            <div class="edit-buttons">
                                <button type="submit" class="edit-save-btn">Сохранить</button>
                                <button type="button" class="edit-cancel-btn">Отмена</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

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
    <script src="../js/create-validation.js"></script>
    <script src="../js/admin.js"></script>
</body>
</html> 