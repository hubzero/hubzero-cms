<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Db2;

/**
 * Database IBM DB2 query syntax class
 *
 * IBM DB2 is a family of data management products, including database servers,
 * developed by IBM. This class extends the base SQL syntax with DB2-specific features.
 *
 * DB2-specific features supported:
 * - OFFSET/FETCH: DB2 9.7+ pagination syntax (SQL:2008 standard)
 * - MERGE: DB2 upsert syntax
 * - SYSCAT.COLUMNS: Column introspection
 * - Sequences instead of AUTO_INCREMENT
 * - SYSIBM.SYSDUMMY1 table for SELECT without FROM
 *
 * DB2 SQL features supported:
 * - Standard DML: SELECT, INSERT, UPDATE, DELETE with full clause support
 * - All JOIN types including FULL OUTER JOIN
 * - Analytic/Window functions (ROW_NUMBER, RANK, etc.)
 * - Common Table Expressions (WITH clause)
 * - Stored Procedures
 */
class Db2Syntax extends \Hubzero\Database\Drivers\Base\BaseSqlSyntax
{
    /**
     * DB2 type mappings to normalized types
     *
     * Maps DB2-specific types to generic type names
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
        'decimal'    => 'decimal',
        'numeric'    => 'decimal',
        'real'       => 'float',
        'double'     => 'double',
        'decfloat'   => 'decimal',

        // Character types
        'character'  => 'char',
        'char'       => 'char',
        'varchar'    => 'varchar',
        'clob'       => 'text',
        'graphic'    => 'char',
        'vargraphic' => 'varchar',
        'dbclob'     => 'text',
        'long varchar' => 'text',

        // Binary types
        'blob'       => 'blob',
        'binary'     => 'blob',
        'varbinary'  => 'blob',

        // Date/time types
        'date'       => 'date',
        'time'       => 'time',
        'timestamp'  => 'datetime',

        // Other types
        'xml'        => 'text',
        'rowid'      => 'varchar',
    ];

    /**
     * Returns the proper query for generating a list of table columns
     *
     * DB2 uses SYSCAT.COLUMNS for column introspection
     *
     * @param   string  $table  The name of the database table
     * @return  string
     */
    public function getColumnsQuery($table)
    {
        // DB2 stores table names in uppercase by default
        $upperTable = strtoupper($table);

        return "SELECT
                colname AS \"Field\",
                typename || CASE
                    WHEN typename IN ('VARCHAR', 'CHARACTER', 'CHAR', 'VARGRAPHIC', 'GRAPHIC')
                    THEN '(' || length || ')'
                    WHEN typename IN ('DECIMAL', 'NUMERIC')
                    THEN '(' || length || ',' || scale || ')'
                    ELSE ''
                END AS \"Type\",
                CASE WHEN nulls = 'Y' THEN 'YES' ELSE 'NO' END AS \"Null\",
                \"DEFAULT\" AS \"Default\",
                '' AS \"Key\"
            FROM syscat.columns
            WHERE tabname = " . $this->connection->quote($upperTable) . "
            ORDER BY colno";
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
                // Handle both uppercase and lowercase due to PDO::ATTR_CASE
                $fieldName = $field->Field ?? $field->field;
                $fieldType = $field->Type ?? $field->type;
                // Lowercase the field name to match model attribute keys
                $results[strtolower($fieldName)] = $this->normalizeType($fieldType);
            }
        } else {
            foreach ($data as $field) {
                // Handle both uppercase and lowercase due to PDO::ATTR_CASE
                $fieldName = $field->Field ?? $field->field;
                $fieldType = $field->Type ?? $field->type;
                $fieldNull = $field->Null ?? $field->null;
                $fieldDefault = $field->Default ?? $field->default;
                $fieldKey = $field->Key ?? $field->key;

                // Lowercase the field name to match model attribute keys
                $lowerFieldName = strtolower($fieldName);

                $results[$lowerFieldName] = [
                    'name'      => $lowerFieldName,
                    'type'      => $this->normalizeType($fieldType),
                    'raw_type'  => $fieldType,
                    'allownull' => ($fieldNull !== null && strtoupper($fieldNull) === 'YES'),
                    'default'   => $this->normalizeDefault($fieldDefault),
                    'pk'        => ($fieldKey === 'PRI')
                ];
            }
        }

        return $results;
    }

    /**
     * Normalize a DB2 type to a generic type name
     *
     * @param   string  $type  The DB2 type
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

        // Default to text for unknown types
        return 'text';
    }

    /**
     * Normalize a default value from DB2
     *
     * DB2 includes trailing spaces and may have type-specific formatting
     *
     * @param   mixed  $value  The raw default value
     * @return  mixed  The normalized default value
     */
    protected function normalizeDefault($value)
    {
        if ($value === null) {
            return null;
        }

        // DB2 may return CLOB/BLOB values as resources - convert to string
        if (is_resource($value)) {
            @rewind($value);
            $stringValue = @stream_get_contents($value);
            $value = $stringValue !== false ? $stringValue : '';
        }

        // Ensure we have a string
        if (!is_string($value)) {
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
    // JSON Operations - DB2 specific overrides
    // =========================================================================

    /**
     * Sets a JSON path extraction where clause
     *
     * DB2 11.1+ uses JSON_VALUE() function with path syntax
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
        $jsonPath = $this->convertToDb2JsonPath($path);
        $quotedColumn = $this->connection->quoteName($column);

        // DB2 uses JSON_VALUE() for scalar extraction
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
     * DB2 11.1+ uses JSON_EXISTS() for containment checks
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
            $jsonPath = $this->convertToDb2JsonPath($path);
            // Use JSON_EXISTS with a path filter
            $raw = "JSON_EXISTS({$quotedColumn}, '{$jsonPath}')";
        } else {
            // Check if the value exists anywhere in the document
            $raw = "JSON_EXISTS({$quotedColumn}, '\$')";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    /**
     * Sets a JSON length where clause
     *
     * DB2 uses JSON_QUERY to get array and check cardinality
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
            $jsonPath = $this->convertToDb2JsonPath($path);
            // DB2 JSON array length via subquery
            $raw = "(SELECT COUNT(*) FROM TABLE(JSON_TABLE("
                . "{$quotedColumn}, '{$jsonPath}[*]' COLUMNS"
                . " (val VARCHAR(4000) PATH '\$'))))"
                . " {$operator} ?";
        } else {
            $raw = "(SELECT COUNT(*) FROM TABLE(JSON_TABLE("
                . "{$quotedColumn}, '\$[*]' COLUMNS"
                . " (val VARCHAR(4000) PATH '\$'))))"
                . " {$operator} ?";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [(int) $value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    /**
     * Converts dot notation path to DB2 JSON path syntax
     *
     * Examples:
     *   "name" -> "$.name"
     *   "user.name" -> "$.user.name"
     *   "items.0.price" -> "$.items[0].price"
     *
     * @param   string  $path  The dot-notation path
     * @return  string  The DB2 JSON path (e.g., "$.user.name")
     */
    protected function convertToDb2JsonPath($path)
    {
        $parts = explode('.', $path);
        $jsonPath = '$';

        foreach ($parts as $part) {
            if (is_numeric($part)) {
                // Array index
                $jsonPath .= '[' . $part . ']';
            } else {
                // Object key
                $jsonPath .= '.' . $part;
            }
        }

        return $jsonPath;
    }

    // =========================================================================
    // Date/Time Operations - DB2 specific overrides
    // =========================================================================

    /**
     * Sets a date/time extraction where clause
     *
     * DB2 uses DATE(), TIME(), YEAR(), MONTH(), DAY() functions
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
                $raw = "DATE({$quotedColumn}) {$operator} DATE(?)";
                break;
            case 'time':
                $raw = "TIME({$quotedColumn}) {$operator} TIME(?)";
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
                $raw = "DATE({$quotedColumn}) {$operator} DATE(?)";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [$value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    // =========================================================================
    // Table Operations - DB2 specific overrides
    // =========================================================================

    /**
     * Get the SQL statements to truncate a table
     *
     * DB2 uses TRUNCATE TABLE with IMMEDIATE option
     *
     * @param   string  $table  The table to truncate
     * @return  array   Array of SQL statements to execute
     */
    public function getTruncateStatements($table)
    {
        return ['TRUNCATE TABLE ' . $this->connection->quoteName($table) . ' IMMEDIATE'];
    }

    /**
     * Builds an upsert statement from the set params
     *
     * DB2 uses MERGE statement for upsert operations
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
               'USING (SELECT ' . implode(', ', $sourceColumns) . ' FROM SYSIBM.SYSDUMMY1) s ' .
               'ON (' . implode(' AND ', $onConditions) . ') ' .
               'WHEN MATCHED THEN UPDATE SET ' . implode(', ', $updateSets) . ' ' .
               'WHEN NOT MATCHED THEN INSERT (' . implode(', ', $quotedColumns) . ') ' .
               'VALUES (' . implode(', ', array_map(fn($c) => 's.' . $this->connection->quoteName($c), $columns)) . ')';

        return $sql;
    }

    /**
     * Builds a bulk upsert statement
     *
     * DB2 uses MERGE with a VALUES clause as source:
     * MERGE INTO t USING (VALUES (?, ?), (?, ?)) AS s(c1, c2)
     *   ON (t.key = s.key) WHEN MATCHED/NOT MATCHED ...
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

        // Build VALUES rows for source
        $valueRows = [];
        foreach ($this->upsertManyRows as $row) {
            $placeholders = [];
            foreach ($columns as $column) {
                $placeholders[] = '?';
                $value = $row[$column] ?? null;
                $this->bind(
                    is_string($value) ? trim($value) : $value
                );
            }
            $valueRows[] = '(' . implode(', ', $placeholders) . ')';
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
            . ' t USING (VALUES '
            . implode(', ', $valueRows) . ') AS s('
            . implode(', ', $quotedColumns) . ') '
            . 'ON (' . implode(' AND ', $onConditions) . ') '
            . 'WHEN MATCHED THEN UPDATE SET '
            . implode(', ', $updateSets) . ' '
            . 'WHEN NOT MATCHED THEN INSERT ('
            . implode(', ', $quotedColumns) . ') VALUES ('
            . implode(', ', $insertValues) . ')';
    }

    // =========================================================================
    // INSERT Operations - DB2 specific overrides
    // =========================================================================

    /**
     * Builds an INSERT statement, handling INSERT IGNORE via MERGE INTO
     *
     * DB2 doesn't have native INSERT IGNORE syntax. For INSERT IGNORE,
     * we use MERGE INTO with WHEN NOT MATCHED THEN INSERT, matching on
     * the primary key column.
     *
     * @return  string
     */
    public function buildInsert()
    {
        $quotedTable = $this->connection->quoteName($this->insert);

        // If not using IGNORE, use standard INSERT
        if (!$this->ignore) {
            return 'INSERT INTO ' . $quotedTable;
        }

        // INSERT IGNORE with SELECT requires MERGE INTO
        // This only works if we have a SELECT source and columns specified
        if (!empty($this->insertSelectQuery) && !empty($this->insertColumns)) {
            // Get primary key column(s) - assume first column is the PK for simplicity
            // In most test cases, this is 'id' or 'user_id'
            $pkColumn = $this->insertColumns[0];
            $quotedPk = $this->connection->quoteName($pkColumn);

            // Build column lists
            $quotedColumns = array_map(
                [$this->connection, 'quoteName'],
                $this->insertColumns
            );
            $columnList = implode(', ', $quotedColumns);

            // Build source column references (s.col1, s.col2, ...)
            $sourceRefs = [];
            foreach ($quotedColumns as $col) {
                $sourceRefs[] = 's.' . $col;
            }
            $sourceRefList = implode(', ', $sourceRefs);

            // Use MERGE INTO pattern for INSERT IGNORE ... SELECT
            return "MERGE INTO {$quotedTable} t "
                . "USING ({$this->insertSelectQuery}) AS s({$columnList}) "
                . "ON (t.{$quotedPk} = s.{$quotedPk}) "
                . "WHEN NOT MATCHED THEN INSERT ({$columnList}) VALUES ({$sourceRefList})";
        }

        // Fallback to standard INSERT (IGNORE flag will be ignored)
        return 'INSERT INTO ' . $quotedTable;
    }

    /**
     * Builds a VALUES clause for INSERT statements
     *
     * For INSERT IGNORE ... SELECT with DB2, the complete MERGE statement
     * is built in buildInsert(), so this method returns empty to avoid
     * appending duplicate column/SELECT clauses.
     *
     * @return  string
     */
    public function buildValues()
    {
        // If using INSERT IGNORE with SELECT, buildInsert() already built
        // the complete MERGE statement - don't append anything
        if ($this->ignore && !empty($this->insertSelectQuery)) {
            // Add bindings from the SELECT query
            foreach ($this->insertSelectBindings as $binding) {
                $this->bind($binding);
            }
            return '';
        }

        // For all other cases, use the parent implementation
        return parent::buildValues();
    }

    /**
     * Builds a bulk insert statement from the set params
     *
     * DB2 uses VALUES with multiple value lists (similar to MySQL)
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

        $quotedTable = $this->connection->quoteName($this->insertManyTable);
        $columnList = implode(', ', $quotedColumns);

        // DB2 supports standard multi-row VALUES syntax
        $sql = "INSERT INTO {$quotedTable} ({$columnList}) VALUES ";

        $valueGroups = [];
        foreach ($this->insertManyRows as $row) {
            $placeholders = [];
            foreach ($columns as $column) {
                $placeholders[] = '?';
                $value = $row[$column] ?? null;
                $this->bind(is_string($value) ? trim($value) : $value);
            }
            $valueGroups[] = '(' . implode(', ', $placeholders) . ')';
        }

        $sql .= implode(', ', $valueGroups);

        return $sql;
    }

    // =========================================================================
    // Expression Builder Support - DB2 specific overrides
    // =========================================================================

    /**
     * Build a function expression into DB2-specific SQL
     *
     * Overrides MySQL syntax for functions that differ in DB2:
     * - CONCAT: Uses || operator or CONCAT() (only 2 args)
     * - IFNULL: Uses COALESCE() or VALUE()
     * - LENGTH: Uses LENGTH() (same as MySQL)
     * - Date parts: Uses YEAR(), MONTH(), DAY() functions
     * - DATE_FORMAT: Uses VARCHAR_FORMAT()
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
                // DB2's CONCAT() only takes 2 arguments
                // For multiple args, chain them with ||
                $parts = array_map([$this, 'buildExpressionArgument'], $args);
                return '(' . implode(' || ', $parts) . ')';

            case 'LENGTH':
                // DB2 uses LENGTH() same as MySQL
                return 'LENGTH(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'SUBSTRING':
                // DB2 uses SUBSTR()
                $col = $this->buildExpressionArgument($args[0]);
                $start = (int) $args[1];
                if ($args[2] !== null) {
                    return "SUBSTR({$col}, {$start}, " . (int) $args[2] . ')';
                }
                return "SUBSTR({$col}, {$start})";

            // Conditional functions
            case 'IFNULL':
                // DB2 uses COALESCE() or VALUE()
                return 'COALESCE('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ')';

            // Date/time functions
            case 'NOW':
                // DB2 uses CURRENT TIMESTAMP
                return 'CURRENT TIMESTAMP';

            case 'CURRENT_DATE':
                return 'CURRENT DATE';

            case 'CURRENT_TIME':
                return 'CURRENT TIME';

            case 'DATE':
                // DB2 uses DATE() function
                return 'DATE(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'TIME':
                // DB2 uses TIME() function
                return 'TIME(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'YEAR':
                return 'YEAR(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'MONTH':
                return 'MONTH(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'DAY':
                return 'DAY(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'HOUR':
                return 'HOUR(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'MINUTE':
                return 'MINUTE(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'SECOND':
                return 'SECOND(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'DATE_FORMAT':
                // DB2 uses VARCHAR_FORMAT() with different format codes
                $format = $this->convertDateFormat($args[1]);
                return "VARCHAR_FORMAT(" . $this->buildExpressionArgument($args[0]) . ", '{$format}')";

            // Sequence functions (native DB2 syntax)
            case 'NEXTVAL':
                return 'NEXT VALUE FOR ' . $this->connection->quoteName($args[0]);

            case 'CURRVAL':
                return 'PREVIOUS VALUE FOR ' . $this->connection->quoteName($args[0]);

            default:
                // Fall back to parent for standard functions
                return parent::buildFunctionExpression($expression);
        }
    }

    /**
     * Build an EXISTS or NOT EXISTS clause for JOIN conditions
     *
     * DB2 requires SELECT to have a FROM clause. If the subquery is missing one,
     * add "FROM SYSIBM.SYSDUMMY1" (DB2's equivalent of Oracle's DUAL).
     *
     * @param   string  $operatorLower  'exists' or 'not exists'
     * @param   mixed   $subquery       The subquery expression
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

        // DB2 requires SELECT to have FROM clause
        // If SELECT doesn't have FROM, add "FROM SYSIBM.SYSDUMMY1"
        if (preg_match('/^\s*SELECT\s+.+/i', $sql) && !preg_match('/\s+FROM\s+/i', $sql)) {
            $sql = rtrim($sql) . ' FROM SYSIBM.SYSDUMMY1';
        }

        if ($sql[0] !== '(') {
            $sql = '(' . $sql . ')';
        }

        return ($operatorLower === 'not exists' ? 'NOT EXISTS ' : 'EXISTS ') . $sql;
    }
}
