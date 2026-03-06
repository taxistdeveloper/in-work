<?php
/**
 * Временный скрипт для диагностики. Удалите после отладки!
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Диагностика</h2>";
echo "<p>PHP: " . PHP_VERSION . "</p>";
echo "<p>Host: " . ($_SERVER['HTTP_HOST'] ?? '?') . "</p>";

try {
    define('ROOT_PATH', dirname(__DIR__));
    $appConfig = require ROOT_PATH . '/config/app.php';
    echo "<p>Config OK. URL: " . ($appConfig['url'] ?? '?') . "</p>";
    
    if (file_exists(ROOT_PATH . '/config/app.production.php')) {
        $prod = require ROOT_PATH . '/config/app.production.php';
        $appConfig = array_merge($appConfig, $prod);
        echo "<p>Production config merged. URL: " . ($appConfig['url'] ?? '?') . "</p>";
    }
    
    require ROOT_PATH . '/config/database.php';
    echo "<p>Database config OK</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>Ошибка: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
