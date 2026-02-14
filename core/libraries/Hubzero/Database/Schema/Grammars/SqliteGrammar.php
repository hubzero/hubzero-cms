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
 * SQLite-specific DDL grammar
 *
 * SQLite uses type affinity, so most types map to one of:
 * - INTEGER
 * - TEXT
 * - REAL
 * - BLOB
 */
class SqliteGrammar extends Grammar
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
     * Type mappings from abstract types to SQLite types
     *
     * SQLite uses type affinity, so most types map to INTEGER, TEXT, REAL, or BLOB.
     *
     * @var array
     */
    protected $typeMap = [
        // Integer types (all map to INTEGER in SQLite)
        'tinyInteger' => 'INTEGER',
        'smallInteger' => 'INTEGER',
        'mediumInteger' => 'INTEGER',
        'integer' => 'INTEGER',
        'bigInteger' => 'INTEGER',
        'boolean' => 'INTEGER',

        // String types (all map to TEXT in SQLite)
        'string' => 'TEXT',
        'char' => 'TEXT',
        'tinyText' => 'TEXT',
        'text' => 'TEXT',
        'mediumText' => 'TEXT',
        'longText' => 'TEXT',

        // Numeric types
        'float' => 'REAL',
        'double' => 'REAL',
        'decimal' => 'REAL',

        // Date/time types (stored as TEXT in SQLite)
        'date' => 'TEXT',
        'time' => 'TEXT',
        'datetime' => 'TEXT',
        'timestamp' => 'TEXT',
        'timestampTz' => 'TEXT',
        'year' => 'INTEGER',

        // Binary types
        'binary' => 'BLOB',

        // Special types (stored as TEXT in SQLite)
        'json' => 'TEXT',
        'uuid' => 'TEXT',
        'ulid' => 'TEXT',
        'ipAddress' => 'TEXT',
        'macAddress' => 'TEXT',
    ];

    /**
     * Compile CREATE TABLE statement(s)
     *
     * SQLite requires separate CREATE INDEX statements for all indexes.
     * Unique constraints can be inline, but regular indexes cannot.
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
        // (SQLite can only have one PRIMARY KEY definition, so composite keys must be declared separately)
        $primaryKeys = $blueprint->getPrimaryKeys();
        if (!empty($primaryKeys)) {
            $parts[] = 'PRIMARY KEY (' . $this->columnize($primaryKeys) . ')';
        }

        // Unique indexes (can be inline in SQLite)
        foreach ($blueprint->getUniqueIndexes() as $name => $columns) {
            $indexSql = $this->compileInlineUniqueIndex($name, $columns);
            if ($indexSql !== null) {
                $parts[] = $indexSql;
            }
        }

        // Foreign keys
        foreach ($blueprint->getForeignKeys() as $fk) {
            $parts[] = $this->compileForeignKey($fk);
        }

        // Build CREATE TABLE statement
        $ifNotExists = $blueprint->getIfNotExists() ? 'IF NOT EXISTS ' : '';
        $sql = "CREATE TABLE {$ifNotExists}{$table} (\n  " . implode(",\n  ", $parts) . "\n)";
        $statements[] = $sql;

        // Regular indexes (must be separate in SQLite)
        foreach ($blueprint->getIndexes() as $name => $columns) {
            $columnList = $this->columnize($columns);
            $statements[] = "CREATE INDEX {$this->wrap($name)} ON {$table} ({$columnList})";
        }

        // Fulltext indexes (SQLite FTS is different, but we'll create regular indexes)
        foreach ($blueprint->getFulltextIndexes() as $name => $columns) {
            $columnList = $this->columnize($columns);
            $statements[] = "CREATE INDEX {$this->wrap($name)} ON {$table} ({$columnList})";
        }

        return $statements;
    }

    /**
     * Compile an inline index definition for CREATE TABLE
     *
     * SQLite does not support inline regular indexes - returns null.
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
     * SQLite supports inline UNIQUE constraints.
     *
     * @param  string  $name     Index name
     * @param  array   $columns  Column names
     * @return string|null
     */
    public function compileInlineUniqueIndex(string $name, array $columns): ?string
    {
        $columnList = $this->columnize($columns);
        return "UNIQUE ({$columnList})";
    }

    /**
     * Compile an inline fulltext index definition for CREATE TABLE
     *
     * SQLite does not support inline fulltext indexes - returns null.
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
     * Note: SQLite has limited ALTER TABLE support. It can only ADD columns,
     * not modify or drop them (without recreating the table).
     *
     * @param  TableDefinition  $blueprint
     * @return array
     */
    public function compileAlterAdd(TableDefinition $blueprint): array
    {
        $statements = [];
        $table = $this->wrapTable($blueprint->getTable());

        foreach ($blueprint->getColumns() as $column) {
            // SQLite ALTER TABLE ADD COLUMN has restrictions:
            // - Cannot add PRIMARY KEY or UNIQUE constraints
            // - Column must have a default if NOT NULL
            $sql = "ALTER TABLE {$table} ADD COLUMN " . $this->compileColumnForAlter($column);
            $statements[] = $sql;
        }

        return $statements;
    }

    /**
     * Compile column for ALTER TABLE ADD (with SQLite restrictions)
     *
     * @param  Column  $column
     * @return string
     */
    protected function compileColumnForAlter(Column $column): string
    {
        $sql = $this->wrap($column->getName()) . ' ' . $this->getColumnType($column);

        // SQLite ALTER TABLE ADD COLUMN restrictions:
        // - NOT NULL requires a DEFAULT
        if (!$column->isNullable() && !$column->isAutoIncrement()) {
            if ($column->hasDefault()) {
                $sql .= ' NOT NULL DEFAULT ' . $this->getDefaultValue($column->getDefault());
            } else {
                // Must have a default for NOT NULL in ALTER TABLE
                $sql .= ' NOT NULL DEFAULT ' . $this->getDefaultForType($column->getType());
            }
        } elseif ($column->hasDefault()) {
            $sql .= ' DEFAULT ' . $this->getDefaultValue($column->getDefault());
        }

        return $sql;
    }


    /**
     * Get a safe default value for a type (for ALTER TABLE NOT NULL columns)
     *
     * @param  string  $type
     * @return string
     */
    protected function getDefaultForType(string $type): string
    {
        switch ($type) {
            case 'tinyInteger':
            case 'smallInteger':
            case 'integer':
            case 'bigInteger':
            case 'boolean':
                return '0';

            case 'float':
            case 'double':
            case 'decimal':
                return '0.0';

            default:
                return "''";
        }
    }

    /**
     * Compile an ALTER TABLE statement to modify columns
     *
     * SQLite doesn't support MODIFY COLUMN directly. Modifying column definitions
     * requires recreating the table with the new schema. This method provides
     * guidance for manual table recreation.
     *
     * For automatic table recreation, use the AlterTableBuilder which handles
     * the complex process of:
     * 1. Creating a temporary table with new schema
     * 2. Copying data
     * 3. Dropping the original table
     * 4. Renaming the temporary table
     *
     * @param  TableDefinition  $blueprint
     * @return array
     * @throws \RuntimeException When column modification is attempted
     */
    public function compileAlterModify(TableDefinition $blueprint): array
    {
        $columns = $blueprint->getModifyColumns();

        if (!empty($columns)) {
            // SQLite doesn't support MODIFY COLUMN - table must be recreated
            // Return empty array to indicate no direct statements possible
            // The higher-level AlterTableBuilder handles this via table recreation
            return [];
        }

        return [];
    }

    /**
     * Compile CREATE INDEX statements
     *
     * SQLite supports basic and unique indexes, but not FULLTEXT or SPATIAL.
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
                    // SQLite doesn't support FULLTEXT indexes
                    // Skip with a comment or create FTS virtual table separately
                    continue 2;

                case 'spatial':
                    // SQLite doesn't support SPATIAL indexes natively
                    // R*Tree extension would be needed
                    continue 2;

                default:
                    $statements[] = "CREATE INDEX {$name} ON {$table} ({$columns})";
                    break;
            }
        }

        return $statements;
    }

    /**
     * Compile a foreign key constraint
     *
     * @param  \Hubzero\Database\Schema\ForeignKeyDefinition  $fk
     * @return string
     */
    protected function compileForeignKey($fk): string
    {
        $sql = "FOREIGN KEY (" . $this->wrap($fk->getColumn()) . ") ";
        $sql .= "REFERENCES " . $this->wrap($fk->getReferencedTable());
        $sql .= " (" . $this->wrap($fk->getReferencedColumn()) . ")";
        $sql .= " ON DELETE " . $fk->getOnDelete();
        $sql .= " ON UPDATE " . $fk->getOnUpdate();

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

        return $this->typeMap[$type] ?? 'TEXT';
    }

    /**
     * Get the auto-increment modifier
     *
     * SQLite auto-increment is implicit for INTEGER PRIMARY KEY
     *
     * @param  Column  $column
     * @return string
     */
    protected function getAutoIncrement(Column $column): string
    {
        if ($column->isAutoIncrement()) {
            // SQLite: INTEGER PRIMARY KEY is auto-incrementing by default
            // AUTOINCREMENT keyword is optional and slightly different behavior
            return ' PRIMARY KEY AUTOINCREMENT';
        }
        return '';
    }

    /**
     * Wrap an identifier
     *
     * SQLite uses double quotes or backticks
     *
     * @param  string  $value
     * @return string
     */
    protected function wrap(string $value): string
    {
        // Use double quotes for SQLite (more standard)
        return '"' . str_replace('"', '""', $value) . '"';
    }

    /**
     * Compile ALTER TABLE operations into SQL statements for SQLite
     *
     * SQLite has very limited ALTER TABLE support. It only supports:
     * - RENAME TABLE
     * - ADD COLUMN
     * - RENAME COLUMN (SQLite 3.25+)
     *
     * For other operations (DROP COLUMN, MODIFY COLUMN, DROP PRIMARY KEY, etc.),
     * we need to rebuild the table by creating a new one, copying data, and swapping.
     *
     * @param   AlterTableBuilder  $builder  The table builder
     * @return  array  Array of SQL statements to execute
     */
    public function compileAlterTable(AlterTableBuilder $builder): array
    {
        $table = $this->driver->replacePrefix($builder->getTable());
        $statements = [];

        // Check if we need a table rebuild for complex operations
        $needsRebuild = !empty($builder->getDropColumns()) ||
                        !empty($builder->getModifyColumns()) ||
                        !empty($builder->getRenameColumns()) ||
                        $builder->getDropPrimaryKeyFlag() ||
                        $builder->getAddPrimaryKey() !== null;

        // Simple operations that SQLite supports directly
        if (!$needsRebuild) {
            foreach ($builder->getAddColumns() as $name => $definition) {
                $colDef = $this->driver->buildAlterColumnDefinition($name, $definition);
                $statements[] = "ALTER TABLE " . $this->wrap($table) . " ADD COLUMN $colDef";
            }
        }


        // Drop indexes - SQLite supports this
        foreach ($builder->getDropIndexes() as $indexName) {
            $statements[] = "DROP INDEX IF EXISTS " . $this->wrap($indexName);
        }

        // Add indexes
        if (!$needsRebuild) {
            foreach ($builder->getAddIndexes() as $name => $info) {
                $columns = array_map([$this, 'wrap'], $info['columns']);
                $columnList = implode(', ', $columns);
                $unique = $info['unique'] ? 'UNIQUE ' : '';
                $statements[] = "CREATE {$unique}INDEX IF NOT EXISTS "
                    . $this->wrap($name) . " ON "
                    . $this->wrap($table) . " ($columnList)";
            }

            // Add fulltext indexes as regular indexes (SQLite doesn't support FULLTEXT natively)
            foreach ($builder->getAddFulltextIndexes() as $name => $columns) {
                $quotedColumns = array_map([$this, 'wrap'], $columns);
                $columnList = implode(', ', $quotedColumns);
                $statements[] = "CREATE INDEX IF NOT EXISTS "
                    . $this->wrap($name) . " ON "
                    . $this->wrap($table) . " ($columnList)";
            }
        }

        // For operations SQLite doesn't support natively, rebuild the table
        if ($needsRebuild) {
            $rebuildStatements = $this->buildTableRebuild($table, $builder);
            $statements = array_merge($statements, $rebuildStatements);
        }

        return $statements;
    }

    /**
     * Convert TableInfo to PRAGMA table_info structure
     *
     * Converts ColumnInfo objects from TableInfo into the same structure
     * that PRAGMA table_info returns, for use in table rebuild operations.
     *
     * @param   \Hubzero\Database\Schema\TableInfo  $tableInfo  Table information
     * @return  array  Array of objects with PRAGMA table_info structure
     */
    protected function convertTableInfoToColumnStructure(\Hubzero\Database\Schema\TableInfo $tableInfo): array
    {
        $columns = [];
        $pkIndex = $tableInfo->getPrimaryKeyIndex();
        $pkColumns = $pkIndex ? $pkIndex->getColumns() : [];
        $cid = 0;

        foreach ($tableInfo->getColumns() as $columnInfo) {
            $obj = new \stdClass();
            $obj->cid = $cid++;
            $obj->name = $columnInfo->getName();
            $obj->type = strtoupper($columnInfo->getFullType());
            $obj->notnull = !$columnInfo->isNullable() ? 1 : 0;
            $obj->dflt_value = $columnInfo->getDefault();

            // Check if this column is part of the primary key
            $pkIndex = array_search($columnInfo->getName(), $pkColumns);
            $obj->pk = $pkIndex !== false ? ($pkIndex + 1) : 0;

            $columns[] = $obj;
        }

        return $columns;
    }

    /**
     * Rebuild a SQLite table to apply unsupported schema changes
     *
     * @param   string  $table    Table name (already prefixed)
     * @param   AlterTableBuilder  $builder  The table builder
     * @return  array  Array of SQL statements
     */
    protected function buildTableRebuild(string $table, AlterTableBuilder $builder): array
    {
        $statements = [];
        $tempTable = $table . '_rebuild_' . time();

        // Get current table structure from TableInfo if available, otherwise query database
        $sourceTableInfo = $builder->getSourceTableInfo();
        if ($sourceTableInfo !== null) {
            // Use TableInfo from schema comparison
            $currentColumns = $this->convertTableInfoToColumnStructure($sourceTableInfo);
        } else {
            // Query actual database table via Driver
            $currentColumns = $this->driver->getTableColumnInfo($table);
        }

        if (empty($currentColumns)) {
            return $statements;
        }

        // Get current indexes from TableInfo if available, otherwise query database
        $indexesToRecreate = [];
        if ($sourceTableInfo !== null) {
            // Use indexes from TableInfo
            foreach ($sourceTableInfo->getIndexes() as $indexInfo) {
                if ($indexInfo->isPrimary()) {
                    continue; // Skip primary key indexes
                }

                $columnNames = [];
                foreach ($indexInfo->getColumns() as $colName) {
                    if (isset($builder->getRenameColumns()[$colName])) {
                        $colName = $builder->getRenameColumns()[$colName]['newName'];
                    }
                    if (!in_array($colName, $builder->getDropColumns())) {
                        $columnNames[] = $colName;
                    }
                }

                if (!empty($columnNames)) {
                    $indexesToRecreate[] = [
                        'name' => $indexInfo->getName(),
                        'unique' => $indexInfo->isUnique(),
                        'columns' => $columnNames,
                    ];
                }
            }
        } else {
            // Query actual database table via Driver
            $currentIndexes = $this->driver->getTableIndexList($table);

            foreach ($currentIndexes as $index) {
                if (strpos($index->name, 'sqlite_autoindex_') === 0) {
                    continue;
                }

                $indexColumns = $this->driver->getIndexColumnInfo($index->name);

                $columnNames = [];
                foreach ($indexColumns as $col) {
                    $colName = $col->name;
                    if (isset($builder->getRenameColumns()[$colName])) {
                        $colName = $builder->getRenameColumns()[$colName]['newName'];
                    }
                    if (!in_array($col->name, $builder->getDropColumns())) {
                        $columnNames[] = $colName;
                    }
                }

                if (!empty($columnNames)) {
                    $indexesToRecreate[] = [
                        'name' => $index->name,
                        'unique' => (bool) $index->unique,
                        'columns' => $columnNames,
                    ];
                }
            }
        }

        // Determine primary key columns
        $pkColumns = [];
        if ($builder->getAddPrimaryKey() !== null) {
            $pkColumns = $builder->getAddPrimaryKey();
        } elseif (!$builder->getDropPrimaryKeyFlag()) {
            foreach ($currentColumns as $col) {
                if ($col->pk > 0 && !in_array($col->name, $builder->getDropColumns())) {
                    $colName = isset($builder->getRenameColumns()[$col->name])
                        ? $builder->getRenameColumns()[$col->name]['newName']
                        : $col->name;
                    $pkColumns[$col->pk] = $colName;
                }
            }
            ksort($pkColumns);
            $pkColumns = array_values($pkColumns);
        }

        // Build new column definitions
        $newColumnDefs = [];
        $selectColumns = [];
        $targetColumns = [];

        foreach ($currentColumns as $col) {
            if (in_array($col->name, $builder->getDropColumns())) {
                continue;
            }

            $colName = $col->name;

            if (isset($builder->getRenameColumns()[$col->name])) {
                $colName = $builder->getRenameColumns()[$col->name]['newName'];
            }

            if (isset($builder->getModifyColumns()[$col->name])) {
                $def = $builder->getModifyColumns()[$col->name];
                $colDef = $this->driver->buildAlterColumnDefinition($colName, $def);
            } else {
                $colDef = $this->wrap($colName) . " {$col->type}";
                if ($col->notnull && $col->dflt_value === null) {
                    $colDef .= ' NOT NULL';
                }
                if ($col->dflt_value !== null) {
                    $colDef .= ' DEFAULT ' . $col->dflt_value;
                }
            }

            $newColumnDefs[] = $colDef;
            $selectColumns[] = $this->wrap($col->name);
            $targetColumns[] = $this->wrap($colName);
        }

        foreach ($builder->getAddColumns() as $name => $definition) {
            $newColumnDefs[] = $this->driver->buildAlterColumnDefinition($name, $definition);
        }

        if (!empty($pkColumns)) {
            $pkList = implode(', ', array_map([$this, 'wrap'], $pkColumns));
            $newColumnDefs[] = "PRIMARY KEY ($pkList)";
        }

        // Execute rebuild
        $statements[] = "CREATE TABLE " . $this->wrap($tempTable) . " (" . implode(', ', $newColumnDefs) . ")";

        if (!empty($selectColumns)) {
            $statements[] = "INSERT INTO " . $this->wrap($tempTable)
                . " (" . implode(', ', $targetColumns) . ")"
                . " SELECT " . implode(', ', $selectColumns)
                . " FROM " . $this->wrap($table);
        }

        $statements[] = "DROP TABLE " . $this->wrap($table);
        $statements[] = "ALTER TABLE " . $this->wrap($tempTable) . " RENAME TO " . $this->wrap($table);

        foreach ($indexesToRecreate as $index) {
            $columns = array_map([$this, 'wrap'], $index['columns']);
            $columnList = implode(', ', $columns);
            $unique = $index['unique'] ? 'UNIQUE ' : '';
            $statements[] = "CREATE {$unique}INDEX IF NOT EXISTS "
                . $this->wrap($index['name']) . " ON "
                . $this->wrap($table) . " ($columnList)";
        }

        foreach ($builder->getAddIndexes() as $name => $info) {
            $columns = array_map([$this, 'wrap'], $info['columns']);
            $columnList = implode(', ', $columns);
            $unique = $info['unique'] ? 'UNIQUE ' : '';
            $statements[] = "CREATE {$unique}INDEX IF NOT EXISTS "
                . $this->wrap($name) . " ON "
                . $this->wrap($table) . " ($columnList)";
        }

        return $statements;
    }
}
