<?php

namespace Ebilet\Common\Facades;

use Ebilet\Common\Services\CentralizedLogger;

/**
* Simple Log facade for centralized logging.
* 
* @method static void emergency(string $message, array $context = [])
* @method static void alert(string $message, array $context = [])
* @method static void critical(string $message, array $context = [])
* @method static void error(string $message, array $context = [])
* @method static void warning(string $message, array $context = [])
* @method static void notice(string $message, array $context = [])
* @method static void info(string $message, array $context = [])
* @method static void debug(string $message, array $context = [])
* @method static void log(string $level, string $message, array $context = [])
* @method static void logHttpRequest(string $method, string $url, array $headers = [], array $body = [])
* @method static void logHttpResponse(int $statusCode, array $headers = [], string $body = '', float $duration = 0)
* @method static void logPerformance(string $operation, float $duration, array $metadata = [])
* @method static void logBusinessEvent(string $event, array $data = [])
* 
* @see \Ebilet\Common\Services\CentralizedLogger
*/
class Log extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'ebilet.logger';
    }
} 