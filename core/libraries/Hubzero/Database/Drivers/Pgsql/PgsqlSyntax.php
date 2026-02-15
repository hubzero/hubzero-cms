<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Pgsql;

/**
 * Database PostgreSQL query syntax class
 *
 * PostgreSQL is a powerful, open source object-relational database system.
 * This class extends the base SQL syntax with PostgreSQL-specific features.
 *
 * PostgreSQL-specific features supported:
 * - LIMIT x OFFSET y: PostgreSQL pagination syntax
 * - ON CONFLICT DO UPDATE: PostgreSQL upsert syntax
 * - JSON/JSONB operators (#>>, @>, jsonb_array_length)
 * - Type casting (::date, ::time, ::jsonb)
 * - EXTRACT() for date parts
 * - information_schema for column introspection
 *
 * PostgreSQL SQL features supported:
 * - Standard DML: SELECT, INSERT, UPDATE, DELETE with full clause support
 * - All JOIN types including FULL OUTER JOIN
 * - Window functions (ROW_NUMBER, RANK, etc.)
 * - Common Table Expressions (WITH clause)
 * - RETURNING clause for INSERT/UPDATE/DELETE
 * - Array types and operations
 * - JSON/JSONB operators and functions
 */
class PgsqlSyntax extends \Hubzero\Database\Drivers\Base\BaseSqlSyntax
{
    /**
     * PostgreSQL type mappings to normalized types
     *
     * Maps PostgreSQL-specific types to generic type names
     * that match what other drivers return.
     *
     * @var array
     */
    protected static $typeMappings = [
        // Integer types
        'smallint'    => 'smallint',
        'integer'     => 'int',
        'bigint'      => 'bigint',
        'int2'        => 'smallint',
        'int4'        => 'int',
        'int8'        => 'bigint',
        'serial'      => 'int',
        'bigserial'   => 'bigint',
        'smallserial' => 'smallint',

        // Floating point
        'real'             => 'float',
        'double precision' => 'double',
        'float4'           => 'float',
        'float8'           => 'double',
        'numeric'          => 'decimal',
        'decimal'          => 'decimal',
        'money'            => 'decimal',

        // Character types
        'character varying' => 'varchar',
        'character'         => 'char',
        'varchar'           => 'varchar',
        'char'              => 'char',
        'text'              => 'text',
        'name'              => 'varchar',

        // Binary types
        'bytea' => 'blob',

        // Date/time types
        'timestamp without time zone' => 'datetime',
        'timestamp with time zone'    => 'datetime',
        'date'                        => 'date',
        'time without time zone'      => 'time',
        'time with time zone'         => 'time',
        'interval'                    => 'varchar',

        // Boolean
        'boolean' => 'tinyint',
        'bool'    => 'tinyint',

        // JSON types
        'json'  => 'text',
        'jsonb' => 'text',

        // UUID
        'uuid' => 'varchar',

        // Network types
        'inet'    => 'varchar',
        'cidr'    => 'varchar',
        'macaddr' => 'varchar',

        // Geometric types
        'point'   => 'varchar',
        'line'    => 'varchar',
        'lseg'    => 'varchar',
        'box'     => 'varchar',
        'path'    => 'varchar',
        'polygon' => 'varchar',
        'circle'  => 'varchar',

        // Full text search
        'tsvector' => 'text',
        'tsquery'  => 'text',

        // Arrays (will be handled specially)
        'ARRAY' => 'text',
    ];

    /**
     * Builds an insert statement from the set params
     *
     * PostgreSQL doesn't have INSERT IGNORE syntax.
     * For conflict handling, we use ON CONFLICT DO NOTHING after the VALUES clause,
     * which is appended in buildValues().
     *
     * @return  string
     */
    public function buildInsert()
    {
        return 'INSERT INTO ' . $this->connection->quoteName($this->insert);
    }

    /**
     * Builds a values statement from the set params
     *
     * PostgreSQL 9.5+ supports ON CONFLICT DO NOTHING for INSERT IGNORE behavior.
     * This appends the ON CONFLICT clause when ignore=true.
     *
     * @return  string
     */
    public function buildValues()
    {
        // Call parent to build standard VALUES clause
        $sql = parent::buildValues();

        // Append ON CONFLICT DO NOTHING for INSERT IGNORE behavior
        if ($this->ignore) {
            $sql .= ' ON CONFLICT DO NOTHING';
        }

        return $sql;
    }

    /**
     * Builds a limit statement from the set params
     *
     * PostgreSQL uses "LIMIT x OFFSET y" syntax instead of MySQL's "LIMIT y,x"
     *
     * @return  string
     */
    public function buildLimit()
    {
        $string = 'LIMIT ';

        // Add the limit value (or ALL for unlimited)
        if (!empty($this->limit)) {
            $string .= (int) $this->limit;
        } else {
            $string .= 'ALL';
        }

        // Add offset if specified
        if (!empty($this->start)) {
            $string .= ' OFFSET ' . (int) $this->start;
        }

        return $string;
    }

    /**
     * Returns the proper query for generating a list of table columns
     *
     * PostgreSQL uses information_schema for column introspection
     *
     * @param   string  $table  The name of the database table
     * @return  string
     */
    public function getColumnsQuery($table)
    {
        return "SELECT
                column_name as \"Field\",
                data_type as \"Type\",
                is_nullable as \"Null\",
                column_default as \"Default\",
                CASE WHEN column_name IN (
                    SELECT a.attname
                    FROM pg_index i
                    JOIN pg_attribute a ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey)
                    WHERE i.indrelid = " . $this->connection->quote($table) . "::regclass
                    AND i.indisprimary
                ) THEN 'PRI' ELSE '' END as \"Key\"
            FROM information_schema.columns
            WHERE table_name = " . $this->connection->quote($table) . "
            AND table_schema = 'public'
            ORDER BY ordinal_position";
    }

    /**
     * Normalizes the results of the information_schema query
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
                $results[$field->Field] = $this->normalizeType($field->Type);
            }
        } else {
            foreach ($data as $field) {
                $results[$field->Field] = [
                    'name'      => $field->Field,
                    'type'      => $this->normalizeType($field->Type),
                    'raw_type'  => $field->Type,
                    'allownull' => (strtoupper($field->Null) === 'YES'),
                    'default'   => $this->normalizeDefault($field->Default),
                    'pk'        => ($field->Key === 'PRI')
                ];
            }
        }

        return $results;
    }

    /**
     * Normalize a PostgreSQL type to a generic type name
     *
     * @param   string  $type  The PostgreSQL type
     * @return  string  The normalized type
     */
    protected function normalizeType($type)
    {
        if (empty($type)) {
            return 'text';
        }

        $type = strtolower(trim($type));

        // Handle array types (e.g., "integer[]" or "character varying[]")
        if (substr($type, -2) === '[]') {
            return 'text';
        }

        // Check for exact match first
        if (isset(self::$typeMappings[$type])) {
            return self::$typeMappings[$type];
        }

        // Check for types with length specifiers
        foreach (self::$typeMappings as $pgType => $normalized) {
            if (strpos($type, $pgType) === 0) {
                return $normalized;
            }
        }

        // Default to text for unknown types
        return 'text';
    }

    /**
     * Normalize a default value from PostgreSQL
     *
     * PostgreSQL includes type casts in default values (e.g., 'value'::text)
     * and function calls need to be recognized.
     *
     * @param   mixed  $value  The raw default value
     * @return  mixed  The normalized default value
     */
    protected function normalizeDefault($value)
    {
        if ($value === null) {
            return null;
        }

        // Check for NULL literal
        if (strtoupper($value) === 'NULL') {
            return null;
        }

        // Handle nextval() for auto-increment columns
        if (strpos($value, 'nextval(') === 0) {
            return null;
        }

        // Remove type casts (e.g., 'value'::character varying)
        if (preg_match("/^'(.*?)'::.*$/s", $value, $matches)) {
            return str_replace("''", "'", $matches[1]);
        }

        // Handle unquoted type casts (e.g., 0::integer)
        if (preg_match('/^(.+?)::.*$/', $value, $matches)) {
            return $matches[1];
        }

        // Remove surrounding quotes
        if (preg_match("/^'(.*)'$/s", $value, $matches)) {
            return str_replace("''", "'", $matches[1]);
        }

        return $value;
    }

    /**
     * Get the SQL for checking if a table exists
     *
     * @param   string  $table  The table name
     * @return  string
     */
    public function getTableExistsQuery($table)
    {
        return "SELECT COUNT(*) FROM information_schema.tables " .
            "WHERE table_schema = 'public' AND table_name = " .
            $this->connection->quote($table);
    }

    /**
     * Get the SQL for retrieving table names
     *
     * @return  string
     */
    public function getTableListQuery()
    {
        return "SELECT table_name FROM information_schema.tables " .
            "WHERE table_schema = 'public' AND table_type = 'BASE TABLE' " .
            "ORDER BY table_name";
    }

    // =========================================================================
    // JSON Operations - PostgreSQL specific overrides
    // =========================================================================

    /**
     * Sets a JSON path extraction where clause
     *
     * PostgreSQL uses the #>> operator to extract text at a path,
     * or -> and ->> for traversal.
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
        $pgPath = $this->convertToPostgresPath($path);
        $quotedColumn = $this->connection->quoteName($column);

        // Use #>> operator to extract text at path (works with both json and jsonb)
        $raw = "{$quotedColumn} #>> '{$pgPath}' {$operator} ?";

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
     * PostgreSQL uses the @> containment operator for JSONB columns.
     * Note: This requires the column to be JSONB, not JSON.
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

        // JSON value must be encoded for containment check
        $jsonValue = json_encode($value);

        if ($path !== null) {
            // Extract the array at path, then check containment
            $pgPath = $this->convertToPostgresPath($path);
            // Use #> to get jsonb at path, then check containment
            $raw = "({$quotedColumn} #> '{$pgPath}')::jsonb @> ?::jsonb";
        } else {
            // Check if the whole column contains the value
            // Cast to jsonb to ensure @> operator works
            $raw = "{$quotedColumn}::jsonb @> ?::jsonb";
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
     * PostgreSQL uses jsonb_array_length() for JSONB columns.
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
            $pgPath = $this->convertToPostgresPath($path);
            // Extract array at path, then get length
            $raw = "jsonb_array_length(({$quotedColumn} #> '{$pgPath}')::jsonb) {$operator} ?";
        } else {
            // Get length of top-level array
            $raw = "jsonb_array_length({$quotedColumn}::jsonb) {$operator} ?";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [(int) $value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    /**
     * Converts dot notation path to PostgreSQL JSON path array syntax
     *
     * PostgreSQL uses array notation for JSON paths with #> and #>> operators.
     *
     * Examples:
     *   "name" -> "{name}"
     *   "user.name" -> "{user,name}"
     *   "items.0.price" -> "{items,0,price}"
     *
     * @param   string  $path  The dot-notation path
     * @return  string  The PostgreSQL path array (e.g., "{user,name}")
     */
    protected function convertToPostgresPath($path)
    {
        $parts = explode('.', $path);
        return '{' . implode(',', $parts) . '}';
    }

    // =========================================================================
    // Date/Time Operations - PostgreSQL specific overrides
    // =========================================================================

    /**
     * Sets a date/time extraction where clause
     *
     * PostgreSQL uses casts (::date, ::time) and EXTRACT() function.
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
                $raw = "{$quotedColumn}::date {$operator} ?";
                break;
            case 'time':
                $raw = "{$quotedColumn}::time {$operator} ?";
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
                $raw = "{$quotedColumn}::date {$operator} ?";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [$value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    // =========================================================================
    // Table Operations - PostgreSQL specific overrides
    // =========================================================================

    /**
     * Get the SQL statements to truncate a table
     *
     * PostgreSQL uses TRUNCATE TABLE with RESTART IDENTITY to reset sequences.
     *
     * @param   string  $table  The table to truncate
     * @return  array   Array of SQL statements to execute
     */
    public function getTruncateStatements($table)
    {
        return ['TRUNCATE TABLE ' . $this->connection->quoteName($table) . ' RESTART IDENTITY'];
    }

    /**
     * Builds an upsert statement from the set params
     *
     * PostgreSQL uses "INSERT INTO ... ON CONFLICT (...) DO UPDATE SET" syntax.
     * Requires conflict columns to be specified (defaults to first column if not provided).
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

        // PostgreSQL requires conflict columns to be specified
        // Default to first column (usually the primary key) if not specified
        $conflictColumns = !empty($this->upsertConflictColumns)
            ? $this->upsertConflictColumns
            : [$columns[0]];

        $quotedConflict = array_map(
            fn($col) => $this->connection->quoteName($col),
            $conflictColumns
        );

        $updates = [];
        foreach ($this->upsertUpdateColumns as $col) {
            $quotedCol = $this->connection->quoteName($col);
            // PostgreSQL uses EXCLUDED pseudo-table for the proposed values
            $updates[] = "$quotedCol = EXCLUDED.$quotedCol";
        }

        return sprintf(
            'INSERT INTO %s (%s) VALUES (%s) ON CONFLICT (%s) DO UPDATE SET %s',
            $this->connection->quoteName($this->upsertTable),
            implode(', ', $quotedColumns),
            implode(', ', $placeholders),
            implode(', ', $quotedConflict),
            implode(', ', $updates)
        );
    }

    /**
     * Builds a bulk upsert statement
     *
     * PostgreSQL uses multi-row VALUES with ON CONFLICT ... DO UPDATE:
     * INSERT INTO t (cols) VALUES (...), (...) ON CONFLICT (keys)
     *   DO UPDATE SET col = EXCLUDED.col
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

        $valueSets = [];
        foreach ($this->upsertManyRows as $row) {
            $placeholders = [];
            foreach ($columns as $column) {
                $placeholders[] = '?';
                $value = $row[$column] ?? null;
                $this->bind(
                    is_string($value) ? trim($value) : $value
                );
            }
            $valueSets[] = '(' . implode(', ', $placeholders) . ')';
        }

        $conflictColumns = !empty($this->upsertManyConflictColumns)
            ? $this->upsertManyConflictColumns
            : [$columns[0]];

        $quotedConflict = array_map(
            fn($col) => $this->connection->quoteName($col),
            $conflictColumns
        );

        $updates = [];
        foreach ($this->upsertManyUpdateColumns as $col) {
            $quotedCol = $this->connection->quoteName($col);
            $updates[] = "$quotedCol = EXCLUDED.$quotedCol";
        }

        return sprintf(
            'INSERT INTO %s (%s) VALUES %s'
            . ' ON CONFLICT (%s) DO UPDATE SET %s',
            $this->connection->quoteName($this->upsertManyTable),
            implode(', ', $quotedColumns),
            implode(', ', $valueSets),
            implode(', ', $quotedConflict),
            implode(', ', $updates)
        );
    }

    // =========================================================================
    // Bulk Insert Operations - PostgreSQL specific override
    // =========================================================================

    /**
     * Builds a bulk insert statement from the set params
     *
     * PostgreSQL uses "ON CONFLICT DO NOTHING" instead of MySQL's "INSERT IGNORE".
     * Otherwise, the multi-row VALUES syntax is the same.
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
        $quotedColumns = [];

        foreach ($columns as $column) {
            $quotedColumns[] = $this->connection->quoteName($column);
        }

        // Build VALUES clause with multiple rows
        $valueSets = [];
        foreach ($this->insertManyRows as $row) {
            $placeholders = [];
            foreach ($columns as $column) {
                $placeholders[] = '?';
                $value = $row[$column] ?? null;
                $this->bind(is_string($value) ? trim($value) : $value);
            }
            $valueSets[] = '(' . implode(', ', $placeholders) . ')';
        }

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES %s',
            $this->connection->quoteName($this->insertManyTable),
            implode(', ', $quotedColumns),
            implode(', ', $valueSets)
        );

        // PostgreSQL uses ON CONFLICT DO NOTHING for INSERT IGNORE behavior
        if ($this->insertManyIgnore) {
            $sql .= ' ON CONFLICT DO NOTHING';
        }

        return $sql;
    }

    /**
     * Get the value to use for empty IN() sets
     *
     * PostgreSQL is strict about types — '' can't compare to integer columns.
     * Using NULL ensures IN(NULL) is always false regardless of column type.
     *
     * @return string
     */
    protected function getEmptySetValue()
    {
        return 'NULL';
    }

    // =========================================================================
    // Expression Builder Support - PostgreSQL specific overrides
    // =========================================================================

    /**
     * Build a function expression into PostgreSQL-specific SQL
     *
     * Overrides MySQL syntax for functions that differ in PostgreSQL:
     * - CONCAT: Uses concat() or || operator
     * - IFNULL: Uses COALESCE (standard SQL)
     * - SUBSTRING: Uses SUBSTRING(str FROM start FOR length)
     * - LENGTH: Uses char_length() for character count
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
                // PostgreSQL supports both concat() and || operator
                // Using concat() for consistency with MySQL behavior (handles NULLs)
                $parts = array_map([$this, 'buildExpressionArgument'], $args);
                return 'concat(' . implode(', ', $parts) . ')';

            case 'LENGTH':
                // PostgreSQL uses char_length() for character count
                return 'char_length(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'SUBSTRING':
                // PostgreSQL uses SUBSTRING(str FROM start FOR length) syntax
                $col = $this->buildExpressionArgument($args[0]);
                $start = (int) $args[1];
                if ($args[2] !== null) {
                    return "SUBSTRING({$col} FROM {$start} FOR " . (int) $args[2] . ')';
                }
                return "SUBSTRING({$col} FROM {$start})";

            // Conditional functions
            case 'IFNULL':
                // PostgreSQL uses COALESCE (standard SQL)
                return 'COALESCE('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ')';

            // Math functions
            case 'CEIL':
                // PostgreSQL uses ceil() same as MySQL
                return 'ceil(' . $this->buildExpressionArgument($args[0]) . ')';

            // Date/time functions
            case 'NOW':
                // PostgreSQL uses NOW() or CURRENT_TIMESTAMP
                return 'NOW()';

            case 'CURRENT_TIMESTAMP':
                return 'CURRENT_TIMESTAMP';

            case 'DATE':
                // PostgreSQL uses casting: column::date
                return '(' . $this->buildExpressionArgument($args[0]) . ')::date';

            case 'TIME':
                // PostgreSQL uses casting: column::time
                return '(' . $this->buildExpressionArgument($args[0]) . ')::time';

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
                // PostgreSQL uses TO_CHAR() with different format codes
                $format = $this->convertDateFormat($args[1]);
                return "TO_CHAR(" . $this->buildExpressionArgument($args[0]) . ", '{$format}')";

            // Sequence functions (native PostgreSQL syntax)
            case 'NEXTVAL':
                return "nextval('" . $args[0] . "')";

            case 'CURRVAL':
                return "currval('" . $args[0] . "')";

            default:
                // Fall back to parent for standard functions
                return parent::buildFunctionExpression($expression);
        }
    }
}
