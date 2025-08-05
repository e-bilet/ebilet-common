<?php

namespace Ebilet\Common\Facades;

use Illuminate\Support\Facades\Facade;
use Ebilet\Common\Services\ConfigManager;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static array getRabbitMQConfig()
 * @method static array getQueueConfig()
 * @method static array getLoggingConfig()
 * @method static array getHttpLoggingConfig()
 * @method static array getPerformanceConfig()
 * @method static array getBusinessEventsConfig()
 * @method static array getErrorHandlingConfig()
 * @method static array getSecurityConfig()
 * @method static bool isEnabled(string $feature)
 * @method static array getAllConfig()
 * @method static array validateConfig()
 * @method static array getConfigForEnvironment(string $environment)
 */
class Config extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'ebilet.config';
    }

    /**
     * Get the facade accessor.
     *
     * @return ConfigManager
     */
    public static function getFacadeRoot(): ConfigManager
    {
        return new ConfigManager();
    }
} 