<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $code ?> — inWork</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="text-8xl font-extrabold text-gray-200 mb-4"><?= $code ?></div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($message) ?></h1>
        <p class="text-gray-500 mb-8">Страница, которую вы ищете, не существует или была перемещена.</p>
        <a href="/" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            На главную
        </a>
    </div>
</body>
</html>
