<?php

return [

    'admin' => [
        'permissions' => [
            'dashboard.view',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'books.view',
            'books.create',
            'books.edit',
            'books.delete',
        ],
    ],

    'author' => [
        'permissions' => [
            'dashboard.view',
            'books.view',
            'books.create',
            'books.edit',
        ],
    ],

    'user' => [
        'permissions' => [
            'dashboard.view',
            'books.view',
        ],
    ],

];
