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
 * SQL Server-specific DDL grammar
 *
 * SQL Server (T-SQL) has different DDL syntax from MySQL:
 * - Uses square brackets for identifier quoting: [column]
 * - Uses IDENTITY(1,1) instead of AUTO_INCREMENT
 * - Uses NVARCHAR for Unicode strings
 * - No storage engines concept
 * - Different constraint syntax
 * - Uses BIT instead of BOOLEAN
 *
 */
class SqlsrvGrammar extends Grammar
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
     * Type mappings from abstract types to SQL Server types
     *
     * @var array
     */
    protected $typeMap = [
        // Integer types (SQL Server has no MEDIUMINT)
        'tinyInteger'  => 'TINYINT',
        'smallInteger' => 'SMALLINT',
        'mediumInteger' => 'INT',
        'integer'      => 'INT',
        'bigInteger'   => 'BIGINT',
        'boolean'      => 'BIT',

        // String types (using NVARCHAR for Unicode support)
        'string'       => 'NVARCHAR',
        'char'         => 'NCHAR',
        'tinyText'     => 'NVARCHAR(255)',
        'text'         => 'NVARCHAR(MAX)',
        'mediumText'   => 'NVARCHAR(MAX)',
        'longText'     => 'NVARCHAR(MAX)',

        // Numeric types
        'float'        => 'FLOAT',
        'double'       => 'FLOAT',
        'decimal'      => 'DECIMAL',

        // Date/time types
        'date'         => 'DATE',
        'time'         => 'TIME',
        'datetime'     => 'DATETIME2(0)',
        'timestamp'    => 'DATETIME2(0)',
        'timestampTz'  => 'DATETIMEOFFSET',
        'year'         => 'SMALLINT',

        // Binary types
        'binary'       => 'VARBINARY(MAX)',

        // Special types
        'json'         => 'NVARCHAR(MAX)',
        'uuid'         => 'CHAR(36)',
        'ulid'         => 'CHAR(26)',
        'ipAddress'    => 'VARCHAR(45)',
        'macAddress'   => 'VARCHAR(17)',
    ];

    /**
     * Compile CREATE TABLE statement(s)
     *
     * SQL Server requires separate CREATE INDEX statements for all indexes.
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
            $parts[] = $this->compileForeignKey($fk);
        }

        // Build CREATE TABLE statement (SQL Server doesn't support IF NOT EXISTS)
        $sql = "CREATE TABLE {$table} (\n  " . implode(",\n  ", $parts) . "\n)";
        $statements[] = $sql;

        // SQL Server requires separate CREATE INDEX statements
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

        // Fulltext indexes (SQL Server has FULLTEXT INDEX but requires catalog setup)
        foreach ($blueprint->getFulltextIndexes() as $name => $columns) {
            $columnList = $this->columnize($columns);
            $statements[] = "CREATE FULLTEXT INDEX ON {$table} ({$columnList}) KEY INDEX {$this->wrap($name)}";
        }

        return $statements;
    }

    /**
     * Compile an inline index definition for CREATE TABLE
     *
     * SQL Server does not support inline indexes - returns null.
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
     * SQL Server does not support inline unique indexes - returns null.
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
     * SQL Server does not support inline fulltext indexes - returns null.
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
            // SQL Server doesn't support column positioning
            $sql = "ALTER TABLE {$table} ADD " . $this->compileColumn($column);
            $statements[] = $sql;
        }

        return $statements;
    }

    /**
     * Compile an ALTER TABLE statement to modify columns
     *
     * SQL Server uses ALTER COLUMN for modifying column definitions.
     *
     * @param  TableDefinition  $blueprint
     * @return array
     */
    public function compileAlterModify(TableDefinition $blueprint): array
    {
        $statements = [];
        $table = $this->wrapTable($blueprint->getTable());

        foreach ($blueprint->getModifyColumns() as $column) {
            // SQL Server uses ALTER COLUMN instead of MODIFY COLUMN
            $sql = "ALTER TABLE {$table} ALTER COLUMN " . $this->compileColumn($column);
            $statements[] = $sql;
        }

        return $statements;
    }

    /**
     * Compile a RENAME TABLE statement
     *
     * SQL Server uses sp_rename for table renaming.
     *
     * @param  string  $from  Current table name
     * @param  string  $to    New table name
     * @return string
     */
    public function compileRename(string $from, string $to): string
    {
        // sp_rename takes string literal arguments, not bracket-quoted identifiers
        return 'EXEC sp_rename ' . $this->driver->quote($from)
            . ', ' . $this->driver->quote($to);
    }

    /**
     * Compile a RENAME COLUMN statement
     *
     * SQL Server uses sp_rename for column renaming.
     *
     * @param  string  $table   Table name
     * @param  string  $from    Current column name
     * @param  string  $to      New column name
     * @return string
     */
    public function compileRenameColumn(string $table, string $from, string $to): string
    {
        // sp_rename takes string literal arguments, not bracket-quoted identifiers
        return 'EXEC sp_rename ' . $this->driver->quote($table . '.' . $from)
            . ', ' . $this->driver->quote($to) . ", 'COLUMN'";
    }

    /**
     * Compile CREATE INDEX statements
     *
     * @param  TableDefinition  $blueprint
     * @return array
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
                    // SQL Server full-text indexing requires a fulltext catalog
                    // and a unique index on the table. This cannot be expressed
                    // as a single DDL statement.
                    throw new \RuntimeException(
                        'Fulltext indexes require a fulltext catalog and unique index. '
                        . 'Use raw SQL: CREATE FULLTEXT INDEX ON '
                        . "{$table} ({$columns}) KEY INDEX <unique_index>"
                    );

                case 'spatial':
                    $statements[] = "CREATE SPATIAL INDEX {$name} ON {$table} ({$columns})";
                    break;

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
        $params = $column->getParameters();
        $sqlType = $this->typeMap[$type] ?? 'NVARCHAR';

        // Handle types that need length
        if (in_array($type, ['string', 'char'])) {
            $length = $params['length'] ?? 255;
            $sqlType .= "({$length})";
        } elseif ($type === 'decimal') {
            $precision = $params['precision'] ?? 10;
            $scale = $params['scale'] ?? 2;
            $sqlType .= "({$precision},{$scale})";
        }

        return $sqlType;
    }

    /**
     * Get the auto-increment modifier
     *
     * SQL Server uses IDENTITY(1,1) for auto-increment.
     *
     * @param  Column  $column
     * @return string
     */
    protected function getAutoIncrement(Column $column): string
    {
        if ($column->isAutoIncrement()) {
            return ' IDENTITY(1,1) PRIMARY KEY';
        }
        return '';
    }

    /**
     * Wrap an identifier
     *
     * SQL Server uses square brackets for identifier quoting.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrap(string $value): string
    {
        // SQL Server uses square brackets
        return '[' . str_replace(']', ']]', $value) . ']';
    }

    /**
     * Compile a column definition
     *
     * Override to handle SQL Server-specific column syntax.
     *
     * @param  Column  $column
     * @return string
     */
    protected function compileColumn(Column $column): string
    {
        $sql = $this->wrap($column->getName()) . ' ' . $this->getColumnType($column);

        // Auto-increment (IDENTITY)
        $sql .= $this->getAutoIncrement($column);

        // NULL/NOT NULL
        if (!$column->isAutoIncrement()) {
            $sql .= $column->isNullable() ? ' NULL' : ' NOT NULL';
        }

        // Default value
        if ($column->hasDefault() && !$column->isAutoIncrement()) {
            $sql .= ' DEFAULT ' . $this->getDefaultValue($column->getDefault());
        }

        return $sql;
    }


    /**
     * Compile ALTER TABLE operations into SQL statements for SQL Server
     *
     * SQL Server has some differences from MySQL:
     * - Uses ALTER COLUMN instead of MODIFY COLUMN
     * - Doesn't support column positioning (AFTER, FIRST)
     * - Uses square brackets for identifiers
     * - Uses sp_rename for renaming
     *
     * @param   AlterTableBuilder  $builder  The table builder
     * @return  array  Array of SQL statements to execute
     */
    public function compileAlterTable(AlterTableBuilder $builder): array
    {
        $table = $this->driver->replacePrefix($builder->getTable());
        $qt = $this->wrap($table);
        $statements = [];

        // Drop indexes first (before dropping columns that might be indexed)
        foreach ($builder->getDropIndexes() as $indexName) {
            $statements[] = 'DROP INDEX ' . $this->wrap($indexName) . " ON $qt";
        }

        // Drop foreign keys
        foreach ($builder->getDropForeignKeys() as $fkName) {
            $statements[] = "ALTER TABLE $qt DROP CONSTRAINT " . $this->wrap($fkName);
        }

        // Drop columns - each as a separate statement
        foreach ($builder->getDropColumns() as $columnName) {
            $statements[] = "ALTER TABLE $qt DROP COLUMN " . $this->wrap($columnName);
        }

        // Add columns - each as a separate statement
        foreach ($builder->getAddColumns() as $name => $definition) {
            $colDef = $this->driver->buildAlterColumnDefinition($name, $definition);
            $statements[] = "ALTER TABLE $qt ADD $colDef";
        }

        // Modify columns - SQL Server uses ALTER COLUMN
        foreach ($builder->getModifyColumns() as $name => $definition) {
            $colDef = $this->driver->buildAlterColumnDefinition($name, $definition);
            $statements[] = "ALTER TABLE $qt ALTER COLUMN $colDef";
        }

        // Rename columns - SQL Server uses sp_rename with string arguments
        foreach ($builder->getRenameColumns() as $oldName => $info) {
            $statements[] = 'EXEC sp_rename '
                . $this->driver->quote($table . '.' . $oldName) . ', '
                . $this->driver->quote($info['newName']) . ", 'COLUMN'";
        }

        // Add indexes - as separate CREATE INDEX statements
        foreach ($builder->getAddIndexes() as $name => $info) {
            $columnList = implode(', ', array_map(
                fn($col) => $this->wrap($col),
                $info['columns']
            ));
            $unique = $info['unique'] ? 'UNIQUE ' : '';
            $statements[] = "CREATE {$unique}INDEX "
                . $this->wrap($name) . " ON $qt ($columnList)";
        }

        // Add fulltext indexes.
        // When FTS is installed, create a real fulltext catalog + index.
        // Otherwise, fall back to a regular index (same as Firebird/Informix).
        $hasFts = $this->driver->isFullTextInstalled();
        foreach ($builder->getAddFulltextIndexes() as $name => $columns) {
            $columnList = implode(', ', array_map(
                fn($c) => $this->wrap($c),
                $columns
            ));

            if (!$hasFts) {
                $statements[] = "CREATE INDEX "
                    . $this->wrap($name)
                    . " ON $qt ($columnList)";
                continue;
            }

            $catalog = 'ft_catalog_' . preg_replace(
                '/[^a-zA-Z0-9_]/',
                '_',
                $table
            );
            $statements[] = "IF NOT EXISTS (SELECT 1 FROM sys.fulltext_catalogs"
                . " WHERE name = " . $this->driver->quote($catalog) . ")"
                . " CREATE FULLTEXT CATALOG " . $this->wrap($catalog);

            $keyIndexSql = "DECLARE @ki NVARCHAR(128), @sql NVARCHAR(MAX);"
                . " SELECT TOP 1 @ki = i.name FROM sys.indexes i"
                . " WHERE i.object_id = OBJECT_ID("
                . $this->driver->quote($table) . ")"
                . " AND i.is_unique = 1 ORDER BY i.is_primary_key DESC;"
                . " IF @ki IS NOT NULL BEGIN"
                . "   SET @sql = 'CREATE FULLTEXT INDEX ON $qt ("
                . $columnList
                . ") KEY INDEX ' + QUOTENAME(@ki) + ' ON "
                . $this->wrap($catalog) . "';"
                . "   EXEC(@sql);"
                . " END";
            $statements[] = $keyIndexSql;
        }

        // Drop primary key - SQL Server requires dynamic lookup of constraint name
        if ($builder->getDropPrimaryKeyFlag()) {
            $quotedTable = addslashes($table);
            $script = <<<SQL
DECLARE @pkConstraint NVARCHAR(128), @sql NVARCHAR(MAX);
SELECT @pkConstraint = name FROM sys.key_constraints
WHERE type = 'PK' AND parent_object_id = OBJECT_ID('$quotedTable');
IF @pkConstraint IS NOT NULL BEGIN
    SET @sql = 'ALTER TABLE $qt DROP CONSTRAINT ' + QUOTENAME(@pkConstraint);
    EXEC(@sql);
END
SQL;
            $statements[] = $script;
        }

        // Add primary key - SQL Server requires NOT NULL on PK columns
        if ($builder->getAddPrimaryKey() !== null) {
            $pkCols = $builder->getAddPrimaryKey();
            // Ensure PK columns are NOT NULL before adding constraint
            $tableColumns = $this->driver->getTableColumns($table, false);
            foreach ($pkCols as $col) {
                if (isset($tableColumns[$col])) {
                    $colInfo = $tableColumns[$col];
                    $colType = $this->driver->buildFullColumnType($colInfo);
                    $qCol = $this->wrap($col);
                    $statements[] = "ALTER TABLE $qt"
                        . " ALTER COLUMN $qCol $colType NOT NULL";
                }
            }
            $columnList = implode(', ', array_map(
                fn($col) => $this->wrap($col),
                $pkCols
            ));
            $statements[] = "ALTER TABLE $qt ADD PRIMARY KEY ($columnList)";
        }

        // Add foreign keys
        foreach ($builder->getAddForeignKeys() as $fk) {
            $refTable = $this->driver->replacePrefix($fk['referencedTable']);
            $fkName = !empty($fk['name']) ? $fk['name'] : "fk_{$table}_{$fk['column']}";
            $constraint = "ALTER TABLE $qt ADD CONSTRAINT "
                . $this->wrap($fkName) . ' ';
            $constraint .= 'FOREIGN KEY (' . $this->wrap($fk['column']) . ') ';
            $constraint .= 'REFERENCES ' . $this->wrap($refTable)
                . ' (' . $this->wrap($fk['referencedColumn']) . ') ';
            $constraint .= "ON DELETE {$fk['onDelete']} ON UPDATE {$fk['onUpdate']}";
            $statements[] = $constraint;
        }


        return $statements;
    }
}
