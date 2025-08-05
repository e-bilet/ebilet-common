<?php

namespace Ebilet\Common\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Ebilet\Common\Services\CentralizedLogger;
use Ebilet\Common\Facades\Log;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/ebilet-common.php', 'ebilet-common'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/logging.php', 'ebilet-logging'
        );

        $this->app->singleton('ebilet.logger', function ($app) {
            return new CentralizedLogger();
        });

        $this->app->alias('ebilet.logger', CentralizedLogger::class);

        $this->app->singleton('ebilet.queue', function ($app) {
            return \Ebilet\Common\Managers\QueueManager::getInstance();
        });

        $this->app->alias('ebilet.queue', \Ebilet\Common\Managers\QueueManager::class);

        // Register facades
        $this->app->alias('ebilet.logger', \Ebilet\Common\Facades\Log::class);
        $this->app->alias('ebilet.queue', \Ebilet\Common\Facades\Queue::class);
        $this->app->alias('ebilet.config', \Ebilet\Common\Facades\Config::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config files
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/ebilet-common.php' => config_path('ebilet-common.php'),
                __DIR__ . '/../../config/logging.php' => config_path('ebilet-logging.php'),
            ], 'ebilet-common-config');

            // Publish individual config files
            $this->publishes([
                __DIR__ . '/../../config/ebilet-common.php' => config_path('ebilet-common.php'),
            ], 'ebilet-common-main-config');

            $this->publishes([
                __DIR__ . '/../../config/logging.php' => config_path('ebilet-logging.php'),
            ], 'ebilet-common-logging-config');
        }

        // Register middleware
        $this->app['router']->aliasMiddleware('ebilet.logging', \Ebilet\Common\Middleware\HttpLoggingMiddleware::class);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return ['ebilet.logger', 'ebilet.queue'];
    }
} 