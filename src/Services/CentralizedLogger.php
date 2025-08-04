<?php

namespace Ebilet\Common\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class CentralizedLogger
{
    private \Ebilet\Common\Managers\QueueManager $queueManager;
    private string $serviceName;

    public function __construct()
    {
        $this->queueManager = \Ebilet\Common\Managers\QueueManager::getInstance();
        
        // Set RabbitMQ as default provider
        $rabbitMQProvider = new \Ebilet\Common\Providers\RabbitMQProvider();
        $this->queueManager->setProvider($rabbitMQProvider);
        $this->queueManager->connect();
        
        $this->serviceName = $this->getEnv('APP_NAME', 'unknown-service');
    }

    /**
     * Get environment variable with fallback.
     */
    private function getEnv(string $key, string $default = ''): string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        return $value !== false ? $value : $default;
    }

    /**
     * Log an emergency message to the logs.
     */
    public function emergency($message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }

    /**
     * Log an alert message to the logs.
     */
    public function alert($message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }

    /**
     * Log a critical message to the logs.
     */
    public function critical($message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }

    /**
     * Log an error message to the logs.
     */
    public function error($message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    /**
     * Log a warning message to the logs.
     */
    public function warning($message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    /**
     * Log a notice to the logs.
     */
    public function notice($message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }

    /**
     * Log an informational message to the logs.
     */
    public function info($message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    /**
     * Log a debug message to the logs.
     */
    public function debug($message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }

    /**
     * Log a message to the logs.
     */
    public function log($level, $message, array $context = []): void
    {
        $logData = [
            'service' => $this->serviceName,
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'timestamp' => $this->getCurrentTimestamp(),
            'host' => gethostname(),
            'pid' => getmypid(),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true)
        ];

        // Send to queue
        $this->queueManager->sendLog($logData);

        // Also log to local file as backup
        $this->logToFile($level, $message, $context);
    }

    /**
     * Get current timestamp in ISO format.
     */
    private function getCurrentTimestamp(): string
    {
        return date('c'); // ISO 8601 format
    }

    /**
     * Log to local file as backup.
     */
    private function logToFile($level, $message, array $context = []): void
    {
        $logger = new Logger('ebilet-centralized');
        
        // Create logs directory if it doesn't exist
        $logPath = $this->getLogPath();
        $logDir = dirname($logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
        $logger->log($level, $message, $context);
    }

    /**
     * Get log file path.
     */
    private function getLogPath(): string
    {
        $logDir = $this->getEnv('LOG_PATH', 'logs');
        return $logDir . '/ebilet-centralized.log';
    }

    /**
     * Log HTTP request/response data.
     */
    public function logHttpRequest(string $method, string $url, array $headers = [], array $body = []): void
    {
        $this->info('HTTP Request', [
            'method' => $method,
            'url' => $url,
            'headers' => $headers,
            'body' => $body,
            'type' => 'http_request'
        ]);
    }

    /**
     * Log HTTP response data.
     */
    public function logHttpResponse(int $statusCode, array $headers = [], string $body = '', float $duration = 0): void
    {
        $this->info('HTTP Response', [
            'status_code' => $statusCode,
            'headers' => $headers,
            'body' => $body,
            'duration_ms' => round($duration * 1000, 2),
            'type' => 'http_response'
        ]);
    }

    /**
     * Log performance metrics.
     */
    public function logPerformance(string $operation, float $duration, array $metadata = []): void
    {
        $this->info('Performance Metric', [
            'operation' => $operation,
            'duration_ms' => round($duration * 1000, 2),
            'metadata' => $metadata,
            'type' => 'performance'
        ]);
    }

    /**
     * Log business events.
     */
    public function logBusinessEvent(string $event, array $data = []): void
    {
        $this->info('Business Event', [
            'event' => $event,
            'data' => $data,
            'type' => 'business_event'
        ]);
    }
} 