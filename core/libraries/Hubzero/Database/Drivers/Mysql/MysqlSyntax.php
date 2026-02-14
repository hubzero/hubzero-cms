<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Mysql;

/**
 * Database MySQL query syntax class
 *
 * MySQL is the world's most popular open source relational database. This class
 * extends the base SQL syntax with MySQL-specific features.
 *
 * MySQL-specific features supported:
 * - INSERT IGNORE: Skip duplicate key errors
 * - LIMIT offset,count: MySQL's pagination syntax
 * - ON DUPLICATE KEY UPDATE: MySQL's upsert syntax
 * - JSON functions (JSON_EXTRACT, JSON_CONTAINS, JSON_LENGTH)
 * - Date functions (DATE, TIME, YEAR, MONTH, DAY)
 * - SHOW FULL COLUMNS: Column introspection
 *
 * MySQL 5.7+ features:
 * - JSON functions (JSON_EXTRACT, JSON_OBJECT, etc.)
 * - Generated columns (VIRTUAL and STORED)
 * - Native fulltext search improvements
 *
 * MySQL 8.0+ features:
 * - Window functions (ROW_NUMBER, RANK, etc.)
 * - Common Table Expressions (WITH clause)
 * - LATERAL derived tables
 * - JSON_TABLE function
 * - CHECK constraints (actually enforced)
 *
 * This class serves as the base for MySQL-compatible databases.
 * MariaDB and Percona syntax classes extend this class.
 */
class MysqlSyntax extends \Hubzero\Database\Drivers\Base\BaseSqlSyntax
{
    /**
     * Whether we are emitting a FULL OUTER JOIN emulation query.
     *
     * When true, buildSelect() returns a full UNION query and the remaining
     * clause builders return empty strings to avoid duplicating SQL.
     *
     * @var  bool
     */
    protected $fullJoinUnionBuilt = false;

    // =========================================================================
    // JSON Operations (MySQL-specific)
    // =========================================================================

    /**
     * Sets a JSON path extraction where clause
     *
     * MySQL uses JSON_EXTRACT() with path syntax like "$.user.name"
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
        $this->setJsonPathWhereWithMySqlFunctions(
            $column,
            $path,
            $operator,
            $value,
            $logical,
            $depth
        );
    }

    /**
     * Sets a JSON contains where clause
     *
     * MySQL uses JSON_CONTAINS() function.
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
        $this->setJsonContainsWhereWithMySqlFunctions(
            $column,
            $value,
            $path,
            $logical,
            $depth
        );
    }

    /**
     * Sets a JSON length where clause
     *
     * MySQL uses JSON_LENGTH() function.
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
        $this->setJsonLengthWhereWithMySqlFunctions(
            $column,
            $operator,
            $value,
            $path,
            $logical,
            $depth
        );
    }

    // =========================================================================
    // Date/Time Operations (MySQL-specific)
    // =========================================================================

    /**
     * Sets a date/time extraction where clause
     *
     * MySQL uses DATE(), TIME(), YEAR(), MONTH(), DAY() functions.
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
        $function = $this->getDateFunction($part);
        $this->setDateWhereWithUnaryFunction(
            $column,
            $operator,
            $value,
            $logical,
            $depth,
            $function
        );
    }

    /**
     * Get the appropriate date extraction function for MySQL
     *
     * @param   string  $part  The date part: 'date', 'time', 'year', 'month', 'day'
     * @return  string  The MySQL function name
     */
    protected function getDateFunction($part)
    {
        $functions = [
            'date'  => 'DATE',
            'time'  => 'TIME',
            'year'  => 'YEAR',
            'month' => 'MONTH',
            'day'   => 'DAY',
        ];

        return $this->getSimpleDateFunction($part, $functions, 'DATE');
    }

    // =========================================================================
    // Build Methods (MySQL-specific overrides)
    // =========================================================================

    /**
     * Builds an update statement from the set params
     *
     * MySQL supports UPDATE ... JOIN ... SET syntax, where JOINs
     * appear directly in the UPDATE clause rather than using FROM.
     *
     * @return  string
     */
    public function buildUpdate()
    {
        return $this->buildUpdateWithJoinClause();
    }

    /**
     * Build a CONCAT expression
     *
     * MySQL uses the CONCAT() function instead of the SQL standard
     * || operator.
     *
     * @param   array  $parts  Array of column names or quoted strings
     * @return  string
     */
    public function buildConcat(array $parts)
    {
        return $this->buildConcatFunction($parts);
    }

    /**
     * Builds an insert statement from the set params
     *
     * MySQL supports INSERT IGNORE syntax.
     *
     * @return  string
     */
    public function buildInsert()
    {
        return 'INSERT ' . (($this->ignore) ? 'IGNORE ' : '') . 'INTO ' . $this->connection->quoteName($this->insert);
    }

    /**
     * Builds a select statement from the set params
     *
     * MySQL/MariaDB do not support FULL OUTER JOIN. We emulate it using:
     *   LEFT JOIN
     *   UNION ALL
     *   RIGHT JOIN ... WHERE left_key IS NULL
     *
     * This is more expensive, but it preserves correctness. A simpler approach
     * (degrading FULL to LEFT) is faster but drops right-only rows, which is a
     * semantic mismatch when the caller explicitly asked for FULL.
     *
     * To guarantee identical behavior across all drivers, FULL JOIN usage
     * is restricted to a single FULL JOIN between two base tables. Additional
     * JOINs are allowed only if they are INNER or LEFT joins that reference
     * tables introduced earlier in the join order.
     *
     * @return  string
     */
    protected function buildSelect()
    {
        if (!$this->hasFullJoin()) {
            $this->fullJoinUnionBuilt = false;
            return parent::buildSelect();
        }

        $this->fullJoinUnionBuilt = true;

        $branches = $this->expandFullJoinBranches();
        if (empty($branches)) {
            $this->fullJoinUnionBuilt = false;
            return parent::buildSelect();
        }

        $originalJoins = $this->join;
        $sqlParts = [];
        $allBindings = [];

        foreach ($branches as $branch) {
            $this->join = $branch['joins'];
            $this->clearBindings();

            $leftWhere = !empty($this->where) ? parent::buildWhere() : '';
            $whereSql = $this->appendFiltersToWhere($leftWhere, $branch['filters']);

            $selectSql = parent::buildSelect();
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
     * Builds a join statement from the set params
     *
     * @return  string
     */
    public function buildJoin()
    {
        if ($this->isBuildingFullJoinUnionQuery()) {
            return '';
        }

        return parent::buildJoin();
    }

    /**
     * Builds a from statement from the set params
     *
     * @return  string
     */
    protected function buildFrom()
    {
        if ($this->isBuildingFullJoinUnionQuery()) {
            return '';
        }

        return parent::buildFrom();
    }

    /**
     * Builds a where statement from the set params
     *
     * MySQL handles JOINs in buildUpdate() directly, so we bypass
     * the base class UPDATE+JOIN dispatch and call buildWhereClause()
     * to get the raw WHERE clause.
     *
     * @return  string
     */
    protected function buildWhere()
    {
        if ($this->isBuildingFullJoinUnionQuery()) {
            return '';
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
        if ($this->isBuildingFullJoinUnionQuery()) {
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
        if ($this->isBuildingFullJoinUnionQuery()) {
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
        if ($this->isBuildingFullJoinUnionQuery()) {
            return '';
        }

        return parent::buildOrder();
    }

    /**
     * Builds a limit statement from the set params
     *
     * MySQL uses "LIMIT offset,count" syntax instead of SQL:2008 OFFSET/FETCH.
     *
     * @return  string
     */
    public function buildLimit()
    {
        if ($this->isBuildingFullJoinUnionQuery()) {
            return '';
        }

        return $this->buildMySqlStyleLimitClause();
    }

    /**
     * Builds a LIMIT statement for UNION queries
     *
     * Uses MySQL's LIMIT offset,count syntax but bypasses the FULL JOIN guard
     * because this is applied to the unioned result set.
     *
     * @return  string
     */
    protected function buildUnionLimit(): string
    {
        return $this->buildMySqlStyleLimitClause();
    }

    /**
     * Builds an upsert statement from the set params
     *
     * MySQL uses "INSERT INTO ... ON DUPLICATE KEY UPDATE" syntax.
     * The conflict is determined by PRIMARY KEY or UNIQUE indexes.
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

        $updates = [];
        foreach ($this->upsertUpdateColumns as $col) {
            $quotedCol = $this->connection->quoteName($col);
            $updates[] = "$quotedCol = VALUES($quotedCol)";
        }

        return sprintf(
            'INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s',
            $this->connection->quoteName($this->upsertTable),
            implode(', ', $quotedColumns),
            implode(', ', $placeholders),
            implode(', ', $updates)
        );
    }

    /**
     * Builds a bulk upsert statement
     *
     * MySQL uses multi-row VALUES with ON DUPLICATE KEY UPDATE:
     * INSERT INTO t (cols) VALUES (...), (...) ON DUPLICATE KEY UPDATE
     *   col = VALUES(col)
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

        $updates = [];
        foreach ($this->upsertManyUpdateColumns as $col) {
            $quotedCol = $this->connection->quoteName($col);
            $updates[] = "$quotedCol = VALUES($quotedCol)";
        }

        return sprintf(
            'INSERT INTO %s (%s) VALUES %s'
            . ' ON DUPLICATE KEY UPDATE %s',
            $this->connection->quoteName($this->upsertManyTable),
            implode(', ', $quotedColumns),
            implode(', ', $valueSets),
            implode(', ', $updates)
        );
    }

    /**
     * Builds a bulk insert statement from the set params
     *
     * MySQL supports multi-row VALUES syntax with optional IGNORE:
     * INSERT [IGNORE] INTO table (col1, col2) VALUES (?, ?), (?, ?), ...
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

        $ignore = $this->insertManyIgnore ? 'IGNORE ' : '';

        return sprintf(
            'INSERT %sINTO %s (%s) VALUES %s',
            $ignore,
            $this->connection->quoteName($this->insertManyTable),
            implode(', ', $quotedColumns),
            implode(', ', $valueSets)
        );
    }

    // =========================================================================
    // Column Introspection (MySQL-specific)
    // =========================================================================

    /**
     * Returns the proper query for generating a list of table columns
     *
     * MySQL uses SHOW FULL COLUMNS FROM syntax.
     *
     * @param   string  $table  The name of the database table
     * @return  string
     */
    public function getColumnsQuery($table)
    {
        return $this->buildShowColumnsQuery($table, true);
    }

    /**
     * Normalizes the results of the column introspection query
     *
     * Parses MySQL's SHOW COLUMNS output format.
     *
     * @param   array  $data      The raw column data
     * @param   bool   $typeOnly  True (default) to only return field types
     * @return  array
     */
    public function normalizeColumns($data, $typeOnly = true)
    {
        return $this->normalizeShowColumnsMetadata(
            (array) $data,
            (bool) $typeOnly
        );
    }

    // =========================================================================
    // Expression Builder (MySQL-specific overrides)
    // =========================================================================

    /**
     * Build a function expression into MySQL-specific SQL
     *
     * @param   \Hubzero\Database\Expression  $expression  The function expression
     * @return  string  The SQL representation
     */
    protected function buildFunctionExpression($expression): string
    {
        $function = $expression->getFunction();
        $args = $expression->getArguments();

        switch ($function) {
            // MySQL-specific: IFNULL (use native function)
            case 'IFNULL':
                return 'IFNULL('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ')';

            // MySQL-specific: CHAR_LENGTH for character count
            case 'LENGTH':
                return 'CHAR_LENGTH(' . $this->buildExpressionArgument($args[0]) . ')';

            // MySQL-specific: SUBSTRING syntax
            case 'SUBSTRING':
                $col = $this->buildExpressionArgument($args[0]);
                $start = (int) $args[1];
                if ($args[2] !== null) {
                    return "SUBSTRING({$col}, {$start}, " . (int) $args[2] . ')';
                }
                return "SUBSTRING({$col}, {$start})";

            // MySQL-specific: Date functions
            case 'NOW':
                return 'NOW()';

            case 'CURRENT_TIMESTAMP':
                return 'CURRENT_TIMESTAMP';

            case 'DATE':
            case 'TIME':
            case 'YEAR':
            case 'MONTH':
            case 'DAY':
            case 'HOUR':
            case 'MINUTE':
            case 'SECOND':
                return $function . '(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'DATE_FORMAT':
                $this->bind($args[1]);
                return 'DATE_FORMAT(' . $this->buildExpressionArgument($args[0]) . ', ?)';

            default:
                // Fall back to parent implementation
                return parent::buildFunctionExpression($expression);
        }
    }
}
