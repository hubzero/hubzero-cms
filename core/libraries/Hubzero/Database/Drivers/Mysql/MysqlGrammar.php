<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Mysql;

use Hubzero\Database\Schema\AlterTableBuilder;
use Hubzero\Database\Schema\TableDefinition;
use Hubzero\Database\Schema\Column;
use Hubzero\Database\Drivers\Base\BaseSchemaGrammar;

/**
 * MySQL-specific DDL grammar
 */
class MysqlGrammar extends BaseSchemaGrammar
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
     * Type mappings from abstract types to MySQL types
     *
     * @var array
     */
    protected $typeMap = [
        // Integer types
        'tinyInteger' => 'TINYINT',
        'smallInteger' => 'SMALLINT',
        'mediumInteger' => 'MEDIUMINT',
        'integer' => 'INT',
        'bigInteger' => 'BIGINT',
        'boolean' => 'TINYINT(1)',

        // String types
        'string' => 'VARCHAR',
        'char' => 'CHAR',
        'tinyText' => 'TINYTEXT',
        'text' => 'TEXT',
        'mediumText' => 'MEDIUMTEXT',
        'longText' => 'LONGTEXT',

        // Numeric types
        'float' => 'FLOAT',
        'double' => 'DOUBLE',
        'decimal' => 'DECIMAL',

        // Date/time types
        'date' => 'DATE',
        'time' => 'TIME',
        'datetime' => 'DATETIME',
        'timestamp' => 'TIMESTAMP',
        'timestampTz' => 'TIMESTAMP',
        'year' => 'YEAR',

        // Binary types
        'binary' => 'BLOB',

        // Special types
        'json' => 'JSON',
        'uuid' => 'CHAR(36)',
        'ulid' => 'CHAR(26)',
        'ipAddress' => 'VARCHAR(45)',
        'macAddress' => 'VARCHAR(17)',
    ];

    /**
     * Compile CREATE TABLE statement(s)
     *
     * MySQL supports inline indexes, so this returns a single CREATE TABLE
     * statement with all indexes inline.
     *
     * @param  TableDefinition  $blueprint
     * @return array  Array of SQL statements
     */
    public function compileCreate(TableDefinition $blueprint): array
    {
        $table = $this->wrapTable($blueprint->getTable());
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

        // Unique indexes (inline)
        foreach ($blueprint->getUniqueIndexes() as $name => $columns) {
            $indexSql = $this->compileInlineUniqueIndex($name, $columns);
            if ($indexSql !== null) {
                $parts[] = $indexSql;
            }
        }

        // Regular indexes (inline)
        foreach ($blueprint->getIndexes() as $name => $columns) {
            $indexSql = $this->compileInlineIndex($name, $columns);
            if ($indexSql !== null) {
                $parts[] = $indexSql;
            }
        }

        // Fulltext indexes (inline)
        foreach ($blueprint->getFulltextIndexes() as $name => $columns) {
            $indexSql = $this->compileInlineFulltextIndex($name, $columns);
            if ($indexSql !== null) {
                $parts[] = $indexSql;
            }
        }

        // Foreign keys
        foreach ($blueprint->getForeignKeys() as $fk) {
            $parts[] = $this->compileForeignKey($fk, $blueprint->getTable());
        }

        // Build CREATE TABLE statement
        $ifNotExists = $blueprint->getIfNotExists() ? 'IF NOT EXISTS ' : '';
        $sql = "CREATE TABLE {$ifNotExists}{$table} (\n  " . implode(",\n  ", $parts) . "\n)";

        // Table options
        $sql .= ' ENGINE=' . $blueprint->getEngine();
        $sql .= ' DEFAULT CHARSET=' . $blueprint->getCharset();
        $sql .= ' COLLATE=' . $blueprint->getCollation();

        return [$sql];
    }

    /**
     * Compile an inline index definition for CREATE TABLE
     *
     * MySQL supports inline KEY definitions.
     *
     * @param  string  $name     Index name
     * @param  array   $columns  Column names
     * @return string|null
     */
    public function compileInlineIndex(string $name, array $columns): ?string
    {
        $columnList = $this->columnize($columns);
        return "KEY {$this->wrap($name)} ($columnList)";
    }

    /**
     * Compile an inline unique index definition for CREATE TABLE
     *
     * MySQL supports inline UNIQUE KEY definitions.
     *
     * @param  string  $name     Index name
     * @param  array   $columns  Column names
     * @return string|null
     */
    public function compileInlineUniqueIndex(string $name, array $columns): ?string
    {
        $columnList = $this->columnize($columns);
        return "UNIQUE KEY {$this->wrap($name)} ($columnList)";
    }

    /**
     * Compile an inline fulltext index definition for CREATE TABLE
     *
     * MySQL supports inline FULLTEXT KEY definitions.
     *
     * @param  string  $name     Index name
     * @param  array   $columns  Column names
     * @return string|null
     */
    public function compileInlineFulltextIndex(string $name, array $columns): ?string
    {
        $columnList = $this->columnize($columns);
        return "FULLTEXT KEY {$this->wrap($name)} ($columnList)";
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
            $sql = "ALTER TABLE {$table} ADD COLUMN " . $this->compileColumn($column);

            if ($column->getAfter()) {
                $sql .= ' AFTER ' . $this->wrap($column->getAfter());
            }

            $statements[] = $sql;
        }

        return $statements;
    }

    /**
     * Compile an ALTER TABLE statement to modify columns
     *
     * MySQL uses MODIFY COLUMN for changing column definitions
     *
     * @param  TableDefinition  $blueprint
     * @return array
     */
    public function compileAlterModify(TableDefinition $blueprint): array
    {
        $statements = [];
        $table = $this->wrapTable($blueprint->getTable());

        foreach ($blueprint->getModifyColumns() as $column) {
            $sql = "ALTER TABLE {$table} MODIFY COLUMN " . $this->compileColumn($column);

            if ($column->getAfter()) {
                $sql .= ' AFTER ' . $this->wrap($column->getAfter());
            }

            $statements[] = $sql;
        }

        return $statements;
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
     * Compile CREATE INDEX statements
     *
     * Override to support MySQL-specific index types
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
                    $statements[] = "CREATE FULLTEXT INDEX {$name} ON {$table} ({$columns})";
                    break;

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
     * @param  string  $tableName
     * @return string
     */
    protected function compileForeignKey($fk, string $tableName): string
    {
        $name = $tableName . '_' . $fk->getColumn() . '_foreign';

        $sql = "CONSTRAINT {$name} FOREIGN KEY (" . $this->wrap($fk->getColumn()) . ") ";
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

        $mysqlType = $this->typeMap[$type] ?? 'VARCHAR(255)';

        // Add length/precision for types that need it
        switch ($type) {
            case 'string':
            case 'char':
                $length = $params['length'] ?? 255;
                return "{$mysqlType}({$length})";

            case 'float':
            case 'double':
            case 'decimal':
                $precision = $params['precision'] ?? 10;
                $scale = $params['scale'] ?? 2;
                return "{$mysqlType}({$precision},{$scale})";

            default:
                if (
                    isset($params['length']) && in_array($type, [
                    'tinyInteger', 'smallInteger', 'mediumInteger',
                    'integer', 'bigInteger',
                    ])
                ) {
                    return "{$mysqlType}({$params['length']})";
                }
                return $mysqlType;
        }
    }


    /**
     * Get the unsigned modifier
     *
     * @param  Column  $column
     * @return string
     */
    protected function getUnsigned(Column $column): string
    {
        if ($column->isUnsigned()) {
            return ' UNSIGNED';
        }
        return '';
    }

    /**
     * Get the auto-increment modifier
     *
     * @param  Column  $column
     * @return string
     */
    protected function getAutoIncrement(Column $column): string
    {
        if ($column->isAutoIncrement()) {
            return ' AUTO_INCREMENT PRIMARY KEY';
        }
        return '';
    }

    /**
     * Compile ALTER TABLE operations into SQL statements
     *
     * This method generates MySQL-specific ALTER TABLE SQL from the operations
     * collected in an AlterTableBuilder.
     *
     * @param   AlterTableBuilder  $builder  The table builder
     * @return  array  Array of SQL statements to execute
     */
    public function compileAlterTable(AlterTableBuilder $builder): array
    {
        $table = $this->driver->replacePrefix($builder->getTable());
        $statements = [];

        // MySQL can handle most operations in single statements
        $alterParts = [];

        // Drop indexes first (before dropping columns that might be indexed)
        foreach ($builder->getDropIndexes() as $indexName) {
            $alterParts[] = "DROP INDEX " . $this->wrap($indexName);
        }

        // Drop foreign keys
        foreach ($builder->getDropForeignKeys() as $fkName) {
            $alterParts[] = "DROP FOREIGN KEY " . $this->wrap($fkName);
        }

        // Drop columns
        foreach ($builder->getDropColumns() as $columnName) {
            $alterParts[] = "DROP COLUMN " . $this->wrap($columnName);
        }

        // Add columns
        foreach ($builder->getAddColumns() as $name => $definition) {
            $colDef = $this->driver->buildAlterColumnDefinition($name, $definition);
            $alterParts[] = "ADD COLUMN $colDef";
        }

        // Modify columns
        foreach ($builder->getModifyColumns() as $name => $definition) {
            $colDef = $this->driver->buildAlterColumnDefinition($name, $definition);
            $alterParts[] = "MODIFY COLUMN $colDef";
        }

        // Rename columns
        foreach ($builder->getRenameColumns() as $oldName => $info) {
            $alterParts[] = "CHANGE COLUMN "
                . $this->wrap($oldName) . " "
                . $this->wrap($info['newName'])
                . " {$info['type']}";
        }

        // Add indexes
        foreach ($builder->getAddIndexes() as $name => $info) {
            $columnList = implode(', ', array_map([$this, 'wrap'], $info['columns']));
            $unique = $info['unique'] ? 'UNIQUE ' : '';
            $alterParts[] = "ADD {$unique}INDEX " . $this->wrap($name) . " ($columnList)";
        }

        // Add fulltext indexes
        foreach ($builder->getAddFulltextIndexes() as $name => $columns) {
            $columnList = implode(', ', array_map([$this, 'wrap'], $columns));
            $alterParts[] = "ADD FULLTEXT KEY " . $this->wrap($name) . " ($columnList)";
        }

        // Drop primary key
        if ($builder->getDropPrimaryKeyFlag()) {
            $alterParts[] = "DROP PRIMARY KEY";
        }

        // Add primary key
        if ($builder->getAddPrimaryKey() !== null) {
            $columnList = implode(', ', array_map([$this, 'wrap'], $builder->getAddPrimaryKey()));
            $alterParts[] = "ADD PRIMARY KEY ($columnList)";
        }

        // Add foreign keys
        foreach ($builder->getAddForeignKeys() as $fk) {
            $refTable = $this->driver->replacePrefix($fk['referencedTable']);
            // Use custom name if provided, otherwise auto-generate
            $fkName = !empty($fk['name']) ? $fk['name'] : "fk_{$table}_{$fk['column']}";
            $constraint = "ADD CONSTRAINT " . $this->wrap($fkName) . " ";
            $constraint .= "FOREIGN KEY (" . $this->wrap($fk['column']) . ") ";
            $constraint .= "REFERENCES " . $this->wrap($refTable) . " (" . $this->wrap($fk['referencedColumn']) . ") ";
            $constraint .= "ON DELETE {$fk['onDelete']} ON UPDATE {$fk['onUpdate']}";
            $alterParts[] = $constraint;
        }

        // Change engine
        if ($builder->getNewEngine() !== null) {
            $alterParts[] = "ENGINE = {$builder->getNewEngine()}";
        }

        // Change charset/collation
        if ($builder->getNewCharset() !== null) {
            $charsetPart = "CONVERT TO CHARACTER SET {$builder->getNewCharset()}";
            if ($builder->getNewCollation() !== null) {
                $charsetPart .= " COLLATE {$builder->getNewCollation()}";
            }
            $alterParts[] = $charsetPart;
        } elseif ($builder->getNewCollation() !== null) {
            // Collation only (without charset change)
            $alterParts[] = "COLLATE {$builder->getNewCollation()}";
        }

        // Build the ALTER TABLE statement(s)
        if (!empty($alterParts)) {
            $statements[] = "ALTER TABLE " . $this->wrap($table) . " " . implode(", ", $alterParts);
        }

        // Rename table (separate statement)
        return $statements;
    }
}
