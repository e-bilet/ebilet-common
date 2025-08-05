<?php

namespace Ebilet\Common\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ebilet\Common\Services\Logger;
use Ebilet\Common\Interfaces\HttpLoggingConfigInterface;
use Ebilet\Common\Services\HttpLoggingConfig;

/**
 * HTTP Request/Response Logging Middleware
 * 
 * Bu middleware HTTP isteklerini ve yanıtlarını detaylı şekilde loglar.
 * Configurable ve extensible yapı ile farklı loglama stratejileri destekler.
 */
class HttpLoggingMiddleware
{
    private HttpLoggingConfigInterface $config;
    private Logger $logger;
    private float $startTime;

    public function __construct(HttpLoggingConfigInterface $config = null)
    {
        $this->config = $config ?? new HttpLoggingConfig();
        $this->logger = new Logger();
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->shouldLogRequest($request)) {
            return $next($request);
        }

        $this->startTime = microtime(true);
        
        // Log request
        $this->logRequest($request);
        
        $response = $next($request);
        
        // Log response
        $this->logResponse($request, $response);
        
        return $response;
    }

    /**
     * Determine if request should be logged.
     */
    private function shouldLogRequest(Request $request): bool
    {
        if (!$this->config->isEnabled()) {
            return false;
        }

        // Check excluded paths
        if ($this->isPathExcluded($request->path())) {
            return false;
        }

        // Check excluded methods
        if ($this->isMethodExcluded($request->method())) {
            return false;
        }

        // Check excluded domains
        if ($this->isDomainExcluded($request->getHost())) {
            return false;
        }

        return true;
    }

    /**
     * Check if path is excluded from logging.
     */
    private function isPathExcluded(string $path): bool
    {
        $excludedPaths = $this->config->getExcludedPaths();
        
        foreach ($excludedPaths as $excludedPath) {
            if (str_starts_with($path, $excludedPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if method is excluded from logging.
     */
    private function isMethodExcluded(string $method): bool
    {
        return in_array(strtoupper($method), $this->config->getExcludedMethods());
    }

    /**
     * Check if domain is excluded from logging.
     */
    private function isDomainExcluded(string $domain): bool
    {
        return in_array($domain, $this->config->getExcludedDomains());
    }

    /**
     * Log HTTP request.
     */
    private function logRequest(Request $request): void
    {
        $logData = [
            'type' => 'http_request',
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'query_string' => $request->getQueryString(),
            'headers' => $this->sanitizeHeaders($request->headers->all()),
            'body' => $this->shouldLogRequestBody() ? $this->sanitizeBody($request->all()) : null,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $request->user()?->id,
            'session_id' => $request->session()?->getId(),
            'timestamp' => now()->toISOString(),
            'request_id' => $this->generateRequestId(),
        ];

        $this->logger->info('HTTP Request', $logData);
    }

    /**
     * Log HTTP response.
     */
    private function logResponse(Request $request, $response): void
    {
        $duration = microtime(true) - $this->startTime;
        
        $logData = [
            'type' => 'http_response',
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'status_text' => $response->getStatusCode() . ' ' . $this->getStatusText($response->getStatusCode()),
            'headers' => $this->sanitizeHeaders($response->headers->all()),
            'body' => $this->shouldLogResponseBody() ? $this->sanitizeResponseBody($response->getContent()) : null,
            'duration_ms' => round($duration * 1000, 2),
            'duration_seconds' => round($duration, 4),
            'size_bytes' => strlen($response->getContent()),
            'timestamp' => now()->toISOString(),
            'request_id' => $this->generateRequestId(),
        ];

        // Log with appropriate level based on status code
        $this->logResponseWithLevel($logData);
    }

    /**
     * Log response with appropriate level.
     */
    private function logResponseWithLevel(array $logData): void
    {
        $statusCode = $logData['status_code'];
        
        if ($statusCode >= 500) {
            $this->logger->error('HTTP Response Error', $logData);
        } elseif ($statusCode >= 400) {
            $this->logger->warning('HTTP Response Client Error', $logData);
        } elseif ($statusCode >= 300) {
            $this->logger->info('HTTP Response Redirect', $logData);
        } else {
            $this->logger->info('HTTP Response Success', $logData);
        }
    }

    /**
     * Sanitize headers to remove sensitive information.
     */
    private function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = $this->config->getSensitiveHeaders();
        
        foreach ($sensitiveHeaders as $header) {
            if (isset($headers[$header])) {
                $headers[$header] = '[REDACTED]';
            }
        }
        
        return $headers;
    }

    /**
     * Sanitize request body to remove sensitive information.
     */
    private function sanitizeBody(array $body): array
    {
        $sensitiveFields = $this->config->getSensitiveBodyFields();
        
        foreach ($sensitiveFields as $field) {
            if (isset($body[$field])) {
                $body[$field] = '[REDACTED]';
            }
        }
        
        return $body;
    }

    /**
     * Sanitize response body to remove sensitive information.
     */
    private function sanitizeResponseBody(string $body): string
    {
        if (!$this->isJson($body)) {
            return $body;
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            return $body;
        }

        $sensitiveFields = $this->config->getSensitiveResponseFields();
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }
        
        return json_encode($data);
    }

    /**
     * Check if string is valid JSON.
     */
    private function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Generate unique request ID.
     */
    private function generateRequestId(): string
    {
        return uniqid('req_', true);
    }

    /**
     * Check if request body should be logged.
     */
    private function shouldLogRequestBody(): bool
    {
        return $this->config->shouldLogRequestBody();
    }

    /**
     * Get status text for HTTP status code.
     */
    private function getStatusText(int $statusCode): string
    {
        return match($statusCode) {
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            204 => 'No Content',
            301 => 'Moved Permanently',
            302 => 'Found',
            304 => 'Not Modified',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            409 => 'Conflict',
            422 => 'Unprocessable Entity',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            default => 'Unknown'
        };
    }

    /**
     * Check if response body should be logged.
     */
    private function shouldLogResponseBody(): bool
    {
        return $this->config->shouldLogResponseBody();
    }
} 