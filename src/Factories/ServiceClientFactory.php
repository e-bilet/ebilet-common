<?php

namespace Ebilet\Common\Factories;

use Ebilet\Common\Interfaces\ServiceClientInterface;
use Ebilet\Common\Services\ServiceClient;
use Ebilet\Common\Exceptions\ServiceConfigurationException;

class ServiceClientFactory
{
    /**
     * Create a service client instance
     */
    public function create(string $serviceName): ServiceClientInterface
    {
        $this->validateServiceConfiguration($serviceName);
        
        return new ServiceClient($serviceName);
    }

    /**
     * Validate service configuration exists
     */
    private function validateServiceConfiguration(string $serviceName): void
    {
        $config = config("ebilet-common.services.{$serviceName}");
        
        if (!$config) {
            throw new ServiceConfigurationException(
                "Service configuration not found for: {$serviceName}"
            );
        }

        if (!isset($config['url'])) {
            throw new ServiceConfigurationException(
                "Service URL not configured for: {$serviceName}"
            );
        }
    }

    /**
     * Get all available service names
     */
    public function getAvailableServices(): array
    {
        $config = config('ebilet-common.services', []);
        return array_keys($config);
    }

    /**
     * Check if service is configured
     */
    public function isServiceConfigured(string $serviceName): bool
    {
        $config = config("ebilet-common.services.{$serviceName}");
        return $config && isset($config['url']);
    }
} 