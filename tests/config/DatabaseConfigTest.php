<?php

declare(strict_types=1);

namespace Tests\Config;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class DatabaseConfigTest extends TestCase
{
    private array $originalEnv;

    protected function setUp(): void
    {
        // Store original values
        $this->originalEnv = [
            'DB_HOST' => getenv('DB_HOST'),
            'DB_NAME' => getenv('DB_NAME'),
            'DB_USER' => getenv('DB_USER'),
            'DB_PASS' => getenv('DB_PASS'),
        ];
    }

    protected function tearDown(): void
    {
        // Restore original values
        foreach ($this->originalEnv as $key => $value) {
            if ($value === false) {
                putenv($key); // Remove
            } else {
                putenv("$key=$value");
            }
        }
    }

    public function testThrowsExceptionWhenDbNameNotSet()
    {
        putenv('DB_NAME');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('DB_NAME environment variable is required');
        require __DIR__ . '/../../config/database.php';
    }

    public function testThrowsExceptionWhenDbUserNotSet()
    {
        putenv('DB_USER');
        putenv('DB_NAME=test_db');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('DB_USER environment variable is required');
        require __DIR__ . '/../../config/database.php';
    }

    public function testReturnsDefaultHostWhenNotSet()
    {
        putenv('DB_HOST');
        putenv('DB_NAME=test_db');
        putenv('DB_USER=test_user');
        $config = require __DIR__ . '/../../config/database.php';
        $this->assertEquals('localhost', $config['host']);
    }

    public function testReturnsEmptyStringWhenPasswordNotSet()
    {
        putenv('DB_PASS');
        putenv('DB_NAME=test_db');
        putenv('DB_USER=test_user');
        putenv('DB_HOST=localhost');
        $config = require __DIR__ . '/../../config/database.php';
        $this->assertEquals('', $config['pass']);
    }
}
