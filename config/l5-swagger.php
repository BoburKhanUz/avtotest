<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Avto Test Imtihon API',
                'version' => '1.0.0',
                'description' => 'Avto Test Imtihon loyihasi uchun API hujjatlari',
            ],
            'routes' => [
                'api' => 'api/documentation',
                'docs' => 'docs',
                'oauth2_callback' => 'api/oauth2-callback',
            ],
            'paths' => [
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'annotations' => [base_path('app')],
                'docs' => storage_path('api-docs'),
                'excludes' => [],
                'base' => env('APP_URL', 'http://localhost'),
            ],
        ],
    ],
    'generate_always' => true,
    'swagger_version' => '3.0.0',
];