<?php

namespace Ebilet\Common\Interfaces;

use Illuminate\Http\Client\Response;

interface ServiceClientInterface
{
    /**
     * Make a GET request
     */
    public function get(string $endpoint, array $headers = []): Response;

    /**
     * Make a POST request
     */
    public function post(string $endpoint, array $data = [], array $headers = []): Response;

    /**
     * Make a PUT request
     */
    public function put(string $endpoint, array $data = [], array $headers = []): Response;

    /**
     * Make a PATCH request
     */
    public function patch(string $endpoint, array $data = [], array $headers = []): Response;

    /**
     * Make a DELETE request
     */
    public function delete(string $endpoint, array $headers = []): Response;

    /**
     * Make a custom request
     */
    public function request(string $method, string $endpoint, array $data = [], array $headers = []): Response;

    /**
     * Set authentication token
     */
    public function withToken(string $token): self;

    /**
     * Set custom headers
     */
    public function withHeaders(array $headers): self;

    /**
     * Set timeout
     */
    public function timeout(int $seconds): self;

    /**
     * Get service name
     */
    public function getServiceName(): string;

    /**
     * Get base URL
     */
    public function getBaseUrl(): string;
} 