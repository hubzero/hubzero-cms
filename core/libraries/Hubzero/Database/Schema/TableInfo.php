<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

/**
 * Represents complete metadata about a database table
 *
 * This class provides a unified, structured interface for all table metadata
 * including columns, indexes, and foreign keys. It consolidates multiple
 * introspection calls into a single object.
 *
 * Usage:
 * ```php
 * $table = $schema->introspectTable('#__users');
 *
 * // Table properties
 * echo $table->getName();        // 'jos_users'
 * echo $table->getEngine();      // 'InnoDB'
 *
 * // Columns
 * foreach ($table->getColumns() as $column) {
 *     echo $column->getName() . ': ' . $column->getType();
 * }
 *
 * // Get specific column
 * $emailCol = $table->getColumn('email');
 * if ($emailCol && $emailCol->isNullable()) {
 *     // ...
 * }
 *
 * // Indexes
 * foreach ($table->getIndexes() as $index) {
 *     echo $index->getName();
 * }
 *
 * // Foreign keys
 * foreach ($table->getForeignKeys() as $fk) {
 *     echo $fk->getName() . ' -> ' . $fk->getForeignTable();
 * }
 * ```
 */
class TableInfo
{
    /**
     * Table name (with prefix resolved)
     *
     * @var string
     */
    protected $name;

    /**
     * Storage engine (MySQL: InnoDB, MyISAM, etc.)
     *
     * @var string|null
     */
    protected $engine;

    /**
     * Character set
     *
     * @var string|null
     */
    protected $charset;

    /**
     * Collation
     *
     * @var string|null
     */
    protected $collation;

    /**
     * Table comment
     *
     * @var string|null
     */
    protected $comment;

    /**
     * Auto-increment value
     *
     * @var int|null
     */
    protected $autoIncrement;

    /**
     * Column information
     *
     * @var ColumnInfo[]
     */
    protected $columns = [];

    /**
     * Index information
     *
     * @var IndexInfo[]
     */
    protected $indexes = [];

    /**
     * Foreign key information
     *
     * @var ForeignKeyInfo[]
     */
    protected $foreignKeys = [];

    /**
     * Primary key column name(s)
     *
     * @var string|array|null
     */
    protected $primaryKey;

    /**
     * Create a new TableInfo instance
     *
     * @param array $data Table data from introspection
     */
    public function __construct(array $data = [])
    {
        $this->name = $data['name'] ?? '';
        $this->engine = $data['engine'] ?? null;
        $this->charset = $data['charset'] ?? null;
        $this->collation = $data['collation'] ?? null;
        $this->comment = $data['comment'] ?? null;
        $this->autoIncrement = $data['auto_increment'] ?? null;
        $this->primaryKey = $data['primary_key'] ?? null;

        // Build column objects
        if (isset($data['columns']) && is_array($data['columns'])) {
            foreach ($data['columns'] as $colData) {
                $this->columns[] = new ColumnInfo($colData);
            }
        }

        // Build index objects
        if (isset($data['indexes']) && is_array($data['indexes'])) {
            foreach ($data['indexes'] as $idxData) {
                $this->indexes[] = new IndexInfo($idxData);
            }
        }

        // Build foreign key objects
        if (isset($data['foreign_keys']) && is_array($data['foreign_keys'])) {
            foreach ($data['foreign_keys'] as $fkData) {
                $this->foreignKeys[] = new ForeignKeyInfo($fkData);
            }
        }
    }

    /**
     * Get the table name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the storage engine
     *
     * @return string|null
     */
    public function getEngine(): ?string
    {
        return $this->engine;
    }

    /**
     * Get the character set
     *
     * @return string|null
     */
    public function getCharset(): ?string
    {
        return $this->charset;
    }

    /**
     * Get the collation
     *
     * @return string|null
     */
    public function getCollation(): ?string
    {
        return $this->collation;
    }

    /**
     * Get the table comment
     *
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Get the auto-increment value
     *
     * @return int|null
     */
    public function getAutoIncrement(): ?int
    {
        return $this->autoIncrement;
    }

    /**
     * Get the primary key column name(s)
     *
     * @return string|array|null
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Get all columns
     *
     * @return ColumnInfo[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get a specific column by name
     *
     * @param  string  $name
     * @return ColumnInfo|null
     */
    public function getColumn(string $name): ?ColumnInfo
    {
        foreach ($this->columns as $column) {
            if ($column->getName() === $name) {
                return $column;
            }
        }
        return null;
    }

    /**
     * Check if a column exists
     *
     * @param  string  $name
     * @return bool
     */
    public function hasColumn(string $name): bool
    {
        return $this->getColumn($name) !== null;
    }

    /**
     * Get column names
     *
     * @return array
     */
    public function getColumnNames(): array
    {
        return array_map(function ($col) {
            return $col->getName();
        }, $this->columns);
    }

    /**
     * Get all indexes
     *
     * @return IndexInfo[]
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * Get a specific index by name
     *
     * @param  string  $name
     * @return IndexInfo|null
     */
    public function getIndex(string $name): ?IndexInfo
    {
        foreach ($this->indexes as $index) {
            if ($index->getName() === $name) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Check if an index exists
     *
     * @param  string  $name
     * @return bool
     */
    public function hasIndex(string $name): bool
    {
        return $this->getIndex($name) !== null;
    }

    /**
     * Get the primary key index
     *
     * @return IndexInfo|null
     */
    public function getPrimaryKeyIndex(): ?IndexInfo
    {
        foreach ($this->indexes as $index) {
            if ($index->isPrimary()) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Get all unique indexes (excluding primary key)
     *
     * @return IndexInfo[]
     */
    public function getUniqueIndexes(): array
    {
        return array_filter($this->indexes, function ($index) {
            return $index->isUnique() && !$index->isPrimary();
        });
    }

    /**
     * Get all foreign keys
     *
     * @return ForeignKeyInfo[]
     */
    public function getForeignKeys(): array
    {
        return $this->foreignKeys;
    }

    /**
     * Get a specific foreign key by name
     *
     * @param  string  $name
     * @return ForeignKeyInfo|null
     */
    public function getForeignKey(string $name): ?ForeignKeyInfo
    {
        foreach ($this->foreignKeys as $fk) {
            if ($fk->getName() === $name) {
                return $fk;
            }
        }
        return null;
    }

    /**
     * Check if a foreign key exists
     *
     * @param  string  $name
     * @return bool
     */
    public function hasForeignKey(string $name): bool
    {
        return $this->getForeignKey($name) !== null;
    }

    /**
     * Get foreign keys that reference a specific table
     *
     * @param  string  $table
     * @return ForeignKeyInfo[]
     */
    public function getForeignKeysTo(string $table): array
    {
        return array_filter($this->foreignKeys, function ($fk) use ($table) {
            return $fk->references($table);
        });
    }

    /**
     * Get the number of columns
     *
     * @return int
     */
    public function getColumnCount(): int
    {
        return count($this->columns);
    }

    /**
     * Get the number of indexes
     *
     * @return int
     */
    public function getIndexCount(): int
    {
        return count($this->indexes);
    }

    /**
     * Get the number of foreign keys
     *
     * @return int
     */
    public function getForeignKeyCount(): int
    {
        return count($this->foreignKeys);
    }

    /**
     * Convert to array representation
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'engine' => $this->engine,
            'charset' => $this->charset,
            'collation' => $this->collation,
            'comment' => $this->comment,
            'auto_increment' => $this->autoIncrement,
            'primary_key' => $this->primaryKey,
            'columns' => array_map(function ($col) {
                return $col->toArray();
            }, $this->columns),
            'indexes' => array_map(function ($idx) {
                return $idx->toArray();
            }, $this->indexes),
            'foreign_keys' => array_map(function ($fk) {
                return $fk->toArray();
            }, $this->foreignKeys),
        ];
    }
}
