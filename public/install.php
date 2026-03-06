<?php
/**
 * Установщик платформы inWork
 * Откройте в браузере: http://localhost/projects/inwork/public/install.php
 * После установки УДАЛИТЕ этот файл!
 */

$config = [
    'host'     => 'localhost',
    'port'     => '3306',
    'database' => 'inwork',
    'username' => 'root',
    'password' => 'root',
    'charset'  => 'utf8mb4',
];

$messages = [];
$errors = [];

try {
    // 1. Подключаемся без указания базы
    $dsn = "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // 2. Создаём базу данных
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$config['database']}`");
    $messages[] = "База данных '{$config['database']}' создана";

    // 3. Создаём таблицы
    $schema = file_get_contents(__DIR__ . '/../config/schema.sql');
    // Убираем CREATE DATABASE и USE — мы уже подключены
    $schema = preg_replace('/CREATE DATABASE.*?;/s', '', $schema);
    $schema = preg_replace('/USE\s+\w+;/s', '', $schema);

    // Выполняем каждый запрос отдельно
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $pdo->exec($stmt);
        }
    }
    $messages[] = "Таблицы созданы";

    // Исправление размера page_icon для существующих установок
    try {
        $pdo->exec("ALTER TABLE page_settings MODIFY page_icon TEXT DEFAULT NULL");
    } catch (PDOException $e) {
        // Игнорируем, если таблицы нет
    }

    // 4. Проверяем, есть ли уже пользователи
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count > 0) {
        $messages[] = "Пользователи уже существуют ({$count} шт.), пропускаем заполнение";
    } else {
        // 5. Создаём тестовых пользователей с правильным хешем
        $password = password_hash('123456', PASSWORD_BCRYPT);

        $users = [
            ['Админ', 'admin@inwork.kz', $password, 'admin', 'Главный администратор платформы inWork', 5000000.00, 5.00, 0],
            ['Арман Сериков', 'client@inwork.kz', $password, 'client', 'Ищу специалистов для своих проектов', 250000.00, 4.50, 12],
            ['Даулет Касымов', 'freelancer@inwork.kz', $password, 'freelancer', 'Full-stack разработчик, 5 лет опыта. PHP, JavaScript, MySQL.', 120000.00, 4.80, 85],
            ['Айгерим Нурланова', 'aigerim@inwork.kz', $password, 'freelancer', 'UI/UX дизайнер. Figma, Adobe, прототипирование.', 75000.00, 4.60, 52],
            ['Нурсултан Жумабаев', 'nursultan@inwork.kz', $password, 'freelancer', 'Мобильная разработка. Flutter, React Native.', 40000.00, 4.20, 23],
            ['Динара Ахметова', 'dinara@inwork.kz', $password, 'freelancer', 'Копирайтер и переводчик. Тексты для бизнеса.', 15000.00, 3.90, 8],
        ];

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, bio, balance, rating, completed_orders) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($users as $user) {
            $stmt->execute($user);
        }
        $messages[] = "Создано 6 тестовых пользователей";

        // 6. Создаём тестовые заказы
        $orders = [
            [2, 'Разработать лендинг для стартапа', 'Нужен современный одностраничный сайт для стартапа в сфере EdTech в Алматы. Адаптивный дизайн, анимации, форма обратной связи. Дизайн-макет предоставлю в Figma.', 'web-development', 75000.00, date('Y-m-d', strtotime('+14 days'))],
            [2, 'Дизайн мобильного приложения', 'Разработать UI/UX дизайн для приложения доставки еды по Казахстану. 15-20 экранов, iOS и Android. Стиль — минимализм, чистый и современный.', 'design', 120000.00, date('Y-m-d', strtotime('+21 days'))],
            [2, 'Написать статьи для блога', 'Требуется 10 SEO-оптимизированных статей для IT-блога. Темы: веб-разработка, облачные технологии, DevOps. По 2000-3000 слов каждая.', 'writing', 40000.00, date('Y-m-d', strtotime('+30 days'))],
            [1, 'Настроить рекламу в Google Ads и Kaspi', 'Нужно настроить и запустить рекламную кампанию в Google Ads и на Kaspi Магазине для интернет-магазина электроники. Бюджет на рекламу отдельно.', 'marketing', 60000.00, date('Y-m-d', strtotime('+7 days'))],
            [1, 'Разработать Telegram-бота', 'Бот для автоматизации работы с клиентами: приём заявок, FAQ, уведомления. Python + aiogram. Интеграция с Google Sheets.', 'web-development', 100000.00, date('Y-m-d', strtotime('+10 days'))],
            [2, 'Создать промо-ролик', 'Короткий промо-ролик для социальных сетей (30-60 сек). Motion-дизайн, анимация логотипа, текстовые подписи. Предоставлю бренд-бук.', 'video', 90000.00, date('Y-m-d', strtotime('+14 days'))],
        ];

        $stmt = $pdo->prepare("INSERT INTO orders (client_id, title, description, category, budget, deadline, status) VALUES (?, ?, ?, ?, ?, ?, 'open')");
        foreach ($orders as $order) {
            $stmt->execute($order);
        }
        $messages[] = "Создано 6 тестовых заказов";
    }

    // 7. Настройки страниц
    $pageCount = $pdo->query("SELECT COUNT(*) FROM page_settings")->fetchColumn();
    if ($pageCount == 0) {
        $pages = [
            ['dashboard',    'Личный кабинет',      'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', 1],
            ['orders',       'Лента заказов',       'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 1],
            ['orders_create','Создание заказа',     'M12 4v16m8-8H4', 1],
            ['my_orders',    'Мои заказы',          'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 1],
            ['profile',      'Профиль',             'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 1],
            ['balance',      'Баланс',              'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 1],
            ['chat',         'Чат / Сообщения',     'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 1],
            ['reviews',      'Отзывы',              'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 1],
            ['register',     'Регистрация',         'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', 1],
        ];

        $stmt = $pdo->prepare("INSERT INTO page_settings (page_key, page_name, page_icon, is_enabled) VALUES (?, ?, ?, ?)");
        foreach ($pages as $p) {
            $stmt->execute($p);
        }
        $messages[] = "Настройки страниц созданы (9 разделов)";
    }

    $messages[] = "Установка завершена!";

} catch (PDOException $e) {
    $errors[] = "Ошибка: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Установка inWork</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                <span class="text-white font-bold text-xl">in</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Установка inWork</h1>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $err): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4">
                        <?= htmlspecialchars($err) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($messages)): ?>
                <div class="space-y-2 mb-6">
                    <?php foreach ($messages as $msg): ?>
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-gray-700"><?= htmlspecialchars($msg) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($errors)): ?>
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Тестовые аккаунты (пароль: <code class="bg-gray-200 px-1.5 py-0.5 rounded text-emerald-700 font-mono">123456</code>)</h3>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="py-1.5 font-medium text-gray-900">Админ</td>
                                <td class="py-1.5 text-gray-600">admin@inwork.kz</td>
                                <td class="py-1.5"><span class="text-xs px-2 py-0.5 bg-red-100 text-red-700 rounded-full">Администратор</span></td>
                            </tr>
                            <tr>
                                <td class="py-1.5 font-medium text-gray-900">Арман Сериков</td>
                                <td class="py-1.5 text-gray-600">client@inwork.kz</td>
                                <td class="py-1.5"><span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">Заказчик</span></td>
                            </tr>
                            <tr>
                                <td class="py-1.5 font-medium text-gray-900">Даулет Касымов</td>
                                <td class="py-1.5 text-gray-600">freelancer@inwork.kz</td>
                                <td class="py-1.5"><span class="text-xs px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full">Исполнитель</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <a href="./" class="block w-full py-3 bg-emerald-600 text-white text-center font-semibold rounded-xl hover:bg-emerald-700 transition">
                    Перейти на сайт
                </a>

                <p class="text-xs text-red-500 text-center mt-4">
                    После установки удалите файл install.php!
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
