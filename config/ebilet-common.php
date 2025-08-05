<?php

return [
    /*
    |--------------------------------------------------------------------------
    | E-Bilet Common Package Configuration
    |--------------------------------------------------------------------------
    |
    | Bu dosya E-Bilet common paketinin tüm konfigürasyon ayarlarını
    | içerir. Bu ayarlar environment variables ile override edilebilir.
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
        'connection_timeout' => env('RABBITMQ_CONNECTION_TIMEOUT', 3),
        'read_write_timeout' => env('RABBITMQ_READ_WRITE_TIMEOUT', 3),
        'heartbeat' => env('RABBITMQ_HEARTBEAT', 60),
        'ssl_options' => [
            'verify_peer' => env('RABBITMQ_SSL_VERIFY_PEER', false),
            'verify_peer_name' => env('RABBITMQ_SSL_VERIFY_PEER_NAME', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Queue kanalları ve ayarları. Her queue için ayrı konfigürasyon
    | yapılabilir.
    |
    */
    'queues' => [
        'default_channel' => env('EBILET_DEFAULT_QUEUE_CHANNEL', 'log-messages'),
        'channels' => [
            'log_messages' => env('EBILET_QUEUE_LOG_CHANNEL', 'log-messages'),
            'metrics' => env('EBILET_QUEUE_METRICS_CHANNEL', 'metrics'),
            'events' => env('EBILET_QUEUE_EVENTS_CHANNEL', 'events'),
            'business_events' => env('EBILET_QUEUE_BUSINESS_EVENTS_CHANNEL', 'business-events'),
            'performance' => env('EBILET_QUEUE_PERFORMANCE_CHANNEL', 'performance'),
        ],
        'settings' => [
            'log_messages' => [
                'durable' => env('EBILET_QUEUE_LOG_DURABLE', true),
                'ttl' => env('EBILET_QUEUE_LOG_TTL', 86400000), // 24 hours
                'max_length' => env('EBILET_QUEUE_LOG_MAX_LENGTH', 10000),
                'auto_delete' => env('EBILET_QUEUE_LOG_AUTO_DELETE', false),
            ],
            'metrics' => [
                'durable' => env('EBILET_QUEUE_METRICS_DURABLE', true),
                'ttl' => env('EBILET_QUEUE_METRICS_TTL', 604800000), // 7 days
                'max_length' => env('EBILET_QUEUE_METRICS_MAX_LENGTH', 50000),
                'auto_delete' => env('EBILET_QUEUE_METRICS_AUTO_DELETE', false),
            ],
            'events' => [
                'durable' => env('EBILET_QUEUE_EVENTS_DURABLE', true),
                'ttl' => env('EBILET_QUEUE_EVENTS_TTL', 2592000000), // 30 days
                'max_length' => env('EBILET_QUEUE_EVENTS_MAX_LENGTH', 100000),
                'auto_delete' => env('EBILET_QUEUE_EVENTS_AUTO_DELETE', false),
            ],
            'business_events' => [
                'durable' => env('EBILET_QUEUE_BUSINESS_EVENTS_DURABLE', true),
                'ttl' => env('EBILET_QUEUE_BUSINESS_EVENTS_TTL', 604800000), // 7 days
                'max_length' => env('EBILET_QUEUE_BUSINESS_EVENTS_MAX_LENGTH', 25000),
                'auto_delete' => env('EBILET_QUEUE_BUSINESS_EVENTS_AUTO_DELETE', false),
            ],
            'performance' => [
                'durable' => env('EBILET_QUEUE_PERFORMANCE_DURABLE', true),
                'ttl' => env('EBILET_QUEUE_PERFORMANCE_TTL', 86400000), // 24 hours
                'max_length' => env('EBILET_QUEUE_PERFORMANCE_MAX_LENGTH', 15000),
                'auto_delete' => env('EBILET_QUEUE_PERFORMANCE_AUTO_DELETE', false),
            ],
        ],
        'delivery_mode' => env('EBILET_QUEUE_DELIVERY_MODE', 2), // Persistent
        'retry_attempts' => env('EBILET_QUEUE_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('EBILET_QUEUE_RETRY_DELAY', 1000), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Genel loglama ayarları. Bu ayarlar tüm loglama işlemleri için
    | temel konfigürasyonu sağlar.
    |
    */
    'logging' => [
        'enabled' => env('EBILET_LOGGING_ENABLED', true),
        'service_name' => env('APP_NAME', 'unknown-service'),
        'service_version' => env('APP_VERSION', '1.0.0'),
        'environment' => env('APP_ENV', 'production'),
        'log_level' => env('EBILET_LOG_LEVEL', 'info'),
        'include_stack_trace' => env('EBILET_INCLUDE_STACK_TRACE', true),
        'max_message_size' => env('EBILET_MAX_MESSAGE_SIZE', 1024 * 1024), // 1MB
        'timestamp_format' => env('EBILET_TIMESTAMP_FORMAT', 'Y-m-d H:i:s'),
        'timezone' => env('EBILET_TIMEZONE', 'UTC'),
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Logging Configuration
    |--------------------------------------------------------------------------
    |
    | HTTP request/response loglama ayarları. Bu ayarlar middleware
    | tarafından kullanılır.
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
            '/.well-known',
            '/api/health',
            '/api/metrics',
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
            'x-real-ip',
            'x-forwarded-proto',
            'x-forwarded-host',
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
            'ssn',
            'social_security_number',
            'passport_number',
        ]),
        'sensitive_response_fields' => env('EBILET_HTTP_LOGGING_SENSITIVE_RESPONSE_FIELDS', [
            'token',
            'access_token',
            'refresh_token',
            'secret',
            'password',
            'credit_card',
            'api_key',
        ]),
        'log_request_body' => env('EBILET_HTTP_LOGGING_REQUEST_BODY', true),
        'log_response_body' => env('EBILET_HTTP_LOGGING_RESPONSE_BODY', false),
        'max_body_size' => env('EBILET_HTTP_LOGGING_MAX_BODY_SIZE', 1024 * 1024), // 1MB
        'slow_request_threshold' => env('EBILET_HTTP_LOGGING_SLOW_THRESHOLD', 2000), // 2 seconds
        'log_headers' => env('EBILET_HTTP_LOGGING_HEADERS', true),
        'log_user_agent' => env('EBILET_HTTP_LOGGING_USER_AGENT', true),
        'log_ip_address' => env('EBILET_HTTP_LOGGING_IP_ADDRESS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | Performans izleme ayarları. Bu ayarlar performans metriklerinin
    | toplanması ve raporlanması için kullanılır.
    |
    */
    'performance' => [
        'enabled' => env('EBILET_PERFORMANCE_MONITORING_ENABLED', true),
        'memory_threshold' => env('EBILET_MEMORY_THRESHOLD', 128 * 1024 * 1024), // 128MB
        'slow_query_threshold' => env('EBILET_SLOW_QUERY_THRESHOLD', 1000), // 1 second
        'external_api_timeout' => env('EBILET_EXTERNAL_API_TIMEOUT', 5000), // 5 seconds
        'cpu_threshold' => env('EBILET_CPU_THRESHOLD', 80), // percentage
        'disk_usage_threshold' => env('EBILET_DISK_USAGE_THRESHOLD', 90), // percentage
        'collect_metrics' => [
            'memory_usage' => env('EBILET_COLLECT_MEMORY_METRICS', true),
            'cpu_usage' => env('EBILET_COLLECT_CPU_METRICS', true),
            'disk_usage' => env('EBILET_COLLECT_DISK_METRICS', true),
            'database_queries' => env('EBILET_COLLECT_DB_METRICS', true),
            'external_api_calls' => env('EBILET_COLLECT_API_METRICS', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Events Configuration
    |--------------------------------------------------------------------------
    |
    | İş olayları için konfigürasyon. Bu ayarlar business event'lerin
    | loglanması için kullanılır.
    |
    */
    'business_events' => [
        'enabled' => env('EBILET_BUSINESS_EVENT_LOGGING', true),
        'events' => [
            'user_registered' => env('EBILET_LOG_USER_REGISTERED', true),
            'user_logged_in' => env('EBILET_LOG_USER_LOGGED_IN', true),
            'user_logged_out' => env('EBILET_LOG_USER_LOGGED_OUT', true),
            'order_created' => env('EBILET_LOG_ORDER_CREATED', true),
            'order_cancelled' => env('EBILET_LOG_ORDER_CANCELLED', true),
            'payment_successful' => env('EBILET_LOG_PAYMENT_SUCCESSFUL', true),
            'payment_failed' => env('EBILET_LOG_PAYMENT_FAILED', true),
            'ticket_booked' => env('EBILET_LOG_TICKET_BOOKED', true),
            'ticket_cancelled' => env('EBILET_LOG_TICKET_CANCELLED', true),
        ],
        'include_user_data' => env('EBILET_BUSINESS_EVENTS_INCLUDE_USER_DATA', false),
        'include_sensitive_data' => env('EBILET_BUSINESS_EVENTS_INCLUDE_SENSITIVE_DATA', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Handling Configuration
    |--------------------------------------------------------------------------
    |
    | Hata yönetimi ayarları. Bu ayarlar exception handling ve
    | error reporting için kullanılır.
    |
    */
    'error_handling' => [
        'log_exceptions' => env('EBILET_LOG_EXCEPTIONS', true),
        'log_errors' => env('EBILET_LOG_ERRORS', true),
        'log_warnings' => env('EBILET_LOG_WARNINGS', true),
        'include_stack_trace' => env('EBILET_ERROR_INCLUDE_STACK_TRACE', true),
        'max_stack_trace_depth' => env('EBILET_MAX_STACK_TRACE_DEPTH', 10),
        'sanitize_error_messages' => env('EBILET_SANITIZE_ERROR_MESSAGES', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Güvenlik ayarları. Bu ayarlar güvenlik ile ilgili loglama
    | işlemleri için kullanılır.
    |
    */
    'security' => [
        'log_failed_logins' => env('EBILET_LOG_FAILED_LOGINS', true),
        'log_successful_logins' => env('EBILET_LOG_SUCCESSFUL_LOGINS', false),
        'log_password_resets' => env('EBILET_LOG_PASSWORD_RESETS', true),
        'log_account_locks' => env('EBILET_LOG_ACCOUNT_LOCKS', true),
        'log_suspicious_activity' => env('EBILET_LOG_SUSPICIOUS_ACTIVITY', true),
        'suspicious_activity_threshold' => env('EBILET_SUSPICIOUS_ACTIVITY_THRESHOLD', 5),
    ],
]; 