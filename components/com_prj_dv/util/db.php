<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2010-2013 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2010-2012 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

function get_db($db = false)
{
	global $dv_conf;

	if (!$db) {
		$db = $dv_conf['db'];
	} else {
		$db['host'] = isset($db['host'])? $db['host']: $dv_conf['db']['host'];
		$db['user'] = isset($db['user'])? $db['user']: $dv_conf['db']['user'];
		$db['pass'] = isset($db['pass'])? $db['pass']: $dv_conf['db']['pass'];
		$db['name'] = isset($db['name'])? $db['name']: $dv_conf['db']['name'];
	}

	$link = mysql_connect($db['host'] , $db['user'], $db['pass']);

	if (!mysql_select_db($db['name'], $link)) {
		print("DB error" . mysql_errno($link) . ": " . mysql_error($link));
		exit();
	}

	mysql_set_charset('utf8');

	mysql_query("SET SESSION group_concat_max_len = 2048");

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

function query_gen(&$dd)
{
	global $dv_config;

	if (!isset($dd['cols']) && isset($dd['table'])) {
		$link = isset($dd['db'])? get_db($dd['db']): get_db();
		$sql = "SELECT DB_column_name, Column_info FROM Columns_Info WHERE Table_name='" . $dd['table'] . "'";
		$result = mysql_query($sql, $link);
		$col_info = array();
		if ($result && mysql_num_rows($result) > 0) {
			while ($rec = mysql_fetch_assoc($result)) {
				$col_info[$rec['DB_column_name']] = json_decode($rec['Column_info'], true);
			}
		}

		$sql = "SHOW COLUMNS FROM " . $dd['table'];
		$result = mysql_query($sql, $link);

		while ($rec = mysql_fetch_assoc($result)) {
			if (isset($col_info[$rec['Field']])) {
				$dd['cols'][$dd['table'] . '.' . $rec['Field']] = $col_info[$rec['Field']];
			} else {
				$dd['cols'][$dd['table'] . '.' . $rec['Field']] = array('label'=>ucwords(str_replace('_', ' ', $rec['Field'])));
			}
		}
	} elseif (isset($dd['col_info']) && $dd['col_info'] == 'override') {

		$tables = array();
		foreach($dd['cols'] as $id=>$prop) {
			$table = explode('.', $id);
			$table = $table[0];

			if (!in_array($table, $tables)) {
				$tables[] = $table;
			}

		}

		$tables = implode("', '", $tables);

		$link = isset($dd['db'])? get_db($dd['db']): get_db();
		$sql = "SELECT CONCAT(Table_name, '.', DB_column_name) AS col, Column_info FROM Columns_Info WHERE Table_name IN ('" . $tables . "')";
		$result = mysql_query($sql, $link);

		$col_info = array();
		if ($result && mysql_num_rows($result) > 0) {
			while ($rec = mysql_fetch_assoc($result)) {
				$col_info[$rec['col']] = json_decode($rec['Column_info'], true);
			}
		}

		foreach($dd['cols'] as $id=>$prop) {
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
		foreach($dd['cols'] as $id=>$prop) {
			$table = explode('.', $id);
			$table = $table[0];

			if (!in_array($table, $tables)) {
				$tables[] = $table;
			}

		}

		$tables = implode("', '", $tables);

		$link = isset($dd['db'])? get_db($dd['db']): get_db();
		$sql = "SELECT CONCAT(Table_name, '.', DB_column_name) AS col, Column_info FROM Columns_Info WHERE Table_name IN ('" . $tables . "')";
		$result = mysql_query($sql, $link);

		$col_info = array();
		if ($result && mysql_num_rows($result) > 0) {
			while ($rec = mysql_fetch_assoc($result)) {
				$col_info[$rec['col']] = json_decode($rec['Column_info'], true);
			}
		}

		foreach($dd['cols'] as $id=>$prop) {
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

	$sql = "SELECT SQL_CACHE SQL_CALC_FOUND_ROWS $cols_sql FROM ";
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
					$v = strtolower(trim($where_filter[$col]['val']));
					if ($where_filter[$col]['fieldtype'] == 'number' || $where_filter[$col]['fieldtype'] == 'datetime') {
						if (strstr($v, 'to')) {
							$vals = explode('to', $v);
							$min = trim($vals[0]);
							$max = trim($vals[1]);
							if ($min < $max) {
								$where_sg[] = $c . " BETWEEN '$min' AND '$max'";
							} else {
								$where_sg[] = $c . " BETWEEN '$max' AND '$min'";
							}
						} elseif (strstr($v, '<') || strstr($v, '>')) {
							if (strstr($v, '=')) {
								$v = str_replace('=', "= '", $v);
							} else {
								$v = str_replace('>', "> '", $v);
								$v = str_replace('<', "< '", $v);
							}
							$where_sg[] = $c . " $v'";
						} elseif (strstr($v, '=')) {
							$v = trim(str_replace('=', '', $v));
							$where_sg[] = $c . " = '" . $v . "'";
						} elseif (strstr($v, '!=')) {
							$v = trim(str_replace('!=', '', $v));
							$where_sg[] = "NOT " . $c . " <=> '" . $v . "'";
						} elseif (strstr($v, '!')) {
							$v = trim(str_replace('!', '', $v));
							$where_sg[] = $c . " NOT LIKE '%" . $v . "%'";
						} else {
							$where_sg[] = $c . " LIKE '%" . $v . "%'";
						}
					} elseif ($where_filter[$col]['fieldtype'] == 'numrange') {
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
							$where_sg[] = $c . " = '" . $v . "'";
						} elseif (strstr($v, '!=')) {
							$v = trim(str_replace('!=', '', $v));
							$where_sg[] = "NOT " . $c . " <=> '" . $v . "'";
						} elseif (strstr($v, '!')) {
							$v = trim(str_replace('!', '', $v));
							$where_sg[] = $c . " NOT LIKE '%" . $v . "%'";
						}
					} elseif (strpos($v, '=') === 0) {
						$v = trim(str_replace('=', '', $v));
						$where_sg[] = $c . " = '" . $v . "'";
					} elseif (strpos($v, '!=') === 0) {
						$v = trim(str_replace('!=', '', $v));
						$where_sg[] = "NOT " . $c . " <=> '" . $v . "'";
					} elseif (strpos($v, '!') === 0) {
						$v = trim(str_replace('!', '', $v));
						$where_sg[] = $c . " NOT LIKE '%" . $v . "%'";
					} else {
						$where_sg[] = $c . " LIKE '%" . $v . "%'";
					}
					unset($where_filter[$col]);
				}

				if (isset($having_filter[$col])) {
					$c = $having_filter[$col]['col'];
					$v = strtolower(trim($having_filter[$col]['val']));
					if ($having_filter[$col]['fieldtype'] == 'number' || $having_filter[$col]['fieldtype'] == 'datetime') {
						$v = strtolower($v);
						if (strstr($v, 'to')) {
							$vals = explode('to', $v);
							$min = trim($vals[0]);
							$max = trim($vals[1]);
							if ($min < $max) {
								$having_sg[] = $c . " BETWEEN '$min' AND '$max'";
							} else {
								$having_sg[] = $c . " BETWEEN '$max' AND '$min'";
							}
						} elseif (strstr($v, '<') || strstr($v, '>')) {
							if (strstr($v, '=')) {
								$v = str_replace('=', "= '", $v);
							} else {
								$v = str_replace('>', "> '", $v);
								$v = str_replace('<', "< '", $v);
							}
							$having_sg[] = $c . " $v'";
						} elseif (strstr($v, '=')) {
							$v = trim(str_replace('=', '', $v));
							$having_sg[] = $c . " = '" . $v . "'";
						} elseif (strstr($v, '!=')) {
							$v = trim(str_replace('!=', '', $v));
							$having_sg[] = "NOT " . $c . " <=> '" . $v . "'";
						} elseif (strstr($v, '!')) {
							$v = trim(str_replace('!', '', $v));
							$having_sg[] = $c . " NOT LIKE '%" . $v . "%'";
						} else {
							$having_sg[] = $c . " LIKE '%" . $v . "%'";
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
							$having_sg[] = $c . " = '" . $v . "'";
						} elseif (strstr($v, '!=')) {
							$v = trim(str_replace('!=', '', $v));
							$having_sg[] = "NOT " . $c . " <=> '" . $v . "'";
						} elseif (strstr($v, '!')) {
							$v = trim(str_replace('!', '', $v));
							$having_sg[] = $c . " NOT LIKE '%" . $v . "%'";
						}
					} elseif (strpos($v, '=') === 0) {
						$v = trim(str_replace('=', '', $v));
						$having_sg[] = $c . " = '" . $v . "'";
					} elseif (strpos($v, '!=') === 0) {
						$v = trim(str_replace('!=', '', $v));
						$having_sg[] = "NOT " . $c . " <=> '" . $v . "'";
					} elseif (strpos($v, '!') === 0) {
						$v = trim(str_replace('!', '', $v));
						$having_sg[] = $c . " NOT LIKE '%" . $v . "%'";
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
				} elseif (strstr($val['val'], '=')) {
					$val['val'] = trim(str_replace('=', '', $val['val']));
					$where_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
				} elseif (strstr($val['val'], '!=')) {
					$val['val'] = trim(str_replace('!=', '', $val['val']));
					$where_filter_arr[] = "NOT " . $val['col'] . " <=> '" . $val['val'] . "'";
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
				} elseif (strstr($val['val'], '=')) {
					$val['val'] = trim(str_replace('=', '', $val['val']));
					$where_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
				} elseif (strstr($val['val'], '!=')) {
					$val['val'] = trim(str_replace('!=', '', $val['val']));
					$where_filter_arr[] = "NOT " . $val['col'] . " <=> '" . $val['val'] . "'";
				} elseif (strstr($val['val'], '!')) {
					$val['val'] = trim(str_replace('!', '', $val['val']));
					$where_filter_arr[] = $val['col'] . " NOT LIKE '%" . $val['val'] . "%'";
				} else {
					$where_filter_arr[] = $val['col'] . " LIKE '%" . $val['val'] . "%'";
				}
			} elseif (isset($val['filtered_view'])) {
				$where_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
			} elseif (strpos($val['val'], '=') === 0) {
				$val['val'] = trim(str_replace('=', '', $val['val']));
				$where_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
			} elseif (strpos($val['val'], '!=') === 0) {
				$val['val'] = trim(str_replace('!=', '', $val['val']));
				$where_filter_arr[] = "NOT " . $val['col'] . " <=> '" . $val['val'] . "'";
			} elseif (strpos($val['val'], '!') === 0) {
				$val['val'] = trim(str_replace('!', '', $val['val']));
				$where_filter_arr[] = $val['col'] . " NOT LIKE '%" . $val['val'] . "%'";
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
				} elseif (strstr($val['val'], '=')) {
					$val['val'] = trim(str_replace('=', '', $val['val']));
					$having_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
				} elseif (strstr($val['val'], '!=')) {
					$val['val'] = trim(str_replace('!=', '', $val['val']));
					$having_filter_arr[] = "NOT " . $val['col'] . " <=> '" . $val['val'] . "'";
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
				} elseif (strstr($val['val'], '=')) {
					$val['val'] = trim(str_replace('=', '', $val['val']));
					$having_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
				} elseif (strstr($val['val'], '!=')) {
					$val['val'] = trim(str_replace('!=', '', $val['val']));
					$having_filter_arr[] = "NOT " . $val['col'] . " <=> '" . $val['val'] . "'";
				} elseif (strstr($val['val'], '!')) {
					$val['val'] = trim(str_replace('!', '', $val['val']));
					$having_filter_arr[] = $val['col'] . " NOT LIKE '%" . $val['val'] . "%'";
				}
			} elseif (isset($val['filtered_view'])) {
				$having_filter_arr[] = $val['col'] . " = '" . $val['val'] . "'";
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
		$limit = " LIMIT 0, 1" . $dv_config['settings']['limit'];
	}

	$sql .= $limit;

	return $sql;
}

function query_gen_total($dd) {
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
			if (isset($w['field']) && isset($w['value'])) {
				$where[] = $w['field'] . "='" . $w['value'] . "'";
			} elseif (isset($w['raw'])) {
				$where[] = $w['raw'];
			}
		}
		$where_str .= implode(' AND ', $where);
	}

	$sql .= $where_str;

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

	return $sql;
}

function group_cols($dd, $gid) {
	if (!isset($dd['col_groups'][$gid])) {
		return $dd;
	}
}

function mysql_fetch_all($result) {
	$all = array();
	while ($row = mysql_fetch_assoc($result)) {
		$all[] = $row;
	}

	return $all;
}

function db_clean_input(&$mixed_var) {
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

function get_dd($name, $version = false) {
	$db = &JFactory::getDBO();
	$dd = false;

	if (!$version) {
		$sql = 'SELECT data_definition FROM #__project_databases WHERE `database_name` = ' . $db->quote($name);
		$db->setQuery($sql);
		$database = $db->loadAssoc();
		$dd = json_decode($database['data_definition'], true);
	} else {
		$sql = 'SELECT data_definition FROM #__project_database_versions WHERE database_name=' . $db->quote($name) .
			' AND version=' . $db->quote($version);
		$db->setQuery($sql);
		$ver = $db->loadAssoc();
		$dd = json_decode($ver['data_definition'], true);
		
		// Check publication state
		$sql = 'SELECT state FROM jos_publication_versions ' .
			'LEFT JOIN jos_publication_attachments ON ' . 
				'(jos_publication_versions.publication_id=jos_publication_attachments.publication_id '.
				'AND jos_publication_versions.id=jos_publication_attachments.publication_version_id) '.
			'WHERE object_name=' . $db->quote($name);

		$db->setQuery($sql);
		$state = $db->loadResult();

		$dd['publication_state'] = $state;
		
	}


	$dd['db'] = array('name'=>$dd['database']);


	/* Dynamically set processing mode */
	$link = get_db($dd['db']);
	$cell_count_threshold = 20000;
	$total = mysql_query(query_gen_total($dd), $link);
	$total = mysql_fetch_assoc($total);
	$total = isset($total['total']) ? $total['total'] : 0;
	$dd['total_records'] = $total;

	$vis_col_count = count(array_filter($dd['cols'], function ($col) { return !isset($col['hide']); }));

	if ($cell_count_threshold < ($total * $vis_col_count)) {
		$dd['serverside'] = true;
	}

	return $dd;
}
?>
