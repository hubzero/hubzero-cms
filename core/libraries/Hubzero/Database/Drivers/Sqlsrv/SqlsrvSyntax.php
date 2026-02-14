<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Sqlsrv;

/**
 * Database SQL Server query syntax class
 *
 * Microsoft SQL Server uses T-SQL (Transact-SQL). This class extends the
 * base SQL syntax with SQL Server-specific features.
 *
 * SQL Server-specific features supported:
 * - OFFSET/FETCH: SQL Server 2012+ pagination syntax (SQL:2008 standard)
 * - TOP n: Simple limit without offset
 * - MERGE: SQL Server upsert syntax
 * - INFORMATION_SCHEMA: Column introspection
 * - Square brackets [column] for identifier quoting
 * - IDENTITY instead of AUTO_INCREMENT
 * - JSON_VALUE, OPENJSON: JSON support (SQL Server 2016+)
 *
 * SQL Server version requirements:
 * - 2012+: OFFSET/FETCH for pagination
 * - 2016+: JSON support, STRING_AGG
 * - 2017+: CONCAT_WS function
 */
class SqlsrvSyntax extends \Hubzero\Database\Drivers\Base\BaseSqlSyntax
{
    /**
     * Builds a limit statement from the set params
     *
     * SQL Server 2012+ uses OFFSET/FETCH syntax instead of LIMIT.
     * This requires an ORDER BY clause to be present.
     *
     * For simple TOP queries without offset, we use TOP.
     * For offset queries, we use OFFSET n ROWS FETCH NEXT n ROWS ONLY.
     *
     * @return  string
     **/
    public function buildLimit()
    {
        // When UNIONs are present, TOP cannot be used (it only applies
        // to the first SELECT). Use OFFSET 0 ROWS FETCH NEXT n instead.
        // Note: SQL Server requires ORDER BY items to be in the select
        // list when UNION is used, so use column ordinal (1) not (SELECT NULL).
        if (!empty($this->union) && !empty($this->limit) && empty($this->start)) {
            $parts = [];
            if (empty($this->order)) {
                $parts[] = 'ORDER BY 1';
            }
            $parts[] = 'OFFSET 0 ROWS FETCH NEXT '
                . (int) $this->limit . ' ROWS ONLY';
            return implode("\n", $parts);
        }

        // SQL Server requires ORDER BY for OFFSET/FETCH
        // If we have an offset, we must use OFFSET/FETCH syntax
        if (!empty($this->start)) {
            $parts = [];

            // SQL Server requires ORDER BY before OFFSET/FETCH.
            // If no ORDER BY was set, inject a no-op one.
            // With UNION, must use column ordinal instead of (SELECT NULL).
            if (empty($this->order)) {
                $parts[] = !empty($this->union)
                    ? 'ORDER BY 1'
                    : 'ORDER BY (SELECT NULL)';
            }

            $offset = 'OFFSET ' . (int) $this->start . ' ROWS';
            if (!empty($this->limit)) {
                $offset .= ' FETCH NEXT '
                    . (int) $this->limit . ' ROWS ONLY';
            }
            $parts[] = $offset;

            return implode("\n", $parts);
        }

        // For simple limit without offset, use TOP in the SELECT clause
        // (handled by buildSelect) — return empty here
        return '';
    }

    /**
     * Builds a select statement from the set params
     *
     * Override to support TOP syntax for simple limits without offset.
     *
     * @return  string
     **/
    protected function buildSelect()
    {
        // FULL JOIN uses parent's UNION emulation for cross-DB consistency
        if ($this->hasFullJoin()) {
            $this->validateFullJoinLayout();
        }

        $selects = [];

        foreach ($this->select as $select) {
            // Handle raw select expressions (subqueries, etc.)
            if (array_key_exists('raw', $select)) {
                $string = '(' . $select['raw'] . ')';

                // Add bindings from the raw expression
                foreach ($select['bindings'] as $binding) {
                    $this->bind($binding);
                }
            } elseif ($select['column'] instanceof \Hubzero\Database\Expression) {
                $string = $this->buildExpression($select['column']);
            } elseif (isset($select['count']) && $select['count'] === 'distinct') {
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

        // Handle TOP for simple limits (no offset).
        // Skip TOP when UNIONs are present — TOP only applies to the
        // first SELECT, not the combined result. UNION + LIMIT uses
        // OFFSET/FETCH in buildLimit() instead.
        $top = '';
        if (empty($this->start) && !empty($this->limit) && empty($this->union)) {
            $top = 'TOP ' . (int) $this->limit . ' ';
        }

        $distinct = $this->distinct ? 'DISTINCT ' : '';
        return 'SELECT ' . $distinct . $top . implode(',', $selects);
    }

    /**
     * Returns the proper query for generating a list of table columns
     *
     * SQL Server uses INFORMATION_SCHEMA instead of SHOW COLUMNS.
     *
     * @param   string  $table  The name of the database table
     * @return  string
     */
    public function getColumnsQuery($table)
    {
        return "SELECT
                    c.COLUMN_NAME as Field,
                    c.DATA_TYPE as Type,
                    c.IS_NULLABLE as [Null],
                    c.COLUMN_DEFAULT as [Default],
                    CASE WHEN pk.COLUMN_NAME IS NOT NULL
                        THEN 'PRI' ELSE '' END as [Key]
                FROM INFORMATION_SCHEMA.COLUMNS c
                LEFT JOIN (
                    SELECT ku.COLUMN_NAME
                    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc
                    INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE ku
                        ON tc.CONSTRAINT_NAME = ku.CONSTRAINT_NAME
                    WHERE tc.CONSTRAINT_TYPE = 'PRIMARY KEY'
                        AND tc.TABLE_NAME = "
            . $this->connection->quote($table) . "
                ) pk ON c.COLUMN_NAME = pk.COLUMN_NAME
                WHERE c.TABLE_NAME = "
            . $this->connection->quote($table);
    }

    /**
     * Normalizes the results of the columns query
     *
     * @param   array  $data      The raw column data
     * @param   bool   $typeOnly  True (default) to only return field types
     * @return  array
     **/
    public function normalizeColumns($data, $typeOnly = true)
    {
        $results = [];

        if ($typeOnly) {
            foreach ($data as $field) {
                $results[$field->Field] = $field->Type;
            }
        } else {
            foreach ($data as $field) {
                $results[$field->Field] = [
                    'name'      => $field->Field,
                    'type'      => $field->Type,
                    'allownull' => ($field->Null == 'NO') ? false : true,
                    'default'   => $field->Default,
                    'pk'        => ($field->Key == 'PRI') ? true : false
                ];
            }
        }

        return $results;
    }

    /**
     * Gets the query for checking table existence
     *
     * @param   string  $table  The table name
     * @return  string
     */
    public function getTableExistsQuery($table)
    {
        return "SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = " .
               $this->connection->quote($table);
    }

    /**
     * Gets the query for listing all tables
     *
     * @return  string
     */
    public function getTableListQuery()
    {
        return "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'";
    }

    // =========================================================================
    // JSON Operations - SQL Server specific overrides
    // =========================================================================

    /**
     * Sets a JSON path extraction where clause
     *
     * SQL Server uses JSON_VALUE() for scalar values at a path.
     * Path syntax uses $ root with dot notation.
     *
     * Requires SQL Server 2016+.
     *
     * @param   string  $column    The JSON column name
     * @param   string  $path      The dot-notation path to the value
     * @param   string  $operator  Comparison operator
     * @param   mixed   $value     The value to compare against
     * @param   string  $logical   The operator between multiple clauses
     * @param   int     $depth     The depth level of the clause
     * @return  void
     */
    public function setJsonPathWhere($column, $path, $operator, $value, $logical = 'and', $depth = 0)
    {
        $jsonPath = $this->convertToJsonPath($path);
        $quotedColumn = $this->connection->quoteName($column);

        // JSON_VALUE extracts a scalar value from JSON
        $raw = "JSON_VALUE({$quotedColumn}, '{$jsonPath}') {$operator} ?";

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [$value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    /**
     * Sets a JSON contains where clause
     *
     * SQL Server uses OPENJSON() with EXISTS to check array membership.
     * No native containment operator exists.
     *
     * Requires SQL Server 2016+.
     *
     * @param   string       $column   The JSON column name
     * @param   mixed        $value    The value to search for
     * @param   string|null  $path     Optional dot-notation path to a nested array
     * @param   string       $logical  The operator between multiple clauses
     * @param   int          $depth    The depth level of the clause
     * @return  void
     */
    public function setJsonContainsWhere($column, $value, $path = null, $logical = 'and', $depth = 0)
    {
        $quotedColumn = $this->connection->quoteName($column);

        if ($path !== null) {
            $jsonPath = $this->convertToJsonPath($path);
            // JSON_QUERY extracts a JSON array, then OPENJSON parses it
            $raw = "EXISTS (SELECT 1 FROM OPENJSON(JSON_QUERY({$quotedColumn}, '{$jsonPath}')) WHERE value = ?)";
        } else {
            // Parse top-level array
            $raw = "EXISTS (SELECT 1 FROM OPENJSON({$quotedColumn}) WHERE value = ?)";
        }

        // Handle value binding - convert objects/arrays to JSON
        if (is_array($value) || is_object($value)) {
            $bindValue = json_encode($value);
        } else {
            $bindValue = $value;
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [$bindValue],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    /**
     * Sets a JSON length where clause
     *
     * SQL Server uses a COUNT from OPENJSON() to get array length.
     *
     * Requires SQL Server 2016+.
     *
     * @param   string       $column    The JSON column name
     * @param   string       $operator  Comparison operator
     * @param   int          $value     The length value to compare against
     * @param   string|null  $path      Optional dot-notation path to a nested array
     * @param   string       $logical   The operator between multiple clauses
     * @param   int          $depth     The depth level of the clause
     * @return  void
     */
    public function setJsonLengthWhere($column, $operator, $value, $path = null, $logical = 'and', $depth = 0)
    {
        $quotedColumn = $this->connection->quoteName($column);

        if ($path !== null) {
            $jsonPath = $this->convertToJsonPath($path);
            // Use JSON_QUERY to get the array at path, then count with OPENJSON
            $raw = "(SELECT COUNT(*) FROM OPENJSON(JSON_QUERY({$quotedColumn}, '{$jsonPath}'))) {$operator} ?";
        } else {
            // Count top-level array
            $raw = "(SELECT COUNT(*) FROM OPENJSON({$quotedColumn})) {$operator} ?";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [(int) $value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    // =========================================================================
    // Date/Time Operations - SQL Server specific overrides
    // =========================================================================

    /**
     * Sets a date/time extraction where clause
     *
     * SQL Server uses CAST for date/time and YEAR(), MONTH(), DAY() functions.
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
                $raw = "CAST({$quotedColumn} AS DATE) {$operator} ?";
                break;
            case 'time':
                $raw = "CAST({$quotedColumn} AS TIME) {$operator} ?";
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
                $raw = "CAST({$quotedColumn} AS DATE) {$operator} ?";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [$value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    // =========================================================================
    // Table Operations - SQL Server specific overrides
    // =========================================================================

    /**
     * Get the SQL statements to truncate a table
     *
     * SQL Server uses standard TRUNCATE TABLE syntax.
     *
     * @param   string  $table  The table to truncate
     * @return  array   Array of SQL statements to execute
     */
    public function getTruncateStatements($table)
    {
        return ['TRUNCATE TABLE ' . $this->connection->quoteName($table)];
    }

    /**
     * Build a CONCAT expression for SQL Server
     *
     * SQL Server 2012+ supports CONCAT() function with multiple arguments.
     * For older versions, use + operator (requires NULL handling).
     *
     * @param   array  $parts  Array of column names or quoted strings
     * @return  string
     */
    public function buildConcat(array $parts)
    {
        // SQL Server 2012+ supports CONCAT() with multiple arguments
        return 'CONCAT(' . implode(', ', $parts) . ')';
    }

    /**
     * SQL Server has no native INSERT IGNORE syntax.
     * INSERT...SELECT must use row-by-row to skip duplicates.
     *
     * @return bool
     */
    public function needsRowByRowInsertIgnore(): bool
    {
        return $this->ignore && $this->insertSelectQuery !== null;
    }

    /**
     * Builds an upsert statement from the set params
     *
     * SQL Server uses the MERGE statement for upsert operations.
     * Requires conflict columns to be specified for the match condition.
     *
     * @return  string
     */
    public function buildUpsert()
    {
        $columns = array_keys($this->upsertValues);
        $quotedColumns = [];
        $sourceValues = [];

        foreach ($columns as $column) {
            $quotedColumns[] = $this->connection->quoteName($column);
            $sourceValues[] = '? AS ' . $this->connection->quoteName($column);
            $this->bind(
                is_string($this->upsertValues[$column])
                    ? trim($this->upsertValues[$column])
                    : $this->upsertValues[$column]
            );
        }

        // SQL Server requires conflict columns for the MERGE match condition
        $conflictColumns = !empty($this->upsertConflictColumns)
            ? $this->upsertConflictColumns
            : [$columns[0]];

        $matchConditions = [];
        foreach ($conflictColumns as $col) {
            $quotedCol = $this->connection->quoteName($col);
            $matchConditions[] = "target.$quotedCol = source.$quotedCol";
        }

        $updateSet = [];
        foreach ($this->upsertUpdateColumns as $col) {
            $quotedCol = $this->connection->quoteName($col);
            $updateSet[] = "target.$quotedCol = source.$quotedCol";
        }

        $insertColumns = implode(', ', $quotedColumns);
        $insertValues = implode(', ', array_map(
            fn($col) => 'source.' . $this->connection->quoteName($col),
            $columns
        ));

        return sprintf(
            'MERGE INTO %s AS target USING (SELECT %s) AS source ON (%s) ' .
            'WHEN MATCHED THEN UPDATE SET %s ' .
            'WHEN NOT MATCHED THEN INSERT (%s) VALUES (%s);',
            $this->connection->quoteName($this->upsertTable),
            implode(', ', $sourceValues),
            implode(' AND ', $matchConditions),
            implode(', ', $updateSet),
            $insertColumns,
            $insertValues
        );
    }

    /**
     * Builds a bulk upsert statement
     *
     * SQL Server uses MERGE with a UNION ALL source:
     * MERGE INTO t AS target USING (SELECT ? AS c1, ? AS c2
     *   UNION ALL SELECT ?, ?) AS source ON (...) WHEN
     *   MATCHED/NOT MATCHED ...;
     *
     * @return  string
     */
    public function buildUpsertMany()
    {
        if (empty($this->upsertManyRows)) {
            return '';
        }

        $columns = array_keys($this->upsertManyRows[0]);
        $quotedColumns = array_map(
            fn($col) => $this->connection->quoteName($col),
            $columns
        );

        $conflictColumns = !empty($this->upsertManyConflictColumns)
            ? $this->upsertManyConflictColumns
            : [$columns[0]];

        // Build UNION ALL source rows
        $sourceRows = [];
        foreach ($this->upsertManyRows as $row) {
            $sourceCols = [];
            foreach ($columns as $column) {
                $sourceCols[] = '? AS '
                    . $this->connection->quoteName($column);
                $value = $row[$column] ?? null;
                $this->bind(
                    is_string($value) ? trim($value) : $value
                );
            }
            $sourceRows[] = 'SELECT ' . implode(', ', $sourceCols);
        }

        $matchConditions = [];
        foreach ($conflictColumns as $col) {
            $q = $this->connection->quoteName($col);
            $matchConditions[] = "target.$q = source.$q";
        }

        $updateSet = [];
        foreach ($this->upsertManyUpdateColumns as $col) {
            $q = $this->connection->quoteName($col);
            $updateSet[] = "target.$q = source.$q";
        }

        $insertValues = array_map(
            fn($c) => 'source.' . $this->connection->quoteName($c),
            $columns
        );

        return sprintf(
            'MERGE INTO %s AS target'
            . ' USING (%s) AS source ON (%s)'
            . ' WHEN MATCHED THEN UPDATE SET %s'
            . ' WHEN NOT MATCHED THEN INSERT (%s) VALUES (%s);',
            $this->connection->quoteName($this->upsertManyTable),
            implode(' UNION ALL ', $sourceRows),
            implode(' AND ', $matchConditions),
            implode(', ', $updateSet),
            implode(', ', $quotedColumns),
            implode(', ', $insertValues)
        );
    }

    /**
     * Build a function expression into SQL Server-specific SQL
     *
     * @param   \Hubzero\Database\Expression  $expression  The function expression
     * @return  string  The SQL representation
     */
    protected function buildFunctionExpression($expression): string
    {
        $function = $expression->getFunction();
        $args = $expression->getArguments();

        switch ($function) {
            case 'MOD':
                return '(' . $this->buildExpressionArgument($args[0])
                    . ' % ' . $this->buildExpressionArgument($args[1]) . ')';

            case 'NEXTVAL':
                return 'NEXT VALUE FOR '
                    . $this->connection->quoteName($args[0]);

            case 'CURRVAL':
                return '(SELECT current_value FROM sys.sequences'
                    . ' WHERE name = '
                    . $this->connection->quote($args[0]) . ')';

            // NULL handling
            case 'IFNULL':
            case 'COALESCE':
                return 'ISNULL(' . $this->buildExpressionArgument($args[0])
                    . ', ' . $this->buildExpressionArgument($args[1]) . ')';

            // String functions
            case 'LENGTH':
            case 'CHAR_LENGTH':
                return 'LEN(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'SUBSTRING':
                return 'SUBSTRING('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ', '
                    . $this->buildExpressionArgument($args[2]) . ')';

            case 'CONCAT':
                $parts = array_map(
                    fn($a) => $this->buildExpressionArgument($a),
                    $args
                );
                return 'CONCAT(' . implode(', ', $parts) . ')';

            // Date/time functions
            case 'NOW':
            case 'CURRENT_TIMESTAMP':
                return 'GETDATE()';

            case 'DATE':
                return 'CAST('
                    . $this->buildExpressionArgument($args[0])
                    . ' AS DATE)';

            case 'TIME':
                return 'CAST('
                    . $this->buildExpressionArgument($args[0])
                    . ' AS TIME)';

            case 'YEAR':
            case 'MONTH':
            case 'DAY':
            case 'HOUR':
            case 'MINUTE':
            case 'SECOND':
                return 'DATEPART(' . $function . ', '
                    . $this->buildExpressionArgument($args[0]) . ')';

            // Math functions
            case 'CEIL':
                return 'CEILING('
                    . $this->buildExpressionArgument($args[0]) . ')';

            default:
                return parent::buildFunctionExpression($expression);
        }
    }
}
