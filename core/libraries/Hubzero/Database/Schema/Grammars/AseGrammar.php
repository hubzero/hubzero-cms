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
 * SAP ASE (Sybase) DDL grammar
 *
 * ASE uses T-SQL with some differences from SQL Server:
 * - Uses square brackets for identifier quoting: [column]
 * - Uses IDENTITY for auto-increment (no SERIAL, no SEQUENCE)
 * - Uses ALTER TABLE MODIFY (not ALTER TABLE ALTER COLUMN)
 * - No storage engines concept
 * - BIT type cannot be NULL
 * - No NVARCHAR(MAX) - use TEXT for large strings
 * - Page size limits VARCHAR to ~4096 bytes
 * - sp_rename for renaming tables and columns
 */
class AseGrammar extends Grammar
{
    /**
     * Compile CREATE TABLE statements from normalized definition data.
     *
     * @param  array $definition
     * @return array
     */
    public function compileCreateTableFromDefinition(array $definition): array
    {
        // ASE requires PRIMARY KEY columns to be NOT NULL.
        // Mark PK columns explicitly so compileColumnFromDefinition()
        // won't make them nullable.
        $primaryKey = $definition['primaryKey'] ?? [];
        if (!empty($primaryKey)) {
            foreach ($primaryKey as $pkCol) {
                if (isset($definition['columns'][$pkCol])) {
                    $definition['columns'][$pkCol]['modifiers']['nullable'] = false;
                }
            }
        }

        return $this->compileCreateTableFromDefinitionGeneric($definition);
    }

    /**
     * Type mappings from abstract types to ASE types
     *
     * @var array
     */
    protected $typeMap = [
        // Integer types
        'tinyInteger'  => 'TINYINT',
        'smallInteger' => 'SMALLINT',
        'mediumInteger' => 'INT',
        'integer'      => 'INT',
        'bigInteger'   => 'BIGINT',
        'boolean'      => 'BIT',

        // String types - ASE has VARCHAR limit based on page size (~4096)
        'string'       => 'VARCHAR',
        'char'         => 'CHAR',
        'tinyText'     => 'VARCHAR(255)',
        'text'         => 'TEXT',
        'mediumText'   => 'TEXT',
        'longText'     => 'TEXT',

        // Numeric types
        'float'        => 'FLOAT',
        'double'       => 'FLOAT',
        'decimal'      => 'DECIMAL',

        // Date/time types — ASE DATE type returns non-ISO format via
        // PDO_DBLIB (e.g. "Jun 15 2000 12:00AM"), so use DATETIME which
        // respects SET DATEFORMAT ymd for consistent ISO output.
        'date'         => 'DATETIME',
        'time'         => 'TIME',
        'datetime'     => 'DATETIME',
        'timestamp'    => 'DATETIME',
        'timestampTz'  => 'DATETIME',
        'year'         => 'SMALLINT',

        // Binary types
        'binary'       => 'VARBINARY(255)',

        // Special types
        'json'         => 'TEXT',
        'uuid'         => 'CHAR(36)',
        'ulid'         => 'CHAR(26)',
        'ipAddress'    => 'VARCHAR(45)',
        'macAddress'   => 'VARCHAR(17)',
    ];

    /**
     * Compile CREATE TABLE statement(s)
     *
     * ASE requires separate CREATE INDEX statements for indexes.
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

        $sql = "CREATE TABLE {$table} (\n  " . implode(",\n  ", $parts) . "\n)";
        $statements[] = $sql;

        // Unique indexes - separate statements
        foreach ($blueprint->getUniqueIndexes() as $name => $columns) {
            $columnList = $this->columnize($columns);
            $statements[] = "CREATE UNIQUE INDEX {$this->wrap($name)} ON {$table} ({$columnList})";
        }

        // Regular indexes - separate statements
        foreach ($blueprint->getIndexes() as $name => $columns) {
            $columnList = $this->columnize($columns);
            $statements[] = "CREATE INDEX {$this->wrap($name)} ON {$table} ({$columnList})";
        }

        // Fulltext indexes not supported on ASE - create regular indexes as fallback
        foreach ($blueprint->getFulltextIndexes() as $name => $columns) {
            $columnList = $this->columnize($columns);
            $statements[] = "CREATE INDEX {$this->wrap($name)} ON {$table} ({$columnList})";
        }

        return $statements;
    }

    /**
     * Compile an inline index definition for CREATE TABLE
     *
     * ASE does not support inline indexes - returns null.
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
     * ASE does not support inline unique indexes - returns null.
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
     * ASE does not support fulltext indexes - returns null.
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
            $sql = "ALTER TABLE {$table} ADD " . $this->compileColumn($column);
            $statements[] = $sql;
        }

        return $statements;
    }

    /**
     * Compile an ALTER TABLE statement to modify columns
     *
     * ASE uses MODIFY instead of ALTER COLUMN.
     *
     * @param  TableDefinition  $blueprint
     * @return array
     */
    public function compileAlterModify(TableDefinition $blueprint): array
    {
        $statements = [];
        $table = $this->wrapTable($blueprint->getTable());

        foreach ($blueprint->getModifyColumns() as $column) {
            $sql = "ALTER TABLE {$table} MODIFY " . $this->compileColumn($column);
            $statements[] = $sql;
        }

        return $statements;
    }

    /**
     * Compile a RENAME TABLE statement
     *
     * ASE uses sp_rename for table renaming.
     *
     * @param  string  $from  Current table name
     * @param  string  $to    New table name
     * @return string
     */
    public function compileRename(string $from, string $to): string
    {
        return 'EXEC sp_rename ' . $this->driver->quote($from)
            . ', ' . $this->driver->quote($to);
    }

    /**
     * Compile a DROP TABLE statement
     *
     * ASE does not support DROP TABLE IF EXISTS. The driver's dropTable()
     * method handles the existence check; this always emits a plain DROP.
     *
     * @param  string  $table
     * @param  bool    $ifExists  Ignored — ASE has no IF EXISTS syntax
     * @return string
     */
    public function compileDrop(string $table, bool $ifExists = true): string
    {
        return 'DROP TABLE ' . $this->wrapTable($table);
    }

    /**
     * Compile a RENAME COLUMN statement
     *
     * ASE uses sp_rename for column renaming.
     *
     * @param  string  $table   Table name
     * @param  string  $from    Current column name
     * @param  string  $to      New column name
     * @return string
     */
    public function compileRenameColumn(string $table, string $from, string $to): string
    {
        return 'EXEC sp_rename ' . $this->driver->quote($table . '.' . $from)
            . ', ' . $this->driver->quote($to) . ", 'COLUMN'";
    }

    /**
     * Compile ALTER TABLE operations into SQL statements
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
            $statements[] = 'DROP INDEX ' . $qt . '.' . $this->wrap($indexName);
        }

        // Drop foreign keys
        foreach ($builder->getDropForeignKeys() as $fkName) {
            $statements[] = "ALTER TABLE $qt DROP CONSTRAINT " . $this->wrap($fkName);
        }

        // Drop columns
        foreach ($builder->getDropColumns() as $columnName) {
            $statements[] = "ALTER TABLE $qt DROP " . $this->wrap($columnName);
        }

        // Add columns
        foreach ($builder->getAddColumns() as $name => $definition) {
            $colDef = $this->driver->buildAlterColumnDefinition($name, $definition);
            $statements[] = "ALTER TABLE $qt ADD $colDef";
        }

        // Modify columns - ASE uses MODIFY
        foreach ($builder->getModifyColumns() as $name => $definition) {
            $colDef = $this->driver->buildAlterColumnDefinition($name, $definition);
            $statements[] = "ALTER TABLE $qt MODIFY $colDef";
        }

        // Rename columns - sp_rename with result set consumption
        foreach ($builder->getRenameColumns() as $oldName => $info) {
            $statements[] = 'EXEC sp_rename '
                . $this->driver->quote($table . '.' . $oldName) . ', '
                . $this->driver->quote($info['newName']) . ", 'column'";
        }

        // Add indexes
        foreach ($builder->getAddIndexes() as $name => $info) {
            $columnList = implode(', ', array_map(
                fn($col) => $this->wrap($col),
                $info['columns']
            ));
            $unique = $info['unique'] ? 'UNIQUE ' : '';
            $statements[] = "CREATE {$unique}INDEX "
                . $this->wrap($name) . " ON $qt ($columnList)";
        }

        // Fulltext indexes - fall back to regular indexes
        foreach ($builder->getAddFulltextIndexes() as $name => $columns) {
            $columnList = implode(', ', array_map(
                fn($c) => $this->wrap($c),
                $columns
            ));
            $statements[] = "CREATE INDEX "
                . $this->wrap($name) . " ON $qt ($columnList)";
        }

        // Drop primary key
        if ($builder->getDropPrimaryKeyFlag()) {
            // ASE: PK constraints are stored as indexes in sysindexes,
            // NOT in sysconstraints. status & 2048 = primary key index.
            $statements[] = "DECLARE @pk_name VARCHAR(255) "
                . "SELECT @pk_name = i.name FROM sysindexes i "
                . "WHERE i.id = object_id('$table') "
                . "AND i.indid > 0 AND i.indid < 255 "
                . "AND i.status & 2048 = 2048 "
                . "IF @pk_name IS NOT NULL "
                . "EXEC('ALTER TABLE $qt DROP CONSTRAINT [' + @pk_name + ']')";
        }

        // Add primary key — ASE requires PK columns to be NOT NULL
        if ($builder->getAddPrimaryKey() !== null) {
            $pkCols = $builder->getAddPrimaryKey();

            // Make nullable PK columns NOT NULL first
            $colInfo = $this->driver->getTableColumns($table, false);
            foreach ($pkCols as $col) {
                if (isset($colInfo[$col]) && $colInfo[$col]->Null === 'YES') {
                    $type = $this->driver->buildFullColumnType($colInfo[$col]);
                    $statements[] = "ALTER TABLE $qt MODIFY " . $this->wrap($col) . " $type NOT NULL";
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
                . ' (' . $this->wrap($fk['referencedColumn']) . ')';
            if (!empty($fk['onDelete'])) {
                $constraint .= ' ON DELETE ' . $fk['onDelete'];
            }
            if (!empty($fk['onUpdate'])) {
                $constraint .= ' ON UPDATE ' . $fk['onUpdate'];
            }
            $statements[] = $constraint;
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
     * Compile a column definition from normalized array data
     *
     * Injects implicit defaults for NOT NULL columns without explicit defaults,
     * matching MySQL non-strict mode zero-value behavior. ASE strictly enforces
     * NOT NULL and rejects inserts that omit columns without defaults.
     *
     * @param  string  $name        Column name
     * @param  array   $definition  Column definition array
     * @return string
     */
    protected function compileColumnFromDefinition(string $name, array $definition): string
    {
        $modifiers = $definition['modifiers'] ?? [];
        $hasExplicitNullable = isset($modifiers['nullable']);
        $isExplicitlyNotNullable = $hasExplicitNullable && !$modifiers['nullable'];
        $hasDefault = array_key_exists('default', $modifiers);
        $isAutoIncrement = !empty($modifiers['autoIncrement']);

        // Resolve the SQL type to check for BIT
        $type = $definition['type'] ?? '';
        $resolved = strtoupper(
            method_exists($this->driver, 'normalizeColumnType')
                ? $this->driver->normalizeColumnType($type, $modifiers)
                : $type
        );
        $isBit = (trim(preg_replace('/\([^)]*\)/', '', $resolved)) === 'BIT');

        // When no explicit nullable flag is set, ASE defaults to NOT NULL
        // while MySQL defaults to NULL. Inject explicit nullable=true to
        // match MySQL's cross-database behavior.
        // Exception: ASE BIT columns cannot be NULL — keep as NOT NULL DEFAULT 0.
        if (!$hasExplicitNullable && !$isAutoIncrement) {
            if ($isBit) {
                $definition['modifiers']['nullable'] = false;
                if (!$hasDefault) {
                    $definition['modifiers']['default'] = 0;
                }
            } else {
                $definition['modifiers']['nullable'] = true;
            }
        } elseif ($isBit && $hasExplicitNullable && $modifiers['nullable']) {
            // Even if explicitly set nullable, BIT cannot be NULL in ASE
            $definition['modifiers']['nullable'] = false;
            if (!$hasDefault) {
                $definition['modifiers']['default'] = 0;
            }
        }

        // Refresh after possible changes
        $modifiers = $definition['modifiers'] ?? [];
        $hasDefault = array_key_exists('default', $modifiers);
        $isExplicitlyNotNullable = isset($modifiers['nullable']) && !$modifiers['nullable'];

        // For explicitly NOT NULL columns without defaults, add type-appropriate
        // implicit defaults matching MySQL non-strict mode zero-values.
        if ($isExplicitlyNotNullable && !$hasDefault && !$isAutoIncrement) {
            $implicit = null;
            if (str_starts_with($resolved, 'VARCHAR') || str_starts_with($resolved, 'CHAR')
                || str_starts_with($resolved, 'NVARCHAR') || str_starts_with($resolved, 'NCHAR')
            ) {
                $implicit = '';
            } elseif (in_array($resolved, ['INT', 'BIGINT', 'SMALLINT', 'TINYINT', 'BIT'])) {
                $implicit = 0;
            } elseif ($resolved === 'FLOAT' || str_starts_with($resolved, 'DECIMAL')) {
                $implicit = 0;
            }

            if ($implicit !== null) {
                $definition['modifiers']['default'] = $implicit;
            }
        }

        return parent::compileColumnFromDefinition($name, $definition);
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
        $sqlType = $this->typeMap[$type] ?? 'VARCHAR(255)';

        if (in_array($type, ['string', 'char'])) {
            $length = $params['length'] ?? 255;
            $sqlType .= "({$length})";
        } elseif ($type === 'decimal') {
            $precision = $params['precision'] ?? 10;
            $scale = $params['scale'] ?? 2;
            $sqlType .= "({$precision},{$scale})";
        } elseif ($type === 'binary') {
            $length = $params['length'] ?? 255;
            $sqlType = "VARBINARY({$length})";
        }

        return $sqlType;
    }

    /**
     * Get the auto-increment modifier
     *
     * ASE uses IDENTITY for auto-increment.
     *
     * @param  Column  $column
     * @return string
     */
    protected function getAutoIncrement(Column $column): string
    {
        if ($column->isAutoIncrement()) {
            return ' IDENTITY PRIMARY KEY';
        }
        return '';
    }

    /**
     * Wrap an identifier
     *
     * ASE uses square brackets for identifier quoting.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrap(string $value): string
    {
        return '[' . str_replace(']', ']]', $value) . ']';
    }

    /**
     * Compile a column definition
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
        } elseif (!$column->isNullable() && !$column->isAutoIncrement() && !$column->hasDefault()) {
            // Implicit default for NOT NULL columns without explicit defaults.
            // MySQL non-strict mode supplies zero-values for omitted NOT NULL
            // columns; ASE does not, causing INSERT failures. Adding implicit
            // defaults here matches MySQL's behavior.
            $implicit = $this->getImplicitDefault($column);
            if ($implicit !== null) {
                $sql .= ' DEFAULT ' . $implicit;
            }
        }

        return $sql;
    }

    /**
     * Get an implicit default value for a NOT NULL column
     *
     * Returns type-appropriate zero-values matching MySQL non-strict behavior.
     *
     * @param  Column  $column
     * @return string|null  SQL default expression, or null if not applicable
     */
    protected function getImplicitDefault(Column $column): ?string
    {
        $type = $column->getType();
        $sqlType = strtoupper($this->getColumnType($column));

        // String types → DEFAULT ''
        if (in_array($type, ['string', 'char', 'tinyText', 'uuid', 'ulid', 'ipAddress', 'macAddress'])
            || str_starts_with($sqlType, 'VARCHAR')
            || str_starts_with($sqlType, 'CHAR')
        ) {
            return "''";
        }

        // Integer types → DEFAULT 0
        if (in_array($type, ['tinyInteger', 'smallInteger', 'mediumInteger', 'integer', 'bigInteger', 'boolean', 'year'])
            || in_array($sqlType, ['INT', 'BIGINT', 'SMALLINT', 'TINYINT', 'BIT'])
        ) {
            return '0';
        }

        // Float/decimal → DEFAULT 0
        if (in_array($type, ['float', 'double', 'decimal'])
            || in_array($sqlType, ['FLOAT', 'DECIMAL'])
            || str_starts_with($sqlType, 'DECIMAL')
        ) {
            return '0';
        }

        // TEXT, IMAGE, DATETIME — cannot or shouldn't have implicit defaults
        return null;
    }

    /**
     * Get the SQL default value expression
     *
     * ASE uses GETDATE() instead of CURRENT_TIMESTAMP for defaults.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function getDefaultValue($value): string
    {
        if (is_string($value) && strtoupper($value) === 'CURRENT_TIMESTAMP') {
            return 'GETDATE()';
        }

        return parent::getDefaultValue($value);
    }
}
