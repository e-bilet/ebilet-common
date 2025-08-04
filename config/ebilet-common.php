<?php

return [
    /*
    |--------------------------------------------------------------------------
    | E-Bilet Common Package Configuration
    |--------------------------------------------------------------------------
    |
    | Bu dosya E-Bilet common paketi için konfigürasyon
    | ayarlarını içerir.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Merkezi loglama sistemi ayarları.
    |
    */
    'logging' => [
        'enabled' => env('EBILET_LOGGING_ENABLED', true),
        'service_name' => env('APP_NAME', 'unknown-service'),
        'log_path' => env('LOG_PATH', 'logs'),
        'fallback_logging' => env('EBILET_FALLBACK_LOGGING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Queue sistemi ayarları.
    |
    */
    'queue' => [
        'enabled' => env('EBILET_QUEUE_ENABLED', true),
        'provider' => env('EBILET_QUEUE_PROVIDER', 'rabbitmq'),
        'rabbitmq' => [
            'host' => env('RABBITMQ_HOST', 'localhost'),
            'port' => env('RABBITMQ_PORT', 5672),
            'user' => env('RABBITMQ_USER', 'guest'),
            'password' => env('RABBITMQ_PASSWORD', 'guest'),
            'vhost' => env('RABBITMQ_VHOST', '/'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Logging Configuration
    |--------------------------------------------------------------------------
    |
    | HTTP request/response loglama ayarları.
    |
    */
    'http_logging' => [
        'enabled' => env('EBILET_HTTP_LOGGING_ENABLED', true),
        'log_request_body' => env('EBILET_HTTP_LOGGING_REQUEST_BODY', true),
        'log_response_body' => env('EBILET_HTTP_LOGGING_RESPONSE_BODY', false),
        'max_body_size' => env('EBILET_HTTP_LOGGING_MAX_BODY_SIZE', 1024 * 1024),
        'slow_request_threshold' => env('EBILET_HTTP_LOGGING_SLOW_THRESHOLD', 2000),
        'excluded_paths' => env('EBILET_HTTP_LOGGING_EXCLUDED_PATHS', [
            '/health',
            '/metrics',
            '/favicon.ico',
            '/robots.txt',
            '/.well-known'
        ]),
        'excluded_methods' => env('EBILET_HTTP_LOGGING_EXCLUDED_METHODS', [
            'OPTIONS'
        ]),
        'excluded_domains' => env('EBILET_HTTP_LOGGING_EXCLUDED_DOMAINS', []),
        'sensitive_headers' => env('EBILET_HTTP_LOGGING_SENSITIVE_HEADERS', [
            'authorization',
            'cookie',
            'x-api-key',
            'x-auth-token',
            'x-csrf-token',
            'x-forwarded-for',
            'x-real-ip'
        ]),
        'sensitive_body_fields' => env('EBILET_HTTP_LOGGING_SENSITIVE_BODY_FIELDS', [
            'password',
            'token',
            'secret',
            'api_key',
            'auth_token',
            'refresh_token',
            'access_token',
            'credit_card',
            'ssn'
        ]),
        'sensitive_response_fields' => env('EBILET_HTTP_LOGGING_SENSITIVE_RESPONSE_FIELDS', [
            'token',
            'access_token',
            'refresh_token',
            'secret',
            'password',
            'credit_card'
        ]),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | Performans izleme ayarları.
    |
    */
    'performance' => [
        'enabled' => env('EBILET_PERFORMANCE_LOGGING', true),
        'slow_query_threshold' => env('EBILET_SLOW_QUERY_THRESHOLD', 1.0),
        'slow_request_threshold' => env('EBILET_SLOW_REQUEST_THRESHOLD', 2.0),
        'memory_threshold' => env('EBILET_MEMORY_THRESHOLD', 100 * 1024 * 1024),
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Events
    |--------------------------------------------------------------------------
    |
    | İş olayları loglama ayarları.
    |
    */
    'business_events' => [
        'enabled' => env('EBILET_BUSINESS_EVENT_LOGGING', true),
        'events' => [
            'user_registered',
            'user_logged_in',
            'user_logged_out',
            'order_created',
            'order_cancelled',
            'payment_successful',
            'payment_failed',
            'ticket_booked',
            'ticket_cancelled',
        ],
    ],
]; 