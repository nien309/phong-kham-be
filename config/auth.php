<?php

return [

    'defaults' => [
        'guard' => 'api', // ← đổi 'web' → 'api' nếu bạn dùng API
        'passwords' => 'taikhoans',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'taikhoans',
        ],

        'api' => [
            'driver' => 'sanctum',
            'provider' => 'taikhoans',
        ],
    ],

    'providers' => [
        'taikhoans' => [
            'driver' => 'eloquent',
            'model' => App\Models\TaiKhoan::class,
        ],
    ],

    'passwords' => [
        'taikhoans' => [
            'provider' => 'taikhoans',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
