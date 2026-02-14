<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Syntax;

/**
 * Database IBM Informix query syntax class
 *
 * IBM Informix is a product family within IBM's Information Management division
 * that is centered on several relational database management system (RDBMS) offerings.
 * This class extends the base SQL syntax with Informix-specific features.
 *
 * Informix-specific features supported:
 * - FIRST/SKIP: Informix pagination syntax
 * - MERGE: Informix upsert syntax (Informix 11.50+)
 * - systables, syscolumns: System catalog tables
 * - Sequences (SERIAL, SERIAL8, BIGSERIAL)
 * - || operator for string concatenation
 * - EXTEND function for type conversion
 *
 * Informix SQL features supported:
 * - Standard DML: SELECT, INSERT, UPDATE, DELETE with full clause support
 * - All JOIN types including OUTER JOIN
 * - Aggregate functions
 * - Stored Procedures (SPL)
 * - User-Defined Types (UDTs)
 */
class Informix extends Sql
{
    /**
     * Whether to inline SELECT-expression bindings into SQL
     *
     * PDO_INFORMIX 1.3.x cannot handle placeholders as function arguments
     * in SELECT expressions (SQLPrepare returns -999). When true, buildSelect()
     * substitutes literal values instead of using ? placeholders.
     *
     * Set to false if a future pdo_informix version fixes this limitation.
     * Can be toggled via setInlineSelectBindings().
     *
     * @var bool
     */
    protected $inlineSelectBindings = true;

    /**
     * Informix type mappings to normalized types
     *
     * Maps Informix-specific types to generic type names
     * that match what other drivers return.
     *
     * @var array
     */
    protected static $typeMappings = [
        // Integer types
        'integer'    => 'int',
        'int'        => 'int',
        'smallint'   => 'smallint',
        'bigint'     => 'bigint',
        'int8'       => 'bigint',
        'serial'     => 'int',
        'serial8'    => 'bigint',
        'bigserial'  => 'bigint',
        'decimal'    => 'decimal',
        'numeric'    => 'decimal',
        'money'      => 'decimal',
        'smallfloat' => 'float',
        'real'       => 'float',
        'float'      => 'double',
        'double precision' => 'double',

        // Character types
        'character'  => 'char',
        'char'       => 'char',
        'nchar'      => 'char',
        'varchar'    => 'varchar',
        'nvarchar'   => 'varchar',
        'lvarchar'   => 'varchar',
        'text'       => 'text',
        'clob'       => 'text',

        // Binary types
        'byte'       => 'blob',
        'blob'       => 'blob',

        // Date/time types
        'date'       => 'date',
        'datetime'   => 'datetime',
        'interval'   => 'varchar',

        // Boolean type
        'boolean'    => 'boolean',
    ];

    /**
     * Builds a limit statement from the set params
     *
     * Informix uses FIRST n SKIP m syntax which appears AFTER SELECT:
     * SELECT FIRST 10 SKIP 20 * FROM table
     *
     * However, in our architecture, buildLimit() is called after buildSelect(),
     * so this method returns empty and the driver must handle FIRST/SKIP
     * in the SELECT clause itself.
     *
     * @return  string
     */
    public function buildLimit()
    {
        // Informix FIRST/SKIP must be in SELECT clause
        // Return empty here - handled in buildSelect() override
        return '';
    }

    /**
     * Builds a select statement from the set params
     *
     * Override to support FIRST/SKIP syntax for pagination.
     *
     * @return  string
     */
    protected function buildSelect()
    {
        if ($this->hasFullJoin()) {
            $this->validateFullJoinLayout();
        }

        $selects = [];

        foreach ($this->select as $select) {
            // Handle raw select expressions (subqueries, etc.)
            if (array_key_exists('raw', $select)) {
                // PDO_INFORMIX (pdo_informix 1.3.x) cannot handle placeholders
                // as function arguments in SELECT expressions — SQLPrepare returns
                // error -999 "Not implemented yet". When inlineSelectBindings is
                // enabled, substitute literal values into the SQL string instead
                // of using ? placeholders. Disable via setInlineSelectBindings(false)
                // if a newer driver version fixes this.
                if ($this->inlineSelectBindings && !empty($select['bindings'])) {
                    $string = '(' . $this->inlineBindings($select['raw'], $select['bindings']) . ')';
                } else {
                    $string = '(' . $select['raw'] . ')';
                    foreach ($select['bindings'] as $binding) {
                        $this->bind($binding);
                    }
                }
            } elseif ($select['column'] instanceof \Hubzero\Database\Expression) {
                $string = $this->buildExpression($select['column']);
            } elseif (isset($select['count']) && $select['count'] == 'distinct') {
                $string = "COUNT(DISTINCT({$select['column']}))";
            } elseif (!empty($select['count'])) {
                $string = "COUNT({$select['column']})";
            } else {
                $string = $select['column'];
            }

            // See if we're including an alias
            if (isset($select['as']) && $select['as'] !== null) {
                $string .= " AS {$select['as']}";
            }

            $selects[] = $string;
        }

        // Handle SKIP/FIRST for pagination
        // Note: Informix requires SKIP before FIRST
        $limitClause = '';
        if (!empty($this->start)) {
            $limitClause = 'SKIP ' . (int) $this->start . ' ';
        }
        if (!empty($this->limit)) {
            $limitClause .= 'FIRST ' . (int) $this->limit . ' ';
        }

        return 'SELECT ' . $limitClause . implode(',', $selects);
    }

    /**
     * Enable or disable SELECT-expression binding inlining
     *
     * @param   bool  $inline  True to inline (default), false to use normal placeholders
     * @return  $this
     */
    public function setInlineSelectBindings(bool $inline): self
    {
        $this->inlineSelectBindings = $inline;
        return $this;
    }

    /**
     * Quote a PHP value as a SQL literal string
     *
     * Used to inline values into SQL when PDO_INFORMIX cannot handle
     * ? placeholders (MERGE USING SELECT, SELECT expressions, etc.).
     *
     * @param   mixed  $value  The value to quote
     * @return  string  SQL literal
     */
    protected function quoteValue($value): string
    {
        if ($value === null) {
            return 'NULL';
        }
        if (is_int($value)) {
            return (string) $value;
        }
        if (is_float($value)) {
            return sprintf('%F', $value);
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return $this->connection->quote((string) $value);
    }

    /**
     * Replace ? placeholders with literal values in a SQL expression
     *
     * PDO_INFORMIX cannot handle placeholders as function arguments in
     * SELECT expressions. This method substitutes them with safe literal
     * values so the SQL can be prepared without error.
     *
     * @param   string  $expression  SQL expression containing ? placeholders
     * @param   array   $bindings    Values to substitute (consumed left-to-right)
     * @return  string  Expression with placeholders replaced by literals
     */
    protected function inlineBindings(string $expression, array $bindings): string
    {
        $idx = 0;
        $count = count($bindings);

        return preg_replace_callback('/\?/', function () use ($bindings, &$idx, $count) {
            if ($idx >= $count) {
                return '?';
            }

            return $this->quoteValue($bindings[$idx++]);
        }, $expression);
    }

    /**
     * Returns the proper query for generating a list of table columns
     *
     * Informix uses systables and syscolumns for column introspection
     *
     * @param   string  $table  The name of the database table
     * @return  string
     */
    public function getColumnsQuery($table)
    {
        return "SELECT
                c.colname AS Field,
                c.coltype AS Type,
                CASE WHEN c.coltype >= 256 THEN 'NO' ELSE 'YES' END AS Nullable,
                c.colno AS Position
            FROM syscolumns c
            JOIN systables t ON c.tabid = t.tabid
            WHERE t.tabname = " . $this->connection->quote(strtolower($table)) . "
                AND t.tabtype = 'T'
            ORDER BY c.colno";
    }

    /**
     * Normalizes the results of the column query
     *
     * @param   array  $data      The raw column data
     * @param   bool   $typeOnly  True (default) to only return field types
     * @return  array
     */
    public function normalizeColumns($data, $typeOnly = true)
    {
        $results = [];

        if ($typeOnly) {
            foreach ($data as $field) {
                // Informix returns UPPERCASE field names
                $fieldName = isset($field->FIELD) ? $field->FIELD : $field->field;
                $fieldType = isset($field->TYPE) ? $field->TYPE : $field->type;
                $results[trim($fieldName)] = $this->normalizeType($fieldType);
            }
        } else {
            foreach ($data as $field) {
                // Informix returns UPPERCASE field names
                $fieldName = isset($field->FIELD) ? $field->FIELD : $field->field;
                $fieldType = isset($field->TYPE) ? $field->TYPE : $field->type;
                $nullable = isset($field->NULLABLE) ? $field->NULLABLE : $field->nullable;

                $results[trim($fieldName)] = [
                    'name'      => trim($fieldName),
                    'type'      => $this->normalizeType($fieldType),
                    'raw_type'  => $fieldType,
                    'allownull' => (strtoupper($nullable) === 'YES'),
                    'default'   => null,  // Requires separate query
                    'pk'        => false  // Requires separate query
                ];
            }
        }

        return $results;
    }

    /**
     * Normalize an Informix type code to a generic type name
     *
     * Informix stores types as numeric codes in syscolumns.coltype
     *
     * @param   int  $typeCode  The Informix type code
     * @return  string  The normalized type
     */
    protected function normalizeType($typeCode)
    {
        // Remove nullable flag (MOD 256)
        $baseType = $typeCode % 256;

        // Informix type codes
        $types = [
            0   => 'char',
            1   => 'smallint',
            2   => 'int',
            3   => 'float',
            4   => 'smallfloat',
            5   => 'decimal',
            6   => 'serial',
            7   => 'date',
            8   => 'money',
            9   => 'null',
            10  => 'datetime',
            11  => 'byte',
            12  => 'text',
            13  => 'varchar',
            14  => 'interval',
            15  => 'nchar',
            16  => 'nvarchar',
            17  => 'int8',
            18  => 'serial8',
            19  => 'set',
            20  => 'multiset',
            21  => 'list',
            22  => 'row',
            40  => 'lvarchar',
            41  => 'blob',
            43  => 'clob',
            45  => 'boolean',
            52  => 'bigint',
            53  => 'bigserial',
        ];

        $typeName = $types[$baseType] ?? 'varchar';

        // Map to generic types
        return self::$typeMappings[$typeName] ?? 'varchar';
    }

    /**
     * Get the SQL for checking if a table exists
     *
     * @param   string  $table  The table name
     * @return  string
     */
    public function getTableExistsQuery($table)
    {
        return "SELECT COUNT(*) FROM systables WHERE tabname = " .
            $this->connection->quote(strtolower($table)) .
            " AND tabtype = 'T'";
    }

    /**
     * Get the SQL for retrieving table list
     *
     * @return  string
     */
    public function getTableListQuery()
    {
        return "SELECT tabname AS table_name
                FROM systables
                WHERE tabtype = 'T'
                AND tabid > 99
                ORDER BY tabname";
    }

    // =========================================================================
    // Date/Time Operations - Informix specific overrides
    // =========================================================================

    /**
     * Sets a date/time extraction where clause
     *
     * Informix uses EXTEND() function for date/time manipulation
     *
     * @param   string  $column    The datetime column name
     * @param   string  $operator  Comparison operator
     * @param   mixed   $value     The value to compare against
     * @param   string  $part      The date part: 'date', 'time', 'year', 'month', 'day'
     * @param   string  $logical   The operator between multiple clauses
     * @param   int     $depth     The depth level of the clause
     * @return  void
     */
    public function setDateWhere($column, $operator, $value, $part, $logical = 'and', $depth = 0)
    {
        $quotedColumn = $this->connection->quoteName($column);

        switch ($part) {
            case 'date':
                // EXTEND to DATE
                $raw = "EXTEND({$quotedColumn}, YEAR TO DAY) {$operator} ?";
                break;
            case 'year':
                $raw = "YEAR({$quotedColumn}) {$operator} ?";
                break;
            case 'month':
                $raw = "MONTH({$quotedColumn}) {$operator} ?";
                break;
            case 'day':
                $raw = "DAY({$quotedColumn}) {$operator} ?";
                break;
            default:
                $raw = "EXTEND({$quotedColumn}, YEAR TO DAY) {$operator} ?";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [$value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    // =========================================================================
    // Table Operations - Informix specific overrides
    // =========================================================================

    /**
     * Get the SQL statements to truncate a table
     *
     * Informix uses TRUNCATE TABLE syntax
     *
     * @param   string  $table  The table to truncate
     * @return  array   Array of SQL statements to execute
     */
    public function getTruncateStatements($table)
    {
        return ['TRUNCATE TABLE ' . $this->connection->quoteName($table)];
    }

    /**
     * Builds an upsert statement from the set params
     *
     * Informix 11.50+ uses MERGE statement for upsert operations
     *
     * @return  string
     */
    public function buildUpsert()
    {
        $columns = array_keys($this->upsertValues);
        $quotedColumns = [];

        foreach ($columns as $column) {
            $quotedColumns[] = $this->connection->quoteName($column);
        }

        // Determine the conflict (key) columns
        $conflictColumns = !empty($this->upsertConflictColumns)
            ? $this->upsertConflictColumns
            : [$columns[0]];

        $quotedConflict = array_map(
            fn($col) => $this->connection->quoteName($col),
            $conflictColumns
        );

        // Build the ON condition for MERGE
        $onConditions = [];
        foreach ($conflictColumns as $col) {
            $quotedCol = $this->connection->quoteName($col);
            $onConditions[] = "t.{$quotedCol} = s.{$quotedCol}";
        }

        // Build UPDATE SET clause
        $updateSets = [];
        foreach ($this->upsertUpdateColumns as $col) {
            $quotedCol = $this->connection->quoteName($col);
            $updateSets[] = "t.{$quotedCol} = s.{$quotedCol}";
        }

        // Build source SELECT with inlined values
        // PDO_INFORMIX 1.3.x cannot handle ? placeholders in
        // MERGE USING SELECT — SQLPrepare returns -201 syntax error.
        // Inline literal values instead (same approach as inlineSelectBindings).
        $sourceColumns = [];
        foreach ($columns as $column) {
            $value = $this->upsertValues[$column];
            if (is_string($value)) {
                $value = trim($value);
            }
            $literal = $this->quoteValue($value);
            $sourceColumns[] = $literal
                . ' AS ' . $this->connection->quoteName($column);
        }

        $sql = 'MERGE INTO ' . $this->connection->quoteName($this->upsertTable) . ' t ' .
               'USING (SELECT ' . implode(', ', $sourceColumns) . ' FROM sysmaster:sysdual) s ' .
               'ON (' . implode(' AND ', $onConditions) . ') ' .
               'WHEN MATCHED THEN UPDATE SET ' . implode(', ', $updateSets) . ' ' .
               'WHEN NOT MATCHED THEN INSERT (' . implode(', ', $quotedColumns) . ') ' .
               'VALUES (' . implode(', ', array_map(fn($c) => 's.' . $this->connection->quoteName($c), $columns)) . ')';

        return $sql;
    }

    /**
     * Builds a bulk insert statement from the set params
     *
     * Informix does not support multi-row INSERT ... VALUES (...), (...) syntax.
     * Returns empty string to trigger individual INSERT fallback.
     *
     * @return  string
     */
    public function buildInsertMany()
    {
        return '';
    }

    /**
     * Informix needs row-by-row INSERT IGNORE for INSERT ... SELECT
     *
     * Informix has no INSERT IGNORE syntax. Single-row inserts catch
     * duplicate errors in Query::execute(). But INSERT ... SELECT
     * fails entirely on the first duplicate, skipping non-duplicate
     * rows too. So we break it into per-row inserts with error catching.
     *
     * @return bool
     */
    public function needsRowByRowInsertIgnore(): bool
    {
        return $this->ignore && $this->insertSelectQuery !== null;
    }

    /**
     * Build a function expression with Informix-specific overrides
     *
     * @param   object  $expression  The expression object
     * @return  string
     */
    protected function buildFunctionExpression($expression): string
    {
        $function = $expression->getFunction();
        $args = $expression->getArguments();

        switch ($function) {
            // Sequence functions (native Informix syntax)
            case 'NEXTVAL':
                return $args[0] . '.NEXTVAL';

            case 'CURRVAL':
                return $args[0] . '.CURRVAL';

            default:
                return parent::buildFunctionExpression($expression);
        }
    }
}
