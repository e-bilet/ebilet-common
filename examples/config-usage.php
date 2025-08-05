<?php

/**
 * E-Bilet Common Package Config Usage Examples
 * 
 * Bu dosya, paket config ayarlarının nasıl kullanılacağını gösteren örnekler içerir.
 */

use Ebilet\Common\Services\ConfigManager;
use Ebilet\Common\Facades\Config;
use Ebilet\Common\Facades\Log;

// ============================================================================
// CONFIG MANAGER KULLANIMI
// ============================================================================

// 1. Temel Config Değerlerine Erişim
$rabbitMQConfig = ConfigManager::getRabbitMQConfig();
$loggingConfig = ConfigManager::getLoggingConfig();
$httpLoggingConfig = ConfigManager::getHttpLoggingConfig();

// 2. Belirli Bir Config Değerini Alma
$logLevel = ConfigManager::get('logging.log_level', 'info');
$serviceName = ConfigManager::get('logging.service_name', 'unknown-service');
$isLoggingEnabled = ConfigManager::get('logging.enabled', true);

// 3. Özellik Kontrolü
if (ConfigManager::isEnabled('logging.enabled')) {
    Log::info('Logging is enabled');
}

if (ConfigManager::isEnabled('http_logging.enabled')) {
    Log::info('HTTP logging is enabled');
}

// 4. Tüm Config'i Alma
$allConfig = ConfigManager::getAllConfig();

// 5. Config Doğrulama
$configErrors = ConfigManager::validateConfig();
if (!empty($configErrors)) {
    foreach ($configErrors as $error) {
        Log::error("Config error: {$error}");
    }
}

// 6. Environment'a Özel Config
$productionConfig = ConfigManager::getConfigForEnvironment('production');
$developmentConfig = ConfigManager::getConfigForEnvironment('development');

// ============================================================================
// CONFIG FACADE KULLANIMI
// ============================================================================

// 1. Config Değerlerine Erişim
$rabbitMQHost = Config::get('rabbitmq.host', 'localhost');
$rabbitMQPort = Config::get('rabbitmq.port', 5672);

// 2. Belirli Config Bölümlerini Alma
$queueConfig = Config::getQueueConfig();
$performanceConfig = Config::getPerformanceConfig();
$businessEventsConfig = Config::getBusinessEventsConfig();

// 3. Özellik Kontrolü
if (Config::isEnabled('performance.enabled')) {
    // Performance monitoring aktif
}

// ============================================================================
// RABBITMQ CONFIG KULLANIMI
// ============================================================================

$rabbitMQConfig = ConfigManager::getRabbitMQConfig();

// RabbitMQ bağlantı ayarları
$host = $rabbitMQConfig['host'] ?? 'localhost';
$port = $rabbitMQConfig['port'] ?? 5672;
$user = $rabbitMQConfig['user'] ?? 'guest';
$password = $rabbitMQConfig['password'] ?? 'guest';
$vhost = $rabbitMQConfig['vhost'] ?? '/';

// SSL ayarları
$sslOptions = $rabbitMQConfig['ssl_options'] ?? [];
$verifyPeer = $sslOptions['verify_peer'] ?? false;
$verifyPeerName = $sslOptions['verify_peer_name'] ?? false;

// ============================================================================
// QUEUE CONFIG KULLANIMI
// ============================================================================

$queueConfig = ConfigManager::getQueueConfig();

// Queue kanalları
$defaultChannel = $queueConfig['default_channel'] ?? 'log-messages';
$channels = $queueConfig['channels'] ?? [];

// Queue ayarları
$settings = $queueConfig['settings'] ?? [];
$logMessagesSettings = $settings['log_messages'] ?? [];

// Queue özellikleri
$durable = $logMessagesSettings['durable'] ?? true;
$ttl = $logMessagesSettings['ttl'] ?? 86400000; // 24 hours
$maxLength = $logMessagesSettings['max_length'] ?? 10000;
$autoDelete = $logMessagesSettings['auto_delete'] ?? false;

// ============================================================================
// LOGGING CONFIG KULLANIMI
// ============================================================================

$loggingConfig = ConfigManager::getLoggingConfig();

// Logging ayarları
$enabled = $loggingConfig['enabled'] ?? true;
$serviceName = $loggingConfig['service_name'] ?? 'unknown-service';
$serviceVersion = $loggingConfig['service_version'] ?? '1.0.0';
$environment = $loggingConfig['environment'] ?? 'production';
$logLevel = $loggingConfig['log_level'] ?? 'info';
$includeStackTrace = $loggingConfig['include_stack_trace'] ?? true;
$maxMessageSize = $loggingConfig['max_message_size'] ?? 1024 * 1024;
$timestampFormat = $loggingConfig['timestamp_format'] ?? 'Y-m-d H:i:s';
$timezone = $loggingConfig['timezone'] ?? 'UTC';

// ============================================================================
// HTTP LOGGING CONFIG KULLANIMI
// ============================================================================

$httpLoggingConfig = ConfigManager::getHttpLoggingConfig();

// HTTP logging ayarları
$httpLoggingEnabled = $httpLoggingConfig['enabled'] ?? true;
$endpoints = $httpLoggingConfig['endpoints'] ?? '*';
$excludedPaths = $httpLoggingConfig['excluded_paths'] ?? [];
$excludedMethods = $httpLoggingConfig['excluded_methods'] ?? [];
$sensitiveHeaders = $httpLoggingConfig['sensitive_headers'] ?? [];
$sensitiveBodyFields = $httpLoggingConfig['sensitive_body_fields'] ?? [];
$logRequestBody = $httpLoggingConfig['log_request_body'] ?? true;
$logResponseBody = $httpLoggingConfig['log_response_body'] ?? false;
$maxBodySize = $httpLoggingConfig['max_body_size'] ?? 1024 * 1024;
$slowRequestThreshold = $httpLoggingConfig['slow_request_threshold'] ?? 2000;

// ============================================================================
// PERFORMANCE CONFIG KULLANIMI
// ============================================================================

$performanceConfig = ConfigManager::getPerformanceConfig();

// Performance monitoring ayarları
$performanceEnabled = $performanceConfig['enabled'] ?? true;
$memoryThreshold = $performanceConfig['memory_threshold'] ?? 128 * 1024 * 1024;
$slowQueryThreshold = $performanceConfig['slow_query_threshold'] ?? 1000;
$externalApiTimeout = $performanceConfig['external_api_timeout'] ?? 5000;
$cpuThreshold = $performanceConfig['cpu_threshold'] ?? 80;
$diskUsageThreshold = $performanceConfig['disk_usage_threshold'] ?? 90;

// Metrik toplama ayarları
$collectMetrics = $performanceConfig['collect_metrics'] ?? [];
$collectMemoryMetrics = $collectMetrics['memory_usage'] ?? true;
$collectCpuMetrics = $collectMetrics['cpu_usage'] ?? true;
$collectDiskMetrics = $collectMetrics['disk_usage'] ?? true;
$collectDbMetrics = $collectMetrics['database_queries'] ?? true;
$collectApiMetrics = $collectMetrics['external_api_calls'] ?? true;

// ============================================================================
// BUSINESS EVENTS CONFIG KULLANIMI
// ============================================================================

$businessEventsConfig = ConfigManager::getBusinessEventsConfig();

// Business events ayarları
$businessEventsEnabled = $businessEventsConfig['enabled'] ?? true;
$events = $businessEventsConfig['events'] ?? [];
$includeUserData = $businessEventsConfig['include_user_data'] ?? false;
$includeSensitiveData = $businessEventsConfig['include_sensitive_data'] ?? false;

// Event ayarları
$logUserRegistered = $events['user_registered'] ?? true;
$logUserLoggedIn = $events['user_logged_in'] ?? true;
$logUserLoggedOut = $events['user_logged_out'] ?? true;
$logOrderCreated = $events['order_created'] ?? true;
$logOrderCancelled = $events['order_cancelled'] ?? true;
$logPaymentSuccessful = $events['payment_successful'] ?? true;
$logPaymentFailed = $events['payment_failed'] ?? true;
$logTicketBooked = $events['ticket_booked'] ?? true;
$logTicketCancelled = $events['ticket_cancelled'] ?? true;

// ============================================================================
// ERROR HANDLING CONFIG KULLANIMI
// ============================================================================

$errorHandlingConfig = ConfigManager::getErrorHandlingConfig();

// Error handling ayarları
$logExceptions = $errorHandlingConfig['log_exceptions'] ?? true;
$logErrors = $errorHandlingConfig['log_errors'] ?? true;
$logWarnings = $errorHandlingConfig['log_warnings'] ?? true;
$includeStackTrace = $errorHandlingConfig['include_stack_trace'] ?? true;
$maxStackTraceDepth = $errorHandlingConfig['max_stack_trace_depth'] ?? 10;
$sanitizeErrorMessages = $errorHandlingConfig['sanitize_error_messages'] ?? true;

// ============================================================================
// SECURITY CONFIG KULLANIMI
// ============================================================================

$securityConfig = ConfigManager::getSecurityConfig();

// Security ayarları
$logFailedLogins = $securityConfig['log_failed_logins'] ?? true;
$logSuccessfulLogins = $securityConfig['log_successful_logins'] ?? false;
$logPasswordResets = $securityConfig['log_password_resets'] ?? true;
$logAccountLocks = $securityConfig['log_account_locks'] ?? true;
$logSuspiciousActivity = $securityConfig['log_suspicious_activity'] ?? true;
$suspiciousActivityThreshold = $securityConfig['suspicious_activity_threshold'] ?? 5;

// ============================================================================
// CONDITIONAL LOGGING ÖRNEKLERİ
// ============================================================================

// Logging aktifse log gönder
if (ConfigManager::isEnabled('logging.enabled')) {
    Log::info('Application started', [
        'service_name' => ConfigManager::get('logging.service_name'),
        'environment' => ConfigManager::get('logging.environment'),
        'version' => ConfigManager::get('logging.service_version'),
    ]);
}

// HTTP logging aktifse request logla
if (ConfigManager::isEnabled('http_logging.enabled')) {
    $requestData = [
        'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
        'url' => $_SERVER['REQUEST_URI'] ?? '/',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
    ];
    
    // Sensitive header'ları filtrele
    $sensitiveHeaders = ConfigManager::get('http_logging.sensitive_headers', []);
    foreach ($sensitiveHeaders as $header) {
        if (isset($requestData[$header])) {
            $requestData[$header] = '***REDACTED***';
        }
    }
    
    Log::info('HTTP Request', $requestData);
}

// Performance monitoring aktifse metrik topla
if (ConfigManager::isEnabled('performance.enabled')) {
    $memoryUsage = memory_get_usage(true);
    $memoryThreshold = ConfigManager::get('performance.memory_threshold', 128 * 1024 * 1024);
    
    if ($memoryUsage > $memoryThreshold) {
        Log::warning('High memory usage detected', [
            'current_usage' => $memoryUsage,
            'threshold' => $memoryThreshold,
        ]);
    }
}

// Business events aktifse event logla
if (ConfigManager::isEnabled('business_events.enabled')) {
    $events = ConfigManager::get('business_events.events', []);
    
    if ($events['user_registered'] ?? false) {
        Log::info('User registered', [
            'event' => 'user_registered',
            'include_user_data' => ConfigManager::get('business_events.include_user_data', false),
        ]);
    }
}

// ============================================================================
// ENVIRONMENT'A GÖRE CONFIG ÖRNEKLERİ
// ============================================================================

$environment = ConfigManager::get('logging.environment', 'production');

switch ($environment) {
    case 'production':
        // Production ayarları
        $logLevel = 'warning';
        $logResponseBody = false;
        $includeStackTrace = false;
        break;
        
    case 'staging':
        // Staging ayarları
        $logLevel = 'info';
        $logResponseBody = false;
        $includeStackTrace = true;
        break;
        
    case 'development':
        // Development ayarları
        $logLevel = 'debug';
        $logResponseBody = true;
        $includeStackTrace = true;
        break;
        
    default:
        // Default ayarlar
        $logLevel = 'info';
        $logResponseBody = false;
        $includeStackTrace = true;
        break;
}

// ============================================================================
// CONFIG VALIDATION ÖRNEKLERİ
// ============================================================================

// Config'i doğrula
$validationErrors = ConfigManager::validateConfig();

if (!empty($validationErrors)) {
    // Config hatalarını logla
    foreach ($validationErrors as $error) {
        Log::error("Configuration error: {$error}");
    }
    
    // Hata durumunda varsayılan değerleri kullan
    $rabbitMQConfig = [
        'host' => 'localhost',
        'port' => 5672,
        'user' => 'guest',
        'password' => 'guest',
        'vhost' => '/',
    ];
} else {
    // Config geçerli, normal kullan
    $rabbitMQConfig = ConfigManager::getRabbitMQConfig();
}

// ============================================================================
// DYNAMIC CONFIG UPDATES
// ============================================================================

// Runtime'da config değerlerini güncelle (örnek)
function updateConfigDynamically(string $key, $value): void
{
    // Bu örnek sadece gösterim amaçlıdır
    // Gerçek uygulamada config cache'i temizlenmelidir
    
    Log::info('Config updated dynamically', [
        'key' => $key,
        'value' => $value,
    ]);
}

// Örnek kullanım
updateConfigDynamically('logging.log_level', 'debug');
updateConfigDynamically('http_logging.enabled', true);

// ============================================================================
// CONFIG CACHE MANAGEMENT
// ============================================================================

// Config cache'ini temizle (Laravel command)
// php artisan config:clear

// Config cache'ini yeniden oluştur (Laravel command)
// php artisan config:cache

// ============================================================================
// CONFIG PUBLISHING
// ============================================================================

// Config dosyalarını yayınla (Laravel command)
// php artisan vendor:publish --tag=ebilet-common-config

// Sadece belirli config dosyasını yayınla
// php artisan vendor:publish --tag=ebilet-common-main-config
// php artisan vendor:publish --tag=ebilet-common-logging-config 