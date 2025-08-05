<?php

namespace Ebilet\Common\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ebilet\Common\Enums\LogMessageType;

class CentralizedLogger
{
    private \Ebilet\Common\Managers\QueueManager $queueManager;
    private string $serviceName;

    public function __construct()
    {
        error_log("CentralizedLogger: Constructor started");
        
        $this->queueManager = \Ebilet\Common\Managers\QueueManager::getInstance();
        error_log("CentralizedLogger: QueueManager instance created");
        
        // Set RabbitMQ as default provider
        $rabbitMQProvider = new \Ebilet\Common\Providers\RabbitMQProvider();
        $this->queueManager->setProvider($rabbitMQProvider);
        error_log("CentralizedLogger: RabbitMQProvider set");
        
        // Try to connect and log the result
        $connected = $this->queueManager->connect();
        if (!$connected) {
            error_log("CentralizedLogger: Failed to connect to RabbitMQ. Logs will be written to file only.");
        } else {
            error_log("CentralizedLogger: Successfully connected to RabbitMQ.");
        }
        
        $this->serviceName = $this->getEnv('APP_NAME', 'unknown-service');
        error_log("CentralizedLogger: Service name set to: " . $this->serviceName);
        error_log("CentralizedLogger: Constructor completed");
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

        // Determine message type based on level
        $messageType = $this->getMessageTypeFromLevel($level);

        // Send to queue
        $this->queueManager->sendLog($logData, $messageType);

        // Also log to local file as backup
        $this->logToFile($level, $message, $context);
    }

    /**
     * Get message type from log level
     */
    private function getMessageTypeFromLevel(string $level): LogMessageType
    {
        return match(strtolower($level)) {
            'emergency', 'critical', 'error' => LogMessageType::APPLICATION_ERROR,
            'warning' => LogMessageType::APPLICATION_WARNING,
            'debug' => LogMessageType::APPLICATION_DEBUG,
            default => LogMessageType::APPLICATION_INFO
        };
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
        $logData = [
            'method' => $method,
            'url' => $url,
            'headers' => $headers,
            'body' => $body,
            'message' => 'HTTP Request',
            'context' => [
                'method' => $method,
                'url' => $url,
                'headers' => $headers,
                'body' => $body
            ]
        ];

        $this->queueManager->sendLog($logData, LogMessageType::HTTP_REQUEST);
        $this->logToFile('info', 'HTTP Request', $logData['context']);
    }

    /**
     * Log HTTP response data.
     */
    public function logHttpResponse(int $statusCode, array $headers = [], string $body = '', float $duration = 0): void
    {
        $messageType = $statusCode >= 400 ? LogMessageType::HTTP_ERROR : LogMessageType::HTTP_RESPONSE;
        
        $logData = [
            'status_code' => $statusCode,
            'headers' => $headers,
            'body' => $body,
            'duration_ms' => round($duration * 1000, 2),
            'message' => 'HTTP Response',
            'context' => [
                'status_code' => $statusCode,
                'headers' => $headers,
                'body' => $body,
                'duration_ms' => round($duration * 1000, 2)
            ]
        ];

        $this->queueManager->sendLog($logData, $messageType);
        $this->logToFile('info', 'HTTP Response', $logData['context']);
    }

    /**
     * Log performance metrics.
     */
    public function logPerformance(string $operation, float $duration, array $metadata = []): void
    {
        $logData = [
            'operation' => $operation,
            'duration_ms' => round($duration * 1000, 2),
            'metadata' => $metadata,
            'message' => 'Performance Metric',
            'context' => [
                'operation' => $operation,
                'duration_ms' => round($duration * 1000, 2),
                'metadata' => $metadata
            ]
        ];

        $this->queueManager->sendLog($logData, LogMessageType::PERFORMANCE_METRIC);
        $this->logToFile('info', 'Performance Metric', $logData['context']);
    }

    /**
     * Log business events.
     */
    public function logBusinessEvent(string $event, array $data = []): void
    {
        $logData = [
            'event' => $event,
            'data' => $data,
            'message' => 'Business Event',
            'context' => [
                'event' => $event,
                'data' => $data
            ]
        ];

        $this->queueManager->sendLog($logData, LogMessageType::BUSINESS_EVENT);
        $this->logToFile('info', 'Business Event', $logData['context']);
    }
} 