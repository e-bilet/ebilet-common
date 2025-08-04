<?php

namespace Ebilet\Common\Interfaces;

/**
 * Queue Provider Interface
 * 
 * Bu interface tüm queue provider'ları için ortak contract sağlar.
 * Strategy pattern ile farklı queue sistemleri desteklenir.
 */
interface QueueProviderInterface
{
    /**
     * Connect to queue provider
     */
    public function connect(): bool;

    /**
     * Disconnect from queue provider
     */
    public function disconnect(): void;

    /**
     * Check if connected
     */
    public function isConnected(): bool;

    /**
     * Send message to queue
     */
    public function send(string $queue, array $data, array $options = []): bool;

    /**
     * Receive message from queue
     */
    public function receive(string $queue, callable $callback): void;

    /**
     * Create queue if not exists
     */
    public function createQueue(string $queue, array $options = []): bool;

    /**
     * Delete queue
     */
    public function deleteQueue(string $queue): bool;

    /**
     * Get queue statistics
     */
    public function getQueueStats(string $queue): array;

    /**
     * Get provider name
     */
    public function getProviderName(): string;
} 