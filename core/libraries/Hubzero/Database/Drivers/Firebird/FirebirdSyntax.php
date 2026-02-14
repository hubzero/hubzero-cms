<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Firebird;

use Hubzero\Database\Exception\UnsupportedSyntaxException;

/**
 * Database Firebird query syntax class
 *
 * Firebird is a relational database offering many ANSI SQL standard features.
 * This class extends the base SQL syntax with Firebird-specific features.
 *
 * Firebird-specific features supported:
 * - OFFSET/FETCH: Firebird 3.0+ pagination syntax (SQL:2008 standard)
 * - UPDATE OR INSERT: Firebird 2.1+ upsert syntax
 * - RDB$ system tables: Column introspection
 * - Generators (sequences) instead of auto-increment
 * - RETURNING clause for INSERT/UPDATE
 * - Double quotes for identifier quoting (SQL standard)
 * - || for string concatenation
 *
 * Firebird limitations:
 * - No native JSON support (Firebird 4.0+ has limited support)
 * - Different date/time functions (EXTRACT, CAST)
 *
 * Firebird 3.0+ features:
 * - Window functions
 * - Boolean data type
 * - SCROLL cursors
 *
 * Firebird 4.0+ features:
 * - Built-in encryption
 * - Time zones
 * - INT128, DECFLOAT types
 */
class FirebirdSyntax extends \Hubzero\Database\Drivers\Base\BaseSqlSyntax
{
    /**
     * Whether we are emitting a FULL OUTER JOIN emulation query.
     *
     * When true, buildSelect() returns a full UNION query and the remaining
     * clause builders return empty strings to avoid duplicating SQL.
     *
     * Even though Firebird supports FULL OUTER JOIN natively, we emulate it
     * to ensure consistent behavior across all database drivers.
     *
     * @var  bool
     */
    protected $fullJoinUnionBuilt = false;

    /**
     * Firebird type mappings
     *
     * Maps common type names to Firebird-native types.
     *
     * @var array
     */
    protected static $typeMappings = [
        // Integer types
        'int'       => 'INTEGER',
        'integer'   => 'INTEGER',
        'tinyint'   => 'SMALLINT',
        'smallint'  => 'SMALLINT',
        'mediumint' => 'INTEGER',
        'bigint'    => 'BIGINT',
        'boolean'   => 'BOOLEAN',  // Firebird 3.0+
        'bool'      => 'BOOLEAN',

        // Text types - Firebird uses VARCHAR with max 32767 bytes
        'character' => 'VARCHAR',
        'varchar'   => 'VARCHAR',
        'char'      => 'CHAR',
        'text'      => 'BLOB SUB_TYPE TEXT',
        'longtext'  => 'BLOB SUB_TYPE TEXT',
        'mediumtext' => 'BLOB SUB_TYPE TEXT',
        'tinytext'  => 'VARCHAR(255)',
        'enum'      => 'VARCHAR(255)',  // Firebird has no ENUM
        'set'       => 'VARCHAR(255)',  // Firebird has no SET
        'json'      => 'BLOB SUB_TYPE TEXT',  // No native JSON

        // Numeric types
        'real'      => 'FLOAT',
        'double'    => 'DOUBLE PRECISION',
        'float'     => 'FLOAT',
        'decimal'   => 'DECIMAL',
        'numeric'   => 'NUMERIC',

        // Binary types
        'blob'      => 'BLOB',
        'binary'    => 'BLOB',
        'varbinary' => 'BLOB',
        'longblob'  => 'BLOB',
        'mediumblob' => 'BLOB',
        'tinyblob'  => 'BLOB',

        // Date/time types
        'date'      => 'DATE',
        'datetime'  => 'TIMESTAMP',
        'timestamp' => 'TIMESTAMP',
        'time'      => 'TIME',
        'year'      => 'SMALLINT',
    ];

    /**
     * Firebird reserved words that must be quoted when used as aliases
     *
     * @var array
     */
    protected static $reservedWords = [
        'count', 'sum', 'avg', 'min', 'max', 'value', 'position', 'row',
        'rows', 'first', 'skip', 'order', 'by', 'group', 'having',
        'where', 'from', 'select', 'insert', 'update', 'delete',
        'create', 'alter', 'drop', 'table', 'index', 'view',
        'user', 'password', 'type', 'current_time', 'current_date',
        'current_timestamp', 'timestamp', 'date', 'time', 'year',
        'month', 'day', 'hour', 'minute', 'second',
    ];

    /**
     * Builds a select statement from the set params
     *
     * Firebird supports FULL OUTER JOIN natively. However, to ensure
     * consistent behavior across all database drivers, we emulate it using:
     *   LEFT JOIN
     *   UNION ALL
     *   RIGHT JOIN ... WHERE left_key IS NULL
     *
     * This prevents different behavior on different databases. To guarantee
     * identical results, FULL JOIN usage is restricted to a single FULL JOIN
     * between two base tables. Additional JOINs are allowed only if they are
     * INNER or LEFT joins referencing tables introduced earlier.
     *
     * @return  string
     */
    protected function buildSelect()
    {
        // Check if we need FULL JOIN emulation
        if (!$this->hasFullJoin()) {
            $this->fullJoinUnionBuilt = false;
            return $this->buildSelectClause();
        }

        // Emulate FULL JOIN with UNION ALL
        $this->fullJoinUnionBuilt = true;

        $branches = $this->expandFullJoinBranches();
        if (empty($branches)) {
            $this->fullJoinUnionBuilt = false;
            return $this->buildSelectClause();
        }

        $originalJoins = $this->join;
        $sqlParts = [];
        $allBindings = [];

        foreach ($branches as $branch) {
            $this->join = $branch['joins'];
            $this->clearBindings();

            $leftWhere = !empty($this->where) ? $this->buildWhereClause() : '';
            $whereSql = $this->appendFiltersToWhere($leftWhere, $branch['filters']);

            $selectSql = $this->buildSelectClause();
            $fromSql = parent::buildFrom();
            $joinSql = parent::buildJoin();
            $groupSql = !empty($this->group) ? parent::buildGroup() : '';
            $havingSql = !empty($this->having) ? parent::buildHaving() : '';

            $sqlParts[] = $this->assembleFullJoinSelect(
                $selectSql,
                $fromSql,
                $joinSql,
                $whereSql,
                $groupSql,
                $havingSql
            );

            $allBindings = array_merge($allBindings, $this->getBindings());
        }

        $this->join = $originalJoins;
        $this->clearBindings();
        foreach ($allBindings as $binding) {
            $this->bind($binding);
        }

        $sql = implode("\nUNION ALL\n", $sqlParts);

        $order = !empty($this->order) ? parent::buildOrder() : '';
        if (!empty($order)) {
            $sql .= "\n" . $order;
        }

        $limit = (!empty($this->limit) || !empty($this->start)) ? $this->buildUnionLimit() : '';
        if (!empty($limit)) {
            $sql .= "\n" . $limit;
        }

        return $sql;
    }

    /**
     * Builds the SELECT clause (extracted from buildSelect for reuse)
     *
     * Overrides parent to quote reserved words used as aliases (like 'count').
     *
     * @return  string
     */
    protected function buildSelectClause()
    {
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
                // Handle Expression objects (arithmetic, functions, etc.)
                $string = $this->buildExpression($select['column']);
            } elseif (isset($select['count']) && $select['count'] == 'distinct') {
                // Quote column name to handle reserved keywords
                $quoted = $this->connection->quoteName($select['column']);
                $string = "COUNT(DISTINCT({$quoted}))";
            } elseif (!empty($select['count'])) {
                // Quote column name to handle reserved keywords
                $quoted = $this->connection->quoteName($select['column']);
                $string = "COUNT({$quoted})";
            } else {
                // Quote column name to handle reserved keywords like VALUE, USER, etc.
                // Skip quoting for:
                // - * wildcard
                // - Expressions with functions (contains parentheses)
                // - Numeric literals (like SELECT 1)
                // - Comma-separated lists (multiple columns in one string)
                // - Dotted expressions (table.column handled by quoteName)
                $col = $select['column'];
                if ($col === '*' || strpos($col, '(') !== false || is_numeric($col) || strpos($col, ',') !== false) {
                    // For comma-separated columns, split and quote each individually
                    if (strpos($col, ',') !== false) {
                        $columns = array_map('trim', explode(',', $col));
                        $quoted = [];
                        foreach ($columns as $column) {
                            if ($column === '*' || strpos($column, '(') !== false || is_numeric($column)) {
                                $quoted[] = $column;
                            } else {
                                $quoted[] = $this->connection->quoteName($column);
                            }
                        }
                        $string = implode(', ', $quoted);
                    } else {
                        $string = $col;
                    }
                } else {
                    $string = $this->connection->quoteName($col);
                }
            }

            // See if we're including an alias
            if (isset($select['as']) && $select['as'] !== null) {
                $alias = $select['as'];
                // Quote reserved words to avoid SQL errors
                if (in_array(strtolower($alias), self::$reservedWords)) {
                    $alias = '"' . $alias . '"';
                }
                $string .= " AS {$alias}";
            }

            $selects[] = $string;
        }

        return 'SELECT ' . implode(',', $selects);
    }

    /**
     * Builds a values statement from the set params
     *
     * Adds RETURNING clause to capture the inserted ID, whether it was
     * auto-generated or explicitly provided. This eliminates the need
     * to manually sync generators when seeding explicit IDs.
     *
     * @return  string
     */
    public function buildValues()
    {
        // Check if this is an INSERT ... SELECT
        if ($this->insertSelectQuery !== null) {
            // Add bindings from the SELECT query
            foreach ($this->insertSelectBindings as $binding) {
                $this->bind($binding);
            }

            // Build column list if specified
            if (!empty($this->insertColumns)) {
                $quotedColumns = [];
                foreach ($this->insertColumns as $column) {
                    $quotedColumns[] = $this->connection->quoteName($column);
                }
                return '(' . implode(',', $quotedColumns) . ') ' . $this->insertSelectQuery;
            }

            // No columns specified - SELECT determines columns
            return $this->insertSelectQuery;
        }

        // Standard INSERT VALUES
        $fields = [];
        $values = [];

        foreach ($this->values as $field => $value) {
            $fields[] = $this->connection->quoteName($field);
            $values[] = '?';
            $this->bind(is_string($value) ? trim($value) : $value);
        }

        $sql = '(' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';

        // Note: When ignore flag is set, we use regular INSERT and catch duplicate
        // key errors in the driver's execute() method. No special SQL syntax needed.

        // Add RETURNING clause to get the inserted ID, but NOT for INSERT SELECT
        // (RETURNING with INSERT SELECT can cause ambiguous column name errors)
        if ($this->insertSelectQuery === null) {
            // Check if 'id' was provided in the values (case-insensitive check)
            $hasIdField = false;
            foreach (array_keys($this->values) as $field) {
                if (strtolower($field) === 'id') {
                    $hasIdField = true;
                    break;
                }
            }

            // Also check if the table has an 'id' column by querying the table name from the query
            // The insert table name is stored in $this->insert
            if (!$hasIdField && !empty($this->insert)) {
                $tableName = is_array($this->insert) ? $this->insert[0] : $this->insert;
                $hasIdField = $this->connection->tableHasField($tableName, 'id');
            }

            if ($hasIdField) {
                // Firebird 2.0+ supports RETURNING for INSERT statements
                // We use "ID" (uppercase) as Firebird stores unquoted identifiers uppercase
                // PDO::ATTR_CASE normalizes returned column names to lowercase for ORM
                $sql .= ' RETURNING "ID"';
            }
        }

        return $sql;
    }

    /**
     * Builds a limit statement from the set params
     *
     * Firebird uses FIRST/SKIP syntax which appears AFTER SELECT:
     * SELECT FIRST 10 SKIP 20 * FROM table
     *
     * However, in our architecture, buildLimit() is called after buildSelect(),
     * so we need to use ROWS syntax (Firebird 2.0+) which appears at the end:
     * SELECT * FROM table ROWS 21 TO 30
     *
     * Or for modern Firebird 3.0+, use OFFSET/FETCH:
     * SELECT * FROM table OFFSET 20 ROWS FETCH FIRST 10 ROWS ONLY
     *
     * @return  string
     */
    public function buildLimit()
    {
        if ($this->fullJoinUnionBuilt) {
            return '';
        }

        // Use SQL:2008 standard OFFSET/FETCH syntax (Firebird 3.0+)
        $string = '';

        // OFFSET must come before FETCH
        if (!empty($this->start)) {
            $string .= 'OFFSET ' . (int) $this->start . ' ROWS ';
        }

        // FETCH for limit
        if (!empty($this->limit)) {
            $string .= 'FETCH FIRST ' . (int) $this->limit . ' ROWS ONLY';
        }

        return trim($string);
    }

    /**
     * Builds a LIMIT statement for UNION queries
     *
     * Uses Firebird's OFFSET/FETCH syntax but bypasses the FULL JOIN guard
     * because this is applied to the unioned result set.
     *
     * @return  string
     */
    protected function buildUnionLimit(): string
    {
        $string = '';

        // OFFSET must come before FETCH
        if (!empty($this->start)) {
            $string .= 'OFFSET ' . (int) $this->start . ' ROWS ';
        }

        // FETCH for limit
        if (!empty($this->limit)) {
            $string .= 'FETCH FIRST ' . (int) $this->limit . ' ROWS ONLY';
        }

        return trim($string);
    }

    /**
     * Builds a limit statement for older Firebird versions (pre-3.0)
     *
     * Uses ROWS syntax: ROWS start TO end
     *
     * @return  string
     */
    public function buildLimitLegacy()
    {
        if (empty($this->limit)) {
            return '';
        }

        $start = !empty($this->start) ? (int) $this->start : 0;
        $end = $start + (int) $this->limit;

        // ROWS uses 1-based indexing
        return 'ROWS ' . ($start + 1) . ' TO ' . $end;
    }

    /**
     * Sets a from element on the query
     *
     * Overrides to parse embedded aliases (e.g., "table AS alias").
     * Firebird doesn't use AS for table aliases.
     *
     * @param   string  $table  The table of interest
     * @param   string  $as     What to call the table
     * @return  void
     */
    public function setFrom($table, $as = null)
    {
        // Parse embedded alias if present (e.g., "table AS alias" or "table alias")
        if ($as === null && $table && preg_match('/^(.+?)\s+(?:AS\s+)?(\w+)$/i', trim($table), $matches)) {
            $table = $matches[1];
            $as = $matches[2];
        }

        $this->from[] = [
            'table' => $table,
            'as'    => $as
        ];
    }

    /**
     * Sets a join element on the query
     *
     * Overrides to parse embedded aliases (e.g., "table AS alias").
     * Firebird doesn't use AS for table aliases.
     *
     * @param   string  $table     The table join
     * @param   string  $leftKey   The left side of the join condition
     * @param   string  $rightKey  The right side of the join condition
     * @param   string  $type      The join type to perform
     * @return  void
     */
    public function setJoin($table, $leftKey, $rightKey, $type = 'inner')
    {
        // Parse embedded alias if present
        $alias = null;
        if ($table && preg_match('/^(.+?)\s+(?:AS\s+)?(\w+)$/i', trim($table), $matches)) {
            $table = $matches[1];
            $alias = $matches[2];
        }

        $this->join[] = [
            'table' => $table,
            'alias' => $alias,
            'left'  => $leftKey,
            'right' => $rightKey,
            'type'  => $type
        ];
    }

    /**
     * Builds a from statement from the set params
     *
     * Firebird does not use AS keyword for table aliases - just a space.
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
            // Handle raw from expressions (subqueries)
            if (array_key_exists('raw', $from)) {
                $string = '(' . $from['raw'] . ')';

                // Add bindings from the raw expression
                foreach ($from['bindings'] as $binding) {
                    $this->bind($binding);
                }

                // Alias for derived tables - Firebird uses space, not AS
                if (isset($from['as'])) {
                    $string .= ' ' . $this->connection->quoteName($from['as']);
                }
            } else {
                $string = $this->connection->quoteName($from['table']);

                // Alias - Firebird uses space, not AS
                if (isset($from['as'])) {
                    $string .= ' ' . $this->connection->quoteName($from['as']);
                }
            }

            $froms[] = $string;
        }

        return 'FROM ' . implode(',', $froms);
    }

    /**
     * Builds a join statement from the set params
     *
     * Firebird does not use AS keyword for table aliases - just a space.
     *
     * @return  string
     */
    public function buildJoin()
    {
        if ($this->fullJoinUnionBuilt) {
            return '';
        }

        $joins = [];

        foreach ($this->join as $join) {
            if (isset($join['subquery'])) {
                // Handle subquery joins - Firebird uses space, not AS
                $tableExpr = '(' . $join['subquery'] . ') ' . $this->connection->quoteName($join['as']);

                // Add bindings from the subquery
                foreach ($join['bindings'] as $binding) {
                    $this->bind($binding);
                }

                $joins[] = strtoupper($join['type']) .
                    ' JOIN ' .
                    $tableExpr .
                    ' ON ' .
                    $this->connection->quoteName($join['left']) .
                    ' = ' .
                    $this->connection->quoteName($join['right']);
            } elseif (isset($join['raw'])) {
                $joins[] = strtoupper($join['type']) . ' JOIN ' . $join['table'] . ' ON ' . $join['raw'];
            } elseif (isset($join['conditions'])) {
                // Handle complex join conditions (joinOn, joinBuilder)
                // Build table expression with optional alias
                $tableExpr = $this->connection->quoteName($join['table']);
                if (!empty($join['alias'])) {
                    $tableExpr .= ' ' . $this->connection->quoteName($join['alias']);
                }

                $joins[] = strtoupper($join['type']) .
                    ' JOIN ' .
                    $tableExpr .
                    ' ON ' .
                    $this->buildJoinConditions($join['conditions']);
            } else {
                // Simple join with left/right equality
                // Build table expression with optional alias
                $tableExpr = $this->connection->quoteName($join['table']);
                if (!empty($join['alias'])) {
                    $tableExpr .= ' ' . $this->connection->quoteName($join['alias']);
                }

                $joins[] = strtoupper($join['type']) .
                    ' JOIN ' .
                    $tableExpr .
                    ' ON ' .
                    $this->connection->quoteName($join['left']) .
                    ' = ' .
                    $this->connection->quoteName($join['right']);
            }
        }

        return implode("\n", $joins);
    }

    /**
     * Builds a where statement from the set params
     *
     * @return  string
     */
    protected function buildWhere()
    {
        if ($this->fullJoinUnionBuilt) {
            return '';
        }

        // If this is an UPDATE with JOINs, wrap in subquery using RDB$DB_KEY
        // Firebird doesn't support MySQL-style UPDATE ... JOIN, so we emulate it:
        // MySQL: UPDATE t1 JOIN t2 ON ... SET ... WHERE ...
        // Firebird: UPDATE t1 SET ... WHERE RDB$DB_KEY IN (SELECT t1.RDB$DB_KEY FROM t1 JOIN t2 ON ... WHERE ...)
        if (!empty($this->update) && !empty($this->join)) {
            $updateTable = $this->update;
            $quotedTable = $this->connection->quoteName($updateTable);

            // Build subquery: SELECT table.RDB$DB_KEY FROM table JOIN ...
            $subquery = "SELECT {$quotedTable}.RDB\$DB_KEY FROM {$quotedTable}";
            $subquery .= ' ' . $this->buildJoin();

            // Add original WHERE conditions to subquery
            $originalWhere = $this->buildWhereClause();
            if ($originalWhere) {
                // Remove 'WHERE' prefix since we'll add it back
                $originalWhere = preg_replace('/^WHERE\\s+/i', '', $originalWhere);
                $subquery .= ' WHERE ' . $originalWhere;
            }

            return "WHERE RDB\$DB_KEY IN ({$subquery})";
        }

        return $this->buildWhereClause();
    }

    /**
     * Builds a group statement from the set params
     *
     * @return  string
     */
    public function buildGroup()
    {
        if ($this->fullJoinUnionBuilt) {
            return '';
        }

        return parent::buildGroup();
    }

    /**
     * Builds a having statement from the set params
     *
     * @return  string
     */
    public function buildHaving()
    {
        if ($this->fullJoinUnionBuilt) {
            return '';
        }

        return parent::buildHaving();
    }

    /**
     * Builds an order statement from the set params
     *
     * @return  string
     */
    public function buildOrder()
    {
        if ($this->fullJoinUnionBuilt) {
            return '';
        }

        return parent::buildOrder();
    }

    /**
     * Build a CONCAT expression for Firebird
     *
     * Firebird uses the || operator for string concatenation.
     *
     * @param   array  $parts  Array of column names or quoted strings
     * @return  string
     */
    public function buildConcat(array $parts)
    {
        // Quote column names that aren't already quoted literals
        $quotedParts = array_map(function ($part) {
            // If it's already a quoted string literal (starts and ends with '), leave as-is
            if (preg_match("/^'.*'$/", $part)) {
                return $part;
            }
            // Otherwise, quote as a column name
            return $this->connection->quoteName($part);
        }, $parts);

        return implode(' || ', $quotedParts);
    }

    /**
     * Returns the proper query for generating a list of table columns
     *
     * Firebird uses system tables for metadata.
     *
     * @param   string  $table  The name of the database table
     * @return  string
     */
    public function getColumnsQuery($table)
    {
        // Remove any prefix and clean the table name
        $tableName = strtoupper(trim($table));

        return "SELECT
                rf.RDB\$FIELD_NAME AS name,
                f.RDB\$FIELD_TYPE AS field_type,
                f.RDB\$FIELD_SUB_TYPE AS field_sub_type,
                f.RDB\$FIELD_LENGTH AS field_length,
                f.RDB\$FIELD_PRECISION AS field_precision,
                f.RDB\$FIELD_SCALE AS field_scale,
                rf.RDB\$NULL_FLAG AS not_null,
                rf.RDB\$DEFAULT_SOURCE AS default_value
            FROM RDB\$RELATION_FIELDS rf
            JOIN RDB\$FIELDS f ON rf.RDB\$FIELD_SOURCE = f.RDB\$FIELD_NAME
            WHERE rf.RDB\$RELATION_NAME = " . $this->connection->quote($tableName) . "
            ORDER BY rf.RDB\$FIELD_POSITION";
    }

    /**
     * Normalizes the results of the column metadata query
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
                // Normalize Firebird uppercase column names to lowercase
                // to match ORM attribute conventions
                $name = strtolower(trim($field->name ?? ''));
                $results[$name] = $this->convertFieldType(
                    $field->field_type,
                    $field->field_sub_type ?? null,
                    $field->field_length ?? null,
                    $field->field_scale ?? null
                );
            }
        } else {
            foreach ($data as $field) {
                // Normalize Firebird uppercase column names to lowercase
                $name = strtolower(trim($field->name ?? ''));
                $type = $this->convertFieldType(
                    $field->field_type,
                    $field->field_sub_type ?? null,
                    $field->field_length ?? null,
                    $field->field_scale ?? null
                );
                $results[$name] = [
                    'name'      => $name,
                    'type'      => $type,
                    'raw_type'  => $field->field_type,
                    'allownull' => empty($field->not_null),
                    'default'   => $this->normalizeDefault($field->default_value ?? null),
                    'pk'        => false  // Need separate query for PK info
                ];
            }
        }

        return $results;
    }

    /**
     * Convert Firebird field type codes to readable type names
     *
     * Firebird stores field types as numeric codes in RDB$FIELD_TYPE.
     *
     * @param   int       $type      The Firebird field type code
     * @param   int|null  $subType   The field sub-type (for BLOBs, etc.)
     * @param   int|null  $length    The field length
     * @param   int|null  $scale     The field scale (for decimals)
     * @return  string
     */
    protected function convertFieldType($type, $subType = null, $length = null, $scale = null)
    {
        // Firebird type codes (from RDB$TYPES where RDB$TYPE_NAME = 'RDB$FIELD_TYPE')
        $types = [
            7   => 'SMALLINT',
            8   => 'INTEGER',
            10  => 'FLOAT',
            12  => 'DATE',
            13  => 'TIME',
            14  => 'CHAR',
            16  => 'BIGINT',   // Also INT64
            23  => 'BOOLEAN',  // Firebird 3.0+
            27  => 'DOUBLE PRECISION',
            35  => 'TIMESTAMP',
            37  => 'VARCHAR',
            261 => 'BLOB',
        ];

        $typeName = $types[$type] ?? 'UNKNOWN';

        // Handle BLOB sub-types
        if ($type == 261) {
            if ($subType == 1) {
                return 'BLOB SUB_TYPE TEXT';  // Text BLOB
            }
            return 'BLOB';  // Binary BLOB
        }

        // Handle scale for numeric types (negative scale = decimal places)
        if (in_array($type, [7, 8, 16]) && $scale !== null && $scale < 0) {
            return 'DECIMAL';
        }

        return $typeName;
    }

    /**
     * Normalize a default value from Firebird system tables
     *
     * Firebird stores defaults as expressions like "DEFAULT 'value'" or "DEFAULT 0".
     *
     * @param   mixed  $value  The raw default value
     * @return  mixed  The normalized default value
     */
    protected function normalizeDefault($value)
    {
        if ($value === null) {
            return null;
        }

        // Remove "DEFAULT " prefix
        $value = trim($value);
        if (stripos($value, 'DEFAULT ') === 0) {
            $value = substr($value, 8);
        }

        // Check for NULL literal
        if (strtoupper(trim($value)) === 'NULL') {
            return null;
        }

        // Remove surrounding quotes from string defaults
        if (preg_match("/^'(.*)'$/s", $value, $matches)) {
            return str_replace("''", "'", $matches[1]);
        }

        return trim($value);
    }

    /**
     * Get the SQL for checking if a table exists
     *
     * @param   string  $table  The table name
     * @return  string
     */
    public function getTableExistsQuery($table)
    {
        return "SELECT COUNT(*) FROM RDB\$RELATIONS WHERE RDB\$RELATION_NAME = " .
            $this->connection->quote(strtoupper($table));
    }

    /**
     * Get the SQL for retrieving the CREATE TABLE statement
     *
     * Firebird doesn't have a direct SHOW CREATE TABLE equivalent.
     * We reconstruct the DDL from system tables.
     *
     * @param   string  $table  The table name
     * @return  string  SQL query that returns table DDL reconstruction data
     */
    public function getTableCreateQuery($table)
    {
        // Return a query that gets the necessary info to reconstruct the DDL
        // The driver will need to assemble this into a CREATE TABLE statement
        $tableName = strtoupper(trim($table));

        return "SELECT
                rf.RDB\$FIELD_NAME AS field_name,
                f.RDB\$FIELD_TYPE AS field_type,
                f.RDB\$FIELD_SUB_TYPE AS field_sub_type,
                f.RDB\$FIELD_LENGTH AS field_length,
                f.RDB\$FIELD_PRECISION AS field_precision,
                f.RDB\$FIELD_SCALE AS field_scale,
                rf.RDB\$NULL_FLAG AS not_null,
                rf.RDB\$DEFAULT_SOURCE AS default_value,
                rf.RDB\$FIELD_POSITION AS field_position
            FROM RDB\$RELATION_FIELDS rf
            JOIN RDB\$FIELDS f ON rf.RDB\$FIELD_SOURCE = f.RDB\$FIELD_NAME
            WHERE rf.RDB\$RELATION_NAME = " . $this->connection->quote($tableName) . "
            ORDER BY rf.RDB\$FIELD_POSITION";
    }

    /**
     * Get the SQL for retrieving table list
     *
     * @return  string
     */
    public function getTableListQuery()
    {
        return "SELECT TRIM(RDB\$RELATION_NAME) AS table_name
                FROM RDB\$RELATIONS
                WHERE RDB\$SYSTEM_FLAG = 0
                AND RDB\$VIEW_BLR IS NULL
                ORDER BY RDB\$RELATION_NAME";
    }

    // =========================================================================
    // String Functions - Firebird specific overrides
    // =========================================================================

    /**
     * Build a function expression into Firebird-specific SQL
     *
     * Overrides for functions that differ in Firebird:
     * - CONCAT: Uses || operator
     * - NOW: Uses CURRENT_TIMESTAMP
     * - IFNULL: Uses COALESCE
     * - Date parts: Uses EXTRACT()
     *
     * @param   \Hubzero\Database\Expression  $expression  The function expression
     * @return  string  The SQL representation
     */
    protected function buildFunctionExpression($expression): string
    {
        $function = $expression->getFunction();
        $args = $expression->getArguments();

        switch ($function) {
            // Aggregate functions - these are standard SQL and inherited from parent
            // SUM, AVG, MIN, MAX, COUNT, COUNT_DISTINCT all work unchanged

            // String functions
            case 'CONCAT':
                // Firebird uses || for concatenation
                $parts = array_map([$this, 'buildExpressionArgument'], $args);
                return '(' . implode(' || ', $parts) . ')';

            case 'LENGTH':
                // Firebird uses CHAR_LENGTH for character count
                return 'CHAR_LENGTH(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'SUBSTRING':
                // Firebird uses SUBSTRING(str FROM start FOR length)
                $col = $this->buildExpressionArgument($args[0]);
                $start = (int) $args[1];
                if (isset($args[2]) && $args[2] !== null) {
                    return "SUBSTRING({$col} FROM {$start} FOR " . (int) $args[2] . ')';
                }
                return "SUBSTRING({$col} FROM {$start})";

            case 'REPLACE':
                // Firebird uses REPLACE(string, old, new)
                $col = $this->buildExpressionArgument($args[0]);
                $this->bind($args[1]);
                $this->bind($args[2]);
                return "REPLACE({$col}, ?, ?)";

            case 'UPPER':
                return 'UPPER(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'LOWER':
                return 'LOWER(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'TRIM':
                return 'TRIM(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'LTRIM':
                return 'TRIM(LEADING FROM ' . $this->buildExpressionArgument($args[0]) . ')';

            case 'RTRIM':
                return 'TRIM(TRAILING FROM ' . $this->buildExpressionArgument($args[0]) . ')';

            case 'LEFT':
                // LEFT(str, len) = SUBSTRING(str FROM 1 FOR len)
                $col = $this->buildExpressionArgument($args[0]);
                $len = (int) $args[1];
                return "SUBSTRING({$col} FROM 1 FOR {$len})";

            case 'RIGHT':
                // RIGHT(str, len) = SUBSTRING(str FROM CHAR_LENGTH(str) - len + 1)
                $col = $this->buildExpressionArgument($args[0]);
                $len = (int) $args[1];
                return "SUBSTRING({$col} FROM CHAR_LENGTH({$col}) - {$len} + 1)";

            // Conditional functions
            case 'COALESCE':
                $parts = array_map([$this, 'buildExpressionArgument'], $args);
                return 'COALESCE(' . implode(', ', $parts) . ')';

            case 'IFNULL':
                // Firebird uses COALESCE
                return 'COALESCE('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ')';

            case 'NULLIF':
                return 'NULLIF('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ')';

            case 'IF':
                // Firebird uses CASE WHEN instead of IF()
                $condition = $this->buildExpressionArgument($args[0]);
                $ifTrue = $this->buildExpressionArgument($args[1]);
                $ifFalse = $this->buildExpressionArgument($args[2]);
                return "CASE WHEN {$condition} THEN {$ifTrue} ELSE {$ifFalse} END";

            case 'CASE':
                return $this->buildCaseExpression($args[0], $args[1], $args[2] ?? null);

            // Date/time functions
            case 'NOW':
                return 'CURRENT_TIMESTAMP';

            case 'CURRENT_TIMESTAMP':
                return 'CURRENT_TIMESTAMP';

            case 'CURRENT_DATE':
                return 'CURRENT_DATE';

            case 'CURRENT_TIME':
                return 'CURRENT_TIME';

            case 'DATE':
                return 'CAST(' . $this->buildExpressionArgument($args[0]) . ' AS DATE)';

            case 'TIME':
                return 'CAST(' . $this->buildExpressionArgument($args[0]) . ' AS TIME)';

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
                // Firebird doesn't have DATE_FORMAT, we would need to construct with EXTRACT
                // For now, throw an exception - applications should use EXTRACT directly
                throw new UnsupportedSyntaxException(
                    'DATE_FORMAT is not supported in Firebird. Use EXTRACT() for date parts.',
                    500
                );

            case 'DATE_ADD':
            case 'DATE_SUB':
                // Firebird uses DATEADD function
                // DATE_ADD(date, INTERVAL n unit) -> DATEADD(unit, n, date)
                throw new UnsupportedSyntaxException(
                    'DATE_ADD/DATE_SUB not yet implemented for Firebird. Use DATEADD function directly.',
                    500
                );

            // Math functions
            case 'ROUND':
                $col = $this->buildExpressionArgument($args[0]);
                $decimals = isset($args[1]) ? (int) $args[1] : 0;
                return "ROUND({$col}, {$decimals})";

            case 'FLOOR':
                return 'FLOOR(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'CEIL':
            case 'CEILING':
                return 'CEILING(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'ABS':
                return 'ABS(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'MOD':
                // Firebird uses MOD(x, y) function
                return 'MOD('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ')';

            case 'POWER':
                return 'POWER('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ')';

            case 'SQRT':
                return 'SQRT(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'RAND':
                return 'RAND()';  // Firebird has RAND() starting from 2.5

            case 'SIGN':
                return 'SIGN(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'TRUNC':
            case 'TRUNCATE':
                // Firebird uses TRUNC
                $col = $this->buildExpressionArgument($args[0]);
                $decimals = isset($args[1]) ? (int) $args[1] : 0;
                return "TRUNC({$col}, {$decimals})";

            // Bit functions
            case 'BIT_AND':
                return 'BIN_AND('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ')';

            case 'BIT_OR':
                return 'BIN_OR('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ')';

            case 'BIT_XOR':
                return 'BIN_XOR('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ')';

            // Sequence functions (Firebird generators)
            case 'NEXTVAL':
                return 'NEXT VALUE FOR ' . $this->connection->quoteName($args[0]);

            case 'CURRVAL':
                return 'GEN_ID(' . $this->connection->quoteName($args[0]) . ', 0)';

            default:
                // Fall back to parent for standard functions
                return parent::buildFunctionExpression($expression);
        }
    }

    // =========================================================================
    // Date/Time Operations - Firebird specific overrides
    // =========================================================================

    /**
     * Sets a date/time extraction where clause
     *
     * Firebird uses EXTRACT() function for date parts.
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
                $raw = "EXTRACT(YEAR FROM {$quotedColumn}) {$operator} ?";
                break;
            case 'month':
                $raw = "EXTRACT(MONTH FROM {$quotedColumn}) {$operator} ?";
                break;
            case 'day':
                $raw = "EXTRACT(DAY FROM {$quotedColumn}) {$operator} ?";
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
    // JSON Operations - Firebird has limited support
    // =========================================================================

    /**
     * Sets a JSON path extraction where clause
     *
     * Firebird has limited JSON support (4.0+). For older versions,
     * JSON must be handled as text or via UDF.
     *
     * @param   string  $column    The JSON column name
     * @param   string  $path      The dot-notation path to the value
     * @param   string  $operator  Comparison operator
     * @param   mixed   $value     The value to compare against
     * @param   string  $logical   The operator between multiple clauses
     * @param   int     $depth     The depth level of the clause
     * @return  void
     * @throws  UnsupportedSyntaxException
     */
    public function setJsonPathWhere($column, $path, $operator, $value, $logical = 'and', $depth = 0)
    {
        throw new UnsupportedSyntaxException(
            'Native JSON operations are not fully supported in Firebird. ' .
            'Consider storing JSON as text and parsing in application code.',
            500
        );
    }

    /**
     * Sets a JSON contains where clause
     *
     * @param   string       $column   The JSON column name
     * @param   mixed        $value    The value to search for
     * @param   string|null  $path     Optional dot-notation path
     * @param   string       $logical  The operator between multiple clauses
     * @param   int          $depth    The depth level of the clause
     * @return  void
     * @throws  UnsupportedSyntaxException
     */
    public function setJsonContainsWhere($column, $value, $path = null, $logical = 'and', $depth = 0)
    {
        throw new UnsupportedSyntaxException(
            'JSON_CONTAINS is not supported in Firebird. ' .
            'Consider using LIKE or POSITION for text-based searches.',
            500
        );
    }

    /**
     * Sets a JSON length where clause
     *
     * @param   string       $column    The JSON column name
     * @param   string       $operator  Comparison operator
     * @param   int          $value     The length value
     * @param   string|null  $path      Optional path
     * @param   string       $logical   The operator between multiple clauses
     * @param   int          $depth     The depth level of the clause
     * @return  void
     * @throws  UnsupportedSyntaxException
     */
    public function setJsonLengthWhere($column, $operator, $value, $path = null, $logical = 'and', $depth = 0)
    {
        throw new UnsupportedSyntaxException(
            'JSON_LENGTH is not supported in Firebird.',
            500
        );
    }

    // =========================================================================
    // Table Operations - Firebird specific
    // =========================================================================

    /**
     * Get the SQL statements to truncate a table
     *
     * Firebird doesn't have TRUNCATE TABLE. Use DELETE FROM.
     * Note: This doesn't reset generators (sequences).
     *
     * @param   string  $table  The table to truncate
     * @return  array   Array of SQL statements to execute
     */
    public function getTruncateStatements($table)
    {
        $quotedTable = $this->connection->quoteName($table);

        return [
            'DELETE FROM ' . $quotedTable
        ];
    }

    /**
     * Builds an upsert statement from the set params
     *
     * Firebird 2.1+ supports UPDATE OR INSERT syntax.
     * MATCHING clause specifies the columns for conflict detection.
     *
     * @return  string
     */
    public function buildUpsert()
    {
        $columns = array_keys($this->upsertValues);
        $quotedColumns = [];
        $placeholders = [];

        foreach ($columns as $column) {
            $quotedColumns[] = $this->connection->quoteName($column);
            $placeholders[] = '?';
            $this->bind(
                is_string($this->upsertValues[$column])
                    ? trim($this->upsertValues[$column])
                    : $this->upsertValues[$column]
            );
        }

        // Firebird uses MATCHING clause for conflict columns
        $conflictColumns = !empty($this->upsertConflictColumns)
            ? $this->upsertConflictColumns
            : [$columns[0]];

        $quotedConflict = array_map(
            fn($col) => $this->connection->quoteName($col),
            $conflictColumns
        );

        return sprintf(
            'UPDATE OR INSERT INTO %s (%s) VALUES (%s) MATCHING (%s)',
            $this->connection->quoteName($this->upsertTable),
            implode(', ', $quotedColumns),
            implode(', ', $placeholders),
            implode(', ', $quotedConflict)
        );
    }

    /**
     * Build INSERT statement
     *
     * @return  string
     */
    public function buildInsert()
    {
        return 'INSERT INTO ' . $this->connection->quoteName($this->insert);
    }

    /**
     * Firebird needs row-by-row INSERT IGNORE for INSERT ... SELECT
     *
     * Firebird has no INSERT IGNORE syntax. Single-row inserts catch
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
     * Builds a bulk insert statement
     *
     * Firebird uses INSERT INTO ... SELECT ... FROM RDB$DATABASE UNION ALL ...
     * for multi-row inserts. This requires explicit CAST expressions for type safety.
     *
     * @return  string
     */
    public function buildInsertMany()
    {
        if (empty($this->insertManyRows)) {
            return '';
        }

        // Get columns from the first row
        $columns = array_keys($this->insertManyRows[0]);

        // Get type information for the columns
        $columnTypes = $this->getColumnTypesForCast($this->insertManyTable, $columns);

        // If we couldn't get type info, fall back to individual inserts
        if (empty($columnTypes)) {
            return '';
        }

        // Build the column list
        $quotedColumns = [];
        foreach ($columns as $column) {
            $quotedColumns[] = $this->connection->quoteName($column);
        }

        // Build SELECT ... UNION ALL statements
        $selects = [];
        foreach ($this->insertManyRows as $row) {
            $casts = [];
            foreach ($columns as $column) {
                $value = $row[$column] ?? null;

                // Get the CAST type for this column
                $castType = $columnTypes[$column] ?? 'VARCHAR(255)';

                // Firebird BOOLEAN can't be CAST from string - use literal
                if ($castType === 'BOOLEAN') {
                    if ($value === null) {
                        $casts[] = 'NULL';
                    } else {
                        $casts[] = $value ? 'TRUE' : 'FALSE';
                    }
                } else {
                    $this->bind(is_string($value) ? trim($value) : $value);
                    $casts[] = "CAST(? AS {$castType})";
                }
            }
            $selects[] = 'SELECT ' . implode(', ', $casts) . ' FROM RDB$DATABASE';
        }

        return sprintf(
            'INSERT INTO %s (%s) %s',
            $this->connection->quoteName($this->insertManyTable),
            implode(', ', $quotedColumns),
            implode(' UNION ALL ', $selects)
        );
    }

    /**
     * Get column types for CAST expressions in bulk inserts
     *
     * Queries the Firebird system tables to get type information for the specified columns.
     *
     * @param   string  $table    The table name
     * @param   array   $columns  The column names
     * @return  array   Map of column name => CAST type string
     */
    protected function getColumnTypesForCast($table, $columns)
    {
        try {
            // Build query to get column info
            $tableName = strtoupper(trim($table));
            $sql = "SELECT
                    rf.RDB\$FIELD_NAME AS name,
                    f.RDB\$FIELD_TYPE AS field_type,
                    f.RDB\$FIELD_SUB_TYPE AS field_sub_type,
                    f.RDB\$FIELD_LENGTH AS field_length,
                    f.RDB\$FIELD_PRECISION AS field_precision,
                    f.RDB\$FIELD_SCALE AS field_scale,
                    f.RDB\$CHARACTER_LENGTH AS char_length
                FROM RDB\$RELATION_FIELDS rf
                JOIN RDB\$FIELDS f ON rf.RDB\$FIELD_SOURCE = f.RDB\$FIELD_NAME
                WHERE rf.RDB\$RELATION_NAME = " . $this->connection->quote($tableName);

            // Execute query using driver methods
            $this->connection->setQuery($sql);
            $fields = $this->connection->loadObjectList();

            // Map column names to CAST types
            $result = [];
            foreach ($fields as $field) {
                $columnName = strtolower(trim($field->name));
                $result[$columnName] = $this->convertTypeToCast(
                    $field->field_type,
                    $field->field_sub_type ?? null,
                    $field->field_length ?? null,
                    $field->field_precision ?? null,
                    $field->field_scale ?? null,
                    $field->char_length ?? null
                );
            }

            return $result;
        } catch (\Exception $e) {
            // If we can't get type info, return empty to trigger fallback
            return [];
        }
    }

    /**
     * Convert Firebird field type to CAST type expression
     *
     * Maps Firebird type codes to the appropriate CAST type strings for use in
     * SELECT...UNION ALL bulk insert queries.
     *
     * @param   int       $type       The Firebird field type code
     * @param   int|null  $subType    The field sub-type (for BLOBs)
     * @param   int|null  $length     The field byte length
     * @param   int|null  $precision  The field precision
     * @param   int|null  $scale      The field scale (for decimals)
     * @param   int|null  $charLength The character length (for char/varchar)
     * @return  string
     */
    protected function convertTypeToCast(
        $type,
        $subType = null,
        $length = null,
        $precision = null,
        $scale = null,
        $charLength = null
    ) {
        // Firebird type codes
        switch ($type) {
            case 7:  // SMALLINT
                // Check for scaled numeric
                if ($scale !== null && $scale < 0) {
                    $decimals = abs($scale);
                    return "DECIMAL(4, $decimals)";
                }
                return 'SMALLINT';

            case 8:  // INTEGER
                // Check for scaled numeric
                if ($scale !== null && $scale < 0) {
                    $decimals = abs($scale);
                    return "DECIMAL(9, $decimals)";
                }
                return 'INTEGER';

            case 10: // FLOAT
                return 'FLOAT';

            case 12: // DATE
                return 'DATE';

            case 13: // TIME
                return 'TIME';

            case 14: // CHAR
                $len = $charLength ?? ($length ?? 1);
                return "CHAR($len)";

            case 16: // BIGINT (INT64)
                // Check for scaled numeric
                if ($scale !== null && $scale < 0) {
                    $decimals = abs($scale);
                    return "DECIMAL(18, $decimals)";
                }
                return 'BIGINT';

            case 23: // BOOLEAN (Firebird 3.0+)
                return 'BOOLEAN';

            case 27: // DOUBLE PRECISION
                return 'DOUBLE PRECISION';

            case 35: // TIMESTAMP
                return 'TIMESTAMP';

            case 37: // VARCHAR
                $len = $charLength ?? ($length ?? 255);
                return "VARCHAR($len)";

            case 261: // BLOB
                if ($subType == 1) {
                    // Text BLOB - use VARCHAR for compatibility
                    return 'VARCHAR(8192)';
                }
                return 'BLOB';

            default:
                // Fallback for unknown types
                return 'VARCHAR(255)';
        }
    }

    /**
     * Get the value to use for an empty set in an IN clause
     *
     * Firebird fails on empty strings for integer column IN clauses.
     *
     * @return  string
     */
    protected function getEmptySetValue()
    {
        return 'NULL';
    }

    /**
     * Build an EXISTS / NOT EXISTS predicate for Firebird.
     *
     * Firebird requires a FROM clause in subqueries. If the test uses
     * "SELECT 1" or "SELECT expression" without a FROM clause, we add
     * "FROM RDB$DATABASE" which is a system table with exactly one row.
     *
     * @param   string  $operatorLower
     * @param   mixed   $subquery
     * @return  string
     */
    protected function buildJoinExistsClause(string $operatorLower, $subquery): string
    {
        if (is_object($subquery) && method_exists($subquery, 'build')) {
            $sql = $subquery->build($this);
        } else {
            $sql = (string) $subquery;
        }

        $sql = trim($sql);
        if ($sql === '') {
            throw new \InvalidArgumentException('EXISTS join conditions require a subquery expression.');
        }

        // Firebird requires FROM clause in subqueries
        // Check if this is a simple SELECT without FROM
        if (preg_match('/^SELECT\s+(.+?)$/i', $sql, $matches) && !preg_match('/\bFROM\b/i', $sql)) {
            $sql = rtrim($sql) . ' FROM RDB$DATABASE';
        }

        if ($sql[0] !== '(') {
            $sql = '(' . $sql . ')';
        }

        return ($operatorLower === 'not exists' ? 'NOT EXISTS ' : 'EXISTS ') . $sql;
    }
}
