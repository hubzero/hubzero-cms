<?php
/**
 * Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

function get_db($db = false)
{
	global $dv_conf;

	if (!isset($db['host'])) {
		$db = $dv_conf['db'];
	}

	$link = mysql_connect($db['host'] , $db['user'], $db['pass']);

	if (!mysql_select_db($db['name'], $link)) {
		print("DB error" . mysql_errno($link) . ": " . mysql_error($link));
		exit();
	}

	mysql_set_charset('utf8');

	return $link;
}

function get_results($sql, $dd) {
	$link = isset($dd['db'])? get_db($dd['db']): get_db();
	$res['dd'] = $dd;
	$res['data'] = array();
	$res['total'] = 0;
	$res['found'] = 0;
	$res['sql'] = $sql;
	//$res['sql'] = '';

	if ($result = mysql_query($sql)) {
		$res['data'] = $result;
		$found = mysql_query('SELECT FOUND_ROWS() AS found');
		$res['found'] = ($found)? mysql_fetch_all($found): 0;
		$res['found'] = $res['found'][0]['found'];
		if (isset($dd['total_records'])) {
			$res['total'] = $dd['total_records'];
		} else {
			$total = mysql_query(query_gen_total($dd));
			$res['total'] = ($total)? mysql_fetch_all($total): 0;
			$res['total'] = $res['total'][0]['total'];
		}
	}

	return $res;
}

function query_gen($dd)
{
	$cols = array();
	$cols_vis = array();
	$cols_sql = array();

	foreach($dd['cols'] as $id=>$conf) {
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

	$sql = "SELECT SQL_CALC_FOUND_ROWS $cols_sql FROM ";
	$sql .= $dd['table'] . ' ';

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
				$con[] = "$f1=$f2";
			}
			$con = implode(' AND ', $con);
			$sql .= "$type " . $j['table'] . ' ON (' . $con .  ') ';
		}
	}

	// Column filter
	$where_filter = array();
	$having_filter = array();
	for ($i=0; $i<count($cols_vis); $i++) {
		if (isset($_REQUEST['bSearchable_'.$i]) && $_REQUEST['bSearchable_'.$i] == "true" && isset($_REQUEST['sSearch_'.$i]) && $_REQUEST['sSearch_'.$i] != '') {
			$val = $_REQUEST['sSearch_'.$i];
			if ($cols[$cols_vis[$i]]['aggr']) {
				$having_filter[$cols_vis[$i]] = array('val'=>$val, 'col'=>'`' . $cols_vis[$i] . '`', 'fieldtype'=>$_REQUEST['fieldtype_'.$i]);
			} elseif ($cols[$cols_vis[$i]]['raw']) {
				$where_filter[$cols_vis[$i]] = array('val'=>$val, 'col'=>$cols[$cols_vis[$i]]['expr'], 'fieldtype'=>$_REQUEST['fieldtype_'.$i]);
			} else {
				$where_filter[$cols_vis[$i]] = array('val'=>$val, 'col'=>$cols_vis[$i], 'fieldtype'=>$_REQUEST['fieldtype_'.$i]);
			}
		}
	}

	// Filtered views
	if (isset($_GET['filter'])) {
		$ff = explode('||', $_GET['filter']);
		foreach($ff as $f) {
			$f = explode('|', $f);
			$fieldtype = isset($f[2])? $f[2]: 'string';
			if ($cols[$f[0]]['aggr']) {
				$having_filter[$f[0]] = array('val'=>$f[1], 'col'=>'`' . $f[0] . '`', 'fieldtype'=>$fieldtype, 'filtered_view'=>'filtered_view');
			} elseif ($cols[$f[0]]['raw']) {
				$where_filter[$f[0]] = array('val'=>$f[1], 'col'=>$cols[$f[0]]['expr'], 'fieldtype'=>$fieldtype, 'filtered_view'=>'filtered_view');
			} else {
				$where_filter[$f[0]] = array('val'=>$f[1], 'col'=>$f[0], 'fieldtype'=>$fieldtype, 'filtered_view'=>'filtered_view');
			}
		}
	}

	foreach ($cols_vis as $id) {
		if (isset($_REQUEST['bSearchable_'.$i]) && $_REQUEST['bSearchable_'.$i] == "true" && isset($_REQUEST['sSearch_'.$i]) && $_REQUEST['sSearch_'.$i] != '') {
			$val = $_REQUEST['sSearch_'.$i];
			if ($cols[$cols_vis[$i]]['aggr']) {
				$having_filter[$cols_vis[$i]] = array('val'=>$val, 'col'=>'`' . $cols_vis[$i] . '`', 'fieldtype'=>$_REQUEST['fieldtype_'.$i]);
			} elseif ($cols[$cols_vis[$i]]['raw']) {
				$where_filter[$cols_vis[$i]] = array('val'=>$val, 'col'=>$cols[$cols_vis[$i]]['expr'], 'fieldtype'=>$_REQUEST['fieldtype_'.$i]);
			} else {
				$where_filter[$cols_vis[$i]] = array('val'=>$val, 'col'=>$cols_vis[$i], 'fieldtype'=>$_REQUEST['fieldtype_'.$i]);
			}
		}
	}

	$where_str = '';
	$having_str = '';
	if (isset($dd['search_groups'])) {
		$where_sg_arr = array();
		$having_sg_arr = array();

		foreach ($dd['search_groups'] as $sg) {
			$where_sg = array();
			$having_sg = array();
			foreach ($sg['columns'] as $col) {
				if (isset($where_filter[$col])) {
					$c = $where_filter[$col]['col'];
					$v = $where_filter[$col]['val'];
					if ($where_filter[$col]['fieldtype'] == 'number') {
						$v = strtolower($v);
						if (strstr($v, 'to')) {
							$vals = explode('to', $v);
							$min = trim($vals[0]);
							$max = trim($vals[1]);
							if ($min < $max) {
								$where_sg[] = $c . " BETWEEN $min AND $max";
							} else {
								$where_sg[] = $c . " BETWEEN $max AND $min";
							}
						} elseif (strstr($v, '<') || strstr($v, '>')) {
							$where_sg[] = $c . " $v";
						} elseif (strstr($v, '=')) {
							$v = trim(str_replace('=', '', $v));
							$having_sg[] = $c . " LIKE '" . $v . "'";
						}
					} elseif ($where_filter[$col]['fieldtype'] == 'numrange') {
						$v = strtolower($v);
						$min_col = $dd['cols'][$col]['numrange']['min'];
						$max_col = $dd['cols'][$col]['numrange']['max'];
						if (strstr($v, 'to')) {
							$vals = explode('to', $v);
							$min = trim($vals[0]);
							$max = trim($vals[1]);
							if ($min < $max) {
								$where_sg[] = $min_col . " >= $min";
								$where_sg[] = $max_col . " <= $max";
							} else {
								$where_sg[] = $min_col . " >= $max";
								$where_sg[] = $max_col . " <= $min";
							}
						} elseif (strstr($v, '<')) {
							$where_sg[] = $max_col . " $v";
						} elseif(strstr($v, '>')) {
							$where_sg[] = $min_col . " $v";
						} elseif (strstr($v, '=')) {
							$v = trim(str_replace('=', '', $v));
							$where_sg[] = $c . " LIKE '" . $v . "'";
						}
					} else {
						$where_sg[] = $c . " LIKE '%" . $v . "%'";
					}
					unset($where_filter[$col]);
				}

				if (isset($having_filter[$col])) {
					$c = $having_filter[$col]['col'];
					$v = $having_filter[$col]['val'];
					if ($having_filter[$col]['fieldtype'] == 'number') {
						$v = strtolower($v);
						if (strstr($v, 'to')) {
							$vals = explode('to', $v);
							$min = trim($vals[0]);
							$max = trim($vals[1]);
							if ($min < $max) {
								$having_sg[] = $c . " BETWEEN $min AND $max";
							} else {
								$having_sg[] = $c . " BETWEEN $max AND $min";
							}
						} elseif (strstr($v, '<') || strstr($v, '>')) {
							$having_sg[] = $c . " $v";
						} elseif (strstr($v, '=')) {
							$v = trim(str_replace('=', '', $v));
							$having_sg[] = $c . " LIKE '" . $v . "'";
						}
					} elseif ($where_filter[$col]['fieldtype'] == 'numrange') {
						$v = strtolower($v);
						$min_col = $dd['cols'][$col]['numrange']['min'];
						$max_col = $dd['cols'][$col]['numrange']['max'];
						if (strstr($v, 'to')) {
							$vals = explode('to', $v);
							$min = trim($vals[0]);
							$max = trim($vals[1]);
							if ($min < $max) {
								$having_sg[] = $min_col . " >= $min";
								$having_sg[] = $max_col . " <= $max";
							} else {
								$having_sg[] = $min_col . " >= $max";
								$having_sg[] = $max_col . " <= $min";
							}
						} elseif (strstr($v, '<')) {
							$having_sg[] = $max_col . " $v";
						} elseif(strstr($v, '>')) {
							$having_sg[] = $min_col . " $v";
						} elseif (strstr($v, '=')) {
							$v = trim(str_replace('=', '', $v));
							$having_sg[] = $c . " LIKE '" . $v . "'";
						}
					} else {
						$having_sg[] = $c . " LIKE '%" . $v . "%'";
					}
					unset($having_filter[$col]);
				}
			}

			if (count($where_sg) > 0) {
				$where_sg_arr[] = '(' . implode(' OR ', $where_sg) . ')';
			}

			if (count($having_sg) > 0) {
				$having_sg_arr[] = '(' . implode(' OR ', $having_sg) . ')';
			}
		}

		if (count($where_sg_arr) > 0) {
			$where_str .= " WHERE " . implode(' AND ', $where_sg_arr);
		}

		if (count($having_sg_arr) > 0) {
			$having_str .= " HAVING " . implode(' AND ', $having_sg_arr);
		}
	}

	if (count($where_filter) > 0) {
		$where_filter_arr = array();
		foreach ($where_filter as $key=>$val) {
			if ($val['fieldtype'] == 'number') {
				$val['val'] = strtolower($val['val']);
				if (strstr($val['val'], 'to')) {
					$vals = explode('to', $val['val']);
					$min = trim($vals[0]);
					$max = trim($vals[1]);
					if ($min < $max) {
						$where_filter_arr[] = $val['col'] . " BETWEEN $min AND $max";
					} else {
						$where_filter_arr[] = $val['col'] . " BETWEEN $max AND $min";
					}
				} elseif (strstr($val['val'], '<') || strstr($val['val'], '>')) {
					$where_filter_arr[] = $val['col'] . " " . $val['val'];
				} elseif (strstr($val['val'], '=')) {
					$val['val'] = trim(str_replace('=', '', $val['val']));
					$where_filter_arr[] = $val['col'] . " LIKE '" . $val['val'] . "'";
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
				} elseif (strstr($val['val'], '=')) {
					$val['val'] = trim(str_replace('=', '', $val['val']));
					$where_filter_arr[] = $val['col'] . " LIKE '" . $val['val'] . "'";
				} else {
					$where_filter_arr[] = $val['col'] . " LIKE '%" . $val['val'] . "%'";
				}
			} elseif (isset($val['filtered_view'])) {
				$where_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
			} else {
				$where_filter_arr[] = $val['col'] . " LIKE '%" . $val['val'] . "%'";
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
			if ($val['fieldtype'] == 'number') {
				$val['val'] = strtolower($val['val']);
				if (strstr($val['val'], 'to')) {
					$vals = explode('to', $val['val']);
					$min = trim($vals[0]);
					$max = trim($vals[1]);
					if ($min < $max) {
						$having_filter_arr[] = $val['col'] . " BETWEEN $min AND $max";
					} else {
						$having_filter_arr[] = $val['col'] . " BETWEEN $max AND $min";
					}
				} elseif (strstr($val['val'], '<') || strstr($val['val'], '>')) {
					$having_filter_arr[] = $val['col'] . " " . $val['val'];
				} elseif (strstr($val['val'], '=')) {
					$val['val'] = trim(str_replace('=', '', $val['val']));
					$having_filter_arr[] = $val['col'] . " LIKE '" . $val['val'] . "'";
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
				} elseif (strstr($val['val'], '=')) {
					$val['val'] = trim(str_replace('=', '', $val['val']));
					$having_filter_arr[] = $val['col'] . " LIKE '" . $val['val'] . "'";
				}
			} else {
				$having_filter_arr[] = $val['col'] . " LIKE '%" . $val['val'] . "%'";
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

	if (isset($_REQUEST['sSearch']) && $_REQUEST['sSearch'] != "") {
		for ($i=0; $i<count($cols_vis); $i++) {
			$val = $_REQUEST['sSearch'];
			if (isset($dd['group_by'])) {
				$having_search[] = '`' . $cols_vis[$i] . "` LIKE '%" . $val . "%'";
			} else {
				if ($cols[$cols_vis[$i]]['raw']) {
					$where_search[] = $cols[$cols_vis[$i]]['expr'] . " LIKE '%" . $val . "%'";
				} else {
					$where_search[] = $cols_vis[$i] . " LIKE '%" . $val . "%'";
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
			if ($cols[$w['field']]['raw']) {
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
			$where[] = $w['field'] . "='" . $w['value'] . "'";
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

	$sql .= $having_str;

	$order = array();

	if (isset($_REQUEST['iSortCol_0']) && count($cols_vis)>0) {
		for ($i=0 ; $i<intval($_REQUEST['iSortingCols']); $i++) {
			if ($_REQUEST['bSortable_' . intval($_REQUEST['iSortCol_'.$i])] == "true") {
				$idx = intval($_REQUEST['iSortCol_'.$i]);
				if ($cols[$cols_vis[$idx]]['aggr']) {
					$order[] = '`' . $cols_vis[$idx] . "` " . $_REQUEST['sSortDir_' . $i];
				} elseif ($cols[$cols_vis[$idx]]['raw']) {
					$order[] = $cols[$cols_vis[$idx]]['expr'] . " " . $_REQUEST['sSortDir_' . $i];
				} else {
					$order[] = $cols_vis[$idx] . " " . $_REQUEST['sSortDir_' . $i];
				}
			}
		}
	} elseif (isset($dd['order_by'])) {
		$order = $dd['order_by'];
	}

	$order_str = '';

	if (count($order) > 0) {
		$order_str = " ORDER BY " . implode(', ', $order);
	}

	$sql .= $order_str;

	// Limit
	$limit = "";
	if (!isset($_REQUEST['nolimit']) && isset($_REQUEST['iDisplayStart']) && $_REQUEST['iDisplayLength'] != '-1') {
		$limit = " LIMIT " . $_REQUEST['iDisplayStart'] . ", " . $_REQUEST['iDisplayLength'];
	} else if (!isset($_REQUEST['nolimit']) && isset($dd['serverside']) && $dd['serverside']) {
		$limit = " LIMIT 0, " . $_SESSION['dv']['settings']['limit'];
	}

	$sql .= $limit;

	return $sql;
}

function query_gen_total($dd)
{
	$col = '*';
	if(isset($dd['group_by'])) {
		$col = 'DISTINCT ' . $dd['group_by'];
	}

	$sql = "SELECT COUNT($col) AS `total` FROM ";
	$sql .= $dd['table'] . ' ';

	if (isset($dd['joins'])) {
		foreach ($dd['joins'] as $j) {
			$sql .= 'LEFT JOIN ' . $j['table'] . ' on (' . $j['ids'][0] . '=' . $j['ids'][1] . ') ';
		}
	}

	$where_str = '';

	if (isset($dd['where'])) {
		$where_str = ' WHERE ';
		$where = array();
		foreach ($dd['where'] as $w) {
			$where[] = $w['field'] . "='" . $w['value'] . "'";
		}
		$where_str .= implode(' AND ', $where);
	}

	$sql .= $where_str;

	return $sql;
}

function group_cols($dd, $gid)
{
	if (!isset($dd['col_groups'][$gid])) {
		return $dd;
	}
}

/*
function get_field_type($field_name)
{
	$table = substr($field_name, 0,strpos($field_name, '.'));

	$res = mysql_query("SHOW COLUMNS FROM $table WHERE Field='$field_name'");

	if (!$res) {
		return 'string';
	}

	$res = mysql_fetch_assoc($res);
	$type = $res[0]['Type'];

	if (strstr($type, 'int'))
}
*/

function mysql_fetch_all($result)
{
	$all = array();
	while ($row = mysql_fetch_assoc($result)) {
		$all[] = $row;
	}

	return $all;
}

function db_clean_input(&$mixed_var)
{
	if(is_array($mixed_var)) {
		foreach ($mixed_var as &$val) {
			is_array($val) ? db_clean_input($val) : $val = _db_clean_input($val);
		}
		unset($val);
	} else {
		$mixed_var=_db_clean_input($mixed_var);
	}
}

function _db_clean_input($str) {
	$db = get_db();
	$str = trim($str);
	$str = filter_var($str, FILTER_SANITIZE_STRING);
//	$str = mysql_real_escape_string($db, $str);
	return $str;
}

?>
