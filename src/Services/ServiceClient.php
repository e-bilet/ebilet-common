<?php

namespace Ebilet\Common\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Ebilet\Common\Facades\Log;
use Ebilet\Common\Interfaces\ServiceClientInterface;

class ServiceClient implements ServiceClientInterface
{
    private string $baseUrl;
    private array $defaultHeaders = [];
    private ?string $authToken = null;
    private int $timeout = 30;
    private string $serviceName;

    public function __construct(string $serviceName)
    {
        $this->serviceName = $serviceName;
        $this->baseUrl = $this->getServiceUrl($serviceName);
        $this->defaultHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'E-Bilet-Service-Client/1.0',
        ];
    }

    /**
     * Get service URL from config
     */
    private function getServiceUrl(string $serviceName): string
    {
        $config = config("ebilet-common.services.{$serviceName}");
        return $config['url'] ?? env(strtoupper($serviceName) . '_SERVICE_URL', "http://{$serviceName}-service:8000");
    }

    /**
     * Make a GET request
     */
    public function get(string $endpoint, array $headers = []): Response
    {
        return $this->request('GET', $endpoint, [], $headers);
    }

    /**
     * Make a POST request
     */
    public function post(string $endpoint, array $data = [], array $headers = []): Response
    {
        return $this->request('POST', $endpoint, $data, $headers);
    }

    /**
     * Make a PUT request
     */
    public function put(string $endpoint, array $data = [], array $headers = []): Response
    {
        return $this->request('PUT', $endpoint, $data, $headers);
    }

    /**
     * Make a PATCH request
     */
    public function patch(string $endpoint, array $data = [], array $headers = []): Response
    {
        return $this->request('PATCH', $endpoint, $data, $headers);
    }

    /**
     * Make a DELETE request
     */
    public function delete(string $endpoint, array $headers = []): Response
    {
        return $this->request('DELETE', $endpoint, [], $headers);
    }

    /**
     * Make a custom request
     */
    public function request(string $method, string $endpoint, array $data = [], array $headers = []): Response
    {
        $url = $this->buildUrl($endpoint);
        $requestHeaders = array_merge($this->defaultHeaders, $headers);

        if ($this->authToken) {
            $requestHeaders['Authorization'] = 'Bearer ' . $this->authToken;
        }

        $startTime = microtime(true);

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($requestHeaders)
                ->$method($url, $data);

            $duration = (microtime(true) - $startTime) * 1000;
            $this->logRequest($method, $url, $data, $response, $duration);

            return $response;

        } catch (\Exception $e) {
            $duration = (microtime(true) - $startTime) * 1000;
            $this->logError($method, $url, $data, $e, $duration);
            throw $e;
        }
    }

    /**
     * Set authentication token
     */
    public function withToken(string $token): self
    {
        $this->authToken = $token;
        return $this;
    }

    /**
     * Set custom headers
     */
    public function withHeaders(array $headers): self
    {
        $this->defaultHeaders = array_merge($this->defaultHeaders, $headers);
        return $this;
    }

    /**
     * Set timeout
     */
    public function timeout(int $seconds): self
    {
        $this->timeout = $seconds;
        return $this;
    }

    /**
     * Get service name
     */
    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    /**
     * Get base URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Build full URL
     */
    private function buildUrl(string $endpoint): string
    {
        $endpoint = ltrim($endpoint, '/');
        return $this->baseUrl . '/' . $endpoint;
    }

    /**
     * Log request
     */
    private function logRequest(string $method, string $url, array $data, Response $response, float $duration): void
    {
        $logData = [
            'service' => $this->serviceName,
            'method' => $method,
            'url' => $url,
            'status_code' => $response->status(),
            'duration_ms' => round($duration, 2),
        ];

        if ($response->successful()) {
            Log::info("Service request successful", $logData);
        } else {
            Log::warning("Service request failed", $logData);
        }
    }

    /**
     * Log error
     */
    private function logError(string $method, string $url, array $data, \Exception $exception, float $duration): void
    {
        Log::error("Service request error", [
            'service' => $this->serviceName,
            'method' => $method,
            'url' => $url,
            'duration_ms' => round($duration, 2),
            'error' => $exception->getMessage(),
        ]);
    }
} 