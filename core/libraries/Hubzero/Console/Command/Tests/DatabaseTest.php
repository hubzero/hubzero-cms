<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Database console command tests
 */
class DatabaseTest extends TestCase
{
    /**
     * Test that the Database command class exists and has required methods
     *
     * @return void
     */
    public function testDatabaseCommandHasRequiredMethods()
    {
        $this->assertTrue(
            class_exists('\\Hubzero\\Console\\Command\\Database'),
            'Database command class should exist'
        );

        $reflection = new ReflectionClass('\\Hubzero\\Console\\Command\\Database');

        // Core methods
        $this->assertTrue($reflection->hasMethod('execute'), 'Should have execute method');
        $this->assertTrue($reflection->hasMethod('help'), 'Should have help method');

        // Existing commands
        $this->assertTrue($reflection->hasMethod('dump'), 'Should have dump method');
        $this->assertTrue($reflection->hasMethod('load'), 'Should have load method');
        $this->assertTrue($reflection->hasMethod('schema'), 'Should have schema method');

        // New commands
        $this->assertTrue($reflection->hasMethod('cli'), 'Should have cli method');
        $this->assertTrue($reflection->hasMethod('query'), 'Should have query method');
    }

    /**
     * Test that cli method has proper muse description annotation
     *
     * @return void
     */
    public function testCliMethodHasMuseDescription()
    {
        $reflection = new ReflectionClass('\\Hubzero\\Console\\Command\\Database');
        $method = $reflection->getMethod('cli');
        $docComment = $method->getDocComment();

        $this->assertStringContainsString(
            '@museDescription',
            $docComment,
            'cli method should have @museDescription annotation'
        );
        $this->assertStringContainsString(
            'database client',
            strtolower($docComment),
            'cli method description should mention database client'
        );
    }

    /**
     * Test that query method has proper muse description annotation
     *
     * @return void
     */
    public function testQueryMethodHasMuseDescription()
    {
        $reflection = new ReflectionClass('\\Hubzero\\Console\\Command\\Database');
        $method = $reflection->getMethod('query');
        $docComment = $method->getDocComment();

        $this->assertStringContainsString(
            '@museDescription',
            $docComment,
            'query method should have @museDescription annotation'
        );
        $this->assertStringContainsString(
            'sql query',
            strtolower($docComment),
            'query method description should mention SQL query'
        );
    }

    /**
     * Test that all command methods are public
     *
     * @return void
     */
    public function testCommandMethodsArePublic()
    {
        $reflection = new ReflectionClass('\\Hubzero\\Console\\Command\\Database');

        $commands = ['execute', 'dump', 'load', 'schema', 'cli', 'query', 'help'];

        foreach ($commands as $command) {
            $method = $reflection->getMethod($command);
            $this->assertTrue(
                $method->isPublic(),
                "{$command} method should be public"
            );
        }
    }

    /**
     * Test that cli method handles multiple database drivers
     *
     * @return void
     */
    public function testCliMethodSourceCodeHandlesMultipleDrivers()
    {
        $reflection = new ReflectionClass('\\Hubzero\\Console\\Command\\Database');
        $method = $reflection->getMethod('cli');

        // Get the file contents to check the switch statement
        $filename = $method->getFileName();
        $startLine = $method->getStartLine();
        $endLine = $method->getEndLine();

        $source = file($filename);
        $methodSource = implode('', array_slice($source, $startLine - 1, $endLine - $startLine + 1));

        // Check for driver support
        $this->assertStringContainsString('mysql', $methodSource, 'Should support mysql driver');
        $this->assertStringContainsString('pgsql', $methodSource, 'Should support pgsql driver');
        $this->assertStringContainsString('sqlite', $methodSource, 'Should support sqlite driver');

        // Check for proper command building
        $this->assertStringContainsString('escapeshellarg', $methodSource, 'Should escape shell arguments');
        $this->assertStringContainsString('passthru', $methodSource, 'Should use passthru for interactive session');
    }

    /**
     * Test that query method supports table prefix replacement
     *
     * @return void
     */
    public function testQueryMethodSourceCodeSupportsTablePrefix()
    {
        $reflection = new ReflectionClass('\\Hubzero\\Console\\Command\\Database');
        $method = $reflection->getMethod('query');

        $filename = $method->getFileName();
        $startLine = $method->getStartLine();
        $endLine = $method->getEndLine();

        $source = file($filename);
        $methodSource = implode('', array_slice($source, $startLine - 1, $endLine - $startLine + 1));

        // Check for prefix replacement
        $this->assertStringContainsString('#__', $methodSource, 'Should handle #__ table prefix');
        $this->assertStringContainsString('str_replace', $methodSource, 'Should replace table prefix');

        // Check for JSON output option
        $this->assertStringContainsString('json', strtolower($methodSource), 'Should support JSON output');
        $this->assertStringContainsString('JSON_PRETTY_PRINT', $methodSource, 'Should format JSON output');
    }

    /**
     * Test that query method detects SELECT vs non-SELECT queries
     *
     * @return void
     */
    public function testQueryMethodDetectsQueryType()
    {
        $reflection = new ReflectionClass('\\Hubzero\\Console\\Command\\Database');
        $method = $reflection->getMethod('query');

        $filename = $method->getFileName();
        $startLine = $method->getStartLine();
        $endLine = $method->getEndLine();

        $source = file($filename);
        $methodSource = implode('', array_slice($source, $startLine - 1, $endLine - $startLine + 1));

        // Check that it detects different query types
        $this->assertStringContainsString('SELECT', $methodSource, 'Should detect SELECT queries');
        $this->assertStringContainsString('SHOW', $methodSource, 'Should detect SHOW queries');
        $this->assertStringContainsString('DESCRIBE', $methodSource, 'Should detect DESCRIBE queries');
        $this->assertStringContainsString('EXPLAIN', $methodSource, 'Should detect EXPLAIN queries');
    }
}
