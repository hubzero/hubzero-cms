<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Base;

use Hubzero\Database\Exception\UnsupportedSyntaxException;
use InvalidArgumentException;

/**
 * Database SQL query syntax base class
 *
 * This abstract class provides the common SQL syntax building functionality
 * shared by all database drivers. Database-specific syntax classes should
 * extend this class and override methods as needed for their dialect.
 *
 * SQL standard features supported:
 * - Standard DML: SELECT, INSERT, UPDATE, DELETE with full clause support
 * - JOIN types: INNER, LEFT, RIGHT, CROSS, FULL OUTER joins (FULL JOIN limited to simple layouts)
 * - Subqueries: In WHERE, FROM, and SELECT clauses
 * - LIMIT/OFFSET: Result pagination using SQL:2008 standard syntax
 * - ORDER BY: Column-based and expression-based ordering
 * - GROUP BY/HAVING: Aggregation with filtering
 * - UNION queries: Combining result sets
 * - Prepared statements: Parameter binding for security
 *
 * Subclasses should override:
 * - buildLimit() if their database uses different pagination syntax
 * - buildInsert() if their database has INSERT IGNORE or similar
 * - getColumnsQuery() for database-specific column introspection
 * - normalizeColumns() to parse introspection results
 * - buildUpsert() for database-specific upsert syntax
 * - JSON/date methods if the database has different function names
 */
abstract class BaseSqlSyntax
{
    /**
     * The database connection object
     *
     * @var object
     */
    protected $connection = null;

    /**
     * The prepared statement binding parameters
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * The syntax element containers
     */
    protected $select = [];
    protected $distinct = false;
    protected $insert = '';
    protected $ignore = false;
    protected $update = '';
    protected $delete = '';
    protected $set    = [];
    protected $values = [];
    protected $from   = [];
    protected $join   = [];
    protected $where  = [];
    protected $group  = [];
    protected $having = [];
    protected $order  = [];
    protected $start  = '';
    protected $limit  = '';

    /**
     * Union queries
     *
     * Stores union queries to be appended to the main SELECT.
     * Each element is an array with 'query' (the SQL string),
     * 'bindings' (parameter bindings), and 'all' (boolean for UNION ALL).
     *
     * @var array
     */
    protected $union = [];

    /**
     * Upsert-related properties
     *
     * Note: $upsert is used by build() to check if upsert is configured.
     * The actual data is stored in $upsertTable, $upsertValues, etc.
     */
    protected $upsert = false;
    protected $upsertTable = '';
    protected $upsertValues = [];
    protected $upsertUpdateColumns = [];
    protected $upsertConflictColumns = [];

    /**
     * Bulk upsert-related properties
     *
     * Note: $upsertMany is used by build() to check if bulk upsert is
     * configured. The actual data is in $upsertMany* properties below.
     */
    protected $upsertMany = false;
    protected $upsertManyTable = '';
    protected $upsertManyRows = [];
    protected $upsertManyUpdateColumns = [];
    protected $upsertManyConflictColumns = [];

    /**
     * Bulk insert-related properties
     *
     * Note: $insertMany is used by the build() method to check if bulk insert is configured.
     * The actual data is stored in $insertManyTable, $insertManyRows, and $insertManyIgnore.
     */
    protected $insertMany = false;
    protected $insertManyTable = '';

    /**
     * The columns for INSERT ... SELECT
     *
     * @var  array
     **/
    protected $insertColumns = [];

    /**
     * The SELECT query for INSERT ... SELECT
     *
     * @var  string|null
     **/
    protected $insertSelectQuery = null;

    /**
     * Bindings for the INSERT ... SELECT query
     *
     * @var  array
     **/
    protected $insertSelectBindings = [];
    protected $insertManyRows = [];
    protected $insertManyIgnore = false;

    /**
     * Cached table columns for schema-aware resolution
     *
     * @var array
     */
    protected $tableColumnCache = [];

    /**
     * Whether we are emitting a FULL OUTER JOIN emulation query.
     *
     * When true, buildSelect() returns a full UNION query and the remaining
     * clause builders can return empty strings to avoid duplicating SQL.
     *
     * @var  bool
     */
    protected $fullJoinUnionBuilt = false;



    /**
     * Constructs query syntax class, setting database connection
     *
     * @param   object  $connection  The database connection to use
     * @return  void
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // =========================================================================
    // Getters and Binding
    // =========================================================================

    /**
     * Grabs the params bindings
     *
     * @return  array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Clears all parameter bindings
     *
     * @return  $this
     */
    public function clearBindings()
    {
        $this->bindings = [];
        return $this;
    }

    /**
     * Count FULL JOINs present on the query
     *
     * @return  int
     */
    protected function countFullJoins(): int
    {
        $count = 0;
        foreach ($this->join as $join) {
            if (!empty($join['type']) && strtolower($join['type']) === 'full') {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Check if any FULL joins are present
     *
     * @return  bool
     */
    protected function hasFullJoin(): bool
    {
        return $this->countFullJoins() > 0;
    }

    /**
     * Validate FULL OUTER JOIN layout for cross-database correctness
     *
     * To guarantee identical behavior across all drivers (including those
     * without native FULL JOIN support), we restrict FULL JOIN usage to a
     * single, simple FULL JOIN between the base FROM table and one joined
     * table. Additional JOINs are allowed only if they are INNER/LEFT joins
     * that reference tables introduced earlier in the join order.
     *
     * @return  void
     * @throws  UnsupportedSyntaxException
     */
    protected function validateFullJoinLayout(): void
    {
        if (!$this->hasFullJoin()) {
            return;
        }

        $fullIndexes = [];
        foreach ($this->join as $idx => $join) {
            $type = strtolower($join['type'] ?? '');
            if (in_array($type, ['full', 'full outer'], true)) {
                $fullIndexes[] = $idx;
            }
        }

        if (count($fullIndexes) !== 1) {
            throw new UnsupportedSyntaxException(
                'FULL OUTER JOIN support is limited to a single FULL JOIN between two base tables.',
                500
            );
        }

        $fullIndex = $fullIndexes[0];
        if ($fullIndex !== 0) {
            throw new UnsupportedSyntaxException(
                'FULL OUTER JOIN must be the first JOIN when used with additional joins.',
                500
            );
        }

        if (count($this->from) !== 1 || isset($this->from[0]['raw'])) {
            throw new UnsupportedSyntaxException(
                'FULL OUTER JOIN support requires a single table in FROM (no subqueries).',
                500
            );
        }

        $allowedTypes = ['left', 'left outer', 'inner'];
        foreach ($this->join as $idx => $join) {
            $type = strtolower($join['type'] ?? '');
            if ($idx === $fullIndex) {
                if (!in_array($type, ['full', 'full outer'], true)) {
                    throw new UnsupportedSyntaxException(
                        'FULL OUTER JOIN must use FULL JOIN type.',
                        500
                    );
                }
                if (isset($join['conditions'])) {
                    throw new UnsupportedSyntaxException(
                        'FULL OUTER JOIN support requires a single join predicate.',
                        500
                    );
                }
            } elseif (!in_array($type, $allowedTypes, true)) {
                throw new UnsupportedSyntaxException(
                    'FULL OUTER JOIN support allows only INNER or LEFT joins after the FULL join.',
                    500
                );
            }

            if (isset($join['raw']) || isset($join['subquery'])) {
                throw new UnsupportedSyntaxException(
                    'FULL OUTER JOIN support requires simple column-to-column joins.',
                    500
                );
            }

            if ($idx === $fullIndex) {
                if (
                    empty($join['left']) || empty($join['right'])
                    || !is_string($join['left'])
                    || !is_string($join['right'])
                ) {
                    throw new UnsupportedSyntaxException(
                        'FULL OUTER JOIN support requires simple column-to-column joins.',
                        500
                    );
                }
            } elseif (!isset($join['conditions'])) {
                if (
                    empty($join['left']) || empty($join['right'])
                    || !is_string($join['left'])
                    || !is_string($join['right'])
                ) {
                    throw new UnsupportedSyntaxException(
                        'FULL OUTER JOIN support requires simple column-to-column joins.',
                        500
                    );
                }
            } else {
                foreach ($join['conditions'] as $condition) {
                    $operator = strtolower((string) ($condition['operator'] ?? '='));
                    if (in_array($operator, ['exists', 'not exists'], true)) {
                        if (empty($condition['right'])) {
                            throw new UnsupportedSyntaxException(
                                'JOIN EXISTS predicates require a subquery expression.',
                                500
                            );
                        }
                        continue;
                    }

                    if (
                        empty($condition['left']) || empty($condition['right'])
                        || !is_string($condition['left'])
                        || (
                            !is_string($condition['right'])
                            && !(is_object($condition['right'])
                                && method_exists($condition['right'], 'build'))
                            && !is_array($condition['right'])
                        )
                    ) {
                        throw new UnsupportedSyntaxException(
                            'FULL OUTER JOIN support requires simple column-to-column joins.',
                            500
                        );
                    }
                }
            }
        }

        $availableMap = $this->buildRefToTableMap(
            $this->from[0]['table'] ?? '',
            $this->from[0]['as'] ?? null
        );
        $availableCandidates = $this->buildTableCandidates($availableMap);

        $fullJoin = $this->join[$fullIndex];

        // Reconstruct full table expression including alias if present
        $fullJoinTableExpr = $fullJoin['table'] ?? '';
        if (!empty($fullJoin['alias'])) {
            $fullJoinTableExpr .= ' ' . $fullJoin['alias'];
        }

        $fullJoinMap = $this->buildRefToTableMapFromTableExpr($fullJoinTableExpr);
        $fullJoinCandidates = $this->buildTableCandidates($fullJoinMap);

        if (empty($fullJoinMap)) {
            throw new UnsupportedSyntaxException(
                'FULL OUTER JOIN support requires a simple JOIN table.',
                500
            );
        }

        $leftInfo = $this->resolveJoinColumn(
            $fullJoin['left'],
            $availableMap,
            $fullJoinMap,
            $availableCandidates,
            $fullJoinCandidates,
            'available'  // Prefer left side (available tables) for left key
        );
        $rightInfo = $this->resolveJoinColumn(
            $fullJoin['right'],
            $availableMap,
            $fullJoinMap,
            $availableCandidates,
            $fullJoinCandidates,
            'join'  // Prefer right side (join table) for right key
        );

        if ($leftInfo['group'] === 'join' && $rightInfo['group'] === 'available') {
            $tmp = $leftInfo;
            $leftInfo = $rightInfo;
            $rightInfo = $tmp;
        }

        if (!($leftInfo['group'] === 'available' && $rightInfo['group'] === 'join')) {
            throw new UnsupportedSyntaxException(
                'FULL OUTER JOIN support requires base_table.column = joined_table.column.',
                500
            );
        }

        $this->join[$fullIndex]['left'] = $leftInfo['qualified'];
        $this->join[$fullIndex]['right'] = $rightInfo['qualified'];

        $availableMap = array_merge($availableMap, $fullJoinMap);
        $availableCandidates = $this->buildTableCandidates($availableMap);

        foreach ($this->join as $idx => $join) {
            if ($idx <= $fullIndex) {
                continue;
            }

            // Reconstruct full table expression including alias if present
            // (Firebird's setJoin stores alias separately in $join['alias'])
            $tableExpr = $join['table'] ?? '';
            if (!empty($join['alias'])) {
                $tableExpr .= ' ' . $join['alias'];
            }

            $joinMap = $this->buildRefToTableMapFromTableExpr($tableExpr);
            $joinCandidates = $this->buildTableCandidates($joinMap);

            if (empty($joinMap)) {
                throw new UnsupportedSyntaxException(
                    'FULL OUTER JOIN support requires a simple JOIN table.',
                    500
                );
            }

            $conditions = $join['conditions'] ?? [[
                'left' => $join['left'],
                'operator' => '=',
                'right' => $join['right'],
                'boolean' => 'and',
            ]];

            $linkFound = false;

            foreach ($conditions as $condIndex => $condition) {
                $leftInfo = $this->resolveJoinOperandInfo(
                    $condition['left'],
                    $availableMap,
                    $joinMap,
                    $availableCandidates,
                    $joinCandidates
                );
                $rightInfo = $this->resolveJoinOperandInfo(
                    $condition['right'],
                    $availableMap,
                    $joinMap,
                    $availableCandidates,
                    $joinCandidates
                );

                $valid = ($leftInfo['group'] === 'available' && $rightInfo['group'] === 'join')
                    || ($leftInfo['group'] === 'join' && $rightInfo['group'] === 'available')
                    || ($leftInfo['group'] === 'available' && $rightInfo['group'] === 'literal')
                    || ($leftInfo['group'] === 'literal' && $rightInfo['group'] === 'available')
                    || ($leftInfo['group'] === 'join' && $rightInfo['group'] === 'literal')
                    || ($leftInfo['group'] === 'literal' && $rightInfo['group'] === 'join')
                    || ($leftInfo['group'] === 'available' && $rightInfo['group'] === 'available')
                    || ($leftInfo['group'] === 'join' && $rightInfo['group'] === 'join');

                if (!$valid) {
                    throw new UnsupportedSyntaxException(
                        'JOIN keys must reference tables introduced earlier in the join order and the joined table.',
                        500
                    );
                }

                if ($leftInfo['group'] !== 'literal') {
                    $conditions[$condIndex]['left'] = $leftInfo['qualified'];
                }
                if ($rightInfo['group'] !== 'literal') {
                    $conditions[$condIndex]['right'] = $rightInfo['qualified'];
                }

                if (
                    ($leftInfo['group'] === 'available' && $rightInfo['group'] === 'join')
                    || ($leftInfo['group'] === 'join' && $rightInfo['group'] === 'available')
                ) {
                    $linkFound = true;
                }
            }

            if (!$linkFound) {
                throw new UnsupportedSyntaxException(
                    'JOIN keys must include at least one predicate linking the joined table to an earlier table.',
                    500
                );
            }

            if (isset($this->join[$idx]['conditions'])) {
                $this->join[$idx]['conditions'] = $conditions;
            } else {
                $this->join[$idx]['left'] = $conditions[0]['left'];
                $this->join[$idx]['right'] = $conditions[0]['right'];
            }

            $availableMap = array_merge($availableMap, $joinMap);
            $availableCandidates = $this->buildTableCandidates($availableMap);
        }
    }

    /**
     * Expand FULL JOINs into UNION ALL branches
     *
     * @return  array
     */
    protected function expandFullJoinBranches(): array
    {
        $this->validateFullJoinLayout();

        $branches = [
            [
                'from' => $this->from,
                'joins' => $this->join,
                'filters' => [],
            ],
        ];

        foreach ($this->join as $index => $join) {
            if (empty($join['type']) || strtolower($join['type']) !== 'full') {
                continue;
            }

            if (isset($join['raw']) || isset($join['subquery']) || empty($join['left']) || empty($join['right'])) {
                throw new UnsupportedSyntaxException(
                    'FULL OUTER JOIN emulation requires simple column-to-column joins. ' .
                    'Use explicit UNIONs for raw or subquery joins.'
                );
            }

            $next = [];
            foreach ($branches as $branch) {
                $left = $branch;
                $left['joins'][$index]['type'] = 'left';
                $left = $this->transformFullJoinBranch($left, $index, 'left');
                $next[] = $left;

                $right = $branch;
                $right['joins'][$index]['type'] = 'right';
                $right['filters'][] = $this->buildRightOnlyFilter($join['left'], $join['right']);
                $right = $this->transformFullJoinBranch($right, $index, 'right');
                $next[] = $right;
            }

            $branches = $next;
        }

        return $branches;
    }

    /**
     * Parse a table expression into base table and alias.
     *
     * @param   string  $table
     * @return  array{0:string,1:?string}
     */
    protected function parseTableAlias(string $table): array
    {
        $table = trim($table);
        $alias = null;

        if (preg_match('/\s+AS\s+/i', $table)) {
            [$table, $alias] = preg_split('/\s+AS\s+/i', $table, 2);
        } elseif (preg_match('/\s+/', $table)) {
            [$table, $alias] = preg_split('/\s+/', $table, 2);
        }

        $table = trim($table);
        $alias = $alias !== null ? trim($alias) : null;

        return [$table, $alias !== '' ? $alias : null];
    }

    /**
     * Get table references from the FROM clause (table name and alias).
     *
     * @param   array  $from
     * @return  array
     */
    protected function getFromTableRefs(array $from): array
    {
        $table = $from['table'] ?? null;
        $alias = $from['as'] ?? null;
        $refs = [];

        if (!empty($table)) {
            $refs[] = $table;
        }
        if (!empty($alias)) {
            $refs[] = $alias;
        }

        return $refs;
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
        if ($expr === '' || strpos($expr, '.') === false) {
            return null;
        }

        return substr($expr, 0, strpos($expr, '.'));
    }

    /**
     * Build a map of reference => table name.
     *
     * @param   string       $table
     * @param   string|null  $alias
     * @return  array
     */
    protected function buildRefToTableMap(string $table, ?string $alias): array
    {
        $map = [];
        $table = $this->normalizeIdentifier($table);
        $alias = $alias !== null ? $this->normalizeIdentifier($alias) : null;

        if ($table !== '') {
            $map[$table] = $table;
        }
        if ($alias !== null && $alias !== '' && !isset($map[$alias])) {
            $map[$alias] = $table;
        }

        return $map;
    }

    /**
     * Build a ref map from a table expression with optional alias.
     *
     * @param   string  $tableExpr
     * @return  array
     */
    protected function buildRefToTableMapFromTableExpr(string $tableExpr): array
    {
        [$table, $alias] = $this->parseTableAlias($tableExpr);
        if ($table === '') {
            return [];
        }

        return $this->buildRefToTableMap($table, $alias);
    }

    /**
     * Build table candidates with preferred refs for schema resolution.
     *
     * @param   array  $refToTable
     * @return  array
     */
    protected function buildTableCandidates(array $refToTable): array
    {
        $tableToRef = [];
        foreach ($refToTable as $ref => $table) {
            if (!isset($tableToRef[$table])) {
                $tableToRef[$table] = $ref;
                continue;
            }

            if ($tableToRef[$table] === $table && $ref !== $table) {
                $tableToRef[$table] = $ref;
            }
        }

        $candidates = [];
        foreach ($tableToRef as $table => $ref) {
            $candidates[] = [
                'table' => $table,
                'ref' => $ref,
            ];
        }

        return $candidates;
    }

    /**
     * Resolve a join column reference with optional schema lookup.
     *
     * @param   string  $expr
     * @param   array   $availableMap
     * @param   array   $joinMap
     * @param   array   $availableCandidates
     * @param   array   $joinCandidates
     * @param   string  $preferredGroup  Preferred group when ambiguous: 'available' or 'join'
     * @return  array
     */
    protected function resolveJoinColumn(
        string $expr,
        array $availableMap,
        array $joinMap,
        array $availableCandidates,
        array $joinCandidates,
        ?string $preferredGroup = null
    ): array {
        [$ref, $column] = $this->parseColumnReference($expr);

        if ($ref !== null) {
            if (isset($availableMap[$ref])) {
                return [
                    'group' => 'available',
                    'ref' => $ref,
                    'column' => $column,
                    'qualified' => $ref . '.' . $column,
                ];
            }

            if (isset($joinMap[$ref])) {
                return [
                    'group' => 'join',
                    'ref' => $ref,
                    'column' => $column,
                    'qualified' => $ref . '.' . $column,
                ];
            }

            throw new UnsupportedSyntaxException(
                'FULL OUTER JOIN references an unknown table alias.',
                500
            );
        }

        $matches = [];
        foreach ($availableCandidates as $candidate) {
            if ($this->tableHasColumnCached($candidate['table'], $column)) {
                $matches[] = [
                    'group' => 'available',
                    'ref' => $candidate['ref'],
                ];
            }
        }

        foreach ($joinCandidates as $candidate) {
            if ($this->tableHasColumnCached($candidate['table'], $column)) {
                $matches[] = [
                    'group' => 'join',
                    'ref' => $candidate['ref'],
                ];
            }
        }

        if (count($matches) === 1) {
            return [
                'group' => $matches[0]['group'],
                'ref' => $matches[0]['ref'],
                'column' => $column,
                'qualified' => $matches[0]['ref'] . '.' . $column,
            ];
        }

        if (empty($matches)) {
            throw new UnsupportedSyntaxException(
                'FULL OUTER JOIN could not resolve an unqualified column via schema.',
                500
            );
        }

        // Multiple matches - use preferred group if specified
        if ($preferredGroup !== null) {
            foreach ($matches as $match) {
                if ($match['group'] === $preferredGroup) {
                    return [
                        'group' => $match['group'],
                        'ref' => $match['ref'],
                        'column' => $column,
                        'qualified' => $match['ref'] . '.' . $column,
                    ];
                }
            }
        }

        throw new UnsupportedSyntaxException(
            'FULL OUTER JOIN has an ambiguous unqualified column.',
            500
        );
    }

    /**
     * Resolve a join operand that may be a column or literal.
     *
     * @param   mixed  $operand
     * @param   array  $availableMap
     * @param   array  $joinMap
     * @param   array  $availableCandidates
     * @param   array  $joinCandidates
     * @return  array
     */
    protected function resolveJoinOperandInfo(
        $operand,
        array $availableMap,
        array $joinMap,
        array $availableCandidates,
        array $joinCandidates
    ): array {
        if (is_object($operand) && method_exists($operand, 'build')) {
            return [
                'group' => 'literal',
                'qualified' => null,
            ];
        }

        if (!is_string($operand)) {
            return [
                'group' => 'literal',
                'qualified' => null,
            ];
        }

        return $this->resolveJoinColumn(
            $operand,
            $availableMap,
            $joinMap,
            $availableCandidates,
            $joinCandidates
        );
    }

    /**
     * Parse a column reference, optionally qualified with a table/alias.
     *
     * @param   string  $expr
     * @return  array
     */
    protected function parseColumnReference(string $expr): array
    {
        $expr = trim($expr);
        if ($expr === '' || preg_match('/[\\s\\(\\)\\=\\+\\-\\*\\/]/', $expr)) {
            throw new UnsupportedSyntaxException(
                'FULL OUTER JOIN support requires simple column references.',
                500
            );
        }

        if (substr_count($expr, '.') > 1) {
            throw new UnsupportedSyntaxException(
                'FULL OUTER JOIN support does not allow schema-qualified columns.',
                500
            );
        }

        $ref = null;
        $column = $expr;
        if (strpos($expr, '.') !== false) {
            [$ref, $column] = explode('.', $expr, 2);
        }

        $ref = $ref !== null ? $this->normalizeIdentifier($ref) : null;
        $column = $this->normalizeIdentifier($column);

        if ($column === '' || ($ref !== null && $ref === '')) {
            throw new UnsupportedSyntaxException(
                'FULL OUTER JOIN support requires simple column references.',
                500
            );
        }

        if (!preg_match('/^[A-Za-z0-9_#]+$/', $column)) {
            throw new UnsupportedSyntaxException(
                'FULL OUTER JOIN support requires simple column references.',
                500
            );
        }

        if ($ref !== null && !preg_match('/^[A-Za-z0-9_#]+$/', $ref)) {
            throw new UnsupportedSyntaxException(
                'FULL OUTER JOIN support requires simple column references.',
                500
            );
        }

        return [$ref, $column];
    }

    /**
     * Normalize an identifier by trimming quotes/backticks.
     *
     * @param   string  $identifier
     * @return  string
     */
    protected function normalizeIdentifier(string $identifier): string
    {
        $identifier = trim($identifier);
        return trim($identifier, " \t\n\r\0\x0B`\"[]");
    }

    /**
     * Check column existence with caching.
     *
     * @param   string  $table
     * @param   string  $column
     * @return  bool
     */
    protected function tableHasColumnCached(string $table, string $column): bool
    {
        $table = trim($table);
        if ($table === '') {
            return false;
        }

        if (!isset($this->tableColumnCache[$table])) {
            $columns = $this->connection->getTableColumns($table);
            $lower = array_map('strtolower', array_keys($columns));
            $this->tableColumnCache[$table] = array_fill_keys($lower, true);
        }

        return isset($this->tableColumnCache[$table][strtolower($column)]);
    }

    /**
     * Allow dialects to transform branches for FULL JOIN emulation
     *
     * @param   array   $branch     Branch definition
     * @param   int     $joinIndex  Index of the FULL join being expanded
     * @param   string  $direction  left|right
     * @return  array
     */
    protected function transformFullJoinBranch(array $branch, int $joinIndex, string $direction): array
    {
        return $branch;
    }

    /**
     * Append additional filters to a WHERE clause
     *
     * @param   string  $whereSql  Existing WHERE clause (may be empty)
     * @param   array   $filters   Additional raw filters to AND
     * @return  string
     */
    protected function appendFiltersToWhere(string $whereSql, array $filters): string
    {
        if (empty($filters)) {
            return $whereSql;
        }

        $suffix = implode(' AND ', $filters);

        if (!empty($whereSql)) {
            return $whereSql . ' AND ' . $suffix;
        }

        return 'WHERE ' . $suffix;
    }

    /**
     * Build a right-only filter for FULL JOIN emulation
     *
     * Prefer NOT EXISTS to avoid incorrect results when join keys are nullable.
     * Falls back to "left_key IS NULL" when the left table cannot be derived.
     *
     * @param   string  $leftKey   Left join key (e.g., users.id)
     * @param   string  $rightKey  Right join key (e.g., posts.user_id)
     * @return  string
     */
    protected function buildRightOnlyFilter(string $leftKey, string $rightKey): string
    {
        if (strpos($leftKey, '.') !== false) {
            $leftTable = substr($leftKey, 0, strpos($leftKey, '.'));

            if ($leftTable !== '') {
                // Quote table and column names for database compatibility
                $quotedTable = $this->connection->quoteName($leftTable);
                $quotedLeftKey = $this->connection->quoteName($leftKey);
                $quotedRightKey = $this->connection->quoteName($rightKey);

                return 'NOT EXISTS (SELECT 1 FROM '
                    . $quotedTable . ' WHERE '
                    . $quotedLeftKey . ' = '
                    . $quotedRightKey . ')';
            }
        }

        // Quote the left key for IS NULL check
        $quotedLeftKey = $this->connection->quoteName($leftKey);
        return $quotedLeftKey . ' IS NULL';
    }

    /**
     * Assemble a SELECT statement for FULL JOIN emulation
     *
     * @param   string  $selectSql  SELECT clause
     * @param   string  $fromSql    FROM clause
     * @param   string  $joinSql    JOIN clause
     * @param   string  $whereSql   WHERE clause
     * @param   string  $groupSql   GROUP BY clause
     * @param   string  $havingSql  HAVING clause
     * @return  string
     */
    protected function assembleFullJoinSelect(
        string $selectSql,
        string $fromSql,
        string $joinSql,
        string $whereSql,
        string $groupSql,
        string $havingSql
    ): string {
        $parts = [
            $selectSql,
            $fromSql,
            $joinSql,
            $whereSql,
            $groupSql,
            $havingSql,
        ];

        $parts = array_values(array_filter($parts, function ($part) {
            return $part !== '';
        }));

        return implode("\n", $parts);
    }

    /**
     * Gets the primary table name from the FROM clause
     *
     * Returns the first table specified in the FROM clause, which is
     * typically the primary table being queried. This is used by
     * convenience methods like increment() and decrement().
     *
     * @return  string|null  The table name, or null if no FROM clause set
     */
    public function getFromTable()
    {
        if (empty($this->from)) {
            return null;
        }

        $first = $this->from[0];

        // Raw from expressions (subqueries) don't have a simple table name
        if (isset($first['raw'])) {
            return null;
        }

        return $first['table'] ?? null;
    }

    /**
     * Sets a new bind value
     *
     * @param   string  $value  The value to bind
     * @param   string  $type   The value type
     * @return  $this
     */
    public function bind($value, $type = null)
    {
        $this->bindings[] = $value;
        return $this;
    }

    // =========================================================================
    // Select Methods
    // =========================================================================

    /**
     * Empty select values
     *
     * @return  void
     */
    public function resetSelect()
    {
        $this->select = [];
    }

    /**
     * Sets a select element on the query
     *
     * @param   string  $column  The column to select
     * @param   string  $as      What to call the return val
     * @param   bool    $count   Whether or not to count column
     * @return  void
     */
    public function setSelect($column, $as = null, $count = false)
    {
        // A default * is often added, get rid of it if anything else is added
        // This wouldn't get rid of table.* as that is likely added intentionally
        if (isset($this->select[0]) && $this->select[0]['column'] == '*') {
            $this->resetSelect();
        }

        $this->select[] = [
            'column' => $column,
            'as'     => $as,
            'count'  => $count
        ];
    }

    /**
     * Sets the DISTINCT modifier for SELECT queries
     *
     * @param   bool  $distinct  Whether to use DISTINCT
     * @return  $this
     */
    public function setDistinct($distinct = true)
    {
        $this->distinct = (bool) $distinct;
        return $this;
    }

    /**
     * Sets a raw select element on the query (for subqueries and expressions)
     *
     * @param   string  $raw       The raw SQL expression
     * @param   array   $bindings  Parameter bindings for the expression
     * @param   string  $as        What to call the return val
     * @return  void
     */
    public function setRawSelect($raw, $bindings = [], $as = null)
    {
        // A default * is often added, get rid of it if anything else is added
        if (isset($this->select[0]) && $this->select[0]['column'] == '*') {
            $this->resetSelect();
        }

        $this->select[] = [
            'raw'      => $raw,
            'bindings' => $bindings,
            'as'       => $as
        ];
    }

    // =========================================================================
    // Insert Methods
    // =========================================================================

    /**
     * Sets an insert element on the query
     *
     * @param   string  $table   The table into which we will be inserting
     * @param   bool    $ignore  Whether or not to ignore errors produced related to things like duplicate keys
     * @return  void
     */
    public function setInsert($table, $ignore = false)
    {
        $this->insert = $table;
        $this->ignore = $ignore;
    }

    /**
     * Sets the columns for an INSERT statement
     *
     * Used with setInsertSelect() to specify which columns to insert into.
     * If not specified, the SELECT query determines the columns.
     *
     * @param   array  $columns  The column names
     * @return  void
     */
    public function setColumns(array $columns)
    {
        $this->insertColumns = $columns;
    }

    /**
     * Sets a SELECT query as the source for an INSERT statement
     *
     * Enables INSERT ... SELECT patterns.
     *
     * @param   string  $selectSql  The SELECT query SQL
     * @param   array   $bindings   Parameter bindings for the SELECT query
     * @return  void
     */
    public function setInsertSelect($selectSql, array $bindings = [])
    {
        $this->insertSelectQuery = $selectSql;
        $this->insertSelectBindings = $bindings;
    }

    /**
     * Check if INSERT IGNORE mode is enabled
     *
     * @return  bool
     */
    public function isIgnore()
    {
        return $this->ignore;
    }

    /**
     * Check if this is an INSERT IGNORE ... SELECT that needs row-by-row execution
     *
     * Databases with native INSERT IGNORE syntax (MySQL, SQLite) return false.
     * Databases without it (Firebird, Oracle, etc.) return true when both the
     * ignore flag and an INSERT SELECT query are set, because the single-statement
     * approach would fail entirely on the first duplicate instead of skipping it.
     *
     * @return bool
     */
    public function needsRowByRowInsertIgnore(): bool
    {
        return false;
    }

    /**
     * Get the INSERT SELECT query SQL
     *
     * @return string|null
     */
    public function getInsertSelectQuery(): ?string
    {
        return $this->insertSelectQuery;
    }

    /**
     * Get the INSERT SELECT bindings
     *
     * @return array
     */
    public function getInsertSelectBindings(): array
    {
        return $this->insertSelectBindings;
    }

    /**
     * Get the target table for INSERT
     *
     * @return string
     */
    public function getInsertTable(): string
    {
        return $this->insert;
    }

    /**
     * Get the columns for INSERT
     *
     * @return array
     */
    public function getInsertColumns(): array
    {
        return $this->insertColumns;
    }

    /**
     * Empty insert values
     *
     * @return  void
     */
    public function resetInsert()
    {
        $this->insert = '';
        $this->ignore = false;
        $this->insertColumns = [];
        $this->insertSelectQuery = null;
        $this->insertSelectBindings = [];
    }

    /**
     * Resets INSERT ... SELECT values
     *
     * @return  void
     */
    public function resetInsertSelect()
    {
        $this->insertColumns = [];
        $this->insertSelectQuery = null;
        $this->insertSelectBindings = [];
    }

    // =========================================================================
    // Update Methods
    // =========================================================================

    /**
     * Sets an update element on the query
     *
     * @param   string  $table  The table whose fields will be updated
     * @return  void
     */
    public function setUpdate($table)
    {
        $this->update = $table;
    }

    /**
     * Empty update values
     *
     * @return  void
     */
    public function resetUpdate()
    {
        $this->update = '';
        // Also clear SET values when clearing UPDATE
        $this->set = [];
    }

    // =========================================================================
    // Delete Methods
    // =========================================================================

    /**
     * Sets a delete element on the query
     *
     * @param   string  $table  The table whose row will be deleted
     * @return  void
     */
    public function setDelete($table)
    {
        $this->delete = $table;
    }

    /**
     * Empty update values
     *
     * @return  void
     */
    public function resetDelete()
    {
        $this->delete = '';
    }

    // =========================================================================
    // From Methods
    // =========================================================================

    /**
     * Sets a from element on the query
     *
     * @param   string  $table  The table of interest
     * @param   string  $as     What to call the table
     * @return  void
     */
    public function setFrom($table, $as = null)
    {
        $this->from[] = [
            'table' => $table,
            'as'    => $as
        ];
    }

    /**
     * Sets a raw from element on the query (for subqueries)
     *
     * @param   string  $raw       The raw SQL expression (subquery)
     * @param   array   $bindings  Parameter bindings for the expression
     * @param   string  $as        Alias for the derived table (required for subqueries)
     * @return  void
     */
    public function setRawFrom($raw, $bindings = [], $as = null)
    {
        $this->from[] = [
            'raw'      => $raw,
            'bindings' => $bindings,
            'as'       => $as
        ];
    }

    /**
     * Empty from values
     *
     * @return  void
     */
    public function resetFrom()
    {
        $this->from = [];
    }

    // =========================================================================
    // Join Methods
    // =========================================================================

    /**
     * Sets a join element on the query
     *
     * @param   string  $table     The table join
     * @param   string  $leftKey   The left side of the join condition
     * @param   string  $rightKey  The right side of the join condition
     * @param   string  $type      The join type to perform
     * @return  void
     */
    public function setJoin($table, $leftKey, $rightKey, $type = 'inner')
    {
        $this->join[] = [
            'table' => $table,
            'left'  => $leftKey,
            'right' => $rightKey,
            'type'  => $type
        ];
    }

    /**
     * Sets a join element using multiple ON predicates
     *
     * Conditions accept the following forms:
     * - ['left', 'right']
     * - ['left', '=', 'right']
     * - ['or', 'left', '=', 'right']
     * - ['left' => 'a.id', 'operator' => '=', 'right' => 'b.a_id', 'boolean' => 'and']
     *
     * @param   string  $table
     * @param   array   $conditions
     * @param   string  $type
     * @return  void
     */
    public function setJoinOn($table, array $conditions, $type = 'inner')
    {
        $normalized = $this->normalizeJoinConditions($conditions);

        if (in_array(strtolower($type), ['full', 'full outer'], true)) {
            if (count($normalized) !== 1) {
                throw new UnsupportedSyntaxException(
                    'FULL OUTER JOIN support requires a single join predicate.',
                    500
                );
            }

            $condition = $normalized[0];
            if (strtolower($condition['operator']) !== '=' || strtolower($condition['boolean']) !== 'and') {
                throw new UnsupportedSyntaxException(
                    'FULL OUTER JOIN support requires a simple equality predicate.',
                    500
                );
            }

            $this->setJoin($table, $condition['left'], $condition['right'], $type);
            return;
        }

        $this->join[] = [
            'table' => $table,
            'conditions' => $normalized,
            'type' => $type,
        ];
    }

    /**
     * Empty join values
     *
     * @return  void
     */
    public function resetJoin()
    {
        $this->join = [];
    }

    /**
     * Normalize join conditions into a consistent shape
     *
     * @param   array  $conditions
     * @return  array
     */
    protected function normalizeJoinConditions(array $conditions): array
    {
        if (empty($conditions)) {
            throw new InvalidArgumentException('Join conditions cannot be empty.');
        }

        $normalized = [];

        foreach ($conditions as $condition) {
            if (!is_array($condition)) {
                throw new InvalidArgumentException('Join conditions must be arrays.');
            }

            if (array_key_exists('group', $condition)) {
                $group = $condition['group'];
                if (!is_array($group) || empty($group)) {
                    throw new InvalidArgumentException('Join condition groups cannot be empty.');
                }
                $boolean = $condition['boolean'] ?? 'and';
                $normalized[] = [
                    'group' => $this->normalizeJoinConditions($group),
                    'boolean' => strtolower((string) $boolean),
                ];
                continue;
            }

            $left = null;
            $right = null;
            $operator = '=';
            $boolean = 'and';

            if (
                array_key_exists('left', $condition) || array_key_exists('right', $condition)
                || array_key_exists('value', $condition) || array_key_exists('literal', $condition)
                || array_key_exists('left_value', $condition) || array_key_exists('left_literal', $condition)
            ) {
                $left = $condition['left'] ?? null;
                $right = $condition['right'] ?? null;
                $operator = $condition['operator'] ?? '=';
                $boolean = $condition['boolean'] ?? 'and';
                $operatorLower = strtolower((string) $operator);
                if (array_key_exists('left_value', $condition) || array_key_exists('left_literal', $condition)) {
                    if (array_key_exists('left_value', $condition)) {
                        $value = $condition['left_value'];
                    } else {
                        $value = $condition['left_literal'];
                    }
                    if (is_array($value)) {
                        throw new InvalidArgumentException('Left-side join literals cannot be arrays.');
                    }
                    $left = $this->wrapJoinLiteral($value, $operator);
                }
                if (array_key_exists('value', $condition) || array_key_exists('literal', $condition)) {
                    if (array_key_exists('value', $condition)) {
                        $value = $condition['value'];
                    } else {
                        $value = $condition['literal'];
                    }
                    if (in_array($operatorLower, ['exists', 'not exists'], true)) {
                        $right = $value;
                    } else {
                        $right = $this->wrapJoinLiteral($value, $operator);
                    }
                }
            } else {
                $count = count($condition);

                if ($count === 2) {
                    if (in_array(strtolower((string) $condition[0]), ['exists', 'not exists'], true)) {
                        $operator = $condition[0];
                        $right = $condition[1];
                    } else {
                        [$left, $right] = $condition;
                    }
                } elseif ($count === 3) {
                    if (in_array(strtolower((string) $condition[0]), ['and', 'or'], true)) {
                        $boolean = $condition[0];
                        $left = $condition[1];
                        $right = $condition[2];
                    } else {
                        $left = $condition[0];
                        $operator = $condition[1];
                        $right = $condition[2];
                    }
                } elseif ($count === 4) {
                    $boolean = $condition[0];
                    $left = $condition[1];
                    $operator = $condition[2];
                    $right = $condition[3];
                } else {
                    throw new InvalidArgumentException('Invalid join condition format.');
                }
            }

            $operatorLower = strtolower((string) $operator);
            if (in_array($operatorLower, ['exists', 'not exists'], true)) {
                if ($right === null) {
                    throw new InvalidArgumentException('EXISTS join conditions require a subquery expression.');
                }
                $left = $left ?? '';
            } else {
                if ($left === null || $right === null) {
                    throw new InvalidArgumentException('Join conditions require left and right operands.');
                }
            }

            $normalized[] = [
                'left' => $left,
                'operator' => $operator,
                'right' => $right,
                'boolean' => strtolower((string) $boolean),
            ];
        }

        return $normalized;
    }

    /**
     * Wrap join literal values for proper binding/NULL handling.
     *
     * @param   mixed   $value
     * @param   string  $operator
     * @return  mixed
     */
    protected function wrapJoinLiteral($value, string $operator)
    {
        $operator = strtolower($operator);
        if (is_object($value) && method_exists($value, 'build')) {
            return $value;
        }

        if (is_array($value)) {
            return $value;
        }

        if ($value === null && in_array($operator, ['is', 'is not'], true)) {
            return new \Hubzero\Database\Value\Raw('NULL');
        }

        return new \Hubzero\Database\Value\Basic($value);
    }

    /**
     * Sets a join element on the query
     *
     * @param   string  $table  The table join
     * @param   string  $raw    The join clause
     * @param   string  $type   The join type to perform
     * @return  $this
     */
    public function setRawJoin($table, $raw, $type = 'inner')
    {
        $this->join[] = [
            'table'  => $table,
            'raw'    => $raw,
            'type'   => $type
        ];
    }

    /**
     * Sets a subquery join element on the query
     *
     * @param   string  $subquery   The subquery SQL
     * @param   array   $bindings   Parameter bindings for the subquery
     * @param   string  $as         Alias for the derived table
     * @param   string  $leftKey    The left side of the join condition
     * @param   string  $rightKey   The right side of the join condition
     * @param   string  $type       The join type to perform
     * @return  void
     */
    public function setSubqueryJoin($subquery, $bindings, $as, $leftKey, $rightKey, $type = 'inner')
    {
        $this->join[] = [
            'subquery'  => $subquery,
            'bindings'  => $bindings,
            'as'        => $as,
            'left'      => $leftKey,
            'right'     => $rightKey,
            'type'      => $type
        ];
    }

    // =========================================================================
    // Set/Values Methods (for UPDATE/INSERT)
    // =========================================================================

    /**
     * Sets a set element on the query
     *
     * @param   array  $data  The data to be modified
     * @return  void
     */
    public function setSet($data)
    {
        // Merge with existing SET clauses instead of overwriting
        if (!is_array($this->set)) {
            $this->set = [];
        }
        $this->set = array_merge($this->set, $data);
    }

    /**
     * Empty join values
     *
     * @return  void
     */
    public function resetSet()
    {
        $this->set = [];
    }
    /**
     * Sets a values element on the query
     *
     * @param   array  $data  The data to be inserted
     * @return  void
     */
    public function setValues($data)
    {
        $this->values = $data;
    }

    /**
     * Empty values
     *
     * @return  void
     */
    public function resetValues()
    {
        $this->values = [];
    }

    // =========================================================================
    // Group By Methods
    // =========================================================================

    /**
     * Sets a group element on the query
     *
     * @param   string  $column  The column on which to apply the group by
     * @return  void
     */
    public function setGroup($column)
    {
        $this->group[] = $column;
    }

    /**
     * Empty group values
     *
     * @return  void
     */
    public function resetGroup()
    {
        $this->group = [];
    }

    // =========================================================================
    // Having Methods
    // =========================================================================

    /**
     * Sets a having element on the query
     *
     * @param   string  $column    The column to which the clause will apply
     * @param   string  $operator  The operation that will compare column to value
     * @param   string  $value     The value to which the column will be evaluated
     * @return  void
     */
    public function setHaving($column, $operator, $value)
    {
        $this->having[] = [
            'column'   => $column,
            'operator' => $operator,
            'value'    => $value
        ];
    }

    /**
     * Sets a raw having clause on the query
     *
     * @param   string  $raw       The raw having clause
     * @param   array   $bindings  The clause bindings, if any
     * @param   string  $logical   The operator between multiple clauses (AND/OR)
     * @return  void
     */
    public function setRawHaving($raw, $bindings = [], $logical = 'and')
    {
        $this->having[] = [
            'raw'      => $raw,
            'bindings' => $bindings,
            'logical'  => $logical
        ];
    }

    /**
     * Empty having values
     *
     * @return  void
     */
    public function resetHaving()
    {
        $this->having = [];
    }

    // =========================================================================
    // Where Methods
    // =========================================================================

    /**
     * Sets a where element on the query
     *
     * @param   string  $column    The column to which the clause will apply
     * @param   string  $operator  The operation that will compare column to value
     * @param   string  $value     The value to which the column will be evaluated
     * @param   string  $logical   The operator between multiple clauses
     * @param   int     $depth     The depth level of the clause, for sub clauses
     * @return  void
     */
    public function setWhere($column, $operator, $value, $logical = 'and', $depth = 0)
    {
        $this->where[] = [
            'column'   => $column,
            'operator' => $operator,
            'value'    => $value,
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    /**
     * Sets a raw where element on the query
     *
     * @param   string  $raw       The raw where clause
     * @param   array   $bindings  The clause bindings, if any
     * @param   string  $logical   The operator between multiple clauses
     * @param   int     $depth     The depth level of the clause, for sub clauses
     * @return  void
     */
    public function setRawWhere($raw, $bindings = [], $logical = 'and', $depth = 0)
    {
        $this->where[] = [
            'raw'      => $raw,
            'bindings' => $bindings,
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    /**
     * Sets the query clause depth
     *
     * @param   int  $depth  The depth to set to
     * @return  void
     */
    public function resetDepth($depth = 0)
    {
        $this->where[] = [
            'resetdepth' => true,
            'depth'      => $depth
        ];
    }

    /**
     * Empty where values
     *
     * @return  void
     */
    public function resetWhere()
    {
        $this->where = [];
    }

    // =========================================================================
    // Limit/Offset Methods
    // =========================================================================

    /**
     * Sets a limit element on the query
     *
     * @param   int  $limit  Number of results to return on next query
     * @return  void
     */
    public function setLimit($limit)
    {
        if (!is_int($limit) || $limit < 0) {
            throw new InvalidArgumentException('Limit must be a whole integer of 0 or greater.');
        }

        $this->limit = $limit;
    }

    /**
     * Sets a start element on the query
     *
     * @param   int  $start  Position to start from
     * @return  void
     */
    public function setStart($start)
    {
        if (!is_int($start) || $start < 0) {
            throw new InvalidArgumentException('Start must be a whole integer of 0 or greater.');
        }

        $this->start = $start;
    }

    // =========================================================================
    // Order By Methods
    // =========================================================================

    /**
     * Sets an order element on the query
     *
     * @param   string  $column  The column to which the order by will apply
     * @param   string  $dir     The direction in which the results will be ordered
     * @return  void
     */
    public function setOrder($column, $dir)
    {
        if (!in_array(strtolower($dir), ['asc', 'desc'])) {
            throw new InvalidArgumentException('Order direction must be one of ASC or DESC.');
        }

        $this->order[] = [
            'column' => $column,
            'dir'    => $dir
        ];
    }

    /**
     * Resets an order element on the query
     *
     * @return  void
     */
    public function resetOrder()
    {
        $this->order = [];
    }

    /**
     * Returns the order clauses set on this syntax instance
     *
     * Each element is ['column' => string, 'dir' => string].
     *
     * @return  array
     */
    public function getOrderClauses(): array
    {
        return $this->order;
    }

    /**
     * Returns the current limit value
     *
     * @return  int
     */
    public function getLimit(): int
    {
        return (int) $this->limit;
    }

    // =========================================================================
    // Union Methods
    // =========================================================================

    /**
     * Adds a UNION query
     *
     * @param   string  $query     The SQL query string to union
     * @param   array   $bindings  Parameter bindings for the query
     * @param   bool    $all       Whether to use UNION ALL (true) or UNION (false)
     * @return  void
     */
    public function setUnion($query, array $bindings = [], $all = false)
    {
        $this->union[] = [
            'query'    => $query,
            'bindings' => $bindings,
            'all'      => $all
        ];
    }

    // =========================================================================
    // Upsert Methods
    // =========================================================================

    /**
     * Sets the upsert element on the query
     *
     * @param   string      $table            The table for the upsert
     * @param   array       $values           Key-value pairs to insert
     * @param   array|null  $updateColumns    Columns to update on conflict
     * @param   array|null  $conflictColumns  Columns that define conflict
     * @return  void
     */
    public function setUpsert(
        string $table,
        array $values,
        ?array $updateColumns = null,
        ?array $conflictColumns = null
    ) {
        $this->upsert = true;
        $this->upsertTable = $table;
        $this->upsertValues = $values;
        $this->upsertUpdateColumns = $updateColumns ?? array_keys($values);
        $this->upsertConflictColumns = $conflictColumns ?? [];
    }

    /**
     * Empty upsert values
     *
     * @return  void
     */
    public function resetUpsert()
    {
        $this->upsert = false;
        $this->upsertTable = '';
        $this->upsertValues = [];
        $this->upsertUpdateColumns = [];
        $this->upsertConflictColumns = [];
    }

    // =========================================================================
    // Bulk Upsert Methods
    // =========================================================================

    /**
     * Sets the bulk upsert parameters
     *
     * @param   string      $table           The table for the upsert
     * @param   array       $rows            Array of associative arrays
     * @param   array|null  $updateColumns   Columns to update on conflict
     * @param   array|null  $conflictColumns Columns that define conflict
     * @return  void
     */
    public function setUpsertMany(
        string $table,
        array $rows,
        ?array $updateColumns = null,
        ?array $conflictColumns = null
    ) {
        $this->upsertMany = true;
        $this->upsertManyTable = $table;
        $this->upsertManyRows = $rows;
        $columns = array_keys($rows[0]);
        $this->upsertManyUpdateColumns = $updateColumns ?? $columns;
        $this->upsertManyConflictColumns = $conflictColumns ?? [];
    }

    /**
     * Reset bulk upsert state
     *
     * @return  void
     */
    public function resetUpsertMany()
    {
        $this->upsertMany = false;
        $this->upsertManyTable = '';
        $this->upsertManyRows = [];
        $this->upsertManyUpdateColumns = [];
        $this->upsertManyConflictColumns = [];
    }

    /**
     * Builds a bulk upsert statement
     *
     * Default implementation returns empty string, which triggers
     * individual upsert fallback in Query::upsertMany(). Override
     * in subclasses that support multi-row upsert.
     *
     * @return  string
     */
    public function buildUpsertMany()
    {
        return '';
    }

    // =========================================================================
    // Bulk Insert Methods
    // =========================================================================

    /**
     * Sets the bulk insert element on the query
     *
     * @param   string  $table   The table for the bulk insert
     * @param   array   $rows    Array of rows, where each row is an associative array
     * @param   bool    $ignore  Whether to use INSERT IGNORE (database-specific)
     * @return  void
     */
    public function setInsertMany(string $table, array $rows, bool $ignore = false)
    {
        $this->insertMany = true;
        $this->insertManyTable = $table;
        $this->insertManyRows = $rows;
        $this->insertManyIgnore = $ignore;
    }

    /**
     * Empty bulk insert values
     *
     * @return  void
     */
    public function resetInsertMany()
    {
        $this->insertMany = false;
        $this->insertManyTable = '';
        $this->insertManyRows = [];
        $this->insertManyIgnore = false;
    }

    // =========================================================================
    // JSON Operations (Default implementations - override in subclasses)
    // =========================================================================

    /**
     * Sets a JSON path extraction where clause
     *
     * Default implementation throws UnsupportedSyntaxException.
     * Database-specific classes should override with their JSON syntax.
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
            'JSON path queries are not supported by the base SQL syntax. ' .
            'Use a database-specific syntax class that supports JSON operations.'
        );
    }

    /**
     * Sets a JSON contains where clause
     *
     * Default implementation throws UnsupportedSyntaxException.
     * Database-specific classes should override with their JSON syntax.
     *
     * @param   string       $column   The JSON column name
     * @param   mixed        $value    The value to search for
     * @param   string|null  $path     Optional dot-notation path to a nested array
     * @param   string       $logical  The operator between multiple clauses
     * @param   int          $depth    The depth level of the clause
     * @return  void
     * @throws  UnsupportedSyntaxException
     */
    public function setJsonContainsWhere($column, $value, $path = null, $logical = 'and', $depth = 0)
    {
        throw new UnsupportedSyntaxException(
            'JSON contains queries are not supported by the base SQL syntax. ' .
            'Use a database-specific syntax class that supports JSON operations.'
        );
    }

    /**
     * Sets a JSON length where clause
     *
     * Default implementation throws UnsupportedSyntaxException.
     * Database-specific classes should override with their JSON syntax.
     *
     * @param   string       $column    The JSON column name
     * @param   string       $operator  Comparison operator
     * @param   int          $value     The length value to compare against
     * @param   string|null  $path      Optional dot-notation path to a nested array
     * @param   string       $logical   The operator between multiple clauses
     * @param   int          $depth     The depth level of the clause
     * @return  void
     * @throws  UnsupportedSyntaxException
     */
    public function setJsonLengthWhere($column, $operator, $value, $path = null, $logical = 'and', $depth = 0)
    {
        throw new UnsupportedSyntaxException(
            'JSON length queries are not supported by the base SQL syntax. ' .
            'Use a database-specific syntax class that supports JSON operations.'
        );
    }

    /**
     * MySQL-style JSON path where clause helper.
     *
     * Generates: JSON_UNQUOTE(JSON_EXTRACT(col, '$.path')) op ?
     *
     * @param   string  $column
     * @param   string  $path
     * @param   string  $operator
     * @param   mixed   $value
     * @param   string  $logical
     * @param   int     $depth
     * @return  void
     */
    protected function setJsonPathWhereWithMySqlFunctions(
        $column,
        $path,
        $operator,
        $value,
        $logical = 'and',
        $depth = 0
    ) {
        $jsonPath = $this->convertToJsonPath($path);
        $quotedColumn = $this->connection->quoteName($column);
        $raw = "JSON_UNQUOTE(JSON_EXTRACT({$quotedColumn}, '{$jsonPath}')) {$operator} ?";

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [$value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    /**
     * MySQL-style JSON contains where clause helper.
     *
     * Generates JSON_CONTAINS(col, ?, '$.path') and binds JSON-encoded value.
     *
     * @param   string       $column
     * @param   mixed        $value
     * @param   string|null  $path
     * @param   string       $logical
     * @param   int          $depth
     * @return  void
     */
    protected function setJsonContainsWhereWithMySqlFunctions(
        $column,
        $value,
        $path = null,
        $logical = 'and',
        $depth = 0
    ) {
        $quotedColumn = $this->connection->quoteName($column);
        $jsonValue = json_encode($value);

        if ($path !== null) {
            $jsonPath = $this->convertToJsonPath($path);
            $raw = "JSON_CONTAINS({$quotedColumn}, ?, '{$jsonPath}')";
        } else {
            $raw = "JSON_CONTAINS({$quotedColumn}, ?)";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [$jsonValue],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    /**
     * MySQL-style JSON length where clause helper.
     *
     * Generates JSON_LENGTH(col[, '$.path']) op ?
     *
     * @param   string       $column
     * @param   string       $operator
     * @param   int          $value
     * @param   string|null  $path
     * @param   string       $logical
     * @param   int          $depth
     * @return  void
     */
    protected function setJsonLengthWhereWithMySqlFunctions(
        $column,
        $operator,
        $value,
        $path = null,
        $logical = 'and',
        $depth = 0
    ) {
        $quotedColumn = $this->connection->quoteName($column);

        if ($path !== null) {
            $jsonPath = $this->convertToJsonPath($path);
            $raw = "JSON_LENGTH({$quotedColumn}, '{$jsonPath}') {$operator} ?";
        } else {
            $raw = "JSON_LENGTH({$quotedColumn}) {$operator} ?";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [(int) $value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    /**
     * Converts dot notation path to JSON path syntax
     *
     * Default implementation uses the common $.path.to.key syntax.
     * Override in subclasses if different syntax is needed.
     *
     * @param   string  $path  The dot-notation path
     * @return  string  The JSON path (e.g., "$.user.name")
     */
    protected function convertToJsonPath($path)
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

    /**
     * Converts MySQL date format codes to SQL standard format codes
     *
     * Used by databases that use TO_CHAR() for date formatting (PostgreSQL,
     * Oracle, DB2). MySQL uses its own format codes natively. SQLite
     * overrides with strftime-compatible codes.
     *
     * @param   string  $mysqlFormat  The MySQL format string
     * @return  string  The SQL standard format string
     */
    protected function convertDateFormat(string $mysqlFormat): string
    {
        $conversions = [
            '%Y' => 'YYYY',
            '%y' => 'YY',
            '%m' => 'MM',
            '%d' => 'DD',
            '%H' => 'HH24',
            '%h' => 'HH12',
            '%i' => 'MI',
            '%s' => 'SS',
            '%p' => 'AM',
            '%W' => 'Day',
            '%M' => 'Month',
            '%a' => 'Dy',
            '%b' => 'Mon',
            '%%' => '%',
        ];

        return strtr($mysqlFormat, $conversions);
    }

    // =========================================================================
    // Date/Time Operations (Default implementations using SQL standard EXTRACT)
    // =========================================================================

    /**
     * Sets a date/time extraction where clause
     *
     * Default implementation uses SQL standard EXTRACT() function.
     * Database-specific classes may override for their syntax.
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
        $function = $this->getDateFunction($part);

        $raw = "{$function} {$operator} ?";

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [$value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    /**
     * Get the appropriate date extraction expression for SQL standard
     *
     * Default uses EXTRACT() which is the SQL standard.
     * Override in subclasses for database-specific functions.
     *
     * @param   string  $part  The date part: 'date', 'time', 'year', 'month', 'day'
     * @return  string  The SQL expression
     */
    protected function getDateFunction($part)
    {
        $column = $this->connection->quoteName($this->where[count($this->where) - 1]['column'] ?? 'column');

        $functions = [
            'date'  => "CAST({$column} AS DATE)",
            'time'  => "CAST({$column} AS TIME)",
            'year'  => "EXTRACT(YEAR FROM {$column})",
            'month' => "EXTRACT(MONTH FROM {$column})",
            'day'   => "EXTRACT(DAY FROM {$column})",
        ];

        return $functions[$part] ?? "CAST({$column} AS DATE)";
    }

    /**
     * Resolve date part to simple function name from a map.
     *
     * Used by MySQL-style dialects (e.g. MySQL, CUBRID) that use
     * DATE()/TIME()/YEAR()/MONTH()/DAY() wrappers.
     *
     * @param   string  $part
     * @param   array   $map
     * @param   string  $default
     * @return  string
     */
    protected function getSimpleDateFunction($part, array $map, $default = 'DATE')
    {
        return $map[$part] ?? $default;
    }

    /**
     * Build a date where clause using unary date functions.
     *
     * Generates: FUNCTION(quoted_column) operator ?
     *
     * @param   string  $column
     * @param   string  $operator
     * @param   mixed   $value
     * @param   string  $logical
     * @param   int     $depth
     * @param   string  $function
     * @return  void
     */
    protected function setDateWhereWithUnaryFunction(
        $column,
        $operator,
        $value,
        $logical,
        $depth,
        $function
    ) {
        $quotedColumn = $this->connection->quoteName($column);
        $raw = "{$function}({$quotedColumn}) {$operator} ?";

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [$value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    // =========================================================================
    // Table Operations
    // =========================================================================

    /**
     * Get the SQL statements to truncate a table
     *
     * Default implementation uses standard TRUNCATE TABLE syntax.
     * Override in subclasses if different syntax is needed.
     *
     * @param   string  $table  The table to truncate
     * @return  array   Array of SQL statements to execute
     */
    public function getTruncateStatements($table)
    {
        return ['TRUNCATE TABLE ' . $this->connection->quoteName($table)];
    }

    // =========================================================================
    // Build Methods
    // =========================================================================

    /**
     * Builds the given query element
     *
     * @param   string  $type  The query element to build
     * @return  string|false
     */
    public function build($type)
    {
        // Special case for 'values': allow buildValues() to be called even if $this->values is empty
        // because we might have INSERT ... SELECT instead
        if ($type === 'values') {
            // Call buildValues() if we have either regular values OR INSERT SELECT
            if (!empty($this->values) || $this->insertSelectQuery !== null) {
                return $this->buildValues();
            }
            return false;
        }

        if (empty($this->{$type})) {
            return false;
        }

        $method = 'build' . ucfirst($type);

        return $this->{$method}();
    }

    /**
     * Builds a select statement from the set params
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
                $string = $select['column'];
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
     * Builds an insert statement from the set params
     *
     * Default implementation uses plain INSERT INTO (no IGNORE).
     * Override in subclasses for database-specific INSERT IGNORE syntax.
     *
     * @return  string
     */
    public function buildInsert()
    {
        // Standard SQL INSERT - no IGNORE support
        // Subclasses should override if they support INSERT IGNORE
        return 'INSERT INTO ' . $this->connection->quoteName($this->insert);
    }

    /**
     * Builds an update statement from the set params
     *
     * Standard SQL: UPDATE table SET ... WHERE ...
     * MySQL overrides this to add JOIN support in the UPDATE clause.
     *
     * @return  string
     */
    public function buildUpdate()
    {
        return 'UPDATE ' . $this->connection->quoteName($this->update);
    }

    /**
     * Build MySQL-style UPDATE clause with optional JOIN segment.
     *
     * Used by dialects that support: UPDATE table [JOIN ...]
     *
     * @return  string
     */
    protected function buildUpdateWithJoinClause()
    {
        $sql = 'UPDATE ' . $this->connection->quoteName($this->update);

        if (!empty($this->join)) {
            $sql .= ' ' . $this->buildJoin();
        }

        return $sql;
    }

    /**
     * Builds a delete statement from the set params
     *
     * @return  string
     */
    public function buildDelete()
    {
        return 'DELETE FROM ' . $this->connection->quoteName($this->delete);
    }

    /**
     * Builds a from statement from the set params
     *
     * @return  string
     */
    protected function buildFrom()
    {
        $froms = [];

        foreach ($this->from as $from) {
            // Handle raw from expressions (subqueries)
            if (array_key_exists('raw', $from)) {
                $string = '(' . $from['raw'] . ')';

                // Add bindings from the raw expression
                foreach ($from['bindings'] as $binding) {
                    $this->bind($binding);
                }

                // Alias is required for derived tables in most databases
                if (isset($from['as'])) {
                    $string .= ' AS ' . $this->connection->quoteName($from['as']);
                }
            } else {
                $string = $this->connection->quoteName($from['table']);

                // See if we're including an alias
                if (isset($from['as'])) {
                    $string .= ' AS ' . $this->connection->quoteName($from['as']);
                }
            }

            $froms[] = $string;
        }

        return 'FROM ' . implode(',', $froms);
    }

    /**
     * Builds a join statement from the set params
     *
     * @return  string
     */
    public function buildJoin()
    {
        $joins = [];

        foreach ($this->join as $join) {
            if (isset($join['subquery'])) {
                // Handle subquery joins
                $tableExpr = '(' . $join['subquery'] . ') AS ' . $this->connection->quoteName($join['as']);

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
                // Raw joins are user-provided SQL, don't quote
                $joins[] = strtoupper($join['type']) . ' JOIN ' . $join['table'] . ' ON ' . $join['raw'];
            } elseif (isset($join['conditions'])) {
                // Build table expression with optional alias
                $tableExpr = $this->connection->wrap($join['table']);
                if (!empty($join['alias'])) {
                    $tableExpr .= ' ' . $this->connection->quoteName($join['alias']);
                }

                $joins[] = strtoupper($join['type']) .
                    ' JOIN ' .
                    $tableExpr .
                    ' ON ' .
                    $this->buildJoinConditions($join['conditions']);
            } else {
                // Build table expression with optional alias
                $tableExpr = $this->connection->wrap($join['table']);
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
     * Build ON clause for join conditions
     *
     * @param   array  $conditions
     * @return  string
     */
    protected function buildJoinConditions(array $conditions): string
    {
        $parts = [];

        foreach ($conditions as $index => $condition) {
            $boolean = strtolower($condition['boolean'] ?? 'and');
            $prefix = $index === 0 ? '' : strtoupper($boolean) . ' ';

            if (isset($condition['group'])) {
                $parts[] = $prefix . '(' . $this->buildJoinConditions($condition['group']) . ')';
                continue;
            }

            $leftValue = $condition['left'] ?? null;
            $operator = $condition['operator'] ?? '=';
            $operatorLower = strtolower(trim($operator));
            $left = $this->buildJoinOperand($leftValue);

            if (in_array($operatorLower, ['exists', 'not exists'], true)) {
                $subquery = $condition['right'] ?? null;
                $parts[] = $prefix . $this->buildJoinExistsClause($operatorLower, $subquery);
                continue;
            }

            if (in_array($operatorLower, ['is distinct from', 'is not distinct from'], true)) {
                $rightOperand = $condition['right'] ?? null;
                $isNot = $operatorLower === 'is not distinct from';
                $parts[] = $prefix
                    . $this->buildJoinDistinctFromClause(
                        $leftValue,
                        $rightOperand,
                        $isNot
                    );
                continue;
            }

            if (in_array($operatorLower, ['in', 'not in'], true) && is_array($condition['right'] ?? null)) {
                // Handle empty IN/NOT IN specially to avoid NULL semantics
                if (empty($condition['right'])) {
                    // Empty IN: always FALSE (1 = 0)
                    // Empty NOT IN: always TRUE (1 = 1)
                    $tautology = $operatorLower === 'not in' ? '1 = 1' : '1 = 0';
                    $parts[] = $prefix . $tautology;
                    continue;
                }
                $right = $this->buildJoinInList($condition['right']);
            } elseif (
                in_array($operatorLower, ['between', 'not between'], true)
                && is_array($condition['right'] ?? null)
            ) {
                $right = $this->buildJoinBetweenRange($condition['right']);
            } else {
                $right = $this->buildJoinOperand($condition['right'] ?? null);
            }

            $parts[] = $prefix . $left . ' ' . $operator . ' ' . $right;
        }

        return implode(' ', $parts);
    }

    /**
     * Build a join operand (column, expression, or literal)
     *
     * @param   mixed  $operand
     * @return  string
     */
    protected function buildJoinOperand($operand): string
    {
        if (is_object($operand) && method_exists($operand, 'build')) {
            return $operand->build($this);
        }

        if ($operand === null) {
            return 'NULL';
        }

        // Quote column names (e.g., 'users.id' or 'posts.user_id')
        return $this->connection->quoteName((string) $operand);
    }

    /**
     * Build a list for IN() predicates.
     *
     * @param   array  $values
     * @return  string
     */
    protected function buildJoinInList(array $values): string
    {
        if (empty($values)) {
            return '(' . $this->getEmptySetValue() . ')';
        }

        $parts = [];
        foreach ($values as $value) {
            $parts[] = $this->buildJoinLiteralValue($value);
        }

        return '(' . implode(',', $parts) . ')';
    }

    /**
     * Build a range for BETWEEN predicates.
     *
     * @param   array  $values
     * @return  string
     */
    protected function buildJoinBetweenRange(array $values): string
    {
        if (count($values) !== 2) {
            throw new InvalidArgumentException('BETWEEN requires exactly two values.');
        }

        $start = $this->buildJoinLiteralValue($values[0]);
        $end = $this->buildJoinLiteralValue($values[1]);

        return $start . ' AND ' . $end;
    }

    /**
     * Build a literal value for JOIN predicates.
     *
     * @param   mixed  $value
     * @return  string
     */
    protected function buildJoinLiteralValue($value): string
    {
        if (is_object($value) && method_exists($value, 'build')) {
            return $value->build($this);
        }

        if ($value === null) {
            return 'NULL';
        }

        $this->bind($value);
        return '?';
    }

    /**
     * Build an EXISTS / NOT EXISTS predicate.
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
            throw new InvalidArgumentException('EXISTS join conditions require a subquery expression.');
        }

        if ($sql[0] !== '(') {
            $sql = '(' . $sql . ')';
        }

        return ($operatorLower === 'not exists' ? 'NOT EXISTS ' : 'EXISTS ') . $sql;
    }

    /**
     * Build an IS DISTINCT FROM / IS NOT DISTINCT FROM predicate.
     *
     * @param   string  $left
     * @param   mixed   $right
     * @param   bool    $negated
     * @return  string
     */
    protected function buildJoinDistinctFromClause($left, $right, bool $negated): string
    {
        $rightSql = $this->buildJoinOperand($right);

        if ($negated) {
            $leftEq = $this->buildJoinOperand($left);
            $rightEq = $this->buildJoinOperand($right);
            $leftNull = $this->buildJoinOperand($left);
            $rightNull = $this->buildJoinOperand($right);
            return '(' . $leftEq . ' = ' . $rightEq . ' OR (' . $leftNull . ' IS NULL AND ' . $rightNull . ' IS NULL))';
        }

        $leftNe = $this->buildJoinOperand($left);
        $rightNe = $this->buildJoinOperand($right);
        $leftNull = $this->buildJoinOperand($left);
        $rightNotNull = $this->buildJoinOperand($right);
        $leftNotNull = $this->buildJoinOperand($left);
        $rightNull = $this->buildJoinOperand($right);

        return '(' . $leftNe . ' <> ' . $rightNe
            . ' OR (' . $leftNull . ' IS NULL AND '
            . $rightNotNull . ' IS NOT NULL)'
            . ' OR (' . $leftNotNull . ' IS NOT NULL AND '
            . $rightNull . ' IS NULL))';
    }

    /**
     * Builds a where statement from the set params
     *
     * For non-MySQL drivers, dispatches to buildUpdateJoinWhere() when
     * an UPDATE query has JOINs (converting to FROM-based syntax).
     * MySQL overrides this to skip the dispatch since it handles
     * JOINs directly in buildUpdate().
     *
     * @return  string
     */
    protected function buildWhere()
    {
        if (!empty($this->update) && !empty($this->join)) {
            return $this->buildUpdateJoinWhere();
        }

        return $this->buildWhereClause();
    }

    /**
     * Builds the raw WHERE clause from the set params
     *
     * This is the core WHERE-building logic, separated from buildWhere()
     * so that buildUpdateJoinWhere() can call it without retriggering
     * the UPDATE+JOIN dispatch.
     *
     * @return  string
     */
    protected function buildWhereClause()
    {
        $strings = [];
        $first   = true;
        $depth   = 0;

        foreach ($this->where as $constraint) {
            $string = '';

            if (array_key_exists('resetdepth', $constraint)) {
                $string .= str_repeat(')', ($depth - $constraint['depth']));
            } else {
                $string .= ($constraint['depth'] < $depth) ? ') ' : '';
                $string .= ($first) ? 'WHERE ' : strtoupper($constraint['logical']) . ' ';
                $string .= ($constraint['depth'] > $depth) ? '(' : '';

                // Make sure this isn't a 'raw' where clause
                if (array_key_exists('raw', $constraint)) {
                    $string .= $constraint['raw'];

                    foreach ($constraint['bindings'] as $binding) {
                        $this->bind($binding);
                    }
                } else {
                    // Handle Column (might be an Expression)
                    if (is_object($constraint['column']) && method_exists($constraint['column'], 'build')) {
                        $string .= $constraint['column']->build($this);
                    } else {
                        $string .= $this->connection->quoteName($constraint['column']);
                    }

                    $string .= ' ' . $constraint['operator'];

                    // Handle Value (might be an Expression or array of values)
                    if (is_array($constraint['value'])) {
                        $values = array();
                        foreach ($constraint['value'] as $value) {
                            if (is_object($value) && method_exists($value, 'build')) {
                                $values[] = $value->build($this);
                            } else {
                                $values[] = '?';
                                $this->bind($value);
                            }
                        }
                        $string .= ' (' . ((!empty($values))
                            ? implode(',', $values)
                            : $this->getEmptySetValue()) . ')';
                    } else {
                        if (is_object($constraint['value']) && method_exists($constraint['value'], 'build')) {
                            $string .= ' ' . $constraint['value']->build($this);
                        } else {
                            // Special handling for IS NULL / IS NOT NULL
                            if (
                                is_null($constraint['value'])
                                && in_array(
                                    strtoupper($constraint['operator']),
                                    ['IS', 'IS NOT']
                                )
                            ) {
                                $string .= ' NULL';
                            } else {
                                $string .= ' ?';
                                $this->bind($constraint['value']);
                            }
                        }
                    }
                }
            }

            $strings[] = $string;
            $first     = false;
            $depth     = $constraint['depth'];
        }

        // Catch instance where last item was at a greater depth
        // and never got a closing ')'
        if ($depth > 0) {
            $strings[] = str_repeat(')', $depth);
        }

        return implode("\n", $strings);
    }

    /**
     * Build FROM + WHERE for UPDATE with JOINs (SQL standard)
     *
     * Converts JOIN-based UPDATE syntax into FROM-based syntax used by
     * PostgreSQL, SQL Server, Informix, and other standard SQL databases.
     * Extracts joined tables into a FROM clause and merges join ON
     * conditions into the WHERE clause.
     *
     * MySQL overrides buildWhere() to skip this dispatch entirely.
     *
     * @return  string
     */
    protected function buildUpdateJoinWhere()
    {
        $fromTables = [];
        $joinConditions = [];

        foreach ($this->join as $join) {
            $fromTables[] = $this->connection->quoteName($join['table']);

            if (isset($join['conditions'])) {
                $joinConditions[] = $this->buildJoinConditions(
                    $join['conditions']
                );
            } elseif (isset($join['left']) && isset($join['right'])) {
                $joinConditions[] = $this->connection->quoteName($join['left'])
                    . ' = '
                    . $this->connection->quoteName($join['right']);
            }
        }

        $sql = 'FROM ' . implode(', ', $fromTables);

        // Build the original WHERE conditions (without retriggering dispatch)
        $originalWhere = $this->buildWhereClause();

        // Merge join conditions and original WHERE
        if (!empty($joinConditions) && !empty($originalWhere)) {
            $originalConditions = preg_replace(
                '/^WHERE\s+/i',
                '',
                $originalWhere
            );
            $sql .= "\nWHERE "
                . implode(' AND ', $joinConditions)
                . ' AND ' . $originalConditions;
        } elseif (!empty($joinConditions)) {
            $sql .= "\nWHERE " . implode(' AND ', $joinConditions);
        } elseif (!empty($originalWhere)) {
            $sql .= "\n" . $originalWhere;
        }

        return $sql;
    }

    /**
     * Strip table qualifier from a field name
     *
     * Converts 'table.column' to 'column'. Used in SET clauses where
     * most databases require unqualified column names.
     *
     * @param   string  $field  The field name, possibly table-qualified
     * @return  string  The unqualified field name
     */
    protected function stripTableQualifier(string $field): string
    {
        if (strpos($field, '.') !== false) {
            return substr($field, strrpos($field, '.') + 1);
        }
        return $field;
    }

    /**
     * Builds a set statement from the set params
     *
     * Strips table qualifiers from field names since most databases
     * require unqualified column names in SET clauses.
     *
     * @return  string
     */
    public function buildSet()
    {
        $updates = [];

        foreach ($this->set as $field => $value) {
            $field = $this->stripTableQualifier($field);

            if (is_object($value)) {
                $updates[] = $this->connection->quoteName($field)
                    . ' = ' . $value->build($this);
            } else {
                $updates[] = $this->connection->quoteName($field) . ' = ?';
                $this->bind(is_string($value) ? trim($value) : $value);
            }
        }

        return 'SET ' . implode(',', $updates);
    }

    /**
     * Builds a values statement from the set params
     *
     * Handles both standard INSERT VALUES and INSERT ... SELECT patterns.
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

        return '(' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
    }

    /**
     * Builds a group statement from the set params
     *
     * @return  string
     */
    public function buildGroup()
    {
        return 'GROUP BY ' . implode(',', $this->group);
    }

    /**
     * Builds a having statement from the set params
     *
     * Supports both structured having clauses (column, operator, value)
     * and raw having clauses (raw SQL with bindings).
     *
     * @return  string
     */
    public function buildHaving()
    {
        $havings = [];
        $first = true;

        foreach ($this->having as $having) {
            // Determine the logical operator (AND/OR)
            $logical = isset($having['logical']) ? strtoupper($having['logical']) : 'AND';
            $prefix = $first ? '' : ' ' . $logical . ' ';

            if (isset($having['raw'])) {
                // Raw having clause
                $havings[] = $prefix . $having['raw'];

                // Bind any parameters
                foreach ($having['bindings'] as $binding) {
                    $this->bind($binding);
                }
            } else {
                // Structured having clause
                $havings[] = $prefix . $having['column'] . ' ' . $having['operator'] . ' ?';
                $this->bind(is_string($having['value']) ? trim($having['value']) : $having['value']);
            }

            $first = false;
        }

        return 'HAVING ' . implode('', $havings);
    }

    /**
     * Builds a limit statement from the set params
     *
     * Default implementation uses SQL:2008 standard OFFSET/FETCH syntax.
     * Override in subclasses for database-specific syntax (e.g., MySQL LIMIT).
     *
     * @return  string
     */
    public function buildLimit()
    {
        $string = '';

        // SQL:2008 standard: OFFSET n ROWS FETCH FIRST n ROWS ONLY
        if (!empty($this->start)) {
            $string .= 'OFFSET ' . (int) $this->start . ' ROWS ';
        }

        if (!empty($this->limit)) {
            $string .= 'FETCH FIRST ' . (int) $this->limit . ' ROWS ONLY';
        }

        return trim($string);
    }

    /**
     * Builds an order statement from the set params
     *
     * @return  string
     */
    public function buildOrder()
    {
        $orders = [];

        foreach ($this->order as $order) {
            if (is_object($order['column'])) {
                $string = $order['column']->build($this);
            } else {
                $string = $this->connection->quoteName($order['column']);
            }

            $string .= ' ' . strtoupper($order['dir']);
            $orders[] = $string;
        }

        return 'ORDER BY ' . implode(',', $orders);
    }

    /**
     * Builds the UNION clause(s)
     *
     * @return  string
     */
    protected function buildUnion()
    {
        if (empty($this->union)) {
            return '';
        }

        $unions = [];

        foreach ($this->union as $union) {
            // UNION or UNION ALL
            $keyword = $union['all'] ? 'UNION ALL' : 'UNION';

            // Add the bindings from the union query
            foreach ($union['bindings'] as $binding) {
                $this->bind($binding);
            }

            $unions[] = $keyword . "\n" . $union['query'];
        }

        return implode("\n", $unions);
    }

    /**
     * Builds an upsert statement from the set params
     *
     * This is database-specific and must be implemented by subclasses.
     * Different databases use different syntax:
     * - MySQL: ON DUPLICATE KEY UPDATE
     * - PostgreSQL/SQLite: ON CONFLICT DO UPDATE
     * - SQL Server/Oracle/DB2: MERGE
     *
     * @return  string
     */
    abstract public function buildUpsert();

    /**
     * Builds a bulk insert statement from the set params
     *
     * Default implementation uses multi-row VALUES syntax which is
     * supported by most databases. Override in subclasses if needed.
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

        return sprintf(
            'INSERT INTO %s (%s) VALUES %s',
            $this->connection->quoteName($this->insertManyTable),
            implode(', ', $quotedColumns),
            implode(', ', $valueSets)
        );
    }

    // =========================================================================
    // Column Introspection (Abstract - must be implemented by subclasses)
    // =========================================================================

    /**
     * Returns the proper query for generating a list of table columns
     *
     * Each database has different system catalogs for introspection:
     * - MySQL: SHOW FULL COLUMNS FROM
     * - PostgreSQL/SQL Server: information_schema
     * - SQLite: PRAGMA table_info
     * - Oracle: USER_TAB_COLUMNS
     * - DB2: SYSCAT.COLUMNS
     * - Firebird: RDB$ system tables
     *
     * @param   string  $table  The name of the database table
     * @return  string
     */
    abstract public function getColumnsQuery($table);

    /**
     * Normalizes the results of the column introspection query
     *
     * Each database returns column metadata in a different format.
     * This method normalizes it to a consistent structure.
     *
     * @param   array  $data      The raw column data
     * @param   bool   $typeOnly  True (default) to only return field types
     * @return  array
     */
    abstract public function normalizeColumns($data, $typeOnly = true);

    // =========================================================================
    // Expression Builder Support
    // =========================================================================

    /**
     * Build an Expression object into database-specific SQL
     *
     * This method handles the rendering of Expression objects into SQL
     * strings. Other database syntaxes can override specific functions
     * where the syntax differs.
     *
     * @param   \Hubzero\Database\Expression  $expression  The expression to build
     * @return  string  The SQL representation
     */
    public function buildExpression($expression): string
    {
        $type = $expression->getType();

        switch ($type) {
            case \Hubzero\Database\Expression::TYPE_RAW:
                return $expression->getArguments()[0];

            case \Hubzero\Database\Expression::TYPE_COLUMN:
                return $this->connection->quoteName($expression->getArguments()[0]);

            case \Hubzero\Database\Expression::TYPE_LITERAL:
                $this->bind($expression->getArguments()[0]);
                return '?';

            case \Hubzero\Database\Expression::TYPE_FUNCTION:
                return $this->buildFunctionExpression($expression);

            case \Hubzero\Database\Expression::TYPE_OPERATOR:
                return $this->buildOperatorExpression($expression);

            default:
                return '';
        }
    }

    /**
     * Build an operator expression (binary math operators)
     *
     * @param   \Hubzero\Database\Expression  $expression  The operator expression
     * @return  string  The SQL representation
     */
    protected function buildOperatorExpression($expression): string
    {
        $operator = $expression->getFunction();
        $args = $expression->getArguments();

        $left = $this->buildExpressionArgument($args[0]);
        $right = $this->buildExpressionArgument($args[1]);

        // Sequential evaluation uses parentheses to enforce order
        if ($operator === 'MOD') {
            return $this->buildFunctionExpression($expression);
        }

        return "({$left} {$operator} {$right})";
    }

    /**
     * Build a function expression into database-specific SQL
     *
     * Default implementation uses standard SQL function names.
     * Override in subclasses for database-specific functions.
     *
     * @param   \Hubzero\Database\Expression  $expression  The function expression
     * @return  string  The SQL representation
     */
    protected function buildFunctionExpression($expression): string
    {
        $function = $expression->getFunction();
        $args = $expression->getArguments();

        switch ($function) {
            // Aggregate functions (standard SQL)
            case 'SUM':
            case 'AVG':
            case 'MIN':
            case 'MAX':
            case 'COUNT':
                return $function . '(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'COUNT_DISTINCT':
                return 'COUNT(DISTINCT ' . $this->buildExpressionArgument($args[0]) . ')';

            // String functions
            case 'CONCAT':
                $parts = array_map([$this, 'buildExpressionArgument'], $args);
                return 'CONCAT(' . implode(', ', $parts) . ')';

            case 'UPPER':
            case 'LOWER':
            case 'TRIM':
                return $function . '(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'LENGTH':
                return 'LENGTH(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'SUBSTRING':
                $col = $this->buildExpressionArgument($args[0]);
                $start = (int) $args[1];
                if ($args[2] !== null) {
                    return "SUBSTRING({$col} FROM {$start} FOR " . (int) $args[2] . ')';
                }
                return "SUBSTRING({$col} FROM {$start})";

            case 'REPLACE':
                $col = $this->buildExpressionArgument($args[0]);

                $search = $args[1];
                if ($search instanceof \Hubzero\Database\Expression) {
                    $search = $this->buildExpression($search);
                } else {
                    $this->bind($search);
                    $search = '?';
                }

                $replace = $args[2];
                if ($replace instanceof \Hubzero\Database\Expression) {
                    $replace = $this->buildExpression($replace);
                } else {
                    $this->bind($replace);
                    $replace = '?';
                }

                return "REPLACE({$col}, {$search}, {$replace})";

            // Conditional functions (standard SQL)
            case 'COALESCE':
                $parts = array_map([$this, 'buildExpressionArgument'], $args);
                return 'COALESCE(' . implode(', ', $parts) . ')';

            case 'IFNULL':
                // Standard SQL uses COALESCE for this
                return 'COALESCE('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ')';

            case 'NULLIF':
                return 'NULLIF('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ')';

            case 'CASE':
                return $this->buildCaseExpression($args[0], $args[1], $args[2] ?? null);

            // Math functions (standard SQL)
            case 'ROUND':
                return 'ROUND(' . $this->buildExpressionArgument($args[0]) . ', ' . (int) $args[1] . ')';

            case 'FLOOR':
            case 'CEIL':
            case 'ABS':
                return $function . '(' . $this->buildExpressionArgument($args[0]) . ')';

            // Date/time functions (standard SQL)
            case 'NOW':
                return 'CURRENT_TIMESTAMP';

            case 'CURRENT_TIMESTAMP':
                return 'CURRENT_TIMESTAMP';

            case 'CURRENT_DATE':
                return 'CURRENT_DATE';

            case 'CURRENT_TIME':
                return 'CURRENT_TIME';

            case 'DATE':
            case 'TIME':
                return "CAST(" . $this->buildExpressionArgument($args[0]) . " AS " . $function . ")";

            case 'YEAR':
            case 'MONTH':
            case 'DAY':
            case 'HOUR':
            case 'MINUTE':
            case 'SECOND':
                return "EXTRACT({$function} FROM " . $this->buildExpressionArgument($args[0]) . ')';

            case 'DATE_FORMAT':
                // No standard SQL equivalent - subclasses should override
                throw new UnsupportedSyntaxException(
                    'DATE_FORMAT is not standard SQL. Use a database-specific syntax class.'
                );

            // Sequence functions
            // Base implementation resolves via driver API. This works for
            // all backends: emulated (MySQL/SQLite) use their table-based
            // implementation; native backends get a literal value. Per-driver
            // Syntax overrides emit native inline SQL for better performance.
            case 'NEXTVAL':
                return (string) $this->connection->nextSequenceValue($args[0]);

            case 'CURRVAL':
                return (string) $this->connection->currentSequenceValue($args[0]);

            default:
                // Fallback: just output as generic function call
                $parts = array_map([$this, 'buildExpressionArgument'], $args);
                return $function . '(' . implode(', ', $parts) . ')';
        }
    }

    /**
     * Build an expression argument (handles nested expressions and columns)
     *
     * @param   mixed  $arg  The argument (Expression, column name, or literal)
     * @return  string
     */
    protected function buildExpressionArgument($arg): string
    {
        if ($arg instanceof \Hubzero\Database\Expression) {
            return $this->buildExpression($arg);
        }

        // Treat string as column name (not quoted if it contains special chars like *)
        if (is_string($arg)) {
            if ($arg === '*' || strpos($arg, '(') !== false || strpos($arg, ' ') !== false) {
                return $arg;
            }
            return $this->connection->quoteName($arg);
        }

        // Numeric literals
        if (is_numeric($arg)) {
            return (string) $arg;
        }

        // Null
        if ($arg === null) {
            return 'NULL';
        }

        // Other values - bind them
        $this->bind($arg);
        return '?';
    }

    /**
     * Build a CASE expression
     *
     * @param   mixed       $column   The column to switch on
     * @param   array       $cases    Key-value pairs of value => result
     * @param   mixed|null  $default  Default value (ELSE clause)
     * @return  string
     */
    protected function buildCaseExpression($column, array $cases, $default = null): string
    {
        $sql = 'CASE ' . $this->buildExpressionArgument($column);

        foreach ($cases as $when => $then) {
            $this->bind($when);
            $sql .= ' WHEN ? THEN ' . $this->buildExpressionArgument($then);
        }

        if ($default !== null) {
            $sql .= ' ELSE ' . $this->buildExpressionArgument($default);
        }

        $sql .= ' END';

        return $sql;
    }

    /**
     * Get the value to use for an empty set in an IN clause
     *
     * @return  string
     */
    protected function getEmptySetValue()
    {
        return "''";
    }

    /**
     * Build a CONCAT expression
     *
     * Default uses the SQL standard || operator, supported by most
     * databases (PostgreSQL, SQLite, Firebird, Oracle, DB2, Informix).
     * MySQL and SQL Server override with CONCAT() function.
     *
     * @param   array  $parts  Array of column names or quoted strings
     * @return  string
     */
    public function buildConcat(array $parts)
    {
        return implode(' || ', $parts);
    }

    /**
     * Build CONCAT(...) function call for function-based concatenation dialects.
     *
     * @param   array  $parts
     * @return  string
     */
    protected function buildConcatFunction(array $parts)
    {
        return 'CONCAT(' . implode(', ', $parts) . ')';
    }

    /**
     * Build MySQL-style LIMIT clause (LIMIT offset,count).
     *
     * @param   int|string  $maxWhenNoLimit
     * @return  string
     */
    protected function buildMySqlStyleLimitClause($maxWhenNoLimit = '18446744073709551615'): string
    {
        $string  = 'LIMIT ';
        $string .= ((!empty($this->start)) ? (int) $this->start . ',' : '');
        $string .= ((!empty($this->limit)) ? (int) $this->limit : (string) $maxWhenNoLimit);

        return $string;
    }

    /**
     * Check whether clause emission should be suppressed during FULL JOIN UNION emulation.
     *
     * Used by MySQL-style FULL JOIN emulation implementations that emit a complete
     * UNION query in buildSelect() and therefore must suppress individual clause
     * builders to avoid duplicated SQL fragments.
     *
     * @return  bool
     */
    protected function isBuildingFullJoinUnionQuery(): bool
    {
        return property_exists($this, 'fullJoinUnionBuilt') && !empty($this->fullJoinUnionBuilt);
    }

    /**
     * Normalize SHOW COLUMNS style metadata into framework column format.
     *
     * @param   array  $data
     * @param   bool   $typeOnly
     * @return  array
     */
    protected function normalizeShowColumnsMetadata(array $data, $typeOnly = true): array
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
                    'allownull' => (($field->Null ?? 'YES') === 'NO') ? false : true,
                    'default'   => $field->Default ?? null,
                    'pk'        => (($field->Key ?? '') === 'PRI') ? true : false,
                ];
            }
        }

        return $results;
    }

    /**
     * Build SHOW COLUMNS style introspection query.
     *
     * @param   string  $table
     * @param   bool    $full
     * @return  string
     */
    protected function buildShowColumnsQuery($table, bool $full = false): string
    {
        $prefix = $full ? 'SHOW FULL COLUMNS FROM ' : 'SHOW COLUMNS FROM ';
        return $prefix . $this->connection->quoteName($table);
    }

    /**
     * Build a REPLACE expression
     *
     * REPLACE() function is supported across MySQL, PostgreSQL, and SQLite.
     *
     * @param   string  $column   The column name
     * @param   string  $search   The search string (quoted SQL literal or unquoted value)
     * @param   string  $replace  The replacement string (quoted SQL literal or unquoted value)
     * @return  string
     */
    public function buildReplace($column, $search, $replace)
    {
        // If the string is already a quoted SQL literal (starts and ends with '), don't quote again
        $quotedSearch = $this->quoteIfNeeded($search);
        $quotedReplace = $this->quoteIfNeeded($replace);

        return 'REPLACE(' . $this->connection->quoteName($column) . ', ' . $quotedSearch . ', ' . $quotedReplace . ')';
    }

    /**
     * Quote a value if it's not already a quoted SQL literal
     *
     * @param   string  $value  The value to quote
     * @return  string  The quoted value
     */
    protected function quoteIfNeeded($value)
    {
        // If it's already a quoted SQL literal (starts and ends with single quotes), return as-is
        if (preg_match("/^'.*'$/", $value)) {
            return $value;
        }

        // Otherwise, quote it properly
        return $this->connection->quote($value);
    }

    /**
     * Build a LOWER expression
     *
     * LOWER() function is supported across all databases.
     *
     * @param   string  $column  The column name
     * @return  string
     */
    public function buildLower($column)
    {
        return 'LOWER(' . $this->connection->quoteName($column) . ')';
    }

    /**
     * Build an UPPER expression
     *
     * UPPER() function is supported across all databases.
     *
     * @param   string  $column  The column name
     * @return  string
     */
    public function buildUpper($column)
    {
        return 'UPPER(' . $this->connection->quoteName($column) . ')';
    }
}
