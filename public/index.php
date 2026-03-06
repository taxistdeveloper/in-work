<?php

session_start();

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', ROOT_PATH . '/views');

$appConfig = require ROOT_PATH . '/config/app.php';
define('ASSET_PATH', ($appConfig['base_path'] ?? '') ? dirname($appConfig['base_path']) . '/assets' : '/in-work/assets');
define('APP_NAME', $appConfig['name']);
define('APP_URL', $appConfig['url']);
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
        if (str_starts_with($class, $prefix)) {
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
$uri = $_SERVER['REQUEST_URI'];

$basePath = $appConfig['base_path'] ?? '/in-work/public';
$uri = substr($uri, strlen($basePath)) ?: '/';

$router->resolve($method, $uri);
