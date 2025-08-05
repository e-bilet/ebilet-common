<?php

namespace Ebilet\Common\Services;

use Illuminate\Support\Facades\Config;

class ConfigManager
{
    /**
     * Get configuration value with fallback support.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        // First try to get from ebilet-common config
        $value = Config::get("ebilet-common.{$key}");
        
        if ($value !== null) {
            return $value;
        }

        // Then try ebilet-logging config
        $value = Config::get("ebilet-logging.{$key}");
        
        if ($value !== null) {
            return $value;
        }

        // Finally return default
        return $default;
    }

    /**
     * Get RabbitMQ configuration.
     *
     * @return array
     */
    public static function getRabbitMQConfig(): array
    {
        return self::get('rabbitmq', []);
    }

    /**
     * Get Queue configuration.
     *
     * @return array
     */
    public static function getQueueConfig(): array
    {
        return self::get('queues', []);
    }

    /**
     * Get Logging configuration.
     *
     * @return array
     */
    public static function getLoggingConfig(): array
    {
        return self::get('logging', []);
    }

    /**
     * Get HTTP Logging configuration.
     *
     * @return array
     */
    public static function getHttpLoggingConfig(): array
    {
        return self::get('http_logging', []);
    }

    /**
     * Get Performance configuration.
     *
     * @return array
     */
    public static function getPerformanceConfig(): array
    {
        return self::get('performance', []);
    }

    /**
     * Get Business Events configuration.
     *
     * @return array
     */
    public static function getBusinessEventsConfig(): array
    {
        return self::get('business_events', []);
    }

    /**
     * Get Error Handling configuration.
     *
     * @return array
     */
    public static function getErrorHandlingConfig(): array
    {
        return self::get('error_handling', []);
    }

    /**
     * Get Security configuration.
     *
     * @return array
     */
    public static function getSecurityConfig(): array
    {
        return self::get('security', []);
    }

    /**
     * Check if a feature is enabled.
     *
     * @param string $feature
     * @return bool
     */
    public static function isEnabled(string $feature): bool
    {
        return self::get($feature, false);
    }

    /**
     * Get all configuration as array.
     *
     * @return array
     */
    public static function getAllConfig(): array
    {
        return [
            'rabbitmq' => self::getRabbitMQConfig(),
            'queues' => self::getQueueConfig(),
            'logging' => self::getLoggingConfig(),
            'http_logging' => self::getHttpLoggingConfig(),
            'performance' => self::getPerformanceConfig(),
            'business_events' => self::getBusinessEventsConfig(),
            'error_handling' => self::getErrorHandlingConfig(),
            'security' => self::getSecurityConfig(),
        ];
    }

    /**
     * Validate configuration.
     *
     * @return array
     */
    public static function validateConfig(): array
    {
        $errors = [];
        
        $rabbitMQConfig = self::getRabbitMQConfig();
        if (empty($rabbitMQConfig['host'])) {
            $errors[] = 'RabbitMQ host is not configured';
        }

        $loggingConfig = self::getLoggingConfig();
        if (empty($loggingConfig['service_name'])) {
            $errors[] = 'Service name is not configured';
        }

        return $errors;
    }

    /**
     * Get configuration for specific environment.
     *
     * @param string $environment
     * @return array
     */
    public static function getConfigForEnvironment(string $environment): array
    {
        $config = self::getAllConfig();
        
        // Environment specific overrides
        switch ($environment) {
            case 'production':
                $config['logging']['log_level'] = 'warning';
                $config['http_logging']['log_response_body'] = false;
                break;
                
            case 'staging':
                $config['logging']['log_level'] = 'info';
                $config['http_logging']['log_response_body'] = false;
                break;
                
            case 'development':
                $config['logging']['log_level'] = 'debug';
                $config['http_logging']['log_response_body'] = true;
                break;
        }
        
        return $config;
    }
} 