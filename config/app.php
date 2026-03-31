<?php

$host = $_SERVER['HTTP_HOST'] ?? '';
$isProduction = ($host === 'in-work.krg-ktsk.kz')
    || (strpos($host, 'in-work') !== false && strpos($host, 'krg-ktsk.kz') !== false);

// Явная настройка для продакшена (раскомментируйте на сервере, если автоопределение не сработало)
// 'production_url' => 'https://in-work.krg-ktsk.kz',
// 'production_base_path' => '',

$prodUrl = 'https://in-work.krg-ktsk.kz';  // ТОЧКА, не @
$prodBasePath = '';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$localHost = $host !== '' ? $host : 'localhost';
$localUrl = $scheme . '://' . $localHost . '/in-work/public';

return [
    'is_production'   => $isProduction,
    'name'            => 'inWork',
    'url'             => $isProduction ? $prodUrl : $localUrl,
    'base_path'       => $isProduction ? $prodBasePath : '/in-work/public',
    'debug'           => !$isProduction,
    'timezone'        => 'Asia/Almaty',
    'platform_fee'    => 0.10, // 10% комиссия
    'min_balance'     => 0,
    'per_page'        => 20,
    'categories'      => [
        'web-development'    => 'Веб-разработка',
        'mobile-development' => 'Мобильная разработка',
        'design'             => 'Дизайн и креатив',
        'writing'            => 'Тексты и переводы',
        'marketing'          => 'Маркетинг',
        'video'              => 'Видео и анимация',
        'music'              => 'Музыка и аудио',
        'data'               => 'Данные и аналитика',
        'admin'              => 'Администрирование',
        'other'              => 'Другое',
    ],
];
