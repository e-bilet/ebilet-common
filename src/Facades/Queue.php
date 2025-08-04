<?php

namespace Ebilet\Common\Facades;

use Ebilet\Common\Managers\QueueManager;

/**
* Queue Facade for easy access to queue operations.
* 
* @method static bool connect()
* @method static void disconnect()
* @method static bool isConnected()
* @method static bool send(string $queue, array $data, array $options = [])
* @method static void receive(string $queue, callable $callback)
* @method static bool createQueue(string $queue, array $options = [])
* @method static bool deleteQueue(string $queue)
* @method static array getQueueStats(string $queue)
* @method static string getProviderName()
* @method static bool sendLog(array $logData, string $queue = 'logs')
* @method static bool sendMetric(array $metricData, string $queue = 'metrics')
* @method static bool sendEvent(array $eventData, string $queue = 'events')
* 
* @see \Ebilet\Common\Managers\QueueManager
*/
class Queue extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'ebilet.queue';
    }
} 