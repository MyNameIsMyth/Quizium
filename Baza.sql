CREATE DATABASE IF NOT EXISTS quiz_app;
USE quiz_app;

-- Таблица категорий
CREATE TABLE Categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица пользователей
CREATE TABLE Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    login VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    points INT DEFAULT 0,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица вопросов
CREATE TABLE Quiz (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category_id INT,
    quest TEXT NOT NULL,
    first_wrong_answer VARCHAR(255) NOT NULL,
    second_wrong_answer VARCHAR(255) NOT NULL,
    third_wrong_answer VARCHAR(255) NOT NULL,
    correct_answer VARCHAR(255) NOT NULL,
    points INT NOT NULL DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица популярных вопросов
CREATE TABLE PopularQuestions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    FOREIGN KEY (quiz_id) REFERENCES Quiz(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица результатов ответов
CREATE TABLE QuizResults (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    user_answer VARCHAR(255) NOT NULL,
    is_correct BOOLEAN NOT NULL,
    points_earned INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES Quiz(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Наполнение категориями
INSERT INTO Categories (name) VALUES
('История'),
('География'),
('Наука'),
('Искусство'),
('Спорт'),
('Кино'),
('Игры'),
('Мифология');

-- Тестовые пользователи (пароль: password123)
INSERT INTO Users (login, email, password, role) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- История (всего 5 вопросов)
INSERT INTO Quiz (user_id, category_id, quest, first_wrong_answer, second_wrong_answer, third_wrong_answer, correct_answer) VALUES
(1, 1, 'Кто был первым президентом США?', 'Авраам Линкольн', 'Томас Джефферсон', 'Франклин Рузвельт', 'Джордж Вашингтон'),
(1, 1, 'Когда распался СССР?', '1985', '1995', '1989', '1991'),
(1, 1, 'Кто написал "Капитал"?', 'Ленин', 'Энгельс', 'Троцкий', 'Маркс'),
(1, 1, 'В каком году человек впервые полетел в космос?', '1955', '1965', '1945', '1961');

-- География (всего 5 вопросов)
INSERT INTO Quiz (user_id, category_id, quest, first_wrong_answer, second_wrong_answer, third_wrong_answer, correct_answer) VALUES
(1, 2, 'Столица Австралии?', 'Сидней', 'Мельбурн', 'Брисбен', 'Канберра'),
(1, 2, 'Самая высокая гора в мире?', 'К2', 'Канченджанга', 'Макалу', 'Эверест'),
(1, 2, 'Какое озеро самое глубокое?', 'Каспийское', 'Танганьика', 'Ньяса', 'Байкал'),
(1, 2, 'Сколько океанов на Земле?', '3', '6', '7', '5');

-- Наука (всего 5 вопросов)
INSERT INTO Quiz (user_id, category_id, quest, first_wrong_answer, second_wrong_answer, third_wrong_answer, correct_answer) VALUES
(1, 3, 'Сколько элементов в периодической таблице?', '112', '118', '126', '118'),
(1, 3, 'Кто открыл пенициллин?', 'Пастер', 'Кох', 'Мечников', 'Флеминг'),
(1, 3, 'Какая планета ближе всего к Солнцу?', 'Венера', 'Земля', 'Марс', 'Меркурий'),
(1, 3, 'Сколько хромосом у человека?', '44', '48', '52', '46');

-- Искусство (5 вопросов)
INSERT INTO Quiz (user_id, category_id, quest, first_wrong_answer, second_wrong_answer, third_wrong_answer, correct_answer) VALUES
(1, 4, 'Кто написал "Черный квадрат"?', 'Шагал', 'Кандинский', 'Пикассо', 'Малевич'),
(1, 4, 'Автор скульптуры "Давид"?', 'Донателло', 'Рафаэль', 'Бернини', 'Микеланджело'),
(1, 4, 'Кто написал "Девятый вал"?', 'Шишкин', 'Репин', 'Верещагин', 'Айвазовский'),
(1, 4, 'Автор оперы "Кармен"?', 'Верди', 'Моцарт', 'Чайковский', 'Бизе'),
(1, 4, 'Кто написал "Гернику"?', 'Дали', 'Моне', 'Ван Гог', 'Пикассо');

-- Спорт (5 вопросов)
INSERT INTO Quiz (user_id, category_id, quest, first_wrong_answer, second_wrong_answer, third_wrong_answer, correct_answer) VALUES
(1, 5, 'Сколько игроков в бейсбольной команде?', '8', '10', '12', '9'),
(1, 5, 'В каком году проходили Олимпийские игры в Москве?', '1976', '1984', '1992', '1980'),
(1, 5, 'Кто выиграл ЧМ по футболу 2018?', 'Хорватия', 'Бельгия', 'Англия', 'Франция'),
(1, 5, 'Сколько очков за трехочковый бросок в баскетболе?', '1', '2', '4', '3'),
(1, 5, 'Какой теннисист выиграл больше всех турниров Большого шлема?', 'Сампас', 'Федерер', 'Надаль', 'Джокович');

-- Кино (5 уникальных вопросов)
INSERT INTO Quiz (user_id, category_id, quest, first_wrong_answer, second_wrong_answer, third_wrong_answer, correct_answer) VALUES
(1, 6, 'Кто режиссер "Крестного отца"?', 'Спилберг', 'Скорсезе', 'Кубрик', 'Коппола'),
(1, 6, 'Какой фильм получил 11 "Оскаров"?', 'Титаник', 'Властелин колец: Возвращение короля', 'Бен-Гур', 'Все три варианта'),
(1, 6, 'Кто играл главную роль в "Форресте Гампе"?', 'Том Круз', 'Брэд Питт', 'Леонардо ДиКаприо', 'Том Хэнкс'),
(1, 6, 'Какой фильм самый кассовый с учетом инфляции?', 'Аватар', 'Мстители: Финал', 'Титаник', 'Унесенные ветром'),
(1, 6, 'Кто режиссер фильма "Психо" 1960 года?', 'Кубрик', 'Скорсезе', 'Феллини', 'Хичкок');

-- Игры (5 вопросов)
INSERT INTO Quiz (user_id, category_id, quest, first_wrong_answer, second_wrong_answer, third_wrong_answer, correct_answer) VALUES
(1, 7, 'Кто главный герой серии игр The Legend of Zelda?', 'Зельда', 'Ганон', 'Эпоона', 'Линк'),
(1, 7, 'Какая компания создала Minecraft?', 'Valve', 'Electronic Arts', 'Ubisoft', 'Mojang'),
(1, 7, 'Сколько поколений Pokémon существует?', '5', '7', '9', '8'),
(1, 7, 'В каком году вышла первая игра Mario?', '1983', '1987', '1990', '1985'),
(1, 7, 'Какая игра самая продаваемая в истории?', 'GTA V', 'Wii Sports', 'Tetris', 'Minecraft');

-- Мифология (5 вопросов)
INSERT INTO Quiz (user_id, category_id, quest, first_wrong_answer, second_wrong_answer, third_wrong_answer, correct_answer) VALUES
(1, 8, 'Кто верховный бог в древнегреческой мифологии?', 'Посейдон', 'Аид', 'Арес', 'Зевс'),
(1, 8, 'Кто охранял вход в Аид?', 'Минотавр', 'Гидра', 'Харон', 'Цербер'),
(1, 8, 'Бог солнца в египетской мифологии?', 'Анубис', 'Осирис', 'Гор', 'Ра'),
(1, 8, 'Кто создал людей по греческой мифологии?', 'Зевс', 'Афина', 'Гера', 'Прометей'),
(1, 8, 'Бог грома в скандинавской мифологии?', 'Один', 'Локи', 'Бальдр', 'Тор');
