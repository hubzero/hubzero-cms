<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();


function get_conf($db_id)
{
	global $dv_conf, $com_name;
	$db_dv_conf = array();

	$db_name = isset($db_id['name']) ? str_replace('..', '', preg_replace('/[^A-Za-z0-9_.-]/', '', (string) $db_id['name'])) : '';

	if (empty($db_name))
	{
		return array();
	}

	// Base directory
	$dv_conf['db_base_dir'] = Component::params('com_databases')->get('base_dir');
	if (!$dv_conf['db_base_dir'] || $dv_conf['db_base_dir'] == '') {
		$dv_conf['db_base_dir'] = '/db/databases';
	}

	$db_conf_file = "{$dv_conf['db_base_dir']}/$db_name/database.json";
	if (!is_file($db_conf_file))
	{
		return array();
	}
	$db_conf = json_decode((string) file_get_contents($db_conf_file), true);
	if (!is_array($db_conf) || !isset($db_conf['database_ro']))
	{
		return array();
	}
	$dv_conf['db'] = array_merge($dv_conf['db'], $db_conf['database_ro']);

	$dv_conf_file = "{$dv_conf['db_base_dir']}/$db_name/applications/$com_name/config.json";

	if (file_exists($dv_conf_file)) {
		$db_dv_conf = json_decode(file_get_contents($dv_conf_file), true);
		if (!is_array($db_dv_conf)) {
			$db_dv_conf = array();
		} if (isset($db_dv_conf['settings'])) {
			$db_dv_conf['settings'] = array_merge($dv_conf['settings'], $db_dv_conf['settings']);
		}
	}

	if (!isset($dv_conf['base_path']))
	{
		$dv_conf['base_path'] = '';
	}

	$dv_conf = array_merge($dv_conf, $db_dv_conf);

	return $dv_conf;
}

function get_dd($db_id)
{
	global $dv_conf;
	$dd = false;
	$dv_id = preg_replace('/[^A-Za-z0-9_.-]/', '', (string) Request::getString('dv'));
	$dv_id = str_replace('..', '', $dv_id);
	$db_name = str_replace('..', '', preg_replace('/[^A-Za-z0-9_.-]/', '', (string) $db_id['name']));

	$dv_conf['dd_json'] = "{$dv_conf['db_base_dir']}/$db_name/applications/dataviewer/datadefinitions";

	$dd_json_file = (isset($dv_conf['dd_json']) && file_exists($dv_conf['dd_json'] . DS . $dv_id . '.json'))? $dv_conf['dd_json'] . DS . $dv_id . '.json': false;

	if (isset($db_id['extra']) && $db_id['extra'] == 'table') {
		$dd['title'] = 'Table : ' . $dv_id;
		$dd['table'] = $dv_id;

		if (!User::isGuest() && isset($dv_conf['_managers']) && $dv_conf['_managers'] !== false) {
			$dd['acl']['allowed_groups'] = $dv_conf['_managers'];
		} elseif (!User::isGuest() && User::authorise('login', 'administrator')) {
			// Remove access restrictions for managers
			$dd['acl']['allowed_users'] = false;
			$dd['acl']['allowed_groups'] = false;
		}
	} else {

		if ($dd_json_file) {
			$dd = json_decode(file_get_contents($dd_json_file), true);
		} elseif ($dd_php_file) {
			require_once ($dd_php_file);
			$dd_func = 'get_' . $dv_id;
			if (function_exists($dd_func)) {
				$dd = $dd_func();
			}
		} else {
			App::abort(404, 'Invalid or Missing Dataview', 'Invalid or Missing Dataview');
			exit;
		}


		$dd['conf'] = (isset($dd['conf'])) ? $dd['conf'] : array();

		if (isset($dd['conf']['proc_mode_switch'])) {
			$dv_conf['proc_mode_switch'] = $dd['conf']['proc_mode_switch'];
		}

		if (isset($dd['conf']['proc_switch_threshold'])) {
			$dv_conf['proc_switch_threshold'] = $dd['conf']['proc_switch_threshold'];
		}

		// Database override form dd
		if (isset($dd['db']) && is_array($dd['db'])) {
			$dv_conf['db'] = array_merge($dv_conf['db'], $dd['db']);
		}

		$dd = _dd_post($dd);

	}

	/* Dynamically set processing mode */
	if (isset($dv_conf['proc_mode_switch']) && $dv_conf['proc_mode_switch']) {
		$link = get_db();
		mysqli_query($link, query_gen_total($dd));
		$total = mysqli_query($link, 'SELECT FOUND_ROWS() AS total');
		$total = mysqli_fetch_assoc($total);
		$total = isset($total['total']) ? $total['total'] : 0;
		$dd['total_records'] = $total;

		$vis_col_count = 0;
		if (isset($dd['cols'])) {
			$vis_col_count = count(array_filter($dd['cols'], function ($col) {
				return !isset($col['hide']);
			}));
		} elseif (isset($db_id['extra']) && $db_id['extra'] == 'table') {
			$sql = "SELECT COUNT(*) AS cols FROM information_schema.columns WHERE table_name = '{$dd['table']}'";
			$cols = mysqli_fetch_assoc(mysqli_query($link, $sql));
			$vis_col_count = $cols['cols'];
		}

		if ($dv_conf['proc_switch_threshold'] < ($total * $vis_col_count)) {
			$dd['serverside'] = true;
		}
	}

	$dd['db_id'] = $db_id;
	$dd['dv_id'] = $dv_id;

	return $dd;
}

function _dd_post($dd)
{
	$id = Request::getString('id', false);

	if ($id) {
		$dd['where'][] = array('field'=>$dd['pk'], 'value'=>$id);
		$dd['single'] = true;
	}

	$custom_field =  Request::getString('custom_field', false);
	if ($custom_field) {
		$custom_field = explode('|', $custom_field);
		$dd['where'][] = array('field'=>$custom_field[0], 'value'=>$custom_field[1]);
		$dd['single'] = true;
	}

	// Data for Custom Views
	$custom_view = Request::getString('custom_view', '');

	if ($custom_view != '') {
		$custom_view = explode(',', $custom_view);
		unset($dd['customizer']);

		// Custom Title
		$custom_title = Request::getString('custom_title', '');
		if ($custom_title !== '') {
			$dd['title'] = htmlspecialchars($custom_title);
		}

		// Custom Group by
		//
		// An allowlist of this dataview's own columns, for the same reason as
		// custom_view below -- lib/db.php concatenates this straight into the
		// statement. A character class is NOT enough here: letters, digits,
		// comma, dot, space and backtick are everything a
		// "1 WITH ROLLUP UNION SELECT ..." payload needs, and naming WITH ROLLUP
		// also suppresses the trailing ORDER BY at lib/db.php:659, which is what
		// would otherwise have made the injected UNION a syntax error.
		$group_by = Request::getString('group_by', '');
		if ($group_by !== '') {
			$group_cols = array();
			foreach (explode(',', $group_by) as $gb_col) {
				$gb_col = trim($gb_col);
				// accept a bare column or its "WITH ROLLUP" suffix, nothing else
				$rollup = '';
				if (preg_match('/^(.*?)\s+WITH\s+ROLLUP$/i', $gb_col, $m)) {
					$gb_col = trim($m[1]);
					$rollup = ' WITH ROLLUP';
				}
				if ($gb_col !== '' && array_key_exists($gb_col, $dd['cols'])) {
					$group_cols[] = '`' . $gb_col . '`' . $rollup;
				}
			}
			if ($group_cols) {
				$dd['group_by'] = implode(', ', $group_cols);
			}
		}

		// Ordering
		$order_cols = $dd['cols'];
		$dd['cols'] = array();
		foreach ($custom_view as $cv_col) {
			// Only real columns of this dataview. An unknown key yields a null
			// conf, and lib/db.php then uses the key itself both as the column
			// expression and as its backtick-quoted alias -- so anything else was
			// an injection straight into the SELECT list.
			if (!array_key_exists($cv_col, $order_cols)) {
				continue;
			}
			$dd['cols'][$cv_col] = $order_cols[$cv_col];
		}

		// Hiding
		foreach ($order_cols as $id => $prop) {
			if (!in_array($id, $custom_view)) {
				$dd['cols'][$id] = $prop;

				if (!isset($dd['cols'][$id]['hide'])) {
					$dd['cols'][$id]['hide'] = 'custom';
				}

			}
		}
	}

	return $dd;
}


function pathway($dd)
{
	$document = App::get('document');
	$document->setTitle($dd['title']);

	if (isset($_SERVER['HTTP_REFERER'])) {
		$ref_title = Request::getString('ref_title', $dd['title'] . " Resource");
		$ref_title = htmlentities($ref_title);
		Pathway::append($ref_title, $_SERVER['HTTP_REFERER']);
	}

	Pathway::append($dd['title'], $_SERVER['REQUEST_URI']);
}
