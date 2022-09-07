<?php

return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'user' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class,
            'table' => 'user'
        ]
    ]
];
