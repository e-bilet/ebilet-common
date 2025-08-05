# E-Bilet Common Package Configuration

Bu dokümantasyon, E-Bilet Common paketinin konfigürasyon ayarlarını açıklar.

## Kurulum

### 1. Config Dosyalarını Yayınlama

Paket config dosyalarını yayınlamak için aşağıdaki komutu çalıştırın:

```bash
php artisan vendor:publish --tag=ebilet-common-config
```

Bu komut aşağıdaki dosyaları `config/` klasörüne kopyalar:
- `config/ebilet-common.php`
- `config/ebilet-logging.php`

### 2. Ayrı Config Dosyalarını Yayınlama

Sadece belirli config dosyalarını yayınlamak istiyorsanız:

```bash
# Sadece ana config dosyasını yayınla
php artisan vendor:publish --tag=ebilet-common-main-config

# Sadece logging config dosyasını yayınla
php artisan vendor:publish --tag=ebilet-common-logging-config
```

## Konfigürasyon Seçenekleri

### RabbitMQ Ayarları

```php
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
```

### Queue Ayarları

```php
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
        // ... diğer queue ayarları
    ],
    'delivery_mode' => env('EBILET_QUEUE_DELIVERY_MODE', 2), // Persistent
    'retry_attempts' => env('EBILET_QUEUE_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('EBILET_QUEUE_RETRY_DELAY', 1000), // milliseconds
],
```

### Logging Ayarları

```php
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
```

### HTTP Logging Ayarları

```php
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
    'log_request_body' => env('EBILET_HTTP_LOGGING_REQUEST_BODY', true),
    'log_response_body' => env('EBILET_HTTP_LOGGING_RESPONSE_BODY', false),
    'max_body_size' => env('EBILET_HTTP_LOGGING_MAX_BODY_SIZE', 1024 * 1024), // 1MB
    'slow_request_threshold' => env('EBILET_HTTP_LOGGING_SLOW_THRESHOLD', 2000), // 2 seconds
    'log_headers' => env('EBILET_HTTP_LOGGING_HEADERS', true),
    'log_user_agent' => env('EBILET_HTTP_LOGGING_USER_AGENT', true),
    'log_ip_address' => env('EBILET_HTTP_LOGGING_IP_ADDRESS', true),
],
```

### Performance Monitoring Ayarları

```php
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
```

### Business Events Ayarları

```php
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
```

### Error Handling Ayarları

```php
'error_handling' => [
    'log_exceptions' => env('EBILET_LOG_EXCEPTIONS', true),
    'log_errors' => env('EBILET_LOG_ERRORS', true),
    'log_warnings' => env('EBILET_LOG_WARNINGS', true),
    'include_stack_trace' => env('EBILET_ERROR_INCLUDE_STACK_TRACE', true),
    'max_stack_trace_depth' => env('EBILET_MAX_STACK_TRACE_DEPTH', 10),
    'sanitize_error_messages' => env('EBILET_SANITIZE_ERROR_MESSAGES', true),
],
```

### Security Ayarları

```php
'security' => [
    'log_failed_logins' => env('EBILET_LOG_FAILED_LOGINS', true),
    'log_successful_logins' => env('EBILET_LOG_SUCCESSFUL_LOGINS', false),
    'log_password_resets' => env('EBILET_LOG_PASSWORD_RESETS', true),
    'log_account_locks' => env('EBILET_LOG_ACCOUNT_LOCKS', true),
    'log_suspicious_activity' => env('EBILET_LOG_SUSPICIOUS_ACTIVITY', true),
    'suspicious_activity_threshold' => env('EBILET_SUSPICIOUS_ACTIVITY_THRESHOLD', 5),
],
```

## Environment Variables

Aşağıdaki environment variable'ları `.env` dosyanızda tanımlayabilirsiniz:

### RabbitMQ
```
RABBITMQ_HOST=localhost
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/
RABBITMQ_CONNECTION_TIMEOUT=3
RABBITMQ_READ_WRITE_TIMEOUT=3
RABBITMQ_HEARTBEAT=60
RABBITMQ_SSL_VERIFY_PEER=false
RABBITMQ_SSL_VERIFY_PEER_NAME=false
```

### Queue
```
EBILET_DEFAULT_QUEUE_CHANNEL=log-messages
EBILET_QUEUE_LOG_CHANNEL=log-messages
EBILET_QUEUE_METRICS_CHANNEL=metrics
EBILET_QUEUE_EVENTS_CHANNEL=events
EBILET_QUEUE_BUSINESS_EVENTS_CHANNEL=business-events
EBILET_QUEUE_PERFORMANCE_CHANNEL=performance
EBILET_QUEUE_DELIVERY_MODE=2
EBILET_QUEUE_RETRY_ATTEMPTS=3
EBILET_QUEUE_RETRY_DELAY=1000
```

### Logging
```
EBILET_LOGGING_ENABLED=true
EBILET_LOG_LEVEL=info
EBILET_INCLUDE_STACK_TRACE=true
EBILET_MAX_MESSAGE_SIZE=1048576
EBILET_TIMESTAMP_FORMAT=Y-m-d H:i:s
EBILET_TIMEZONE=UTC
```

### HTTP Logging
```
EBILET_HTTP_LOGGING_ENABLED=true
EBILET_HTTP_LOGGING_ENDPOINTS=*
EBILET_HTTP_LOGGING_REQUEST_BODY=true
EBILET_HTTP_LOGGING_RESPONSE_BODY=false
EBILET_HTTP_LOGGING_MAX_BODY_SIZE=1048576
EBILET_HTTP_LOGGING_SLOW_THRESHOLD=2000
EBILET_HTTP_LOGGING_HEADERS=true
EBILET_HTTP_LOGGING_USER_AGENT=true
EBILET_HTTP_LOGGING_IP_ADDRESS=true
```

### Performance
```
EBILET_PERFORMANCE_MONITORING_ENABLED=true
EBILET_MEMORY_THRESHOLD=134217728
EBILET_SLOW_QUERY_THRESHOLD=1000
EBILET_EXTERNAL_API_TIMEOUT=5000
EBILET_CPU_THRESHOLD=80
EBILET_DISK_USAGE_THRESHOLD=90
EBILET_COLLECT_MEMORY_METRICS=true
EBILET_COLLECT_CPU_METRICS=true
EBILET_COLLECT_DISK_METRICS=true
EBILET_COLLECT_DB_METRICS=true
EBILET_COLLECT_API_METRICS=true
```

### Business Events
```
EBILET_BUSINESS_EVENT_LOGGING=true
EBILET_LOG_USER_REGISTERED=true
EBILET_LOG_USER_LOGGED_IN=true
EBILET_LOG_USER_LOGGED_OUT=true
EBILET_LOG_ORDER_CREATED=true
EBILET_LOG_ORDER_CANCELLED=true
EBILET_LOG_PAYMENT_SUCCESSFUL=true
EBILET_LOG_PAYMENT_FAILED=true
EBILET_LOG_TICKET_BOOKED=true
EBILET_LOG_TICKET_CANCELLED=true
EBILET_BUSINESS_EVENTS_INCLUDE_USER_DATA=false
EBILET_BUSINESS_EVENTS_INCLUDE_SENSITIVE_DATA=false
```

### Error Handling
```
EBILET_LOG_EXCEPTIONS=true
EBILET_LOG_ERRORS=true
EBILET_LOG_WARNINGS=true
EBILET_ERROR_INCLUDE_STACK_TRACE=true
EBILET_MAX_STACK_TRACE_DEPTH=10
EBILET_SANITIZE_ERROR_MESSAGES=true
```

### Security
```
EBILET_LOG_FAILED_LOGINS=true
EBILET_LOG_SUCCESSFUL_LOGINS=false
EBILET_LOG_PASSWORD_RESETS=true
EBILET_LOG_ACCOUNT_LOCKS=true
EBILET_LOG_SUSPICIOUS_ACTIVITY=true
EBILET_SUSPICIOUS_ACTIVITY_THRESHOLD=5
```

## Kullanım

### Config Manager ile Config Değerlerine Erişim

```php
use Ebilet\Common\Services\ConfigManager;

// Belirli bir config değerini al
$rabbitMQConfig = ConfigManager::getRabbitMQConfig();
$loggingConfig = ConfigManager::getLoggingConfig();

// Belirli bir değeri al
$logLevel = ConfigManager::get('logging.log_level', 'info');

// Bir özelliğin aktif olup olmadığını kontrol et
$isLoggingEnabled = ConfigManager::isEnabled('logging.enabled');

// Tüm config'i al
$allConfig = ConfigManager::getAllConfig();

// Config'i doğrula
$errors = ConfigManager::validateConfig();

// Environment'a özel config al
$productionConfig = ConfigManager::getConfigForEnvironment('production');
```

### Config Facade ile Erişim

```php
use Ebilet\Common\Facades\Config;

// Config değerlerine erişim
$rabbitMQConfig = Config::getRabbitMQConfig();
$logLevel = Config::get('logging.log_level', 'info');
$isEnabled = Config::isEnabled('logging.enabled');
```

## Middleware Kullanımı

HTTP logging middleware'ini kullanmak için:

```php
// routes/web.php veya routes/api.php
Route::middleware(['ebilet.logging'])->group(function () {
    // Bu route'lar HTTP logging ile izlenir
    Route::get('/api/users', 'UserController@index');
});
```

## Environment'a Göre Konfigürasyon

Farklı environment'lar için farklı konfigürasyonlar:

### Development
```env
EBILET_LOG_LEVEL=debug
EBILET_HTTP_LOGGING_RESPONSE_BODY=true
EBILET_INCLUDE_STACK_TRACE=true
```

### Staging
```env
EBILET_LOG_LEVEL=info
EBILET_HTTP_LOGGING_RESPONSE_BODY=false
EBILET_INCLUDE_STACK_TRACE=true
```

### Production
```env
EBILET_LOG_LEVEL=warning
EBILET_HTTP_LOGGING_RESPONSE_BODY=false
EBILET_INCLUDE_STACK_TRACE=false
```

## Güvenlik Notları

1. **Hassas Veriler**: `sensitive_headers`, `sensitive_body_fields` ve `sensitive_response_fields` ayarlarını kullanarak hassas verilerin loglanmasını engelleyin.

2. **Environment Variables**: Production ortamında hassas bilgileri environment variable'lar ile yönetin.

3. **Log Seviyeleri**: Production ortamında `log_level`'ı `warning` veya `error` olarak ayarlayın.

4. **Response Body Logging**: Production ortamında `log_response_body`'yi `false` olarak ayarlayın.

## Sorun Giderme

### Config Dosyaları Yayınlanmıyor
```bash
# Cache'i temizle
php artisan config:clear
php artisan cache:clear

# Config dosyalarını yeniden yayınla
php artisan vendor:publish --tag=ebilet-common-config --force
```

### Config Değerleri Okunmuyor
```php
// Config değerlerini kontrol et
dd(ConfigManager::getAllConfig());

// Environment variable'ları kontrol et
dd($_ENV);
```

### RabbitMQ Bağlantı Sorunu
```php
// RabbitMQ config'ini kontrol et
dd(ConfigManager::getRabbitMQConfig());

// Bağlantı ayarlarını kontrol et
dd([
    'host' => env('RABBITMQ_HOST'),
    'port' => env('RABBITMQ_PORT'),
    'user' => env('RABBITMQ_USER'),
]);
``` 