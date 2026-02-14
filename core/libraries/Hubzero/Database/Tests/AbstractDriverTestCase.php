<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Hubzero\Database\Driver;
use Hubzero\Database\DatabaseManager;

/**
 * Base test case for multi-database integration tests
 *
 * Provides standard infrastructure for running tests against all configured
 * database backends. Subclasses define table setup via setUpDatabase() and
 * table names via getTestTables().
 *
 * Database configuration is loaded from environment variables set in phpunit.xml
 * or phpunit.xml.dist. See phpunit.xml.dist for the full list of DB_*_* env vars.
 *
 * Usage:
 * ```php
 * class MyTest extends AbstractDriverTestCase
 * {
 *     protected static function getTestTables(): array
 *     {
 *         return ['my_test_table'];
 *     }
 *
 *     protected static function setUpDatabase(Driver $driver): void
 *     {
 *         $driver->createTable('my_test_table')
 *             ->id()
 *             ->string('name', 255)
 *             ->execute();
 *     }
 *
 *     #[Test]
 *     #[DataProvider('databaseProvider')]
 *     public function testSomething(string $dbName, Driver $driver): void
 *     {
 *         // ...
 *     }
 * }
 * ```
 */
abstract class AbstractDriverTestCase extends TestCase
{
    /**
     * Test database configurations
     *
     * @var array
     */
    protected static $testDatabases = [];

    /**
     * Driver instances keyed by [className][dbName]
     *
     * Each subclass references drivers from the shared pool.
     * Teardown drops tables but does NOT disconnect, since
     * connections are shared across classes.
     *
     * @var array<string, array<string, Driver>>
     */
    protected static $drivers = [];

    /**
     * Shared driver connection pool keyed by dbName
     *
     * PHPUnit evaluates ALL data providers before running any tests.
     * Without pooling, each class creates its own connection, which
     * can exhaust database thread limits (e.g. Informix).
     *
     * @var array<string, Driver>
     */
    protected static $sharedPool = [];

    /**
     * Connection errors keyed by backend name
     *
     * Populated by databaseProvider() when a backend fails to connect.
     * Checked by allConfiguredBackendsConnected() to fail explicitly.
     *
     * @var array<string, string>
     */
    protected static $connectionErrors = [];

    /**
     * Backends attempted per class via the base databaseProvider()
     *
     * Tracks which backends each class tried to connect to, so
     * allConfiguredBackendsConnected() only checks backends that
     * this specific class attempted. Backend-specific subclasses
     * that override databaseProvider() won't populate this.
     *
     * @var array<string, array<string, true>>
     */
    protected static $attemptedBackends = [];

    /**
     * Create test tables and insert seed data for a database
     *
     * Called once per driver in setUpBeforeClass(), right before the
     * class's tests run. This avoids cross-class table collisions that
     * would occur if setup ran during data provider evaluation (PHPUnit
     * evaluates ALL providers before running any tests).
     *
     * @param Driver $driver Database driver
     * @return void
     */
    abstract protected static function setUpDatabase(Driver $driver): void;

    /**
     * Return table names created by this test for automatic cleanup
     *
     * Override this to return table names in drop order (children before parents).
     * Tables are dropped in tearDownAfterClass().
     *
     * @return array Table names
     */
    protected static function getTestTables(): array
    {
        return [];
    }

    /**
     * Return sequence names created by this test class for automatic cleanup.
     *
     * Override this to return sequence names that should be dropped
     * in tearDownAfterClass().
     *
     * @return array Sequence names
     */
    protected static function getTestSequences(): array
    {
        return [];
    }

    /**
     * Get driver instances for the current test class
     *
     * @return array<string, Driver>
     */
    protected static function getClassDrivers(): array
    {
        return static::$drivers[static::class] ?? [];
    }

    /**
     * Get an environment variable from $_ENV, $_SERVER, or getenv()
     *
     * PHPUnit 11 sets <env> values in $_ENV only. Real environment
     * variables may be in $_SERVER or accessible via getenv().
     *
     * @param string $name Environment variable name
     * @return string|null Value or null if not set
     */
    protected static function getEnv(string $name): ?string
    {
        if (isset($_ENV[$name])) {
            return (string) $_ENV[$name];
        }
        if (isset($_SERVER[$name])) {
            return (string) $_SERVER[$name];
        }
        $value = getenv($name);
        return $value !== false ? $value : null;
    }

    /**
     * Universal parameters scanned for every backend
     *
     * Instead of mapping each driver to its known params, we scan all
     * common params for every backend. Unset params are simply absent
     * from the config — drivers ignore keys they don't use.
     *
     * @var string[]
     */
    protected static $universalParams = [
        'host', 'port', 'database', 'user', 'password', 'prefix', 'charset',
        'service', 'server', 'protocol', 'role', 'schema', 'timezone',
        'ssl_ca', 'foreign_keys', 'journal_mode', 'busy_timeout', 'synchronous',
        'trust_server_certificate', 'dsn',
    ];

    /**
     * Load database configuration from environment variables
     *
     * Reads DB_TEST_BACKENDS for the list of backends to test, then
     * scans DB_{NAME}_* env vars for each backend's config.
     *
     * Multiple entries can share the same driver. For example, 'firebird3'
     * and 'firebird5' can both set DB_{NAME}_DRIVER=firebird to test
     * against different Firebird server versions.
     *
     * Adding a new backend requires zero PHP changes — just update
     * the env vars in phpunit.xml.
     *
     * @return array Database configurations keyed by backend name
     */
    protected static function loadDatabaseConfig(): array
    {
        $backends = static::getEnv('DB_TEST_BACKENDS');
        if ($backends === null || $backends === '') {
            $backends = 'mock';
        }

        $databases = [];
        foreach (explode(',', $backends) as $name) {
            $name = trim($name);
            if ($name === '') {
                continue;
            }

            $prefix = 'DB_' . strtoupper($name) . '_';
            $driver = static::getEnv($prefix . 'DRIVER') ?? $name;

            $config = ['driver' => $driver];
            foreach (static::$universalParams as $param) {
                $value = static::getEnv($prefix . strtoupper($param));
                if ($value !== null) {
                    if (in_array($param, ['port', 'service', 'busy_timeout']) && is_numeric($value)) {
                        $value = (int) $value;
                    }
                    $config[$param] = $value;
                }
            }

            $databases[$name] = $config;
        }

        return $databases;
    }

    /**
     * Data provider for database connections
     *
     * Creates per-class driver instances (connections only — no table setup).
     * Table setup is deferred to setUpBeforeClass() so that it runs right
     * before each class's tests, avoiding cross-class collisions on shared
     * databases.
     *
     * PHPUnit evaluates data providers for ALL discovered test classes before
     * running any tests. By only creating connections here, multiple classes
     * can safely use the same table names.
     *
     * @return array Array of [dbName, driver] pairs
     */
    public static function databaseProvider(): array
    {
        if (empty(static::$testDatabases)) {
            static::$testDatabases = static::loadDatabaseConfig();
        }

        $class = static::class;
        $databases = [];

        foreach (static::$testDatabases as $dbName => $config) {
            static::$attemptedBackends[$class][$dbName] = true;

            if (!isset(static::$drivers[$class][$dbName])) {
                // Reuse shared pool connection if available
                if (isset(static::$sharedPool[$dbName])) {
                    static::$drivers[$class][$dbName] = static::$sharedPool[$dbName];
                } else {
                    try {
                        $driver = static::createDriver($dbName, $config);
                        static::$sharedPool[$dbName] = $driver;
                        static::$drivers[$class][$dbName] = $driver;
                    } catch (\Exception $e) {
                        if (!isset(static::$connectionErrors[$dbName])) {
                            static::$connectionErrors[$dbName] = $e->getMessage();
                            fwrite(STDERR, sprintf(
                                "\n  [DB] Skipping '%s' backend: %s\n",
                                $dbName,
                                $e->getMessage()
                            ));
                        }
                        continue;
                    }
                }
            }

            $databases[$dbName] = [$dbName, static::$drivers[$class][$dbName]];
        }

        return $databases;
    }

    /**
     * Create a driver instance for the specified database
     *
     * Delegates to DatabaseManager for driver resolution. Override
     * getDatabaseManager() in subclasses that need custom drivers.
     *
     * @param  string $dbName Database name (config key)
     * @param  array  $config Database configuration (must include 'driver')
     * @return Driver
     * @throws \RuntimeException If driver is unknown or unavailable
     */
    protected static function createDriver(string $dbName, array $config): Driver
    {
        $manager = static::getDatabaseManager();
        return $manager->makeDriver($dbName, $config);
    }

    /**
     * Get the DatabaseManager instance
     *
     * Override in subclasses to register custom drivers via extend().
     *
     * @return DatabaseManager
     */
    protected static function getDatabaseManager(): DatabaseManager
    {
        static $manager;
        if (!$manager) {
            $manager = new DatabaseManager();
        }
        return $manager;
    }

    /**
     * Set up tables before the class's tests run
     *
     * Calls setUpDatabase() for each cached driver. This runs after all data
     * providers have been evaluated but right before this class's first test,
     * so tables created here won't collide with other classes' tables.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        foreach (static::getClassDrivers() as $dbName => $driver) {
            static::setUpDatabase($driver);
        }
    }

    /**
     * Commit pending transactions before each test
     *
     * Prevents deadlocks from uncommitted transactions left by previous tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        static::commitPendingTransactions(static::getClassDrivers());
    }

    /**
     * Verify all configured backends connected successfully
     *
     * Backends listed in DB_TEST_BACKENDS are required, not optional.
     * If a backend is configured but can't connect, this test fails
     * instead of silently skipping. Remove backends from DB_TEST_BACKENDS
     * if they're not available in the current environment.
     *
     * Only checks backends that this class attempted via the base
     * databaseProvider(). Backend-specific subclasses that override
     * databaseProvider() have their own availability checks and are
     * not affected.
     */
    #[Test]
    public function allConfiguredBackendsConnected(): void
    {
        $attempted = static::$attemptedBackends[static::class] ?? [];

        if (empty($attempted)) {
            $this->assertTrue(true);
            return;
        }

        $classDrivers = static::getClassDrivers();
        $failures = [];

        foreach (array_keys($attempted) as $dbName) {
            if (!isset($classDrivers[$dbName]) && isset(static::$connectionErrors[$dbName])) {
                $failures[] = sprintf("  %s: %s", $dbName, static::$connectionErrors[$dbName]);
            }
        }

        if (!empty($failures)) {
            $this->fail(
                "Configured backend(s) failed to connect:\n"
                . implode("\n", $failures)
                . "\nRemove from DB_TEST_BACKENDS or fix the connection."
            );
        }

        $this->assertTrue(true);
    }

    /**
     * Return test model classes that use runtime table overrides
     *
     * Override this in subclasses that call useTable() on test models.
     * Each class must have a static useDefaultTable(bool) method.
     * Called during tearDownAfterClass() to reset static table overrides,
     * preventing them from leaking into subsequent test classes.
     *
     * @return array<class-string> Model class names
     */
    protected static function getTestModelClasses(): array
    {
        return [];
    }

    /**
     * Drop test tables and reset model table overrides for this class
     */
    public static function tearDownAfterClass(): void
    {
        // Reset static table overrides so they don't leak across classes
        foreach (static::getTestModelClasses() as $modelClass) {
            if (method_exists($modelClass, 'useDefaultTable')) {
                $modelClass::useDefaultTable(true);
            }
        }

        $tables = static::getTestTables();
        $classDrivers = static::getClassDrivers();

        foreach ($classDrivers as $dbName => $driver) {
            if (!$driver instanceof Driver) {
                continue;
            }

            // Commit any pending transactions before dropping tables
            try {
                $connection = $driver->getConnection();
                if ($connection && $connection->inTransaction()) {
                    $connection->commit();
                }
            } catch (\Exception $e) {
                // Ignore
            }

            foreach ($tables as $table) {
                try {
                    $driver->dropTable($table, true);
                } catch (\Exception $e) {
                    fwrite(STDERR, sprintf(
                        "\n  [teardown] %s DROP TABLE %s: %s\n",
                        $dbName,
                        $table,
                        $e->getMessage()
                    ));
                }
            }

            foreach (static::getTestSequences() as $seq) {
                try {
                    $driver->dropSequence($seq);
                } catch (\Exception $e) {
                    // Ignore — sequence may not exist
                }
            }
        }

        unset(static::$drivers[static::class]);
        unset(static::$attemptedBackends[static::class]);
        parent::tearDownAfterClass();
    }

    /**
     * Commit any outstanding transactions across all drivers
     *
     * @param array $drivers Array of cached drivers
     * @return void
     */
    protected static function commitPendingTransactions(array $drivers): void
    {
        foreach ($drivers as $dbName => $driver) {
            if (!$driver instanceof Driver) {
                continue;
            }

            try {
                $connection = $driver->getConnection();
                if ($connection && $connection->inTransaction()) {
                    $connection->commit();
                }
            } catch (\Exception $e) {
                // Ignore - connection may be closed or no transaction
            }
        }
    }
}
