<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

try {
    session_start();

    define('ROOT_PATH', dirname(__DIR__));
    define('APP_PATH', ROOT_PATH . '/app');
    define('VIEW_PATH', ROOT_PATH . '/views');

    $appConfig = require ROOT_PATH . '/config/app.php';
    if (!empty($appConfig['is_production']) && file_exists(ROOT_PATH . '/config/app.production.php')) {
        $appConfig = array_merge($appConfig, require ROOT_PATH . '/config/app.production.php');
    }
    define('ASSET_PATH', ($appConfig['base_path'] ?? '') !== '' ? dirname($appConfig['base_path']) . '/assets' : '/assets');
    define('APP_NAME', $appConfig['name']);
    $appUrl = $appConfig['url'];
    $appUrl = preg_replace('#in-work@#', 'in-work.', $appUrl);
    define('APP_URL', rtrim($appUrl, '/'));
    define('PLATFORM_FEE', $appConfig['platform_fee']);

    if ($appConfig['debug']) {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
    }

    date_default_timezone_set($appConfig['timezone']);

    spl_autoload_register(function (string $class) {
        $map = [
            'Core\\'       => ROOT_PATH . '/core/',
            'App\\Controllers\\' => APP_PATH . '/controllers/',
            'App\\Models\\'      => APP_PATH . '/models/',
            'App\\Services\\'    => APP_PATH . '/services/',
            'App\\Middlewares\\'  => APP_PATH . '/middlewares/',
            'App\\Helpers\\'     => APP_PATH . '/helpers/',
        ];

        foreach ($map as $prefix => $baseDir) {
            if (strpos($class, $prefix) === 0) {
                $relativeClass = substr($class, strlen($prefix));
                $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
                if (file_exists($file)) {
                    require $file;
                    return;
                }
            }
        }
    });

    require ROOT_PATH . '/app/helpers/functions.php';

    $router = new Core\Router();

    require ROOT_PATH . '/config/routes.php';

    $method = $_SERVER['REQUEST_METHOD'];
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($requestUri, PHP_URL_PATH);
    if ($path === false || $path === '') {
        $path = '/';
    }
    $path = '/' . trim($path, "/ \t\n\r");

    $basePath = $appConfig['base_path'] ?? '/in-work/public';
    $basePath = rtrim($basePath, '/');
    if ($basePath !== '' && (strpos($path, $basePath) === 0)) {
        $suffix = substr($path, strlen($basePath));
        $path = ($suffix === '' || $suffix === '/') ? '/' : ($suffix ?: '/');
    }
    if ($path !== '/') {
        $path = rtrim($path, '/') ?: '/';
    }

    $router->resolve($method, $path);

} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Ошибка приложения</h1>';
    echo '<p><strong>' . htmlspecialchars($e->getMessage()) . '</strong></p>';
    echo '<p>Файл: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}
