<?php

namespace Ebilet\Common\Enums;

/**
 * Log Message Types Enum
 * 
 * Merkezi loglama sistemi için log mesaj tiplerini tanımlar.
 * RabbitMQ'da kullanılacak log-messages kanalı için standart mesaj tipleri.
 */
enum LogMessageType: string
{
    // HTTP Request/Response Logs
    case HTTP_REQUEST = 'http_request';
    case HTTP_RESPONSE = 'http_response';
    case HTTP_ERROR = 'http_error';
    
    // Application Logs
    case APPLICATION_INFO = 'application_info';
    case APPLICATION_ERROR = 'application_error';
    case APPLICATION_WARNING = 'application_warning';
    case APPLICATION_DEBUG = 'application_debug';
    
    // Performance Logs
    case PERFORMANCE_METRIC = 'performance_metric';
    case SLOW_REQUEST = 'slow_request';
    case MEMORY_USAGE = 'memory_usage';
    
    // Business Event Logs
    case BUSINESS_EVENT = 'business_event';
    case USER_ACTION = 'user_action';
    case SYSTEM_EVENT = 'system_event';
    
    // Security Logs
    case SECURITY_ALERT = 'security_alert';
    case AUTHENTICATION = 'authentication';
    case AUTHORIZATION = 'authorization';
    
    // Database Logs
    case DATABASE_QUERY = 'database_query';
    case DATABASE_ERROR = 'database_error';
    case DATABASE_SLOW_QUERY = 'database_slow_query';
    
    // External Service Logs
    case EXTERNAL_API_CALL = 'external_api_call';
    case EXTERNAL_API_ERROR = 'external_api_error';
    case EXTERNAL_SERVICE_TIMEOUT = 'external_service_timeout';
    
    /**
     * Get all log message types as array
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    /**
     * Check if log type is critical
     */
    public function isCritical(): bool
    {
        return in_array($this, [
            self::APPLICATION_ERROR,
            self::HTTP_ERROR,
            self::SECURITY_ALERT,
            self::DATABASE_ERROR,
            self::EXTERNAL_API_ERROR
        ]);
    }
    
    /**
     * Check if log type is performance related
     */
    public function isPerformance(): bool
    {
        return in_array($this, [
            self::PERFORMANCE_METRIC,
            self::SLOW_REQUEST,
            self::MEMORY_USAGE,
            self::DATABASE_SLOW_QUERY,
            self::EXTERNAL_SERVICE_TIMEOUT
        ]);
    }
    
    /**
     * Get log level for this message type
     */
    public function getLogLevel(): string
    {
        return match($this) {
            self::APPLICATION_ERROR,
            self::HTTP_ERROR,
            self::SECURITY_ALERT,
            self::DATABASE_ERROR,
            self::EXTERNAL_API_ERROR => 'error',
            
            self::APPLICATION_WARNING,
            self::SLOW_REQUEST,
            self::EXTERNAL_SERVICE_TIMEOUT => 'warning',
            
            self::APPLICATION_DEBUG,
            self::DATABASE_QUERY,
            self::MEMORY_USAGE => 'debug',
            
            default => 'info'
        };
    }
} 