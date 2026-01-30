<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

use PDO;
use PDOException;

/**
 * MySQL Database Connection utility class
 *
 * Provides common functionality for establishing MySQL database connections.
 * Used by installers and other components that need low-level database access.
 *
 * This class is designed to work both with the HUBzero framework autoloading
 * and standalone during installation (before autoloading is available).
 */
class MysqlDatabaseConnection
{
    /**
     * Default connection timeout in seconds
     */
    private const DEFAULT_TIMEOUT = 30;

    /**
     * Build a MySQL DSN string from configuration
     *
     * @param   array  $config  Database configuration with keys:
     *                          - host: Server hostname (default: localhost)
     *                          - port: Server port (optional)
     *                          - socket: Unix socket path (optional, overrides host/port)
     *                          - db: Database name (optional for initial connection)
     * @return  string  MySQL DSN string
     */
    public static function buildDsn(array $config): string
    {
        $dsn = 'mysql:';

        // Socket takes precedence over host/port
        if (!empty($config['socket'])) {
            $dsn .= 'unix_socket=' . $config['socket'];
        } else {
            $dsn .= 'host=' . ($config['host'] ?? 'localhost');
            if (!empty($config['port'])) {
                $dsn .= ';port=' . $config['port'];
            }
        }

        // Add database name if provided
        if (!empty($config['db'])) {
            $dsn .= ';dbname=' . $config['db'];
        }

        // Always use utf8mb4
        $dsn .= ';charset=utf8mb4';

        return $dsn;
    }

    /**
     * Get default PDO options for MySQL connections
     *
     * @param   int  $timeout  Connection timeout in seconds
     * @return  array  PDO options array
     */
    public static function getDefaultOptions(int $timeout = self::DEFAULT_TIMEOUT): array
    {
        return [
            PDO::ATTR_TIMEOUT => $timeout,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'",
        ];
    }

    /**
     * Connect to a MySQL database
     *
     * @param   array  $config   Database configuration
     * @param   int    $timeout  Connection timeout in seconds
     * @return  PDO|null  PDO connection or null on failure
     */
    public static function connect(array $config, int $timeout = self::DEFAULT_TIMEOUT): ?PDO
    {
        try {
            $dsn = self::buildDsn($config);
            $options = self::getDefaultOptions($timeout);

            return new PDO(
                $dsn,
                $config['user'] ?? '',
                $config['password'] ?? '',
                $options
            );
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Connect to a MySQL database, throwing exception on failure
     *
     * @param   array  $config   Database configuration
     * @param   int    $timeout  Connection timeout in seconds
     * @return  PDO  PDO connection
     * @throws  PDOException  If connection fails
     */
    public static function connectOrFail(array $config, int $timeout = self::DEFAULT_TIMEOUT): PDO
    {
        $dsn = self::buildDsn($config);
        $options = self::getDefaultOptions($timeout);

        return new PDO(
            $dsn,
            $config['user'] ?? '',
            $config['password'] ?? '',
            $options
        );
    }

    /**
     * Test database connection and return result
     *
     * @param   array  $config  Database configuration
     * @return  array  Result array with keys 'success', 'message', and optionally 'pdo'
     */
    public static function test(array $config): array
    {
        try {
            $pdo = self::connectOrFail($config, 10);

            return [
                'success' => true,
                'message' => 'Connection successful',
                'pdo' => $pdo,
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if a database exists
     *
     * @param   PDO     $pdo   PDO connection (without specific database selected)
     * @param   string  $name  Database name to check
     * @return  bool  True if database exists
     */
    public static function databaseExists(PDO $pdo, string $name): bool
    {
        try {
            $stmt = $pdo->prepare(
                "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :name"
            );
            $stmt->execute(['name' => $name]);

            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Create a database
     *
     * @param   PDO     $pdo   PDO connection
     * @param   string  $name  Database name to create
     * @return  bool  True on success
     */
    public static function createDatabase(PDO $pdo, string $name): bool
    {
        try {
            // Validate database name (basic protection against SQL injection)
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
                return false;
            }

            $pdo->exec(
                "CREATE DATABASE `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
