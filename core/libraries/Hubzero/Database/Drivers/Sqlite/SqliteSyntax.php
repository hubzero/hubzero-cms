<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Sqlite;

use Hubzero\Database\Exception\UnsupportedSyntaxException;

/**
 * Database SQLite query syntax class
 *
 * SQLite is a self-contained, serverless SQL database engine.
 * This class extends the base SQL syntax with SQLite-specific features.
 *
 * SQLite-specific features supported:
 * - INSERT OR IGNORE: SQLite's conflict handling syntax
 * - LIMIT x OFFSET y: SQLite pagination syntax
 * - ON CONFLICT DO UPDATE: SQLite 3.24.0+ upsert syntax
 * - json_extract(), json_each(): JSON1 extension (SQLite 3.9.0+)
 * - strftime(): Date/time formatting
 * - PRAGMA table_info: Column introspection
 * - Type affinity system (INTEGER, TEXT, REAL, BLOB, NUMERIC)
 *
 * SQLite limitations:
 * - No RIGHT JOIN or FULL OUTER JOIN
 * - No TRUNCATE TABLE (uses DELETE)
 * - No native boolean type (uses INTEGER 0/1)
 */
class SqliteSyntax extends \Hubzero\Database\Drivers\Base\BaseSqlSyntax
{
    /**
     * SQLite type affinity mappings
     *
     * SQLite uses type affinity rather than strict types.
     * This maps common type names to their affinity.
     *
     * @var array
     */
    protected static $typeAffinities = [
        // INTEGER affinity
        'int'       => 'INTEGER',
        'integer'   => 'INTEGER',
        'tinyint'   => 'INTEGER',
        'smallint'  => 'INTEGER',
        'mediumint' => 'INTEGER',
        'bigint'    => 'INTEGER',
        'int2'      => 'INTEGER',
        'int8'      => 'INTEGER',
        'boolean'   => 'INTEGER',
        'bool'      => 'INTEGER',

        // TEXT affinity
        'character' => 'TEXT',
        'varchar'   => 'TEXT',
        'char'      => 'TEXT',
        'nchar'     => 'TEXT',
        'nvarchar'  => 'TEXT',
        'text'      => 'TEXT',
        'clob'      => 'TEXT',
        'longtext'  => 'TEXT',
        'mediumtext' => 'TEXT',
        'tinytext'  => 'TEXT',
        'enum'      => 'TEXT',
        'set'       => 'TEXT',
        'json'      => 'TEXT',

        // REAL affinity
        'real'      => 'REAL',
        'double'    => 'REAL',
        'float'     => 'REAL',
        'decimal'   => 'REAL',
        'numeric'   => 'REAL',

        // BLOB affinity
        'blob'      => 'BLOB',
        'binary'    => 'BLOB',
        'varbinary' => 'BLOB',
        'longblob'  => 'BLOB',
        'mediumblob' => 'BLOB',
        'tinyblob'  => 'BLOB',

        // NUMERIC affinity (special case)
        'date'      => 'TEXT',
        'datetime'  => 'TEXT',
        'timestamp' => 'TEXT',
        'time'      => 'TEXT',
        'year'      => 'INTEGER',
    ];

    /**
     * Builds an insert statement from the set params
     *
     * SQLite uses "INSERT OR IGNORE" instead of MySQL's "INSERT IGNORE"
     *
     * @return  string
     */
    public function buildInsert()
    {
        return 'INSERT ' .
            ($this->ignore ? 'OR IGNORE ' : '') .
            'INTO ' .
            $this->connection->quoteName($this->insert);
    }

    /**
     * Builds a select statement from the set params
     *
     * SQLite does not support FULL OUTER JOIN natively. We emulate a single
     * FULL join using a UNION ALL of two LEFT JOIN branches, swapping the
     * FROM/JOIN order for the right-only branch. To guarantee correctness
     * across all drivers, FULL JOINs are restricted to a single FULL JOIN
     * between the base FROM table and one joined table. Additional JOINs
     * are allowed only if they are INNER or LEFT joins that reference tables
     * introduced earlier in the join order.
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

        $originalFrom = $this->from;
        $originalJoins = $this->join;
        $sqlParts = [];
        $allBindings = [];

        foreach ($branches as $branch) {
            $this->from = $branch['from'];
            $this->join = $this->reorderJoinsForSqlite($branch['joins'], $branch['from']);
            $this->clearBindings();

            $leftWhere = !empty($this->where) ? $this->buildWhereClause() : '';
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

        $this->from = $originalFrom;
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
     * Builds a limit statement from the set params
     *
     * SQLite uses "LIMIT x OFFSET y" syntax instead of MySQL's "LIMIT y,x"
     *
     * @return  string
     */
    public function buildLimit()
    {
        if ($this->fullJoinUnionBuilt) {
            return '';
        }

        $string = 'LIMIT ';

        // Add the limit value (or -1 for unlimited, which SQLite supports)
        $string .= !empty($this->limit) ? (int) $this->limit : -1;

        // Add offset if specified
        if (!empty($this->start)) {
            $string .= ' OFFSET ' . (int) $this->start;
        }

        return $string;
    }

    /**
     * Builds a LIMIT statement for UNION queries
     *
     * SQLite uses "LIMIT x OFFSET y" syntax for the unioned result set.
     *
     * @return  string
     */
    protected function buildUnionLimit(): string
    {
        $string = 'LIMIT ';

        if (!empty($this->limit)) {
            $string .= (int) $this->limit;
        } else {
            $string .= '-1';
        }

        if (!empty($this->start)) {
            $string .= ' OFFSET ' . (int) $this->start;
        }

        return trim($string);
    }

    /**
     * SQLite emulates RIGHT JOIN by swapping the FROM table and JOIN target.
     *
     * @param   array   $branch
     * @param   int     $joinIndex
     * @param   string  $direction
     * @return  array
     */
    protected function transformFullJoinBranch(array $branch, int $joinIndex, string $direction): array
    {
        if ($direction !== 'right') {
            return $branch;
        }

        if (count($branch['from']) !== 1 || isset($branch['from'][0]['raw'])) {
            throw new UnsupportedSyntaxException(
                'SQLite FULL OUTER JOIN emulation requires a single table in FROM.'
            );
        }

        $fromTable = $branch['from'][0]['table'] ?? null;
        $fromAlias = $branch['from'][0]['as'] ?? null;
        if (!$fromTable) {
            throw new UnsupportedSyntaxException(
                'SQLite FULL OUTER JOIN emulation requires a simple FROM table.'
            );
        }

        $join = $branch['joins'][$joinIndex];
        [$joinTable, $joinAlias] = $this->parseTableAlias($join['table']);
        if (!$joinTable) {
            throw new UnsupportedSyntaxException(
                'SQLite FULL OUTER JOIN emulation requires a simple JOIN table.'
            );
        }

        $branch['from'][0]['table'] = $joinTable;
        if ($joinAlias) {
            $branch['from'][0]['as'] = $joinAlias;
        } else {
            unset($branch['from'][0]['as']);
        }

        $leftJoinTable = $fromTable;
        if ($fromAlias) {
            $leftJoinTable .= ' ' . $fromAlias;
        }

        $branch['joins'][$joinIndex]['table'] = $leftJoinTable;
        $branch['joins'][$joinIndex]['type'] = 'left';
        $branch['joins'][$joinIndex]['left'] = $join['right'];
        $branch['joins'][$joinIndex]['right'] = $join['left'];

        // Move the swapped join to the front so referenced tables appear earlier
        $moved = $branch['joins'][$joinIndex];
        array_splice($branch['joins'], $joinIndex, 1);
        array_unshift($branch['joins'], $moved);

        return $branch;
    }

    /**
     * Reorder joins so ON clauses only reference tables already introduced.
     *
     * This is needed for SQLite when emulating RIGHT/FULL joins via swaps.
     *
     * @param   array  $joins
     * @param   array  $from
     * @return  array
     */
    protected function reorderJoinsForSqlite(array $joins, array $from): array
    {
        if (empty($joins) || empty($from)) {
            return $joins;
        }

        $available = [];
        $fromTable = $from[0]['table'] ?? null;
        $fromAlias = $from[0]['as'] ?? null;
        if ($fromAlias) {
            $available[$fromAlias] = true;
        }
        if ($fromTable) {
            $available[$fromTable] = true;
        }

        $ordered = [];
        $remaining = $joins;

        $iterations = 0;
        while (!empty($remaining) && $iterations < 50) {
            $progress = false;
            foreach ($remaining as $idx => $join) {
                $joinRef = $this->extractJoinTableRef($join['table']);
                $leftRef = $this->extractColumnTableRef($join['left'] ?? '');
                $rightRef = $this->extractColumnTableRef($join['right'] ?? '');

                $refs = array_filter([$leftRef, $rightRef]);
                $refs = array_unique($refs);

                $allKnown = true;
                foreach ($refs as $ref) {
                    if ($ref === $joinRef) {
                        continue;
                    }
                    if (!isset($available[$ref])) {
                        $allKnown = false;
                        break;
                    }
                }

                if ($allKnown) {
                    $ordered[] = $join;
                    unset($remaining[$idx]);
                    if ($joinRef) {
                        $available[$joinRef] = true;
                    }
                    $progress = true;
                    break;
                }
            }

            if (!$progress) {
                if ($this->fullJoinUnionBuilt) {
                    throw new UnsupportedSyntaxException(
                        'Unable to reorder FULL JOIN emulation for SQLite. ' .
                        'A JOIN references tables to its right. ' .
                        'Rewrite the query with explicit UNIONs or reorder joins.'
                    );
                }

                // Fallback: preserve remaining order
                foreach ($remaining as $join) {
                    $ordered[] = $join;
                }
                break;
            }

            $iterations++;
        }

        return $ordered;
    }


    /**
     * Extract table alias/name from a join table string.
     *
     * @param   string  $table
     * @return  string|null
     */
    protected function extractJoinTableRef(string $table): ?string
    {
        if (preg_match('/\s+AS\s+/i', $table)) {
            [$tbl, $alias] = preg_split('/\s+AS\s+/i', $table, 2);
            return trim($alias);
        }

        if (preg_match('/\s+/', $table)) {
            [$tbl, $alias] = preg_split('/\s+/', $table, 2);
            return trim($alias);
        }

        return trim($table) ?: null;
    }

    /**
     * Extract table/alias from a column reference like "t1.id".
     *
     * @param   string  $expr
     * @return  string|null
     */
    protected function extractColumnTableRef(string $expr): ?string
    {
        $expr = trim($expr);
        if (strpos($expr, '.') === false) {
            return null;
        }

        return substr($expr, 0, strpos($expr, '.'));
    }

    /**
     * Builds a join statement from the set params
     *
     * @return  string
     */
    public function buildJoin()
    {
        if ($this->fullJoinUnionBuilt) {
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
        if ($this->fullJoinUnionBuilt) {
            return '';
        }

        return parent::buildFrom();
    }

    /**
     * Builds a where statement from the set params
     *
     * For UPDATE queries with JOINs, rewrites to use a subquery:
     * WHERE rowid IN (SELECT table.rowid FROM table JOIN ... WHERE ...)
     *
     * @return  string
     */
    protected function buildWhere()
    {
        if ($this->fullJoinUnionBuilt) {
            return '';
        }

        // If this is an UPDATE with JOINs, wrap in subquery
        if (!empty($this->update) && !empty($this->join)) {
            $updateTable = $this->update;
            $quotedTable = $this->connection->quoteName($updateTable);

            // Build subquery: SELECT table.rowid FROM table JOIN ...
            $subquery = "SELECT {$quotedTable}.rowid FROM {$quotedTable}";
            $subquery .= ' ' . $this->buildJoin();

            // Add original WHERE conditions to subquery
            $originalWhere = $this->buildWhereClause();
            if ($originalWhere) {
                // Remove 'WHERE' prefix since we'll add it back
                $originalWhere = preg_replace('/^WHERE\\s+/i', '', $originalWhere);
                $subquery .= ' WHERE ' . $originalWhere;
            }

            return "WHERE rowid IN ({$subquery})";
        }

        return $this->buildWhereClause();
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
     * Sets a join element on the query
     *
     * SQLite does not support RIGHT JOIN. We emulate RIGHT JOIN by swapping
     * join sides to a LEFT JOIN. FULL OUTER JOIN is emulated for simple
     * column-to-column joins.
     *
     * @param   string  $table     The table join
     * @param   string  $leftKey   The left side of the join condition
     * @param   string  $rightKey  The right side of the join condition
     * @param   string  $type      The join type to perform
     * @return  void
     * @throws  UnsupportedSyntaxException
     */
    public function setJoin($table, $leftKey, $rightKey, $type = 'inner')
    {
        $type = strtolower($type);

        if (in_array($type, ['right', 'right outer'])) {
            // Emulate RIGHT JOIN by swapping the FROM table and JOIN target,
            // then using LEFT JOIN with reversed keys.
            if (count($this->from) !== 1 || isset($this->from[0]['raw'])) {
                throw new UnsupportedSyntaxException(
                    'SQLite RIGHT JOIN emulation requires a single table in FROM.',
                    500
                );
            }

            $fromTable = $this->from[0]['table'] ?? null;
            $fromAlias = $this->from[0]['as'] ?? null;
            if (!$fromTable) {
                throw new UnsupportedSyntaxException(
                    'SQLite RIGHT JOIN emulation requires a simple FROM table.',
                    500
                );
            }

            $joinTable = $table;
            $joinAlias = null;

            if (preg_match('/\s+AS\s+/i', $table)) {
                [$joinTable, $joinAlias] = preg_split('/\s+AS\s+/i', $table, 2);
            } elseif (preg_match('/\s+/', $table)) {
                [$joinTable, $joinAlias] = preg_split('/\s+/', $table, 2);
            }

            $this->from[0]['table'] = $joinTable;
            if ($joinAlias) {
                $this->from[0]['as'] = $joinAlias;
            } else {
                unset($this->from[0]['as']);
            }

            $leftJoinTable = $fromTable;
            if ($fromAlias) {
                $leftJoinTable .= ' ' . $fromAlias;
            }

            parent::setJoin($leftJoinTable, $rightKey, $leftKey, 'left');
            return;
        }

        parent::setJoin($table, $leftKey, $rightKey, $type);
    }

    /**
     * Sets a raw join element on the query
     *
     * SQLite does not support RIGHT JOIN or FULL OUTER JOIN.
     *
     * @param   string  $table  The table join
     * @param   string  $raw    The join clause
     * @param   string  $type   The join type to perform
     * @return  $this
     * @throws  UnsupportedSyntaxException
     */
    public function setRawJoin($table, $raw, $type = 'inner')
    {
        $type = strtolower($type);

        if (in_array($type, ['right', 'right outer', 'full', 'full outer'])) {
            throw new UnsupportedSyntaxException(
                'RIGHT and FULL OUTER JOINs are not supported by SQLite. ' .
                'Consider rewriting as a LEFT JOIN with reversed table order.',
                500
            );
        }

        return parent::setRawJoin($table, $raw, $type);
    }

    /**
     * Returns the proper query for generating a list of table columns per this syntax
     *
     * @param   string  $table  The name of the database table
     * @return  string
     */
    public function getColumnsQuery($table)
    {
        return 'PRAGMA table_info(' . $this->connection->quoteName($table) . ')';
    }

    /**
     * Normalizes the results of the PRAGMA table_info query
     *
     * @param   array  $data      The raw column data from PRAGMA table_info
     * @param   bool   $typeOnly  True (default) to only return field types
     * @return  array
     */
    public function normalizeColumns($data, $typeOnly = true)
    {
        $results = [];

        if ($typeOnly) {
            foreach ($data as $field) {
                $results[$field->name] = $this->normalizeType($field->type);
            }
        } else {
            foreach ($data as $field) {
                $results[$field->name] = [
                    'name'      => $field->name,
                    'type'      => $this->normalizeType($field->type),
                    'raw_type'  => $field->type,
                    'allownull' => !$field->notnull,
                    'default'   => $this->normalizeDefault($field->dflt_value),
                    'pk'        => (bool) $field->pk
                ];
            }
        }

        return $results;
    }

    /**
     * Normalize a SQLite type declaration to its type affinity
     *
     * SQLite uses type affinity rules:
     * 1. If the type contains "INT" -> INTEGER
     * 2. If the type contains "CHAR", "CLOB", or "TEXT" -> TEXT
     * 3. If the type contains "BLOB" or is empty -> BLOB
     * 4. If the type contains "REAL", "FLOA", or "DOUB" -> REAL
     * 5. Otherwise -> NUMERIC
     *
     * @param   string  $type  The declared type
     * @return  string  The normalized type affinity
     */
    protected function normalizeType($type)
    {
        if (empty($type)) {
            return 'BLOB';
        }

        // Extract base type (remove size specifiers like VARCHAR(255))
        $baseType = strtolower(preg_replace('/\s*\([^)]+\)/', '', $type));
        $baseType = trim($baseType);

        // Check our mapping first
        if (isset(self::$typeAffinities[$baseType])) {
            return self::$typeAffinities[$baseType];
        }

        // Apply SQLite's type affinity rules
        $upperType = strtoupper($type);

        if (strpos($upperType, 'INT') !== false) {
            return 'INTEGER';
        }

        if (
            strpos($upperType, 'CHAR') !== false ||
            strpos($upperType, 'CLOB') !== false ||
            strpos($upperType, 'TEXT') !== false
        ) {
            return 'TEXT';
        }

        if (strpos($upperType, 'BLOB') !== false) {
            return 'BLOB';
        }

        if (
            strpos($upperType, 'REAL') !== false ||
            strpos($upperType, 'FLOA') !== false ||
            strpos($upperType, 'DOUB') !== false
        ) {
            return 'REAL';
        }

        // Default to NUMERIC affinity
        return 'NUMERIC';
    }

    /**
     * Normalize a default value from SQLite PRAGMA output
     *
     * SQLite returns default values with quotes for strings,
     * and special handling is needed for NULL and expressions.
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

        // Remove surrounding quotes from string defaults
        if (preg_match("/^'(.*)'$/s", $value, $matches)) {
            return str_replace("''", "'", $matches[1]);
        }

        // Remove surrounding double quotes
        if (preg_match('/^"(.*)"$/s', $value, $matches)) {
            return str_replace('""', '"', $matches[1]);
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
        return "SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = " .
            $this->connection->quote($table);
    }

    /**
     * Get the SQL for retrieving the CREATE TABLE statement
     *
     * @param   string  $table  The table name
     * @return  string
     */
    public function getTableCreateQuery($table)
    {
        return "SELECT sql FROM sqlite_master WHERE type = 'table' AND name = " .
            $this->connection->quote($table);
    }

    // =========================================================================
    // JSON Operations - SQLite specific overrides
    // =========================================================================

    /**
     * Sets a JSON path extraction where clause
     *
     * SQLite uses json_extract() with path syntax like "$.user.name"
     * Similar to MySQL's JSON_EXTRACT().
     *
     * Requires SQLite 3.9.0+ (2015-10-14) which added JSON1 extension.
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

        // SQLite's json_extract already returns unquoted values for scalars
        $raw = "json_extract({$quotedColumn}, '{$jsonPath}') {$operator} ?";

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
     * SQLite doesn't have a native JSON_CONTAINS function like MySQL.
     * We use json_each() with EXISTS to check array membership.
     *
     * Requires SQLite 3.9.0+ for JSON1 extension.
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
            // Extract the array at path, then iterate with json_each
            $raw = "EXISTS (SELECT 1 FROM json_each(json_extract({$quotedColumn}, '{$jsonPath}')) WHERE value = ?)";
        } else {
            // Iterate over top-level array
            $raw = "EXISTS (SELECT 1 FROM json_each({$quotedColumn}) WHERE value = ?)";
        }

        // For JSON contains, we need to handle the value type correctly
        // If it's a string, bind as string; if object/array, encode to JSON
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
     * SQLite uses json_array_length() function.
     *
     * Requires SQLite 3.9.0+ for JSON1 extension.
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
            // json_array_length can take a path as second argument
            $raw = "json_array_length({$quotedColumn}, '{$jsonPath}') {$operator} ?";
        } else {
            $raw = "json_array_length({$quotedColumn}) {$operator} ?";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [(int) $value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    // =========================================================================
    // Date/Time Operations - SQLite specific overrides
    // =========================================================================

    /**
     * Sets a date/time extraction where clause
     *
     * SQLite uses date(), time(), and strftime() functions.
     * Note: SQLite stores dates as TEXT, so these functions parse the text.
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
                $raw = "date({$quotedColumn}) {$operator} ?";
                break;
            case 'time':
                $raw = "time({$quotedColumn}) {$operator} ?";
                break;
            case 'year':
                // CAST to INTEGER for proper numeric comparison
                $raw = "CAST(strftime('%Y', {$quotedColumn}) AS INTEGER) {$operator} ?";
                break;
            case 'month':
                // CAST to INTEGER for proper numeric comparison
                $raw = "CAST(strftime('%m', {$quotedColumn}) AS INTEGER) {$operator} ?";
                break;
            case 'day':
                // CAST to INTEGER for proper numeric comparison
                $raw = "CAST(strftime('%d', {$quotedColumn}) AS INTEGER) {$operator} ?";
                break;
            default:
                $raw = "date({$quotedColumn}) {$operator} ?";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [$value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    // =========================================================================
    // Table Operations - SQLite specific overrides
    // =========================================================================

    /**
     * Get the SQL statements to truncate a table
     *
     * SQLite doesn't have TRUNCATE TABLE. We use DELETE FROM and
     * optionally reset the sqlite_sequence for auto-increment.
     *
     * @param   string  $table  The table to truncate
     * @return  array   Array of SQL statements to execute
     */
    public function getTruncateStatements($table)
    {
        $quotedTable = $this->connection->quoteName($table);

        return [
            'DELETE FROM ' . $quotedTable,
            // Reset auto-increment counter (if table has one)
            "DELETE FROM sqlite_sequence WHERE name = " . $this->connection->quote($table)
        ];
    }

    /**
     * Builds an upsert statement from the set params
     *
     * SQLite 3.24.0+ uses "INSERT INTO ... ON CONFLICT (...) DO UPDATE SET" syntax.
     * Similar to PostgreSQL, requires conflict columns to be specified.
     *
     * Note: For SQLite < 3.24.0, you would need to use INSERT OR REPLACE,
     * but that deletes and re-inserts the row, resetting any ROWID.
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

        // SQLite requires conflict columns to be specified
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
            // SQLite uses excluded pseudo-table for the proposed values (like PostgreSQL)
            $updates[] = "$quotedCol = excluded.$quotedCol";
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
     * SQLite uses multi-row VALUES with ON CONFLICT ... DO UPDATE:
     * INSERT INTO t (cols) VALUES (...), (...) ON CONFLICT (keys)
     *   DO UPDATE SET col = excluded.col
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
            $updates[] = "$quotedCol = excluded.$quotedCol";
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
    // Expression Builder Support - SQLite specific overrides
    // =========================================================================

    /**
     * Build a function expression into SQLite-specific SQL
     *
     * Overrides MySQL syntax for functions that differ in SQLite:
     * - CONCAT: Uses || operator instead of CONCAT()
     * - NOW: Uses datetime('now')
     * - CURRENT_DATE: Uses date('now')
     * - CURRENT_TIME: Uses time('now')
     * - IFNULL: Uses COALESCE (standard SQL)
     * - Date parts: Uses strftime() instead of YEAR(), MONTH(), etc.
     * - DATE_FORMAT: Uses strftime() with different format codes
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
                // SQLite uses || for concatenation
                $parts = array_map([$this, 'buildExpressionArgument'], $args);
                return '(' . implode(' || ', $parts) . ')';

            case 'LENGTH':
                // SQLite's length() counts bytes for BLOBs but characters for TEXT
                // This is usually what we want
                return 'length(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'SUBSTRING':
                // SQLite uses substr() not SUBSTRING
                $col = $this->buildExpressionArgument($args[0]);
                $start = (int) $args[1];
                if ($args[2] !== null) {
                    return "substr({$col}, {$start}, " . (int) $args[2] . ')';
                }
                return "substr({$col}, {$start})";

            // Conditional functions
            case 'IFNULL':
                // SQLite doesn't have IFNULL but COALESCE works the same
                return 'COALESCE('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ')';

            // Math functions
            case 'CEIL':
                // SQLite 3.35.0+ has ceil(), for older versions we'd need a workaround
                // Using ceil() for modern SQLite
                return 'ceil(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'MOD':
                // SQLite uses % operator for modulo
                return '('
                    . $this->buildExpressionArgument($args[0]) . ' % '
                    . $this->buildExpressionArgument($args[1]) . ')';

            // Date/time functions
            case 'NOW':
                return "datetime('now')";

            case 'CURRENT_TIMESTAMP':
                return 'CURRENT_TIMESTAMP';

            case 'CURRENT_DATE':
                return "date('now')";

            case 'CURRENT_TIME':
                return "time('now')";

            case 'DATE':
                return 'date(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'TIME':
                return 'time(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'YEAR':
                return "CAST(strftime('%Y', " . $this->buildExpressionArgument($args[0]) . ') AS INTEGER)';

            case 'MONTH':
                return "CAST(strftime('%m', " . $this->buildExpressionArgument($args[0]) . ') AS INTEGER)';

            case 'DAY':
                return "CAST(strftime('%d', " . $this->buildExpressionArgument($args[0]) . ') AS INTEGER)';

            case 'HOUR':
                return "CAST(strftime('%H', " . $this->buildExpressionArgument($args[0]) . ') AS INTEGER)';

            case 'MINUTE':
                return "CAST(strftime('%M', " . $this->buildExpressionArgument($args[0]) . ') AS INTEGER)';

            case 'SECOND':
                return "CAST(strftime('%S', " . $this->buildExpressionArgument($args[0]) . ') AS INTEGER)';

            case 'DATE_FORMAT':
                // Convert MySQL date format codes to SQLite strftime codes
                $format = $this->convertDateFormat($args[1]);
                return "strftime('{$format}', " . $this->buildExpressionArgument($args[0]) . ')';

            default:
                // Fall back to parent for standard functions
                return parent::buildFunctionExpression($expression);
        }
    }

    /**
     * Convert MySQL DATE_FORMAT codes to SQLite strftime codes
     *
     * Common conversions:
     * - %Y -> %Y (4-digit year)
     * - %y -> %y (2-digit year)
     * - %m -> %m (month 01-12)
     * - %d -> %d (day 01-31)
     * - %H -> %H (hour 00-23)
     * - %i -> %M (minute 00-59) - MySQL uses %i, SQLite uses %M
     * - %s -> %S (second 00-59)
     * - %W -> %w (weekday name) - Different meaning!
     *
     * @param   string  $mysqlFormat  The MySQL format string
     * @return  string  The SQLite strftime format string
     */
    protected function convertDateFormat(string $mysqlFormat): string
    {
        // MySQL to SQLite format code conversions
        $conversions = [
            '%i' => '%M',  // Minutes: MySQL uses %i, SQLite uses %M
            '%s' => '%S',  // Seconds: standardize to uppercase
        ];

        return strtr($mysqlFormat, $conversions);
    }

    // =========================================================================
    // Bulk Insert Operations - SQLite specific override
    // =========================================================================

    /**
     * Builds a bulk insert statement from the set params
     *
     * SQLite uses "INSERT OR IGNORE" instead of MySQL's "INSERT IGNORE".
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

        // SQLite uses "INSERT OR IGNORE" syntax
        $ignore = $this->insertManyIgnore ? 'OR IGNORE ' : '';

        return sprintf(
            'INSERT %sINTO %s (%s) VALUES %s',
            $ignore,
            $this->connection->quoteName($this->insertManyTable),
            implode(', ', $quotedColumns),
            implode(', ', $valueSets)
        );
    }
}
