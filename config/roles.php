<?php

return [

    'admin' => [
        'permissions' => [
            'dashboard.view',
            'admins.view',
            'admins.create',
            'admins.edit',
            'admins.delete',
            'authors.view',
            'authors.create',
            'authors.edit',
            'authors.delete',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'books.view',
            'books.create',
            'books.edit',
            'books.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'app-settings.view',
            'app-settings.create',
            'app-settings.edit',
            'app-settings.delete',
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
