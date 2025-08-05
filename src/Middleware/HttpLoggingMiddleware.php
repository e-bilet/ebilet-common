<?php

namespace Ebilet\Common\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ebilet\Common\Services\HttpLoggingConfig;
use Ebilet\Common\Services\Logger;

/**
 * HTTP Logging Middleware
 * 
 * HTTP request ve response'ları otomatik olarak loglar.
 * Config'de belirtilen endpoint'lerde çalışır.
 */
class HttpLoggingMiddleware
{
    private HttpLoggingConfig $config;
    private array $endpoints;

    public function __construct(HttpLoggingConfig $config)
    {
        $this->config = $config;
        $this->endpoints = $this->parseEndpoints();
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->config->isEnabled()) {
            return $next($request);
        }

        // Check if current endpoint should be logged
        if (!$this->shouldLogEndpoint($request)) {
            return $next($request);
        }

        $startTime = microtime(true);
        
        // Log the request
        $this->logRequest($request);

        // Process the request
        $response = $next($request);

        // Calculate execution time
        $executionTime = (microtime(true) - $startTime) * 1000;

        // Log the response
        $this->logResponse($request, $response, $executionTime);

        return $response;
    }

    /**
     * Parse endpoints configuration
     */
    private function parseEndpoints(): array
    {
        $endpoints = $this->config->getEndpoints();
        
        if ($endpoints === '*' || $endpoints === ['*']) {
            return ['*']; // Log all endpoints
        }
        
        if (is_string($endpoints)) {
            return array_filter(array_map('trim', explode(',', $endpoints)));
        }
        
        return is_array($endpoints) ? $endpoints : ['*'];
    }

    /**
     * Check if current endpoint should be logged
     */
    private function shouldLogEndpoint(Request $request): bool
    {
        $path = $request->path();
        $method = $request->method();

        // Check if all endpoints should be logged
        if (in_array('*', $this->endpoints)) {
            // Check exclusions
            return !$this->isExcluded($request);
        }

        // Check if current endpoint is in the allowed list
        $currentEndpoint = $method . ':' . $path;
        
        foreach ($this->endpoints as $endpoint) {
            if ($this->matchesEndpoint($currentEndpoint, $endpoint)) {
                return !$this->isExcluded($request);
            }
        }

        return false;
    }

    /**
     * Check if endpoint matches pattern
     */
    private function matchesEndpoint(string $current, string $pattern): bool
    {
        // Exact match
        if ($current === $pattern) {
            return true;
        }

        // Wildcard match (e.g., "GET:*" or "POST:/api/*")
        if (str_contains($pattern, '*')) {
            $pattern = str_replace('*', '.*', $pattern);
            return preg_match('/^' . $pattern . '$/', $current);
        }

        return false;
    }

    /**
     * Check if request should be excluded from logging
     */
    private function isExcluded(Request $request): bool
    {
        $path = $request->path();
        $method = $request->method();
        $domain = $request->getHost();

        // Check excluded paths
        foreach ($this->config->getExcludedPaths() as $excludedPath) {
            if (str_starts_with($path, $excludedPath)) {
                return true;
            }
        }

        // Check excluded methods
        if (in_array($method, $this->config->getExcludedMethods())) {
            return true;
        }

        // Check excluded domains
        foreach ($this->config->getExcludedDomains() as $excludedDomain) {
            if ($domain === $excludedDomain || str_ends_with($domain, '.' . $excludedDomain)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log HTTP request
     */
    private function logRequest(Request $request): void
    {
        try {
            $logData = [
                'type' => 'http_request',
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'path' => $request->path(),
                'query_string' => $request->getQueryString(),
                'headers' => $this->filterSensitiveHeaders($request->headers->all()),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString(),
                'request_id' => uniqid(),
            ];

            // Add request body if enabled and not too large
            if ($this->config->shouldLogRequestBody() && $request->getContent()) {
                $content = $request->getContent();
                if (strlen($content) <= $this->config->getMaxBodySize()) {
                    $logData['body'] = $this->filterSensitiveBodyFields($content);
                }
            }

            Logger::info('HTTP Request', $logData);
        } catch (\Exception $e) {
            // Silently fail to avoid breaking the request
            error_log("HTTP Logging Error: " . $e->getMessage());
        }
    }

    /**
     * Log HTTP response
     */
    private function logResponse(Request $request, $response, float $executionTime): void
    {
        try {
            $logData = [
                'type' => 'http_response',
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'path' => $request->path(),
                'status_code' => $response->getStatusCode(),
                'status_text' => $response->getStatusCode() . ' ' . $this->getStatusText($response->getStatusCode()),
                'headers' => $this->filterSensitiveHeaders($response->headers->all()),
                'execution_time_ms' => round($executionTime, 2),
                'timestamp' => now()->toISOString(),
                'request_id' => uniqid(),
            ];

            // Check if response is slow
            if ($executionTime > $this->config->getSlowRequestThreshold()) {
                $logData['slow_request'] = true;
                Logger::warning('Slow HTTP Request', $logData);
            } else {
                Logger::info('HTTP Response', $logData);
            }

            // Add response body if enabled and not too large
            if ($this->config->shouldLogResponseBody() && $response->getContent()) {
                $content = $response->getContent();
                if (strlen($content) <= $this->config->getMaxBodySize()) {
                    $logData['body'] = $this->filterSensitiveResponseFields($content);
                }
            }

        } catch (\Exception $e) {
            // Silently fail to avoid breaking the request
            error_log("HTTP Logging Error: " . $e->getMessage());
        }
    }

    /**
     * Filter sensitive headers
     */
    private function filterSensitiveHeaders(array $headers): array
    {
        $sensitiveHeaders = $this->config->getSensitiveHeaders();
        
        foreach ($headers as $key => $value) {
            if (in_array(strtolower($key), array_map('strtolower', $sensitiveHeaders))) {
                $headers[$key] = '[REDACTED]';
            }
        }

        return $headers;
    }

    /**
     * Filter sensitive body fields
     */
    private function filterSensitiveBodyFields(string $content): string
    {
        $sensitiveFields = $this->config->getSensitiveBodyFields();
        
        // Try to decode JSON
        $data = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            $data = $this->recursivelyFilterSensitiveFields($data, $sensitiveFields);
            return json_encode($data);
        }

        // Try to decode form data
        parse_str($content, $data);
        if (!empty($data)) {
            $data = $this->recursivelyFilterSensitiveFields($data, $sensitiveFields);
            return http_build_query($data);
        }

        return $content;
    }

    /**
     * Filter sensitive response fields
     */
    private function filterSensitiveResponseFields(string $content): string
    {
        $sensitiveFields = $this->config->getSensitiveResponseFields();
        
        // Try to decode JSON
        $data = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            $data = $this->recursivelyFilterSensitiveFields($data, $sensitiveFields);
            return json_encode($data);
        }

        return $content;
    }

    /**
     * Recursively filter sensitive fields from array
     */
    private function recursivelyFilterSensitiveFields(array $data, array $sensitiveFields): array
    {
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), array_map('strtolower', $sensitiveFields))) {
                $data[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $data[$key] = $this->recursivelyFilterSensitiveFields($value, $sensitiveFields);
            }
        }

        return $data;
    }

    /**
     * Get HTTP status text
     */
    private function getStatusText(int $statusCode): string
    {
        return match($statusCode) {
            200 => 'OK', 201 => 'Created', 202 => 'Accepted', 204 => 'No Content',
            301 => 'Moved Permanently', 302 => 'Found', 304 => 'Not Modified',
            400 => 'Bad Request', 401 => 'Unauthorized', 403 => 'Forbidden',
            404 => 'Not Found', 405 => 'Method Not Allowed', 409 => 'Conflict',
            422 => 'Unprocessable Entity', 429 => 'Too Many Requests',
            500 => 'Internal Server Error', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout',
            default => 'Unknown'
        };
    }
} 