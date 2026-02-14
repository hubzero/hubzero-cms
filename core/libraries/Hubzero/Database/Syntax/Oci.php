<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Syntax;

/**
 * Database Oracle query syntax class
 *
 * Oracle Database is a multi-model database management system produced and
 * marketed by Oracle Corporation. This class extends the base SQL syntax
 * with Oracle-specific features.
 *
 * Oracle-specific features supported:
 * - OFFSET/FETCH: Oracle 12c+ pagination syntax (SQL:2008 standard)
 * - MERGE: Oracle upsert syntax with DUAL table
 * - USER_TAB_COLUMNS: Column introspection
 * - Sequences instead of AUTO_INCREMENT
 * - TO_DATE/TO_CHAR for date formatting
 * - NVL instead of IFNULL
 * - DUAL table for SELECT without FROM
 *
 * Oracle SQL features supported:
 * - Standard DML: SELECT, INSERT, UPDATE, DELETE with full clause support
 * - All JOIN types including FULL OUTER JOIN
 * - Analytic/Window functions (ROW_NUMBER, RANK, etc.)
 * - Common Table Expressions (WITH clause)
 * - PL/SQL integration
 */
class Oci extends Sql
{
    /**
     * Oracle type mappings to normalized types
     *
     * Maps Oracle-specific types to generic type names
     * that match what other drivers return.
     *
     * @var array
     */
    protected static $typeMappings = [
        // Integer types
        'number'    => 'int',
        'integer'   => 'int',
        'smallint'  => 'smallint',
        'binary_float'  => 'float',
        'binary_double' => 'double',

        // Character types
        'varchar2'  => 'varchar',
        'nvarchar2' => 'varchar',
        'char'      => 'char',
        'nchar'     => 'char',
        'clob'      => 'text',
        'nclob'     => 'text',
        'long'      => 'text',

        // Binary types
        'blob'      => 'blob',
        'raw'       => 'blob',
        'long raw'  => 'blob',
        'bfile'     => 'blob',

        // Date/time types
        'date'                       => 'datetime',
        'timestamp'                  => 'datetime',
        'timestamp with time zone'   => 'datetime',
        'timestamp with local time zone' => 'datetime',
        'interval year to month'     => 'varchar',
        'interval day to second'     => 'varchar',

        // Other types
        'rowid'     => 'varchar',
        'urowid'    => 'varchar',
        'xmltype'   => 'text',
    ];

    /**
     * Builds a select statement for Oracle
     *
     * Overrides the base buildSelect() to quote plain column references.
     * Oracle has many reserved words (ACCESS, COMMENT, etc.) that cause
     * ORA-00936 errors when used unquoted as column names. This method
     * detects plain column names and table.column references and quotes
     * them with uppercase Oracle identifiers.
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
                $string = '(' . $select['raw'] . ')';

                // Add bindings from the raw expression
                foreach ($select['bindings'] as $binding) {
                    $this->bind($binding);
                }
            } elseif ($select['column'] instanceof \Hubzero\Database\Expression) {
                $string = $this->buildExpression($select['column']);
            } elseif (isset($select['count']) && $select['count'] == 'distinct') {
                $string = "COUNT(DISTINCT({$select['column']}))";
            } elseif (!empty($select['count'])) {
                $string = "COUNT({$select['column']})";
            } else {
                // Quote plain column references for Oracle
                $string = $this->quoteSelectColumns($select['column']);
            }

            // See if we're including an alias
            if (isset($select['as']) && $select['as'] !== null) {
                $string .= " AS {$select['as']}";
            }

            $selects[] = $string;
        }

        $distinct = $this->distinct ? 'DISTINCT ' : '';
        return 'SELECT ' . $distinct . implode(',', $selects);
    }

    /**
     * Quote column references in a SELECT column expression
     *
     * Handles single columns, table.column references, and comma-separated
     * lists. Leaves expressions (containing parentheses, operators, etc.)
     * unquoted.
     *
     * @param   string  $column  The column expression
     * @return  string  The quoted column expression
     */
    protected function quoteSelectColumns(string $column): string
    {
        // If it contains commas (but not inside parentheses), split and quote each part
        if (strpos($column, ',') !== false && !$this->commaIsInsideParens($column)) {
            $parts = $this->splitSelectColumns($column);
            $quoted = [];
            foreach ($parts as $part) {
                $quoted[] = $this->quoteSelectColumns(trim($part));
            }
            return implode(', ', $quoted);
        }

        $column = trim($column);

        // Empty string
        if ($column === '') {
            return $column;
        }

        // Wildcards: * or table.*
        if ($column === '*' || substr($column, -2) === '.*') {
            if ($column === '*') {
                return '*';
            }
            // table.* — quote the table part
            $table = substr($column, 0, -2);
            return $this->connection->quoteName($table) . '.*';
        }

        // Numeric literals (1, 42, 3.14) — leave as-is
        if (is_numeric($column)) {
            return $column;
        }

        // String literals ('text') — leave as-is
        if ($column[0] === "'" && substr($column, -1) === "'") {
            return $column;
        }

        // Already quoted identifiers — leave as-is
        if ($column[0] === '"' && substr($column, -1) === '"') {
            return $column;
        }

        // If it contains parentheses, it's a function/expression — leave as-is
        if (strpos($column, '(') !== false) {
            return $column;
        }

        // If it contains SQL operators beyond simple identifiers, leave as-is
        if (preg_match('/[+\-\/|<>=!?]/', $column)) {
            return $column;
        }

        // If it contains whitespace, check for alias patterns
        if (preg_match('/\s/', $column)) {
            // "column AS alias" or "column alias"
            if (preg_match('/^(\S+)\s+(?:as\s+)?(\S+)$/i', $column, $m)) {
                return $this->quoteSelectColumns($m[1])
                    . ' ' . $this->connection->quoteName($m[2]);
            }
            // Complex expression with spaces — leave as-is
            return $column;
        }

        // Simple column name or table.column — quote it
        return $this->connection->quoteName($column);
    }

    /**
     * Check if a comma exists outside of parentheses in a string
     *
     * @param   string  $str  The string to check
     * @return  bool    True if all commas are inside parentheses
     */
    private function commaIsInsideParens(string $str): bool
    {
        $depth = 0;
        for ($i = 0, $len = strlen($str); $i < $len; $i++) {
            if ($str[$i] === '(') {
                $depth++;
            } elseif ($str[$i] === ')') {
                $depth--;
            } elseif ($str[$i] === ',' && $depth === 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Split a comma-separated column list, respecting parentheses
     *
     * @param   string  $columns  The comma-separated column string
     * @return  array   Array of column expressions
     */
    private function splitSelectColumns(string $columns): array
    {
        $parts = [];
        $current = '';
        $depth = 0;

        for ($i = 0, $len = strlen($columns); $i < $len; $i++) {
            $ch = $columns[$i];
            if ($ch === '(') {
                $depth++;
                $current .= $ch;
            } elseif ($ch === ')') {
                $depth--;
                $current .= $ch;
            } elseif ($ch === ',' && $depth === 0) {
                $parts[] = $current;
                $current = '';
            } else {
                $current .= $ch;
            }
        }
        if ($current !== '') {
            $parts[] = $current;
        }

        return $parts;
    }

    /**
     * Builds a FROM clause for Oracle
     *
     * Oracle does not support AS for table aliases (before 23ai).
     * Uses space instead: FROM "TABLE" "ALIAS"
     *
     * @return  string
     */
    protected function buildFrom()
    {
        if ($this->fullJoinUnionBuilt) {
            return '';
        }

        $froms = [];

        foreach ($this->from as $from) {
            if (array_key_exists('raw', $from)) {
                $string = '(' . $from['raw'] . ')';

                foreach ($from['bindings'] as $binding) {
                    $this->bind($binding);
                }

                // Oracle uses space, not AS, for table aliases
                if (isset($from['as'])) {
                    $string .= ' '
                        . $this->connection->quoteName($from['as']);
                }
            } else {
                $string = $this->connection->quoteName(
                    $from['table']
                );

                // Oracle uses space, not AS, for table aliases
                if (isset($from['as'])) {
                    $string .= ' '
                        . $this->connection->quoteName($from['as']);
                }
            }

            $froms[] = $string;
        }

        return 'FROM ' . implode(',', $froms);
    }

    /**
     * Builds JOIN clauses for Oracle
     *
     * Oracle does not support AS for table aliases (before 23ai).
     * Uses space instead for subquery join aliases.
     *
     * For UPDATE with JOINs, the join is handled in buildWhere()
     * via a ROWID subquery, so this method is only called from
     * there (not from buildQuery directly).
     *
     * @return  string
     */
    public function buildJoin()
    {
        $joins = [];

        foreach ($this->join as $join) {
            if (isset($join['subquery'])) {
                // Subquery join — Oracle uses space, not AS
                $tableExpr = '(' . $join['subquery'] . ') '
                    . $this->connection->quoteName($join['as']);

                foreach ($join['bindings'] as $binding) {
                    $this->bind($binding);
                }

                $joins[] = strtoupper($join['type'])
                    . ' JOIN ' . $tableExpr
                    . ' ON '
                    . $this->connection->quoteName($join['left'])
                    . ' = '
                    . $this->connection->quoteName(
                        $join['right']
                    );
            } elseif (isset($join['raw'])) {
                $joins[] = strtoupper($join['type'])
                    . ' JOIN ' . $join['table']
                    . ' ON ' . $join['raw'];
            } elseif (isset($join['conditions'])) {
                $tableExpr = $this->connection->wrap(
                    $join['table']
                );
                if (!empty($join['alias'])) {
                    $tableExpr .= ' '
                        . $this->connection->quoteName(
                            $join['alias']
                        );
                }

                $joins[] = strtoupper($join['type'])
                    . ' JOIN ' . $tableExpr
                    . ' ON '
                    . $this->buildJoinConditions(
                        $join['conditions']
                    );
            } else {
                $tableExpr = $this->connection->wrap(
                    $join['table']
                );
                if (!empty($join['alias'])) {
                    $tableExpr .= ' '
                        . $this->connection->quoteName(
                            $join['alias']
                        );
                }

                $joins[] = strtoupper($join['type'])
                    . ' JOIN ' . $tableExpr
                    . ' ON '
                    . $this->connection->quoteName($join['left'])
                    . ' = '
                    . $this->connection->quoteName(
                        $join['right']
                    );
            }
        }

        return implode("\n", $joins);
    }

    /**
     * Build EXISTS clause for Oracle join conditions
     *
     * Oracle requires FROM DUAL for SELECT without FROM.
     *
     * @param   string  $operatorLower  'exists' or 'not exists'
     * @param   mixed   $subquery
     * @return  string
     */
    protected function buildJoinExistsClause(
        string $operatorLower,
        $subquery
    ): string {
        if (
            is_object($subquery)
            && method_exists($subquery, 'build')
        ) {
            $sql = $subquery->build($this);
        } else {
            $sql = (string) $subquery;
        }

        $sql = trim($sql);
        if ($sql === '') {
            throw new \InvalidArgumentException(
                'EXISTS join conditions require a subquery expression.'
            );
        }

        // Oracle requires FROM DUAL for bare SELECTs
        if (
            preg_match(
                '/^SELECT\s+(.+?)$/i',
                $sql,
                $matches
            ) && !preg_match('/\bFROM\b/i', $sql)
        ) {
            $sql = rtrim($sql) . ' FROM DUAL';
        }

        if ($sql[0] !== '(') {
            $sql = '(' . $sql . ')';
        }

        return ($operatorLower === 'not exists'
            ? 'NOT EXISTS ' : 'EXISTS ') . $sql;
    }

    /**
     * Builds a WHERE clause for Oracle
     *
     * For UPDATE queries with JOINs, rewrites to use a subquery:
     * WHERE ROWID IN (SELECT t.ROWID FROM t JOIN ... WHERE ...)
     *
     * @return  string
     */
    protected function buildWhere()
    {
        // UPDATE with JOINs — rewrite to subquery
        if (!empty($this->update) && !empty($this->join)) {
            $updateTable = $this->update;
            $quotedTable = $this->connection->quoteName(
                $updateTable
            );

            $subquery = "SELECT " . $quotedTable . ".ROWID"
                . " FROM " . $quotedTable;
            $subquery .= ' ' . $this->buildJoin();

            $originalWhere = $this->buildWhereClause();
            if ($originalWhere) {
                $originalWhere = preg_replace(
                    '/^WHERE\\s+/i',
                    '',
                    $originalWhere
                );
                $subquery .= ' WHERE ' . $originalWhere;
            }

            return "WHERE ROWID IN ({$subquery})";
        }

        return $this->buildWhereClause();
    }

    /**
     * Returns the proper query for generating a list of table columns
     *
     * Oracle uses USER_TAB_COLUMNS for column introspection
     *
     * @param   string  $table  The name of the database table
     * @return  string
     */
    public function getColumnsQuery($table)
    {
        // Oracle stores table names in uppercase by default
        $upperTable = strtoupper($table);

        // Column aliases are lowercase to match PDO::ATTR_CASE = CASE_LOWER
        return "SELECT
                column_name AS field,
                data_type || CASE
                    WHEN data_type IN ('VARCHAR2', 'NVARCHAR2', 'CHAR', 'NCHAR', 'RAW')
                    THEN '(' || data_length || ')'
                    WHEN data_type = 'NUMBER' AND data_precision IS NOT NULL
                    THEN '(' || data_precision || CASE WHEN data_scale > 0 THEN ',' || data_scale ELSE '' END || ')'
                    ELSE ''
                END AS type,
                CASE WHEN nullable = 'Y' THEN 'YES' ELSE 'NO' END AS nullable_flag,
                data_default AS default_value,
                '' AS key_type
            FROM user_tab_columns
            WHERE table_name = " . $this->connection->quote($upperTable) . "
            ORDER BY column_id";
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
                // Lowercase key for ORM compatibility
                $name = strtolower($field->field);
                $results[$name] = $this->normalizeType($field->type);
            }
        } else {
            foreach ($data as $field) {
                $name = strtolower($field->field);
                $results[$name] = [
                    'name'      => $name,
                    'type'      => $this->normalizeType($field->type),
                    'raw_type'  => $field->type,
                    'allownull' => (strtoupper($field->nullable_flag ?? '') === 'YES'),
                    'default'   => $this->normalizeDefault($field->default_value ?? null),
                    'pk'        => (($field->key_type ?? '') === 'PRI')
                ];
            }
        }

        return $results;
    }

    /**
     * Normalize an Oracle type to a generic type name
     *
     * @param   string  $type  The Oracle type
     * @return  string  The normalized type
     */
    protected function normalizeType($type)
    {
        if (empty($type)) {
            return 'text';
        }

        // Extract base type (before parentheses)
        $type = strtolower(trim($type));
        $baseType = preg_replace('/\(.*\)/', '', $type);
        $baseType = trim($baseType);

        // Check for exact match first
        if (isset(self::$typeMappings[$baseType])) {
            return self::$typeMappings[$baseType];
        }

        // Handle NUMBER without precision as int
        if ($baseType === 'number') {
            return 'int';
        }

        // Default to text for unknown types
        return 'text';
    }

    /**
     * Normalize a default value from Oracle
     *
     * Oracle includes trailing spaces and may have type-specific formatting
     *
     * @param   mixed  $value  The raw default value
     * @return  mixed  The normalized default value
     */
    protected function normalizeDefault($value)
    {
        if ($value === null) {
            return null;
        }

        // Trim whitespace
        $value = trim($value);

        // Check for NULL literal
        if (strtoupper($value) === 'NULL') {
            return null;
        }

        // Remove surrounding quotes for string defaults
        if (preg_match("/^'(.*)'$/s", $value, $matches)) {
            return str_replace("''", "'", $matches[1]);
        }

        return $value;
    }

    // =========================================================================
    // JSON Operations - Oracle specific overrides
    // =========================================================================

    /**
     * Sets a JSON path extraction where clause
     *
     * Oracle 12c+ uses JSON_VALUE() function with path syntax
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

        // Oracle uses JSON_VALUE() for scalar extraction
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
     * Oracle 12c+ uses JSON_EXISTS() for containment checks
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

        // JSON value must be encoded for comparison
        $jsonValue = json_encode($value);

        if ($path !== null) {
            $jsonPath = $this->convertToJsonPath($path);
            // Use JSON_EXISTS with a path filter
            $raw = "JSON_EXISTS({$quotedColumn}, '{$jsonPath}?(@ == \$v)' PASSING ? AS \"v\")";
        } else {
            // Check if the value exists anywhere in the document
            $raw = "JSON_EXISTS({$quotedColumn}, '\$?(@ == \$v)' PASSING ? AS \"v\")";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [$jsonValue],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    /**
     * Sets a JSON length where clause
     *
     * Oracle uses JSON_QUERY to get array and check cardinality
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
            // Oracle 12.2+ supports JSON array length via subquery
            $raw = "(SELECT COUNT(*) FROM JSON_TABLE("
                . "{$quotedColumn}, '{$jsonPath}[*]' COLUMNS"
                . " (val VARCHAR2(4000) PATH '\$')))"
                . " {$operator} ?";
        } else {
            $raw = "(SELECT COUNT(*) FROM JSON_TABLE("
                . "{$quotedColumn}, '\$[*]' COLUMNS"
                . " (val VARCHAR2(4000) PATH '\$')))"
                . " {$operator} ?";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [(int) $value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    // =========================================================================
    // Date/Time Operations - Oracle specific overrides
    // =========================================================================

    /**
     * Sets a date/time extraction where clause
     *
     * Oracle uses TRUNC() for date and EXTRACT() for parts
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
                $raw = "TRUNC({$quotedColumn}) {$operator} TO_DATE(?, 'YYYY-MM-DD')";
                break;
            case 'time':
                $raw = "TO_CHAR({$quotedColumn}, 'HH24:MI:SS') {$operator} ?";
                break;
            case 'year':
                $raw = "EXTRACT(YEAR FROM {$quotedColumn}) {$operator} ?";
                break;
            case 'month':
                $raw = "EXTRACT(MONTH FROM {$quotedColumn}) {$operator} ?";
                break;
            case 'day':
                $raw = "EXTRACT(DAY FROM {$quotedColumn}) {$operator} ?";
                break;
            default:
                $raw = "TRUNC({$quotedColumn}) {$operator} TO_DATE(?, 'YYYY-MM-DD')";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [$value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    // =========================================================================
    // Table Operations - Oracle specific overrides
    // =========================================================================

    /**
     * Returns the proper query to check if a table exists
     *
     * @param   string  $table  The name of the database table
     * @return  string
     */
    public function getTableExistsQuery($table)
    {
        return "SELECT COUNT(*) FROM user_tables WHERE table_name = "
            . $this->connection->quote(strtoupper($table));
    }

    /**
     * Returns the proper query to get a list of all tables
     *
     * @return  string
     */
    public function getTableListQuery()
    {
        return "SELECT table_name FROM user_tables ORDER BY table_name";
    }

    /**
     * Get the SQL statements to truncate a table
     *
     * Oracle uses simple TRUNCATE TABLE syntax
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
     * Oracle uses MERGE statement for upsert operations
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

        // Build source SELECT with bindings
        $sourceColumns = [];
        foreach ($columns as $column) {
            $sourceColumns[] = '? AS ' . $this->connection->quoteName($column);
            $this->bind(
                is_string($this->upsertValues[$column])
                    ? trim($this->upsertValues[$column])
                    : $this->upsertValues[$column]
            );
        }

        $sql = 'MERGE INTO ' . $this->connection->quoteName($this->upsertTable) . ' t ' .
               'USING (SELECT ' . implode(', ', $sourceColumns) . ' FROM DUAL) s ' .
               'ON (' . implode(' AND ', $onConditions) . ') ' .
               'WHEN MATCHED THEN UPDATE SET ' . implode(', ', $updateSets) . ' ' .
               'WHEN NOT MATCHED THEN INSERT (' . implode(', ', $quotedColumns) . ') ' .
               'VALUES (' . implode(', ', array_map(fn($c) => 's.' . $this->connection->quoteName($c), $columns)) . ')';

        return $sql;
    }

    /**
     * Builds a bulk upsert statement
     *
     * Oracle uses MERGE with a UNION ALL source from DUAL:
     * MERGE INTO t USING (SELECT ? AS c1 FROM DUAL UNION ALL
     *   SELECT ? FROM DUAL) s ON (t.key = s.key) WHEN MATCHED ...
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
            $sourceRows[] = 'SELECT ' . implode(', ', $sourceCols)
                . ' FROM DUAL';
        }

        $onConditions = [];
        foreach ($conflictColumns as $col) {
            $q = $this->connection->quoteName($col);
            $onConditions[] = "t.$q = s.$q";
        }

        $updateSets = [];
        foreach ($this->upsertManyUpdateColumns as $col) {
            $q = $this->connection->quoteName($col);
            $updateSets[] = "t.$q = s.$q";
        }

        $insertValues = array_map(
            fn($c) => 's.' . $this->connection->quoteName($c),
            $columns
        );

        return 'MERGE INTO '
            . $this->connection->quoteName($this->upsertManyTable)
            . ' t USING (' . implode(' UNION ALL ', $sourceRows)
            . ') s ON (' . implode(' AND ', $onConditions) . ') '
            . 'WHEN MATCHED THEN UPDATE SET '
            . implode(', ', $updateSets) . ' '
            . 'WHEN NOT MATCHED THEN INSERT ('
            . implode(', ', $quotedColumns) . ') VALUES ('
            . implode(', ', $insertValues) . ')';
    }

    // =========================================================================
    // Bulk Insert Operations - Oracle specific overrides
    // =========================================================================

    /**
     * Oracle has no INSERT IGNORE syntax. Single-row inserts catch
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
     * Builds a bulk insert statement from the set params
     *
     * Oracle uses INSERT ALL syntax for multi-row inserts
     *
     * @return  string
     */
    public function buildInsertMany()
    {
        if (empty($this->insertManyRows)) {
            return '';
        }

        $columns = array_keys($this->insertManyRows[0]);

        // Oracle INSERT ALL generates the same identity value
        // for all rows (known limitation). Check if the table
        // has an identity column and the data doesn't include
        // explicit ID values — if so, fall back to individual
        // inserts via the empty-string return path.
        if ($this->tableHasIdentityConflict($columns)) {
            return '';
        }

        $quotedColumns = [];
        foreach ($columns as $column) {
            $quotedColumns[] = $this->connection->quoteName(
                $column
            );
        }

        $quotedTable = $this->connection->quoteName(
            $this->insertManyTable
        );
        $columnList = implode(', ', $quotedColumns);

        // Oracle uses INSERT ALL ... SELECT FROM DUAL
        $sql = 'INSERT ALL' . "\n";

        foreach ($this->insertManyRows as $row) {
            $placeholders = [];
            foreach ($columns as $column) {
                $placeholders[] = '?';
                $value = $row[$column] ?? null;
                $this->bind(
                    is_string($value) ? trim($value) : $value
                );
            }
            $sql .= "INTO {$quotedTable} ({$columnList})"
                . " VALUES ("
                . implode(', ', $placeholders) . ")\n";
        }

        $sql .= 'SELECT 1 FROM DUAL';

        return $sql;
    }

    /**
     * Check if INSERT ALL would conflict with identity column
     *
     * Returns true if the table has an identity column and the
     * insert data does NOT include explicit values for it.
     *
     * @param   array  $columns  Column names in the insert data
     * @return  bool
     */
    protected function tableHasIdentityConflict(
        array $columns
    ): bool {
        $table = strtoupper(
            $this->connection->replacePrefix($this->insertManyTable)
        );

        // Check for identity column
        $this->connection->setQuery(
            "SELECT column_name FROM user_tab_identity_cols"
            . " WHERE table_name = "
            . $this->connection->quote($table)
        );
        $identityCol = $this->connection->loadResult();

        if (!$identityCol) {
            return false;
        }

        // If the insert data includes the identity column,
        // INSERT ALL is safe (explicit values, no auto-gen)
        $upperColumns = array_map('strtoupper', $columns);
        return !in_array(
            strtoupper($identityCol),
            $upperColumns,
            true
        );
    }

    // =========================================================================
    // Expression Builder Support - Oracle specific overrides
    // =========================================================================

    /**
     * Build a function expression into Oracle-specific SQL
     *
     * Overrides MySQL syntax for functions that differ in Oracle:
     * - CONCAT: Uses || operator or CONCAT() (only 2 args)
     * - IFNULL: Uses NVL()
     * - LENGTH: Uses LENGTH() (same as MySQL)
     * - Date parts: Uses EXTRACT(part FROM column)
     * - DATE_FORMAT: Uses TO_CHAR()
     *
     * @param   \Hubzero\Database\Expression  $expression  The function expression
     * @return  string  The SQL representation
     */
    protected function buildFunctionExpression($expression): string
    {
        $function = $expression->getFunction();
        $args = $expression->getArguments();

        switch ($function) {
            // String functions
            case 'CONCAT':
                // Oracle's CONCAT() only takes 2 arguments
                // For multiple args, chain them with ||
                $parts = array_map([$this, 'buildExpressionArgument'], $args);
                return '(' . implode(' || ', $parts) . ')';

            case 'LENGTH':
                // Oracle uses LENGTH() same as MySQL
                return 'LENGTH(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'SUBSTRING':
                // Oracle uses SUBSTR()
                $col = $this->buildExpressionArgument($args[0]);
                $start = (int) $args[1];
                if ($args[2] !== null) {
                    return "SUBSTR({$col}, {$start}, " . (int) $args[2] . ')';
                }
                return "SUBSTR({$col}, {$start})";

            // Conditional functions
            case 'IFNULL':
                // Oracle uses NVL()
                return 'NVL('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ')';

            // Date/time functions
            case 'NOW':
                // Oracle uses SYSDATE or SYSTIMESTAMP
                return 'SYSTIMESTAMP';

            case 'CURRENT_DATE':
                return 'SYSDATE';

            case 'CURRENT_TIME':
                return "TO_CHAR(SYSTIMESTAMP, 'HH24:MI:SS')";

            case 'DATE':
                // Oracle uses TRUNC() to get date part
                return 'TRUNC(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'TIME':
                // Oracle extracts time as string
                return "TO_CHAR(" . $this->buildExpressionArgument($args[0]) . ", 'HH24:MI:SS')";

            case 'YEAR':
                return 'EXTRACT(YEAR FROM ' . $this->buildExpressionArgument($args[0]) . ')';

            case 'MONTH':
                return 'EXTRACT(MONTH FROM ' . $this->buildExpressionArgument($args[0]) . ')';

            case 'DAY':
                return 'EXTRACT(DAY FROM ' . $this->buildExpressionArgument($args[0]) . ')';

            case 'HOUR':
                return 'EXTRACT(HOUR FROM ' . $this->buildExpressionArgument($args[0]) . ')';

            case 'MINUTE':
                return 'EXTRACT(MINUTE FROM ' . $this->buildExpressionArgument($args[0]) . ')';

            case 'SECOND':
                return 'EXTRACT(SECOND FROM ' . $this->buildExpressionArgument($args[0]) . ')';

            case 'DATE_FORMAT':
                // Oracle uses TO_CHAR() with different format codes
                $format = $this->convertDateFormat($args[1]);
                return "TO_CHAR(" . $this->buildExpressionArgument($args[0]) . ", '{$format}')";

            // Sequence functions (native Oracle syntax)
            case 'NEXTVAL':
                return $this->connection->quoteName($args[0]) . '.NEXTVAL';

            case 'CURRVAL':
                return $this->connection->quoteName($args[0]) . '.CURRVAL';

            default:
                // Fall back to parent for standard functions
                return parent::buildFunctionExpression($expression);
        }
    }
}
