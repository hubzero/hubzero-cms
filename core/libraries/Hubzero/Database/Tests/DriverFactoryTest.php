<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use Hubzero\Database\Driver;
use Hubzero\Database\Drivers\Mock\MockDriver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Driver static factory behavior.
 */
class DriverFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        $property = new \ReflectionProperty(Driver::class, 'instances');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }

    #[Test]
    public function getInstanceResolvesCanonicalBuiltInDriver(): void
    {
        $driver = Driver::getInstance(['driver' => 'mock']);

        $this->assertInstanceOf(MockDriver::class, $driver);
        $this->assertSame(MockDriver::class, get_class($driver));
    }

    #[Test]
    public function getInstanceCachesByOptionsSignature(): void
    {
        $a = Driver::getInstance(['driver' => 'mock', 'database' => 'alpha']);
        $b = Driver::getInstance(['driver' => 'mock', 'database' => 'alpha']);

        $this->assertSame($a, $b);
    }

    #[Test]
    public function getInstanceNormalizesPdoAliasToMysql(): void
    {
        $viaAlias = Driver::getInstance([
            'driver' => 'pdo',
            'host' => 'localhost',
            'user' => 'root',
            'password' => '',
            'database' => 'hubzero',
            'prefix' => '',
        ]);

        $viaMysql = Driver::getInstance([
            'driver' => 'mysql',
            'host' => 'localhost',
            'user' => 'root',
            'password' => '',
            'database' => 'hubzero',
            'prefix' => '',
        ]);

        $this->assertSame($viaMysql, $viaAlias);
    }

    #[Test]
    public function getInstanceResolvesCanonicalConventionCandidate(): void
    {
        $class = '\\Hubzero\\Database\\Drivers\\FactoryTestCustom\\FactoryTestCustomDriver';
        if (!class_exists($class)) {
            eval(
                'namespace Hubzero\\Database\\Drivers\\FactoryTestCustom;'
                . 'class FactoryTestCustomDriver extends \\Hubzero\\Database\\Drivers\\Mock\\MockDriver {}'
            );
        }

        $driver = Driver::getInstance(['driver' => 'factory_test_custom']);

        $this->assertSame($class, '\\' . get_class($driver));
    }

    #[Test]
    public function getConnectorsIncludesMockConnector(): void
    {
        $connectors = Driver::getConnectors();

        $this->assertContains('Mock', $connectors);
    }
}
