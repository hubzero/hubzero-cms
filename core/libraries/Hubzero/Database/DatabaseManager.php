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
 * Resolves driver names to Driver instances.
 * Third parties can register custom drivers via extend().
 *
 * Resolution order for makeDriver():
 * 1. Parse 'url' into config components (if present)
 * 2. Custom creators registered via extend()
 * 3. Built-in driver resolution via BackendRegistry
 * 4. Convention class fallback:
 *    - Hubzero\Database\Drivers\<Name>\<Name>Driver
 *
 * Built-in drivers: mysql, mariadb, percona, cubrid, sqlite, mock, pgsql, firebird, informix, oci, sqlsrv, db2, ase
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

        // Prefer canonical built-in registry resolution.
        $builtInClass = BackendRegistry::resolveDriverClassFor((string) $driverName);
        if ($builtInClass !== null) {
            return $this->buildDriver($builtInClass, $config);
        }

        // Custom class fallback by canonical naming convention.
        $candidate = BackendRegistry::firstExistingClassCandidate(
            BackendRegistry::conventionDriverClassCandidates((string) $driverName)
        );
        if ($candidate !== null) {
            if (!is_subclass_of($candidate, Driver::class)) {
                throw new \RuntimeException(
                    "Invalid driver class {$candidate}; expected subclass of " . Driver::class
                );
            }

            return $this->buildDriver($candidate, $config);
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
        // Canonical built-ins come from registry metadata.
        $builtIn = array_keys(BackendRegistry::driverClassMap());

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
            $class = BackendRegistry::resolveDriverClassFor($name);
            $result[$name] = [
                'class'     => $class,
                'available' => $class ? $class::test() : null,
                'custom'    => isset($this->customCreators[$name]),
            ];
        }

        return $result;
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
