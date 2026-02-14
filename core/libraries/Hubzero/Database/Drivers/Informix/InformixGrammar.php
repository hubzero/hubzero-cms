<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Informix;

use Hubzero\Database\Schema\AlterTableBuilder;
use Hubzero\Database\Schema\TableDefinition;
use Hubzero\Database\Schema\Column;
use Hubzero\Database\Drivers\Base\BaseSchemaGrammar;

/**
 * Informix-specific DDL grammar
 */
class InformixGrammar extends BaseSchemaGrammar
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
     * Type mappings from abstract types to Informix types
     *
     * @var array
     */
    protected $typeMap = [
        // Integer types
        'tinyInteger' => 'SMALLINT',
        'smallInteger' => 'SMALLINT',
        'mediumInteger' => 'INTEGER',
        'integer' => 'INTEGER',
        'bigInteger' => 'INT8',
        'boolean' => 'SMALLINT',

        // String types
        'string' => 'VARCHAR',
        'char' => 'CHAR',
        'tinyText' => 'LVARCHAR(255)',
        // Text-family columns use CLOB to support larger payloads and BTS.
        'text' => 'CLOB',
        'mediumText' => 'CLOB',
        'longText' => 'CLOB',

        // Numeric types
        'float' => 'SMALLFLOAT',
        'double' => 'FLOAT',
        'decimal' => 'DECIMAL',

        // Date/time types
        // Use YEAR TO SECOND (not FRACTION) so output matches MySQL/SQLite format
        'date' => 'DATE',
        'time' => 'DATETIME HOUR TO SECOND',
        'datetime' => 'DATETIME YEAR TO SECOND',
        'timestamp' => 'DATETIME YEAR TO SECOND',
        'timestampTz' => 'DATETIME YEAR TO SECOND',
        'year' => 'SMALLINT',

        // Binary types
        'binary' => 'BYTE',

        // Special types
        // Keep JSON bounded/queryable and avoid oversized LVARCHAR definitions
        // that can exceed Informix row-size limits in mixed tables.
        'json' => 'LVARCHAR(8192)',
        'uuid' => 'CHAR(36)',
        'ulid' => 'CHAR(26)',
        'ipAddress' => 'VARCHAR(45)',
        'macAddress' => 'VARCHAR(17)',
    ];

    /**
     * Compile CREATE TABLE statement(s)
     *
     * Informix requires separate CREATE INDEX statements for all indexes.
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

        // Build CREATE TABLE statement (Informix doesn't support IF NOT EXISTS)
        $sql = "CREATE TABLE {$table} (\n  " . implode(",\n  ", $parts) . "\n)";
        $statements[] = $sql;

        // Informix requires separate CREATE INDEX statements
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

        // Fulltext indexes via Informix BTS (driver-specific DDL generation)
        foreach ($blueprint->getFulltextIndexes() as $name => $columns) {
            if (method_exists($this->driver, 'buildDeferredFulltextIndexStatements')) {
                $driverStatements = $this->driver->buildDeferredFulltextIndexStatements(
                    $blueprint->getTable(),
                    $name,
                    is_array($columns) ? $columns : [$columns]
                );
                foreach ($driverStatements as $driverStatement) {
                    $statements[] = $driverStatement;
                }
            } else {
                $columnList = $this->columnize($columns);
                $statements[] = "CREATE INDEX {$this->wrap($name)} ON {$table} ({$columnList})";
            }
        }

        return $statements;
    }

    /**
     * Compile an inline index definition for CREATE TABLE
     *
     * Informix does not support inline indexes - returns null.
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
     * Informix does not support inline unique indexes - returns null.
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
     * Informix does not support inline fulltext indexes - returns null.
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
            $statements[] = "ALTER TABLE {$table} ADD " . $this->compileColumn($column);
        }

        return $statements;
    }

    /**
     * Compile an ALTER TABLE statement to drop columns
     *
     * @param  TableDefinition  $blueprint
     * @return array
     */
    public function compileAlterDrop(TableDefinition $blueprint): array
    {
        $statements = [];
        $table = $this->wrapTable($blueprint->getTable());

        foreach ($blueprint->getDropColumns() as $column) {
            $statements[] = "ALTER TABLE {$table} DROP " . $this->wrap($column);
        }

        return $statements;
    }

    /**
     * Compile an ALTER TABLE statement to modify columns
     *
     * Informix uses ALTER TABLE ... MODIFY ...
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

            $statements[] = "ALTER TABLE {$table} MODIFY {$colName} {$colType}";

            if (!$column->isNullable()) {
                $statements[] = "ALTER TABLE {$table} MODIFY {$colName} {$colType} NOT NULL";
            }
        }

        return $statements;
    }

    /**
     * Get the SQL for a column definition
     *
     * @param  Column  $column
     * @return string
     */
    protected function compileColumn(Column $column): string
    {
        $type = $column->getType();

        // Handle auto-increment via SERIAL types
        if ($column->isAutoIncrement()) {
            $colType = ($type === 'bigInteger') ? 'SERIAL8' : 'SERIAL';
        } else {
            $colType = $this->getColumnType($column);
        }

        $sql = $this->wrap($column->getName()) . ' ' . $colType;

        // Default value (SERIAL columns cannot have defaults in Informix)
        if ($column->hasDefault() && !$column->isAutoIncrement()) {
            $sql .= ' DEFAULT ' . $this->getDefaultValue($column->getDefault());
        }

        // Nullable
        if (!$column->isNullable()) {
            $sql .= ' NOT NULL';
        }

        // Primary key (inline)
        if ($column->isPrimaryKey() && !$column->isAutoIncrement()) {
            $sql .= ' PRIMARY KEY';
        }

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

        $informixType = $this->typeMap[$type] ?? 'VARCHAR(255)';

        // Add length/precision for types that need it
        switch ($type) {
            case 'string':
            case 'char':
                $length = $params['length'] ?? 255;
                return "{$informixType}({$length})";

            case 'decimal':
                $precision = $params['precision'] ?? 10;
                $scale = $params['scale'] ?? 2;
                return "{$informixType}({$precision},{$scale})";

            default:
                return $informixType;
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
            return $value ? '1' : '0'; // SMALLINT 0/1 for cross-database consistency
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        // Informix uses CURRENT YEAR TO SECOND instead of CURRENT_TIMESTAMP
        if (is_string($value) && strtoupper($value) === 'CURRENT_TIMESTAMP') {
            return 'CURRENT YEAR TO SECOND';
        }

        return $this->driver->quote($value);
    }

    /**
     * Wrap an identifier
     *
     * Informix is usually case-insensitive unless DELIMIDENT is set
     *
     * @param  string  $value
     * @return string
     */
    protected function wrap(string $value): string
    {
        // Hubzero Informix driver usually works with lowercase
        return $this->driver->quoteName($value);
    }

    /**
     * Compile a RENAME TABLE statement
     *
     * @param  string  $from  Current table name
     * @param  string  $to    New table name
     * @return string
     */
    public function compileRename(string $from, string $to): string
    {
        return "RENAME TABLE " . $this->wrap($from) . " TO " . $this->wrap($to);
    }

    /**
     * Compile a RENAME COLUMN statement
     *
     * Informix uses RENAME COLUMN table.old TO new
     *
     * @param  string  $table   Table name
     * @param  string  $from    Current column name
     * @param  string  $to      New column name
     * @return string
     */
    public function compileRenameColumn(string $table, string $from, string $to): string
    {
        $table = $this->wrap($table);
        $from = $this->wrap($from);
        $to = $this->wrap($to);

        return "RENAME COLUMN {$table}.{$from} TO {$to}";
    }

    /**
     * Compile ALTER TABLE operations from an AlterTableBuilder
     *
     * Informix requires separate ALTER TABLE statements for each operation.
     * Unlike MySQL which can combine operations with commas.
     *
     * @param   AlterTableBuilder  $builder
     * @return  array  Array of SQL statements
     */
    public function compileAlterTable(AlterTableBuilder $builder): array
    {
        $table = $this->driver->replacePrefix($builder->getTable());
        $statements = [];

        // Informix requires separate ALTER TABLE statement for each operation
        // Unlike MySQL which can combine operations with commas

        // Drop indexes first (before dropping columns that might be indexed)
        foreach ($builder->getDropIndexes() as $indexName) {
            $statements[] = "DROP INDEX $indexName";
        }

        // Drop foreign keys
        foreach ($builder->getDropForeignKeys() as $fkName) {
            $statements[] = "ALTER TABLE $table DROP CONSTRAINT $fkName";
        }

        // Drop columns
        foreach ($builder->getDropColumns() as $columnName) {
            $statements[] = "ALTER TABLE $table DROP $columnName";
        }

        // Add columns
        foreach ($builder->getAddColumns() as $name => $definition) {
            $colDef = $this->driver->buildAlterColumnDefinition($name, $definition);
            $statements[] = "ALTER TABLE $table ADD $colDef";
        }

        // Modify columns
        foreach ($builder->getModifyColumns() as $name => $definition) {
            $colDef = $this->driver->buildAlterColumnDefinition($name, $definition);
            $statements[] = "ALTER TABLE $table MODIFY $colDef";
        }

        // Rename columns (Informix: RENAME COLUMN table.old TO new, not ALTER TABLE)
        foreach ($builder->getRenameColumns() as $oldName => $info) {
            $statements[] = "RENAME COLUMN $table.$oldName TO {$info['newName']}";
        }

        // Add indexes (CREATE INDEX is separate from ALTER TABLE)
        foreach ($builder->getAddIndexes() as $name => $info) {
            $columnList = implode(', ', $info['columns']);
            $unique = $info['unique'] ? 'UNIQUE ' : '';
            $statements[] = "CREATE {$unique}INDEX $name ON $table ($columnList)";
        }

        // Add fulltext indexes as regular indexes (Informix doesn't support FULLTEXT natively)
        foreach ($builder->getAddFulltextIndexes() as $name => $columns) {
            $columnList = implode(', ', $columns);
            $statements[] = "CREATE INDEX $name ON $table ($columnList)";
        }

        // Drop primary key (Informix: must look up constraint name)
        if ($builder->getDropPrimaryKeyFlag()) {
            $pkName = $this->driver->getPrimaryKeyConstraintName($table);
            if ($pkName) {
                $statements[] = "ALTER TABLE $table DROP CONSTRAINT " . $this->wrap($pkName);
            }
        }

        // Add primary key (Informix: constraint name at end)
        if ($builder->getAddPrimaryKey() !== null) {
            $columnList = implode(', ', $builder->getAddPrimaryKey());
            $constraintName = 'pk_' . strtolower(str_replace('"', '', $table));
            $statements[] = "ALTER TABLE $table ADD CONSTRAINT"
                . " PRIMARY KEY ($columnList)"
                . " CONSTRAINT " . $this->wrap($constraintName);
        }

        // Add foreign keys (Informix: constraint name at end)
        foreach ($builder->getAddForeignKeys() as $fk) {
            $refTable = $this->driver->replacePrefix($fk['referencedTable']);
            $fkName = !empty($fk['name']) ? $fk['name'] : "fk_{$table}_{$fk['column']}";
            $constraint = "ALTER TABLE $table ADD CONSTRAINT";
            $constraint .= " FOREIGN KEY ({$fk['column']})";
            $constraint .= " REFERENCES $refTable ({$fk['referencedColumn']})";

            $onDelete = strtoupper($fk['onDelete'] ?? 'RESTRICT');
            if ($onDelete === 'CASCADE') {
                $constraint .= ' ON DELETE CASCADE';
            }

            $constraint .= ' CONSTRAINT ' . $this->wrap($fkName);

            $statements[] = $constraint;
        }


        return $statements;
    }
}
