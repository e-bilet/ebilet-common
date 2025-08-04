<?php

return [
    /*
    |--------------------------------------------------------------------------
    | E-Bilet Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Bu dosya E-Bilet merkezi loglama sistemi için konfigürasyon
    | ayarlarını içerir.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | RabbitMQ Configuration
    |--------------------------------------------------------------------------
    |
    | RabbitMQ bağlantı ayarları. Bu ayarlar environment variables
    | ile override edilebilir.
    |
    */
    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST', 'localhost'),
        'port' => env('RABBITMQ_PORT', 5672),
        'user' => env('RABBITMQ_USER', 'guest'),
        'password' => env('RABBITMQ_PASSWORD', 'guest'),
        'vhost' => env('RABBITMQ_VHOST', '/'),
        'exchange' => env('RABBITMQ_EXCHANGE', 'log-messages'),
        'routing_key' => env('RABBITMQ_ROUTING_KEY', 'logs'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Configuration
    |--------------------------------------------------------------------------
    |
    | Uygulama seviyesi loglama ayarları.
    |
    */
    'app' => [
        'name' => env('APP_NAME', 'unknown-service'),
        'log_path' => env('LOG_PATH', 'logs'),
        'enabled' => env('EBILET_LOGGING_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Features
    |--------------------------------------------------------------------------
    |
    | Hangi loglama özelliklerinin aktif olduğunu belirler.
    |
    */
    'features' => [
        'performance_logging' => env('EBILET_PERFORMANCE_LOGGING', true),
        'http_logging' => env('EBILET_HTTP_LOGGING', true),
        'business_event_logging' => env('EBILET_BUSINESS_EVENT_LOGGING', true),
        'fallback_logging' => env('EBILET_FALLBACK_LOGGING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sensitive Data Sanitization
    |--------------------------------------------------------------------------
    |
    | Loglarda gizlenmesi gereken hassas veri alanları.
    |
    */
    'sanitization' => [
        'headers' => [
            'authorization',
            'cookie',
            'x-api-key',
            'x-auth-token',
            'x-csrf-token',
        ],
        'body_fields' => [
            'password',
            'token',
            'secret',
            'api_key',
            'auth_token',
            'refresh_token',
            'access_token',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Levels
    |--------------------------------------------------------------------------
    |
    | Hangi log seviyelerinin RabbitMQ'ya gönderileceği.
    |
    */
    'levels' => [
        'rabbitmq' => [
            'emergency',
            'alert',
            'critical',
            'error',
            'warning',
            'notice',
            'info',
        ],
        'file' => [
            'emergency',
            'alert',
            'critical',
            'error',
            'warning',
            'notice',
            'info',
            'debug',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Thresholds
    |--------------------------------------------------------------------------
    |
    | Performans metrikleri için eşik değerleri.
    |
    */
    'performance' => [
        'slow_query_threshold' => env('EBILET_SLOW_QUERY_THRESHOLD', 1.0), // seconds
        'slow_request_threshold' => env('EBILET_SLOW_REQUEST_THRESHOLD', 2.0), // seconds
        'memory_threshold' => env('EBILET_MEMORY_THRESHOLD', 100 * 1024 * 1024), // 100MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Events
    |--------------------------------------------------------------------------
    |
    | İş olayları için konfigürasyon.
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