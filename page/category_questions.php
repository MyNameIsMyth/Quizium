<?php
require_once 'config.php';

// Получаем ID категории из URL
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // Получаем информацию о категории
    $stmt = $pdo->prepare("SELECT name FROM Categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        die("Категория не найдена");
    }

    // Получаем все вопросы для данной категории
    $stmt = $pdo->prepare("
        SELECT 
            q.*,
            u.login as author_name
        FROM Quiz q
        LEFT JOIN Users u ON q.user_id = u.id
        WHERE q.category_id = ?
        ORDER BY q.created_at DESC
    ");
    $stmt->execute([$category_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Отладочная информация
    error_log("Категория ID {$category_id}: найдено вопросов - " . count($questions));
    foreach ($questions as $question) {
        error_log("Вопрос: " . $question['quest'] . " (ID: " . $question['id'] . ")");
    }

} catch (PDOException $e) {
    error_log("Ошибка при получении вопросов: " . $e->getMessage());
    die("Ошибка при получении вопросов");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/index.css">
    <style>
        .questions-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .question-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .question-text {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #333;
            font-weight: bold;
        }
        .answers-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .answer-item {
            padding: 0.8rem;
            margin: 0.5rem 0;
            background: #f8f9fa;
            border-radius: 5px;
            transition: background-color 0.2s;
            cursor: pointer;
        }
        .answer-item:hover {
            background: #e9ecef;
        }
        .answer-item.selected {
            background: #007bff;
            color: white;
        }
        .answer-item.correct {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .answer-item.incorrect {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .question-meta {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #666;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .back-link {
            display: inline-block;
            margin: 1rem 0;
            color: #007bff;
            text-decoration: none;
            font-size: 1.1rem;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .category-title {
            text-align: center;
            margin: 2rem 0;
            color: #333;
            font-size: 2rem;
        }
        .question-count {
            text-align: center;
            color: #666;
            margin-bottom: 2rem;
        }
        .submit-btn {
            display: block;
            width: 200px;
            margin: 2rem auto;
            padding: 1rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
        }
        .submit-btn:hover {
            background: #0056b3;
        }
        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .result-container {
            display: none;
            text-align: center;
            margin: 2rem 0;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .result-score {
            font-size: 2rem;
            color: #28a745;
            margin: 1rem 0;
        }
        .result-details {
            margin-top: 2rem;
            text-align: left;
        }
        .result-question {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .result-question.correct {
            border-left: 4px solid #28a745;
        }
        .result-question.incorrect {
            border-left: 4px solid #dc3545;
        }
        .result-question-text {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .result-answer {
            margin: 0.5rem 0;
            padding: 0.5rem;
            border-radius: 4px;
        }
        .result-answer.correct {
            background: #d4edda;
            color: #155724;
        }
        .result-answer.incorrect {
            background: #f8d7da;
            color: #721c24;
        }
        .result-answer.your-answer {
            background: #e2e3e5;
            color: #383d41;
        }
        .result-stats {
            margin: 1rem 0;
            font-size: 1.1rem;
            color: #666;
        }
        .error-message {
            display: none;
            color: #dc3545;
            text-align: center;
            margin: 1rem 0;
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
        <button class="nav__btn" onclick="window.location.href='popular.php'">Популярное</button>
        <button class="nav__btn" onclick="window.location.href='create.php'">Создать</button>
    </nav>

    <main class="questions-container">
        <a href="kategorii.php" class="back-link">← Назад к категориям</a>
        <h1 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h1>
        <div class="question-count">Всего вопросов: <?php echo count($questions); ?></div>
        
        <?php if (!empty($questions)): ?>
            <form id="quizForm">
                <?php foreach ($questions as $index => $question): ?>
                    <div class="question-card" data-question-id="<?php echo $question['id']; ?>">
                        <div class="question-text"><?php echo htmlspecialchars($question['quest']); ?></div>
                        <ul class="answers-list">
                            <?php
                            // Создаем массив ответов в правильном порядке
                            $answers = [
                                $question['first_wrong_answer'],
                                $question['second_wrong_answer'],
                                $question['third_wrong_answer'],
                                $question['correct_answer']
                            ];
                            // Перемешиваем только неправильные ответы
                            $wrong_answers = array_slice($answers, 0, 3);
                            shuffle($wrong_answers);
                            // Добавляем правильный ответ в конец
                            $wrong_answers[] = $question['correct_answer'];
                            // Выводим ответы
                            foreach ($wrong_answers as $answer):
                            ?>
                                <li class="answer-item" data-answer="<?php echo htmlspecialchars($answer); ?>">
                                    <?php echo htmlspecialchars($answer); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="question-meta">
                            <span>Автор: <?php echo htmlspecialchars($question['author_name']); ?></span>
                            <span>Очки: <?php echo $question['points']; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="submit-btn" id="submitBtn">Завершить тест</button>
                <div class="error-message" id="errorMessage">Пожалуйста, ответьте на все вопросы</div>
            </form>
            <div class="result-container" id="resultContainer">
                <h2>Результаты теста</h2>
                <div class="result-score">Ваш результат: <span id="totalScore">0</span> очков</div>
                <div class="result-stats">
                    Правильных ответов: <span id="correctAnswers">0</span> из <span id="totalQuestions">0</span>
                </div>
                <div class="result-details" id="resultDetails"></div>
            </div>
        <?php else: ?>
            <div class="no-questions">В этой категории пока нет вопросов</div>
        <?php endif; ?>
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
        const quizForm = document.getElementById('quizForm');
        const submitBtn = document.getElementById('submitBtn');
        const errorMessage = document.getElementById('errorMessage');
        const resultContainer = document.getElementById('resultContainer');
        const totalScoreElement = document.getElementById('totalScore');
        const questionCards = document.querySelectorAll('.question-card');
        let selectedAnswers = new Map();

        // Обработка выбора ответа
        questionCards.forEach(card => {
            const answers = card.querySelectorAll('.answer-item');
            answers.forEach(answer => {
                answer.addEventListener('click', function() {
                    // Убираем выделение с других ответов в этом вопросе
                    answers.forEach(a => a.classList.remove('selected'));
                    // Выделяем выбранный ответ
                    this.classList.add('selected');
                    // Сохраняем выбранный ответ
                    selectedAnswers.set(card.dataset.questionId, this.dataset.answer);
                    
                    // Проверяем, все ли вопросы отвечены
                    if (selectedAnswers.size === questionCards.length) {
                        submitBtn.disabled = false;
                        errorMessage.style.display = 'none';
                    }
                });
            });
        });

        // Обработка отправки формы
        quizForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (selectedAnswers.size !== questionCards.length) {
                errorMessage.style.display = 'block';
                return;
            }

            let totalScore = 0;
            let correctAnswers = 0;

            // Проверяем ответы
            questionCards.forEach(card => {
                const selectedAnswer = selectedAnswers.get(card.dataset.questionId);
                const correctAnswer = card.querySelector('.answer-item[data-answer="' + 
                    card.querySelector('.answer-item:nth-child(4)').dataset.answer + '"]');
                
                const selectedElement = card.querySelector(`[data-answer="${selectedAnswer}"]`);
                
                if (selectedAnswer === correctAnswer.dataset.answer) {
                    selectedElement.classList.add('correct');
                    totalScore += parseInt(card.querySelector('.question-meta span:last-child').textContent.match(/\d+/)[0]);
                    correctAnswers++;
                } else {
                    selectedElement.classList.add('incorrect');
                    correctAnswer.classList.add('correct');
                }
            });

            // Показываем результаты
            quizForm.style.display = 'none';
            resultContainer.style.display = 'block';
            totalScoreElement.textContent = totalScore;
            document.getElementById('correctAnswers').textContent = correctAnswers;
            document.getElementById('totalQuestions').textContent = questionCards.length;

            // Создаем детальный отчет о результатах
            const resultDetails = document.getElementById('resultDetails');
            questionCards.forEach(card => {
                const selectedAnswer = selectedAnswers.get(card.dataset.questionId);
                const correctAnswer = card.querySelector('.answer-item[data-answer="' + 
                    card.querySelector('.answer-item:nth-child(4)').dataset.answer + '"]').dataset.answer;
                
                const questionDiv = document.createElement('div');
                questionDiv.className = `result-question ${selectedAnswer === correctAnswer ? 'correct' : 'incorrect'}`;
                
                const questionText = document.createElement('div');
                questionText.className = 'result-question-text';
                questionText.textContent = card.querySelector('.question-text').textContent;
                questionDiv.appendChild(questionText);

                const yourAnswer = document.createElement('div');
                yourAnswer.className = 'result-answer your-answer';
                yourAnswer.textContent = `Ваш ответ: ${selectedAnswer}`;
                questionDiv.appendChild(yourAnswer);

                if (selectedAnswer !== correctAnswer) {
                    const correctAnswerDiv = document.createElement('div');
                    correctAnswerDiv.className = 'result-answer correct';
                    correctAnswerDiv.textContent = `Правильный ответ: ${correctAnswer}`;
                    questionDiv.appendChild(correctAnswerDiv);
                }

                const points = document.createElement('div');
                points.className = 'result-answer';
                points.textContent = `Очки за вопрос: ${card.querySelector('.question-meta span:last-child').textContent}`;
                questionDiv.appendChild(points);

                resultDetails.appendChild(questionDiv);
            });

            // Отправляем результаты на сервер (если нужно)
            fetch('save_results.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    category_id: <?php echo $category_id; ?>,
                    score: totalScore,
                    correct_answers: correctAnswers,
                    total_questions: questionCards.length
                })
            });
        });
    });
    </script>
</body>
</html> 