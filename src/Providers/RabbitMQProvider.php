<?php

namespace Ebilet\Common\Providers;

use PhpAmqpLib\Wire\AMQPTable;
use Ebilet\Common\Interfaces\QueueProviderInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;

/**
 * RabbitMQ Queue Provider
 *
 * RabbitMQ queue sistemi için provider implementation.
 */
class RabbitMQProvider implements QueueProviderInterface
{
    private ?AMQPStreamConnection $connection = null;
    private ?AMQPChannel $channel = null;
    private string $host;
    private int $port;
    private string $user;
    private string $password;
    private string $vhost;

    public function __construct(array $config = [])
    {
        $this->host = $config['host'] ?? $this->getEnv('RABBITMQ_HOST', 'localhost');
        $this->port = (int) ($config['port'] ?? $this->getEnv('RABBITMQ_PORT', '5672'));
        $this->user = $config['user'] ?? $this->getEnv('RABBITMQ_USER', 'guest');
        $this->password = $config['password'] ?? $this->getEnv('RABBITMQ_PASSWORD', 'guest');
        $this->vhost = $config['vhost'] ?? $this->getEnv('RABBITMQ_VHOST', '/');
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
     * Connect to RabbitMQ
     */
    public function connect(): bool
    {
        try {
            error_log("Attempting to connect to RabbitMQ at {$this->host}:{$this->port}");

            $this->connection = new AMQPStreamConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->password,
                $this->vhost
            );

            $this->channel = $this->connection->channel();
            error_log("Successfully connected to RabbitMQ");
            return true;
        } catch (\Exception $e) {
            error_log("RabbitMQ connection failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Disconnect from RabbitMQ
     */
    public function disconnect(): void
    {
        if (isset($this->channel)) {
            $this->channel->close();
        }
        if (isset($this->connection)) {
            $this->connection->close();
        }
    }

    /**
     * Check if connected
     */
    public function isConnected(): bool
    {
        return isset($this->connection) && $this->connection->isConnected();
    }

    /**
     * Send message to queue
     */
    public function send(string $queue, array $data, array $options = []): bool
    {
        try {
            if (!isset($this->channel)) {
                return false;
            }

            $message = new AMQPMessage(
                json_encode($data),
                [
                    'content_type' => 'application/json',
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                    'timestamp' => time(),
                    ...$options
                ]
            );

            $this->channel->basic_publish($message, '', $queue);
            return true;
        } catch (\Exception $e) {
            error_log("Failed to send message to RabbitMQ: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Receive message from queue
     */
    public function receive(string $queue, callable $callback): void
    {
        try {
            if (!isset($this->channel)) {
                return;
            }

            $this->channel->basic_consume(
                $queue,
                '',
                false,
                false,
                false,
                false,
                function (AMQPMessage $message) use ($callback) {
                    $data = json_decode($message->body, true);
                    $callback($data);
                    $message->ack();
                }
            );

            while ($this->channel->is_consuming()) {
                $this->channel->wait();
            }
        } catch (\Exception $e) {
            error_log("Failed to receive message from RabbitMQ: " . $e->getMessage());
        }
    }

    /**
     * Create queue if not exists
     */

    public function createQueue(string $queue, array $options = []): bool
    {
        try {
            if (!isset($this->channel)) {
                return false;
            }

            $arguments = $options['arguments'] ?? null;
            if (is_array($arguments)) {
                $arguments = new AMQPTable($arguments);
            }

            $this->channel->queue_declare(
                $queue,
                $options['passive'] ?? false,
                $options['durable'] ?? true,
                $options['exclusive'] ?? false,
                $options['auto_delete'] ?? false,
                $options['nowait'] ?? false,
                $arguments
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete queue
     */
    public function deleteQueue(string $queue): bool
    {
        try {
            if (!isset($this->channel)) {
                return false;
            }

            $this->channel->queue_delete($queue);
            return true;
        } catch (\Exception $e) {
            error_log("Failed to delete queue: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get queue statistics
     */
    public function getQueueStats(string $queue): array
    {
        try {
            if (!isset($this->channel)) {
                return [];
            }

            $stats = $this->channel->queue_declare(
                $queue,
                true,
                true,
                false,
                false
            );

            return [
                'queue' => $queue,
                'message_count' => $stats[1],
                'consumer_count' => $stats[2]
            ];
        } catch (\Exception $e) {
            error_log("Failed to get queue stats: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get provider name
     */
    public function getProviderName(): string
    {
        return 'rabbitmq';
    }
}
