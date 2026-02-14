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
 * DB2-specific DDL grammar
 */
class Db2Grammar extends Grammar
{
    /**
     * Compile CREATE TABLE statements from normalized definition data.
     *
     * @param  array $definition
     * @return array
     */
    public function compileCreateTableFromDefinition(array $definition): array
    {
        // DB2 requires primary key columns to be NOT NULL
        // Ensure all primary key columns have nullable=false
        $primaryKey = $definition['primaryKey'] ?? [];
        if (!empty($primaryKey)) {
            foreach ($primaryKey as $pkColumn) {
                if (isset($definition['columns'][$pkColumn])) {
                    // Set nullable=false if not already set
                    if (!isset($definition['columns'][$pkColumn]['modifiers']['nullable'])) {
                        $definition['columns'][$pkColumn]['modifiers']['nullable'] = false;
                    }
                }
            }
        }

        return $this->compileCreateTableFromDefinitionGeneric($definition);
    }

    /**
     * Type mappings from abstract types to DB2 types
     *
     * @var array
     */
    protected $typeMap = [
        // Integer types (keys in lowercase for case-insensitive matching)
        'tinyinteger' => 'SMALLINT',
        'smallinteger' => 'SMALLINT',
        'mediuminteger' => 'INTEGER',
        'integer' => 'INTEGER',
        'biginteger' => 'BIGINT',
        'boolean' => 'SMALLINT',

        // String types
        'string' => 'VARCHAR',
        'char' => 'CHAR',
        'tinytext' => 'VARCHAR(255)',
        'text' => 'CLOB',
        'mediumtext' => 'CLOB',
        'longtext' => 'CLOB',

        // Numeric types
        'float' => 'REAL',
        'double' => 'DOUBLE',
        'decimal' => 'DECIMAL',

        // Date/time types
        'date' => 'DATE',
        'time' => 'TIME',
        'datetime' => 'TIMESTAMP',
        'timestamp' => 'TIMESTAMP',
        'timestamptz' => 'TIMESTAMP',
        'year' => 'SMALLINT',

        // Binary types
        'binary' => 'BLOB',

        // Special types
        'json' => 'CLOB',
        'uuid' => 'CHAR(36)',
        'ulid' => 'CHAR(26)',
        'ipaddress' => 'VARCHAR(45)',
        'macaddress' => 'VARCHAR(17)',
    ];

    /**
     * Compile CREATE TABLE statement(s)
     *
     * DB2 requires separate CREATE INDEX statements for all indexes.
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

        // Foreign keys (inline in CREATE TABLE for DB2)
        foreach ($blueprint->getForeignKeys() as $fk) {
            if (is_array($fk)) {
                // Handle plain array from TableBuilder::foreign()
                $fkName = $fk['name'] ?? 'FK_' . strtoupper($blueprint->getTable()) . '_' . strtoupper($fk['column']);
                $sql = "CONSTRAINT {$this->wrap($fkName)} FOREIGN KEY (" . $this->wrap($fk['column']) . ") ";
                $sql .= "REFERENCES " . $this->wrap($fk['referencedTable']);
                $sql .= " (" . $this->wrap($fk['referencedColumn']) . ")";
                $sql .= " ON DELETE " . $fk['onDelete'];
                // DB2 doesn't support CASCADE for ON UPDATE - convert to NO ACTION
                $onUpdate = strtoupper($fk['onUpdate']) === 'CASCADE' ? 'NO ACTION' : $fk['onUpdate'];
                $sql .= " ON UPDATE " . $onUpdate;
                $parts[] = $sql;
            } else {
                // Handle ForeignKeyBuilder object
                $parts[] = $this->compileForeignKey($fk);
            }
        }

        // Build CREATE TABLE statement (DB2 doesn't support IF NOT EXISTS)
        $sql = "CREATE TABLE {$table} (\n  " . implode(",\n  ", $parts) . "\n)";
        $statements[] = $sql;

        // DB2 requires separate CREATE INDEX statements
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

        // Fulltext indexes (DB2 doesn't have built-in fulltext, create regular indexes)
        foreach ($blueprint->getFulltextIndexes() as $name => $columns) {
            $columnList = $this->columnize($columns);
            $statements[] = "CREATE INDEX {$this->wrap($name)} ON {$table} ({$columnList})";
        }

        return $statements;
    }

    /**
     * Compile an inline index definition for CREATE TABLE
     *
     * DB2 does not support inline indexes - returns null.
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
     * DB2 does not support inline unique indexes - returns null.
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
     * DB2 does not support inline fulltext indexes - returns null.
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
     * Compile a foreign key constraint
     *
     * DB2 doesn't support CASCADE for ON UPDATE - only RESTRICT or NO ACTION.
     * This override converts CASCADE to NO ACTION for ON UPDATE clauses.
     *
     * @param  \Hubzero\Database\Schema\ForeignKeyBuilder  $fk
     * @return string
     */
    protected function compileForeignKey($fk): string
    {
        $name = $fk->getName();
        $sql = "CONSTRAINT {$name} FOREIGN KEY (" . $this->wrap($fk->getColumn()) . ") ";
        $sql .= "REFERENCES " . $this->wrap($fk->getReferencedTable());
        $sql .= " (" . $this->wrap($fk->getReferencedColumn()) . ")";
        $sql .= " ON DELETE " . $fk->getOnDelete();

        // DB2 doesn't support CASCADE for ON UPDATE - convert to NO ACTION
        $onUpdate = strtoupper($fk->getOnUpdate()) === 'CASCADE' ? 'NO ACTION' : $fk->getOnUpdate();
        $sql .= " ON UPDATE " . $onUpdate;

        return $sql;
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
     * DB2 uses ALTER TABLE ... ALTER COLUMN ...
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

            // Change data type
            $statements[] = "ALTER TABLE {$table} ALTER COLUMN"
                . " {$colName} SET DATA TYPE "
                . $this->getColumnType($column);

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
            } else {
                $statements[] = "ALTER TABLE {$table} ALTER COLUMN {$colName} DROP DEFAULT";
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
        $sql = $this->wrap($column->getName()) . ' ' . $this->getColumnType($column);

        // Auto-increment (GENERATED BY DEFAULT allows explicit values)
        if ($column->isAutoIncrement()) {
            $sql .= ' GENERATED BY DEFAULT AS IDENTITY (START WITH 1 INCREMENT BY 1)';
        }

        // Nullable
        if (!$column->isNullable()) {
            $sql .= ' NOT NULL';
        }

        // Default value
        if ($column->hasDefault()) {
            $sql .= ' DEFAULT ' . $this->getDefaultValue($column->getDefault());
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

        // Normalize type to lowercase for typeMap lookup (AlterTableBuilder uses uppercase)
        $normalizedType = strtolower($type);
        $db2Type = $this->typeMap[$normalizedType] ?? 'VARCHAR(255)';

        // Add length/precision for types that need it
        switch ($normalizedType) {
            case 'string':
            case 'char':
                $length = $params['length'] ?? 255;
                return "{$db2Type}({$length})";

            case 'decimal':
                $precision = $params['precision'] ?? 10;
                $scale = $params['scale'] ?? 2;
                return "{$db2Type}({$precision},{$scale})";

            default:
                return $db2Type;
        }
    }


    /**
     * Get the SQL for a default value
     *
     * DB2 doesn't support MySQL's zero-date ('0000-00-00') - convert to NULL
     *
     * @param  mixed  $value
     * @return string
     */
    protected function getDefaultValue($value): string
    {
        // Convert MySQL zero-dates to NULL for DB2
        if (is_string($value) && preg_match('/^0000-00-00/', $value)) {
            return 'NULL';
        }

        return parent::getDefaultValue($value);
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
     * Compile ALTER TABLE statements
     *
     * @param   AlterTableBuilder  $builder
     * @return  array
     */
    public function compileAlterTable(AlterTableBuilder $builder): array
    {
        $table = $this->driver->replacePrefix($builder->getTable());
        $statements = [];
        $quotedTable = $this->wrap($table);

        // 1. Drop Foreign Keys
        foreach ($builder->getDropForeignKeys() as $fkName) {
            $statements[] = "ALTER TABLE " . $quotedTable . " DROP FOREIGN KEY " . $this->wrap($fkName);
        }

        // 2. Drop Indexes
        foreach ($builder->getDropIndexes() as $indexName) {
            $statements[] = "DROP INDEX " . $this->wrap($indexName);
        }

        // 3. Drop Primary Key
        if ($builder->getDropPrimaryKeyFlag()) {
            $statements[] = "ALTER TABLE " . $quotedTable . " DROP PRIMARY KEY";
        }

        // 4. Drop Columns
        foreach ($builder->getDropColumns() as $column) {
            $statements[] = "ALTER TABLE " . $quotedTable . " DROP COLUMN " . $this->wrap($column);
        }

        // 5. Add Columns
        foreach ($builder->getAddColumns() as $name => $column) {
            // Normalize the type (convert abstract types like 'string' to DB2 types like 'VARCHAR')
            $type = $this->driver->normalizeColumnType($column['type'] ?? 'VARCHAR(255)', $column['modifiers'] ?? []);

            $command = "ALTER TABLE " . $quotedTable . " ADD COLUMN " . $this->wrap($name) . ' ' . $type;

            $modifiers = $column['modifiers'] ?? [];

            // Check if default is a zero-date (will be converted to NULL)
            $hasZeroDateDefault = isset($modifiers['default'])
                && is_string($modifiers['default'])
                && preg_match('/^0000-00-00/', $modifiers['default']);

            // DB2 doesn't allow NOT NULL with NULL defaults, so make zero-date columns nullable
            $isNullable = !isset($modifiers['nullable']) || $modifiers['nullable'] !== false || $hasZeroDateDefault;

            if (!$isNullable) {
                $command .= " NOT NULL";
            }

            if (isset($modifiers['default'])) {
                $default = $modifiers['default'];
                if ($default === null) {
                    $command .= " DEFAULT NULL";
                } else {
                    // Use getDefaultValue to handle zero-date conversion to NULL
                    $command .= " DEFAULT " . $this->getDefaultValue($default);
                }
            }

            $statements[] = $command;
        }

        // 6. Modify Columns
        foreach ($builder->getModifyColumns() as $name => $column) {
            $colName = $this->wrap($name);

            // Parse the type string to extract constraints (MySQL-style: "VARCHAR(255) NOT NULL")
            $typeString = $column['type'] ?? 'VARCHAR(255)';
            $isNotNull = false;
            $isNullable = false;

            // Check for NOT NULL or NULL in the type string
            if (preg_match('/\s+NOT\s+NULL\s*$/i', $typeString)) {
                $isNotNull = true;
                $typeString = preg_replace('/\s+NOT\s+NULL\s*$/i', '', $typeString);
            } elseif (preg_match('/\s+NULL\s*$/i', $typeString)) {
                $isNullable = true;
                $typeString = preg_replace('/\s+NULL\s*$/i', '', $typeString);
            }

            // Normalize the type (without constraints)
            $type = $this->driver->normalizeColumnType($typeString, $column['modifiers'] ?? []);

            // Type change
            $statements[] = "ALTER TABLE " . $quotedTable . " ALTER COLUMN " . $colName . " SET DATA TYPE " . $type;

            // Check if default is a zero-date (will be converted to NULL)
            $modifiers = $column['modifiers'] ?? [];
            $hasZeroDateDefault = isset($modifiers['default'])
                && is_string($modifiers['default'])
                && preg_match('/^0000-00-00/', $modifiers['default']);

            // Nullable (from type string or modifiers)
            // DB2 doesn't allow NOT NULL with NULL defaults, so make zero-date columns nullable
            if ($hasZeroDateDefault) {
                $statements[] = "ALTER TABLE " . $quotedTable . " ALTER COLUMN " . $colName . " DROP NOT NULL";
            } elseif ($isNotNull || (isset($modifiers['nullable']) && $modifiers['nullable'] === false)) {
                $statements[] = "ALTER TABLE " . $quotedTable . " ALTER COLUMN " . $colName . " SET NOT NULL";
            } elseif ($isNullable || (isset($modifiers['nullable']) && $modifiers['nullable'] === true)) {
                $statements[] = "ALTER TABLE " . $quotedTable . " ALTER COLUMN " . $colName . " DROP NOT NULL";
            }

            // Default
            if (isset($modifiers['default'])) {
                $default = $modifiers['default'];
                if ($default === null) {
                    $statements[] = "ALTER TABLE " . $quotedTable . " ALTER COLUMN " . $colName . " SET DEFAULT NULL";
                } else {
                    // Use getDefaultValue to handle zero-date conversion to NULL
                    $statements[] = "ALTER TABLE " . $quotedTable
                        . " ALTER COLUMN " . $colName
                        . " SET DEFAULT "
                        . $this->getDefaultValue($default);
                }
            }
        }

        // 7. Rename Columns
        foreach ($builder->getRenameColumns() as $oldName => $column) {
            $newName = $column['newName'] ?? $oldName;
            $statements[] = "ALTER TABLE " . $quotedTable
                . " RENAME COLUMN " . $this->wrap($oldName)
                . " TO " . $this->wrap($newName);
        }

        // 8. Add Primary Key
        $addPk = $builder->getAddPrimaryKey();
        if ($addPk && is_array($addPk)) {
            // Handle both formats: ['columns' => [...]] or just [...]
            $pkColumns = isset($addPk['columns']) ? $addPk['columns'] : $addPk;
            if (is_array($pkColumns) && !empty($pkColumns)) {
                $columns = array_map(function ($c) {
                    return $this->wrap($c);
                }, $pkColumns);
                $statements[] = "ALTER TABLE " . $quotedTable . " ADD PRIMARY KEY (" . implode(', ', $columns) . ")";
            }
        }

        // 9. Add Foreign Keys
        foreach ($builder->getAddForeignKeys() as $fk) {
             $fkName = !empty($fk['name'])
                ? $fk['name']
                : "FK_" . strtoupper($table) . "_" . strtoupper($fk['column']);
             $refTable = $this->wrap(
                 $this->driver->replacePrefix($fk['referencedTable'])
             );
             $sql = "ALTER TABLE " . $quotedTable
                 . " ADD CONSTRAINT " . $this->wrap($fkName)
                 . " FOREIGN KEY (" . $this->wrap($fk['column']) . ")"
                 . " REFERENCES " . $refTable
                 . " (" . $this->wrap($fk['referencedColumn']) . ")";
            if ($fk['onDelete']) {
                $sql .= " ON DELETE " . $fk['onDelete'];
            }
            if ($fk['onUpdate']) {
                // DB2 doesn't support CASCADE for ON UPDATE - only RESTRICT or NO ACTION
                $onUpdate = strtoupper($fk['onUpdate']) === 'CASCADE' ? 'NO ACTION' : $fk['onUpdate'];
                $sql .= " ON UPDATE " . $onUpdate;
            }
             $statements[] = $sql;
        }

        // 10. Add Indexes
        foreach ($builder->getAddIndexes() as $name => $index) {
             $type = (!empty($index['unique'])) ? 'UNIQUE' : '';
             $columns = array_map(function ($c) {
                return $this->wrap($c);
             }, $index['columns'] ?? []);
             $statements[] = "CREATE $type INDEX "
                 . $this->wrap($name) . " ON " . $quotedTable
                 . " (" . implode(', ', $columns) . ")";
        }

        // 11. Add Fulltext Indexes (DB2 doesn't support fulltext - create as regular indexes)
        foreach ($builder->getAddFulltextIndexes() as $name => $columns) {
             $columnList = array_map(function ($c) {
                return $this->wrap($c);
             }, is_array($columns) ? $columns : [$columns]);
             $statements[] = "CREATE INDEX "
                 . $this->wrap($name) . " ON " . $quotedTable
                 . " (" . implode(', ', $columnList) . ")";
        }

        // 12. Rename Table

        return $statements;
    }
}
