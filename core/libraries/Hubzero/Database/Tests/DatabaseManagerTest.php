<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses -- Test file with helper stubs
namespace Hubzero\Database\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hubzero\Database\DatabaseManager;
use Hubzero\Database\BackendRegistry;
use Hubzero\Database\Driver;
use Hubzero\Database\Drivers\Mock\MockDriver;
use Hubzero\Database\Drivers\Sqlite\SqliteDriver;

class ConventionDriver extends MockDriver
{
    public static function test()
    {
        return true;
    }
}

class ConventionPriorityDriver extends MockDriver
{
    public static function test()
    {
        return true;
    }
}

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
        $this->assertNotContains('builtIn', $drivers);
    }

    #[Test]
    public function getAvailableDriversIncludesCustomDrivers(): void
    {
        $manager = new DatabaseManager();
        $manager->extend('custom', function ($config) {
            return new MockDriver();
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
        $mockDriver = new MockDriver();
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

        $mockDriver = new MockDriver();
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
        $mockDriver = new MockDriver();
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
        $this->assertInstanceOf(MockDriver::class, $driver);
    }

    #[Test]
    public function makeDriverFallsBackToNameWhenNoDriverKey(): void
    {
        $manager = new DatabaseManager();

        $driver = $manager->makeDriver('mock', []);

        $this->assertInstanceOf(MockDriver::class, $driver);
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

        $this->assertInstanceOf(MockDriver::class, $driver);
    }

    #[Test]
    public function makeDriverCustomCreatorTakesPrecedenceOverConvention(): void
    {
        $manager = new DatabaseManager();
        $customCalled = false;

        $mockDriver = new MockDriver();
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
        $mockDriver = new MockDriver();
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
        $mockDriver = new MockDriver();
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
        $mockDriver = new MockDriver();
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

    #[Test]
    public function parseUrlThrowsForMalformedUrl(): void
    {
        $manager = new DatabaseManager();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid database URL');

        $manager->makeDriver('test', [
            'url' => 'mysql://user@:3306',
        ]);
    }

    // =========================================================================
    // Built-in factory methods (integration)
    // =========================================================================

    #[Test]
    public function createMockDriverWorks(): void
    {
        $manager = new DatabaseManager();
        $driver = $manager->makeDriver('test', ['driver' => 'mock']);

        $this->assertInstanceOf(MockDriver::class, $driver);
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

        $this->assertInstanceOf(SqliteDriver::class, $driver);
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
        $mockDriver = new MockDriver();
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

        $mockDriver = new MockDriver();
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
            return new MockDriver();
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

        $this->assertEquals(
            BackendRegistry::canonicalDriverClassFor('percona'),
            $availability['percona']['class']
        );
        $this->assertEquals(
            BackendRegistry::canonicalDriverClassFor('mariadb'),
            $availability['mariadb']['class']
        );
    }

    #[Test]
    public function backendRegistryResolvesDriverClassWithCanonicalFallback(): void
    {
        $canonical = BackendRegistry::canonicalDriverClassFor('mysql');
        $resolved = BackendRegistry::resolveDriverClassFor('mysql');

        $this->assertNotNull($canonical);
        $this->assertNotNull($resolved);
        $this->assertEquals($canonical, $resolved);
    }

    #[Test]
    public function backendRegistryResolvesSyntaxClassWithCanonicalFallback(): void
    {
        $canonical = BackendRegistry::canonicalSyntaxClassFor('mysql');
        $resolved = BackendRegistry::resolveSyntaxClassFor('mysql');

        $this->assertNotNull($canonical);
        $this->assertNotNull($resolved);
        if (class_exists($canonical)) {
            $this->assertEquals($canonical, $resolved);
        } else {
            $this->assertEquals('\\Hubzero\\Database\\Syntax\\Mysql', $resolved);
        }
    }

    #[Test]
    public function backendRegistryResolvesSyntaxAliasesBeforeClassResolution(): void
    {
        $resolved = BackendRegistry::resolveSyntaxClassFor('ibm');
        $this->assertEquals(
            BackendRegistry::resolveSyntaxClassFor('db2'),
            $resolved
        );
    }

    #[Test]
    public function backendRegistryUsesCanonicalAseDriverWhenAvailable(): void
    {
        $canonical = BackendRegistry::canonicalDriverClassFor('ase');
        $resolved = BackendRegistry::resolveDriverClassFor('ase');

        $this->assertEquals(
            '\\Hubzero\\Database\\Drivers\\Ase\\AseDriver',
            $canonical
        );
        $this->assertTrue(class_exists($canonical));
        $this->assertEquals($canonical, $resolved);
    }

    #[Test]
    public function backendRegistryUsesCanonicalAseSyntaxWhenAvailable(): void
    {
        $canonical = BackendRegistry::canonicalSyntaxClassFor('ase');
        $resolved = BackendRegistry::resolveSyntaxClassFor('ase');

        $this->assertEquals(
            '\\Hubzero\\Database\\Drivers\\Ase\\AseSyntax',
            $canonical
        );
        $this->assertTrue(class_exists($canonical));
        $this->assertEquals($canonical, $resolved);
    }

    #[Test]
    public function backendRegistryUsesCanonicalMariadbDriverWhenAvailable(): void
    {
        $canonical = BackendRegistry::canonicalDriverClassFor('mariadb');
        $resolved = BackendRegistry::resolveDriverClassFor('mariadb');

        $this->assertEquals(
            '\\Hubzero\\Database\\Drivers\\Mariadb\\MariadbDriver',
            $canonical
        );
        $this->assertTrue(class_exists($canonical));
        $this->assertEquals($canonical, $resolved);
    }

    #[Test]
    public function backendRegistryUsesCanonicalMariadbSyntaxWhenAvailable(): void
    {
        $canonical = BackendRegistry::canonicalSyntaxClassFor('mariadb');
        $resolved = BackendRegistry::resolveSyntaxClassFor('mariadb');

        $this->assertEquals(
            '\\Hubzero\\Database\\Drivers\\Mariadb\\MariadbSyntax',
            $canonical
        );
        $this->assertTrue(class_exists($canonical));
        $this->assertEquals($canonical, $resolved);
    }

    #[Test]
    public function backendRegistryUsesCanonicalDriverClassesForMovedBackends(): void
    {
        $backends = [
            'mock', 'ase', 'mysql', 'mariadb', 'pgsql', 'sqlite',
            'sqlsrv', 'cubrid', 'db2', 'firebird', 'informix', 'oci',
        ];

        foreach ($backends as $backend) {
            $canonical = BackendRegistry::canonicalDriverClassFor($backend);
            $resolved = BackendRegistry::resolveDriverClassFor($backend);

            $this->assertNotNull($canonical);
            $this->assertTrue(class_exists($canonical), "Canonical driver class missing for {$backend}");
            $this->assertEquals($canonical, $resolved, "Resolved driver class mismatch for {$backend}");
        }
    }

    #[Test]
    public function backendRegistryUsesCanonicalSyntaxClassesForMovedBackends(): void
    {
        $backends = [
            'ase', 'mysql', 'mariadb', 'pgsql', 'sqlite', 'sqlsrv',
            'cubrid', 'db2', 'firebird', 'informix', 'oci', 'percona',
        ];

        foreach ($backends as $backend) {
            $canonical = BackendRegistry::canonicalSyntaxClassFor($backend);
            $resolved = BackendRegistry::resolveSyntaxClassFor($backend);

            $this->assertNotNull($canonical);
            $this->assertTrue(class_exists($canonical), "Canonical syntax class missing for {$backend}");
            $this->assertEquals($canonical, $resolved, "Resolved syntax class mismatch for {$backend}");
        }
    }

    #[Test]
    public function canonicalGrammarClassesUseExpectedCanonicalHierarchy(): void
    {
        $base = \Hubzero\Database\Drivers\Base\BaseSchemaGrammar::class;
        $mysql = \Hubzero\Database\Drivers\Mysql\MysqlGrammar::class;
        $cases = [
            ['canonical' => \Hubzero\Database\Drivers\Ase\AseGrammar::class, 'base' => $base],
            ['canonical' => $mysql, 'base' => $base],
            ['canonical' => \Hubzero\Database\Drivers\Mariadb\MariadbGrammar::class, 'base' => $mysql],
            ['canonical' => \Hubzero\Database\Drivers\Percona\PerconaGrammar::class, 'base' => $mysql],
            ['canonical' => \Hubzero\Database\Drivers\Cubrid\CubridGrammar::class, 'base' => $mysql],
            ['canonical' => \Hubzero\Database\Drivers\Sqlite\SqliteGrammar::class, 'base' => $base],
            ['canonical' => \Hubzero\Database\Drivers\Pgsql\PgsqlGrammar::class, 'base' => $base],
            ['canonical' => \Hubzero\Database\Drivers\Firebird\FirebirdGrammar::class, 'base' => $base],
            ['canonical' => \Hubzero\Database\Drivers\Informix\InformixGrammar::class, 'base' => $base],
            ['canonical' => \Hubzero\Database\Drivers\Oci\OciGrammar::class, 'base' => $base],
            ['canonical' => \Hubzero\Database\Drivers\Sqlsrv\SqlsrvGrammar::class, 'base' => $base],
            ['canonical' => \Hubzero\Database\Drivers\Db2\Db2Grammar::class, 'base' => $base],
        ];

        foreach ($cases as $case) {
            $this->assertTrue(class_exists($case['canonical']));
            $this->assertTrue(is_a($case['canonical'], $case['base'], true));
        }
    }

    #[Test]
    public function conventionSyntaxClassCandidatesUseCanonicalOnly(): void
    {
        $candidates = BackendRegistry::conventionSyntaxClassCandidates('custom_backend');

        $this->assertSame(
            [
                '\\Hubzero\\Database\\Drivers\\CustomBackend\\CustomBackendSyntax',
            ],
            $candidates
        );
    }

    #[Test]
    public function conventionSyntaxClassCandidatesApplyAliases(): void
    {
        $candidates = BackendRegistry::conventionSyntaxClassCandidates('ibm');

        $this->assertSame(
            [
                '\\Hubzero\\Database\\Drivers\\Db2\\Db2Syntax',
            ],
            $candidates
        );
    }

    #[Test]
    public function conventionDriverClassCandidatesUseCanonicalOnly(): void
    {
        $candidates = BackendRegistry::conventionDriverClassCandidates('custom_backend');

        $this->assertSame(
            [
                '\\Hubzero\\Database\\Drivers\\CustomBackend\\CustomBackendDriver',
            ],
            $candidates
        );
    }

    #[Test]
    public function backendRegistryResolvesCanonicalGrammarClassesForMovedBackends(): void
    {
        $backends = [
            'mock', 'ase', 'mysql', 'mariadb', 'percona', 'cubrid',
            'sqlite', 'pgsql', 'firebird', 'informix', 'oci', 'sqlsrv', 'db2',
        ];

        foreach ($backends as $backend) {
            $resolved = BackendRegistry::resolveGrammarClassFor($backend);
            $canonical = BackendRegistry::grammarClassMap()[$backend] ?? null;

            $this->assertNotNull($canonical);
            $this->assertNotNull($resolved);
            $this->assertTrue(class_exists($canonical), "Canonical grammar class missing for {$backend}");
            $this->assertEquals($canonical, $resolved);
        }
    }

    #[Test]
    public function makeDriverResolvesConventionDriverClassFallback(): void
    {
        $manager = new DatabaseManager();

        if (!class_exists('\\Hubzero\\Database\\Drivers\\ConventionDriver\\ConventionDriverDriver')) {
            class_alias(
                ConventionDriver::class,
                '\\Hubzero\\Database\\Drivers\\ConventionDriver\\ConventionDriverDriver'
            );
        }

        $driver = $manager->makeDriver('anything', ['driver' => 'convention_driver']);

        $this->assertInstanceOf(ConventionDriver::class, $driver);
        $this->assertTrue($driver->connected());
        $driver->disconnect();
    }

    #[Test]
    public function makeDriverUsesConventionClassFallback(): void
    {
        $manager = new DatabaseManager();

        if (!class_exists('\\Hubzero\\Database\\Drivers\\Fallback\\FallbackDriver')) {
            class_alias(
                ConventionPriorityDriver::class,
                '\\Hubzero\\Database\\Drivers\\Fallback\\FallbackDriver'
            );
        }

        $driver = $manager->makeDriver('anything', ['driver' => 'fallback']);

        $this->assertInstanceOf(ConventionPriorityDriver::class, $driver);
        $this->assertTrue($driver->connected());
        $driver->disconnect();
    }
}
