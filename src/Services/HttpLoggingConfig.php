<?php

namespace Ebilet\Common\Services;

use Ebilet\Common\Interfaces\HttpLoggingConfigInterface;

/**
 * HTTP Logging Configuration
 * 
 * HTTP loglama konfigürasyonu için default implementation.
 * Environment variables ve config dosyasından ayarları okur.
 */
class HttpLoggingConfig implements HttpLoggingConfigInterface
{
    private array $config;

    public function __construct()
    {
        $this->config = $this->loadConfig();
    }

    /**
     * Load configuration from environment and config files.
     */
    private function loadConfig(): array
    {
        return [
            'enabled' => $this->getEnv('EBILET_HTTP_LOGGING_ENABLED', true),
            'excluded_paths' => $this->getEnvArray('EBILET_HTTP_LOGGING_EXCLUDED_PATHS', [
                '/health',
                '/metrics',
                '/favicon.ico',
                '/robots.txt',
                '/.well-known'
            ]),
            'excluded_methods' => $this->getEnvArray('EBILET_HTTP_LOGGING_EXCLUDED_METHODS', [
                'OPTIONS'
            ]),
            'excluded_domains' => $this->getEnvArray('EBILET_HTTP_LOGGING_EXCLUDED_DOMAINS', []),
            'sensitive_headers' => $this->getEnvArray('EBILET_HTTP_LOGGING_SENSITIVE_HEADERS', [
                'authorization',
                'cookie',
                'x-api-key',
                'x-auth-token',
                'x-csrf-token',
                'x-forwarded-for',
                'x-real-ip'
            ]),
            'sensitive_body_fields' => $this->getEnvArray('EBILET_HTTP_LOGGING_SENSITIVE_BODY_FIELDS', [
                'password',
                'token',
                'secret',
                'api_key',
                'auth_token',
                'refresh_token',
                'access_token',
                'credit_card',
                'ssn'
            ]),
            'sensitive_response_fields' => $this->getEnvArray('EBILET_HTTP_LOGGING_SENSITIVE_RESPONSE_FIELDS', [
                'token',
                'access_token',
                'refresh_token',
                'secret',
                'password',
                'credit_card'
            ]),
            'log_request_body' => $this->getEnv('EBILET_HTTP_LOGGING_REQUEST_BODY', true),
            'log_response_body' => $this->getEnv('EBILET_HTTP_LOGGING_RESPONSE_BODY', false),
            'max_body_size' => (int) $this->getEnv('EBILET_HTTP_LOGGING_MAX_BODY_SIZE', 1024 * 1024), // 1MB
            'slow_request_threshold' => (int) $this->getEnv('EBILET_HTTP_LOGGING_SLOW_THRESHOLD', 2000), // 2 seconds
        ];
    }

    /**
     * Get environment variable with fallback.
     */
    private function getEnv(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        
        if ($value === false) {
            return $default;
        }

        // Convert string booleans
        if (is_string($value)) {
            $lowerValue = strtolower($value);
            if ($lowerValue === 'true') return true;
            if ($lowerValue === 'false') return false;
        }

        return $value;
    }

    /**
     * Get environment variable as array.
     */
    private function getEnvArray(string $key, array $default = []): array
    {
        $value = $this->getEnv($key);
        
        if (is_string($value)) {
            return array_filter(array_map('trim', explode(',', $value)));
        }
        
        return $default;
    }

    /**
     * Check if HTTP logging is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->config['enabled'];
    }

    /**
     * Get excluded paths from logging.
     */
    public function getExcludedPaths(): array
    {
        return $this->config['excluded_paths'];
    }

    /**
     * Get excluded HTTP methods from logging.
     */
    public function getExcludedMethods(): array
    {
        return array_map('strtoupper', $this->config['excluded_methods']);
    }

    /**
     * Get excluded domains from logging.
     */
    public function getExcludedDomains(): array
    {
        return $this->config['excluded_domains'];
    }

    /**
     * Get sensitive headers to redact.
     */
    public function getSensitiveHeaders(): array
    {
        return array_map('strtolower', $this->config['sensitive_headers']);
    }

    /**
     * Get sensitive body fields to redact.
     */
    public function getSensitiveBodyFields(): array
    {
        return $this->config['sensitive_body_fields'];
    }

    /**
     * Get sensitive response fields to redact.
     */
    public function getSensitiveResponseFields(): array
    {
        return $this->config['sensitive_response_fields'];
    }

    /**
     * Check if request body should be logged.
     */
    public function shouldLogRequestBody(): bool
    {
        return $this->config['log_request_body'];
    }

    /**
     * Check if response body should be logged.
     */
    public function shouldLogResponseBody(): bool
    {
        return $this->config['log_response_body'];
    }

    /**
     * Get maximum body size to log.
     */
    public function getMaxBodySize(): int
    {
        return $this->config['max_body_size'];
    }

    /**
     * Get slow request threshold in milliseconds.
     */
    public function getSlowRequestThreshold(): int
    {
        return $this->config['slow_request_threshold'];
    }
} 