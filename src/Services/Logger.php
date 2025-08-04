<?php

namespace Ebilet\Common\Services;

use Ebilet\Common\Services\CentralizedLogger;

/**
 * Main Logger class for easy access to centralized logging.
 */
class Logger
{
    private static ?CentralizedLogger $instance = null;

    /**
     * Get the logger instance.
     */
    public static function getInstance(): CentralizedLogger
    {
        if (self::$instance === null) {
            self::$instance = new CentralizedLogger();
        }
        return self::$instance;
    }

    /**
     * Log an emergency message.
     */
    public static function emergency($message, array $context = []): void
    {
        self::getInstance()->emergency($message, $context);
    }

    /**
     * Log an alert message.
     */
    public static function alert($message, array $context = []): void
    {
        self::getInstance()->alert($message, $context);
    }

    /**
     * Log a critical message.
     */
    public static function critical($message, array $context = []): void
    {
        self::getInstance()->critical($message, $context);
    }

    /**
     * Log an error message.
     */
    public static function error($message, array $context = []): void
    {
        self::getInstance()->error($message, $context);
    }

    /**
     * Log a warning message.
     */
    public static function warning($message, array $context = []): void
    {
        self::getInstance()->warning($message, $context);
    }

    /**
     * Log a notice message.
     */
    public static function notice($message, array $context = []): void
    {
        self::getInstance()->notice($message, $context);
    }

    /**
     * Log an info message.
     */
    public static function info($message, array $context = []): void
    {
        self::getInstance()->info($message, $context);
    }

    /**
     * Log a debug message.
     */
    public static function debug($message, array $context = []): void
    {
        self::getInstance()->debug($message, $context);
    }

    /**
     * Log a message with custom level.
     */
    public static function log($level, $message, array $context = []): void
    {
        self::getInstance()->log($level, $message, $context);
    }

    /**
     * Log HTTP request.
     */
    public static function logHttpRequest(string $method, string $url, array $headers = [], array $body = []): void
    {
        self::getInstance()->logHttpRequest($method, $url, $headers, $body);
    }

    /**
     * Log HTTP response.
     */
    public static function logHttpResponse(int $statusCode, array $headers = [], string $body = '', float $duration = 0): void
    {
        self::getInstance()->logHttpResponse($statusCode, $headers, $body, $duration);
    }

    /**
     * Log performance metric.
     */
    public static function logPerformance(string $operation, float $duration, array $metadata = []): void
    {
        self::getInstance()->logPerformance($operation, $duration, $metadata);
    }

    /**
     * Log business event.
     */
    public static function logBusinessEvent(string $event, array $data = []): void
    {
        self::getInstance()->logBusinessEvent($event, $data);
    }
} 