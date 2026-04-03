USE inwork;

-- Тестовые пользователи (пароль для всех: 123456)
-- bcrypt hash для '123456'
SET @pass = '$2y$10$on.zXmtOXgxGxr/1QXayoOoIsmbqr1o9U.rtrAipWCdnjgYd2dDW6';

-- Главный администратор
INSERT INTO users (name, email, password, role, bio, balance, rating, completed_orders) VALUES
('Админ', 'admin@inwork.kz', @pass, 'admin', 'Главный администратор платформы inWork', 5000000.00, 5.00, 0);

-- Тестовый заказчик
INSERT INTO users (name, email, password, role, bio, balance, rating, completed_orders) VALUES
('Арман Сериков', 'client@inwork.kz', @pass, 'client', 'Ищу специалистов для своих проектов', 250000.00, 4.50, 12);

-- Тестовые исполнители
INSERT INTO users (name, email, password, role, bio, balance, rating, completed_orders) VALUES
('Даулет Касымов', 'freelancer@inwork.kz', @pass, 'freelancer', 'Full-stack разработчик, 5 лет опыта. PHP, JavaScript, MySQL.', 120000.00, 4.80, 85),
('Айгерим Нурланова', 'aigerim@inwork.kz', @pass, 'freelancer', 'UI/UX дизайнер. Figma, Adobe, прототипирование.', 75000.00, 4.60, 52),
('Нурсултан Жумабаев', 'nursultan@inwork.kz', @pass, 'freelancer', 'Мобильная разработка. Flutter, React Native.', 40000.00, 4.20, 23),
('Динара Ахметова', 'dinara@inwork.kz', @pass, 'freelancer', 'Копирайтер и переводчик. Тексты для бизнеса.', 15000.00, 3.90, 8);

-- Каталожные специализации (электрик, сантехник, ремонт)
INSERT INTO freelancer_categories (user_id, category) VALUES
(3, 'electrician'),
(4, 'plumber'),
(5, 'repair'),
(6, 'electrician');

-- Тестовые заказы
INSERT INTO orders (client_id, title, description, category, budget, deadline, status, created_at) VALUES
(2, 'Разработать лендинг для стартапа', 'Нужен современный одностраничный сайт для стартапа в сфере EdTech в Алматы. Адаптивный дизайн, анимации, форма обратной связи. Дизайн-макет предоставлю в Figma.', 'web-development', 75000.00, DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'open', NOW() - INTERVAL 2 HOUR),
(2, 'Дизайн мобильного приложения', 'Разработать UI/UX дизайн для приложения доставки еды по Казахстану. 15-20 экранов, iOS и Android. Стиль — минимализм, чистый и современный.', 'design', 120000.00, DATE_ADD(CURDATE(), INTERVAL 21 DAY), 'open', NOW() - INTERVAL 5 HOUR),
(2, 'Написать статьи для блога', 'Требуется 10 SEO-оптимизированных статей для IT-блога. Темы: веб-разработка, облачные технологии, DevOps. По 2000-3000 слов каждая.', 'writing', 40000.00, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'open', NOW() - INTERVAL 1 DAY),
(1, 'Настроить рекламу в Google Ads и Kaspi', 'Нужно настроить и запустить рекламную кампанию в Google Ads и на Kaspi Магазине для интернет-магазина электроники. Бюджет на рекламу отдельно.', 'marketing', 60000.00, DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'open', NOW() - INTERVAL 3 HOUR),
(1, 'Разработать Telegram-бота', 'Бот для автоматизации работы с клиентами: приём заявок, FAQ, уведомления. Python + aiogram. Интеграция с Google Sheets.', 'web-development', 100000.00, DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'open', NOW() - INTERVAL 8 HOUR),
(2, 'Создать промо-ролик', 'Короткий промо-ролик для социальных сетей (30-60 сек). Motion-дизайн, анимация логотипа, текстовые подписи. Предоставлю бренд-бук.', 'video', 90000.00, DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'open', NOW() - INTERVAL 12 HOUR);

-- Настройки страниц (все включены по умолчанию)
INSERT INTO page_settings (page_key, page_name, is_enabled) VALUES
('dashboard',     'Личный кабинет',    1),
('orders',        'Лента заказов',     1),
('orders_create', 'Создание заказа',   1),
('my_orders',     'Мои заказы',        1),
('profile',       'Профиль',           1),
('balance',       'Баланс',            1),
('chat',          'Чат / Сообщения',   1),
('reviews',       'Отзывы',            1),
('register',      'Регистрация',       1);
