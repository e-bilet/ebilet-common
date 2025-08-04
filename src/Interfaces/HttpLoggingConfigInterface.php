<?php

namespace Ebilet\Common\Interfaces;

/**
 * HTTP Logging Configuration Interface
 * 
 * Bu interface HTTP loglama konfigürasyonu için contract sağlar.
 * Farklı konfigürasyon stratejileri destekler.
 */
interface HttpLoggingConfigInterface
{
    /**
     * Check if HTTP logging is enabled.
     */
    public function isEnabled(): bool;

    /**
     * Get excluded paths from logging.
     */
    public function getExcludedPaths(): array;

    /**
     * Get excluded HTTP methods from logging.
     */
    public function getExcludedMethods(): array;

    /**
     * Get excluded domains from logging.
     */
    public function getExcludedDomains(): array;

    /**
     * Get sensitive headers to redact.
     */
    public function getSensitiveHeaders(): array;

    /**
     * Get sensitive body fields to redact.
     */
    public function getSensitiveBodyFields(): array;

    /**
     * Get sensitive response fields to redact.
     */
    public function getSensitiveResponseFields(): array;

    /**
     * Check if request body should be logged.
     */
    public function shouldLogRequestBody(): bool;

    /**
     * Check if response body should be logged.
     */
    public function shouldLogResponseBody(): bool;

    /**
     * Get maximum body size to log.
     */
    public function getMaxBodySize(): int;

    /**
     * Get slow request threshold in milliseconds.
     */
    public function getSlowRequestThreshold(): int;
} 