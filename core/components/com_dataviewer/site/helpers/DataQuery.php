<?php

/**
 * Data query builder and executor for the Dataviewer component.
 *
 * Builds dynamic SQL from JSON data definitions and executes
 * against arbitrary external databases via the framework Driver.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Helpers;

use Hubzero\Database\Driver;
use Hubzero\Facades\Request;

class DataQuery
{
    /**
     * Database driver instance
     *
     * @var  Driver
     */
    protected $driver;

    /**
     * Default record limit
     *
     * @var  int
     */
    protected $defaultLimit = 10;

    /**
     * Constructor
     *
     * @param   Driver  $driver        Database driver instance
     * @param   int     $defaultLimit  Default record display limit
     */
    public function __construct(Driver $driver, $defaultLimit = 10)
    {
        $this->driver = $driver;
        $this->defaultLimit = (int) $defaultLimit;

        $driver->exec("SET SESSION group_concat_max_len = 16384");
    }

    /**
     * Create a Driver instance from a database config array.
     *
     * Replaces the old Db::getDb() static factory.
     *
     * @param   array  $dbConfig  Database credentials (host, user, password/pass, database)
     * @return  Driver
     */
    public static function createDriver(array $dbConfig)
    {
        // Backward compatibility: 'pass' -> 'password'
        if (!isset($dbConfig['password']) && isset($dbConfig['pass'])) {
            $dbConfig['password'] = $dbConfig['pass'];
        }

        return Driver::getInstance([
            'driver'   => 'mysql',
            'host'     => $dbConfig['host'],
            'user'     => $dbConfig['user'],
            'password' => $dbConfig['password'],
            'database' => $dbConfig['database'],
            'prefix'   => '',
        ]);
    }

    /**
     * Execute a query and return results with pagination metadata.
     *
     * Replaces the old Db::getResults() static method.
     *
     * @param   string  $sql  SQL query string
     * @param   array   &$dd  Data definition array
     * @return  array         ['data' => [], 'total' => int, 'found' => int, 'sql' => string]
     */
    public function execute($sql, array &$dd)
    {
        $res = [
            'data'  => [],
            'total' => 0,
            'found' => 0,
            'sql'   => $sql,
        ];

        $this->driver->setQuery($sql);
        $data = $this->driver->loadAssocList();

        if ($data !== null) {
            $res['data'] = $data;

            $this->driver->setQuery('SELECT FOUND_ROWS() AS found');
            $found = $this->driver->loadAssoc();
            $res['found'] = $found ? $found['found'] : 0;

            if (isset($dd['total_records'])) {
                $res['total'] = $dd['total_records'];
            } else {
                $this->driver->setQuery($this->buildCountQuery($dd));
                $total = $this->driver->loadAssoc();
                $res['total'] = $total ? $total['total'] : 0;
            }
        }

        return $res;
    }

    /**
     * Build a SELECT query from a data definition array.
     *
     * Replaces the old Db::queryGen() static method with proper
     * value escaping via Driver::quote().
     *
     * @param   array  &$dd  Data definition array (may be modified with column metadata)
     * @return  string       Complete SQL query
     */
    public function buildQuery(array &$dd)
    {
        // Auto-discover columns if not defined
        if (!isset($dd['cols']) && isset($dd['table'])) {
            $this->discoverColumns($dd);
        } elseif (isset($dd['col_info']) && $dd['col_info'] == 'override') {
            $this->enrichColumnInfo($dd, false);
        } elseif (isset($dd['col_info']) && $dd['col_info'] == 'soft_override') {
            $this->enrichColumnInfo($dd, true);
        }

        // Build column arrays
        $cols = [];
        $cols_vis = [];
        $cols_sql = [];

        foreach ($dd['cols'] as $id => $conf) {
            if (isset($conf['field_type']) && $conf['field_type'] === 'point') {
                $conf['raw'] = "CONCAT(X($id), ',', Y($id))";
            }

            if (isset($conf['field_type']) && $conf['field_type'] === 'polygon') {
                $conf['raw'] = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(AsText($id), "
                    . "'),(', ';'), ' ', ','), 'POLYGON((', ''), '))', ''), ';', '; ')";
                $dd['cols'][$id]['truncate'] = 'truncate';
                $width = isset($dd['cols'][$id]['width']) ? $dd['cols'][$id]['width'] : '200';
                $dd['cols'][$id]['width'] = $width;
            }

            $expr = $id;
            $aggr = false;
            $raw = false;

            if (isset($conf['raw'])) {
                $expr = $conf['raw'];
                $aggr = isset($conf['aggr']);
                $raw = true;
            }

            $cols[$id] = ['expr' => $expr, 'aggr' => $aggr, 'raw' => $raw];
            $cols_sql[] = "$expr AS `$id`";

            if (!isset($conf['hide'])) {
                $cols_vis[] = $id;
            }
        }

        $cols_sql_str = implode(', ', $cols_sql);

        // FROM clause
        if (substr($dd['table'], 0, 1) == '(') {
            $sql = "SELECT SQL_CACHE SQL_CALC_FOUND_ROWS $cols_sql_str FROM " . $dd['table'] . ' ';
        } else {
            $sql = "SELECT SQL_CACHE SQL_CALC_FOUND_ROWS $cols_sql_str FROM `"
                . $dd['table'] . '` ';
        }

        // JOIN clauses
        $sql .= $this->buildJoins($dd);

        // WHERE and HAVING from user filters
        $where_str = '';
        $having_str = '';

        $this->buildColumnFilters($dd, $cols, $cols_vis, $where_str, $having_str);
        $this->buildUrlFilters($dd, $cols, $where_str, $having_str);
        $this->buildFullTextSearch($dd, $cols, $cols_vis, $where_str, $having_str);
        $this->buildDefinitionWhere($dd, $cols, $where_str);

        $sql .= $where_str;

        // GROUP BY
        $group_by = '';
        if (isset($dd['group_by'])) {
            $group_by = ' GROUP BY ' . $dd['group_by'];
        }
        $sql .= $group_by;

        // HAVING from data definition
        $this->buildDefinitionHaving($dd, $having_str);
        $sql .= $having_str;

        // ORDER BY
        $sql .= $this->buildOrderBy($dd, $cols, $cols_vis, $group_by);

        // LIMIT
        $sql .= $this->buildLimit($dd);

        return $sql;
    }

    /**
     * Build a COUNT query from a data definition.
     *
     * Replaces the old Db::queryGenTotal() static method.
     *
     * @param   array  $dd  Data definition array
     * @return  string      SQL count query
     */
    public function buildCountQuery(array $dd)
    {
        if (substr($dd['table'], 0, 1) == '(') {
            $sql = "SELECT SQL_CACHE SQL_CALC_FOUND_ROWS 1 AS `total` FROM " . $dd['table'] . ' ';
        } else {
            $sql = "SELECT SQL_CACHE SQL_CALC_FOUND_ROWS 1 AS `total` FROM `"
                . $dd['table'] . '` ';
        }

        $sql .= $this->buildJoins($dd);

        $where_str = '';
        if (isset($dd['where'])) {
            $where_str = ' WHERE ';
            $where = [];
            foreach ($dd['where'] as $w) {
                if (isset($w['field']) && isset($w['value'])) {
                    $where[] = $w['field'] . '=' . $this->driver->quote($w['value']);
                } elseif (isset($w['raw'])) {
                    $where[] = $w['raw'];
                }
            }
            $where_str .= implode(' AND ', $where);
        }
        $sql .= $where_str;

        if (isset($dd['group_by'])) {
            $sql .= ' GROUP BY ' . $dd['group_by'];
        }

        if (isset($dd['having'])) {
            $having = [];
            foreach ($dd['having'] as $h) {
                if (isset($h['field']) && isset($h['value'])) {
                    $having[] = $h['field'] . '=' . $this->driver->quote($h['value']);
                } elseif (isset($h['raw'])) {
                    $having[] = $h['raw'];
                }
            }
            if (!empty($having)) {
                $sql .= ' HAVING ' . implode(' AND ', $having);
            }
        }

        $sql .= ' LIMIT 1';

        return $sql;
    }

    /**
     * Get the underlying driver instance.
     *
     * @return  Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    // ---------------------------------------------------------------
    // Private helpers
    // ---------------------------------------------------------------

    /**
     * Auto-discover columns from a table when no cols are defined in the DD.
     *
     * @param   array  &$dd  Data definition
     * @return  void
     */
    private function discoverColumns(array &$dd)
    {
        $sql = "SELECT DB_column_name, Column_info FROM Columns_Info"
            . " WHERE Table_name=" . $this->driver->quote($dd['table']);
        $this->driver->setQuery($sql);
        $result = $this->driver->loadAssocList();

        $col_info = [];
        if ($result) {
            foreach ($result as $rec) {
                $col_info[$rec['DB_column_name']] = json_decode($rec['Column_info'], true);
            }
        }

        $this->driver->setQuery("SHOW COLUMNS FROM " . $this->driver->quoteName($dd['table']));
        $result = $this->driver->loadAssocList();

        foreach ($result as $rec) {
            if (isset($col_info[$rec['Field']])) {
                $dd['cols'][$dd['table'] . '.' . $rec['Field']] = $col_info[$rec['Field']];
            } else {
                $label = ucwords(str_replace('_', ' ', $rec['Field']));
                $dd['cols'][$dd['table'] . '.' . $rec['Field']] = ['label' => $label];
            }
        }
    }

    /**
     * Enrich column definitions with metadata from the Columns_Info table.
     *
     * @param   array  &$dd   Data definition
     * @param   bool   $soft  If true, only fill in missing labels/units/desc
     * @return  void
     */
    private function enrichColumnInfo(array &$dd, $soft = false)
    {
        $tables = [];
        foreach ($dd['cols'] as $id => $prop) {
            $table = explode('.', $id)[0];
            if (!in_array($table, $tables)) {
                $tables[] = $table;
            }
        }

        $tableList = implode(', ', array_map([$this->driver, 'quote'], $tables));
        $sql = "SELECT CONCAT(Table_name, '.', DB_column_name) AS col, Column_info "
            . "FROM Columns_Info WHERE Table_name IN (" . $tableList . ")";
        $this->driver->setQuery($sql);
        $result = $this->driver->loadAssocList();

        $col_info = [];
        if ($result) {
            foreach ($result as $rec) {
                $col_info[$rec['col']] = json_decode($rec['Column_info'], true);
            }
        }

        $allowedTags = '<br><hr>';
        foreach ($dd['cols'] as $id => $prop) {
            if (!isset($col_info[$id])) {
                continue;
            }

            $info = $col_info[$id];

            if (isset($info['label']) && (!$soft || !isset($dd['cols'][$id]['label']))) {
                $dd['cols'][$id]['label'] = nl2br(strip_tags($info['label'], $allowedTags));
            }
            if (isset($info['unit']) && (!$soft || !isset($dd['cols'][$id]['unit']))) {
                $dd['cols'][$id]['unit'] = strip_tags($info['unit']);
            }
            if (isset($info['desc']) && (!$soft || !isset($dd['cols'][$id]['desc']))) {
                $dd['cols'][$id]['desc'] = strip_tags($info['desc']);
            }
        }
    }

    /**
     * Build JOIN clauses from the data definition.
     *
     * @param   array  $dd  Data definition
     * @return  string      JOIN SQL fragment
     */
    private function buildJoins(array $dd)
    {
        $sql = '';

        if (isset($dd['joins'])) {
            foreach ($dd['joins'] as $j) {
                $sql .= 'LEFT JOIN ' . $j['table']
                    . ' ON (' . $j['ids'][0] . '=' . $j['ids'][1] . ') ';
            }
        }

        if (isset($dd['join'])) {
            foreach ($dd['join'] as $j) {
                $type = isset($j['type']) ? $j['type'] : 'LEFT JOIN';
                $con = [];
                foreach ($j['fields'] as $f1 => $f2) {
                    $con[] = ($f1 == 'raw') ? $f2 : "$f1=$f2";
                }
                $sql .= "$type " . $j['table'] . ' ON (' . implode(' AND ', $con) . ') ';
            }
        }

        return $sql;
    }

    /**
     * Build WHERE/HAVING conditions from DataTables column search parameters.
     *
     * @param   array   $dd          Data definition
     * @param   array   $cols        Processed column info
     * @param   array   $cols_vis    Visible column IDs
     * @param   string  &$where_str  WHERE clause accumulator
     * @param   string  &$having_str HAVING clause accumulator
     * @return  void
     */
    private function buildColumnFilters(
        array $dd,
        array $cols,
        array $cols_vis,
        &$where_str,
        &$having_str
    ) {
        $where_filter = [];
        $having_filter = [];

        for ($i = 0; $i < count($cols_vis); $i++) {
            $col_id = $cols_vis[$i];
            $col = $cols[$col_id];
            $searchable = Request::getString('bSearchable_' . $i, 'false');
            $fieldtype = Request::getString('fieldtype_' . $i, 'string');
            $search_str = Request::getString('sSearch_' . $i, '');

            if ($searchable !== 'true' || $search_str === '') {
                continue;
            }

            $colExpr = '`' . $col_id . '`';
            $entry = [
                'val'       => $search_str,
                'col'       => $col['aggr'] ? $colExpr : ($col['raw'] ? $col['expr'] : $col_id),
                'fieldtype' => $fieldtype,
            ];

            if ($col['aggr']) {
                $having_filter[$col_id] = $entry;
            } else {
                $where_filter[$col_id] = $entry;
            }
        }

        if (!empty($where_filter)) {
            $clauses = $this->buildFilterClauses($where_filter, $dd);
            $this->appendClause($where_str, $clauses, 'WHERE');
        }

        if (!empty($having_filter)) {
            $clauses = $this->buildFilterClauses($having_filter, $dd);
            $this->appendClause($having_str, $clauses, 'HAVING');
        }
    }

    /**
     * Build WHERE/HAVING conditions from URL filter parameter.
     *
     * @param   array   $dd          Data definition
     * @param   array   $cols        Processed column info
     * @param   string  &$where_str  WHERE clause accumulator
     * @param   string  &$having_str HAVING clause accumulator
     * @return  void
     */
    private function buildUrlFilters(array $dd, array $cols, &$where_str, &$having_str)
    {
        $filters = Request::getVar('filter', false);
        if ($filters === false) {
            return;
        }

        $where_filter = [];
        $having_filter = [];

        $filters = explode('||', $filters);
        foreach ($filters as $filter) {
            $parts = explode('|', $filter);
            $col_id = $parts[0];

            if (!isset($cols[$col_id])) {
                continue;
            }

            $col = $cols[$col_id];
            $filter_str = $parts[1];
            $fieldtype = isset($parts[2]) ? $parts[2] : 'string';
            $filter_type = isset($parts[3]) ? $parts[3] : 'exact';
            $colExpr = '`' . $col_id . '`';

            $entry = [
                'val'           => $filter_str,
                'col'           => $col['aggr'] ? $colExpr : ($col['raw'] ? $col['expr'] : $col_id),
                'fieldtype'     => $fieldtype,
                'filtered_view' => true,
                'filter_type'   => $filter_type,
            ];

            if ($col['aggr']) {
                $having_filter[$col_id] = $entry;
            } else {
                $where_filter[$col_id] = $entry;
            }
        }

        if (!empty($where_filter)) {
            $clauses = $this->buildFilterClauses($where_filter, $dd);
            $this->appendClause($where_str, $clauses, 'WHERE');
        }

        if (!empty($having_filter)) {
            $clauses = $this->buildFilterClauses($having_filter, $dd);
            $this->appendClause($having_str, $clauses, 'HAVING');
        }
    }

    /**
     * Build filter SQL clauses with proper escaping.
     *
     * @param   array  $filters  Filter definitions
     * @param   array  $dd       Data definition (for numrange config)
     * @return  string           Combined AND clause
     */
    private function buildFilterClauses(array $filters, array $dd)
    {
        $arr = [];

        foreach ($filters as $key => $val) {
            if ($val['fieldtype'] == 'number' || $val['fieldtype'] == 'datetime') {
                $arr = array_merge(
                    $arr,
                    $this->buildNumericFilter($val)
                );
            } elseif ($val['fieldtype'] == 'numrange') {
                $arr = array_merge(
                    $arr,
                    $this->buildNumrangeFilter($val, $dd, $key)
                );
            } elseif (isset($val['filtered_view'])) {
                $arr[] = $this->buildFilteredViewClause($val);
            } else {
                $arr = array_merge(
                    $arr,
                    $this->buildStringFilter($val)
                );
            }
        }

        return '(' . implode(' AND ', $arr) . ')';
    }

    /**
     * Build filter clause for numeric/datetime fields.
     *
     * @param   array  $val  Filter entry
     * @return  array        SQL clause fragments
     */
    private function buildNumericFilter(array $val)
    {
        $clauses = [];
        $v = strtolower($val['val']);
        $col = $val['col'];

        if (strstr($v, 'to')) {
            $parts = explode('to', $v);
            $min = trim($parts[0]);
            $max = trim($parts[1]);
            if ($min > $max) {
                list($min, $max) = [$max, $min];
            }
            $clauses[] = $col . ' BETWEEN '
                . $this->driver->quote($min) . ' AND '
                . $this->driver->quote($max);
        } elseif (preg_match('/^([<>]=?)\s*(.+)$/', $v, $m)) {
            $clauses[] = $col . ' ' . $m[1] . ' ' . $this->driver->quote(trim($m[2]));
        } elseif (strpos($v, '!=') === 0) {
            $cleaned = trim(substr($v, 2));
            $clauses[] = 'NOT ' . $col . ' <=> ' . $this->driver->quote($cleaned);
        } elseif (strpos($v, '=') === 0) {
            $cleaned = trim(substr($v, 1));
            $clauses[] = $col . ' = ' . $this->driver->quote($cleaned);
        } elseif (strpos($v, '!') === 0) {
            $cleaned = trim(substr($v, 1));
            $clauses[] = $col . ' NOT LIKE ' . $this->driver->quote('%' . $cleaned . '%');
        } else {
            $clauses[] = $col . ' LIKE ' . $this->driver->quote('%' . $v . '%');
        }

        return $clauses;
    }

    /**
     * Build filter clause for numrange fields.
     *
     * @param   array   $val  Filter entry
     * @param   array   $dd   Data definition
     * @param   string  $key  Column key (for numrange min/max lookup)
     * @return  array         SQL clause fragments
     */
    private function buildNumrangeFilter(array $val, array $dd, $key)
    {
        $clauses = [];
        $v = strtolower($val['val']);
        $min_col = $dd['cols'][$key]['numrange']['min'];
        $max_col = $dd['cols'][$key]['numrange']['max'];

        if (strstr($v, 'to')) {
            $parts = explode('to', $v);
            $min = trim($parts[0]);
            $max = trim($parts[1]);
            if ($min > $max) {
                list($min, $max) = [$max, $min];
            }
            $clauses[] = $min_col . ' >= ' . $this->driver->quote($min);
            $clauses[] = $max_col . ' <= ' . $this->driver->quote($max);
        } elseif (preg_match('/^([<>]=?)\s*(.+)$/', $v, $m)) {
            $targetCol = ($m[1][0] === '<') ? $max_col : $min_col;
            $clauses[] = $targetCol . ' ' . $m[1] . ' ' . $this->driver->quote(trim($m[2]));
        } elseif (strpos($v, '!=') === 0) {
            $cleaned = trim(substr($v, 2));
            $clauses[] = 'NOT ' . $val['col'] . ' <=> ' . $this->driver->quote($cleaned);
        } elseif (strpos($v, '=') === 0) {
            $cleaned = trim(substr($v, 1));
            $clauses[] = $val['col'] . ' = ' . $this->driver->quote($cleaned);
        } elseif (strpos($v, '!') === 0) {
            $cleaned = trim(substr($v, 1));
            $clauses[] = $val['col'] . ' NOT LIKE ' . $this->driver->quote('%' . $cleaned . '%');
        } else {
            $clauses[] = $val['col'] . ' LIKE ' . $this->driver->quote('%' . $v . '%');
        }

        return $clauses;
    }

    /**
     * Build a clause for a filtered view entry.
     *
     * @param   array   $val  Filter entry with filter_type
     * @return  string        SQL clause
     */
    private function buildFilteredViewClause(array $val)
    {
        if (isset($val['filter_type']) && $val['filter_type'] == 'like') {
            return $val['col'] . ' LIKE ' . $this->driver->quote('%' . $val['val'] . '%');
        }

        return $val['col'] . ' = ' . $this->driver->quote($val['val']);
    }

    /**
     * Build filter clause for string fields.
     *
     * @param   array  $val  Filter entry
     * @return  array        SQL clause fragments
     */
    private function buildStringFilter(array $val)
    {
        $clauses = [];
        $v = $val['val'];
        $col = $val['col'];

        if (strpos($v, '!=') === 0) {
            $cleaned = trim(substr($v, 2));
            $clauses[] = 'NOT ' . $col . ' <=> ' . $this->driver->quote($cleaned);
        } elseif (strpos($v, '=') === 0) {
            $cleaned = trim(substr($v, 1));
            $clauses[] = $col . ' = ' . $this->driver->quote($cleaned);
        } elseif (strpos($v, '!') === 0) {
            $cleaned = trim(substr($v, 1));
            $clauses[] = $col . ' NOT LIKE ' . $this->driver->quote('%' . $cleaned . '%');
        } else {
            $words = explode(' ', $v);
            if (count($words) > 1) {
                $list = [];
                foreach ($words as $w) {
                    $list[] = $col . ' LIKE ' . $this->driver->quote('%' . $w . '%');
                }
                $clauses[] = '(' . implode(' AND ', $list) . ')';
            } elseif (trim($words[0]) != '') {
                $clauses[] = $col . ' LIKE ' . $this->driver->quote('%' . $words[0] . '%');
            }
        }

        return $clauses;
    }

    /**
     * Build full-text search across all visible columns.
     *
     * @param   array   $dd          Data definition
     * @param   array   $cols        Processed column info
     * @param   array   $cols_vis    Visible column IDs
     * @param   string  &$where_str  WHERE clause accumulator
     * @param   string  &$having_str HAVING clause accumulator
     * @return  void
     */
    private function buildFullTextSearch(
        array $dd,
        array $cols,
        array $cols_vis,
        &$where_str,
        &$having_str
    ) {
        $search_str = Request::getString('sSearch', '');
        if ($search_str == '') {
            return;
        }

        $quoted = $this->driver->quote('%' . $search_str . '%');
        $where_search = [];
        $having_search = [];

        for ($i = 0; $i < count($cols_vis); $i++) {
            $col_id = $cols_vis[$i];
            if (isset($dd['group_by'])) {
                $having_search[] = '`' . $col_id . '` LIKE ' . $quoted;
            } else {
                if ($cols[$col_id]['raw']) {
                    $where_search[] = $cols[$col_id]['expr'] . ' LIKE ' . $quoted;
                } else {
                    $where_search[] = $col_id . ' LIKE ' . $quoted;
                }
            }
        }

        if (!empty($where_search)) {
            $clause = '(' . implode(' OR ', $where_search) . ')';
            $this->appendClause($where_str, $clause, 'WHERE');
        }

        if (!empty($having_search)) {
            $clause = '(' . implode(' OR ', $having_search) . ')';
            $this->appendClause($having_str, $clause, 'HAVING');
        }
    }

    /**
     * Build WHERE clause from data definition's static where conditions.
     *
     * @param   array   $dd         Data definition
     * @param   array   $cols       Processed column info
     * @param   string  &$where_str WHERE clause accumulator
     * @return  void
     */
    private function buildDefinitionWhere(array $dd, array $cols, &$where_str)
    {
        if (!isset($dd['where'])) {
            return;
        }

        if (isset($dd['single'])) {
            // Single-record mode: replaces all prior WHERE
            $where_str = ' WHERE ';
            $parts = [];
            foreach ($dd['where'] as $w) {
                if (isset($w['raw'])) {
                    $parts[] = $w['raw'];
                } else {
                    $values = array_map(
                        [$this->driver, 'quote'],
                        explode(',', $w['value'])
                    );
                    $expr = (isset($cols[$w['field']]) && $cols[$w['field']]['raw'])
                        ? $cols[$w['field']]['expr']
                        : $w['field'];
                    $parts[] = $expr . ' IN (' . implode(',', $values) . ')';
                }
            }
            $where_str .= implode(' AND ', $parts);
        } else {
            $parts = [];
            foreach ($dd['where'] as $w) {
                if (isset($w['field']) && isset($w['value'])) {
                    $parts[] = $w['field'] . '=' . $this->driver->quote($w['value']);
                } elseif (isset($w['raw'])) {
                    $parts[] = $w['raw'];
                }
            }

            if (!empty($parts)) {
                $clause = '(' . implode(' AND ', $parts) . ')';
                $this->appendClause($where_str, $clause, 'WHERE');
            }
        }
    }

    /**
     * Build HAVING clause from data definition's static having conditions.
     *
     * @param   array   $dd          Data definition
     * @param   string  &$having_str HAVING clause accumulator
     * @return  void
     */
    private function buildDefinitionHaving(array $dd, &$having_str)
    {
        if (!isset($dd['having'])) {
            return;
        }

        $parts = [];
        foreach ($dd['having'] as $h) {
            if (isset($h['field']) && isset($h['value'])) {
                $parts[] = $h['field'] . '=' . $this->driver->quote($h['value']);
            } elseif (isset($h['raw'])) {
                $parts[] = $h['raw'];
            }
        }

        if (!empty($parts)) {
            $clause = '(' . implode(' AND ', $parts) . ')';
            $this->appendClause($having_str, $clause, 'HAVING');
        }
    }

    /**
     * Build ORDER BY clause from request params or data definition.
     *
     * @param   array   $dd        Data definition
     * @param   array   $cols      Processed column info
     * @param   array   $cols_vis  Visible column IDs
     * @param   string  $group_by  GROUP BY clause (to check for WITH ROLLUP)
     * @return  string             ORDER BY SQL fragment
     */
    private function buildOrderBy(array $dd, array $cols, array $cols_vis, $group_by)
    {
        $order = [];

        $sorting = Request::getVar('iSortCol_0', false);
        if ($sorting !== false && count($cols_vis) > 0) {
            $sort_col_count = Request::getInt('iSortingCols', 0);
            for ($i = 0; $i < $sort_col_count; $i++) {
                $idx = Request::getInt('iSortCol_' . $i, null);

                // Bounds check
                if ($idx === null || $idx < 0 || $idx >= count($cols_vis)) {
                    continue;
                }

                $sortable = Request::getString('bSortable_' . $idx, 'false');
                if ($sortable !== 'true') {
                    continue;
                }

                $col_id = $cols_vis[$idx];
                $sort_dir = strtolower(Request::getString('sSortDir_' . $i, 'asc'));

                // Whitelist sort direction
                if (!in_array($sort_dir, ['asc', 'desc'])) {
                    $sort_dir = 'asc';
                }

                if ($cols[$col_id]['aggr']) {
                    $order[] = '`' . $col_id . '` ' . $sort_dir;
                } elseif ($cols[$col_id]['raw']) {
                    $order[] = $cols[$col_id]['expr'] . ' ' . $sort_dir;
                } else {
                    $order[] = $col_id . ' ' . $sort_dir;
                }
            }
        } elseif (isset($dd['order_by'])) {
            $order = $dd['order_by'];
        }

        if (!empty($order) && strpos($group_by, 'WITH ROLLUP') === false) {
            return ' ORDER BY ' . implode(', ', $order);
        }

        return '';
    }

    /**
     * Build LIMIT clause from request params or data definition.
     *
     * @param   array  $dd  Data definition
     * @return  string      LIMIT SQL fragment
     */
    private function buildLimit(array $dd)
    {
        $no_limit = Request::getVar('nolimit', false);
        $limit_start = Request::getVar('iDisplayStart', false);
        $limit_length = Request::getVar('iDisplayLength', $this->defaultLimit);

        if ($no_limit === false && $limit_start !== false && $limit_length != '-1') {
            return ' LIMIT ' . (int) $limit_start . ', ' . (int) $limit_length;
        }

        if ($no_limit === false && isset($dd['serverside']) && $dd['serverside']) {
            return ' LIMIT 0, ' . (int) $this->defaultLimit;
        }

        return '';
    }

    /**
     * Append a condition to a WHERE or HAVING accumulator string.
     *
     * @param   string  &$str     Accumulator (may be empty or contain existing clause)
     * @param   string  $clause   New condition to append
     * @param   string  $keyword  'WHERE' or 'HAVING'
     * @return  void
     */
    private function appendClause(&$str, $clause, $keyword)
    {
        if ($str == '') {
            $str = " $keyword $clause";
        } else {
            $str .= " AND $clause";
        }
    }
}
