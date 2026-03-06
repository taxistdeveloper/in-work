<?php

$config = [
    'host'     => 'localhost',
    'port'     => '3306',
    'database' => 'inwork',
    'username' => 'root',
    'password' => 'root',
    'charset'  => 'utf8mb4',
];

if (file_exists(__DIR__ . '/database.production.php')) {
    $config = array_merge($config, require __DIR__ . '/database.production.php');
}

return $config;
