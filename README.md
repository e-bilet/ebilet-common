# E-Bilet Common Package

Ortak bileşenler ve utilities - Logging, helpers, shared models ve common utilities.

## 🚀 Özellikler

### **Logging**
- **Framework Independent**: Laravel dependency'si yok, herhangi bir PHP projesinde kullanılabilir
- **Centralized Logging**: Merkezi loglama sistemi
- **Performance Monitoring**: Performans metriklerini loglar
- **HTTP Request/Response Logging**: HTTP isteklerini detaylı loglar
- **Business Event Logging**: İş olaylarını loglar
- **Fallback Logging**: Queue erişilemezse dosyaya loglar

### **Queue Management**
- **Strategy Pattern**: Farklı queue provider'ları (RabbitMQ, Redis, SQS, etc.)
- **Extensible**: Yeni queue provider'ları kolayca eklenebilir
- **Testable**: Mock queue provider'ları ile test edilebilir
- **Reusable**: Tüm queue işlemleri için tek interface
- **Environment Variables**: Projenin .env dosyasından queue ayarlarını okur

## 📦 Kurulum

### 1. Composer ile ekle

```bash
composer require ebilet/common
```

### 2. Environment variables ekle

`.env` dosyasına ekle:

```env
# RabbitMQ Configuration
RABBITMQ_HOST=localhost
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/

# Application Configuration
APP_NAME=auth-service
LOG_PATH=logs
```

## 🔧 Kullanım

### Laravel Entegrasyonu

1. **Service Provider'ı kaydet:**

```php
// bootstrap/providers.php (Laravel 12)
return [
    App\Providers\AppServiceProvider::class,
    Ebilet\Common\ServiceProviders\LoggingServiceProvider::class,
];
```

2. **Config dosyasını yayınla:**

```bash
php artisan vendor:publish --tag=ebilet-common-config
```

3. **Middleware'i kullan:**

```php
// routes/api.php
Route::middleware(['ebilet.logging'])->group(function () {
    // API routes
});
```

4. **Environment variables ekle:**

```env
# Logging
EBILET_LOGGING_ENABLED=true
EBILET_HTTP_LOGGING_ENABLED=true
EBILET_PERFORMANCE_LOGGING=true
EBILET_BUSINESS_EVENT_LOGGING=true

# Queue
RABBITMQ_HOST=localhost
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/
```

### Temel Logging

```php
use Ebilet\Common\Logger;

// Standart log seviyeleri
Logger::info('User logged in', ['user_id' => 123]);
Logger::error('Database connection failed', ['error' => $e->getMessage()]);
Logger::debug('Processing request', ['request_id' => $requestId]);
```

### Queue Management

```php
use Ebilet\Common\Facades\Queue;

// Send log to queue
Queue::sendLog(['message' => 'Test log'], 'logs');

// Send metric to queue
Queue::sendMetric(['metric' => 'response_time', 'value' => 0.5], 'metrics');

// Send event to queue
Queue::sendEvent(['event' => 'user_registered', 'user_id' => 123], 'events');

// Custom queue operations
Queue::send('custom-queue', ['data' => 'test'], ['priority' => 'high']);
```

### HTTP Request/Response Logging

```php
// Request logging
Logger::logHttpRequest(
    'POST',
    'https://api.ebilet.com/auth/login',
    ['Content-Type' => 'application/json'],
    ['email' => 'user@example.com']
);

// Response logging
Logger::logHttpResponse(
    200,
    ['Content-Type' => 'application/json'],
    '{"token": "abc123"}',
    0.045 // duration in seconds
);
```

### Performance Logging

```php
$startTime = microtime(true);

// ... perform operation ...

$duration = microtime(true) - $startTime;
Logger::logPerformance('database_query', $duration, [
    'table' => 'users',
    'query' => 'SELECT * FROM users WHERE id = ?'
]);
```

### Business Event Logging

```php
Logger::logBusinessEvent('user_registered', [
    'user_id' => 123,
    'email' => 'user@example.com',
    'registration_method' => 'email'
]);

Logger::logBusinessEvent('order_created', [
    'order_id' => 456,
    'total_amount' => 150.00,
    'payment_method' => 'credit_card'
]);
```

## 📊 Log Formatı

### RabbitMQ'ya Gönderilen Log Mesajı

```json
{
    "service": "auth-service",
    "level": "info",
    "message": "User logged in",
    "context": {
        "user_id": 123,
        "ip": "192.168.1.1"
    },
    "timestamp": "2024-01-15T00:34:13.000000Z",
    "host": "auth-service-1",
    "pid": 12345,
    "memory_usage": 1048576,
    "memory_peak": 2097152
}
```

### HTTP Request Log

```json
{
    "service": "auth-service",
    "level": "info",
    "message": "HTTP Request",
    "context": {
        "method": "POST",
        "url": "https://api.ebilet.com/auth/login",
        "headers": {
            "Content-Type": "application/json"
        },
        "body": {
            "email": "user@example.com"
        },
        "type": "http_request"
    },
    "timestamp": "2024-01-15T00:34:13.000000Z"
}
```

## 🔄 RabbitMQ Konfigürasyonu

### Exchange Oluşturma

```bash
# RabbitMQ Management UI'da veya CLI ile
rabbitmqadmin declare exchange name=log-messages type=topic durable=true
```

### Queue Oluşturma

```bash
# Graylog consumer için queue
rabbitmqadmin declare queue name=graylog-logs durable=true
rabbitmqadmin declare binding source=log-messages destination=graylog-logs routing_key=logs
```

## 🛠️ Middleware Kullanımı

### HTTP Logging Middleware

```php
use Ebilet\Logging\Logger;

class HttpLoggingMiddleware
{
    public function handle($request, Closure $next)
    {
        $startTime = microtime(true);
        
        // Log request
        Logger::logHttpRequest(
            $request->method(),
            $request->fullUrl(),
            $request->headers->all(),
            $request->all()
        );
        
        $response = $next($request);
        
        $duration = microtime(true) - $startTime;
        
        // Log response
        Logger::logHttpResponse(
            $response->getStatusCode(),
            $response->headers->all(),
            $response->getContent(),
            $duration
        );
        
        return $response;
    }
}
```

## 📈 Graylog Entegrasyonu

### Graylog Input Konfigürasyonu

Graylog'ta RabbitMQ input oluştur:

1. **System > Inputs** bölümüne git
2. **RabbitMQ** input tipini seç
3. **Configuration**:
   - **Host**: RabbitMQ host
   - **Port**: 5672
   - **Username**: guest
   - **Password**: guest
   - **Queue**: graylog-logs
   - **Exchange**: log-messages

### Log Formatı

Graylog'ta log mesajları şu formatta görünecek:

```
Service: auth-service
Level: info
Message: User logged in
Context: {"user_id": 123, "ip": "192.168.1.1"}
Timestamp: 2024-01-15T00:34:13.000000Z
Host: auth-service-1
```

## 🔧 Environment Variables

### Gerekli Environment Variables

```env
# RabbitMQ Configuration
RABBITMQ_HOST=localhost
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/

# Application Configuration
APP_NAME=auth-service
LOG_PATH=logs
```

### Opsiyonel Environment Variables

```env
# Logging Configuration
EBILET_LOGGING_ENABLED=true
EBILET_PERFORMANCE_LOGGING=true
EBILET_HTTP_LOGGING=true
EBILET_BUSINESS_EVENT_LOGGING=true
EBILET_FALLBACK_LOGGING=true
```

## 🧪 Test

### Unit Test Örneği

```php
use Ebilet\Logging\Logger;

class LoggingTest extends TestCase
{
    public function test_can_log_message()
    {
        Logger::info('Test message', ['test' => true]);
        
        // Assert log was sent to RabbitMQ
        $this->assertTrue(true); // Add your assertions
    }
}
```

## 🔄 Framework Entegrasyonu

### Laravel ile Kullanım

```php
// config/app.php
'providers' => [
    // ...
    Ebilet\Logging\LoggingServiceProvider::class,
],

// Kullanım
use Ebilet\Logging\Facades\Log;

Log::info('User logged in', ['user_id' => 123]);
```

### Standalone PHP ile Kullanım

```php
require_once 'vendor/autoload.php';

use Ebilet\Logging\Logger;

Logger::info('Application started');
Logger::logBusinessEvent('user_registered', ['user_id' => 123]);
```

## 📝 Lisans

MIT License 