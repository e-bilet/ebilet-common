<?php

namespace Ebilet\Common\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Ebilet\Common\Services\ServiceClient;
use Ebilet\Common\Interfaces\ServiceClientInterface;
use Ebilet\Common\Factories\ServiceClientFactory;

class ServiceCommunicationProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerServiceClientFactory();
        $this->registerServiceClients();
        $this->registerFacades();
    }

    /**
     * Register service client factory
     */
    private function registerServiceClientFactory(): void
    {
        $this->app->singleton(ServiceClientFactory::class, function ($app) {
            return new ServiceClientFactory();
        });
    }

    /**
     * Register individual service clients
     */
    private function registerServiceClients(): void
    {
        $services = $this->getConfiguredServices();

        foreach ($services as $serviceName) {
            $this->app->singleton("ebilet.{$serviceName}", function ($app) use ($serviceName) {
                return $app->make(ServiceClientFactory::class)->create($serviceName);
            });
        }
    }

    /**
     * Register facades
     */
    private function registerFacades(): void
    {
        $services = $this->getConfiguredServices();

        foreach ($services as $serviceName) {
            $facadeClass = $this->getFacadeClass($serviceName);
            if (class_exists($facadeClass)) {
                $this->app->alias("ebilet.{$serviceName}", $facadeClass);
            }
        }
    }

    /**
     * Get configured services from config
     */
    private function getConfiguredServices(): array
    {
        $config = config('ebilet-common.services', []);
        return array_keys($config);
    }

    /**
     * Get facade class name for service
     */
    private function getFacadeClass(string $serviceName): string
    {
        $className = ucfirst($serviceName);
        return "\\Ebilet\\Common\\Facades\\{$className}";
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Bootstrap logic if needed
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        $services = $this->getConfiguredServices();
        $provides = [];

        foreach ($services as $serviceName) {
            $provides[] = "ebilet.{$serviceName}";
        }

        return $provides;
    }
} 