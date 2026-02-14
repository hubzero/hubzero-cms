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
 * Firebird-specific DDL grammar
 */
class FirebirdGrammar extends Grammar
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
     * Type mappings from abstract types to Firebird types
     *
     * @var array
     */
    protected $typeMap = [
        // Integer types
        'tinyInteger' => 'SMALLINT',
        'smallInteger' => 'SMALLINT',
        'mediumInteger' => 'INTEGER',
        'integer' => 'INTEGER',
        'bigInteger' => 'BIGINT',
        'boolean' => 'BOOLEAN',

        // String types
        'string' => 'VARCHAR',
        'char' => 'CHAR',
        'tinyText' => 'BLOB SUB_TYPE 1',
        'text' => 'BLOB SUB_TYPE 1',
        'mediumText' => 'BLOB SUB_TYPE 1',
        'longText' => 'BLOB SUB_TYPE 1',

        // Numeric types
        'float' => 'FLOAT',
        'double' => 'DOUBLE PRECISION',
        'decimal' => 'DECIMAL',

        // Date/time types
        'date' => 'DATE',
        'time' => 'TIME',
        'datetime' => 'TIMESTAMP',
        'timestamp' => 'TIMESTAMP',
        'timestampTz' => 'TIMESTAMP',
        'year' => 'SMALLINT',

        // Binary types
        'binary' => 'BLOB SUB_TYPE 0',

        // Special types
        'json' => 'VARCHAR(32765)',  // JSON stored as VARCHAR to avoid BLOB PDO bug
        'uuid' => 'CHAR(36)',  // UUID in string format (e.g., 550e8400-e29b-41d4-a716-446655440000)
        'ulid' => 'CHAR(26)',
        'ipAddress' => 'VARCHAR(45)',
        'macAddress' => 'VARCHAR(17)',
    ];

    /**
     * Compile CREATE TABLE statement(s)
     *
     * Firebird requires separate CREATE INDEX statements for all indexes.
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

        // Build CREATE TABLE statement
        $sql = "CREATE TABLE {$table} (\n  " . implode(",\n  ", $parts) . "\n)";
        $statements[] = $sql;

        // Firebird requires separate CREATE INDEX statements
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

        // Fulltext indexes (Firebird doesn't have built-in fulltext, create regular indexes)
        foreach ($blueprint->getFulltextIndexes() as $name => $columns) {
            $columnList = $this->columnize($columns);
            $statements[] = "CREATE INDEX {$this->wrap($name)} ON {$table} ({$columnList})";
        }

        return $statements;
    }

    /**
     * Compile an inline index definition for CREATE TABLE
     *
     * Firebird does not support inline indexes - returns null.
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
     * Firebird does not support inline unique indexes - returns null.
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
     * Firebird does not support inline fulltext indexes - returns null.
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
     * Firebird uses ALTER TABLE ... ALTER COLUMN ...
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
            $statements[] = "ALTER TABLE {$table} ALTER COLUMN {$colName} TYPE " . $this->getColumnType($column);

            // Change nullability is complex in Firebird via metadata or UPDATE,
            // usually requires direct system table updates for legacy versions.
            // Firebird 3.0+ supports: ALTER TABLE ... ALTER COLUMN ... [SET|DROP] NOT NULL
            if (!$column->isNullable()) {
                $statements[] = "ALTER TABLE {$table} ALTER COLUMN {$colName} SET NOT NULL";
            } else {
                $statements[] = "ALTER TABLE {$table} ALTER COLUMN {$colName} DROP NOT NULL";
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

        // Auto-increment (Firebird 3.0+)
        if ($column->isAutoIncrement()) {
            $sql .= ' GENERATED BY DEFAULT AS IDENTITY';
        }

        // Default value
        if ($column->hasDefault()) {
            $sql .= ' DEFAULT ' . $this->getDefaultValue($column->getDefault());
        }

        // Nullable
        if (!$column->isNullable()) {
            $sql .= ' NOT NULL';
        }

        // CHECK constraint for SET/ENUM types
        $checkValues = $column->getParameter('check_values');
        if (!empty($checkValues)) {
            $quotedValues = array_map(function ($v) {
                return "'" . str_replace("'", "''", $v) . "'";
            }, $checkValues);
            $columnName = $this->wrap($column->getName());
            $sql .= ' CHECK (' . $columnName . ' IN (' . implode(', ', $quotedValues) . '))';
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

        // Handle MySQL SET() type - convert to VARCHAR with CHECK constraint
        if (preg_match('/^SET\((.+)\)$/i', $type, $matches)) {
            // Extract the longest value to determine VARCHAR length
            $values = str_getcsv($matches[1], ',', "'");
            $maxLen = max(array_map('strlen', $values));
            $length = max($maxLen, 20); // Minimum 20 chars

            // Store the CHECK constraint values for later use
            $column->setParameter('check_values', $values);

            return "VARCHAR({$length})";
        }

        // Handle MySQL ENUM() type similarly
        if (preg_match('/^ENUM\((.+)\)$/i', $type, $matches)) {
            $values = str_getcsv($matches[1], ',', "'");
            $maxLen = max(array_map('strlen', $values));
            $length = max($maxLen, 20);

            $column->setParameter('check_values', $values);

            return "VARCHAR({$length})";
        }

        $firebirdType = $this->typeMap[$type] ?? 'VARCHAR(255)';

        // Add length/precision for types that need it
        switch ($type) {
            case 'string':
            case 'char':
                $length = $params['length'] ?? 255;
                return "{$firebirdType}({$length})";

            case 'decimal':
                $precision = $params['precision'] ?? 10;
                $scale = $params['scale'] ?? 2;
                return "{$firebirdType}({$precision},{$scale})";

            default:
                return $firebirdType;
        }
    }

    /**
     * Wrap an identifier
     *
     * Firebird uses double quotes for identifiers. Unquoted identifiers are stored
     * as UPPERCASE. We uppercase before quoting to ensure consistency.
     * PDO::ATTR_CASE=PDO::CASE_LOWER normalizes returned column names to lowercase.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrap(string $value): string
    {
        return '"' . strtoupper(str_replace('"', '""', $value)) . '"';
    }

    /**
     * Compile a RENAME TABLE statement
     *
     * Firebird doesn't support RENAME TABLE directly. Use a temporary table or it's not supported.
     *
     * @param  string  $from  Current table name
     * @param  string  $to    New table name
     * @return string
     */
    public function compileRename(string $from, string $to): string
    {
        // Not natively supported in a single command in Firebird
        throw new \RuntimeException("RENAME TABLE is not supported in Firebird.");
    }

    /**
     * Compile a RENAME COLUMN statement
     *
     * @param  string  $table   Table name
     * @param  string  $from    Current column name
     * @param  string  $to      New column name
     * @return string
     */
    public function compileRenameColumn(string $table, string $from, string $to): string
    {
        return "ALTER TABLE " . $this->wrap($table) . " ALTER COLUMN " . $this->wrap($from) . " TO " . $this->wrap($to);
    }

    /**
     * Compiles an AlterTableBuilder instance into a SQL string.
     *
     * @param   AlterTableBuilder  $builder  The builder instance to compile
     * @return  array
     */
    public function compileAlterTable(AlterTableBuilder $builder): array
    {
        $table = $this->driver->replacePrefix($builder->getTable());
        $statements = [];

        // Check if we need table rebuild for column positioning
        // Firebird doesn't support AFTER/BEFORE in ALTER TABLE ADD COLUMN
        $needsRebuild = false;
        foreach ($builder->getAddColumns() as $name => $definition) {
            $modifiers = $definition['modifiers'] ?? [];
            if (isset($modifiers['after']) || isset($modifiers['before'])) {
                $needsRebuild = true;
                break;
            }
        }

        // If positioning is needed, rebuild the table
        if ($needsRebuild) {
            return $this->buildTableRebuild($table, $builder);
        }

        // Firebird generally prefers separate statements for operations

        // Drop indexes
        foreach ($builder->getDropIndexes() as $indexName) {
            $statements[] = "DROP INDEX " . $this->wrap($indexName);
        }

        // Drop foreign keys
        foreach ($builder->getDropForeignKeys() as $fkName) {
            $statements[] = "ALTER TABLE " . $this->wrap($table) . " DROP CONSTRAINT " . $this->wrap($fkName);
        }

        // Drop columns
        foreach ($builder->getDropColumns() as $columnName) {
            $statements[] = "ALTER TABLE " . $this->wrap($table) . " DROP " . $this->wrap($columnName);
        }

        // Add columns
        foreach ($builder->getAddColumns() as $name => $definition) {
            $colDef = $this->driver->buildAlterColumnDefinition($name, $definition);
            $statements[] = "ALTER TABLE " . $this->wrap($table) . " ADD " . $colDef;
        }

        // Modify columns
        foreach ($builder->getModifyColumns() as $name => $definition) {
            $type = $definition['type'];

            // Parse definition to separate type from constraints
            // Firebird requires separate statements for TYPE and NOT NULL
            $type = trim($type);
            $notNull = null;
            $defaultValue = null;

            // Extract NOT NULL or NULL constraint
            if (preg_match('/\s+(NOT\s+NULL|NULL)\s*$/i', $type, $matches)) {
                $notNull = strtoupper(trim($matches[1]));
                $type = trim(preg_replace('/\s+(NOT\s+NULL|NULL)\s*$/i', '', $type));
            }

            // Extract DEFAULT value
            if (preg_match('/\s+DEFAULT\s+(.+?)(?:\s+(NOT\s+NULL|NULL))?$/i', $type, $matches)) {
                $defaultValue = trim($matches[1]);
                $type = trim(preg_replace('/\s+DEFAULT\s+.+$/i', '', $type));
            }

            // ALTER COLUMN TYPE
            $wrappedTable = $this->wrap($table);
            $wrappedName = $this->wrap($name);
            $statements[] = "ALTER TABLE " . $wrappedTable
                . " ALTER COLUMN " . $wrappedName
                . " TYPE " . $type;

            // Handle NOT NULL constraint separately
            if ($notNull === 'NOT NULL') {
                $statements[] = "ALTER TABLE " . $wrappedTable
                    . " ALTER COLUMN " . $wrappedName
                    . " SET NOT NULL";
            } elseif ($notNull === 'NULL') {
                $statements[] = "ALTER TABLE " . $wrappedTable
                    . " ALTER COLUMN " . $wrappedName
                    . " DROP NOT NULL";
            }

            // Handle DEFAULT value separately
            if ($defaultValue !== null) {
                $statements[] = "ALTER TABLE " . $wrappedTable
                    . " ALTER COLUMN " . $wrappedName
                    . " SET DEFAULT " . $defaultValue;
            }
        }

        // Rename columns
        foreach ($builder->getRenameColumns() as $oldName => $info) {
            $newName = $info['newName'];
            $statements[] = "ALTER TABLE " . $this->wrap($table)
                . " ALTER COLUMN " . $this->wrap($oldName)
                . " TO " . $this->wrap($newName);
        }

        // Add indexes
        foreach ($builder->getAddIndexes() as $name => $info) {
            $cols = array_map([$this, 'wrap'], $info['columns']);
            $columnList = implode(', ', $cols);

            $unique = $info['unique'] ? 'UNIQUE ' : '';
            $statements[] = "CREATE {$unique}INDEX "
                . $this->wrap($name) . " ON "
                . $this->wrap($table) . " ($columnList)";
        }

        // Add fulltext indexes as regular indexes (Firebird doesn't support FULLTEXT natively)
        foreach ($builder->getAddFulltextIndexes() as $name => $columns) {
            $cols = array_map([$this, 'wrap'], $columns);
            $columnList = implode(', ', $cols);
            $statements[] = "CREATE INDEX " . $this->wrap($name) . " ON " . $this->wrap($table) . " ($columnList)";
        }

        // Drop primary key
        if ($builder->getDropPrimaryKeyFlag()) {
             $pkName = $this->driver->getPrimaryKeyConstraintName($table);
            if ($pkName) {
                $statements[] = "ALTER TABLE " . $this->wrap($table) . " DROP CONSTRAINT " . $this->wrap($pkName);
            }
        }

        // Add primary key
        if ($builder->getAddPrimaryKey() !== null) {
            // Firebird requires all PK columns to be NOT NULL
            // First, alter each column to be NOT NULL
            foreach ($builder->getAddPrimaryKey() as $colName) {
                // Get current column type via Driver
                $colInfo = $this->driver->getColumnTypeInfo($table, $colName);

                if ($colInfo) {
                    // Firebird requires separate ALTER statements for TYPE and NOT NULL
                    $typeMap = [7 => 'SMALLINT', 8 => 'INTEGER', 16 => 'BIGINT', 37 => 'VARCHAR'];
                    $fieldType = $colInfo->{'rdb$field_type'}; // Firebird returns lowercase after processing
                    $type = $typeMap[$fieldType] ?? 'VARCHAR';
                    if ($type === 'VARCHAR') {
                        $charLength = $colInfo->{'rdb$character_length'} ?? $colInfo->{'rdb$field_length'};
                        $type .= '(' . $charLength . ')';
                    }
                    // First set the type, then set NOT NULL
                    $statements[] = "ALTER TABLE "
                        . $this->wrap($table)
                        . " ALTER COLUMN "
                        . $this->wrap($colName)
                        . " TYPE " . $type;
                    $statements[] = "ALTER TABLE "
                        . $this->wrap($table)
                        . " ALTER COLUMN "
                        . $this->wrap($colName)
                        . " SET NOT NULL";
                }
            }

            $cols = array_map([$this, 'wrap'], $builder->getAddPrimaryKey());
            $columnList = implode(', ', $cols);
            $statements[] = "ALTER TABLE " . $this->wrap($table) . " ADD PRIMARY KEY ($columnList)";
        }

        // Add foreign keys
        foreach ($builder->getAddForeignKeys() as $fk) {
            $refTable = $this->driver->replacePrefix($fk['referencedTable']);
            $fkName = !empty($fk['name']) ? $fk['name'] : "fk_" . md5(uniqid());

            $constraint = "ALTER TABLE " . $this->wrap($table) . " ADD ";
            if (!empty($fk['name'])) {
                 $constraint .= "CONSTRAINT " . $this->wrap($fk['name']) . " ";
            }
            $constraint .= "FOREIGN KEY (" . $this->wrap($fk['column']) . ") ";
            $constraint .= "REFERENCES " . $this->wrap($refTable) . " (" . $this->wrap($fk['referencedColumn']) . ") ";
            if (!empty($fk['onDelete'])) {
                $constraint .= "ON DELETE {$fk['onDelete']} ";
            }
            if (!empty($fk['onUpdate'])) {
                $constraint .= "ON UPDATE {$fk['onUpdate']}";
            }
            $statements[] = $constraint;
        }

        // Firebird doesn't have a direct RENAME TABLE command, it requires create/copy/drop
        // This complex operation cannot be represented as simple ALTER TABLE SQL

        return $statements;
    }

    /**
     * Rebuild a Firebird table to apply column positioning
     *
     * Firebird doesn't support AFTER/BEFORE in ALTER TABLE ADD COLUMN.
     * This method rebuilds the table with columns in the correct order.
     *
     * @param   string            $table    Table name (already prefixed)
     * @param   AlterTableBuilder $builder  The table builder
     * @return  array  Array of SQL statements
     */
    protected function buildTableRebuild(string $table, AlterTableBuilder $builder): array
    {
        $statements = [];
        $table = strtoupper($table);
        $tempTable = $table . '_RB_' . time();

        // Get current table structure via Driver
        $currentColumns = $this->driver->getTableStructureInfo($table);

        if (empty($currentColumns)) {
            return $statements;
        }

        // Build ordered column list
        $orderedColumns = [];
        foreach ($currentColumns as $col) {
            $colName = trim($col->field_name);
            $orderedColumns[$colName] = $col;
        }

        // Process added columns with positioning
        foreach ($builder->getAddColumns() as $name => $definition) {
            $modifiers = $definition['modifiers'] ?? [];

            if (isset($modifiers['after'])) {
                // Insert after specified column
                $afterColumn = $modifiers['after'];
                $newOrdered = [];
                foreach ($orderedColumns as $existingName => $existingCol) {
                    $newOrdered[$existingName] = $existingCol;
                    if ($existingName === $afterColumn) {
                        $newOrdered[$name] = $definition;
                    }
                }
                $orderedColumns = $newOrdered;
            } elseif (isset($modifiers['before'])) {
                // Insert before specified column
                $beforeColumn = $modifiers['before'];
                $newOrdered = [];
                foreach ($orderedColumns as $existingName => $existingCol) {
                    if ($existingName === $beforeColumn) {
                        $newOrdered[$name] = $definition;
                    }
                    $newOrdered[$existingName] = $existingCol;
                }
                $orderedColumns = $newOrdered;
            } else {
                // Add at end
                $orderedColumns[$name] = $definition;
            }
        }

        // Build new column definitions
        $newColumnDefs = [];
        $selectColumns = [];
        $targetColumns = [];

        foreach ($orderedColumns as $colName => $colInfo) {
            if (is_array($colInfo)) {
                // New column from builder
                $colDef = $this->driver->buildAlterColumnDefinition($colName, $colInfo);
                $newColumnDefs[] = $colDef;
                // New columns won't be in SELECT (no source data)
            } else {
                // Existing column from database
                $colDef = $this->wrap($colName) . ' ';

                // Map Firebird type codes to type names
                $typeMap = [
                    7 => 'SMALLINT', 8 => 'INTEGER', 10 => 'FLOAT', 12 => 'DATE',
                    13 => 'TIME', 14 => 'CHAR', 16 => 'BIGINT', 27 => 'DOUBLE PRECISION',
                    35 => 'TIMESTAMP', 37 => 'VARCHAR', 261 => 'BLOB'
                ];

                $fieldType = $typeMap[$colInfo->field_type] ?? 'VARCHAR';

                if ($fieldType === 'VARCHAR' || $fieldType === 'CHAR') {
                    $colDef .= $fieldType . '(' . ($colInfo->char_length ?? $colInfo->field_length) . ')';
                } elseif ($fieldType === 'BLOB' && $colInfo->field_sub_type == 1) {
                    $colDef .= 'BLOB SUB_TYPE 1';
                } else {
                    $colDef .= $fieldType;
                }

                // Handle IDENTITY (auto-increment) - must come before NOT NULL
                if ($colInfo->identity_type !== null) {
                    if ($colInfo->identity_type == 0) {
                        $colDef .= ' GENERATED ALWAYS AS IDENTITY';
                    } elseif ($colInfo->identity_type == 1) {
                        $colDef .= ' GENERATED BY DEFAULT AS IDENTITY';
                    }
                }

                // Handle NOT NULL
                if ($colInfo->null_flag) {
                    $colDef .= ' NOT NULL';
                }

                // Handle DEFAULT (skip if it's identity-related)
                if ($colInfo->default_source && $colInfo->identity_type === null) {
                    $colDef .= ' ' . trim($colInfo->default_source);
                }

                $newColumnDefs[] = $colDef;

                // For identity columns, don't include in INSERT...SELECT
                // Firebird will generate values automatically
                if ($colInfo->identity_type === null) {
                    $selectColumns[] = $this->wrap($colName);
                    $targetColumns[] = $this->wrap($colName);
                }
            }
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
        $statements[] = "ALTER TABLE " . $this->wrap($tempTable) . " TO " . $this->wrap($table);

        return $statements;
    }
}
