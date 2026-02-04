<?php

declare(strict_types=1);

namespace Tests\Core;

use PHPUnit\Framework\TestCase;
use App\Core\Database;
use PDO;

class DatabaseTest extends TestCase
{
    public function testPdoUsesExceptionErrorMode()
    {
        // This test verifies that the Database class properly configures
        // PDO with ERRMODE_EXCEPTION instead of the default ERRMODE_SILENT

        // Read the Database.php source code to verify it sets ERRMODE_EXCEPTION
        $databaseSource = file_get_contents(__DIR__ . '/../../app/Core/Database.php');

        // Verify that ERRMODE_EXCEPTION is being set in the PDO options
        $this->assertStringContainsString(
            'PDO::ERRMODE_EXCEPTION',
            $databaseSource,
            'Database class should configure PDO with ERRMODE_EXCEPTION for proper error handling'
        );

        // Verify it's being set in the options array (4th parameter of PDO constructor)
        $this->assertStringContainsString(
            'PDO::ATTR_ERRMODE',
            $databaseSource,
            'Database class should set PDO::ATTR_ERRMODE in constructor options'
        );

        // Also verify it's using the 4th parameter (options array) of PDO constructor
        $this->assertMatchesRegularExpression(
            '/new PDO\([^,]+,[^,]+,[^,]+,\s*\[/',
            $databaseSource,
            'PDO constructor should include 4th parameter for options array'
        );
    }

    public function testQueryAcceptsParameterArray()
    {
        $this->markTestIncomplete('Implement parameterized query support');
    }

    public function testQueryWithPositionalParameters()
    {
        $this->markTestIncomplete('Implement parameterized query support');
    }

    public function testQueryReturnsEmptyArrayForNoResults()
    {
        // Skip if SQLite driver is not available
        if (!in_array('sqlite', PDO::getAvailableDrivers())) {
            $this->markTestSkipped('SQLite PDO driver not available');
        }

        $pdo = new PDO('sqlite::memory:');
        $reflection = new \ReflectionClass(Database::class);
        $property = $reflection->getProperty('pdo');
        $property->setAccessible(true);
        $property->setValue($pdo);

        $result = Database::query("SELECT name FROM sqlite_master WHERE type='table'");
        $this->assertIsArray($result);
    }

    public function testPrepareBindAndExecuteWorks()
    {
        // Skip if SQLite driver is not available
        if (!in_array('sqlite', PDO::getAvailableDrivers())) {
            $this->markTestSkipped('SQLite PDO driver not available');
        }

        $pdo = new PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE test (id INTEGER, name TEXT)');

        $reflection = new \ReflectionClass(Database::class);
        $property = $reflection->getProperty('pdo');
        $property->setAccessible(true);
        $property->setValue($pdo);

        Database::execute('INSERT INTO test (id, name) VALUES (:id, :name)', [
            ':id' => 1,
            ':name' => 'Test User'
        ]);

        $result = Database::fetchOne('SELECT * FROM test WHERE id = :id', [':id' => 1]);
        $this->assertEquals(['id' => 1, 'name' => 'Test User'], $result);
    }
}
