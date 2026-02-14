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
 * Oracle-specific DDL grammar
 */
class OciGrammar extends Grammar
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
     * Type mappings from abstract types to Oracle types
     *
     * @var array
     */
    protected $typeMap = [
        // Integer types (Oracle uses NUMBER for everything)
        'tinyInteger' => 'NUMBER(3)',
        'smallInteger' => 'NUMBER(5)',
        'mediumInteger' => 'NUMBER(7)',
        'integer' => 'NUMBER(10)',
        'bigInteger' => 'NUMBER(19)',
        'boolean' => 'NUMBER(1)',

        // String types
        'string' => 'VARCHAR2',
        'char' => 'CHAR',
        'tinyText' => 'VARCHAR2(255)',
        'text' => 'CLOB',
        'mediumText' => 'CLOB',
        'longText' => 'CLOB',

        // Numeric types
        'float' => 'FLOAT',
        'double' => 'DOUBLE PRECISION',
        'decimal' => 'NUMBER',

        // Date/time types
        'date' => 'DATE',
        'time' => 'TIMESTAMP',
        'datetime' => 'TIMESTAMP',
        'timestamp' => 'TIMESTAMP',
        'timestampTz' => 'TIMESTAMP WITH TIME ZONE',
        'year' => 'NUMBER(4)',

        // Binary types
        'binary' => 'BLOB',

        // Special types
        'json' => 'CLOB', // Oracle 12.1 doesn't have native JSON, 12.2+ has 'JSON' constraint
        'uuid' => 'CHAR(36)',
        'ulid' => 'CHAR(26)',
        'ipAddress' => 'VARCHAR2(45)',
        'macAddress' => 'VARCHAR2(17)',
    ];

    /**
     * Compile CREATE TABLE statement(s)
     *
     * Oracle requires separate CREATE INDEX statements for all indexes.
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

        // Build CREATE TABLE statement (Oracle doesn't support IF NOT EXISTS)
        $sql = "CREATE TABLE {$table} (\n  " . implode(",\n  ", $parts) . "\n)";
        $statements[] = $sql;

        // Oracle requires separate CREATE INDEX statements
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

        // Fulltext indexes (Oracle Text requires special setup, create regular indexes for now)
        foreach ($blueprint->getFulltextIndexes() as $name => $columns) {
            $columnList = $this->columnize($columns);
            $statements[] = "CREATE INDEX {$this->wrap($name)} ON {$table} ({$columnList})";
        }

        return $statements;
    }

    /**
     * Compile an inline index definition for CREATE TABLE
     *
     * Oracle does not support inline indexes - returns null.
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
     * Oracle does not support inline unique indexes - returns null.
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
     * Oracle does not support inline fulltext indexes - returns null.
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
     * Compile an ALTER TABLE statement to modify columns
     *
     * Oracle uses ALTER TABLE ... MODIFY (...)
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

            $def = "{$colName} {$colType}";

            if (!$column->isNullable()) {
                $def .= " NOT NULL";
            }

            if ($column->hasDefault()) {
                $def .= " DEFAULT " . $this->getDefaultValue($column->getDefault());
            }

            $statements[] = "ALTER TABLE {$table} MODIFY {$def}";
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
        $name = $tableName . '_' . $fk->getColumn() . '_fk';

        $sql = "CONSTRAINT " . $this->wrap($name) . " FOREIGN KEY (" . $this->wrap($fk->getColumn()) . ") ";
        $sql .= "REFERENCES " . $this->wrap($fk->getReferencedTable());
        $sql .= " (" . $this->wrap($fk->getReferencedColumn()) . ")";

        // Oracle doesn't support ON UPDATE, only ON DELETE
        $sql .= " ON DELETE " . ($fk->getOnDelete() === 'CASCADE' ? 'CASCADE' : 'SET NULL');

        return $sql;
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

        // Default value
        if ($column->hasDefault()) {
            $sql .= ' DEFAULT ' . $this->getDefaultValue($column->getDefault());
        }

        // Oracle 12c+ Identity (Auto-increment)
        if ($column->isAutoIncrement()) {
            $sql .= ' GENERATED ALWAYS AS IDENTITY';
        }

        // Nullable
        if (!$column->isNullable()) {
            $sql .= ' NOT NULL';
        }

        // Primary key (inline)
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
     * Get the column type SQL
     *
     * @param  Column  $column
     * @return string
     */
    protected function getColumnType(Column $column): string
    {
        $type = $column->getType();
        $params = $column->getParameters();

        $oracleType = $this->typeMap[$type] ?? 'VARCHAR2(255)';

        // Add length/precision for types that need it
        switch ($type) {
            case 'string':
            case 'char':
                $length = $params['length'] ?? 255;
                return "{$oracleType}({$length})";

            case 'decimal':
                $precision = $params['precision'] ?? 10;
                $scale = $params['scale'] ?? 2;
                return "{$oracleType}({$precision},{$scale})";

            default:
                return $oracleType;
        }
    }

    /**
     * Wrap an identifier
     *
     * Oracle uses double quotes for case-sensitive identifiers
     *
     * @param  string  $value
     * @return string
     */
    protected function wrap(string $value): string
    {
        // Oracle identifiers are usually uppercase. We wrap in double quotes to preserve case if provided.
        return '"' . strtoupper(str_replace('"', '""', $value)) . '"';
    }

    /**
     * Compile ALTER TABLE statements from an AlterTableBuilder
     *
     * Returns an array of SQL statements that implement the requested
     * schema changes (drop FKs, drop indexes, drop/add/modify columns,
     * rename columns, add PKs, add FKs, add indexes, rename table).
     *
     * @param  AlterTableBuilder  $builder
     * @return array
     */
    public function compileAlterTable(AlterTableBuilder $builder): array
    {
        $table = $this->driver->replacePrefix($builder->getTable());
        $statements = [];
        $quotedTable = $this->wrap(strtoupper($table));

        // 1. Drop Foreign Keys
        foreach ($builder->getDropForeignKeys() as $fkName) {
            $statements[] = "ALTER TABLE " . $quotedTable . " DROP CONSTRAINT " . $this->wrap(strtoupper($fkName));
        }

        // 2. Drop Indexes
        foreach ($builder->getDropIndexes() as $indexName) {
            $statements[] = "DROP INDEX " . $this->wrap(strtoupper($indexName));
        }

        // 3. Drop Primary Key
        if ($builder->getDropPrimaryKeyFlag()) {
            $statements[] = "ALTER TABLE " . $quotedTable . " DROP PRIMARY KEY CASCADE";
        }

        // 4. Drop Columns
        $dropColumns = $builder->getDropColumns();
        if (!empty($dropColumns)) {
            $cols = array_map(function ($c) {
                return $this->wrap(strtoupper($c));
            }, $dropColumns);
            $statements[] = "ALTER TABLE " . $quotedTable . " DROP (" . implode(', ', $cols) . ")";
        }

        // 5. Add Columns
        $addColumns = $builder->getAddColumns();
        if (!empty($addColumns)) {
            $defs = [];
            foreach ($addColumns as $name => $definition) {
                $defs[] = $this->driver->buildAlterColumnDefinition(
                    $name,
                    $definition
                );
            }
            $statements[] = "ALTER TABLE " . $quotedTable
                . " ADD (" . implode(', ', $defs) . ")";
        }

        // 6. Modify Columns
        $modifyColumns = $builder->getModifyColumns();
        if (!empty($modifyColumns)) {
            $defs = [];
            foreach ($modifyColumns as $name => $definition) {
                $defs[] = $this->driver->buildAlterColumnDefinition(
                    $name,
                    $definition
                );
            }
            $statements[] = "ALTER TABLE " . $quotedTable
                . " MODIFY (" . implode(', ', $defs) . ")";
        }

        // 7. Rename Columns
        foreach ($builder->getRenameColumns() as $oldName => $info) {
            $newName = $info['newName'];
            $statements[] = "ALTER TABLE " . $quotedTable
                . " RENAME COLUMN "
                . $this->wrap(strtoupper($oldName))
                . " TO "
                . $this->wrap(strtoupper($newName));
        }

        // 8. Add Primary Key
        $addPk = $builder->getAddPrimaryKey();
        if ($addPk) {
            $columns = array_map(
                function ($c) {
                    return $this->wrap(strtoupper($c));
                },
                $addPk
            );
            $statements[] = "ALTER TABLE " . $quotedTable
                . " ADD PRIMARY KEY ("
                . implode(', ', $columns) . ")";
        }

        // 9. Add Foreign Keys
        foreach ($builder->getAddForeignKeys() as $fk) {
            $fkName = !empty($fk['name'])
                ? $fk['name']
                : "FK_" . strtoupper($table) . "_"
                    . strtoupper($fk['column']);
            $sql = "ALTER TABLE " . $quotedTable
                . " ADD CONSTRAINT " . $this->wrap($fkName)
                . " FOREIGN KEY (" . $this->wrap($fk['column'])
                . ") REFERENCES "
                . $this->wrap(
                    $this->driver->replacePrefix($fk['referencedTable'])
                )
                . " (" . $this->wrap($fk['referencedColumn'])
                . ")";
            if (!empty($fk['onDelete'])) {
                $sql .= " ON DELETE " . $fk['onDelete'];
            }
            $statements[] = $sql;
        }

        // 10. Add Indexes (keyed by index name)
        foreach ($builder->getAddIndexes() as $name => $index) {
            $type = !empty($index['unique']) ? 'UNIQUE ' : '';
            $cols = array_map(
                function ($c) {
                    return $this->wrap($c);
                },
                $index['columns']
            );
            $statements[] = "CREATE {$type}INDEX "
                . $this->wrap($name)
                . " ON " . $quotedTable
                . " (" . implode(', ', $cols) . ")";
        }

        // 11. Add Fulltext Indexes (Oracle Text or fallback to regular index)
        foreach ($builder->getAddFulltextIndexes() as $name => $columns) {
            $cols = array_map(
                function ($c) {
                    return $this->wrap(strtoupper($c));
                },
                $columns
            );
            // Use regular index (Oracle Text requires CTXSYS which
            // may not be available). Single-column for CONTEXT type.
            $statements[] = "CREATE INDEX "
                . $this->wrap(strtoupper($name))
                . " ON " . $quotedTable
                . " (" . implode(', ', $cols) . ")";
        }

        // 12. Rename Table

        return $statements;
    }

    /**
     * Compile a DROP TABLE statement
     *
     * Oracle doesn't support IF EXISTS and requires CASCADE CONSTRAINTS PURGE
     * to drop dependent foreign keys and reclaim tablespace.
     *
     * @param  string  $table
     * @param  bool    $ifExists  Ignored — Oracle handles IF EXISTS in Driver
     * @return string
     */
    public function compileDrop(string $table, bool $ifExists = true): string
    {
        return 'DROP TABLE ' . $this->wrapTable($table)
            . ' CASCADE CONSTRAINTS PURGE';
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
        return "RENAME " . $this->wrap($from) . " TO " . $this->wrap($to);
    }
}
