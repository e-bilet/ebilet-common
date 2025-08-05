# E-Bilet Common Package

E-Bilet mikroservisleri için merkezi loglama ve ortak işlevler paketi. Laravel 12+ ile uyumlu, config-based architecture ve endpoint-specific logging özellikleri ile.

## 🚀 Özellikler

- **🔧 Config-Based Architecture**: Tüm ayarlar config dosyasından yönetilir
- **🎯 Endpoint-Specific Logging**: Belirli endpoint'lerde HTTP loglama
- **📊 Merkezi Loglama Sistemi**: RabbitMQ üzerinden log-messages kanalına log gönderimi
- **🌐 HTTP Request/Response Logging**: Otomatik HTTP istek/yanıt loglama
- **⚡ Performance Monitoring**: Performans metrikleri izleme
- **📈 Business Event Logging**: İş olayları loglama
- **🔒 Error Handling**: Gelişmiş hata yönetimi
- **🎨 SOLID Principles**: Clean code ve yüksek OOP standartları

## 📦 Kurulum

### 1. Composer ile Paketi Ekleyin

```bash
composer require ebilet/common
```

### 2. Service Provider'ı Kaydedin

`bootstrap/providers.php` dosyasında (Laravel 12):

```php
return [
    // ...
    Ebilet\Common\ServiceProviders\LoggingServiceProvider::class,
];
```

### 3. Configuration Dosyasını Yayınlayın

```bash
php artisan vendor:publish --provider="Ebilet\Common\ServiceProviders\LoggingServiceProvider"
```

### 4. Environment Variables'ları Ayarlayın

`.env` dosyasında:

```env
# RabbitMQ Configuration
RABBITMQ_HOST=localhost
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/

# HTTP Logging Configuration
EBILET_HTTP_LOGGING_ENABLED=true
EBILET_HTTP_LOGGING_ENDPOINTS=*
EBILET_HTTP_LOGGING_EXCLUDED_PATHS=/health,/metrics

# Queue Configuration
EBILET_QUEUE_LOG_CHANNEL=log-messages
EBILET_QUEUE_METRICS_CHANNEL=metrics
EBILET_QUEUE_EVENTS_CHANNEL=events

# Performance Monitoring
EBILET_PERFORMANCE_MONITORING_ENABLED=true
```

## 🎯 Kullanım

### 1. Basit Loglama

```php
use Ebilet\Common\Facades\Log;

// Temel loglama
Log::info('Kullanıcı giriş yaptı', ['user_id' => 123]);
Log::error('Veritabanı bağlantı hatası', ['error' => $exception->getMessage()]);
Log::warning('Yavaş sorgu tespit edildi', ['query' => $sql, 'duration' => 2.5]);

// HTTP loglama
Log::logHttpRequest('POST', '/api/users', $headers, $body);
Log::logHttpResponse(200, $responseHeaders, $responseBody, 0.15);

// Performans metrikleri
Log::logPerformance('database_query', 0.05, ['table' => 'users']);

// İş olayları
Log::logBusinessEvent('user_registered', [
    'user_id' => 123,
    'email' => 'user@example.com'
]);
```

### 2. Endpoint-Specific Middleware Kullanımı

`bootstrap/app.php` dosyasında (Laravel 12):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \Ebilet\Common\Middleware\HttpLoggingMiddleware::class,
    ]);
    
    $middleware->api(append: [
        \Ebilet\Common\Middleware\HttpLoggingMiddleware::class,
    ]);
})
```

### 3. Config-Based Endpoint Control

`.env` dosyasında:

```env
# Tüm endpoint'lerde loglama
EBILET_HTTP_LOGGING_ENDPOINTS=*

# Sadece belirli endpoint'lerde loglama
EBILET_HTTP_LOGGING_ENDPOINTS=GET:/api/users,POST:/api/auth,PUT:/api/profile

# Wildcard kullanımı
EBILET_HTTP_LOGGING_ENDPOINTS=GET:/api/*,POST:/api/*
```

### 4. Queue Manager Kullanımı

```php
use Ebilet\Common\Facades\Queue;

// Queue bağlantısı
Queue::connect();

// Log gönderimi
Queue::sendLog([
    'message' => 'Test log',
    'level' => 'info',
    'context' => ['test' => true]
], LogMessageType::APPLICATION_INFO);

// Metric gönderimi
Queue::sendMetric([
    'metric' => 'response_time',
    'value' => 0.15,
    'unit' => 'seconds'
]);

// Event gönderimi
Queue::sendEvent([
    'event' => 'user_registered',
    'data' => ['user_id' => 123]
]);
```

### 5. Enum Kullanımı

```php
use Ebilet\Common\Enums\LogMessageType;

// Log mesaj tipi belirleme
$messageType = LogMessageType::HTTP_REQUEST;
$logLevel = $messageType->getLogLevel(); // 'info'
$isCritical = $messageType->isCritical(); // false
$isPerformance = $messageType->isPerformance(); // false
```

## ⚙️ Konfigürasyon

### Config Dosyası: `config/ebilet-common.php`

```php
return [
    /*
    |--------------------------------------------------------------------------
    | RabbitMQ Configuration
    |--------------------------------------------------------------------------
    */
    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST', 'localhost'),
        'port' => env('RABBITMQ_PORT', 5672),
        'user' => env('RABBITMQ_USER', 'guest'),
        'password' => env('RABBITMQ_PASSWORD', 'guest'),
        'vhost' => env('RABBITMQ_VHOST', '/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
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
    | HTTP Logging Configuration
    |--------------------------------------------------------------------------
    */
    'http_logging' => [
        'enabled' => env('EBILET_HTTP_LOGGING_ENABLED', true),
        'endpoints' => env('EBILET_HTTP_LOGGING_ENDPOINTS', '*'),
        'excluded_paths' => env('EBILET_HTTP_LOGGING_EXCLUDED_PATHS', [
            '/health', '/metrics', '/favicon.ico', '/robots.txt', '/.well-known'
        ]),
        'excluded_methods' => env('EBILET_HTTP_LOGGING_EXCLUDED_METHODS', ['OPTIONS']),
        'sensitive_headers' => env('EBILET_HTTP_LOGGING_SENSITIVE_HEADERS', [
            'authorization', 'cookie', 'x-api-key', 'x-auth-token'
        ]),
        'log_request_body' => env('EBILET_HTTP_LOGGING_REQUEST_BODY', true),
        'log_response_body' => env('EBILET_HTTP_LOGGING_RESPONSE_BODY', false),
        'max_body_size' => env('EBILET_HTTP_LOGGING_MAX_BODY_SIZE', 1024 * 1024),
        'slow_request_threshold' => env('EBILET_HTTP_LOGGING_SLOW_THRESHOLD', 2000),
    ],
];
```

## 📊 Log Message Types

Paket aşağıdaki log mesaj tiplerini destekler:

### HTTP Logs
- `HTTP_REQUEST`: HTTP istekleri
- `HTTP_RESPONSE`: HTTP yanıtları  
- `HTTP_ERROR`: HTTP hataları

### Application Logs
- `APPLICATION_INFO`: Genel bilgi logları
- `APPLICATION_ERROR`: Uygulama hataları
- `APPLICATION_WARNING`: Uyarılar
- `APPLICATION_DEBUG`: Debug logları

### Performance Logs
- `PERFORMANCE_METRIC`: Performans metrikleri
- `SLOW_REQUEST`: Yavaş istekler
- `MEMORY_USAGE`: Bellek kullanımı

### Business Event Logs
- `BUSINESS_EVENT`: İş olayları
- `USER_ACTION`: Kullanıcı aksiyonları
- `SYSTEM_EVENT`: Sistem olayları

### Security Logs
- `SECURITY_ALERT`: Güvenlik uyarıları
- `AUTHENTICATION`: Kimlik doğrulama
- `AUTHORIZATION`: Yetkilendirme

### Database Logs
- `DATABASE_QUERY`: Veritabanı sorguları
- `DATABASE_ERROR`: Veritabanı hataları
- `DATABASE_SLOW_QUERY`: Yavaş sorgular

### External Service Logs
- `EXTERNAL_API_CALL`: Dış API çağrıları
- `EXTERNAL_API_ERROR`: Dış API hataları
- `EXTERNAL_SERVICE_TIMEOUT`: Dış servis timeout'ları

## 🔧 Error Handling

```php
use Ebilet\Common\Exceptions\LoggingException;

try {
    Log::info('Test message');
} catch (LoggingException $e) {
    // Logging hatası yönetimi
    error_log("Logging error: " . $e->getMessage());
}
```

## 🧪 Test

```bash
# Unit testleri çalıştırma
./vendor/bin/phpunit packages/ebilet/common/tests/
```

## 📋 Gereksinimler

- **PHP**: ^8.2
- **Laravel**: ^12.0
- **RabbitMQ**: 3.8+
- **php-amqplib**: ^3.0
- **monolog**: ^3.0

## 🤝 Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit yapın (`git commit -m 'Add amazing feature'`)
4. Push yapın (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📄 Lisans

Bu paket MIT lisansı altında lisanslanmıştır.

## 🔄 Versiyon Geçmişi

### v1.2.2 (Current)
- **Service Communication System**: Tamamen yeni servisler arası iletişim sistemi
- **SOLID Architecture**: Interface, Factory, Manager pattern'leri ile temiz mimari
- **User & Order Facades**: Basit facade kullanımı (`User::get()`, `Order::post()`)
- **Configuration Driven**: Config'den service URL'leri okuma
- **Error Handling**: Kapsamlı error handling ve logging
- **Health Check**: Service sağlık kontrolü
- **Method Chaining**: `withToken()`, `withHeaders()`, `timeout()` chaining

### v1.2.1
- ConfigManager ve Config Facade
- Geliştirilmiş config yapısı
- Kapsamlı dokümantasyon
- Güvenlik ayarları

### v1.2.0
- Centralized Logger
- Queue Manager
- HTTP Logging Middleware

### v1.1.0
- Laravel 12 uyumluluğu
- Config-based architecture
- Endpoint-specific logging
- Improved requirements

### v1.0.0
- İlk stable sürüm
- Temel loglama özellikleri
- RabbitMQ entegrasyonu