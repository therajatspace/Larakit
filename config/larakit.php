<?php

return [

    'seo' => [

        'enabled' => true,

        'defaults' => [
            'title' => null,
            'description' => null,
            'robots' => null,
        ],

        'head' => [

            'favicon' => [
                'enabled' => false,
                'url' => null,
                'type' => null,
            ],

            'apple_touch_icon' => [
                'enabled' => false,
                'url' => null,
            ],

            'manifest' => [
                'enabled' => false,
                'url' => null,
            ],

        ],

        'schema' => [
            'auto' => true,
        ],

        'organization' => [
            'name' => null,
            'url' => null,
            'logo' => null,
            'same_as' => [],
        ],

        'website' => [
            'name' => null,
            'url' => null,
            'description' => null,
        ],

    ],

    'auth' => [

        'enabled' => true,

        'route_mode' => 'dedicated',

        'user' => [
            'model' => null,
        ],

        'profiles' => [],

        'delegation' => [
            'enabled' => true,
            'roles' => [],
            'permissions' => [],
        ],

        'rate_limit' => [
            'enabled' => true,

            'account' => [
                'max_attempts' => 5,
                'decay_seconds' => 60,
            ],

            'ip' => [
                'max_attempts' => 30,
                'decay_seconds' => 60,
            ],
        ],

        'password_reset' => [
            'enabled' => true,
            'broker' => null,
        ],

        'email_verification' => [

            'enabled' => true,

            'route_mode' => 'dedicated',

            'expiration' => 60,

            'throttle' => 60,

        ],
    ],

    'admin' => [

        'enabled' => true,

        'route_prefix' => 'admin',

        'middleware' => [
            'web',
        ],

        'access' => [

            'permission' => 'admin.access',

        ],

        'users' => [

            'account' => [

                'driver' => null,

                'status_attribute' => null,

            ],

            'password' => [

                'state_driver' => null,

                'force_change_attribute' => null,

            ],

            'listing' => [

                'searchable' => [
                    'name',
                    'email',
                ],

                'sortable' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],

                'default_sort' => 'created_at',

                'default_direction' => 'desc',

                'per_page' => 25,

                'max_per_page' => 100,

            ],

            'identity' => [

                'display_name_attribute' => 'name',

                'email_attribute' => 'email',

            ],

        ],

        'modules' => [

            'dashboard' => true,

            'users' => true,

            'authorization' => true,

            'website_health' => true,

            'seo_health' => true,

            'traffic' => true,

            'audit' => true,

        ],

    ],

];