<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Cubrid;

/**
 * Database CUBRID query syntax class
 *
 * CUBRID is an open-source relational database optimized for web applications.
 * This implementation follows the shared Sql grammar and applies CUBRID-specific
 * behavior where required.
 *
 * CUBRID-specific syntax differences from MySQL:
 * - No SHOW ENGINES command
 * - No SET FOREIGN_KEY_CHECKS command
 * - SHOW FIELDS not supported (use SHOW COLUMNS instead)
 * - Different transaction syntax (no START TRANSACTION)
 * - Simpler CREATE VIEW syntax (no ALGORITHM, DEFINER, SQL SECURITY)
 * - Uses INFORMATION_SCHEMA differently or not at all
 * - Stricter about reserved keywords (requires quoting column names like 'value')
 *
 * This class extends Sql and implements CUBRID-specific syntax behavior.
 */
class CubridSyntax extends \Hubzero\Database\Drivers\Base\BaseSqlSyntax
{
    /**
     * Determine whether normal clause emission should be suppressed while
     * building FULL OUTER JOIN emulation branches.
     *
     * @return  bool
     */
    protected function shouldSuppressClauseForFullJoinUnion()
    {
        return $this->isBuildingFullJoinUnionQuery();
    }

    /**
     * Flag indicating CUBRID ignore mode for INSERT queries
     *
     * @var bool
     */
    protected $cubridIgnoreMode = false;

    /**
     * Deferred EXISTS/NOT EXISTS predicates from JOIN conditions
     *
     * CUBRID doesn't support subqueries in JOIN ON clauses. This property
     * stores EXISTS/NOT EXISTS predicates that were removed from JOIN conditions
     * so they can be added to the WHERE clause instead.
     *
     * @var array
     */
    protected $deferredExistsPredicates = [];

    /**
     * Track processed EXISTS predicates to avoid duplicate derived table JOINs
     *
     * Stores hashes of subquery SQL that have already been processed.
     * buildJoinConditions() may be called multiple times for the same query
     * during SQL building, so we need to track which EXISTS predicates we've
     * already injected derived tables for.
     *
     * @var array
     */
    protected $processedExistsPredicates = [];

    /**
     * Check whether a JOIN condition operator is EXISTS/NOT EXISTS.
     *
     * @param   string  $operator
     * @return  bool
     */
    protected function isExistsOperator($operator)
    {
        return in_array($this->normalizeOperator($operator), ['exists', 'not exists'], true);
    }

    /**
     * Normalize an SQL operator string for internal comparisons.
     *
     * @param   string  $operator
     * @return  string
     */
    protected function normalizeOperator($operator)
    {
        return strtolower(trim((string) $operator));
    }

    /**
     * Build a stable hash for deduplicating EXISTS predicate processing.
     *
     * @param   string  $subquerySql
     * @param   string  $operator
     * @return  string
     */
    protected function getExistsPredicateHash($subquerySql, $operator)
    {
        return md5($subquerySql . '|' . $this->normalizeOperator($operator));
    }

    /**
     * Mark predicate as deferred to WHERE if not already processed.
     *
     * @param   string  $predicateHash
     * @param   array   $condition
     * @return  void
     */
    protected function deferExistsPredicateIfUnprocessed($predicateHash, array $condition)
    {
        if (!isset($this->processedExistsPredicates[$predicateHash])) {
            $this->deferredExistsPredicates[] = $condition;
            $this->processedExistsPredicates[$predicateHash] = true;
        }
    }

    /**
     * Extract subquery SQL from an EXISTS/NOT EXISTS condition payload.
     *
     * @param   array  $condition
     * @return  string|null
     */
    protected function extractConditionSubquery(array $condition)
    {
        return $condition['right'] ?? $condition['value'] ?? null;
    }

    /**
     * Process one EXISTS/NOT EXISTS condition from a JOIN clause.
     *
     * @param   array  $condition
     * @param   bool   $injectOnCorrelation  True to inject derived JOIN when correlated
     * @return  bool   True when condition was EXISTS/NOT EXISTS and handled
     */
    protected function processExistsJoinCondition(array $condition, $injectOnCorrelation = true)
    {
        $operatorLower = $this->normalizeOperator($condition['operator'] ?? '=');

        if (!$this->isExistsOperator($operatorLower)) {
            return false;
        }

        $subquerySql = $this->extractConditionSubquery($condition);
        if (!$subquerySql) {
            return true;
        }

        $predicateHash = $this->getExistsPredicateHash($subquerySql, $operatorLower);
        if (isset($this->processedExistsPredicates[$predicateHash])) {
            return true;
        }

        $correlation = $this->parseCorrelation($subquerySql);
        if ($correlation) {
            if ($injectOnCorrelation) {
                $this->injectDerivedTableJoin($correlation, $operatorLower === 'not exists');
            }
            $this->processedExistsPredicates[$predicateHash] = true;
            return true;
        }

        $this->deferExistsPredicateIfUnprocessed($predicateHash, $condition);
        return true;
    }

    /**
     * Override build() to force buildJoin() and buildWhere() when we have deferred EXISTS predicates
     *
     * The base implementation only calls build methods if the corresponding property is not empty.
     * But we need buildJoin() called even when `$this->join` might be empty, because we may need
     * to inject derived table JOINs for correlated EXISTS predicates. Similarly for buildWhere().
     *
     * @param   string  $type
     * @return  string|false
     */
    public function build($type)
    {
        if ($this->shouldForceDeferredExistsBuild($type)) {
            if ($type === 'join') {
                return $this->buildJoin();
            }
            return $this->buildWhere();
        }

        // For all other cases, use parent implementation
        return parent::build($type);
    }

    /**
     * Determine whether deferred EXISTS predicates require a forced build pass.
     *
     * @param   string  $type
     * @return  bool
     */
    protected function shouldForceDeferredExistsBuild($type)
    {
        if (empty($this->deferredExistsPredicates)) {
            return false;
        }

        return in_array((string) $type, ['join', 'where'], true);
    }

    /**
     * Sets a JSON path extraction where clause.
     *
     * @param   string  $column
     * @param   string  $path
     * @param   string  $operator
     * @param   mixed   $value
     * @param   string  $logical
     * @param   int     $depth
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
     * Sets a JSON contains where clause.
     *
     * @param   string       $column
     * @param   mixed        $value
     * @param   string|null  $path
     * @param   string       $logical
     * @param   int          $depth
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
     * Sets a JSON length where clause.
     *
     * @param   string       $column
     * @param   string       $operator
     * @param   int          $value
     * @param   string|null  $path
     * @param   string       $logical
     * @param   int          $depth
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

    /**
     * Sets a date/time extraction where clause.
     *
     * @param   string  $column
     * @param   string  $operator
     * @param   mixed   $value
     * @param   string  $part
     * @param   string  $logical
     * @param   int     $depth
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
     * Get date extraction function for the requested part.
     *
     * @param   string  $part
     * @return  string
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

    /**
     * Builds an update statement from set params.
     *
     * @return  string
     */
    public function buildUpdate()
    {
        return $this->buildUpdateWithJoinClause();
    }

    /**
     * Build CONCAT expression.
     *
     * @param   array  $parts
     * @return  string
     */
    public function buildConcat(array $parts)
    {
        return $this->buildConcatFunction($parts);
    }

    /**
     * Builds a select statement from the set params
     *
     * CUBRID is stricter than MySQL about reserved keywords and requires
     * proper quoting of all column names. Override to add column name wrapping.
     *
     * @return  string
     */
    protected function buildSelect()
    {
        if (!$this->hasFullJoin()) {
            $this->fullJoinUnionBuilt = false;
        } else {
            $this->fullJoinUnionBuilt = true;

            $branches = $this->expandFullJoinBranches();
            if (empty($branches)) {
                $this->fullJoinUnionBuilt = false;
            } else {
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
        }

        $this->validateFullJoinLayout();

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
                // Wrap column name for COUNT DISTINCT
                $wrappedCol = $this->wrapColumn($select['column']);
                $string = "COUNT(DISTINCT({$wrappedCol}))";
            } elseif (!empty($select['count'])) {
                // Wrap column name for COUNT
                $wrappedCol = $this->wrapColumn($select['column']);
                $string = "COUNT({$wrappedCol})";
            } else {
                // Wrap regular column names (handles * specially)
                $string = $this->wrapColumn($select['column']);
            }

            // See if we're including an alias
            if (isset($select['as']) && $select['as'] !== null) {
                $string .= " AS " . $this->connection->wrap($select['as']);
            }

            $selects[] = $string;
        }

        $distinct = $this->distinct ? 'DISTINCT ' : '';
        return 'SELECT ' . $distinct . implode(',', $selects);
    }

    /**
     * Wrap a column name for use in SQL, handling special cases
     *
     * Handles comma-separated column lists, table.column syntax, wildcards,
     * and functions/expressions.
     *
     * @param   string  $column  The column name (may contain commas)
     * @return  string  The wrapped column name(s)
     */
    protected function wrapColumn($column)
    {
        // Don't wrap * wildcard
        if ($column === '*') {
            return '*';
        }

        // Don't wrap if already wrapped (starts with backtick)
        if (strpos($column, '`') === 0) {
            return $column;
        }

        // Don't wrap expressions with parentheses (functions, etc.)
        if (strpos($column, '(') !== false) {
            return $column;
        }

        // Handle comma-separated column lists (e.g., "table.col1, table2.col2")
        // Split on commas that are NOT inside parentheses
        // Must check AFTER the parenthesis check above, so we don't split function arguments
        if (strpos($column, ',') !== false) {
            $parts = $this->splitOnCommasOutsideParens($column);
            // Only split if we got multiple parts (meaning commas were at top level)
            if (count($parts) > 1) {
                $wrapped = [];
                foreach ($parts as $part) {
                    $wrapped[] = $this->wrapColumn(trim($part));
                }
                return implode(',', $wrapped);
            }
            // If we only got one part, the commas were all inside parens, fall through
        }

        // Handle table.column syntax - wrap each part separately
        if (strpos($column, '.') !== false) {
            $parts = explode('.', $column);
            $wrapped = [];
            foreach ($parts as $part) {
                // Skip wrapping * in table.*
                if ($part === '*') {
                    $wrapped[] = '*';
                } else {
                    $wrapped[] = $this->connection->wrap(trim($part));
                }
            }
            return implode('.', $wrapped);
        }

        // Use driver's wrap method for proper quoting
        return $this->connection->wrap($column);
    }

    /**
     * Split a string on commas that are outside of parentheses
     *
     * Used to handle comma-separated column lists while preserving function
     * arguments like "col1, CONCAT(a, ',', b), col2"
     *
     * @param   string  $str  The string to split
     * @return  array   Array of parts
     */
    protected function splitOnCommasOutsideParens($str)
    {
        $parts = [];
        $current = '';
        $depth = 0;
        $inString = false;
        $stringChar = null;

        for ($i = 0; $i < strlen($str); $i++) {
            $char = $str[$i];

            // Track string literals (single or double quotes)
            if (($char === "'" || $char === '"') && ($i === 0 || $str[$i - 1] !== '\\')) {
                if (!$inString) {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($char === $stringChar) {
                    $inString = false;
                    $stringChar = null;
                }
                $current .= $char;
                continue;
            }

            // If inside a string, don't process parentheses or commas
            if ($inString) {
                $current .= $char;
                continue;
            }

            // Track parenthesis depth
            if ($char === '(') {
                $depth++;
                $current .= $char;
            } elseif ($char === ')') {
                $depth--;
                $current .= $char;
            } elseif ($char === ',' && $depth === 0) {
                // Comma at top level - split here
                $parts[] = $current;
                $current = '';
            } else {
                $current .= $char;
            }
        }

        // Add the last part
        if ($current !== '') {
            $parts[] = $current;
        }

        return $parts;
    }

    /**
     * Build JOIN conditions
     *
     * CUBRID doesn't support subqueries in JOIN ON clauses. This override
     * detects EXISTS/NOT EXISTS predicates and handles them specially:
     * - Correlated: injects a derived table JOIN immediately
     * - Non-correlated: defers to WHERE clause
     *
     * buildJoin() pre-processes JOIN predicates before parent JOIN compilation,
     * and this method ensures EXISTS predicates are not emitted into ON clause SQL.
     *
     * @param   array  $conditions
     * @return  string
     */
    protected function buildJoinConditions(array $conditions): string
    {
        if (empty($conditions)) {
            return '';
        }

        // Filter out EXISTS/NOT EXISTS predicates - CUBRID can't handle them in JOIN ON
        // Note: These are now pre-processed in buildJoin(), so this just ensures
        // they don't end up in the ON clause SQL
        $allowedConditions = [];
        foreach ($conditions as $condition) {
            if ($this->processExistsJoinCondition($condition, false)) {
                // EXISTS predicates are handled in buildJoin() preprocessing
                // and should not be emitted inside ON clause SQL.
                continue;
            }

            $allowedConditions[] = $condition;
        }

        // If all conditions were EXISTS/NOT EXISTS, we need at least 1 = 1 to avoid syntax error
        if (empty($allowedConditions)) {
            return '1 = 1';
        }

        // Build the remaining conditions using parent implementation
        return parent::buildJoinConditions($allowedConditions);
    }

    /**
     * Builds JOIN clauses
     *
     * Correlated EXISTS rewriting is pre-processed here before parent JOIN compilation
     * so injected derived-table joins are included in the same build pass.
     *
     * @return  string
     */
    public function buildJoin()
    {
        if ($this->shouldSuppressClauseForFullJoinUnion()) {
            return '';
        }

        // Pre-process JOINs to inject derived tables for correlated EXISTS predicates
        // BEFORE parent::buildJoin() starts iterating over $this->join
        //
        // This prevents the "iterator modification" bug where injecting a JOIN
        // from within buildJoinConditions() doesn't work because the parent's
        // foreach loop has already started iterating and won't see newly added elements.

        $this->preprocessJoinExistsPredicates();

        // Now call parent to build all JOINs (including injected derived tables)
        return parent::buildJoin();
    }

    /**
     * Pre-process JOIN condition arrays to extract EXISTS predicates.
     *
     * @return  void
     */
    protected function preprocessJoinExistsPredicates()
    {
        // Make a copy so parent iteration can include newly injected JOINs.
        $originalJoins = $this->join;

        foreach ($originalJoins as $join) {
            if (!isset($join['conditions']) || !is_array($join['conditions'])) {
                continue;
            }

            foreach ($join['conditions'] as $condition) {
                $this->processExistsJoinCondition($condition, true);
            }
        }
    }

    /**
     * Reset JOIN state and clear processed EXISTS predicates tracking
     *
     * Override to also clear CUBRID-specific tracking arrays when JOINs are reset.
     * This prevents stale predicates from affecting subsequent query builds.
     *
     * @return  void
     */
    public function resetJoin()
    {
        parent::resetJoin();
        $this->deferredExistsPredicates = [];
        $this->processedExistsPredicates = [];
    }

    /**
     * Builds FROM clause.
     *
     * @return  string
     */
    protected function buildFrom()
    {
        if ($this->shouldSuppressClauseForFullJoinUnion()) {
            return '';
        }

        return parent::buildFrom();
    }

    /**
     * Builds GROUP BY clause.
     *
     * @return  string
     */
    public function buildGroup()
    {
        if ($this->shouldSuppressClauseForFullJoinUnion()) {
            return '';
        }

        return parent::buildGroup();
    }

    /**
     * Builds HAVING clause.
     *
     * @return  string
     */
    public function buildHaving()
    {
        if ($this->shouldSuppressClauseForFullJoinUnion()) {
            return '';
        }

        return parent::buildHaving();
    }

    /**
     * Builds ORDER BY clause.
     *
     * @return  string
     */
    public function buildOrder()
    {
        if ($this->shouldSuppressClauseForFullJoinUnion()) {
            return '';
        }

        return parent::buildOrder();
    }

    /**
     * Builds LIMIT clause using MySQL/CUBRID syntax.
     *
     * @return  string
     */
    public function buildLimit()
    {
        if ($this->shouldSuppressClauseForFullJoinUnion()) {
            return '';
        }

        return $this->buildMySqlStyleLimitClause();
    }

    /**
     * Builds LIMIT clause for UNION queries (FULL JOIN emulation).
     *
     * @return  string
     */
    protected function buildUnionLimit(): string
    {
        return $this->buildMySqlStyleLimitClause();
    }

    /**
     * Returns the proper query for generating a list of table columns.
     *
     * CUBRID supports SHOW COLUMNS (not SHOW FULL COLUMNS).
     *
     * @param   string  $table
     * @return  string
     */
    public function getColumnsQuery($table)
    {
        return $this->buildShowColumnsQuery($table, false);
    }

    /**
     * Normalizes column metadata output.
     *
     * @param   array  $data
     * @param   bool   $typeOnly
     * @return  array
     */
    public function normalizeColumns($data, $typeOnly = true)
    {
        return $this->normalizeShowColumnsMetadata(
            (array) $data,
            (bool) $typeOnly
        );
    }

    /**
     * Parse a subquery to detect correlation with outer query tables
     *
     * Analyzes the subquery's WHERE clause to find references to outer tables.
     * A correlated subquery contains conditions like "outer_table.column = inner_table.column"
     * where outer_table is from the main query's FROM or JOIN clauses.
     *
     * Example:
     *   SELECT 1 FROM comments WHERE comments.post_id = posts.id
     *   Returns: [
     *     'outer_table' => 'posts',
     *     'outer_column' => 'id',
     *     'inner_table' => 'comments',
     *     'inner_column' => 'post_id',
     *     'subquery' => <original SQL>
     *   ]
     *
     * Handles backtick-quoted identifiers from toSql() output.
     *
     * @param   string  $subquerySql  The subquery SQL string
     * @return  array|null  Correlation info or null if not correlated
     */
    protected function parseCorrelation($subquerySql)
    {
        // Extract WHERE clause from subquery
        if (!preg_match('/WHERE\s+(.+?)(?:GROUP BY|ORDER BY|LIMIT|$)/is', $subquerySql, $matches)) {
            return null;
        }

        $whereClause = $matches[1];

        // Look for correlation pattern: table.column = table.column
        // Allow for optional backticks around identifiers (from toSql() output)
        if (!preg_match('/`?(\w+)`?\.`?(\w+)`?\s*=\s*`?(\w+)`?\.`?(\w+)`?/i', $whereClause, $matches)) {
            return null;
        }

        $leftTable = $matches[1];
        $leftColumn = $matches[2];
        $rightTable = $matches[3];
        $rightColumn = $matches[4];

        // Determine which table is from outer query (in our JOINs) vs inner query
        $outerTable = null;
        $outerColumn = null;
        $innerTable = null;
        $innerColumn = null;

        // Check each joined table to see if it matches either side of the condition
        foreach ($this->join as $join) {
            // Skip subquery joins - they don't have a 'table' key
            if (isset($join['subquery'])) {
                continue;
            }

            $joinTable = is_array($join['table']) ? $join['table']['name'] : $join['table'];
            $joinAlias = $join['alias'] ?? '';

            // Check if left side references this joined table
            if (stripos($joinTable, $leftTable) !== false || $joinAlias === $leftTable) {
                $outerTable = $leftTable;
                $outerColumn = $leftColumn;
                $innerTable = $rightTable;
                $innerColumn = $rightColumn;
                break;
            }

            // Check if right side references this joined table
            if (stripos($joinTable, $rightTable) !== false || $joinAlias === $rightTable) {
                $outerTable = $rightTable;
                $outerColumn = $rightColumn;
                $innerTable = $leftTable;
                $innerColumn = $leftColumn;
                break;
            }
        }

        // Also check the FROM table
        if (!$outerTable && !empty($this->from)) {
            // $this->from is an array like [[table => 'users', 'as' => '']]
            $fromEntry = is_array($this->from) && isset($this->from[0]) ? $this->from[0] : [];
            $fromTable = $fromEntry['table'] ?? '';
            $fromAlias = $fromEntry['as'] ?? '';

            if (stripos($fromTable, $leftTable) !== false || $fromAlias === $leftTable) {
                $outerTable = $leftTable;
                $outerColumn = $leftColumn;
                $innerTable = $rightTable;
                $innerColumn = $rightColumn;
            } elseif (stripos($fromTable, $rightTable) !== false || $fromAlias === $rightTable) {
                $outerTable = $rightTable;
                $outerColumn = $rightColumn;
                $innerTable = $leftTable;
                $innerColumn = $leftColumn;
            }
        }

        // If we couldn't identify an outer table, it's not correlated
        if (!$outerTable) {
            return null;
        }

        // Extract the FROM clause to verify inner table (handle backticks)
        if (!preg_match('/FROM\s+`?(\w+)`?/i', $subquerySql, $fromMatches)) {
            return null;
        }

        $fromTable = $fromMatches[1];

        return [
            'outer_table' => $outerTable,
            'outer_column' => $outerColumn,
            'inner_table' => $fromTable,
            'inner_column' => $innerColumn,
            'subquery' => $subquerySql,
            'where_clause' => $whereClause,
        ];
    }

    /**
     * Inject a derived table JOIN to replace a correlated EXISTS predicate
     *
     * Rewrites: EXISTS (SELECT 1 FROM inner WHERE inner.col = outer.col AND ...)
     * As: INNER JOIN (SELECT DISTINCT col FROM inner WHERE ...) AS alias
     *         ON outer.col = alias.col
     *
     * For NOT EXISTS, uses LEFT JOIN with a WHERE IS NULL check:
     *   LEFT JOIN (...) AS alias ON outer.col = alias.col
     *   WHERE alias.col IS NULL (added to $this->where by buildWhere)
     *
     * The derived table SELECT DISTINCT ensures we don't multiply rows in the
     * main query result.
     *
     * @param   array  $correlation  Correlation info from parseCorrelation()
     * @param   bool   $notExists    True for NOT EXISTS (use LEFT JOIN + IS NULL)
     * @return  void
     */
    protected function injectDerivedTableJoin($correlation, $notExists = false)
    {
        // Build derived table: SELECT DISTINCT inner_column FROM inner_table WHERE ...
        $derivedTableSql = sprintf(
            'SELECT DISTINCT %s FROM %s',
            $this->connection->wrap($correlation['inner_column']),
            $this->connection->wrap($correlation['inner_table'])
        );

        // Extract non-correlation WHERE conditions to include in derived table
        $whereClause = $correlation['where_clause'];

        // Remove the correlation condition from WHERE clause
        // Try both directions: "inner.col = outer.col" and "outer.col = inner.col"
        // Handle optional backticks around identifiers
        $correlationPattern1 = sprintf(
            '/`?%s`?\.`?%s`?\s*=\s*`?%s`?\.`?%s`?/i',
            preg_quote($correlation['inner_table'], '/'),
            preg_quote($correlation['inner_column'], '/'),
            preg_quote($correlation['outer_table'], '/'),
            preg_quote($correlation['outer_column'], '/')
        );
        $whereClause = preg_replace($correlationPattern1, '', $whereClause);

        $correlationPattern2 = sprintf(
            '/`?%s`?\.`?%s`?\s*=\s*`?%s`?\.`?%s`?/i',
            preg_quote($correlation['outer_table'], '/'),
            preg_quote($correlation['outer_column'], '/'),
            preg_quote($correlation['inner_table'], '/'),
            preg_quote($correlation['inner_column'], '/')
        );
        $whereClause = preg_replace($correlationPattern2, '', $whereClause);

        // Clean up AND/OR connectors left at start/end after removing correlation
        $whereClause = trim($whereClause);
        $whereClause = preg_replace('/^\s*(AND|OR)\s*/i', '', $whereClause);
        $whereClause = preg_replace('/\s*(AND|OR)\s*$/i', '', $whereClause);

        // If there are remaining conditions, add them to derived table
        if (!empty($whereClause)) {
            $derivedTableSql .= ' WHERE ' . $whereClause;
        }

        // Generate unique alias for the derived table
        static $counter = 0;
        $alias = '_exists_subq_' . (++$counter);

        // Inject derived table JOIN into $this->join array
        // For EXISTS: use INNER JOIN (only include rows where match exists)
        // For NOT EXISTS: use LEFT JOIN (then filter NULL in WHERE clause)
        $this->join[] = [
            'type' => $notExists ? 'left' : 'inner',
            'subquery' => $derivedTableSql,
            'as' => $alias,
            'left' => $correlation['outer_table'] . '.' . $correlation['outer_column'],
            'right' => $alias . '.' . $correlation['inner_column'],
            'bindings' => [], // No additional bindings needed for this derived table
        ];

        // For NOT EXISTS, add WHERE condition to filter NULL (no match found)
        // This WHERE condition is added by storing it in a way buildWhere() will process
        if ($notExists) {
            // Store the IS NULL condition for buildWhere to append
            $this->where[] = [
                'type' => 'where',
                'column' => $alias . '.' . $correlation['inner_column'],
                'operator' => 'IS',
                'value' => null,
            ];
        }
    }

    /**
     * Builds a WHERE clause from set params
     *
     * Override to append deferred non-correlated EXISTS/NOT EXISTS predicates
     * that were removed from JOIN conditions but couldn't be rewritten as
     * derived tables.
     *
     * Correlated EXISTS are handled in buildJoin() via derived table rewrite.
     * Only non-correlated EXISTS remain here and are appended to WHERE.
     *
     * @return  string
     */
    protected function buildWhere()
    {
        if ($this->shouldSuppressClauseForFullJoinUnion()) {
            return '';
        }

        // Build normal WHERE clause (includes IS NULL conditions from NOT EXISTS rewrites).
        // For UPDATE+JOIN, use raw WHERE clause construction instead of the generic
        // UPDATE ... FROM ... rewrite, which is not valid CUBRID syntax.
        $where = $this->shouldUseRawWhereForUpdateJoin()
            ? $this->buildWhereClause()
            : parent::buildWhere();

        $where = $this->appendDeferredExistsPredicatesToWhere($where);

        return $where;
    }

    /**
     * Determine whether WHERE should bypass generic UPDATE+JOIN rewrite.
     *
     * @return  bool
     */
    protected function shouldUseRawWhereForUpdateJoin(): bool
    {
        return !empty($this->update) && !empty($this->join);
    }

    /**
     * Append deferred EXISTS/NOT EXISTS predicates to a WHERE clause.
     *
     * @param   string  $where
     * @return  string
     */
    protected function appendDeferredExistsPredicatesToWhere($where)
    {
        if (empty($this->deferredExistsPredicates)) {
            return $where;
        }

        $existsParts = $this->buildDeferredExistsClauses();
        if (!empty($existsParts)) {
            $where = $this->appendExistsClauseToWhere($where, implode(' AND ', $existsParts));
        }

        // Clear deferred predicates after processing
        $this->deferredExistsPredicates = [];

        return $where;
    }

    /**
     * Build SQL snippets for deferred EXISTS/NOT EXISTS predicates.
     *
     * @return  array
     */
    protected function buildDeferredExistsClauses(): array
    {
        $existsParts = [];
        foreach ($this->deferredExistsPredicates as $condition) {
            $operator = $condition['operator'] ?? 'exists';
            $operatorLower = strtolower(trim((string) $operator));
            $subquery = $this->extractConditionSubquery($condition);
            $existsParts[] = $this->buildJoinExistsClause($operatorLower, $subquery);
        }

        return $existsParts;
    }

    /**
     * Append an EXISTS clause string to an existing WHERE clause.
     *
     * @param   string  $where
     * @param   string  $existsClause
     * @return  string
     */
    protected function appendExistsClauseToWhere($where, $existsClause)
    {
        if (empty($where)) {
            return 'WHERE ' . $existsClause;
        }

        return $where . ' AND ' . $existsClause;
    }

    /**
     * Builds a single-row upsert statement
     *
     * CUBRID doesn't support MySQL's VALUES() function in ON DUPLICATE KEY UPDATE.
     * Instead, use explicit parameter bindings for the update values.
     *
     * MySQL syntax: INSERT INTO t (a, b) VALUES (?, ?) ON DUPLICATE KEY UPDATE a = VALUES(a)
     * CUBRID syntax: INSERT INTO t (a, b) VALUES (?, ?) ON DUPLICATE KEY UPDATE a = ?
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

        // Build UPDATE clause with explicit value parameters
        $updates = [];
        foreach ($this->upsertUpdateColumns as $col) {
            $quotedCol = $this->connection->quoteName($col);
            // Bind the value again for the UPDATE clause
            $this->bind(
                is_string($this->upsertValues[$col])
                    ? trim($this->upsertValues[$col])
                    : $this->upsertValues[$col]
            );
            $updates[] = "$quotedCol = ?";
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
     * CUBRID doesn't support VALUES() function in ON DUPLICATE KEY UPDATE.
     * For multi-row upserts, return empty string to trigger fallback to
     * individual upserts. This ensures each row gets the correct update
     * values on conflict.
     *
     * The Query class will handle the fallback by calling pushOrUpdate()
     * for each row individually.
     *
     * @return  string  Empty string to trigger individual upsert fallback
     */
    public function buildUpsertMany()
    {
        // Return empty to trigger fallback to individual upserts
        // (see Query::upsertMany() lines 4222-4232)
        return '';
    }

    /**
     * Builds a bulk insert statement from the set params
     *
     * CUBRID doesn't support INSERT IGNORE for multi-row inserts.
     * When ignore flag is set, return empty string to trigger fallback to
     * individual inserts. For regular multi-row inserts (no ignore), use
     * the shared SQL implementation.
     *
     * @return  string
     */
    public function buildInsertMany()
    {
        // CUBRID doesn't support INSERT IGNORE for multi-row inserts
        // Return empty to trigger fallback to individual inserts
        if ($this->insertManyIgnore) {
            return '';
        }

        // For non-ignore multi-row inserts, use the shared SQL implementation.
        return parent::buildInsertMany();
    }

    /**
     * Builds an INSERT statement
     *
     * CUBRID doesn't support INSERT IGNORE syntax. When ignore is true,
     * convert to INSERT ... ON DUPLICATE KEY UPDATE with a no-op update
     * that preserves the existing row (effectively ignoring the duplicate).
     *
     * We use a dummy update like "id=id" to make the statement succeed
     * without modifying the existing row.
     *
     * @return  string
     */
    public function buildInsert()
    {
        $sql = 'INSERT INTO ' . $this->connection->quoteName($this->insert);

        // Track ignore mode per statement build so state never leaks between
        // successive INSERT statements on the same syntax instance.
        // CUBRID doesn't support INSERT IGNORE, so ignore mode is translated
        // later into ON DUPLICATE KEY UPDATE no-op behavior.
        $this->cubridIgnoreMode = !empty($this->ignore);

        return $sql;
    }

    /**
     * Builds the VALUES clause for an INSERT statement
     *
     * Override to append ON DUPLICATE KEY UPDATE when in ignore mode.
     * Handles both INSERT VALUES and INSERT ... SELECT patterns.
     *
     * @return  string
     */
    public function buildValues()
    {
        $ignoreMode = $this->isIgnoreInsertMode();

        try {
            // Check if this is an INSERT ... SELECT
            if ($this->insertSelectQuery !== null) {
                // Add bindings from the SELECT query
                foreach ($this->insertSelectBindings as $binding) {
                    $this->bind($binding);
                }

                // Build column list if specified
                $result = '';
                if (!empty($this->insertColumns)) {
                    $quotedColumns = [];
                    foreach ($this->insertColumns as $column) {
                        $quotedColumns[] = $this->connection->quoteName($column);
                    }
                    $result = '(' . implode(',', $quotedColumns) . ') ' . $this->insertSelectQuery;
                } else {
                    // No columns specified - SELECT determines columns
                    $result = $this->insertSelectQuery;
                }

                if ($ignoreMode) {
                    $result .= $this->buildNoopDuplicateUpdateClause(
                        !empty($this->insertColumns) ? $this->insertColumns[0] : 'id'
                    );
                }

                return $result;
            }

            // Standard INSERT VALUES - use parent implementation
            $values = parent::buildValues();

            if ($ignoreMode) {
                $firstCol = !empty($this->values) ? array_key_first($this->values) : null;
                if ($firstCol) {
                    $values .= $this->buildNoopDuplicateUpdateClause($firstCol);
                }
            }

            return $values;
        } finally {
            // Ensure per-statement ignore mode never leaks between builds.
            $this->cubridIgnoreMode = false;
        }
    }

    /**
     * Build a no-op ON DUPLICATE KEY UPDATE clause for CUBRID ignore emulation.
     *
     * @param   string  $column
     * @return  string
     */
    protected function buildNoopDuplicateUpdateClause($column)
    {
        $quotedCol = $this->connection->quoteName($column);
        return " ON DUPLICATE KEY UPDATE {$quotedCol} = {$quotedCol}";
    }

    /**
     * Determine if the current INSERT build should emulate IGNORE behavior.
     *
     * @return  bool
     */
    protected function isIgnoreInsertMode()
    {
        return !empty($this->cubridIgnoreMode) || !empty($this->ignore);
    }
}
