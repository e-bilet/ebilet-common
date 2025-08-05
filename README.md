# E-Bilet Common Package

E-Bilet mikroservisleri için ortak kullanılan bileşenler ve utility'ler.

## Özellikler

- **Merkezi Loglama Sistemi**: RabbitMQ üzerinden log-messages kanalına log gönderimi
- **HTTP Request/Response Logging**: Otomatik HTTP istek/yanıt loglama
- **Performance Monitoring**: Performans metrikleri izleme
- **Business Event Logging**: İş olayları loglama
- **Configuration Management**: Environment-based konfigürasyon
- **Error Handling**: Gelişmiş hata yönetimi

## Kurulum

### 1. Composer ile Paketi Ekleyin

```bash
composer require ebilet/common
```

### 2. Service Provider'ı Kaydedin

`config/app.php` dosyasında:

```php
'providers' => [
    // ...
    Ebilet\Common\ServiceProviders\LoggingServiceProvider::class,
],
```

### 3. Configuration Dosyasını Yayınlayın

```bash
php artisan vendor:publish --tag=ebilet-common-config
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

# Logging Configuration
EBILET_LOGGING_ENABLED=true
EBILET_LOG_CHANNEL=log-messages
EBILET_METRICS_CHANNEL=metrics
EBILET_EVENTS_CHANNEL=events

# HTTP Logging
EBILET_HTTP_LOGGING_ENABLED=true
EBILET_HTTP_LOGGING_REQUEST_BODY=true
EBILET_HTTP_LOGGING_RESPONSE_BODY=false
```

## Kullanım

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

### 2. Middleware Kullanımı

`app/Http/Kernel.php` dosyasında:

```php
protected $middleware = [
    // ...
    \Ebilet\Common\Middleware\HttpLoggingMiddleware::class,
];
```

Veya route'larda:

```php
Route::middleware(['ebilet.logging'])->group(function () {
    // Routes
});
```

### 3. Queue Manager Kullanımı

```php
use Ebilet\Common\Facades\Queue;

// Queue bağlantısı
Queue::connect();

// Log gönderimi
Queue::sendLog([
    'message' => 'Test log',
    'level' => 'info',
    'context' => ['test' => true]
]);

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

### 4. Enum Kullanımı

```php
use Ebilet\Common\Enums\LogMessageType;

// Log mesaj tipi belirleme
$messageType = LogMessageType::HTTP_REQUEST;
$logLevel = $messageType->getLogLevel(); // 'info'
$isCritical = $messageType->isCritical(); // false
$isPerformance = $messageType->isPerformance(); // false
```

## Konfigürasyon

### RabbitMQ Ayarları

```php
'rabbitmq' => [
    'host' => env('RABBITMQ_HOST', 'localhost'),
    'port' => env('RABBITMQ_PORT', 5672),
    'user' => env('RABBITMQ_USER', 'guest'),
    'password' => env('RABBITMQ_PASSWORD', 'guest'),
    'vhost' => env('RABBITMQ_VHOST', '/'),
],
```

### Logging Ayarları

```php
'logging' => [
    'enabled' => env('EBILET_LOGGING_ENABLED', true),
    'service_name' => env('APP_NAME', 'unknown-service'),
    'log_channel' => env('EBILET_LOG_CHANNEL', 'log-messages'),
    'local_logging' => env('EBILET_LOCAL_LOGGING', true),
],
```

### HTTP Logging Ayarları

```php
'http_logging' => [
    'enabled' => env('EBILET_HTTP_LOGGING_ENABLED', true),
    'excluded_paths' => ['/health', '/metrics'],
    'sensitive_headers' => ['authorization', 'cookie'],
    'log_request_body' => true,
    'log_response_body' => false,
],
```

## Log Message Types

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

## Error Handling

```php
use Ebilet\Common\Exceptions\LoggingException;

try {
    Log::info('Test message');
} catch (LoggingException $e) {
    // Logging hatası yönetimi
    error_log("Logging error: " . $e->getMessage());
}
```

## Test

```bash
# Unit testleri çalıştırma
./vendor/bin/phpunit packages/ebilet/common/tests/
```

## Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit yapın (`git commit -m 'Add amazing feature'`)
4. Push yapın (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## Lisans

Bu paket MIT lisansı altında lisanslanmıştır. 