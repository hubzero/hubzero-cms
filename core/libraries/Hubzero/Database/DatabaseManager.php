<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

/**
 * Database Manager
 *
 * Resolves driver names to Driver instances using convention-based factory
 * methods. Third parties can register custom drivers via extend().
 *
 * Resolution order for makeDriver():
 * 1. Parse 'url' into config components (if present)
 * 2. Custom creators registered via extend()
 * 3. Convention method: create{Name}Driver()
 *
 * Built-in drivers: mysql, mariadb, percona, cubrid, sqlite, mock, pgsql, firebird, informix, oci, sqlsrv, db2
 *
 * Usage:
 * ```php
 * $manager = new DatabaseManager();
 *
 * // Create a driver from config
 * $driver = $manager->makeDriver('main', [
 *     'driver'   => 'mysql',
 *     'host'     => 'localhost',
 *     'database' => 'hubzero',
 *     'user'     => 'root',
 *     'password' => 'secret',
 * ]);
 *
 * // Register a third-party driver
 * $manager->extend('mongodb', function ($config) {
 *     return new MongoDBDriver($config);
 * });
 *
 * // List all available drivers
 * $drivers = $manager->getAvailableDrivers();
 * ```
 */
class DatabaseManager
{
    /**
     * Custom driver resolvers registered via extend()
     *
     * @var array<string, callable>
     */
    protected $customCreators = [];

    /**
     * Map of built-in driver names to their class names
     *
     * Used by getDriverAvailability() to check PHP extension status
     * without instantiating drivers. Multiple names can map to the
     * same class (e.g., mariadb and percona both use Mysql).
     *
     * @var array<string, string>
     */
    protected static $builtInDrivers = [
        'mysql'    => Driver\Mysql::class,
        'mariadb'  => Driver\Mariadb::class,
        'percona'  => Driver\Mysql::class,
        'cubrid'   => Driver\Cubrid::class,
        'sqlite'   => Driver\Sqlite::class,
        'mock'     => Driver\Mock::class,
        'pgsql'    => Driver\Pgsql::class,
        'firebird' => Driver\Firebird::class,
        'informix' => Driver\Informix::class,
        'oci'      => Driver\Oci::class,
        'sqlsrv'   => Driver\Sqlsrv::class,
        'db2'      => Driver\Db2::class,
    ];

    /**
     * Create a driver from a config array
     *
     * @param  string $name   Connection name
     * @param  array  $config Connection config (must include 'driver' or 'url')
     * @return Driver
     * @throws \RuntimeException If driver is unsupported
     */
    public function makeDriver(string $name, array $config): Driver
    {
        if (isset($config['url'])) {
            $config = $this->parseUrl($config);
        }

        $driverName = $config['driver'] ?? $name;

        if (isset($this->customCreators[$driverName])) {
            return call_user_func($this->customCreators[$driverName], $config, $name);
        }

        $method = 'create' . ucfirst($driverName) . 'Driver';

        if (method_exists($this, $method)) {
            return $this->$method($config);
        }

        throw new \RuntimeException("Unsupported driver: {$driverName}");
    }

    /**
     * Parse a database URL into config components
     *
     * Supports: scheme://user:password@host:port/database?key=value
     * Explicit config keys take precedence over URL-derived values.
     *
     * @param  array $config Config array with 'url' key
     * @return array Config with URL parsed into individual keys
     */
    protected function parseUrl(array $config): array
    {
        $parsed = parse_url($config['url']);

        $mapping = [
            'scheme' => 'driver',
            'host'   => 'host',
            'port'   => 'port',
            'user'   => 'user',
            'pass'   => 'password',
        ];

        foreach ($mapping as $urlKey => $configKey) {
            if (isset($parsed[$urlKey]) && !isset($config[$configKey])) {
                $config[$configKey] = $parsed[$urlKey];
            }
        }

        if (isset($parsed['path']) && !isset($config['database'])) {
            $config['database'] = ltrim($parsed['path'], '/');
        }

        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $extra);
            $config = array_merge($extra, $config);
        }

        unset($config['url']);
        return $config;
    }

    /**
     * Register a custom driver resolver
     *
     * @param  string   $name     Driver name
     * @param  callable $resolver Receives ($config, $name), returns Driver
     * @return $this
     */
    public function extend(string $name, callable $resolver): self
    {
        $this->customCreators[$name] = $resolver;
        return $this;
    }

    /**
     * Get all available driver names (built-in + custom)
     *
     * @return array
     */
    public function getAvailableDrivers(): array
    {
        $builtIn = [];
        foreach (get_class_methods($this) as $method) {
            if (preg_match('/^create(\w+)Driver$/', $method, $m)) {
                $builtIn[] = lcfirst($m[1]);
            }
        }

        return array_unique(array_merge($builtIn, array_keys($this->customCreators)));
    }

    /**
     * Get availability status for all registered drivers
     *
     * Returns an array keyed by driver name with:
     * - 'class'     => FQCN or null for custom drivers
     * - 'available' => bool (PHP extension present) or null if unknown
     * - 'custom'    => bool (registered via extend())
     *
     * @return array<string, array{class: ?string, available: ?bool, custom: bool}>
     */
    public function getDriverAvailability(): array
    {
        $result = [];

        foreach ($this->getAvailableDrivers() as $name) {
            $class = static::$builtInDrivers[$name] ?? null;
            $result[$name] = [
                'class'     => $class,
                'available' => $class ? $class::test() : null,
                'custom'    => isset($this->customCreators[$name]),
            ];
        }

        return $result;
    }

    // =========================================================================
    // Built-in factory methods
    // =========================================================================

    protected function createMysqlDriver(array $config): Driver
    {
        return $this->buildDriver(Driver\Mysql::class, $config);
    }

    protected function createMariadbDriver(array $config): Driver
    {
        return $this->buildDriver(Driver\Mariadb::class, $config);
    }

    protected function createPerconaDriver(array $config): Driver
    {
        return $this->buildDriver(Driver\Mysql::class, $config);
    }

    protected function createCubridDriver(array $config): Driver
    {
        return $this->buildDriver(Driver\Cubrid::class, $config);
    }

    protected function createSqliteDriver(array $config): Driver
    {
        return $this->buildDriver(Driver\Sqlite::class, $config);
    }

    protected function createMockDriver(array $config): Driver
    {
        return $this->buildDriver(Driver\Mock::class, $config);
    }

    protected function createPgsqlDriver(array $config): Driver
    {
        return $this->buildDriver(Driver\Pgsql::class, $config);
    }

    protected function createFirebirdDriver(array $config): Driver
    {
        return $this->buildDriver(Driver\Firebird::class, $config);
    }

    protected function createInformixDriver(array $config): Driver
    {
        return $this->buildDriver(Driver\Informix::class, $config);
    }

    protected function createOciDriver(array $config): Driver
    {
        return $this->buildDriver(Driver\Oci::class, $config);
    }

    protected function createSqlsrvDriver(array $config): Driver
    {
        return $this->buildDriver(Driver\Sqlsrv::class, $config);
    }

    protected function createDb2Driver(array $config): Driver
    {
        return $this->buildDriver(Driver\Db2::class, $config);
    }

    /**
     * Instantiate and connect a driver
     *
     * @param  string $class  Driver class name
     * @param  array  $config Connection config
     * @return Driver
     * @throws \RuntimeException If driver extension is not available
     */
    protected function buildDriver(string $class, array $config): Driver
    {
        if (!$class::test()) {
            throw new \RuntimeException("Driver not available: {$class}");
        }

        $driver = new $class($config);
        $driver->connect();
        return $driver;
    }
}
