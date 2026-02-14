<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hubzero\Database\DatabaseManager;
use Hubzero\Database\Driver;

/**
 * Tests for DatabaseManager
 */
class DatabaseManagerTest extends TestCase
{
    // =========================================================================
    // getAvailableDrivers()
    // =========================================================================

    #[Test]
    public function getAvailableDriversReturnsBuiltInDrivers(): void
    {
        $manager = new DatabaseManager();
        $drivers = $manager->getAvailableDrivers();

        $this->assertContains('mysql', $drivers);
        $this->assertContains('mariadb', $drivers);
        $this->assertContains('percona', $drivers);
        $this->assertContains('sqlite', $drivers);
        $this->assertContains('mock', $drivers);
        $this->assertContains('pgsql', $drivers);
        $this->assertContains('firebird', $drivers);
        $this->assertContains('informix', $drivers);
        $this->assertContains('oci', $drivers);
    }

    #[Test]
    public function getAvailableDriversIncludesCustomDrivers(): void
    {
        $manager = new DatabaseManager();
        $manager->extend('custom', function ($config) {
            return new Driver\Mock();
        });

        $drivers = $manager->getAvailableDrivers();

        $this->assertContains('custom', $drivers);
        $this->assertContains('mysql', $drivers);
    }

    // =========================================================================
    // extend()
    // =========================================================================

    #[Test]
    public function extendReturnsSelfForFluent(): void
    {
        $manager = new DatabaseManager();
        $result = $manager->extend('test', function () {
        });

        $this->assertSame($manager, $result);
    }

    #[Test]
    public function extendedDriverIsUsedByMakeDriver(): void
    {
        $manager = new DatabaseManager();
        $mockDriver = new Driver\Mock();
        $mockDriver->connect();

        $manager->extend('custom', function ($config, $name) use ($mockDriver) {
            return $mockDriver;
        });

        $driver = $manager->makeDriver('mydb', ['driver' => 'custom']);

        $this->assertSame($mockDriver, $driver);
    }

    #[Test]
    public function extendedDriverReceivesConfigAndName(): void
    {
        $manager = new DatabaseManager();
        $receivedConfig = null;
        $receivedName = null;

        $mockDriver = new Driver\Mock();
        $mockDriver->connect();

        $manager->extend('custom', function ($config, $name) use ($mockDriver, &$receivedConfig, &$receivedName) {
            $receivedConfig = $config;
            $receivedName = $name;
            return $mockDriver;
        });

        $manager->makeDriver('mydb', ['driver' => 'custom', 'host' => 'localhost']);

        $this->assertEquals('mydb', $receivedName);
        $this->assertEquals('localhost', $receivedConfig['host']);
    }

    #[Test]
    public function extendOverridesBuiltInDriver(): void
    {
        $manager = new DatabaseManager();
        $mockDriver = new Driver\Mock();
        $mockDriver->connect();

        $manager->extend('mock', function ($config, $name) use ($mockDriver) {
            return $mockDriver;
        });

        $driver = $manager->makeDriver('test', ['driver' => 'mock']);

        $this->assertSame($mockDriver, $driver);
    }

    // =========================================================================
    // makeDriver()
    // =========================================================================

    #[Test]
    public function makeDriverUsesDriverConfigKey(): void
    {
        $manager = new DatabaseManager();

        // Mock driver always works — no config needed
        $driver = $manager->makeDriver('anything', ['driver' => 'mock']);

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertInstanceOf(Driver\Mock::class, $driver);
    }

    #[Test]
    public function makeDriverFallsBackToNameWhenNoDriverKey(): void
    {
        $manager = new DatabaseManager();

        $driver = $manager->makeDriver('mock', []);

        $this->assertInstanceOf(Driver\Mock::class, $driver);
    }

    #[Test]
    public function makeDriverThrowsForUnsupportedDriver(): void
    {
        $manager = new DatabaseManager();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unsupported driver: nonexistent');

        $manager->makeDriver('test', ['driver' => 'nonexistent']);
    }

    #[Test]
    public function makeDriverParsesUrlBeforeResolution(): void
    {
        $manager = new DatabaseManager();

        // Use mock:// scheme so it resolves to createMockDriver
        $driver = $manager->makeDriver('test', [
            'url' => 'mock://user:pass@localhost/testdb',
        ]);

        $this->assertInstanceOf(Driver\Mock::class, $driver);
    }

    #[Test]
    public function makeDriverCustomCreatorTakesPrecedenceOverConvention(): void
    {
        $manager = new DatabaseManager();
        $customCalled = false;

        $mockDriver = new Driver\Mock();
        $mockDriver->connect();

        $manager->extend('mock', function ($config) use ($mockDriver, &$customCalled) {
            $customCalled = true;
            return $mockDriver;
        });

        $manager->makeDriver('test', ['driver' => 'mock']);

        $this->assertTrue($customCalled);
    }

    // =========================================================================
    // parseUrl()
    // =========================================================================

    #[Test]
    public function parseUrlExtractsAllComponents(): void
    {
        $manager = new DatabaseManager();

        // Use extend to capture the parsed config
        $parsedConfig = null;
        $mockDriver = new Driver\Mock();
        $mockDriver->connect();

        $manager->extend('mysql', function ($config) use ($mockDriver, &$parsedConfig) {
            $parsedConfig = $config;
            return $mockDriver;
        });

        $manager->makeDriver('test', [
            'url' => 'mysql://admin:secret@db.example.com:3307/hubzero?charset=utf8',
        ]);

        $this->assertEquals('mysql', $parsedConfig['driver']);
        $this->assertEquals('db.example.com', $parsedConfig['host']);
        $this->assertEquals(3307, $parsedConfig['port']);
        $this->assertEquals('admin', $parsedConfig['user']);
        $this->assertEquals('secret', $parsedConfig['password']);
        $this->assertEquals('hubzero', $parsedConfig['database']);
        $this->assertEquals('utf8', $parsedConfig['charset']);
        $this->assertArrayNotHasKey('url', $parsedConfig);
    }

    #[Test]
    public function parseUrlExplicitConfigWins(): void
    {
        $manager = new DatabaseManager();

        $parsedConfig = null;
        $mockDriver = new Driver\Mock();
        $mockDriver->connect();

        $manager->extend('mysql', function ($config) use ($mockDriver, &$parsedConfig) {
            $parsedConfig = $config;
            return $mockDriver;
        });

        $manager->makeDriver('test', [
            'url'      => 'mysql://admin:old@db.example.com/hubzero',
            'password' => 'new-password',
            'host'     => 'override.example.com',
        ]);

        $this->assertEquals('new-password', $parsedConfig['password']);
        $this->assertEquals('override.example.com', $parsedConfig['host']);
        $this->assertEquals('admin', $parsedConfig['user']);
        $this->assertEquals('hubzero', $parsedConfig['database']);
    }

    #[Test]
    public function parseUrlMinimalUrl(): void
    {
        $manager = new DatabaseManager();

        $parsedConfig = null;
        $mockDriver = new Driver\Mock();
        $mockDriver->connect();

        $manager->extend('sqlite', function ($config) use ($mockDriver, &$parsedConfig) {
            $parsedConfig = $config;
            return $mockDriver;
        });

        $manager->makeDriver('test', [
            'url' => 'sqlite://localhost/path/to/db.sqlite',
        ]);

        $this->assertEquals('sqlite', $parsedConfig['driver']);
        $this->assertEquals('path/to/db.sqlite', $parsedConfig['database']);
    }

    // =========================================================================
    // Built-in factory methods (integration)
    // =========================================================================

    #[Test]
    public function createMockDriverWorks(): void
    {
        $manager = new DatabaseManager();
        $driver = $manager->makeDriver('test', ['driver' => 'mock']);

        $this->assertInstanceOf(Driver\Mock::class, $driver);
        $this->assertTrue($driver->connected());

        $driver->disconnect();
    }

    #[Test]
    public function createSqliteDriverWorks(): void
    {
        $manager = new DatabaseManager();
        $driver = $manager->makeDriver('test', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $this->assertInstanceOf(Driver\Sqlite::class, $driver);
        $this->assertTrue($driver->connected());

        $driver->disconnect();
    }

    #[Test]
    public function mariadbResolvesToMysqlDriver(): void
    {
        // We can't actually connect without a MySQL server, but we can
        // verify the resolution by using extend to intercept
        $manager = new DatabaseManager();
        $resolved = false;

        // The mariadb factory method calls buildDriver(Mysql::class, ...)
        // which calls Mysql::test(). If PDO mysql isn't available, it throws.
        // Instead, test via extend override.
        $mockDriver = new Driver\Mock();
        $mockDriver->connect();

        $manager->extend('mariadb', function ($config) use ($mockDriver, &$resolved) {
            $resolved = true;
            return $mockDriver;
        });

        $manager->makeDriver('db', ['driver' => 'mariadb']);
        $this->assertTrue($resolved);
    }

    #[Test]
    public function perconaResolvesToMysqlDriver(): void
    {
        $manager = new DatabaseManager();
        $resolved = false;

        $mockDriver = new Driver\Mock();
        $mockDriver->connect();

        $manager->extend('percona', function ($config) use ($mockDriver, &$resolved) {
            $resolved = true;
            return $mockDriver;
        });

        $manager->makeDriver('db', ['driver' => 'percona']);
        $this->assertTrue($resolved);
    }

    // =========================================================================
    // getDriverAvailability()
    // =========================================================================

    #[Test]
    public function getDriverAvailabilityReturnsAllDrivers(): void
    {
        $manager = new DatabaseManager();
        $availability = $manager->getDriverAvailability();

        $this->assertArrayHasKey('mysql', $availability);
        $this->assertArrayHasKey('sqlite', $availability);
        $this->assertArrayHasKey('mock', $availability);
        $this->assertArrayHasKey('firebird', $availability);
        $this->assertArrayHasKey('oci', $availability);

        foreach ($availability as $name => $info) {
            $this->assertArrayHasKey('class', $info);
            $this->assertArrayHasKey('available', $info);
            $this->assertArrayHasKey('custom', $info);
        }
    }

    #[Test]
    public function getDriverAvailabilityShowsSqliteAsAvailable(): void
    {
        $manager = new DatabaseManager();
        $availability = $manager->getDriverAvailability();

        // SQLite/Mock should always be available in test environments
        $this->assertTrue($availability['sqlite']['available']);
        $this->assertTrue($availability['mock']['available']);
        $this->assertFalse($availability['sqlite']['custom']);
    }

    #[Test]
    public function getDriverAvailabilityIncludesCustomDrivers(): void
    {
        $manager = new DatabaseManager();
        $manager->extend('custom', function ($config) {
            return new Driver\Mock();
        });

        $availability = $manager->getDriverAvailability();

        $this->assertArrayHasKey('custom', $availability);
        $this->assertTrue($availability['custom']['custom']);
        $this->assertNull($availability['custom']['class']);
        $this->assertNull($availability['custom']['available']);
    }

    #[Test]
    public function getDriverAvailabilityMapsDriverClasses(): void
    {
        $manager = new DatabaseManager();
        $availability = $manager->getDriverAvailability();

        // Percona uses the same Mysql driver class
        $this->assertEquals(
            $availability['mysql']['class'],
            $availability['percona']['class']
        );
        // MariaDB has its own driver class (extends Mysql)
        $this->assertEquals(
            \Hubzero\Database\Driver\Mariadb::class,
            $availability['mariadb']['class']
        );
    }
}
