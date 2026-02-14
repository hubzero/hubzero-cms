<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Base;

use Hubzero\Database\Driver;
use Hubzero\Database\Drivers\Base\BaseSqlDriver as SqlDriver;
use Hubzero\Database\Schema\AlterTableBuilder;
use Hubzero\Database\Schema\Column;
use Hubzero\Database\Schema\TableDefinition;

/**
 * Abstract grammar for generating database-specific DDL
 */
abstract class BaseSchemaGrammar
{
    /**
     * Database driver
     *
     * @var Driver
     */
    protected $driver;

    /**
     * Create a new grammar instance
     *
     * @param  Driver  $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Compile CREATE TABLE statement(s)
     *
     * Returns an array of SQL statements. The first statement is always CREATE TABLE.
     * Additional statements are for indexes that cannot be created inline (e.g.,
     * PostgreSQL requires separate CREATE INDEX statements).
     *
     * @param  TableDefinition  $blueprint
     * @return array  Array of SQL statements
     */
    abstract public function compileCreate(TableDefinition $blueprint): array;

    /**
     * Compile CREATE TABLE statements from a normalized array definition.
     *
     * Default implementation compiles statements using the shared generic path.
     * Dialects can override this for backend-specific create-table behavior.
     *
     * @param  array $definition Normalized table definition
     * @return array
     */
    public function compileCreateTableFromDefinition(array $definition): array
    {
        return $this->compileCreateTableFromDefinitionGeneric($definition);
    }

    /**
     * Shared CREATE TABLE compilation from normalized definition data.
     *
     * @param  array $definition
     * @return array
     */
    protected function compileCreateTableFromDefinitionGeneric(array $definition): array
    {
        $table = $definition['table'];
        $statements = [];
        $parts = [];
        $columns = $definition['columns'] ?? [];
        $primaryKey = $definition['primaryKey'] ?? [];
        $indexes = $definition['indexes'] ?? [];
        $uniqueIndexes = $definition['uniqueIndexes'] ?? [];
        $fulltextIndexes = $definition['fulltextIndexes'] ?? [];
        $foreignKeys = $definition['foreignKeys'] ?? [];
        $ifNotExists = !empty($definition['ifNotExists']);
        $options = $definition['options'] ?? [];
        $tableEngine = $options['engine'] ?? 'InnoDB';
        $tableCharset = $options['charset'] ?? 'utf8';
        $tableCollation = $options['collation'] ?? 'utf8_general_ci';

        foreach ($columns as $name => $columnDefinition) {
            $parts[] = $this->compileColumnFromDefinition($name, $columnDefinition);
        }

        if (!empty($primaryKey)) {
            $hasSingleAutoIncrementPK = false;
            if ($this->driver->autoIncrementIncludesPrimaryKey() && count($primaryKey) === 1) {
                $pkColumn = $primaryKey[0];
                if (isset($columns[$pkColumn]['modifiers']['autoIncrement'])) {
                    $hasSingleAutoIncrementPK = true;
                }
            }

            if (!$hasSingleAutoIncrementPK) {
                $pkColumns = implode(', ', array_map([$this->driver, 'quoteName'], $primaryKey));
                $parts[] = "PRIMARY KEY ($pkColumns)";
            }
        }

        foreach ($uniqueIndexes as $name => $indexColumns) {
            $indexDef = $this->compileInlineUniqueIndex($name, $indexColumns);
            if ($indexDef !== null) {
                $parts[] = $indexDef;
            }
        }

        foreach ($indexes as $name => $indexColumns) {
            $indexDef = $this->compileInlineIndex($name, $indexColumns);
            if ($indexDef !== null) {
                $parts[] = $indexDef;
            }
        }

        foreach ($fulltextIndexes as $name => $indexColumns) {
            $indexDef = $this->compileInlineFulltextIndex($name, $indexColumns);
            if ($indexDef !== null) {
                $parts[] = $indexDef;
            }
        }

        foreach ($foreignKeys as $fk) {
            $parts[] = $this->driver->buildForeignKeyDefinition($fk, $table);
        }

        $ifNotExistsSql = ($ifNotExists && $this->driver->supportsIfNotExists()) ? 'IF NOT EXISTS ' : '';
        $quotedTable = $this->driver->quoteName($table);
        $sql = "CREATE TABLE {$ifNotExistsSql}{$quotedTable} (\n  " . implode(",\n  ", $parts) . "\n)";

        $tableOptions = $this->driver->buildTableOptions($tableEngine, $tableCharset, $tableCollation);
        if ($tableOptions !== '') {
            $sql .= ' ' . $tableOptions;
        }

        $statements[] = $sql;

        foreach ($uniqueIndexes as $name => $indexColumns) {
            $indexDef = $this->compileInlineUniqueIndex($name, $indexColumns);
            if ($indexDef === null) {
                $quotedName = $this->driver->quoteName($name);
                $columnList = implode(', ', array_map([$this->driver, 'quoteName'], $indexColumns));
                $statements[] = "CREATE UNIQUE INDEX $quotedName ON $quotedTable ($columnList)";
            }
        }

        foreach ($indexes as $name => $indexColumns) {
            $indexDef = $this->compileInlineIndex($name, $indexColumns);
            if ($indexDef === null) {
                $quotedName = $this->driver->quoteName($name);
                $columnList = implode(', ', array_map([$this->driver, 'quoteName'], $indexColumns));
                $statements[] = "CREATE INDEX $quotedName ON $quotedTable ($columnList)";
            }
        }

        foreach ($fulltextIndexes as $name => $indexColumns) {
            $indexDef = $this->compileInlineFulltextIndex($name, $indexColumns);
            if ($indexDef === null) {
                if (method_exists($this->driver, 'buildDeferredFulltextIndexStatements')) {
                    $driverStatements = $this->driver->buildDeferredFulltextIndexStatements(
                        $table,
                        $name,
                        is_array($indexColumns) ? $indexColumns : [$indexColumns]
                    );
                    foreach ($driverStatements as $driverStatement) {
                        $statements[] = $driverStatement;
                    }
                    continue;
                }

                $quotedName = $this->driver->quoteName($name);
                $columnList = implode(', ', array_map([$this->driver, 'quoteName'], $indexColumns));
                $statements[] = "CREATE FULLTEXT INDEX $quotedName ON $quotedTable ($columnList)";
            }
        }

        foreach ($columns as $name => $columnDefinition) {
            if (!empty($columnDefinition['modifiers']['autoIncrement'])) {
                $autoIncrementStmts = $this->driver->buildAutoIncrementStatements($table, $name);
                foreach ($autoIncrementStmts as $stmt) {
                    $statements[] = $stmt;
                }
            }
        }

        return $statements;
    }

    /**
     * Compile a column definition from normalized array data.
     *
     * Dialects may override for custom behavior.
     *
     * @param  string $name
     * @param  array  $definition
     * @return string
     */
    protected function compileColumnFromDefinition(string $name, array $definition): string
    {
        return $this->compileColumnFromDefinitionGeneric($name, $definition);
    }

    /**
     * Compile an inline index definition for CREATE TABLE
     *
     * Returns the SQL fragment for an inline index (e.g., "KEY idx_name (col1, col2)")
     * or null if the database requires separate CREATE INDEX statements.
     *
     * @param  string  $name     Index name
     * @param  array   $columns  Column names
     * @return string|null  SQL fragment or null
     */
    abstract public function compileInlineIndex(string $name, array $columns): ?string;

    /**
     * Compile an inline unique index definition for CREATE TABLE
     *
     * Returns the SQL fragment for an inline unique constraint/index
     * (e.g., "UNIQUE KEY idx_name (col1, col2)") or null if the database
     * requires separate CREATE UNIQUE INDEX statements.
     *
     * @param  string  $name     Index name
     * @param  array   $columns  Column names
     * @return string|null  SQL fragment or null
     */
    abstract public function compileInlineUniqueIndex(string $name, array $columns): ?string;

    /**
     * Compile an inline fulltext index definition for CREATE TABLE
     *
     * Returns the SQL fragment for an inline fulltext index
     * (e.g., "FULLTEXT KEY idx_name (col1, col2)") or null if the database
     * requires separate CREATE FULLTEXT INDEX statements or doesn't support
     * fulltext indexes.
     *
     * @param  string  $name     Index name
     * @param  array   $columns  Column names
     * @return string|null  SQL fragment or null
     */
    abstract public function compileInlineFulltextIndex(string $name, array $columns): ?string;

    /**
     * Compile an ALTER TABLE statement to add columns
     *
     * @param  TableDefinition  $blueprint
     * @return array  Array of SQL statements
     */
    abstract public function compileAlterAdd(TableDefinition $blueprint): array;

    /**
     * Compile an ALTER TABLE statement to drop columns
     *
     * Uses DROP COLUMN syntax (SQL standard). Firebird and Informix override
     * to use DROP without the COLUMN keyword.
     *
     * @param  TableDefinition  $blueprint
     * @return array  Array of SQL statements
     */
    public function compileAlterDrop(TableDefinition $blueprint): array
    {
        $statements = [];
        $table = $this->wrapTable($blueprint->getTable());

        foreach ($blueprint->getDropColumns() as $column) {
            $statements[] = "ALTER TABLE {$table} DROP COLUMN "
                . $this->wrap($column);
        }

        return $statements;
    }

    /**
     * Compile an ALTER TABLE statement to modify columns
     *
     * Note: SQLite has limited support for column modification and may
     * return an empty array. Use AlterTableBuilder for complex SQLite operations.
     *
     * @param  TableDefinition  $blueprint
     * @return array  Array of SQL statements
     */
    abstract public function compileAlterModify(TableDefinition $blueprint): array;

    /**
     * Compile ALTER TABLE statements from an AlterTableBuilder
     *
     * Generates the full set of ALTER TABLE SQL for adding, dropping,
     * modifying, and renaming columns, indexes, foreign keys, etc.
     *
     * @param  AlterTableBuilder  $builder
     * @return array  Array of SQL statements
     */
    abstract public function compileAlterTable(
        AlterTableBuilder $builder
    ): array;

    /**
     * Compile a DROP TABLE statement
     *
     * @param  string  $table
     * @param  bool    $ifExists
     * @return string
     */
    public function compileDrop(string $table, bool $ifExists = true): string
    {
        $sql = 'DROP TABLE ';
        if ($ifExists) {
            $sql .= 'IF EXISTS ';
        }
        return $sql . $this->wrapTable($table);
    }

    /**
     * Compile a RENAME TABLE statement
     *
     * Uses SQL standard ALTER TABLE ... RENAME TO syntax.
     * Most drivers override with database-specific syntax.
     *
     * @param  string  $from  Current table name
     * @param  string  $to    New table name
     * @return string
     */
    public function compileRename(string $from, string $to): string
    {
        return "ALTER TABLE " . $this->wrap($from)
            . " RENAME TO " . $this->wrap($to);
    }

    /**
     * Compile a RENAME COLUMN statement
     *
     * Uses SQL standard ALTER TABLE ... RENAME COLUMN syntax.
     * Firebird, SQL Server, and Informix override with database-specific syntax.
     *
     * @param  string  $table  Table name
     * @param  string  $from   Current column name
     * @param  string  $to     New column name
     * @return string
     */
    public function compileRenameColumn(
        string $table,
        string $from,
        string $to
    ): string {
        return "ALTER TABLE " . $this->wrap($table)
            . " RENAME COLUMN " . $this->wrap($from)
            . " TO " . $this->wrap($to);
    }

    /**
     * Compile CREATE INDEX statements
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

            if ($index['type'] === 'unique') {
                $statements[] = "CREATE UNIQUE INDEX {$name} ON {$table} ({$columns})";
            } else {
                $statements[] = "CREATE INDEX {$name} ON {$table} ({$columns})";
            }
        }

        return $statements;
    }

    /**
     * Compile a legacy inline regular index definition.
     *
     * Kept for compatibility while moving index DDL ownership to grammar.
     *
     * @param  string  $quotedName
     * @param  string  $columnList
     * @return string|null
     */
    public function compileLegacyInlineIndexDefinition(string $quotedName, string $columnList): ?string
    {
        return "KEY $quotedName ($columnList)";
    }

    /**
     * Compile a legacy inline fulltext index definition.
     *
     * Kept for compatibility while moving index DDL ownership to grammar.
     *
     * @param  string  $quotedName
     * @param  string  $columnList
     * @return string|null
     */
    public function compileLegacyInlineFulltextIndexDefinition(string $quotedName, string $columnList): ?string
    {
        return "FULLTEXT KEY $quotedName ($columnList)";
    }

    /**
     * Compile a CREATE INDEX statement from index metadata.
     *
     * @param  string  $indexName
     * @param  string  $tableName
     * @param  array   $columns
     * @param  bool    $unique
     * @return string
     */
    public function compileCreateIndexStatement(
        string $indexName,
        string $tableName,
        array $columns,
        bool $unique = false
    ): string {
        $uniqueKeyword = $unique ? 'UNIQUE ' : '';
        $wrappedIndex = $this->wrap($indexName);
        $wrappedTable = $this->wrapTable($tableName);
        $columnList = $this->columnize($columns);

        return "CREATE {$uniqueKeyword}INDEX {$wrappedIndex} ON {$wrappedTable} ({$columnList})";
    }

    /**
     * Get the SQL for a column definition
     *
     * @param  Column  $column
     * @return string
     */
    protected function compileColumn(Column $column): string
    {
        $sql = $this->wrap($column->getName()) . ' ' . $this->getColumnType($column);

        // Unsigned (MySQL only, handled in subclass)
        $sql .= $this->getUnsigned($column);

        // Detect zero-date default — translate to DEFAULT NULL
        $isZeroDate = $column->hasDefault()
            && \Hubzero\Database\Drivers\Base\BaseSqlDriver::isZeroDate($column->getDefault());

        // Nullable — force nullable when translating zero-date to NULL
        $nullable = $column->isNullable() || $isZeroDate;
        if (!$nullable && !$column->isAutoIncrement()) {
            $sql .= ' NOT NULL';
        } elseif ($nullable) {
            $sql .= ' NULL';
        }

        // Default value — zero-date becomes DEFAULT NULL
        if ($column->hasDefault()) {
            if ($isZeroDate) {
                $sql .= ' DEFAULT NULL';
            } else {
                $sql .= ' DEFAULT ' . $this->getDefaultValue($column->getDefault());
            }
        }

        // Auto increment (handled in subclass)
        $sql .= $this->getAutoIncrement($column);

        // Primary key (inline, for single-column PKs)
        if ($column->isPrimaryKey() && !$column->isAutoIncrement()) {
            $sql .= ' PRIMARY KEY';
        }

        // Unique constraint
        if ($column->isUnique()) {
            $sql .= ' UNIQUE';
        }

        return $sql;
    }

    /**
     * Compile a column definition from normalized array data.
     *
     * This is shared by create-definition delegation paths across grammars.
     *
     * @param  string $name
     * @param  array  $definition
     * @return string
     */
    protected function compileColumnFromDefinitionGeneric(string $name, array $definition): string
    {
        $type = $definition['type'];
        $modifiers = $definition['modifiers'] ?? [];

        if (method_exists($this->driver, 'normalizeColumnType')) {
            $type = $this->driver->normalizeColumnType($type, $modifiers);
        } else {
            $type = $this->driver->mapColumnType($type);
        }

        if ($this->driver->supportsUnsigned() && !empty($modifiers['unsigned'])) {
            if (stripos($type, 'UNSIGNED') === false) {
                $type .= ' UNSIGNED';
            }
        }

        $quotedName = $this->driver->quoteName($name);

        if (!empty($modifiers['autoIncrement'])) {
            return $this->driver->buildAutoIncrementColumn($quotedName, $type);
        }

        $parts = [$quotedName, $type];
        $hasZeroDateDefault = array_key_exists('default', $modifiers)
            && SqlDriver::isZeroDate($modifiers['default']);

        if (array_key_exists('default', $modifiers)) {
            if ($hasZeroDateDefault) {
                $parts[] = 'DEFAULT NULL';
            } else {
                $default = $modifiers['default'];
                if ($default === null) {
                    $parts[] = 'DEFAULT NULL';
                } elseif (is_bool($default)) {
                    $parts[] = 'DEFAULT ' . $this->driver->formatBooleanLiteral($default);
                } elseif (is_numeric($default)) {
                    $parts[] = 'DEFAULT ' . $default;
                } elseif ($default === 'CURRENT_TIMESTAMP') {
                    $parts[] = 'DEFAULT ' . $this->driver->currentTimestampDefault();
                } else {
                    $parts[] = "DEFAULT '" . addslashes($default) . "'";
                }
            }
        }

        if ($hasZeroDateDefault) {
            if ($this->driver->supportsExplicitNull()) {
                $parts[] = 'NULL';
            }
        } elseif (isset($modifiers['nullable']) && $modifiers['nullable']) {
            if ($this->driver->supportsExplicitNull()) {
                $parts[] = 'NULL';
            }
        } elseif (isset($modifiers['nullable']) && !$modifiers['nullable']) {
            $parts[] = 'NOT NULL';
        }

        if ($this->driver->supportsColumnComments() && isset($modifiers['comment'])) {
            $parts[] = "COMMENT '" . addslashes($modifiers['comment']) . "'";
        }

        if (isset($modifiers['onUpdate'])) {
            $parts[] = 'ON UPDATE ' . $modifiers['onUpdate'];
        }

        return implode(' ', $parts);
    }

    /**
     * Get the column type SQL
     *
     * @param  Column  $column
     * @return string
     */
    abstract protected function getColumnType(Column $column): string;

    /**
     * Get the database-specific type for an abstract type
     *
     * Returns the raw database type for the given abstract type name
     * without any modifiers (length, precision, etc.). This allows
     * Driver classes to access the type mapping without depending on
     * Column objects.
     *
     * @param  string  $abstractType  Abstract type name (e.g., 'integer', 'string')
     * @return string|null  Database type or null if not mapped
     */
    public function getTypeMapping(string $abstractType): ?string
    {
        return $this->typeMap[$abstractType] ?? null;
    }

    /**
     * Get the unsigned modifier
     *
     * @param  Column  $column
     * @return string
     */
    protected function getUnsigned(Column $column): string
    {
        return '';
    }

    /**
     * Get the auto-increment modifier
     *
     * Returns empty string by default. Drivers that use auto-increment
     * modifiers (MySQL, SQL Server, SQLite) override this method.
     *
     * @param  Column  $column
     * @return string
     */
    protected function getAutoIncrement(Column $column): string
    {
        return '';
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
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return $this->driver->quote($value);
    }

    /**
     * Wrap a table name
     *
     * @param  string  $table
     * @return string
     */
    protected function wrapTable(string $table): string
    {
        return $this->wrap($table);
    }

    /**
     * Wrap an identifier (table or column name)
     *
     * @param  string  $value
     * @return string
     */
    protected function wrap(string $value): string
    {
        return $this->driver->quoteName($value);
    }

    /**
     * Convert an array of column names to a comma-separated string
     *
     * @param  array  $columns
     * @return string
     */
    protected function columnize(array $columns): string
    {
        return implode(', ', array_map([$this, 'wrap'], $columns));
    }
}
