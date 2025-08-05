<?php

namespace Ebilet\Common\Managers;

use Ebilet\Common\Interfaces\QueueProviderInterface;
use Ebilet\Common\Providers\RabbitMQProvider;
use Ebilet\Common\Enums\LogMessageType;

/**
 * Queue Manager
 * 
 * Merkezi queue yönetimi için singleton pattern kullanan manager sınıfı.
 * Strategy pattern ile farklı queue provider'ları destekler.
 */
class QueueManager
{
    private static ?self $instance = null;
    private ?QueueProviderInterface $provider = null;
    private bool $isConnected = false;
    private string $logChannel = 'log-messages';
    private string $metricsChannel = 'metrics';
    private string $eventsChannel = 'events';

    private function __construct()
    {
        // Private constructor for singleton pattern
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set queue provider
     */
    public function setProvider(QueueProviderInterface $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * Get current provider
     */
    public function getProvider(): ?QueueProviderInterface
    {
        return $this->provider;
    }

    /**
     * Connect to queue provider
     */
    public function connect(): bool
    {
        if (!$this->provider) {
            $this->provider = new RabbitMQProvider();
        }

        $this->isConnected = $this->provider->connect();
        
        if ($this->isConnected) {
            $this->initializeChannels();
        }

        return $this->isConnected;
    }

    /**
     * Disconnect from queue provider
     */
    public function disconnect(): void
    {
        if ($this->provider) {
            $this->provider->disconnect();
        }
        $this->isConnected = false;
    }

    /**
     * Check if connected
     */
    public function isConnected(): bool
    {
        return $this->isConnected && $this->provider?->isConnected();
    }

    /**
     * Initialize required channels
     */
    private function initializeChannels(): void
    {
        if (!$this->provider) {
            if (class_exists('\Log')) {
                \Log::error("QueueManager: No provider available for channel initialization");
            }
            return;
        }

        if (class_exists('\Log')) {
            \Log::info("QueueManager: Starting channel initialization");
        }

        // Create log-messages channel
        try {
            $result = $this->provider->createQueue($this->logChannel, [
                'durable' => true,
                'arguments' => [
                    'x-message-ttl' => 86400000, // 24 hours
                    'x-max-length' => 10000
                ]
            ]);
            if (class_exists('\Log')) {
                \Log::info("QueueManager: log-messages channel creation result: " . ($result ? 'success' : 'failed'));
            }
        } catch (\Exception $e) {
            if (class_exists('\Log')) {
                \Log::error("QueueManager: Failed to create log-messages channel: " . $e->getMessage());
            }
        }

        // Create metrics channel
        try {
            $result = $this->provider->createQueue($this->metricsChannel, [
                'durable' => true,
                'arguments' => [
                    'x-message-ttl' => 604800000, // 7 days
                    'x-max-length' => 50000
                ]
            ]);
            if (class_exists('\Log')) {
                \Log::info("QueueManager: metrics channel creation result: " . ($result ? 'success' : 'failed'));
            }
        } catch (\Exception $e) {
            if (class_exists('\Log')) {
                \Log::error("QueueManager: Failed to create metrics channel: " . $e->getMessage());
            }
        }

        // Create events channel
        try {
            $result = $this->provider->createQueue($this->eventsChannel, [
                'durable' => true,
                'arguments' => [
                    'x-message-ttl' => 2592000000, // 30 days
                    'x-max-length' => 100000
                ]
            ]);
            if (class_exists('\Log')) {
                \Log::info("QueueManager: events channel creation result: " . ($result ? 'success' : 'failed'));
            }
        } catch (\Exception $e) {
            if (class_exists('\Log')) {
                \Log::error("QueueManager: Failed to create events channel: " . $e->getMessage());
            }
        }

        if (class_exists('\Log')) {
            \Log::info("QueueManager: Channel initialization completed");
        }
    }

    /**
     * Send log message to queue
     */
    public function sendLog(array $logData, LogMessageType $messageType = null): bool
    {
        if (!$this->isConnected()) {
            return false;
        }

        $messageType ??= LogMessageType::APPLICATION_INFO;
        
        $enrichedLogData = array_merge($logData, [
            'message_type' => $messageType->value,
            'log_level' => $messageType->getLogLevel(),
            'timestamp' => $logData['timestamp'] ?? date('c'),
            'service_name' => $logData['service'] ?? $this->getServiceName(),
            'host' => $logData['host'] ?? gethostname(),
            'pid' => $logData['pid'] ?? getmypid(),
            'memory_usage' => $logData['memory_usage'] ?? memory_get_usage(true),
            'memory_peak' => $logData['memory_peak'] ?? memory_get_peak_usage(true)
        ]);

        return $this->provider->send($this->logChannel, $enrichedLogData, [
            'delivery_mode' => 2, // Persistent
            'priority' => $messageType->isCritical() ? 10 : 0,
            'timestamp' => time()
        ]);
    }

    /**
     * Send metric data to queue
     */
    public function sendMetric(array $metricData): bool
    {
        if (!$this->isConnected()) {
            return false;
        }

        $enrichedMetricData = array_merge($metricData, [
            'timestamp' => $metricData['timestamp'] ?? date('c'),
            'service_name' => $metricData['service'] ?? $this->getServiceName(),
            'host' => $metricData['host'] ?? gethostname()
        ]);

        return $this->provider->send($this->metricsChannel, $enrichedMetricData, [
            'delivery_mode' => 2, // Persistent
            'timestamp' => time()
        ]);
    }

    /**
     * Send event data to queue
     */
    public function sendEvent(array $eventData): bool
    {
        if (!$this->isConnected()) {
            return false;
        }

        $enrichedEventData = array_merge($eventData, [
            'timestamp' => $eventData['timestamp'] ?? date('c'),
            'service_name' => $eventData['service'] ?? $this->getServiceName(),
            'host' => $eventData['host'] ?? gethostname()
        ]);

        return $this->provider->send($this->eventsChannel, $enrichedEventData, [
            'delivery_mode' => 2, // Persistent
            'timestamp' => time()
        ]);
    }

    /**
     * Send generic message to queue
     */
    public function send(string $queue, array $data, array $options = []): bool
    {
        if (!$this->isConnected()) {
            return false;
        }

        return $this->provider->send($queue, $data, $options);
    }

    /**
     * Receive messages from queue
     */
    public function receive(string $queue, callable $callback): void
    {
        if (!$this->isConnected()) {
            return;
        }

        $this->provider->receive($queue, $callback);
    }

    /**
     * Get queue statistics
     */
    public function getQueueStats(string $queue): array
    {
        if (!$this->isConnected()) {
            return [];
        }

        return $this->provider->getQueueStats($queue);
    }

    /**
     * Get provider name
     */
    public function getProviderName(): string
    {
        return $this->provider?->getProviderName() ?? 'unknown';
    }

    /**
     * Get service name from environment
     */
    private function getServiceName(): string
    {
        return $_ENV['APP_NAME'] ?? $_SERVER['APP_NAME'] ?? getenv('APP_NAME') ?? 'unknown-service';
    }

    /**
     * Get log channel name
     */
    public function getLogChannel(): string
    {
        return $this->logChannel;
    }

    /**
     * Get metrics channel name
     */
    public function getMetricsChannel(): string
    {
        return $this->metricsChannel;
    }

    /**
     * Get events channel name
     */
    public function getEventsChannel(): string
    {
        return $this->eventsChannel;
    }

    /**
     * Prevent cloning
     */
    private function __clone()
    {
        // Prevent cloning
    }

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
} 