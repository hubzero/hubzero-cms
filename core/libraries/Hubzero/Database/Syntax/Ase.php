<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Syntax;

/**
 * Database SAP ASE (Sybase) query syntax class
 *
 * SAP Adaptive Server Enterprise (formerly Sybase ASE) uses Transact-SQL.
 * While related to SQL Server's T-SQL, ASE has significant differences:
 *
 * ASE-specific limitations and features:
 * - TOP n for simple limits (no OFFSET/FETCH, no TOP...START AT)
 * - No ROW_NUMBER() window functions
 * - No multi-row INSERT VALUES - uses INSERT...SELECT...UNION ALL
 * - No MERGE statement for upserts
 * - No INFORMATION_SCHEMA - uses sysobjects/syscolumns/systypes
 * - STR_REPLACE() instead of REPLACE()
 * - CONVERT() instead of FORMAT() for date formatting
 * - ALTER TABLE MODIFY instead of ALTER TABLE ALTER COLUMN
 * - Square brackets [column] for identifier quoting
 * - IDENTITY columns for auto-increment
 * - BIT type cannot be NULL
 *
 * Pagination: Only TOP n is supported. OFFSET pagination is not available
 * in ASE 16.x. When offset is requested, TOP (offset+limit) is used and
 * the offset rows are skipped by the driver.
 */
class Ase extends Sql
{
    /**
     * Whether we are emitting a FULL OUTER JOIN emulation query
     *
     * @var bool
     */
    protected $fullJoinUnionBuilt = false;

    /**
     * ASE reserved words that must be quoted when used as aliases
     *
     * @var array
     */
    protected static $reservedWords = [
        'count', 'sum', 'avg', 'min', 'max', 'value', 'position', 'row',
        'rows', 'first', 'order', 'by', 'group', 'having',
        'where', 'from', 'select', 'insert', 'update', 'delete',
        'create', 'alter', 'drop', 'table', 'index', 'view',
        'user', 'password', 'type', 'current_time', 'current_date',
        'current_timestamp', 'timestamp', 'date', 'time', 'year',
        'month', 'day', 'hour', 'minute', 'second', 'key', 'level',
        'status', 'name', 'replace',
    ];

    /**
     * Builds a limit statement from the set params
     *
     * ASE uses TOP in the SELECT clause, not LIMIT. Return empty here;
     * pagination is handled by buildSelect() injecting TOP.
     *
     * @return  string
     */
    public function buildLimit()
    {
        return '';
    }

    /**
     * Builds a join statement — suppressed during FULL JOIN emulation
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
     * Builds a from statement — suppressed during FULL JOIN emulation
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
     * Builds a where statement — suppressed during FULL JOIN emulation
     *
     * @return  string
     */
    protected function buildWhere()
    {
        if ($this->isBuildingFullJoinUnionQuery()) {
            return '';
        }

        return parent::buildWhere();
    }

    /**
     * Builds a group statement — suppressed during FULL JOIN emulation
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
     * Builds a having statement — suppressed during FULL JOIN emulation
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
     * Builds an order statement — suppressed during FULL JOIN emulation
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
     * Quote an alias if it is an ASE reserved word
     *
     * @param   string  $alias  The alias name
     * @return  string
     */
    protected function quoteAlias(string $alias): string
    {
        if (in_array(strtolower($alias), static::$reservedWords)) {
            return '[' . $alias . ']';
        }
        return $alias;
    }

    /**
     * Builds a select statement from the set params
     *
     * Override to inject TOP clause for pagination and quote reserved
     * word aliases (e.g., 'count').
     *
     * @return  string
     */
    protected function buildSelect()
    {
        // FULL JOIN emulation: ASE doesn't support FULL OUTER JOIN.
        // Rewrite as LEFT JOIN UNION ALL RIGHT JOIN (same pattern as MySQL).
        if ($this->hasFullJoin()) {
            return $this->buildFullJoinEmulationSelect();
        }

        return $this->buildAseSelect();
    }

    /**
     * Core ASE SELECT builder (called for non-FULL-JOIN queries and
     * for individual branches of FULL JOIN emulation)
     *
     * @return  string
     */
    protected function buildAseSelect()
    {
        $selects = [];

        foreach ($this->select as $select) {
            if (array_key_exists('raw', $select)) {
                $string = '(' . $select['raw'] . ')';
                foreach ($select['bindings'] as $binding) {
                    $this->bind($binding);
                }
            } elseif ($select['column'] instanceof \Hubzero\Database\Expression) {
                $string = $this->buildExpression($select['column']);
            } elseif (isset($select['count']) && $select['count'] === 'distinct') {
                $quoted = $this->connection->quoteName($select['column']);
                $string = "COUNT(DISTINCT({$quoted}))";
            } elseif (!empty($select['count'])) {
                $quoted = $this->connection->quoteName($select['column']);
                $string = "COUNT({$quoted})";
            } else {
                $string = $select['column'];
            }

            if (isset($select['as']) && $select['as'] !== null) {
                $string .= " AS " . $this->quoteAlias($select['as']);
            }

            $selects[] = $string;
        }

        // Handle TOP for pagination
        // ASE has no OFFSET - when offset is set, fetch (offset+limit) rows
        // and the caller must skip the first offset rows
        $top = '';
        $offset = 0;
        $clientLimit = null;
        if (!empty($this->limit)) {
            if (!empty($this->union)) {
                // UNION queries: TOP only applies to the first SELECT, not
                // the combined result. Use client-side limit/offset instead.
                $clientLimit = (int) $this->limit;
                if (!empty($this->start)) {
                    $offset = (int) $this->start;
                }
            } elseif (!empty($this->start)) {
                $offset = (int) $this->start;
                $top = 'TOP ' . ($offset + (int) $this->limit) . ' ';
            } else {
                $top = 'TOP ' . (int) $this->limit . ' ';
            }
        }

        // Tell the driver to skip offset rows and/or limit results client-side
        if ($this->connection instanceof \Hubzero\Database\Driver\Ase) {
            $this->connection->setPendingOffset($offset);
            $this->connection->setPendingLimit($clientLimit);
        }

        $distinct = $this->distinct ? 'DISTINCT ' : '';
        return 'SELECT ' . $distinct . $top . implode(',', $selects);
    }

    /**
     * Build FULL JOIN emulation using LEFT JOIN UNION ALL RIGHT JOIN
     *
     * ASE doesn't support FULL OUTER JOIN natively. This uses the same
     * expandFullJoinBranches() framework as MySQL to decompose into
     * LEFT + RIGHT branches combined with UNION ALL.
     *
     * @return  string
     */
    protected function buildFullJoinEmulationSelect()
    {
        $this->fullJoinUnionBuilt = true;

        $branches = $this->expandFullJoinBranches();
        if (empty($branches)) {
            $this->fullJoinUnionBuilt = false;
            return $this->buildAseSelect();
        }

        $originalJoins = $this->join;
        $sqlParts = [];
        $allBindings = [];

        foreach ($branches as $branch) {
            $this->join = $branch['joins'];
            $this->clearBindings();

            $leftWhere = !empty($this->where) ? parent::buildWhere() : '';
            $whereSql = $this->appendFiltersToWhere($leftWhere, $branch['filters']);

            $selectSql = $this->buildAseSelect();
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

        // For FULL JOIN + LIMIT, use client-side pagination
        if (!empty($this->limit) && $this->connection instanceof \Hubzero\Database\Driver\Ase) {
            $offset = !empty($this->start) ? (int) $this->start : 0;
            $this->connection->setPendingOffset($offset);
            $this->connection->setPendingLimit((int) $this->limit);
        }

        return $sql;
    }

    /**
     * Returns the proper query for generating a list of table columns
     *
     * ASE uses syscolumns joined with systypes for column introspection.
     *
     * @param   string  $table  The name of the database table
     * @return  string
     */
    public function getColumnsQuery($table)
    {
        return "SELECT "
            . "c.name AS Field, "
            . "t.name AS Type, "
            . "c.length AS Length, "
            . "c.prec AS Prec, "
            . "c.scale AS Scale, "
            . "CASE WHEN c.status & 8 = 8 THEN 'YES' ELSE 'NO' END AS [Null], "
            . "CASE WHEN c.status & 128 = 128 THEN 'YES' ELSE 'NO' END AS [Identity], "
            . "CASE WHEN EXISTS("
            . "  SELECT 1 FROM sysindexes i WHERE i.id = c.id "
            . "  AND i.status & 2048 = 2048 "
            . "  AND index_col(object_name(c.id), i.indid, 1) = c.name"
            . ") THEN 'PRI' ELSE '' END AS [Key] "
            . "FROM syscolumns c "
            . "JOIN systypes t ON c.usertype = t.usertype "
            . "WHERE c.id = object_id(" . $this->connection->quote($table) . ") "
            . "ORDER BY c.colid";
    }

    /**
     * Normalizes the results of the columns query
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
                $results[$field->Field] = $field->Type;
            }
        } else {
            foreach ($data as $field) {
                $results[$field->Field] = [
                    'name'      => $field->Field,
                    'type'      => $field->Type,
                    'length'    => $field->Length,
                    'precision' => $field->Prec,
                    'scale'     => $field->Scale,
                    'allownull' => ($field->Null === 'YES'),
                    'identity'  => (isset($field->Identity) && $field->Identity === 'YES'),
                    'pk'        => (isset($field->Key) && $field->Key === 'PRI'),
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
        return "SELECT 1 FROM sysobjects WHERE name = "
            . $this->connection->quote($table)
            . " AND type = 'U'";
    }

    /**
     * Gets the query for listing all tables
     *
     * @return  string
     */
    public function getTableListQuery()
    {
        return "SELECT name FROM sysobjects WHERE type = 'U' ORDER BY name";
    }

    /**
     * Get the SQL statements to truncate a table
     *
     * @param   string  $table  The table to truncate
     * @return  array   Array of SQL statements to execute
     */
    public function getTruncateStatements($table)
    {
        return ['TRUNCATE TABLE ' . $this->connection->quoteName($table)];
    }

    /**
     * Build a CONCAT expression for ASE
     *
     * ASE supports the + operator for string concatenation.
     * ISNULL() wraps each part to handle NULLs.
     *
     * @param   array  $parts  Array of column names or quoted strings
     * @return  string
     */
    public function buildConcat(array $parts)
    {
        $wrapped = array_map(fn($p) => "ISNULL($p, '')", $parts);
        return '(' . implode(' + ', $wrapped) . ')';
    }

    /**
     * Builds an upsert statement
     *
     * ASE doesn't have MERGE. Use DELETE + INSERT pattern.
     * Requires conflict columns to identify existing rows.
     *
     * @return  string
     */
    public function buildUpsert()
    {
        $columns = array_keys($this->upsertValues);
        $conflictColumns = !empty($this->upsertConflictColumns)
            ? $this->upsertConflictColumns
            : [$columns[0]];

        $table = $this->connection->quoteName($this->upsertTable);

        // Determine which columns to update on conflict
        $updateColumns = !empty($this->upsertUpdateColumns)
            ? $this->upsertUpdateColumns
            : $columns;

        // Use IF EXISTS ... UPDATE ... ELSE INSERT pattern
        // This supports selective column updates

        // 1) Bind WHERE clause for IF EXISTS check
        $whereConditions = [];
        foreach ($conflictColumns as $col) {
            $quotedCol = $this->connection->quoteName($col);
            $whereConditions[] = "$quotedCol = ?";
            $this->bind(
                is_string($this->upsertValues[$col])
                    ? trim($this->upsertValues[$col])
                    : $this->upsertValues[$col]
            );
        }
        $whereClause = implode(' AND ', $whereConditions);

        // 2) Bind UPDATE SET values (only updateColumns, excluding conflict columns)
        $setClauses = [];
        foreach ($updateColumns as $col) {
            if (in_array($col, $conflictColumns)) {
                continue;
            }
            $setClauses[] = $this->connection->quoteName($col) . " = ?";
            $this->bind(
                is_string($this->upsertValues[$col])
                    ? trim($this->upsertValues[$col])
                    : $this->upsertValues[$col]
            );
        }

        // 3) Bind UPDATE WHERE clause
        foreach ($conflictColumns as $col) {
            $this->bind(
                is_string($this->upsertValues[$col])
                    ? trim($this->upsertValues[$col])
                    : $this->upsertValues[$col]
            );
        }

        // 4) Bind INSERT VALUES
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

        if (empty($setClauses)) {
            // All columns are conflict columns — just do INSERT if not exists
            $sql = "IF NOT EXISTS (SELECT 1 FROM $table WHERE $whereClause) ";
            $sql .= "INSERT INTO $table (" . implode(', ', $quotedColumns) . ") ";
            $sql .= "VALUES (" . implode(', ', $placeholders) . ")";
        } else {
            $sql = "IF EXISTS (SELECT 1 FROM $table WHERE $whereClause) ";
            $sql .= "UPDATE $table SET " . implode(', ', $setClauses);
            $sql .= " WHERE $whereClause";
            $sql .= " ELSE ";
            $sql .= "INSERT INTO $table (" . implode(', ', $quotedColumns) . ") ";
            $sql .= "VALUES (" . implode(', ', $placeholders) . ")";
        }

        return $sql;
    }

    /**
     * ASE has no native INSERT IGNORE syntax.
     *
     * @return bool
     */
    public function needsRowByRowInsertIgnore(): bool
    {
        return $this->ignore && $this->insertSelectQuery !== null;
    }

    /**
     * Build a function expression into ASE-specific SQL
     *
     * @param   \Hubzero\Database\Expression  $expression  The function expression
     * @return  string
     */
    protected function buildFunctionExpression($expression): string
    {
        $function = $expression->getFunction();
        $args = $expression->getArguments();

        switch ($function) {
            case 'MOD':
                return '(' . $this->buildExpressionArgument($args[0])
                    . ' % ' . $this->buildExpressionArgument($args[1]) . ')';

            case 'IFNULL':
            case 'COALESCE':
                return 'ISNULL(' . $this->buildExpressionArgument($args[0])
                    . ', ' . $this->buildExpressionArgument($args[1]) . ')';

            case 'LENGTH':
            case 'CHAR_LENGTH':
                return 'CHAR_LENGTH(' . $this->buildExpressionArgument($args[0]) . ')';

            case 'SUBSTRING':
                return 'SUBSTRING('
                    . $this->buildExpressionArgument($args[0]) . ', '
                    . $this->buildExpressionArgument($args[1]) . ', '
                    . $this->buildExpressionArgument($args[2]) . ')';

            case 'CONCAT':
                $parts = array_map(
                    fn($a) => 'ISNULL(' . $this->buildExpressionArgument($a) . ", '')",
                    $args
                );
                return '(' . implode(' + ', $parts) . ')';

            case 'NOW':
            case 'CURRENT_TIMESTAMP':
                return 'GETDATE()';

            case 'DATE':
                return 'CONVERT(DATE, '
                    . $this->buildExpressionArgument($args[0]) . ')';

            case 'YEAR':
            case 'MONTH':
            case 'DAY':
            case 'HOUR':
            case 'MINUTE':
            case 'SECOND':
                return 'DATEPART(' . $function . ', '
                    . $this->buildExpressionArgument($args[0]) . ')';

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
                return "STR_REPLACE({$col}, {$search}, {$replace})";

            case 'CEIL':
                return 'CEILING('
                    . $this->buildExpressionArgument($args[0]) . ')';

            default:
                return parent::buildFunctionExpression($expression);
        }
    }

    /**
     * Build a REPLACE expression
     *
     * ASE uses STR_REPLACE() instead of REPLACE().
     *
     * @param   string  $column   The column name
     * @param   string  $search   The search string
     * @param   string  $replace  The replacement string
     * @return  string
     */
    public function buildReplace($column, $search, $replace)
    {
        $quotedSearch = $this->quoteIfNeeded($search);
        $quotedReplace = $this->quoteIfNeeded($replace);

        return 'STR_REPLACE(' . $this->connection->quoteName($column) . ', ' . $quotedSearch . ', ' . $quotedReplace . ')';
    }

    /**
     * Sets a date/time extraction where clause
     *
     * ASE uses DATEPART() for date extraction.
     *
     * @param   string  $column    The datetime column name
     * @param   string  $operator  Comparison operator
     * @param   mixed   $value     The value to compare against
     * @param   string  $part      The date part
     * @param   string  $logical   The operator between multiple clauses
     * @param   int     $depth     The depth level of the clause
     * @return  void
     */
    public function setDateWhere($column, $operator, $value, $part, $logical = 'and', $depth = 0)
    {
        $quotedColumn = $this->connection->quoteName($column);

        switch ($part) {
            case 'date':
                $raw = "CONVERT(DATE, {$quotedColumn}) {$operator} ?";
                break;
            case 'year':
                $raw = "DATEPART(yy, {$quotedColumn}) {$operator} ?";
                break;
            case 'month':
                $raw = "DATEPART(mm, {$quotedColumn}) {$operator} ?";
                break;
            case 'day':
                $raw = "DATEPART(dd, {$quotedColumn}) {$operator} ?";
                break;
            default:
                $raw = "CONVERT(DATE, {$quotedColumn}) {$operator} ?";
        }

        $this->where[] = [
            'raw'      => $raw,
            'bindings' => [$value],
            'logical'  => $logical,
            'depth'    => $depth
        ];
    }

    /**
     * Builds a multi-row insert statement
     *
     * ASE does not support multi-row VALUES. Uses INSERT...SELECT...UNION ALL.
     *
     * @return  string
     */
    public function buildInsertMany()
    {
        if (empty($this->insertManyRows) || empty($this->insertManyTable)) {
            return parent::buildInsertMany();
        }

        $columns = array_keys($this->insertManyRows[0]);

        // ASE's IDENTITY_INSERT doesn't work with INSERT...SELECT...UNION ALL.
        // If the identity column is among the columns being inserted, fall back
        // to individual inserts (return empty to trigger the fallback path).
        if ($this->connection instanceof \Hubzero\Database\Driver\Ase) {
            $identityCol = $this->connection->getIdentityColumn($this->insertManyTable);
            if ($identityCol && in_array($identityCol, $columns, true)) {
                return '';
            }
            // Also check case-insensitive
            if ($identityCol) {
                $identityLower = strtolower($identityCol);
                foreach ($columns as $col) {
                    if (strtolower($col) === $identityLower) {
                        return '';
                    }
                }
            }
        }

        $quotedColumns = array_map(
            fn($col) => $this->connection->quoteName($col),
            $columns
        );

        $selects = [];
        foreach ($this->insertManyRows as $row) {
            $placeholders = [];
            foreach ($columns as $col) {
                $placeholders[] = '?';
                $value = $row[$col] ?? null;
                $this->bind(is_string($value) ? trim($value) : $value);
            }
            $selects[] = 'SELECT ' . implode(', ', $placeholders);
        }

        $table = $this->connection->quoteName($this->insertManyTable);
        $columnList = implode(', ', $quotedColumns);

        return "INSERT INTO $table ($columnList) "
            . implode(' UNION ALL ', $selects);
    }
}
