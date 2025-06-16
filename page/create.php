<?php
require_once 'config.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: vhod.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/index.css">
    <style>
        .question-counter {
            text-align: center;
            margin: 1rem 0;
            font-size: 1.2rem;
            color: black;
        }
        .error-message {
            color: #dc3545;
            margin: 0.5rem 0;
            display: none;
        }
        .success-message {
            color: #28a745;
            margin: 0.5rem 0;
            display: none;
        }
        .question-navigation {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 1rem 0;
        }
        .question-nav-btn {
            padding: 0.5rem 1rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .question-nav-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .question-nav-btn:hover:not(:disabled) {
            background: #0056b3;
        }
        .question-list {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 1rem 0;
        }
        .question-number {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #f8f9fa;
            cursor: pointer;
            border: 1px solid #dee2e6;
        }
        .question-number.active {
            background: #007bff;
            color: white;
        }
        .question-number.completed {
            background:rgb(230, 201, 0);
            color: white;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header__center">
            <img src="../img/logo.png" alt="Логотип" class="header__logo">
        </div>
        <a href="profile.php" style="text-decoration: none;">
            <div class="header__login-btn">
                <img src="../img/vhod.png" alt="Профиль" class="header__login-icon">
            </div>
        </a>
    </header>
    <nav class="nav">
        <button class="nav__btn" onclick="window.location.href='../index.php'">Главная</button>
        <button class="nav__btn" onclick="window.location.href='kategorii.php'">Категории</button>
        <button class="nav__btn" onclick="window.location.href='popular.php'">Популярное</button>
        <button class="nav__btn" onclick="window.location.href='create.php'">Создать</button>
    </nav>

    <main>
        <div class="create-bg">
            <form class="create-form" id="quiz-create-form">
                <h2 class="create-title">Создать новую категорию</h2>
                <div class="question-counter">Вопрос 1 из 5</div>
                
                <div class="question-list" id="question-list">
                    <!-- Здесь будут номера вопросов -->
                </div>

                <div class="question-navigation">
                    <button type="button" class="question-nav-btn" id="prev-question-btn" disabled>← Предыдущий</button>
                    <button type="button" class="question-nav-btn" id="next-question-btn" disabled>Следующий →</button>
                </div>

                <label>
                    Название категории:
                    <input type="text" class="create-category" id="create-category" placeholder="Например: Математика" required>
                    <div class="error-message" id="category-error"></div>
                </label>

                <label>
                    Вопрос:
                    <textarea class="create-question" id="create-question" placeholder="Напишите вопрос" required></textarea>
                    <div class="error-message" id="question-error"></div>
                </label>

                <div class="create-answers">
                    <label>Варианты ответа (отметьте правильный):</label>
                    <div class="create-answer-row">
                        <input type="radio" name="correct-answer" class="create-answer-radio" value="0" required>
                        <input type="text" class="create-answer" placeholder="Вариант 1" required>
                        <div class="error-message" id="answer1-error"></div>
                    </div>
                    <div class="create-answer-row">
                        <input type="radio" name="correct-answer" class="create-answer-radio" value="1" required>
                        <input type="text" class="create-answer" placeholder="Вариант 2" required>
                        <div class="error-message" id="answer2-error"></div>
                    </div>
                    <div class="create-answer-row">
                        <input type="radio" name="correct-answer" class="create-answer-radio" value="2" required>
                        <input type="text" class="create-answer" placeholder="Вариант 3" required>
                        <div class="error-message" id="answer3-error"></div>
                    </div>
                    <div class="create-answer-row">
                        <input type="radio" name="correct-answer" class="create-answer-radio" value="3" required>
                        <input type="text" class="create-answer" placeholder="Вариант 4" required>
                        <div class="error-message" id="answer4-error"></div>
                    </div>
                </div>

                <div class="error-message" id="form-error"></div>
                <div class="success-message" id="success-message"></div>

                <button type="button" class="create-submit-btn" id="add-question-btn">Добавить вопрос</button>
                <button type="submit" class="create-submit-btn" id="create-category-btn" style="display: none;">Создать категорию</button>
            </form>
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
        let questions = [];
        let currentQuestion = 1;
        let editingQuestion = null;

        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }

        function hideError(elementId) {
            const errorElement = document.getElementById(elementId);
            errorElement.style.display = 'none';
        }

        function validateForm() {
            let isValid = true;
            const category = document.getElementById('create-category').value.trim();
            const question = document.getElementById('create-question').value.trim();
            const answers = Array.from(document.getElementsByClassName('create-answer')).map(input => input.value.trim());
            const correctAnswer = document.querySelector('input[name="correct-answer"]:checked');

            if (!category) {
                showError('category-error', 'Введите название категории');
                isValid = false;
            } else {
                hideError('category-error');
            }

            if (!question) {
                showError('question-error', 'Введите вопрос');
                isValid = false;
            } else {
                hideError('question-error');
            }

            answers.forEach((answer, index) => {
                if (!answer) {
                    showError(`answer${index + 1}-error`, 'Введите вариант ответа');
                    isValid = false;
                } else {
                    hideError(`answer${index + 1}-error`);
                }
            });

            if (!correctAnswer) {
                showError('form-error', 'Выберите правильный ответ');
                isValid = false;
            } else {
                hideError('form-error');
            }

            return isValid;
        }

        function clearForm() {
            document.getElementById('create-question').value = '';
            document.getElementsByClassName('create-answer').forEach(input => input.value = '');
            document.querySelector('input[name="correct-answer"]:checked').checked = false;
            hideError('question-error');
            hideError('answer1-error');
            hideError('answer2-error');
            hideError('answer3-error');
            hideError('answer4-error');
            hideError('form-error');
        }

        function updateQuestionCounter() {
            document.querySelector('.question-counter').textContent = `Вопрос ${currentQuestion} из 5`;
        }

        function updateQuestionNavigation() {
            const prevBtn = document.getElementById('prev-question-btn');
            const nextBtn = document.getElementById('next-question-btn');
            
            prevBtn.disabled = currentQuestion === 1;
            nextBtn.disabled = currentQuestion === questions.length + 1;
        }

        function updateQuestionList() {
            const questionList = document.getElementById('question-list');
            questionList.innerHTML = '';
            
            for (let i = 1; i <= 5; i++) {
                const questionNumber = document.createElement('div');
                questionNumber.className = 'question-number';
                if (i === currentQuestion) {
                    questionNumber.classList.add('active');
                } else if (i < currentQuestion) {
                    questionNumber.classList.add('completed');
                }
                questionNumber.textContent = i;
                questionNumber.onclick = () => loadQuestion(i);
                questionList.appendChild(questionNumber);
            }
        }

        function loadQuestion(number) {
            if (number > questions.length + 1) return;
            
            currentQuestion = number;
            updateQuestionCounter();
            updateQuestionNavigation();
            updateQuestionList();

            if (number <= questions.length) {
                const question = questions[number - 1];
                document.getElementById('create-question').value = question.quest;
                
                const answers = [...question.wrong_answers, question.correct_answer];
                document.getElementsByClassName('create-answer').forEach((input, index) => {
                    input.value = answers[index];
                });

                const correctIndex = answers.indexOf(question.correct_answer);
                document.querySelector(`input[name="correct-answer"][value="${correctIndex}"]`).checked = true;
                
                document.getElementById('add-question-btn').textContent = 'Сохранить изменения';
                editingQuestion = number;
            } else {
                clearForm();
                document.getElementById('add-question-btn').textContent = 'Добавить вопрос';
                editingQuestion = null;
            }
        }

        document.getElementById('prev-question-btn').addEventListener('click', () => {
            if (currentQuestion > 1) {
                loadQuestion(currentQuestion - 1);
            }
        });

        document.getElementById('next-question-btn').addEventListener('click', () => {
            if (currentQuestion <= questions.length) {
                loadQuestion(currentQuestion + 1);
            }
        });

        document.getElementById('add-question-btn').addEventListener('click', function() {
            if (!validateForm()) {
                return;
            }

            const category = document.getElementById('create-category').value.trim();
            const question = document.getElementById('create-question').value.trim();
            const answers = Array.from(document.getElementsByClassName('create-answer')).map(input => input.value.trim());
            const correctAnswerIndex = document.querySelector('input[name="correct-answer"]:checked').value;

            const questionData = {
                quest: question,
                wrong_answers: answers.filter((_, index) => index != correctAnswerIndex),
                correct_answer: answers[correctAnswerIndex]
            };

            if (editingQuestion) {
                questions[editingQuestion - 1] = questionData;
            } else {
                questions.push(questionData);
                currentQuestion++;
            }

            if (currentQuestion > 5) {
                document.getElementById('add-question-btn').style.display = 'none';
                document.getElementById('create-category-btn').style.display = 'block';
            }

            clearForm();
            updateQuestionCounter();
            updateQuestionNavigation();
            updateQuestionList();
            editingQuestion = null;
            document.getElementById('add-question-btn').textContent = 'Добавить вопрос';
        });

        document.getElementById('quiz-create-form').addEventListener('submit', function(e) {
            e.preventDefault();

            if (questions.length !== 5) {
                showError('form-error', 'Добавьте все 5 вопросов');
                return;
            }

            const category = document.getElementById('create-category').value.trim();
            const data = {
                category_name: category,
                questions: questions
            };

            console.log('Sending data:', data);

            fetch('save_category.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                // Скрываем старое сообщение об ошибке
                hideError('form-error');
                // Показываем сообщение об успехе, если success
                if (data.success) {
                    let successMsg = document.getElementById('success-message');
                    if (!successMsg) {
                        successMsg = document.createElement('div');
                        successMsg.id = 'success-message';
                        successMsg.style.background = '#d4edda';
                        successMsg.style.color = '#155724';
                        successMsg.style.padding = '1rem';
                        successMsg.style.margin = '1rem 0';
                        successMsg.style.borderRadius = '5px';
                        successMsg.style.textAlign = 'center';
                        successMsg.style.fontWeight = 'bold';
                        document.querySelector('.create-bg').prepend(successMsg);
                    }
                    successMsg.textContent = data.message || 'Категория успешно создана!';
                    successMsg.style.display = 'block';
                    // Очищаем форму и массив вопросов
                    document.getElementById('quiz-create-form').reset();
                    questions = [];
                    currentQuestion = 1;
                    updateQuestionCounter();
                    updateQuestionNavigation();
                    updateQuestionList();
                    editingQuestion = null;
                    document.getElementById('add-question-btn').style.display = 'block';
                    document.getElementById('create-category-btn').style.display = 'none';
                    setTimeout(() => {
                        window.location.href = 'kategorii.php';
                    }, 2000);
                } else if (data.error) {
                    showError('form-error', data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('form-error', 'Произошла ошибка при сохранении');
            });
        });

        // Инициализация навигации по вопросам
        updateQuestionNavigation();
        updateQuestionList();
    </script>
</body>
</html>
