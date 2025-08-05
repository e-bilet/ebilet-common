<?php

namespace Ebilet\Common\Managers;

use Ebilet\Common\Interfaces\QueueProviderInterface;
use Ebilet\Common\Providers\RabbitMQProvider;
use Ebilet\Common\Enums\LogMessageType;

/**
 * Queue Manager
 * 
 * Merkezi queue yönetimi için singleton sınıf.
 * RabbitMQ ve diğer queue sistemleri için provider pattern kullanır.
 */
class QueueManager
{
    private static ?self $instance = null;
    private ?QueueProviderInterface $provider = null;
    private bool $isConnected = false;
    private array $config;

    private function __construct()
    {
        $this->config = config('ebilet-common', []);
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
            return;
        }

        $queueConfig = $this->config['queues'] ?? [];
        $channels = $queueConfig['channels'] ?? [];
        $settings = $queueConfig['settings'] ?? [];

        // Initialize log-messages channel
        $logChannel = $channels['log_messages'] ?? 'log-messages';
        $logSettings = $settings['log_messages'] ?? [];
        
        try {
            $result = $this->provider->createQueue($logChannel, [
                'durable' => $logSettings['durable'] ?? true,
                'arguments' => [
                    'x-message-ttl' => $logSettings['ttl'] ?? 86400000,
                    'x-max-length' => $logSettings['max_length'] ?? 10000
                ]
            ]);

        } catch (\Exception $e) {
            if (class_exists('\Log')) {
                \Log::error("QueueManager: Failed to create log-messages channel: " . $e->getMessage());
            }
        }

        // Initialize metrics channel
        $metricsChannel = $channels['metrics'] ?? 'metrics';
        $metricsSettings = $settings['metrics'] ?? [];
        
        try {
            $result = $this->provider->createQueue($metricsChannel, [
                'durable' => $metricsSettings['durable'] ?? true,
                'arguments' => [
                    'x-message-ttl' => $metricsSettings['ttl'] ?? 604800000,
                    'x-max-length' => $metricsSettings['max_length'] ?? 50000
                ]
            ]);

        } catch (\Exception $e) {
            if (class_exists('\Log')) {
                \Log::error("QueueManager: Failed to create metrics channel: " . $e->getMessage());
            }
        }

        // Initialize events channel
        $eventsChannel = $channels['events'] ?? 'events';
        $eventsSettings = $settings['events'] ?? [];
        
        try {
            $result = $this->provider->createQueue($eventsChannel, [
                'durable' => $eventsSettings['durable'] ?? true,
                'arguments' => [
                    'x-message-ttl' => $eventsSettings['ttl'] ?? 2592000000,
                    'x-max-length' => $eventsSettings['max_length'] ?? 100000
                ]
            ]);

        } catch (\Exception $e) {
            if (class_exists('\Log')) {
                \Log::error("QueueManager: Failed to create events channel: " . $e->getMessage());
            }
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
        $queueConfig = $this->config['queues'] ?? [];
        $channels = $queueConfig['channels'] ?? [];
        $deliveryMode = $queueConfig['delivery_mode'] ?? 2;

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

        $logChannel = $channels['log_messages'] ?? 'log-messages';

        return $this->provider->send($logChannel, $enrichedLogData, [
            'delivery_mode' => $deliveryMode,
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

        $queueConfig = $this->config['queues'] ?? [];
        $channels = $queueConfig['channels'] ?? [];
        $deliveryMode = $queueConfig['delivery_mode'] ?? 2;

        $enrichedMetricData = array_merge($metricData, [
            'timestamp' => $metricData['timestamp'] ?? date('c'),
            'service_name' => $metricData['service'] ?? $this->getServiceName(),
            'host' => $metricData['host'] ?? gethostname(),
            'pid' => $metricData['pid'] ?? getmypid()
        ]);

        $metricsChannel = $channels['metrics'] ?? 'metrics';

        return $this->provider->send($metricsChannel, $enrichedMetricData, [
            'delivery_mode' => $deliveryMode,
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

        $queueConfig = $this->config['queues'] ?? [];
        $channels = $queueConfig['channels'] ?? [];
        $deliveryMode = $queueConfig['delivery_mode'] ?? 2;

        $enrichedEventData = array_merge($eventData, [
            'timestamp' => $eventData['timestamp'] ?? date('c'),
            'service_name' => $eventData['service'] ?? $this->getServiceName(),
            'host' => $eventData['host'] ?? gethostname(),
            'pid' => $eventData['pid'] ?? getmypid()
        ]);

        $eventsChannel = $channels['events'] ?? 'events';

        return $this->provider->send($eventsChannel, $enrichedEventData, [
            'delivery_mode' => $deliveryMode,
            'timestamp' => time()
        ]);
    }

    /**
     * Send message to specific queue
     */
    public function send(string $queue, array $data, array $options = []): bool
    {
        if (!$this->isConnected()) {
            return false;
        }

        $queueConfig = $this->config['queues'] ?? [];
        $deliveryMode = $queueConfig['delivery_mode'] ?? 2;

        $defaultOptions = [
            'delivery_mode' => $deliveryMode,
            'timestamp' => time()
        ];

        return $this->provider->send($queue, $data, array_merge($defaultOptions, $options));
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
     * Get service name from config
     */
    private function getServiceName(): string
    {
        $loggingConfig = $this->config['logging'] ?? [];
        return $loggingConfig['service_name'] ?? 'unknown-service';
    }

    /**
     * Get log channel name from config
     */
    public function getLogChannel(): string
    {
        $queueConfig = $this->config['queues'] ?? [];
        $channels = $queueConfig['channels'] ?? [];
        return $channels['log_messages'] ?? 'log-messages';
    }

    /**
     * Get metrics channel name from config
     */
    public function getMetricsChannel(): string
    {
        $queueConfig = $this->config['queues'] ?? [];
        $channels = $queueConfig['channels'] ?? [];
        return $channels['metrics'] ?? 'metrics';
    }

    /**
     * Get events channel name from config
     */
    public function getEventsChannel(): string
    {
        $queueConfig = $this->config['queues'] ?? [];
        $channels = $queueConfig['channels'] ?? [];
        return $channels['events'] ?? 'events';
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