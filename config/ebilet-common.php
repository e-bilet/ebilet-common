<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RabbitMQ Configuration
    |--------------------------------------------------------------------------
    |
    | RabbitMQ bağlantı ayarları
    |
    */
    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST', 'localhost'),
        'port' => env('RABBITMQ_PORT', 5672),
        'user' => env('RABBITMQ_USER', 'guest'),
        'password' => env('RABBITMQ_PASSWORD', 'guest'),
        'vhost' => env('RABBITMQ_VHOST', '/'),
        'connection_timeout' => env('RABBITMQ_CONNECTION_TIMEOUT', 3),
        'read_write_timeout' => env('RABBITMQ_READ_WRITE_TIMEOUT', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Queue kanalları ve ayarları
    |
    */
    'queues' => [
        'channels' => [
            'log_messages' => env('EBILET_QUEUE_LOG_CHANNEL', 'log-messages'),
            'metrics' => env('EBILET_QUEUE_METRICS_CHANNEL', 'metrics'),
            'events' => env('EBILET_QUEUE_EVENTS_CHANNEL', 'events'),
        ],
        'settings' => [
            'log_messages' => [
                'durable' => env('EBILET_QUEUE_LOG_DURABLE', true),
                'ttl' => env('EBILET_QUEUE_LOG_TTL', 86400000), // 24 hours
                'max_length' => env('EBILET_QUEUE_LOG_MAX_LENGTH', 10000),
            ],
            'metrics' => [
                'durable' => env('EBILET_QUEUE_METRICS_DURABLE', true),
                'ttl' => env('EBILET_QUEUE_METRICS_TTL', 604800000), // 7 days
                'max_length' => env('EBILET_QUEUE_METRICS_MAX_LENGTH', 50000),
            ],
            'events' => [
                'durable' => env('EBILET_QUEUE_EVENTS_DURABLE', true),
                'ttl' => env('EBILET_QUEUE_EVENTS_TTL', 2592000000), // 30 days
                'max_length' => env('EBILET_QUEUE_EVENTS_MAX_LENGTH', 100000),
            ],
        ],
        'delivery_mode' => env('EBILET_QUEUE_DELIVERY_MODE', 2), // Persistent
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Genel loglama ayarları
    |
    */
    'logging' => [
        'enabled' => env('EBILET_LOGGING_ENABLED', true),
        'service_name' => env('APP_NAME', 'unknown-service'),
        'log_level' => env('EBILET_LOG_LEVEL', 'info'),
        'include_stack_trace' => env('EBILET_INCLUDE_STACK_TRACE', true),
        'max_message_size' => env('EBILET_MAX_MESSAGE_SIZE', 1024 * 1024), // 1MB
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Logging Configuration
    |--------------------------------------------------------------------------
    |
    | HTTP request/response loglama ayarları
    |
    */
    'http_logging' => [
        'enabled' => env('EBILET_HTTP_LOGGING_ENABLED', true),
        'endpoints' => env('EBILET_HTTP_LOGGING_ENDPOINTS', '*'), // '*' for all, or comma-separated list
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
        'log_request_body' => env('EBILET_HTTP_LOGGING_REQUEST_BODY', true),
        'log_response_body' => env('EBILET_HTTP_LOGGING_RESPONSE_BODY', false),
        'max_body_size' => env('EBILET_HTTP_LOGGING_MAX_BODY_SIZE', 1024 * 1024), // 1MB
        'slow_request_threshold' => env('EBILET_HTTP_LOGGING_SLOW_THRESHOLD', 2000), // 2 seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | Performans izleme ayarları
    |
    */
    'performance' => [
        'enabled' => env('EBILET_PERFORMANCE_MONITORING_ENABLED', true),
        'memory_threshold' => env('EBILET_MEMORY_THRESHOLD', 128 * 1024 * 1024), // 128MB
        'slow_query_threshold' => env('EBILET_SLOW_QUERY_THRESHOLD', 1000), // 1 second
        'external_api_timeout' => env('EBILET_EXTERNAL_API_TIMEOUT', 5000), // 5 seconds
    ],
]; 