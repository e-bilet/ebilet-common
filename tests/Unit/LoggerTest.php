<?php

namespace Ebilet\Common\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Ebilet\Common\Services\Logger;
use Ebilet\Common\Services\CentralizedLogger;
use Ebilet\Common\Managers\QueueManager;

class LoggerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Test environment variables
        $_ENV['APP_NAME'] = 'test-service';
        $_ENV['LOG_PATH'] = 'tests/logs';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Clean up test logs
        $testLogPath = 'tests/logs/ebilet-centralized.log';
        if (file_exists($testLogPath)) {
            unlink($testLogPath);
        }
        
        // Clean up test directory
        $testLogDir = 'tests/logs';
        if (is_dir($testLogDir)) {
            rmdir($testLogDir);
        }
    }

    public function test_logger_can_log_info_message()
    {
        $this->expectNotToPerformAssertions();
        
        Logger::info('Test info message', ['test' => true]);
    }

    public function test_logger_can_log_error_message()
    {
        $this->expectNotToPerformAssertions();
        
        Logger::error('Test error message', ['error' => 'test error']);
    }

    public function test_logger_can_log_http_request()
    {
        $this->expectNotToPerformAssertions();
        
        Logger::logHttpRequest(
            'POST',
            'https://api.test.com/endpoint',
            ['Content-Type' => 'application/json'],
            ['data' => 'test']
        );
    }

    public function test_logger_can_log_http_response()
    {
        $this->expectNotToPerformAssertions();
        
        Logger::logHttpResponse(
            200,
            ['Content-Type' => 'application/json'],
            '{"success": true}',
            0.1
        );
    }

    public function test_logger_can_log_performance()
    {
        $this->expectNotToPerformAssertions();
        
        Logger::logPerformance('test_operation', 0.05, [
            'operation' => 'test',
            'metadata' => 'test data'
        ]);
    }

    public function test_logger_can_log_business_event()
    {
        $this->expectNotToPerformAssertions();
        
        Logger::logBusinessEvent('test_event', [
            'event_id' => 123,
            'data' => 'test data'
        ]);
    }

    public function test_centralized_logger_creates_log_file()
    {
        $logger = new CentralizedLogger();
        $logger->info('Test message for file logging');
        
        $logPath = 'tests/logs/ebilet-centralized.log';
        $this->assertFileExists($logPath);
        
        $logContent = file_get_contents($logPath);
        $this->assertStringContainsString('Test message for file logging', $logContent);
    }

    public function test_queue_manager_connection_handling()
    {
        $queueManager = QueueManager::getInstance();
        
        // Test connection status (may fail if RabbitMQ not available)
        $this->assertIsBool($queueManager->isConnected());
        
        // Test provider name
        $this->assertEquals('rabbitmq', $queueManager->getProviderName());
    }

    public function test_logger_with_all_levels()
    {
        $levels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
        
        foreach ($levels as $level) {
            $this->expectNotToPerformAssertions();
            Logger::$level("Test {$level} message", ['level' => $level]);
        }
    }
} 