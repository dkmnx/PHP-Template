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
}
