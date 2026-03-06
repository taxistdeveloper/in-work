<?php

return [
    'name'            => 'inWork',
    'url'             => 'http://localhost/projects/inwork/public',
    'debug'           => true,
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
