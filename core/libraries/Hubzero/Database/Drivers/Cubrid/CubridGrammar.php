<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Cubrid;

use Hubzero\Database\Schema\Column;

/**
 * CUBRID-specific DDL grammar
 *
 * This grammar reuses MysqlGrammar as a pragmatic baseline and applies
 * CUBRID-specific DDL adjustments where needed.
 */
class CubridGrammar extends \Hubzero\Database\Drivers\Mysql\MysqlGrammar
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
     * Type mappings from abstract types to CUBRID types
     *
     * CUBRID supports most MySQL types but has some differences:
     * - Uses NUMERIC instead of DECIMAL in some contexts
     * - Has native BIT type (like MySQL)
     * - Supports BLOB/CLOB (like MySQL's TEXT/BLOB)
     *
     * @var array
     */
    protected $typeMap = [
        // Integer types - CUBRID does not support display widths like TINYINT(1)
        'tinyInteger' => 'TINYINT',
        'smallInteger' => 'SMALLINT',
        'mediumInteger' => 'MEDIUMINT',
        'integer' => 'INT',
        'bigInteger' => 'BIGINT',
        'boolean' => 'TINYINT',  // CUBRID requires TINYINT without (1)

        // String types (same as MySQL)
        'string' => 'VARCHAR',
        'char' => 'CHAR',
        'tinyText' => 'VARCHAR(255)',
        'text' => 'STRING',  // CUBRID's STRING type (variable-length, up to 1GB)
        'mediumText' => 'STRING',
        'longText' => 'STRING',

        // Numeric types (same as MySQL)
        'float' => 'FLOAT',
        'double' => 'DOUBLE',
        'decimal' => 'DECIMAL',

        // Date/time types (same as MySQL)
        'date' => 'DATE',
        'time' => 'TIME',
        'datetime' => 'DATETIME',
        'timestamp' => 'TIMESTAMP',
        'timestampTz' => 'TIMESTAMP',
        'year' => 'SMALLINT',

        // Binary types
        'binary' => 'BIT VARYING',

        // Special types
        'json' => 'STRING',  // CUBRID stores JSON as STRING
        'uuid' => 'CHAR(36)',
        'ulid' => 'CHAR(26)',
        'ipAddress' => 'VARCHAR(45)',
        'macAddress' => 'VARCHAR(17)',
    ];

    /**
     * Get the unsigned modifier
     *
     * CUBRID doesn't support UNSIGNED modifier.
     *
     * @param  Column  $column
     * @return string
     */
    protected function getUnsigned(Column $column): string
    {
        // CUBRID doesn't support UNSIGNED
        return '';
    }

    /**
     * Get the column type for the given column
     *
     * Override to strip UNSIGNED and display widths from raw SQL types since CUBRID doesn't support them.
     * Also converts ENUM/SET types to VARCHAR since CUBRID doesn't support them.
     *
     * @param  Column  $column
     * @return string
     */
    protected function getColumnType(Column $column): string
    {
        // Check the raw type BEFORE parent normalizes it
        $rawType = $column->getType();

        $enumOrSetVarcharType = $this->mapEnumOrSetRawTypeToVarchar($rawType);
        if ($enumOrSetVarcharType !== null) {
            return $enumOrSetVarcharType;
        }

        // If this looks like a raw SQL type (contains SQL keywords/modifiers),
        // clean it up BEFORE calling parent.
        $sanitizedRawType = $this->sanitizeRawSqlTypeForCubrid($rawType);
        if ($sanitizedRawType !== null) {
            return $sanitizedRawType;
        }

        // For abstract types, use parent logic
        $type = parent::getColumnType($column);

        // Strip UNSIGNED keyword (in case parent added it back somehow)
        $type = preg_replace('/\s+UNSIGNED/i', '', $type);

        // Strip display widths from integer types
        $type = preg_replace('/(TINYINT|SMALLINT|MEDIUMINT|INT|BIGINT)\(\d+\)/i', '$1', $type);

        return $type;
    }

    /**
     * Map raw ENUM/SET type declarations to CUBRID-compatible VARCHAR types.
     *
     * @param  string  $rawType
     * @return string|null
     */
    protected function mapEnumOrSetRawTypeToVarchar(string $rawType): ?string
    {
        if (!preg_match('/^(ENUM|SET)\(/i', $rawType)) {
            return null;
        }

        // Calculate max length needed for the values:
        // - ENUM: longest value length
        // - SET: sum of all values plus separators (worst case: all selected)
        if (!preg_match('/^(ENUM|SET)\((.*)\)$/i', $rawType, $matches)) {
            return 'VARCHAR(255)';
        }

        $valuesStr = $matches[2];
        preg_match_all("/'([^']*)'/", $valuesStr, $valueMatches);
        $values = $valueMatches[1];

        if (strtoupper($matches[1]) === 'ENUM') {
            $maxLen = 0;
            foreach ($values as $val) {
                $maxLen = max($maxLen, strlen($val));
            }
            return 'VARCHAR(' . max(1, $maxLen) . ')';
        }

        $totalLen = array_sum(array_map('strlen', $values)) + count($values) - 1;
        return 'VARCHAR(' . max(1, $totalLen) . ')';
    }

    /**
     * Sanitize raw SQL type declarations for CUBRID compatibility.
     *
     * Removes unsupported modifiers and integer display widths. Returns null
     * when the raw type should continue through abstract type resolution.
     *
     * @param  string  $rawType
     * @return string|null
     */
    protected function sanitizeRawSqlTypeForCubrid(string $rawType): ?string
    {
        if (
            !preg_match('/\b(UNSIGNED|SIGNED|ZEROFILL)\b/i', $rawType)
            && !preg_match('/(TINYINT|SMALLINT|MEDIUMINT|INT|BIGINT)\(\d+\)/i', $rawType)
        ) {
            return null;
        }

        // Strip UNSIGNED, SIGNED, ZEROFILL keywords.
        $cleanType = preg_replace('/\s+(UNSIGNED|SIGNED|ZEROFILL)/i', '', $rawType);

        // Strip display widths from integer types.
        $cleanType = preg_replace('/(TINYINT|SMALLINT|MEDIUMINT|INT|BIGINT)\(\d+\)/i', '$1', $cleanType);
        $cleanType = trim(preg_replace('/\s+/', ' ', $cleanType));

        $intTypes = 'TINYINT|SMALLINT|MEDIUMINT|INT|BIGINT';
        $numTypes = 'FLOAT|DOUBLE|DECIMAL';
        $strTypes = 'VARCHAR|CHAR|TEXT|STRING';
        $dateTypes = 'DATE|DATETIME|TIMESTAMP|TIME';
        $pattern = "/^({$intTypes}|{$numTypes}|{$strTypes}|{$dateTypes})/i";
        if (preg_match($pattern, $cleanType)) {
            return $cleanType;
        }

        return null;
    }

    /**
     * Compile a column definition
     *
     * Override to ensure UNSIGNED is never added, even if present in raw SQL types.
     *
     * @param  Column  $column
     * @return string
     */
    protected function compileColumn(Column $column): string
    {
        // Get the column type (already strips UNSIGNED via getColumnType())
        $type = $this->getColumnType($column);

        // Start building the column definition
        $sql = $this->wrap($column->getName()) . ' ' . $type;

        // Keep UNSIGNED omitted; CUBRID does not support UNSIGNED modifiers.

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

        // Auto increment
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
     * Compile a CREATE TABLE statement
     *
     * CUBRID doesn't support ENGINE, DEFAULT CHARSET, or COLLATE table options.
     * Override to skip these options.
     *
     * @param  \Hubzero\Database\Schema\TableDefinition  $blueprint
     * @return array  Array of SQL statements
     */
    public function compileCreate(\Hubzero\Database\Schema\TableDefinition $blueprint): array
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
        $this->appendCompiledInlineIndexes(
            $parts,
            $blueprint->getUniqueIndexes(),
            function ($name, $columns) {
                return $this->compileInlineUniqueIndex($name, $columns);
            }
        );

        // Regular indexes (inline)
        $this->appendCompiledInlineIndexes(
            $parts,
            $blueprint->getIndexes(),
            function ($name, $columns) {
                return $this->compileInlineIndex($name, $columns);
            }
        );

        // Foreign keys
        foreach ($blueprint->getForeignKeys() as $fk) {
            $parts[] = $this->compileForeignKey($fk, $blueprint->getTable());
        }

        // Build CREATE TABLE statement (no ENGINE, CHARSET, COLLATE for CUBRID)
        $ifNotExists = $blueprint->getIfNotExists() ? 'IF NOT EXISTS ' : '';
        $sql = "CREATE TABLE {$ifNotExists}{$table} (\n  " . implode(",\n  ", $parts) . "\n)";

        return [$sql];
    }

    /**
     * Append compiled inline index SQL fragments to a CREATE TABLE parts list.
     *
     * @param  array     &$parts
     * @param  iterable  $indexes
     * @param  callable  $compiler
     * @return void
     */
    protected function appendCompiledInlineIndexes(array &$parts, iterable $indexes, callable $compiler): void
    {
        foreach ($indexes as $name => $columns) {
            $indexSql = $compiler($name, $columns);
            if ($indexSql !== null) {
                $parts[] = $indexSql;
            }
        }
    }

    /**
     * Compile RENAME COLUMN statement
     *
     * CUBRID doesn't support MySQL's CHANGE COLUMN syntax properly.
     * Use standard SQL RENAME COLUMN ... TO syntax.
     *
     * @param  string  $table  Table name
     * @param  string  $from   Old column name
     * @param  string  $to     New column name
     * @return string
     */
    public function compileRenameColumn(string $table, string $from, string $to): string
    {
        // CUBRID uses: ALTER TABLE table RENAME COLUMN old_name TO new_name
        return "ALTER TABLE " . $this->wrapTable($table)
            . " RENAME COLUMN " . $this->wrap($from)
            . " TO " . $this->wrap($to);
    }

    /**
     * Compile ALTER TABLE operations
     *
     * CUBRID requires special handling for:
     * - Rename column operations (use separate RENAME statements)
     * - FULLTEXT indexes (not supported, convert to regular indexes)
     *
     * @param  \Hubzero\Database\Schema\AlterTableBuilder  $builder
     * @return array  Array of SQL statements
     */
    public function compileAlterTable(\Hubzero\Database\Schema\AlterTableBuilder $builder): array
    {
        $table = $this->driver->replacePrefix($builder->getTable());
        $statements = [];

        // Process rename columns separately using CUBRID's RENAME syntax
        $renameColumns = $builder->consumeRenameColumns();
        foreach ($renameColumns as $oldName => $info) {
            $statements[] = $this->compileRenameColumn($table, $oldName, $info['newName']);
        }

        // Convert FULLTEXT indexes to regular indexes (CUBRID doesn't support FULLTEXT)
        $fulltextIndexes = $builder->consumeAddFulltextIndexes();
        if (!empty($fulltextIndexes)) {
            $mappedIndexes = [];
            foreach ($fulltextIndexes as $name => $columns) {
                $mappedIndexes[$name] = ['columns' => $columns, 'unique' => false];
            }
            $builder->mergeAddIndexes($mappedIndexes);
        }

        // Let parent handle all other operations (drops, adds, modifies, indexes, etc.)
        $parentStatements = parent::compileAlterTable($builder);
        $statements = array_merge($statements, $parentStatements);

        // Restore removed operations for any other code that might need them
        $builder->restoreRenameColumns($renameColumns);

        if (!empty($fulltextIndexes)) {
            $builder->restoreAddFulltextIndexes($fulltextIndexes);
        }

        return $statements;
    }
}
