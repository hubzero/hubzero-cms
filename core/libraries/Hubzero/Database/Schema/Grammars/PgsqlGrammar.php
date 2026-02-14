<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema\Grammars;

use Hubzero\Database\Schema\AlterTableBuilder;
use Hubzero\Database\Schema\TableDefinition;
use Hubzero\Database\Schema\Column;
use Hubzero\Database\Schema\Grammar;

/**
 * PostgreSQL-specific DDL grammar
 *
 * PostgreSQL has native types for most data including:
 * - SERIAL/BIGSERIAL for auto-increment
 * - BOOLEAN for true booleans
 * - JSON/JSONB for JSON data
 * - BYTEA for binary data
 */
class PgsqlGrammar extends Grammar
{
    /**
     * Compile CREATE TABLE statements from normalized definition data.
     *
     * @param  array $definition
     * @return array
     */
    public function compileCreateTableFromDefinition(array $definition): array
    {
        return $this->compileCreateTableFromDefinitionGeneric($definition);
    }

    /**
     * Type mappings from abstract types to PostgreSQL types
     *
     * @var array
     */
    protected $typeMap = [
        // Integer types (PostgreSQL has no TINYINT or MEDIUMINT)
        'tinyInteger' => 'SMALLINT',
        'smallInteger' => 'SMALLINT',
        'mediumInteger' => 'INTEGER',
        'integer' => 'INTEGER',
        'bigInteger' => 'BIGINT',
        'boolean' => 'BOOLEAN',

        // String types
        'string' => 'VARCHAR',
        'char' => 'CHAR',
        'tinyText' => 'TEXT',
        'text' => 'TEXT',
        'mediumText' => 'TEXT',
        'longText' => 'TEXT',

        // Numeric types
        'float' => 'REAL',
        'double' => 'DOUBLE PRECISION',
        'decimal' => 'DECIMAL',

        // Date/time types
        'date' => 'DATE',
        'time' => 'TIME',
        'datetime' => 'TIMESTAMP',
        'timestamp' => 'TIMESTAMP',
        'timestampTz' => 'TIMESTAMPTZ',
        'year' => 'SMALLINT',

        // Binary types
        'binary' => 'BYTEA',

        // Special types (PostgreSQL has native UUID and network types)
        'json' => 'JSONB',
        'uuid' => 'UUID',
        'ulid' => 'CHAR(26)',
        'ipAddress' => 'INET',
        'macAddress' => 'MACADDR',
    ];

    /**
     * Serial type mappings for auto-increment columns
     *
     * @var array
     */
    protected $serialMap = [
        'tinyInteger' => 'SMALLSERIAL',
        'smallInteger' => 'SMALLSERIAL',
        'integer' => 'SERIAL',
        'bigInteger' => 'BIGSERIAL',
    ];

    /**
     * Compile CREATE TABLE statement(s)
     *
     * PostgreSQL requires separate CREATE INDEX statements for all indexes.
     * Returns an array where the first element is CREATE TABLE and subsequent
     * elements are CREATE INDEX statements.
     *
     * @param  TableDefinition  $blueprint
     * @return array  Array of SQL statements
     */
    public function compileCreate(TableDefinition $blueprint): array
    {
        $table = $this->wrapTable($blueprint->getTable());
        $statements = [];
        $parts = [];

        // Columns
        foreach ($blueprint->getColumns() as $column) {
            $parts[] = $this->compileColumn($column);
        }

        // Composite primary key
        $primaryKeys = $blueprint->getPrimaryKeys();
        if (!empty($primaryKeys)) {
            $parts[] = 'PRIMARY KEY (' . $this->columnize($primaryKeys) . ')';
        }

        // Foreign keys
        foreach ($blueprint->getForeignKeys() as $fk) {
            $parts[] = $this->compileForeignKey($fk, $blueprint->getTable());
        }

        // Build CREATE TABLE statement
        $sql = "CREATE TABLE {$table} (\n  " . implode(",\n  ", $parts) . "\n)";
        $statements[] = $sql;

        // PostgreSQL requires separate CREATE INDEX statements
        // Unique indexes
        foreach ($blueprint->getUniqueIndexes() as $name => $columns) {
            $columnList = $this->columnize($columns);
            $statements[] = "CREATE UNIQUE INDEX {$this->wrap($name)} ON {$table} ({$columnList})";
        }

        // Regular indexes
        foreach ($blueprint->getIndexes() as $name => $columns) {
            $columnList = $this->columnize($columns);
            $statements[] = "CREATE INDEX {$this->wrap($name)} ON {$table} ({$columnList})";
        }

        // Fulltext indexes (PostgreSQL uses GIN with to_tsvector)
        foreach ($blueprint->getFulltextIndexes() as $name => $columns) {
            $quotedCols = array_map([$this, 'wrap'], $columns);
            $tsvectorExpr = implode(" || ' ' || ", array_map(function ($col) {
                return "COALESCE({$col}, '')";
            }, $quotedCols));
            $statements[] = "CREATE INDEX {$this->wrap($name)}"
                . " ON {$table} USING GIN"
                . " (to_tsvector('english', {$tsvectorExpr}))";
        }

        return $statements;
    }

    /**
     * Compile an inline index definition for CREATE TABLE
     *
     * PostgreSQL does not support inline indexes - returns null.
     *
     * @param  string  $name     Index name
     * @param  array   $columns  Column names
     * @return string|null
     */
    public function compileInlineIndex(string $name, array $columns): ?string
    {
        return null;
    }

    /**
     * Compile an inline unique index definition for CREATE TABLE
     *
     * PostgreSQL does not support inline unique indexes - returns null.
     *
     * @param  string  $name     Index name
     * @param  array   $columns  Column names
     * @return string|null
     */
    public function compileInlineUniqueIndex(string $name, array $columns): ?string
    {
        return null;
    }

    /**
     * Compile an inline fulltext index definition for CREATE TABLE
     *
     * PostgreSQL does not support inline fulltext indexes - returns null.
     *
     * @param  string  $name     Index name
     * @param  array   $columns  Column names
     * @return string|null
     */
    public function compileInlineFulltextIndex(string $name, array $columns): ?string
    {
        return null;
    }

    /**
     * Compile an ALTER TABLE statement to add columns
     *
     * @param  TableDefinition  $blueprint
     * @return array
     */
    public function compileAlterAdd(TableDefinition $blueprint): array
    {
        $statements = [];
        $table = $this->wrapTable($blueprint->getTable());

        foreach ($blueprint->getColumns() as $column) {
            $statements[] = "ALTER TABLE {$table} ADD COLUMN " . $this->compileColumn($column);
        }

        return $statements;
    }

    /**
     * Compile an ALTER TABLE statement to modify columns
     *
     * PostgreSQL uses separate ALTER COLUMN statements for each modification
     *
     * @param  TableDefinition  $blueprint
     * @return array
     */
    public function compileAlterModify(TableDefinition $blueprint): array
    {
        $statements = [];
        $table = $this->wrapTable($blueprint->getTable());

        foreach ($blueprint->getModifyColumns() as $column) {
            $colName = $this->wrap($column->getName());
            $colType = $this->getColumnType($column);

            // Change data type
            $statements[] = "ALTER TABLE {$table} ALTER COLUMN {$colName} TYPE {$colType}";

            // Change nullability
            if (!$column->isNullable()) {
                $statements[] = "ALTER TABLE {$table} ALTER COLUMN {$colName} SET NOT NULL";
            } else {
                $statements[] = "ALTER TABLE {$table} ALTER COLUMN {$colName} DROP NOT NULL";
            }

            // Change default
            if ($column->hasDefault()) {
                $default = $this->getDefaultValue($column->getDefault());
                $statements[] = "ALTER TABLE {$table} ALTER COLUMN {$colName} SET DEFAULT {$default}";
            }
        }

        return $statements;
    }

    /**
     * Compile a foreign key constraint
     *
     * @param  \Hubzero\Database\Schema\ForeignKeyDefinition  $fk
     * @param  string  $tableName
     * @return string
     */
    protected function compileForeignKey($fk, string $tableName): string
    {
        $name = $tableName . '_' . $fk->getColumn() . '_fkey';

        $sql = "CONSTRAINT " . $this->wrap($name) . " FOREIGN KEY (" . $this->wrap($fk->getColumn()) . ") ";
        $sql .= "REFERENCES " . $this->wrap($fk->getReferencedTable());
        $sql .= " (" . $this->wrap($fk->getReferencedColumn()) . ")";
        $sql .= " ON DELETE " . $fk->getOnDelete();
        $sql .= " ON UPDATE " . $fk->getOnUpdate();

        return $sql;
    }

    /**
     * Get the SQL for a column definition
     *
     * Override to handle PostgreSQL SERIAL types for auto-increment
     *
     * @param  Column  $column
     * @return string
     */
    protected function compileColumn(Column $column): string
    {
        // For auto-increment columns, use SERIAL types
        if ($column->isAutoIncrement()) {
            return $this->compileSerialColumn($column);
        }

        $sql = $this->wrap($column->getName()) . ' ' . $this->getColumnType($column);

        // Nullable
        if (!$column->isNullable()) {
            $sql .= ' NOT NULL';
        }

        // Default value
        if ($column->hasDefault()) {
            $sql .= ' DEFAULT ' . $this->getDefaultValue($column->getDefault());
        }

        // Primary key (inline, for single-column PKs)
        if ($column->isPrimaryKey()) {
            $sql .= ' PRIMARY KEY';
        }

        // Unique constraint
        if ($column->isUnique()) {
            $sql .= ' UNIQUE';
        }

        return $sql;
    }


    /**
     * Compile a SERIAL (auto-increment) column
     *
     * @param  Column  $column
     * @return string
     */
    protected function compileSerialColumn(Column $column): string
    {
        $type = $column->getType();
        $serialType = $this->serialMap[$type] ?? 'SERIAL';

        $sql = $this->wrap($column->getName()) . ' ' . $serialType;

        // SERIAL columns are implicitly NOT NULL and PRIMARY KEY
        $sql .= ' PRIMARY KEY';

        return $sql;
    }

    /**
     * Get the column type SQL
     *
     * @param  Column  $column
     * @return string
     */
    protected function getColumnType(Column $column): string
    {
        $type = $column->getType();
        $params = $column->getParameters();

        $pgsqlType = $this->typeMap[$type] ?? 'VARCHAR(255)';

        // Add length/precision for types that need it
        switch ($type) {
            case 'string':
            case 'char':
                $length = $params['length'] ?? 255;
                return "{$pgsqlType}({$length})";

            case 'decimal':
                $precision = $params['precision'] ?? 10;
                $scale = $params['scale'] ?? 2;
                return "{$pgsqlType}({$precision},{$scale})";

            default:
                return $pgsqlType;
        }
    }

    /**
     * Get the default value SQL
     *
     * @param  mixed  $value
     * @return string
     */
    protected function getDefaultValue($value): string
    {
        if (is_null($value)) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return $this->driver->quote($value);
    }

    /**
     * Wrap an identifier
     *
     * PostgreSQL uses double quotes for identifiers
     *
     * @param  string  $value
     * @return string
     */
    protected function wrap(string $value): string
    {
        // Use double quotes for PostgreSQL (SQL standard)
        return '"' . str_replace('"', '""', $value) . '"';
    }

    /**
     * Compile CREATE INDEX statements
     *
     * Override to support PostgreSQL-specific index types
     *
     * @param  TableDefinition  $blueprint
     * @return array  Array of SQL statements
     */
    public function compileIndexes(TableDefinition $blueprint): array
    {
        $statements = [];
        $table = $this->wrapTable($blueprint->getTable());

        foreach ($blueprint->getIndexes() as $index) {
            $columns = $this->columnize($index['columns']);
            $name = $this->wrap($index['name']);

            switch ($index['type']) {
                case 'unique':
                    $statements[] = "CREATE UNIQUE INDEX {$name} ON {$table} ({$columns})";
                    break;

                case 'fulltext':
                    // PostgreSQL uses GIN index with to_tsvector for full-text search
                    // This requires columns to be converted to tsvector
                    // Language can be configured: 'english', 'simple', 'spanish', etc.
                    $language = $index['language'] ?? 'english';
                    $tsvectorCols = implode(" || ' ' || ", array_map(function ($col) {
                        return "COALESCE(" . $this->wrap($col) . ", '')";
                    }, $index['columns']));
                    $statements[] = "CREATE INDEX {$name}"
                        . " ON {$table} USING GIN"
                        . " (to_tsvector('{$language}',"
                        . " {$tsvectorCols}))";
                    break;

                case 'spatial':
                    // PostgreSQL uses GiST index for spatial data (with PostGIS)
                    $statements[] = "CREATE INDEX {$name} ON {$table} USING GIST ({$columns})";
                    break;

                default:
                    // B-tree is default for PostgreSQL
                    $statements[] = "CREATE INDEX {$name} ON {$table} ({$columns})";
                    break;
            }
        }

        return $statements;
    }

    /**
     * Compile ALTER TABLE operations into SQL statements for PostgreSQL
     *
     * @param   AlterTableBuilder  $builder  The table builder
     * @return  array  Array of SQL statements to execute
     */
    public function compileAlterTable(AlterTableBuilder $builder): array
    {
        $table = $this->driver->replacePrefix($builder->getTable());
        $quotedTable = $this->wrap($table);
        $statements = [];


        // ADD COLUMN
        foreach ($builder->getAddColumns() as $name => $definition) {
            $colDef = $this->driver->buildAlterColumnDefinition($name, $definition);
            $statements[] = "ALTER TABLE $quotedTable ADD COLUMN $colDef";
        }

        // DROP COLUMN
        foreach ($builder->getDropColumns() as $name) {
            $statements[] = "ALTER TABLE $quotedTable DROP COLUMN " . $this->wrap($name);
        }

        // RENAME COLUMN
        foreach ($builder->getRenameColumns() as $from => $info) {
            $to = $info['newName'];
            $statements[] = "ALTER TABLE $quotedTable RENAME COLUMN " . $this->wrap($from) . " TO " . $this->wrap($to);
        }

        // DROP INDEX
        foreach ($builder->getDropIndexes() as $indexName) {
            // PostgreSQL drops indexes directly, not via ALTER TABLE
            $statements[] = "DROP INDEX IF EXISTS " . $this->wrap($indexName);
        }

        // ADD INDEX
        foreach ($builder->getAddIndexes() as $name => $info) {
             $columns = array_map([$this, 'wrap'], $info['columns']);
             $columnList = implode(', ', $columns);
             $unique = $info['unique'] ? 'UNIQUE ' : '';
             $statements[] = "CREATE {$unique}INDEX " . $this->wrap($name) . " ON $quotedTable ($columnList)";
        }

        // ADD FULLTEXT INDEX (GIN on to_tsvector)
        foreach ($builder->getAddFulltextIndexes() as $name => $columns) {
            $quotedCols = array_map([$this, 'wrap'], $columns);
            $colList = implode(', ', $quotedCols);
            $statements[] = "CREATE INDEX " . $this->wrap($name)
                . " ON $quotedTable USING GIN (to_tsvector('english', $colList))";
        }

        // DROP PRIMARY KEY
        if ($builder->getDropPrimaryKeyFlag()) {
             // In PostgreSQL, dropping PK requires dropping the constraint.
             // Usually named table_pkey.
             $constraintName = $table . '_pkey';
             $statements[] = "ALTER TABLE $quotedTable DROP CONSTRAINT IF EXISTS " . $this->wrap($constraintName);
        }

        // ADD PRIMARY KEY
        if ($builder->getAddPrimaryKey()) {
             $columns = array_map([$this, 'wrap'], $builder->getAddPrimaryKey());
             $columnList = implode(', ', $columns);
             // We can optionally name it, or let PG name it.
             // To match drop behavior, maybe we should name it?
             // "ADD PRIMARY KEY (cols)" uses default name.
             $statements[] = "ALTER TABLE $quotedTable ADD PRIMARY KEY ($columnList)";
        }

        return $statements;
    }
}
