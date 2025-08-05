<?php

namespace Ebilet\Common\Services;

use Ebilet\Common\Interfaces\ServiceClientInterface;
use Ebilet\Common\Factories\ServiceClientFactory;
use Ebilet\Common\Exceptions\ServiceConfigurationException;

class ServiceManager
{
    private ServiceClientFactory $factory;
    private array $clients = [];

    public function __construct(ServiceClientFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Get service client
     */
    public function client(string $serviceName): ServiceClientInterface
    {
        if (!isset($this->clients[$serviceName])) {
            $this->clients[$serviceName] = $this->factory->create($serviceName);
        }

        return $this->clients[$serviceName];
    }

    /**
     * Check if service is healthy
     */
    public function isHealthy(string $serviceName): bool
    {
        try {
            $client = $this->client($serviceName);
            $response = $client->get('/health');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all available services
     */
    public function getAvailableServices(): array
    {
        return $this->factory->getAvailableServices();
    }

    /**
     * Check if service is configured
     */
    public function isServiceConfigured(string $serviceName): bool
    {
        return $this->factory->isServiceConfigured($serviceName);
    }

    /**
     * Get health status of all services
     */
    public function getHealthStatus(): array
    {
        $services = $this->getAvailableServices();
        $status = [];

        foreach ($services as $serviceName) {
            $status[$serviceName] = [
                'configured' => $this->isServiceConfigured($serviceName),
                'healthy' => $this->isHealthy($serviceName),
            ];
        }

        return $status;
    }

    /**
     * Clear cached clients
     */
    public function clearCache(): void
    {
        $this->clients = [];
    }
} 