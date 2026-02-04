<?php

use PHPUnit\Framework\TestCase;

class DatabaseConfigTest extends TestCase
{
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
