<?php

namespace Ebilet\Common\Exceptions;

/**
 * Logging Exception
 * 
 * Merkezi loglama sistemi için özel exception sınıfı.
 */
class LoggingException extends \Exception
{
    private array $context;

    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * Get context data
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Create exception for queue connection failure
     */
    public static function queueConnectionFailed(string $provider, string $reason): self
    {
        return new self(
            "Queue connection failed for provider '{$provider}': {$reason}",
            500,
            null,
            ['provider' => $provider, 'reason' => $reason]
        );
    }

    /**
     * Create exception for message sending failure
     */
    public static function messageSendFailed(string $queue, string $reason): self
    {
        return new self(
            "Failed to send message to queue '{$queue}': {$reason}",
            500,
            null,
            ['queue' => $queue, 'reason' => $reason]
        );
    }

    /**
     * Create exception for configuration error
     */
    public static function configurationError(string $configKey, string $reason): self
    {
        return new self(
            "Configuration error for '{$configKey}': {$reason}",
            500,
            null,
            ['config_key' => $configKey, 'reason' => $reason]
        );
    }

    /**
     * Create exception for invalid log level
     */
    public static function invalidLogLevel(string $level): self
    {
        return new self(
            "Invalid log level '{$level}'",
            400,
            null,
            ['level' => $level]
        );
    }

    /**
     * Create exception for queue not found
     */
    public static function queueNotFound(string $queue): self
    {
        return new self(
            "Queue '{$queue}' not found",
            404,
            null,
            ['queue' => $queue]
        );
    }
} 