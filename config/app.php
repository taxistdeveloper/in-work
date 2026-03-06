<?php

$isProduction = (($_SERVER['HTTP_HOST'] ?? '') === 'in-work.krg-ktsk.kz');

return [
    'name'            => 'inWork',
    'url'             => $isProduction ? 'https://in-work.krg-ktsk.kz' : 'http://localhost/in-work/public',
    'base_path'       => $isProduction ? '' : '/in-work/public',  // пусто = DocumentRoot = public
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
