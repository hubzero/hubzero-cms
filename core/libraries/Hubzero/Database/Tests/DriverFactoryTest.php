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
        Driver::flushInstances();
        Driver::setPoolSize(10);
        Driver::setPoolTimelimit(120);
        Driver::resetTelemetry();
    }

    private function isNativeConnectionOpen(Driver $driver): bool
    {
        $ref = new \ReflectionClass(Driver::class);
        $prop = $ref->getProperty('connection');
        $prop->setAccessible(true);
        $connection = $prop->getValue($driver);

        if (is_object($connection) && method_exists($connection, 'isConnected')) {
            return (bool) $connection->isConnected();
        }

        return false;
    }

    private function getSignatureForInstance(Driver $driver): ?string
    {
        $ref = new \ReflectionClass(Driver::class);
        $instances = $ref->getProperty('instances');
        $instances->setAccessible(true);
        $map = (array) $instances->getValue();

        foreach ($map as $signature => $instance) {
            if ($instance === $driver) {
                return (string) $signature;
            }
        }

        return null;
    }

    private function hasInstance(Driver $driver): bool
    {
        return $this->getSignatureForInstance($driver) !== null;
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

    #[Test]
    public function poolSettingsSurviveDriverFlush(): void
    {
        Driver::setPoolSize(3);
        Driver::setPoolTimelimit(77);

        Driver::flush();

        $ref = new \ReflectionClass(Driver::class);
        $poolSize = $ref->getProperty('poolSize');
        $poolSize->setAccessible(true);
        $poolTimeLimit = $ref->getProperty('poolTimeLimit');
        $poolTimeLimit->setAccessible(true);

        $this->assertSame(3, $poolSize->getValue());
        $this->assertSame(77, $poolTimeLimit->getValue());
    }

    #[Test]
    public function getInstanceEvictsLeastRecentlyUsedWhenPoolLimitIsReached(): void
    {
        Driver::setPoolSize(2);
        Driver::setPoolTimelimit(0);

        $a = Driver::getInstance(['driver' => 'mock', 'database' => 'pool_a']);
        usleep(1000);
        $b = Driver::getInstance(['driver' => 'mock', 'database' => 'pool_b']);
        usleep(1000);
        $aAgain = Driver::getInstance(['driver' => 'mock', 'database' => 'pool_a']);
        usleep(1000);
        Driver::getInstance(['driver' => 'mock', 'database' => 'pool_c']);

        $this->assertSame($a, $aAgain);
        $this->assertSame($a, Driver::getInstance(['driver' => 'mock', 'database' => 'pool_a']));

        $bAgain = Driver::getInstance(['driver' => 'mock', 'database' => 'pool_b']);
        $this->assertNotSame($b, $bAgain);
    }

    #[Test]
    public function getInstanceClosesIdleConnectionsPastPoolTimeLimit(): void
    {
        Driver::setPoolSize(10);
        Driver::setPoolTimelimit(1);

        $stale = Driver::getInstance(['driver' => 'mock', 'database' => 'pool_stale']);
        $stale->connect();
        $this->assertTrue($this->isNativeConnectionOpen($stale));

        $signature = $this->getSignatureForInstance($stale);
        $this->assertNotNull($signature);

        $ref = new \ReflectionClass(Driver::class);
        $lastUsed = $ref->getProperty('instanceLastUsed');
        $lastUsed->setAccessible(true);
        $map = (array) $lastUsed->getValue();
        $map[$signature] = microtime(true) - 10;
        $lastUsed->setValue(null, $map);

        Driver::getInstance(['driver' => 'mock', 'database' => 'pool_other']);

        $this->assertFalse($this->isNativeConnectionOpen($stale));
    }

    #[Test]
    public function nonPersistentInstanceIsHardEvictedByDriverFlush(): void
    {
        $driver = Driver::getInstance([
            'driver' => 'mock',
            'database' => 'non_persistent_driver',
            'persistent' => false,
        ]);

        $driver->connect();
        $this->assertTrue($this->isNativeConnectionOpen($driver));
        $this->assertTrue($this->hasInstance($driver));

        Driver::flush();

        $this->assertFalse($this->isNativeConnectionOpen($driver));
        $this->assertFalse($this->hasInstance($driver));

        $driverAgain = Driver::getInstance([
            'driver' => 'mock',
            'database' => 'non_persistent_driver',
            'persistent' => false,
        ]);
        $this->assertNotSame($driver, $driverAgain);
    }

    #[Test]
    public function persistentInstanceRemainsInPoolAfterDriverFlush(): void
    {
        $driver = Driver::getInstance([
            'driver' => 'mock',
            'database' => 'persistent_driver',
            'persistent' => true,
        ]);

        $driver->connect();
        $this->assertTrue($this->hasInstance($driver));

        Driver::flush();

        $this->assertTrue($this->hasInstance($driver));

        $driverAgain = Driver::getInstance([
            'driver' => 'mock',
            'database' => 'persistent_driver',
            'persistent' => true,
        ]);
        $this->assertSame($driver, $driverAgain);
    }

    #[Test]
    public function telemetryTracksLruAndNonPersistentEvictions(): void
    {
        Driver::setPoolSize(1);
        Driver::setPoolTimelimit(0);

        Driver::getInstance(['driver' => 'mock', 'database' => 'telemetry_a']);
        Driver::getInstance(['driver' => 'mock', 'database' => 'telemetry_b']);

        $nonPersistent = Driver::getInstance([
            'driver' => 'mock',
            'database' => 'telemetry_np',
            'persistent' => false,
        ]);
        $this->assertTrue($this->hasInstance($nonPersistent));

        Driver::flush();

        $telemetry = Driver::telemetry();
        $this->assertGreaterThanOrEqual(2, $telemetry['get_instance_calls']);
        $this->assertGreaterThanOrEqual(1, $telemetry['lru_evictions']);
        $this->assertGreaterThanOrEqual(1, $telemetry['non_persistent_evictions']);
        $this->assertGreaterThanOrEqual(1, $telemetry['flush_calls']);
        $this->assertGreaterThanOrEqual(1, $telemetry['flush_runtime_calls']);
        $this->assertGreaterThanOrEqual(1, $telemetry['reset_calls']);
    }

    #[Test]
    public function resetTelemetryClearsCounters(): void
    {
        Driver::getInstance(['driver' => 'mock', 'database' => 'telemetry_reset']);
        Driver::flush();

        $before = Driver::telemetry();
        $this->assertGreaterThan(0, $before['get_instance_calls']);

        Driver::resetTelemetry();

        $after = Driver::telemetry();
        $this->assertSame(0, $after['get_instance_calls']);
        $this->assertSame(0, $after['flush_calls']);
        $this->assertSame(0, $after['reset_calls']);
    }
}
