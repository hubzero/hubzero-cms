<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

/**
 * Represents complete metadata about a database schema
 *
 * This class provides a portable, driver-agnostic representation of an entire
 * database schema. It contains all tables with their columns, indexes, and
 * foreign keys as structured value objects.
 *
 * The DatabaseInfo object can be:
 * - Introspected from any supported database (MySQL, SQLite, PostgreSQL)
 * - Serialized to array/JSON for storage or comparison
 * - Used for schema diff/comparison operations
 * - Passed to another driver for cross-database migration
 *
 * Usage:
 * ```php
 * // Introspect from MySQL
 * $schema = $db->schema()->introspectDatabase();
 *
 * // Database properties
 * echo $schema->getName();           // 'my_database'
 * echo $schema->getTableCount();     // 42
 *
 * // Iterate all tables
 * foreach ($schema->getTables() as $table) {
 *     echo $table->getName();
 *     foreach ($table->getColumns() as $column) {
 *         echo "  " . $column->getName() . ": " . $column->getType();
 *     }
 * }
 *
 * // Get specific table
 * $usersTable = $schema->getTable('jos_users');
 * if ($usersTable) {
 *     $columns = $usersTable->getColumns();
 * }
 *
 * // Serialize for comparison
 * $data = $schema->toArray();
 * file_put_contents('schema.json', json_encode($data, JSON_PRETTY_PRINT));
 * ```
 */
class DatabaseInfo
{
    /**
     * Database name
     *
     * @var string
     */
    protected $name;

    /**
     * Default character set
     *
     * @var string|null
     */
    protected $charset;

    /**
     * Default collation
     *
     * @var string|null
     */
    protected $collation;

    /**
     * Table information indexed by name
     *
     * @var TableInfo[]
     */
    protected $tables = [];

    /**
     * Create a new DatabaseInfo instance
     *
     * @param array $data Database data from introspection
     */
    public function __construct(array $data = [])
    {
        $this->name = $data['name'] ?? '';
        $this->charset = $data['charset'] ?? null;
        $this->collation = $data['collation'] ?? null;

        // Build table objects indexed by name
        if (isset($data['tables']) && is_array($data['tables'])) {
            foreach ($data['tables'] as $tableData) {
                if ($tableData instanceof TableInfo) {
                    $this->tables[$tableData->getName()] = $tableData;
                } else {
                    $table = new TableInfo($tableData);
                    $this->tables[$table->getName()] = $table;
                }
            }
        }
    }

    /**
     * Get the database name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the default character set
     *
     * @return string|null
     */
    public function getCharset(): ?string
    {
        return $this->charset;
    }

    /**
     * Get the default collation
     *
     * @return string|null
     */
    public function getCollation(): ?string
    {
        return $this->collation;
    }

    /**
     * Get all tables
     *
     * @return TableInfo[]
     */
    public function getTables(): array
    {
        return array_values($this->tables);
    }

    /**
     * Get all table names
     *
     * @return array
     */
    public function getTableNames(): array
    {
        return array_keys($this->tables);
    }

    /**
     * Get a specific table by name
     *
     * @param  string  $name
     * @return TableInfo|null
     */
    public function getTable(string $name): ?TableInfo
    {
        return $this->tables[$name] ?? null;
    }

    /**
     * Check if a table exists
     *
     * @param  string  $name
     * @return bool
     */
    public function hasTable(string $name): bool
    {
        return isset($this->tables[$name]);
    }

    /**
     * Get the number of tables
     *
     * @return int
     */
    public function getTableCount(): int
    {
        return count($this->tables);
    }

    /**
     * Get tables matching a pattern
     *
     * @param  string  $pattern  Glob-style pattern (e.g., 'jos_users*')
     * @return TableInfo[]
     */
    public function getTablesMatching(string $pattern): array
    {
        $regex = '/^' . str_replace(['*', '?'], ['.*', '.'], preg_quote($pattern, '/')) . '$/';
        $matching = [];

        foreach ($this->tables as $name => $table) {
            if (preg_match($regex, $name)) {
                $matching[] = $table;
            }
        }

        return $matching;
    }

    /**
     * Get tables with a specific prefix
     *
     * @param  string  $prefix
     * @return TableInfo[]
     */
    public function getTablesWithPrefix(string $prefix): array
    {
        $matching = [];

        foreach ($this->tables as $name => $table) {
            if (strpos($name, $prefix) === 0) {
                $matching[] = $table;
            }
        }

        return $matching;
    }

    /**
     * Get all foreign key relationships in the database
     *
     * Returns an array of all foreign keys across all tables,
     * useful for understanding the database relationship graph.
     *
     * @return array  Array of ['table' => TableInfo, 'foreign_key' => ForeignKeyInfo]
     */
    public function getAllForeignKeys(): array
    {
        $allFks = [];

        foreach ($this->tables as $table) {
            foreach ($table->getForeignKeys() as $fk) {
                $allFks[] = [
                    'table' => $table,
                    'foreign_key' => $fk,
                ];
            }
        }

        return $allFks;
    }

    /**
     * Get tables that reference a specific table
     *
     * @param  string  $tableName
     * @return TableInfo[]
     */
    public function getTablesReferencingTable(string $tableName): array
    {
        $referencing = [];

        foreach ($this->tables as $table) {
            foreach ($table->getForeignKeys() as $fk) {
                if ($fk->references($tableName)) {
                    $referencing[] = $table;
                    break;
                }
            }
        }

        return $referencing;
    }

    /**
     * Get total column count across all tables
     *
     * @return int
     */
    public function getTotalColumnCount(): int
    {
        $count = 0;
        foreach ($this->tables as $table) {
            $count += $table->getColumnCount();
        }
        return $count;
    }

    /**
     * Get total index count across all tables
     *
     * @return int
     */
    public function getTotalIndexCount(): int
    {
        $count = 0;
        foreach ($this->tables as $table) {
            $count += $table->getIndexCount();
        }
        return $count;
    }

    /**
     * Get total foreign key count across all tables
     *
     * @return int
     */
    public function getTotalForeignKeyCount(): int
    {
        $count = 0;
        foreach ($this->tables as $table) {
            $count += $table->getForeignKeyCount();
        }
        return $count;
    }

    /**
     * Convert to array representation
     *
     * This creates a portable array that can be:
     * - Serialized to JSON for storage
     * - Used for schema comparison
     * - Reconstructed into a DatabaseInfo object
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'charset' => $this->charset,
            'collation' => $this->collation,
            'tables' => array_map(function ($table) {
                return $table->toArray();
            }, array_values($this->tables)),
        ];
    }

    /**
     * Create a DatabaseInfo instance from an array
     *
     * @param  array  $data
     * @return static
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}
