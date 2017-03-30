<?php
/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();


function get_db($db = false)
{
	global $dv_conf;

	if (!$db) {
		$db = $dv_conf['db'];
	}

	/* For backward compatibility */
	if (!isset($db['password']) && isset($db['pass'])) {
		$db['password'] = $db['pass'];
	}

	$link = mysqli_connect($db['host'] , $db['user'], $db['password']);

	if (!mysqli_select_db($db['database'], $link)) {
		print("DB error" . mysqli_errno($link) . ": " . mysqli_error($link));
		exit();
	}

	mysqli_set_charset($link,'utf8');

	mysqli_query($link, "SET SESSION group_concat_max_len = 16384");

	return $link;
}

function get_results($sql, &$dd)
{
	$link = isset($dd['db'])? get_db($dd['db']): get_db();
	$res['data'] = array();
	$res['total'] = 0;
	$res['found'] = 0;
	$res['sql'] = '';
	$res['sql'] = $sql;

        $result =  mysqli_query($link, $sql);
	if ($result) {
		$res['data'] = $result;
		$found = mysqli_query($link, 'SELECT FOUND_ROWS() AS found');
		$res['found'] = ($found) ? mysqli_fetch_assoc($found) : 0;
		$res['found'] = $res['found']['found'];
		if (isset($dd['total_records'])) {
			$res['total'] = $dd['total_records'];
		} else {
			$total = mysqli_query($link, query_gen_total($dd));
			$res['total'] = ($total) ? mysqli_fetch_assoc($total) : 0;
			$res['total'] = $res['total']['total'];
		}
	}

	return $res;
}

function query_gen(&$dd)
{
	global $dv_conf;

	if (!isset($dd['cols']) && isset($dd['table'])) {
		$link = isset($dd['db'])? get_db($dd['db']): get_db();
		$sql = "SELECT DB_column_name, Column_info FROM Columns_Info WHERE Table_name='" . $dd['table'] . "'";
		$result =  mysqli_query($link, $sql);
		$col_info = array();
		if ($result && mysqli_num_rows($result) > 0) {
			while ($rec = mysqli_fetch_assoc($result)) {
				$col_info[$rec['DB_column_name']] = json_decode($rec['Column_info'], true);
			}
		}

		$sql = "SHOW COLUMNS FROM `" . $dd['table'] . "`";
		$result =  mysqli_query($link, $sql);

		while ($rec = mysqli_fetch_assoc($result)) {
			if (isset($col_info[$rec['Field']])) {
				$dd['cols'][$dd['table'] . '.' . $rec['Field']] = $col_info[$rec['Field']];
			} else {
				$dd['cols'][$dd['table'] . '.' . $rec['Field']] = array('label'=>ucwords(str_replace('_', ' ', $rec['Field'])));
			}
		}
	} elseif (isset($dd['col_info']) && $dd['col_info'] == 'override') {

		$tables = array();
		foreach ($dd['cols'] as $id=>$prop) {
			$table = explode('.', $id);
			$table = $table[0];

			if (!in_array($table, $tables)) {
				$tables[] = $table;
			}

		}

		$tables = implode("', '", $tables);

		$link = isset($dd['db'])? get_db($dd['db']): get_db();
		$sql = "SELECT CONCAT(Table_name, '.', DB_column_name) AS col, Column_info FROM Columns_Info WHERE Table_name IN ('" . $tables . "')";
		$result = mysqli_query($link, $sql);

		$col_info = array();
		if ($result && mysqli_num_rows($result) > 0) {
			while ($rec = mysqli_fetch_assoc($result)) {
				$col_info[$rec['col']] = json_decode($rec['Column_info'], true);
			}
		}

		foreach ($dd['cols'] as $id=>$prop) {
			if (isset($col_info[$id])) {
				if (isset($col_info[$id]['label'])) {
					$dd['cols'][$id]['label'] = nl2br(strip_tags($col_info[$id]['label'], '<br /><br/><br><hr /><hr/><hr>'));
				}

				if (isset($col_info[$id]['unit'])) {
					$dd['cols'][$id]['unit'] = strip_tags($col_info[$id]['unit']);
				}

				if (isset($col_info[$id]['desc'])) {
					$dd['cols'][$id]['desc'] = strip_tags($col_info[$id]['desc']);
				}
			}
		}
	} elseif (isset($dd['col_info']) && $dd['col_info'] == 'soft_override') {

		$tables = array();
		foreach ($dd['cols'] as $id=>$prop) {
			$table = explode('.', $id);
			$table = $table[0];

			if (!in_array($table, $tables)) {
				$tables[] = $table;
			}
		}

		$tables = implode("', '", $tables);

		$link = isset($dd['db'])? get_db($dd['db']): get_db();
		$sql = "SELECT CONCAT(Table_name, '.', DB_column_name) AS col, Column_info FROM Columns_Info WHERE Table_name IN ('" . $tables . "')";
		$result = mysqli_query($link, $sql);

		$col_info = array();
		if ($result && mysqli_num_rows($result) > 0) {
			while ($rec = mysqli_fetch_assoc($result)) {
				$col_info[$rec['col']] = json_decode($rec['Column_info'], true);
			}
		}

		foreach ($dd['cols'] as $id=>$prop) {
			if (isset($col_info[$id])) {
				if (isset($col_info[$id]['label']) && !isset($dd['cols'][$id]['label'])) {
					$dd['cols'][$id]['label'] = nl2br(strip_tags($col_info[$id]['label'], '<br /><br/><br><hr /><hr/><hr>'));
				}

				if (isset($col_info[$id]['unit']) && !isset($dd['cols'][$id]['unit'])) {
					$dd['cols'][$id]['unit'] = strip_tags($col_info[$id]['unit']);
				}

				if (isset($col_info[$id]['desc']) && !isset($dd['cols'][$id]['desc'])) {
					$dd['cols'][$id]['desc'] = strip_tags($col_info[$id]['desc']);
				}
			}
		}
	}

	$cols = array();
	$cols_vis = array();
	$cols_sql = array();

	foreach ($dd['cols'] as $id=>$conf) {

		if (isset($conf['field_type']) && $conf['field_type'] === 'point') {
			$conf['raw'] = "CONCAT(X($id), ',', Y($id))";
		}

		if (isset($conf['field_type']) && $conf['field_type'] === 'polygon') {
			$conf['raw'] = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(AsText($id), '),(', ';'), ' ', ','), 'POLYGON((', ''), '))', ''), ';', '; ')";
			$dd['cols'][$id]['truncate'] = 'truncate';
			$dd['cols'][$id]['width'] = isset($dd['cols'][$id]['width']) ? $dd['cols'][$id]['width'] : '200';
		}

		$expr = $id;
		$aggr = false;
		$raw = false;

		if (isset($conf['raw'])) {
			$expr = $conf['raw'];
			$aggr = isset($conf['aggr']);
			$raw = true;
		}

		$cols[$id]['expr'] = $expr;
		$cols[$id]['aggr'] = $aggr;
		$cols[$id]['raw'] = $raw;

		$cols_sql[] = "$expr" . ' AS `' . $id . '`';

		if (!isset($conf['hide'])) {
			$cols_vis[] = $id;
		}
	}


	$cols_sql = implode(', ', $cols_sql);

	if (substr($dd['table'], 0, 1) == '(') {
		$sql = "SELECT SQL_CACHE SQL_CALC_FOUND_ROWS $cols_sql FROM " . $dd['table'] . ' ';
	} else {
		$sql = "SELECT SQL_CACHE SQL_CALC_FOUND_ROWS $cols_sql FROM `";
		$sql .= $dd['table'] . '` ';
	}

	if (isset($dd['joins'])) {
		foreach ($dd['joins'] as $j) {
			$sql .= 'LEFT JOIN ' . $j['table'] . ' ON (' . $j['ids'][0] . '=' . $j['ids'][1] . ') ';
		}
	}

	if (isset($dd['join'])) {
		foreach ($dd['join'] as $j) {
			$type = isset($j['type'])? $j['type']: 'LEFT JOIN';
			$con = array();
			foreach ($j['fields'] as $f1=>$f2) {
				if ($f1 == 'raw') {
					$con[] = $f2;
				} else {
					$con[] = "$f1=$f2";
				}
			}
			$con = implode(' AND ', $con);
			$sql .= "$type " . $j['table'] . ' ON (' . $con .  ') ';
		}
	}

	$where_filter = array();
	$having_filter = array();

	// Column filter
	for ($i=0; $i<count($cols_vis); $i++) {
		$col_id = $cols_vis[$i];
		$col = $cols[$col_id];
		$searchable = Request::getString('bSearchable_' . $i, 'false');
		$fieldtype = Request::getString('fieldtype_' . $i, 'string');
		$search_str = Request::getVar('sSearch_' . $i, '', 'default', null, JREQUEST_ALLOWRAW);

		if ($searchable === 'true' && $search_str !== '') {
			if ($col['aggr']) {
				$having_filter[$col_id] = array('val' => $search_str, 'col' => '`' . $col_id . '`', 'fieldtype' => $fieldtype);
			} elseif ($col['raw']) {
				$where_filter[$col_id] = array('val' => $search_str, 'col' => $col['expr'], 'fieldtype' => $fieldtype);
			} else {
				$where_filter[$col_id] = array('val' => $search_str, 'col' => $col_id, 'fieldtype'=> $fieldtype);
			}
		}
	}

	// Filtered views
	$filters = Request::getVar('filter', false, 'default', null, JREQUEST_ALLOWRAW);
	if ($filters !== false) {
		$filters = explode('||', $filters);
		foreach ($filters as $filter) {
			$filter = explode('|', $filter);
			$col_id = $filter[0];
			$col = $cols[$col_id];
			$filter_str = $filter[1];
			$fieldtype = isset($filter[2]) ? $filter[2] : 'string';
			$filter_type = isset($filter[3]) ? $filter[3] : 'exact';

			if ($col['aggr']) {
				$having_filter[$col_id] = array('val' => $filter_str, 'col' => '`' . $col_id . '`', 'fieldtype' => $fieldtype, 'filtered_view' => true, 'filter_type' => $filter_type);
			} elseif ($col['raw']) {
				$where_filter[$col_id] = array('val' => $filter_str, 'col' => $col['expr'], 'fieldtype' => $fieldtype, 'filtered_view' =>  true, 'filter_type' => $filter_type);
			} else {
				$where_filter[$col_id] = array('val' => $filter_str, 'col' => $col_id, 'fieldtype' => $fieldtype, 'filtered_view' =>  true, 'filter_type' => $filter_type);
			}
		}
	}

	$where_str = '';
	$having_str = '';

	if (count($where_filter) > 0) {
		$where_filter_arr = array();
		foreach ($where_filter as $key => $val) {
			if ($val['fieldtype'] == 'number' || $val['fieldtype'] == 'datetime') {
				$val['val'] = strtolower($val['val']);
				if (strstr($val['val'], 'to')) {
					$vals = explode('to', $val['val']);
					$min = trim($vals[0]);
					$max = trim($vals[1]);
					if ($min < $max) {
						$where_filter_arr[] = $val['col'] . " BETWEEN '$min' AND '$max'";
					} else {
						$where_filter_arr[] = $val['col'] . " BETWEEN '$max' AND '$min'";
					}
				} elseif (strstr($val['val'], '<') || strstr($val['val'], '>')) {
					if (strstr($val['val'], '=')) {
						$val['val'] = str_replace('=', "= '", $val['val']);
					} else {
						$val['val'] = str_replace('>', "> '", $val['val']);
						$val['val'] = str_replace('<', "< '", $val['val']);
					}
					$where_filter_arr[] = $val['col'] . " " . $val['val'] . "'";
				} elseif (strstr($val['val'], '!=')) {
					$val['val'] = trim(str_replace('!=', '', $val['val']));
					$where_filter_arr[] = "NOT " . $val['col'] . " <=> '" . $val['val'] . "'";
				} elseif (strstr($val['val'], '=')) {
					$val['val'] = trim(str_replace('=', '', $val['val']));
					$where_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
				} elseif (strstr($val['val'], '!')) {
					$val['val'] = trim(str_replace('!', '', $val['val']));
					$where_filter_arr[] = $val['col'] . " NOT LIKE '%" . $val['val'] . "%'";
				} else {
					$where_filter_arr[] = $val['col'] . " LIKE '%" . $val['val'] . "%'";
				}
			} elseif ($val['fieldtype'] == 'numrange') {
				$val['val'] = strtolower($val['val']);

				$min_col = $dd['cols'][$key]['numrange']['min'];
				$max_col = $dd['cols'][$key]['numrange']['max'];
				if (strstr($val['val'], 'to')) {
					$vals = explode('to', $val['val']);
					$min = trim($vals[0]);
					$max = trim($vals[1]);
					if ($min < $max) {
						$where_filter_arr[] = $min_col . " >= $min";
						$where_filter_arr[] = $max_col . " <= $max";
					} else {
						$where_filter_arr[] = $min_col . " >= $max";
						$where_filter_arr[] = $max_col . " <= $min";
					}
				} elseif (strstr($val['val'], '<')) {
					$where_filter_arr[] = $max_col . " " . $val['val'];
				} elseif (strstr($val['val'], '>')) {
					$where_filter_arr[] = $min_col . " " . $val['val'];
				} elseif (strstr($val['val'], '!=')) {
					$val['val'] = trim(str_replace('!=', '', $val['val']));
					$where_filter_arr[] = "NOT " . $val['col'] . " <=> '" . $val['val'] . "'";
				} elseif (strstr($val['val'], '=')) {
					$val['val'] = trim(str_replace('=', '', $val['val']));
					$where_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
				} elseif (strstr($val['val'], '!')) {
					$val['val'] = trim(str_replace('!', '', $val['val']));
					$where_filter_arr[] = $val['col'] . " NOT LIKE '%" . $val['val'] . "%'";
				} else {
					$where_filter_arr[] = $val['col'] . " LIKE '%" . $val['val'] . "%'";
				}
			} elseif (isset($val['filtered_view'])) {
				if (isset($val['filter_type']) && $val['filter_type'] == 'like') {
					$where_filter_arr[] = $val['col'] . " LIKE '%" . $val['val'] . "%'";
				} else {
					$where_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
				}
			} elseif (strpos($val['val'], '!=') === 0) {
				$val['val'] = trim(str_replace('!=', '', $val['val']));
				$where_filter_arr[] = "NOT " . $val['col'] . " <=> '" . $val['val'] . "'";
			} elseif (strpos($val['val'], '=') === 0) {
				$val['val'] = trim(str_replace('=', '', $val['val']));
				$where_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
			} elseif (strpos($val['val'], '!') === 0) {
				$val['val'] = trim(str_replace('!', '', $val['val']));
				$where_filter_arr[] = $val['col'] . " NOT LIKE '%" . $val['val'] . "%'";
			} else {
				$v_arr = explode(' ', $val['val']);
				if (count($v_arr) > 1) {
					$list = array();
					foreach ($v_arr as $v) {
						$list[] = $val['col'] . " LIKE '%" . $v . "%'";
					}
					$where_filter_arr[] = '(' . implode(' AND ', $list) . ')';
				} elseif (trim($v_arr[0]) != '') {
					$where_filter_arr[] = $val['col'] . " LIKE '%" . $v_arr[0] . "%'";
				}
			}
		}

		$where_filter_str = '(' . implode(' AND ', $where_filter_arr) . ')';

		if ($where_str == '') {
			$where_str = ' WHERE ' . $where_filter_str;
		} else {
			$where_str .= ' AND ' . $where_filter_str;
		}
	}

	if (count($having_filter) > 0) {
		$having_filter_arr = array();
		foreach ($having_filter as $key=>$val) {
			$val['val'] = strtolower($val['val']);
			if ($val['fieldtype'] == 'number' || $val['fieldtype'] == 'datetime') {
				if (strstr($val['val'], 'to')) {
					$vals = explode('to', $val['val']);
					$min = trim($vals[0]);
					$max = trim($vals[1]);
					if ($min < $max) {
						$having_filter_arr[] = $val['col'] . " BETWEEN '$min' AND '$max'";
					} else {
						$having_filter_arr[] = $val['col'] . " BETWEEN '$max' AND '$min'";
					}
				} elseif (strstr($val['val'], '<') || strstr($val['val'], '>')) {
					if (strstr($val['val'], '=')) {
						$val['val'] = str_replace('=', "= '", $val['val']);
					} else {
						$val['val'] = str_replace('>', "> '", $val['val']);
						$val['val'] = str_replace('<', "< '", $val['val']);
					}
					$having_filter_arr[] = $val['col'] . " " . $val['val'] . "'";
				} elseif (strstr($val['val'], '!=')) {
					$val['val'] = trim(str_replace('!=', '', $val['val']));
					$having_filter_arr[] = "NOT " . $val['col'] . " <=> '" . $val['val'] . "'";
				} elseif (strstr($val['val'], '=')) {
					$val['val'] = trim(str_replace('=', '', $val['val']));
					$having_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
				} elseif (strstr($val['val'], '!')) {
					$val['val'] = trim(str_replace('!', '', $val['val']));
					$having_filter_arr[] = $val['col'] . " NOT LIKE '%" . $val['val'] . "%'";
				} else {
					$having_filter_arr[] = $val['col'] . " LIKE '%" . $val['val'] . "%'";
				}
			} elseif ($val['fieldtype'] == 'numrange') {
				$min_col = $dd['cols'][$key]['numrange']['min'];
				$max_col = $dd['cols'][$key]['numrange']['max'];
				if (strstr($val['val'], 'to')) {
					$vals = explode('to', $val['val']);
					$min = trim($vals[0]);
					$max = trim($vals[1]);
					if ($min < $max) {
						$having_filter_arr[] = $min_col . " >= $min";
						$having_filter_arr[] = $max_col . " <= $max";
					} else {
						$having_filter_arr[] = $min_col . " >= $max";
						$having_filter_arr[] = $max_col . " <= $min";
					}
				} elseif (strstr($val['val'], '<')) {
					$having_filter_arr[] = $max_col . " " . $val['val'];
				} elseif (strstr($val['val'], '>')) {
					$having_filter_arr[] = $min_col . " " . $val['val'];
				} elseif (strstr($val['val'], '!=')) {
					$val['val'] = trim(str_replace('!=', '', $val['val']));
					$having_filter_arr[] = "NOT " . $val['col'] . " <=> '" . $val['val'] . "'";
				} elseif (strstr($val['val'], '=')) {
					$val['val'] = trim(str_replace('=', '', $val['val']));
					$having_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
				} elseif (strstr($val['val'], '!')) {
					$val['val'] = trim(str_replace('!', '', $val['val']));
					$having_filter_arr[] = $val['col'] . " NOT LIKE '%" . $val['val'] . "%'";
				}
			} elseif (isset($val['filtered_view'])) {
				if (isset($val['filter_type']) && $val['filter_type'] == 'like') {
					$having_filter_arr[] = $val['col'] . " LIKE '%" . $val['val'] . "%'";
				} else {
					$having_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
				}
			} elseif (strpos($val['val'], '!=') === 0) {
				$val['val'] = trim(str_replace('!=', '', $val['val']));
				$having_filter_arr[] = "NOT " . $val['col'] . " <=> '" . $val['val'] . "'";
			} elseif (strpos($val['val'], '=') === 0) {
				$val['val'] = trim(str_replace('=', '', $val['val']));
				$having_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
			} elseif (strpos($val['val'], '!') === 0) {
				$val['val'] = trim(str_replace('!', '', $val['val']));
				$having_filter_arr[] = $val['col'] . " NOT LIKE '%" . $val['val'] . "%'";
			} else {
				$v_arr = explode(' ', $val['val']);
				if (count($v_arr) > 1) {
					$list = array();
					foreach ($v_arr as $v) {
						$list[] = $val['col'] . " LIKE '%" . $v . "%'";
					}
					$having_filter_arr[] = '(' . implode(' AND ', $list) . ')';
				} elseif (trim($v_arr[0]) != '') {
					$having_filter_arr[] = $val['col'] . " LIKE '%" . $v_arr[0] . "%'";
				}
			}
		}

		$having_filter_str = '(' . implode(' AND ', $having_filter_arr) . ')';

		if ($having_str == '') {
			$having_str = ' HAVING ' . $having_filter_str;
		} else {
			$having_str .= ' AND ' . $having_filter_str;
		}
	}

	// Full search
	$where_search = array();
	$having_search = array();

	$search_str = Request::getVar('sSearch', '', 'default', null, JREQUEST_ALLOWRAW);
	if ($search_str != '') {
		for ($i = 0; $i < count($cols_vis); $i++) {
			$col_id = $cols_vis[$i];
			if (isset($dd['group_by'])) {
				$having_search[] = "`$col_id` LIKE '%$search_str%'";
			} else {
				if ($cols[$col_id]['raw']) {
					$where_search[] = $cols[$col_id]['expr'] . " LIKE '%$search_str%'";
				} else {
					$where_search[] = "$col_id LIKE '%$search_str%'";
				}
			}
		}
	}

	if (count($where_search) > 0) {
		if ($where_str == '') {
			$where_str .= " WHERE (";
		} else {
			$where_str .= " AND (";
		}
		$where_str .= implode(' OR ', $where_search);
		$where_str .= ')';
	}

	if (count($having_search) > 0) {
		if ($having_str == '') {
			$having_str .= " HAVING (";
		} else {
			$having_str .= " AND (";
		}
		$having_str .= implode(' OR ', $having_search);
		$having_str .= ')';
	}

	if (isset($dd['where']) && isset($dd['single'])) {
		$where_str = ' WHERE ';
		$where = array();
		foreach ($dd['where'] as $w) {
			if (isset($w['raw'])) {
				$where[] = $w['raw'];
			} elseif ($cols[$w['field']]['raw']) {
				$where[] = $cols[$w['field']]['expr'] . " IN ('" . str_replace(',' ,"','", $w['value']) . "')";
			} else {
				$where[] = $w['field'] . " IN ('" . str_replace(',' ,"','", $w['value']) . "')";
			}
		}
		$where_str .= implode(' AND ', $where);
	} elseif (isset($dd['where'])) {
		if ($where_str == '') {
			$where_str .= " WHERE (";
		} else {
			$where_str .= " AND (";
		}

		$where = array();
		foreach ($dd['where'] as $w) {
			if (isset($w['field']) && isset($w['value'])) {
				$where[] = $w['field'] . "='" . $w['value'] . "'";
			} elseif (isset($w['raw'])) {
				$where[] = $w['raw'];
			}
		}

		$where_str .= implode(' AND ', $where);
		$where_str .= ')';
	}

	$sql .= $where_str;

	$group_by = '';

	if (isset($dd['group_by'])) {
		$group_by .= ' GROUP BY ' . $dd['group_by'] . '';
	}

	$sql .= $group_by;

	if (isset($dd['having'])) {
		if ($having_str == '') {
			$having_str .= " HAVING (";
		} else {
			$having_str .= " AND (";
		}

		$having = array();
		foreach ($dd['having'] as $h) {
			if (isset($h['field']) && isset($h['value'])) {
				$having[] = $h['field'] . "='" . $h['value'] . "'";
			} elseif (isset($h['raw'])) {
				$having[] = $h['raw'];
			}
		}

		$having_str .= implode(' AND ', $having);
		$having_str .= ')';
	}

	$sql .= $having_str;

	$order = array();

	$sorting = Request::getVar('iSortCol_0', false);
	if ($sorting !== false && count($cols_vis) > 0) {
		$sort_col_count = Request::getInt('iSortingCols', 0);
		for ($i = 0 ; $i < $sort_col_count; $i++) {
			$idx = Request::getInt('iSortCol_' . $i, NULL);
			$sortable = Request::getString('bSortable_' . $idx, 'false');
			if ($sortable === 'true') {
				$col_id = $cols_vis[$idx];
				$sort_dir = Request::getString('sSortDir_' . $i, 'asc');
				if ($cols[$col_id]['aggr']) {
					$order[] = '`' . $col_id . '` ' . $sort_dir;
				} elseif ($cols[$col_id]['raw']) {
					$order[] = $cols[$col_id]['expr'] . ' ' . $sort_dir;
				} else {
					$order[] = $col_id . ' ' . $sort_dir;
				}
			}
		}
	} elseif (isset($dd['order_by'])) {
		$order = $dd['order_by'];
	}

	$order_str = '';

	if (count($order) > 0 && strpos($group_by, 'WITH ROLLUP') === false) {
		$order_str = " ORDER BY " . implode(', ', $order);
	}

	$sql .= $order_str;

	// Limit
	$limit = '';
	$no_limit = Request::getVar('nolimit', false);
	$limit_start = Request::getVar('iDisplayStart', false);
	$limit_length = Request::getVar('iDisplayLength', $dv_conf['settings']['limit']);
	if ($no_limit === false && $limit_start !== false && $limit_length != '-1') {
		$limit = " LIMIT $limit_start, $limit_length";
	} elseif ($no_limit === false && isset($dd['serverside']) && $dd['serverside']) {
		$limit = " LIMIT 0, " . $dv_conf['settings']['limit'];
	}

	$sql .= $limit;

	return $sql;
}

function query_gen_total($dd)
{

	if (substr($dd['table'], 0, 1) == '(') {
		$sql = "SELECT SQL_CACHE SQL_CALC_FOUND_ROWS 1 AS `total` FROM " . $dd['table'] . ' ';
	} else {
		$sql = "SELECT SQL_CACHE SQL_CALC_FOUND_ROWS 1 AS `total` FROM `";
		$sql .= $dd['table'] . '` ';
	}

	if (isset($dd['joins'])) {
		foreach ($dd['joins'] as $j) {
			$sql .= 'LEFT JOIN ' . $j['table'] . ' on (' . $j['ids'][0] . '=' . $j['ids'][1] . ') ';
		}
	}

	if (isset($dd['join'])) {
		foreach ($dd['join'] as $j) {
			$type = isset($j['type'])? $j['type']: 'LEFT JOIN';
			$con = array();
			foreach ($j['fields'] as $f1=>$f2) {
				if ($f1 == 'raw') {
					$con[] = $f2;
				} else {
					$con[] = "$f1=$f2";
				}
			}
			$con = implode(' AND ', $con);
			$sql .= "$type " . $j['table'] . ' ON (' . $con .  ') ';
		}
	}


	$where_str = '';

	if (isset($dd['where'])) {
		$where_str = ' WHERE ';
		$where = array();
		foreach ($dd['where'] as $w) {
			if (isset($w['field']) && isset($w['value'])) {
				$where[] = $w['field'] . "='" . $w['value'] . "'";
			} elseif (isset($w['raw'])) {
				$where[] = $w['raw'];
			}
		}
		$where_str .= implode(' AND ', $where);
	}

	$sql .= $where_str;

	$group_by = '';

	if (isset($dd['group_by'])) {
		$group_by .= ' GROUP BY ' . $dd['group_by'] . '';
	}

	$sql .= $group_by;

	$having_str = '';
	if (isset($dd['having'])) {
		$having_str = ' HAVING ';
		$having = array();
		foreach ($dd['having'] as $h) {
			if (isset($h['field']) && isset($h['value'])) {
				$having[] = $h['field'] . "='" . $h['value'] . "'";
			} elseif (isset($h['raw'])) {
				$having[] = $h['raw'];
			}
		}
		$having_str .= implode(' AND ', $having);
	}

	$sql .= $having_str;

	$sql .= ' LIMIT 1';

	return $sql;
}
?>
