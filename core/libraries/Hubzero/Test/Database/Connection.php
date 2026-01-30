<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Test\Database;

/**
 * Database connection wrapper for testing
 *
 * Provides helper methods for test assertions like getRowCount() and createQueryTable()
 */
class Connection
{
    /**
     * The PDO connection
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * The schema/database name
     *
     * @var string
     */
    protected $schema;

    /**
     * Create a new connection wrapper
     *
     * @param  \PDO    $pdo
     * @param  string  $schema
     */
    public function __construct(\PDO $pdo, string $schema = '')
    {
        $this->pdo = $pdo;
        $this->schema = $schema;
    }

    /**
     * Get the underlying PDO connection
     *
     * @return \PDO
     */
    public function getConnection(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Get the schema name
     *
     * @return string
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * Get the row count for a table
     *
     * @param  string       $tableName
     * @param  string|null  $whereClause  Optional WHERE clause (without WHERE keyword)
     * @return int
     */
    public function getRowCount(string $tableName, ?string $whereClause = null): int
    {
        $sql = "SELECT COUNT(*) FROM {$tableName}";

        if ($whereClause !== null) {
            $sql .= " WHERE {$whereClause}";
        }

        $stmt = $this->pdo->query($sql);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Create a Table from a SQL query
     *
     * @param  string  $tableName  Name to give the resulting table
     * @param  string  $sql        SQL query to execute
     * @return Table
     */
    public function createQueryTable(string $tableName, string $sql): Table
    {
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $columns = [];
        if (!empty($rows)) {
            $columns = array_keys($rows[0]);
        }

        return new Table($tableName, $columns, $rows);
    }

    /**
     * Create a Table from an existing database table
     *
     * @param  string      $tableName
     * @param  array|null  $columns    Specific columns to include, or null for all
     * @return Table
     */
    public function createTable(string $tableName, ?array $columns = null): Table
    {
        if ($columns !== null) {
            $columnList = implode(', ', $columns);
            $sql = "SELECT {$columnList} FROM {$tableName}";
        } else {
            $sql = "SELECT * FROM {$tableName}";
        }

        return $this->createQueryTable($tableName, $sql);
    }

    /**
     * Truncate a table (delete all rows)
     *
     * @param  string  $tableName
     * @return void
     */
    public function truncate(string $tableName): void
    {
        // SQLite doesn't support TRUNCATE, use DELETE instead
        $driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

        if ($driver === 'sqlite') {
            $this->pdo->exec("DELETE FROM {$tableName}");
            // Reset autoincrement if sqlite_sequence exists
            try {
                $this->pdo->exec("DELETE FROM sqlite_sequence WHERE name = '{$tableName}'");
            } catch (\PDOException $e) {
                // sqlite_sequence doesn't exist until autoincrement is used, ignore
            }
        } else {
            $this->pdo->exec("TRUNCATE TABLE {$tableName}");
        }
    }

    /**
     * Insert a row into a table
     *
     * @param  string  $tableName
     * @param  array   $row
     * @return void
     */
    public function insert(string $tableName, array $row): void
    {
        $columns = array_keys($row);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $tableName,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($row));
    }

    /**
     * Get all table names in the database
     *
     * @return array
     */
    public function getTableNames(): array
    {
        $driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

        switch ($driver) {
            case 'sqlite':
                $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'";
                break;
            case 'mysql':
                $sql = "SHOW TABLES";
                break;
            case 'pgsql':
                $sql = "SELECT tablename FROM pg_tables WHERE schemaname = 'public'";
                break;
            default:
                throw new \RuntimeException("Unsupported database driver: {$driver}");
        }

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}
