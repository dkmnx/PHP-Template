<?php

namespace Tests\Helpers;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function testEscapeFunctionConvertsSpecialCharacters()
    {
        $this->assertFunctionExists('e');
        $input = '<script>alert("XSS")</script>';
        $escaped = e($input);
        $this->assertEquals('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', $escaped);
        $this->assertNotEquals($input, $escaped);
    }

    public function testEscapeHandlesSingleQuotes()
    {
        $input = "O'Reilly";
        $escaped = e($input);
        $this->assertEquals('O&apos;Reilly', $escaped);
    }

    public function testEscapeHandlesDoubleQuotes()
    {
        $input = 'He said "Hello"';
        $escaped = e($input);
        $this->assertEquals('He said &quot;Hello&quot;', $escaped);
    }

    public function testEscapeHandlesNullInput()
    {
        $escaped = e(null);
        $this->assertEquals('', $escaped);
    }

    public function testEscapeHandlesEmptyString()
    {
        $escaped = e('');
        $this->assertEquals('', $escaped);
    }

    public function testEscapePreservesUtf8Characters()
    {
        $input = 'Hello 世界 🌍';
        $escaped = e($input);
        $this->assertEquals('Hello 世界 🌍', $escaped);
    }

    public function testActiveFunctionSanitizesInput()
    {
        $this->assertFunctionExists('active');

        // Test that active() handles malicious input safely
        $_SERVER['REQUEST_URI'] = '/safe-path';
        $maliciousPath = '/safe-path<script>alert("XSS")</script>';

        // Should not match because sanitized path won't equal the safe path
        $result = active($maliciousPath);
        $this->assertEquals('', $result);

        // Test normal path still works
        $result = active('/safe-path');
        $this->assertEquals('active', $result);

        // Test non-matching path
        $result = active('/about');
        $this->assertEquals('', $result);
    }

    private function assertFunctionExists($function)
    {
        static $loaded = false;
        if (!$loaded) {
            require_once __DIR__ . '/../../app/helpers.php';
            $loaded = true;
        }
        $this->assertTrue(function_exists($function), "Function $function should exist");
    }
}
