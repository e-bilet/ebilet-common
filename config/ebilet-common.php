<?php

return [
    /*
    |--------------------------------------------------------------------------
    | E-Bilet Common Configuration
    |--------------------------------------------------------------------------
    |
    | Bu dosya E-Bilet common paketinin konfigürasyon ayarlarını içerir.
    | Environment variables ile override edilebilir.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | RabbitMQ Configuration
    |--------------------------------------------------------------------------
    |
    | RabbitMQ bağlantı ayarları. Environment variables ile override edilebilir.
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
        'heartbeat' => env('RABBITMQ_HEARTBEAT', 0),
    ],

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
        'log_channel' => env('EBILET_LOG_CHANNEL', 'log-messages'),
        'metrics_channel' => env('EBILET_METRICS_CHANNEL', 'metrics'),
        'events_channel' => env('EBILET_EVENTS_CHANNEL', 'events'),
        
        // Local file logging
        'local_logging' => env('EBILET_LOCAL_LOGGING', true),
        'log_path' => env('EBILET_LOG_PATH', 'logs'),
        'log_level' => env('EBILET_LOG_LEVEL', 'info'),
        
        // Queue settings
        'queue_durable' => env('EBILET_QUEUE_DURABLE', true),
        'queue_auto_delete' => env('EBILET_QUEUE_AUTO_DELETE', false),
        'queue_exclusive' => env('EBILET_QUEUE_EXCLUSIVE', false),
        
        // Message TTL (milliseconds)
        'log_message_ttl' => env('EBILET_LOG_MESSAGE_TTL', 86400000), // 24 hours
        'metrics_message_ttl' => env('EBILET_METRICS_MESSAGE_TTL', 604800000), // 7 days
        'events_message_ttl' => env('EBILET_EVENTS_MESSAGE_TTL', 2592000000), // 30 days
        
        // Queue max length
        'log_queue_max_length' => env('EBILET_LOG_QUEUE_MAX_LENGTH', 10000),
        'metrics_queue_max_length' => env('EBILET_METRICS_QUEUE_MAX_LENGTH', 50000),
        'events_queue_max_length' => env('EBILET_EVENTS_QUEUE_MAX_LENGTH', 100000),
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
        
        // Excluded paths, methods, and domains
        'excluded_paths' => explode(',', env('EBILET_HTTP_LOGGING_EXCLUDED_PATHS', '/health,/metrics,/favicon.ico,/robots.txt,/.well-known')),
        'excluded_methods' => explode(',', env('EBILET_HTTP_LOGGING_EXCLUDED_METHODS', 'OPTIONS')),
        'excluded_domains' => explode(',', env('EBILET_HTTP_LOGGING_EXCLUDED_DOMAINS', '')),
        
        // Sensitive data handling
        'sensitive_headers' => explode(',', env('EBILET_HTTP_LOGGING_SENSITIVE_HEADERS', 'authorization,cookie,x-api-key,x-auth-token,x-csrf-token,x-forwarded-for,x-real-ip')),
        'sensitive_body_fields' => explode(',', env('EBILET_HTTP_LOGGING_SENSITIVE_BODY_FIELDS', 'password,token,secret,api_key,auth_token,refresh_token,access_token,credit_card,ssn')),
        'sensitive_response_fields' => explode(',', env('EBILET_HTTP_LOGGING_SENSITIVE_RESPONSE_FIELDS', 'token,access_token,refresh_token,secret,password,credit_card')),
        
        // Body logging settings
        'log_request_body' => env('EBILET_HTTP_LOGGING_REQUEST_BODY', true),
        'log_response_body' => env('EBILET_HTTP_LOGGING_RESPONSE_BODY', false),
        'max_body_size' => env('EBILET_HTTP_LOGGING_MAX_BODY_SIZE', 1024 * 1024), // 1MB
        
        // Performance settings
        'slow_request_threshold' => env('EBILET_HTTP_LOGGING_SLOW_THRESHOLD', 2000), // 2 seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | Performance izleme ayarları.
    |
    */
    'performance' => [
        'enabled' => env('EBILET_PERFORMANCE_MONITORING', true),
        'memory_threshold' => env('EBILET_MEMORY_THRESHOLD', 128 * 1024 * 1024), // 128MB
        'slow_query_threshold' => env('EBILET_SLOW_QUERY_THRESHOLD', 1000), // 1 second
        'external_api_timeout' => env('EBILET_EXTERNAL_API_TIMEOUT', 5000), // 5 seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Handling
    |--------------------------------------------------------------------------
    |
    | Hata yönetimi ayarları.
    |
    */
    'error_handling' => [
        'log_errors' => env('EBILET_LOG_ERRORS', true),
        'log_exceptions' => env('EBILET_LOG_EXCEPTIONS', true),
        'log_fatal_errors' => env('EBILET_LOG_FATAL_ERRORS', true),
        'max_error_log_size' => env('EBILET_MAX_ERROR_LOG_SIZE', 10 * 1024 * 1024), // 10MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    |
    | Güvenlik ayarları.
    |
    */
    'security' => [
        'log_security_events' => env('EBILET_LOG_SECURITY_EVENTS', true),
        'log_authentication' => env('EBILET_LOG_AUTHENTICATION', true),
        'log_authorization' => env('EBILET_LOG_AUTHORIZATION', true),
        'log_suspicious_activity' => env('EBILET_LOG_SUSPICIOUS_ACTIVITY', true),
    ],
]; 