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
        'user' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],

        'customer' => [
            'driver' => 'jwt',
            'provider' => 'customers'
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class
        ],
        'customers' => [
            'driver' => 'eloquent',
            'model' => \App\Models\Customer::class
        ]
    ]
];
