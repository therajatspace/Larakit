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

];